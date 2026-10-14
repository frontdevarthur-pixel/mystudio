<?php
/**
 * ISR-вебхук для принудительной регенерации Next.js-страниц.
 * При сохранении любого CPT или ACF Options отправляет POST-запрос
 * на /api/revalidate фронтенда, запуская on-demand ISR.
 *
 * @package StudioHeadless
 */

// Запрещаем прямой доступ к файлу
defined( 'ABSPATH' ) || exit;

/**
 * Отправляет запрос на регенерацию указанных путей в Next.js.
 * Вызывается при сохранении записей CPT и ACF Options.
 *
 * @param int     $post_id ID записи (0 для ACF Options).
 * @param WP_Post $post    Объект записи.
 * @return void
 */
function studio_trigger_isr_revalidate( int $post_id, WP_Post $post ): void {

    // Игнорируем автосохранения — не нужно регенерировать при каждом нажатии клавиши
    if ( wp_is_post_autosave( $post_id ) ) {
        return;
    }

    // Игнорируем ревизии — регенерируем только при публикации/обновлении основной записи
    if ( wp_is_post_revision( $post_id ) ) {
        return;
    }

    // Получаем URL фронтенда из константы wp-config.php
    $next_url = defined( 'NEXTJS_FRONTEND_URL' ) ? rtrim( NEXTJS_FRONTEND_URL, '/' ) : '';

    // Получаем секрет для защиты вебхука от несанкционированных вызовов
    $secret = defined( 'NEXTJS_REVALIDATE_SECRET' ) ? NEXTJS_REVALIDATE_SECRET : '';

    // Если URL или секрет не настроены — молча выходим, не блокируя сохранение
    if ( empty( $next_url ) || empty( $secret ) ) {
        return;
    }

    // ─── Определяем пути для регенерации ─────────────────────────────────────

    // Главная страница регенерируется всегда
    $paths = [ '/' ];

    // Для портфолио дополнительно регенерируем страницу конкретного проекта
    if ( 'portfolio' === $post->post_type && ! empty( $post->post_name ) ) {
        $paths[] = '/portfolio/' . $post->post_name;
    }

    // ─── Отправляем запрос на регенерацию ────────────────────────────────────
    $response = wp_remote_post(
        $next_url . '/api/revalidate',
        [
            'method'  => 'POST',
            'headers' => [
                'Content-Type'        => 'application/json',
                'x-revalidate-secret' => $secret, // Заголовок аутентификации
            ],
            'body'    => wp_json_encode( [ 'paths' => $paths ] ),
            'timeout' => 5, // Не блокируем Admin дольше 5 секунд
        ]
    );

    // Логируем ошибки соединения для отладки (не прерываем работу Admin)
    if ( is_wp_error( $response ) ) {
        error_log(
            '[Studio Headless] Ошибка ISR-вебхука: ' . $response->get_error_message() .
            ' | Пути: ' . implode( ', ', $paths )
        );
    }
}

// Подключаем хук сохранения записей CPT
add_action( 'save_post', 'studio_trigger_isr_revalidate', 10, 2 );


/**
 * Регенерация при сохранении ACF Options Pages (глобальные настройки секций).
 * Срабатывает только при сохранении настроек через ACF Options страницы.
 */
add_action( 'acf/save_post', function ( $post_id ): void {

    // Реагируем только на сохранение Options (не на конкретные записи)
    if ( 'options' !== $post_id ) {
        return;
    }

    // Создаём минимальный объект поста для передачи в функцию регенерации
    $fake_post = new WP_Post( (object) [
        'ID'        => 0,
        'post_type' => 'options',
        'post_name' => '',
    ] );

    // Регенерируем главную страницу при любом изменении глобальных настроек
    studio_trigger_isr_revalidate( 0, $fake_post );
} );
