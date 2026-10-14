<?php
/**
 * Обработчик контактных заявок.
 * POST /wp-json/studio/v1/contact — приём и валидация форм обратной связи.
 * GET /wp-json/studio/v1/contact-requests — просмотр заявок (только для администраторов).
 *
 * @package StudioHeadless
 */

// Запрещаем прямой доступ к файлу
defined( 'ABSPATH' ) || exit;

/**
 * Переопределяем email и имя отправителя для всех писем WordPress.
 */
add_filter( 'wp_mail_from', function (): string {
    return 'no-reply@' . wp_parse_url( get_home_url(), PHP_URL_HOST );
} );

add_filter( 'wp_mail_from_name', function (): string {
    return get_bloginfo( 'name' );
} );

/**
 * Регистрация эндпоинтов обработки заявок.
 */
add_action( 'rest_api_init', function (): void {

    $namespace = 'studio/v1';

    // ─── POST /contact — отправка заявки ────────────────────────────────────
    register_rest_route( $namespace, '/contact', [
        'methods'             => 'POST',
        'callback'            => 'studio_rest_handle_contact',
        'permission_callback' => '__return_true', // Публичный эндпоинт
    ] );

    // ─── GET /contact-requests — список заявок (только для администраторов) ─
    register_rest_route( $namespace, '/contact-requests', [
        'methods'             => 'GET',
        'callback'            => 'studio_rest_get_contact_requests',
        'permission_callback' => function (): bool {
            // Разрешаем доступ только администраторам
            return current_user_can( 'manage_options' );
        },
    ] );
} );


/**
 * Обрабатывает входящую заявку с контактной формы.
 *
 * @param WP_REST_Request $request Объект запроса.
 * @return WP_REST_Response Ответ с результатом обработки.
 */
function studio_rest_handle_contact( WP_REST_Request $request ): WP_REST_Response {

    // Временный дебаг — возвращаем детальную ошибку вместо 500
    set_error_handler( function( $errno, $errstr, $errfile, $errline ) {
        throw new \ErrorException( $errstr, 0, $errno, $errfile, $errline );
    } );

    try {

    // Извлекаем и санируем параметры
    $name                = sanitize_text_field( (string) $request->get_param( 'name' ) );
    $email               = sanitize_email( (string) $request->get_param( 'email' ) );
    $phone               = sanitize_text_field( (string) ( $request->get_param( 'phone' ) ?? '' ) );
    $project_description = sanitize_textarea_field( (string) $request->get_param( 'project_description' ) );

    // ─── Валидация полей ───────────────────────────────────────────────────────
    $validation_errors = [];

    // Проверяем имя (обязательное, непустое)
    if ( empty( $name ) ) {
        $validation_errors['name'] = [ 'Имя обязательно для заполнения.' ];
    }

    // Проверяем email (обязательный, должен быть валидным)
    if ( empty( $email ) ) {
        $validation_errors['email'] = [ 'Email обязателен для заполнения.' ];
    } elseif ( ! is_email( $email ) ) {
        $validation_errors['email'] = [ 'Введите корректный email-адрес.' ];
    }

    // Проверяем описание проекта (обязательное, непустое)
    if ( empty( $project_description ) ) {
        $validation_errors['project_description'] = [ 'Описание проекта обязательно для заполнения.' ];
    }

    // Если есть ошибки валидации — возвращаем 422 Unprocessable Entity
    if ( ! empty( $validation_errors ) ) {
        return new WP_REST_Response(
            [
                'code'    => 'validation_error',
                'message' => 'Пожалуйста, исправьте ошибки в форме.',
                'data'    => [
                    'status'            => 422,
                    'validation_errors' => $validation_errors,
                ],
            ],
            422
        );
    }

    // ─── Сохранение заявки в WordPress ────────────────────────────────────────
    $post_id = 0;
    try {
        $post_title = sprintf(
            'Заявка від %s (%s)',
            $name,
            wp_date( 'd.m.Y H:i' )
        );

        $post_id = wp_insert_post( [
            'post_type'   => 'contact_request',
            'post_title'  => $post_title,
            'post_status' => 'publish',
            'meta_input'  => [
                'contact_name'        => $name,
                'contact_email'       => $email,
                'contact_phone'       => $phone,
                'contact_description' => $project_description,
                'contact_date'        => current_time( 'mysql' ),
                'contact_ip'          => studio_get_client_ip(),
            ],
        ] );

        if ( is_wp_error( $post_id ) ) {
            error_log( '[Studio] Contact save error: ' . $post_id->get_error_message() );
            $post_id = 0;
        }
    } catch ( \Throwable $e ) {
        error_log( '[Studio] Contact exception: ' . $e->getMessage() );
        $post_id = 0;
    }

    // ─── Отправка уведомления администратору ──────────────────────────────────
    $admin_email = get_option( 'admin_email' );
    $site_name   = get_bloginfo( 'name' );

    $subject = sprintf( '[%s] Нова заявка від %s', $site_name, $name );

    $message  = "Нова заявка з сайту {$site_name}\n\n";
    $message .= "Ім'я: {$name}\n";
    $message .= "Email: {$email}\n";

    if ( ! empty( $phone ) ) {
        $message .= "Телефон: {$phone}\n";
    }

    $message .= "\nОпис проекту:\n{$project_description}\n\n";
    $message .= "---\n";
    $message .= 'Дата: ' . wp_date( 'd.m.Y H:i' ) . "\n";

    if ( $post_id ) {
        $admin_url = admin_url( 'post.php?post=' . $post_id . '&action=edit' );
        $message  .= "Переглянути заявку: {$admin_url}\n";
    }

    $headers = [
        'Content-Type: text/plain; charset=UTF-8',
        "Reply-To: {$name} <{$email}>",
    ];

    wp_mail( $admin_email, $subject, $message, $headers );

    // ─── Успешный ответ ───────────────────────────────────────────────────────
    return new WP_REST_Response(
        [
            'success' => true,
            'message' => 'Спасибо! Мы свяжемся с вами в ближайшее время.',
        ],
        200
    );

    } catch ( \Throwable $e ) {
        restore_error_handler();
        return new WP_REST_Response(
            [
                'success' => false,
                'debug'   => [
                    'error'   => $e->getMessage(),
                    'file'    => $e->getFile(),
                    'line'    => $e->getLine(),
                    'trace'   => $e->getTraceAsString(),
                ],
            ],
            200 // Возвращаем 200 чтобы увидеть тело ответа в браузере
        );
    }
}


/**
 * Возвращает список заявок для администраторов.
 *
 * @param WP_REST_Request $request Объект запроса.
 * @return WP_REST_Response Список заявок.
 */
function studio_rest_get_contact_requests( WP_REST_Request $request ): WP_REST_Response {

    // Параметры пагинации
    $page     = max( 1, (int) $request->get_param( 'page' ) );
    $per_page = min( 100, max( 1, (int) ( $request->get_param( 'per_page' ) ?? 20 ) ) );

    $query = new WP_Query( [
        'post_type'      => 'contact_request',
        'post_status'    => 'publish',
        'posts_per_page' => $per_page,
        'paged'          => $page,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ] );

    $requests = [];

    if ( $query->have_posts() ) {
        foreach ( $query->posts as $post ) {
            $requests[] = [
                'id'          => $post->ID,
                'name'        => get_post_meta( $post->ID, 'contact_name', true ),
                'email'       => get_post_meta( $post->ID, 'contact_email', true ),
                'phone'       => get_post_meta( $post->ID, 'contact_phone', true ),
                'description' => get_post_meta( $post->ID, 'contact_description', true ),
                'date'        => get_post_meta( $post->ID, 'contact_date', true ),
                'submitted_at' => $post->post_date,
            ];
        }
    }

    wp_reset_postdata();

    return new WP_REST_Response( [
        'total'    => (int) $query->found_posts,
        'pages'    => (int) $query->max_num_pages,
        'page'     => $page,
        'per_page' => $per_page,
        'items'    => $requests,
    ], 200 );
}


/**
 * Получает IP-адрес клиента с учётом прокси и CDN.
 *
 * @return string IP-адрес клиента.
 */
function studio_get_client_ip(): string {
    $headers = [
        'HTTP_CF_CONNECTING_IP', // Cloudflare
        'HTTP_X_FORWARDED_FOR',  // Прокси/балансировщик
        'HTTP_X_REAL_IP',        // Nginx
        'REMOTE_ADDR',           // Прямое подключение
    ];

    foreach ( $headers as $header ) {
        if ( ! empty( $_SERVER[ $header ] ) ) {
            $ip = sanitize_text_field( wp_unslash( $_SERVER[ $header ] ) );
            // X-Forwarded-For может содержать несколько IP через запятую
            $ip = trim( explode( ',', $ip )[0] );
            if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
                return $ip;
            }
        }
    }

    return '';
}
