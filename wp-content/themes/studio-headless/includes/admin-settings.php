<?php
/**
 * Страница настроек темы Studio Headless в WordPress Admin.
 * Позволяет настроить подключение к Next.js-фронтенду и принудительно
 * регенерировать сайт без редактирования wp-config.php.
 *
 * @package StudioHeadless
 */

// Запрещаем прямой доступ к файлу
defined( 'ABSPATH' ) || exit;

/**
 * Регистрация пункта меню в WordPress Admin.
 */
add_action( 'admin_menu', function (): void {
    add_menu_page(
        'Studio Theme',                   // Заголовок страницы (тег <title>)
        'Studio Theme',                   // Название в меню
        'manage_options',                 // Права доступа — только администраторы
        'studio-theme-settings',          // Слаг страницы
        'studio_admin_settings_page',     // Callback-функция рендера страницы
        'dashicons-admin-customizer',     // Иконка Dashicons
        58                                // Позиция в меню (перед ACF)
    );
} );

/**
 * Регистрация настроек через WordPress Settings API.
 * Данные сохраняются в таблице wp_options.
 */
add_action( 'admin_init', function (): void {

    // Настройка: URL Next.js-фронтенда
    register_setting(
        'studio_theme_settings',          // Группа настроек
        'studio_nextjs_frontend_url',     // Имя опции в wp_options
        [
            'type'              => 'string',
            'sanitize_callback' => 'esc_url_raw',
            'default'           => '',
        ]
    );

    // Настройка: секрет для ISR-вебхука
    register_setting(
        'studio_theme_settings',
        'studio_nextjs_revalidate_secret',
        [
            'type'              => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default'           => '',
        ]
    );
} );


/**
 * Обработка нажатия кнопки «Импортировать демо-данные».
 */
add_action( 'admin_post_studio_import_demo', function (): void {

    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'У вас нет прав для выполнения этого действия.', 'studio-headless' ) );
    }

    if (
        ! isset( $_POST['studio_import_demo_nonce'] ) ||
        ! wp_verify_nonce(
            sanitize_text_field( wp_unslash( $_POST['studio_import_demo_nonce'] ) ),
            'studio_import_demo'
        )
    ) {
        wp_die( esc_html__( 'Ошибка безопасности. Попробуйте ещё раз.', 'studio-headless' ) );
    }

    $json_file = get_template_directory() . '/demo-data/demo-content.json';
    if ( ! file_exists( $json_file ) ) {
        wp_safe_redirect( add_query_arg( [ 'page' => 'studio-theme-settings', 'import_status' => 'no_file' ], admin_url( 'admin.php' ) ) );
        exit;
    }

    $data = json_decode( file_get_contents( $json_file ), true ); // phpcs:ignore
    if ( ! $data ) {
        wp_safe_redirect( add_query_arg( [ 'page' => 'studio-theme-settings', 'import_status' => 'bad_json' ], admin_url( 'admin.php' ) ) );
        exit;
    }

    // ── Глобальные настройки (ACF Options) ──
    if ( function_exists( 'update_field' ) && isset( $data['settings'] ) ) {
        $s = $data['settings'];
        update_field( 'site_name', $s['site_name'], 'option' );
        update_field( 'email', $s['email'], 'option' );
        update_field( 'phone', $s['phone'], 'option' );
        update_field( 'address', $s['address'], 'option' );
        update_field( 'seo_title', $s['seo_title'], 'option' );
        update_field( 'seo_description', $s['seo_description'], 'option' );
        update_field( 'twitter_card', $s['twitter_card'], 'option' );
        update_field( 'map_lat', $s['map_lat'], 'option' );
        update_field( 'map_lng', $s['map_lng'], 'option' );
        update_field( 'office_marker_label', $s['office_marker_label'], 'option' );
    }

    // ── Hero-секция ──
    if ( function_exists( 'update_field' ) && isset( $data['hero'] ) ) {
        $h = $data['hero'];
        update_field( 'heading', $h['heading'], 'option' );
        update_field( 'subheading', $h['subheading'], 'option' );
        update_field( 'cta_text', $h['cta_text'], 'option' );
        update_field( 'cta_anchor', $h['cta_anchor'], 'option' );
        update_field( 'background_type', $h['background_type'], 'option' );
        update_field( 'background_gradient', $h['background_gradient'], 'option' );
    }

    // ── Секция «О нас» ──
    if ( function_exists( 'update_field' ) && isset( $data['about'] ) ) {
        $a = $data['about'];
        update_field( 'about_title', $a['about_title'], 'option' );
        update_field( 'history', $a['history'], 'option' );
        update_field( 'mission', $a['mission'], 'option' );
        update_field( 'values', array_map( fn( $v ) => [ 'value_item' => $v ], $a['values'] ), 'option' );
        update_field( 'stats', $a['stats'], 'option' );
    }

    // ── Вспомогательная функция создания CPT-записи ──
    $create_post = function ( string $post_type, string $title, string $slug ): int {
        $existing = get_page_by_path( $slug, OBJECT, $post_type );
        if ( $existing ) {
            return $existing->ID;
        }
        $id = wp_insert_post( [
            'post_type'   => $post_type,
            'post_title'  => $title,
            'post_name'   => $slug,
            'post_status' => 'publish',
        ] );
        return is_wp_error( $id ) ? 0 : $id;
    };

    // ── Услуги ──
    if ( isset( $data['services'] ) ) {
        foreach ( $data['services'] as $item ) {
            $id = $create_post( 'services', $item['title'], sanitize_title( $item['title'] ) );
            if ( $id && function_exists( 'update_field' ) ) {
                update_field( 'description', $item['description'], $id );
                update_field( 'sort_order', $item['sort_order'], $id );
                update_field( 'is_visible', $item['is_visible'] ? 1 : 0, $id );
                if ( ! empty( $item['title_en'] ) ) update_field( 'title_en', $item['title_en'], $id );
                if ( ! empty( $item['description_en'] ) ) update_field( 'description_en', $item['description_en'], $id );
            }
        }
    }

    // ── Технологии ──
    if ( isset( $data['technologies'] ) ) {
        foreach ( $data['technologies'] as $item ) {
            $id = $create_post( 'technologies', $item['name'], sanitize_title( $item['name'] ) );
            if ( $id && function_exists( 'update_field' ) ) {
                update_field( 'category', $item['category'], $id );
                update_field( 'sort_order', $item['sort_order'], $id );
                update_field( 'is_visible', $item['is_visible'] ? 1 : 0, $id );
            }
        }
    }

    // ── Команда ──
    if ( isset( $data['team'] ) ) {
        foreach ( $data['team'] as $item ) {
            $id = $create_post( 'team', $item['name'], sanitize_title( $item['name'] ) );
            if ( $id && function_exists( 'update_field' ) ) {
                update_field( 'position', $item['position'], $id );
                update_field( 'sort_order', $item['sort_order'], $id );
            }
        }
    }

    // ── Портфолио ──
    if ( isset( $data['portfolio'] ) ) {
        foreach ( $data['portfolio'] as $item ) {
            $id = $create_post( 'portfolio', $item['title'], $item['slug'] );
            if ( $id && function_exists( 'update_field' ) ) {
                update_field( 'short_description', $item['short_description'], $id );
                update_field( 'full_description', $item['full_description'], $id );
                update_field( 'project_url', $item['project_url'] ?? '', $id );
                update_field( 'sort_order', $item['sort_order'], $id );
                update_field( 'is_visible', 1, $id );
                update_field( 'technologies', array_map( fn( $t ) => [ 'tech_name' => $t ], $item['technologies'] ), $id );
                if ( ! empty( $item['short_description_en'] ) ) update_field( 'short_description_en', $item['short_description_en'], $id );
                if ( ! empty( $item['full_description_en'] ) ) update_field( 'full_description_en', $item['full_description_en'], $id );
            }
        }
    }

    // ── География ──
    if ( isset( $data['geography'] ) ) {
        foreach ( $data['geography'] as $item ) {
            $id = $create_post( 'geography', $item['country_name'], strtolower( $item['country_code'] ) );
            if ( $id && function_exists( 'update_field' ) ) {
                update_field( 'country_code', $item['country_code'], $id );
                update_field( 'projects_count', $item['projects_count'], $id );
                update_field( 'clients_count', $item['clients_count'], $id );
                update_field( 'tooltip_text', $item['tooltip_text'], $id );
            }
        }
    }

    wp_safe_redirect( add_query_arg( [ 'page' => 'studio-theme-settings', 'import_status' => 'success' ], admin_url( 'admin.php' ) ) );
    exit;
} );


/**
 * Обработка нажатия кнопки «Принудительно регенерировать сайт».
 * Вызывается до рендера страницы Admin.
 */
add_action( 'admin_post_studio_force_revalidate', function (): void {

    // Проверяем права пользователя
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'У вас нет прав для выполнения этого действия.', 'studio-headless' ) );
    }

    // Проверяем nonce для защиты от CSRF-атак
    if (
        ! isset( $_POST['studio_revalidate_nonce'] ) ||
        ! wp_verify_nonce(
            sanitize_text_field( wp_unslash( $_POST['studio_revalidate_nonce'] ) ),
            'studio_force_revalidate'
        )
    ) {
        wp_die( esc_html__( 'Ошибка безопасности. Попробуйте ещё раз.', 'studio-headless' ) );
    }

    // Создаём фиктивный объект поста для вызова функции регенерации
    $fake_post = new WP_Post( (object) [
        'ID'        => 0,
        'post_type' => 'options',
        'post_name' => '',
    ] );

    // Вызываем регенерацию (функция определена в isr-webhook.php)
    if ( function_exists( 'studio_trigger_isr_revalidate' ) ) {
        studio_trigger_isr_revalidate( 0, $fake_post );
        $status = 'revalidated';
    } else {
        $status = 'error_no_function';
    }

    // Перенаправляем обратно на страницу настроек с уведомлением
    wp_safe_redirect(
        add_query_arg(
            [ 'page' => 'studio-theme-settings', 'revalidate_status' => $status ],
            admin_url( 'admin.php' )
        )
    );
    exit;
} );


/**
 * Рендер страницы настроек Studio Theme.
 */
function studio_admin_settings_page(): void {

    // Проверяем права доступа
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // Обрабатываем сохранение настроек через стандартный механизм WordPress
    if (
        isset( $_POST['studio_settings_nonce'] ) &&
        wp_verify_nonce(
            sanitize_text_field( wp_unslash( $_POST['studio_settings_nonce'] ) ),
            'studio_save_settings'
        )
    ) {
        // Сохраняем URL фронтенда
        if ( isset( $_POST['studio_nextjs_frontend_url'] ) ) {
            update_option(
                'studio_nextjs_frontend_url',
                esc_url_raw( wp_unslash( $_POST['studio_nextjs_frontend_url'] ) )
            );
        }

        // Сохраняем секрет ISR-вебхука
        if ( isset( $_POST['studio_nextjs_revalidate_secret'] ) ) {
            update_option(
                'studio_nextjs_revalidate_secret',
                sanitize_text_field( wp_unslash( $_POST['studio_nextjs_revalidate_secret'] ) )
            );
        }

        // Показываем уведомление об успешном сохранении
        add_settings_error(
            'studio_settings_messages',
            'studio_settings_saved',
            'Настройки сохранены.',
            'updated'
        );
    }

    // Текущие значения настроек из wp_options
    $frontend_url = get_option( 'studio_nextjs_frontend_url', '' );
    $secret       = get_option( 'studio_nextjs_revalidate_secret', '' );

    // Определяем: настройки заданы в wp-config.php (приоритетнее wp_options)
    $url_from_config    = defined( 'NEXTJS_FRONTEND_URL' ) ? NEXTJS_FRONTEND_URL : null;
    $secret_from_config = defined( 'NEXTJS_REVALIDATE_SECRET' ) ? NEXTJS_REVALIDATE_SECRET : null;

    // Статус последней регенерации
    $revalidate_status = isset( $_GET['revalidate_status'] )
        ? sanitize_text_field( wp_unslash( $_GET['revalidate_status'] ) )
        : '';

    ?>
    <div class="wrap">
        <h1>
            <span class="dashicons dashicons-admin-customizer" style="font-size:30px;vertical-align:middle;margin-right:8px;"></span>
            <?php esc_html_e( 'Studio Theme — Настройки', 'studio-headless' ); ?>
        </h1>

        <?php settings_errors( 'studio_settings_messages' ); ?>

        <?php if ( 'revalidated' === $revalidate_status ) : ?>
            <div class="notice notice-success is-dismissible">
                <p><?php esc_html_e( '✅ Запрос на регенерацию сайта отправлен успешно.', 'studio-headless' ); ?></p>
            </div>
        <?php elseif ( 'error_no_function' === $revalidate_status ) : ?>
            <div class="notice notice-error is-dismissible">
                <p><?php esc_html_e( '❌ Ошибка: функция регенерации не найдена. Убедитесь, что isr-webhook.php подключён.', 'studio-headless' ); ?></p>
            </div>
        <?php endif; ?>

        <?php
        $import_status = isset( $_GET['import_status'] )
            ? sanitize_text_field( wp_unslash( $_GET['import_status'] ) )
            : '';
        ?>
        <?php if ( 'success' === $import_status ) : ?>
            <div class="notice notice-success is-dismissible">
                <p><?php esc_html_e( '✅ Демо-данные успешно импортированы!', 'studio-headless' ); ?></p>
            </div>
        <?php elseif ( 'no_file' === $import_status ) : ?>
            <div class="notice notice-error is-dismissible">
                <p><?php esc_html_e( '❌ Файл demo-content.json не найден.', 'studio-headless' ); ?></p>
            </div>
        <?php elseif ( 'bad_json' === $import_status ) : ?>
            <div class="notice notice-error is-dismissible">
                <p><?php esc_html_e( '❌ Ошибка чтения demo-content.json.', 'studio-headless' ); ?></p>
            </div>
        <?php endif; ?>

        <div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;margin-top:20px;">

            <!-- ─── Основные настройки ────────────────────────────────────────── -->
            <div>
                <div class="postbox">
                    <div class="postbox-header">
                        <h2 class="hndle"><?php esc_html_e( 'Подключение к Next.js-фронтенду', 'studio-headless' ); ?></h2>
                    </div>
                    <div class="inside">

                        <?php if ( $url_from_config || $secret_from_config ) : ?>
                            <div class="notice notice-info inline" style="margin-bottom:15px;">
                                <p>
                                    <strong><?php esc_html_e( 'Обнаружены константы в wp-config.php', 'studio-headless' ); ?></strong><br>
                                    <?php esc_html_e( 'Константы NEXTJS_FRONTEND_URL / NEXTJS_REVALIDATE_SECRET имеют приоритет над настройками ниже.', 'studio-headless' ); ?>
                                </p>
                            </div>
                        <?php endif; ?>

                        <form method="post" action="">
                            <?php wp_nonce_field( 'studio_save_settings', 'studio_settings_nonce' ); ?>

                            <table class="form-table" role="presentation">
                                <tr>
                                    <th scope="row">
                                        <label for="studio_nextjs_frontend_url">
                                            <?php esc_html_e( 'URL Next.js-фронтенда', 'studio-headless' ); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input
                                            type="url"
                                            id="studio_nextjs_frontend_url"
                                            name="studio_nextjs_frontend_url"
                                            value="<?php echo esc_attr( $url_from_config ?? $frontend_url ); ?>"
                                            class="regular-text"
                                            placeholder="https://studio.ru"
                                            <?php echo $url_from_config ? 'readonly' : ''; ?>
                                        >
                                        <?php if ( $url_from_config ) : ?>
                                            <p class="description">
                                                <?php esc_html_e( 'Значение задано через константу NEXTJS_FRONTEND_URL в wp-config.php.', 'studio-headless' ); ?>
                                            </p>
                                        <?php else : ?>
                                            <p class="description">
                                                <?php esc_html_e( 'Или добавьте в wp-config.php: define(\'NEXTJS_FRONTEND_URL\', \'https://studio.ru\');', 'studio-headless' ); ?>
                                            </p>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="studio_nextjs_revalidate_secret">
                                            <?php esc_html_e( 'Секрет ISR-вебхука', 'studio-headless' ); ?>
                                        </label>
                                    </th>
                                    <td>
                                        <input
                                            type="password"
                                            id="studio_nextjs_revalidate_secret"
                                            name="studio_nextjs_revalidate_secret"
                                            value="<?php echo esc_attr( $secret_from_config ?? $secret ); ?>"
                                            class="regular-text"
                                            placeholder="<?php esc_attr_e( 'Введите секретный ключ', 'studio-headless' ); ?>"
                                            <?php echo $secret_from_config ? 'readonly' : ''; ?>
                                        >
                                        <?php if ( $secret_from_config ) : ?>
                                            <p class="description">
                                                <?php esc_html_e( 'Значение задано через константу NEXTJS_REVALIDATE_SECRET в wp-config.php.', 'studio-headless' ); ?>
                                            </p>
                                        <?php else : ?>
                                            <p class="description">
                                                <?php esc_html_e( 'Должен совпадать с переменной REVALIDATE_SECRET в .env Next.js.', 'studio-headless' ); ?>
                                            </p>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>

                            <?php if ( ! $url_from_config && ! $secret_from_config ) : ?>
                                <?php submit_button( 'Сохранить настройки' ); ?>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <!-- ─── Принудительная регенерация ──────────────────────────── -->
                <div class="postbox">
                    <div class="postbox-header">
                        <h2 class="hndle"><?php esc_html_e( 'Принудительная регенерация', 'studio-headless' ); ?></h2>
                    </div>
                    <div class="inside">
                        <p>
                            <?php esc_html_e( 'Отправляет запрос на немедленную перестройку всех статических страниц Next.js. Используйте при изменениях, которые не вызвали автоматическую регенерацию.', 'studio-headless' ); ?>
                        </p>
                        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                            <?php wp_nonce_field( 'studio_force_revalidate', 'studio_revalidate_nonce' ); ?>
                            <input type="hidden" name="action" value="studio_force_revalidate">
                            <button type="submit" class="button button-primary button-hero">
                                <span class="dashicons dashicons-update" style="vertical-align:middle;"></span>
                                <?php esc_html_e( 'Принудительно регенерировать сайт', 'studio-headless' ); ?>
                            </button>
                        </form>
                    </div>
                </div>
                <!-- ─── Импорт демо-данных ──────────────────────────────── -->
                <div class="postbox">
                    <div class="postbox-header">
                        <h2 class="hndle"><?php esc_html_e( 'Демо-данные', 'studio-headless' ); ?></h2>
                    </div>
                    <div class="inside">
                        <p>
                            <?php esc_html_e( 'Заполняет все секции сайта демо-контентом из demo-content.json. Существующие записи не удаляются — новые добавляются или пропускаются если уже существуют.', 'studio-headless' ); ?>
                        </p>
                        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                            <?php wp_nonce_field( 'studio_import_demo', 'studio_import_demo_nonce' ); ?>
                            <input type="hidden" name="action" value="studio_import_demo">
                            <button type="submit" class="button button-secondary button-hero" onclick="return confirm('Импортировать демо-данные? Существующие записи не будут удалены.');">
                                <span class="dashicons dashicons-database-import" style="vertical-align:middle;"></span>
                                <?php esc_html_e( 'Импортировать демо-данные', 'studio-headless' ); ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div>
                <div class="postbox">
                    <div class="postbox-header">
                        <h2 class="hndle"><?php esc_html_e( 'Управление контентом', 'studio-headless' ); ?></h2>
                    </div>
                    <div class="inside">
                        <p class="description" style="margin-bottom:10px;">
                            <?php esc_html_e( 'Быстрый доступ к редактированию контента секций через ACF Options.', 'studio-headless' ); ?>
                        </p>
                        <ul style="list-style:none;padding:0;margin:0;">
                            <?php
                            // Ссылки на ACF Options Pages (появляются только если ACF активен)
                            $acf_pages = [
                                'studio-settings' => '⚙️ Studio Settings (Настройки студии)',
                                'hero-section'    => '🦸 Hero Section (Hero-секция)',
                                'about-section'   => 'ℹ️ About Section (О нас)',
                            ];

                            foreach ( $acf_pages as $slug => $label ) :
                                $url = admin_url( 'admin.php?page=' . $slug );
                            ?>
                                <li style="margin-bottom:8px;">
                                    <a href="<?php echo esc_url( $url ); ?>" class="button button-secondary" style="width:100%;text-align:left;">
                                        <?php echo esc_html( $label ); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                        <hr style="margin:15px 0;">

                        <p class="description" style="margin-bottom:10px;">
                            <?php esc_html_e( 'Кастомные типы записей:', 'studio-headless' ); ?>
                        </p>
                        <ul style="list-style:none;padding:0;margin:0;">
                            <?php
                            // Ссылки на CPT в Admin
                            $cpt_links = [
                                'portfolio'    => '📁 Портфолио',
                                'services'     => '🔧 Услуги',
                                'technologies' => '💻 Технологии',
                                'team'         => '👥 Команда',
                                'geography'    => '🌍 География',
                            ];

                            foreach ( $cpt_links as $post_type => $label ) :
                                $url = admin_url( 'edit.php?post_type=' . $post_type );
                            ?>
                                <li style="margin-bottom:6px;">
                                    <a href="<?php echo esc_url( $url ); ?>">
                                        <?php echo esc_html( $label ); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <!-- ─── Статус подключения ───────────────────────────────────── -->
                <div class="postbox">
                    <div class="postbox-header">
                        <h2 class="hndle"><?php esc_html_e( 'Статус', 'studio-headless' ); ?></h2>
                    </div>
                    <div class="inside">
                        <?php
                        $effective_url    = $url_from_config ?? $frontend_url;
                        $effective_secret = $secret_from_config ?? $secret;
                        ?>
                        <table class="widefat striped" style="font-size:13px;">
                            <tbody>
                                <tr>
                                    <td><?php esc_html_e( 'ACF Pro', 'studio-headless' ); ?></td>
                                    <td>
                                        <?php if ( class_exists( 'ACF' ) ) : ?>
                                            <span style="color:green;">✅ <?php esc_html_e( 'Активен', 'studio-headless' ); ?></span>
                                        <?php else : ?>
                                            <span style="color:red;">❌ <?php esc_html_e( 'Не найден', 'studio-headless' ); ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php esc_html_e( 'Frontend URL', 'studio-headless' ); ?></td>
                                    <td>
                                        <?php if ( $effective_url ) : ?>
                                            <span style="color:green;">✅</span>
                                            <code><?php echo esc_html( $effective_url ); ?></code>
                                        <?php else : ?>
                                            <span style="color:orange;">⚠️ <?php esc_html_e( 'Не задан', 'studio-headless' ); ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php esc_html_e( 'ISR-секрет', 'studio-headless' ); ?></td>
                                    <td>
                                        <?php if ( $effective_secret ) : ?>
                                            <span style="color:green;">✅ <?php esc_html_e( 'Задан', 'studio-headless' ); ?></span>
                                        <?php else : ?>
                                            <span style="color:orange;">⚠️ <?php esc_html_e( 'Не задан', 'studio-headless' ); ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Метабокс для просмотра данных контактной заявки.
 */
add_action( 'add_meta_boxes', function (): void {
    add_meta_box(
        'studio_contact_request_details',
        'Детали заявки',
        'studio_contact_request_metabox',
        'contact_request',
        'normal',
        'high'
    );
} );

function studio_contact_request_metabox( WP_Post $post ): void {
    $name        = get_post_meta( $post->ID, 'contact_name', true );
    $email       = get_post_meta( $post->ID, 'contact_email', true );
    $phone       = get_post_meta( $post->ID, 'contact_phone', true );
    $description = get_post_meta( $post->ID, 'contact_description', true );
    $date        = get_post_meta( $post->ID, 'contact_date', true );
    $ip          = get_post_meta( $post->ID, 'contact_ip', true );
    ?>
    <table class="form-table" style="margin:0;">
        <tr><th style="width:140px;">Ім'я</th><td><?php echo esc_html( $name ?: '—' ); ?></td></tr>
        <tr><th>Email</th><td><?php echo $email ? '<a href="mailto:' . esc_attr( $email ) . '">' . esc_html( $email ) . '</a>' : '—'; ?></td></tr>
        <tr><th>Телефон</th><td><?php echo $phone ? '<a href="tel:' . esc_attr( $phone ) . '">' . esc_html( $phone ) . '</a>' : '—'; ?></td></tr>
        <tr><th>Опис проекту</th><td style="white-space:pre-wrap;"><?php echo esc_html( $description ?: '—' ); ?></td></tr>
        <tr><th>Дата</th><td><?php echo esc_html( $date ?: $post->post_date ); ?></td></tr>
        <tr><th>IP</th><td><?php echo esc_html( $ip ?: '—' ); ?></td></tr>
    </table>
    <?php
}
