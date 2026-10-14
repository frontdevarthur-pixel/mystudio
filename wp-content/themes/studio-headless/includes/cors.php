<?php
/**
 * Настройка CORS-заголовков для REST API WordPress.
 * Разрешает запросы от Next.js-фронтенда к /wp-json/*.
 *
 * @package StudioHeadless
 */

// Запрещаем прямой доступ к файлу
defined( 'ABSPATH' ) || exit;

/**
 * Добавляем CORS-заголовки ко всем ответам REST API.
 * Приоритет 15 — выполняется после стандартных хуков WordPress.
 */
add_action( 'rest_api_init', function (): void {

    // Добавляем заголовки через фильтр rest_pre_serve_request
    add_filter( 'rest_pre_serve_request', function ( bool $served ): bool {

        // Определяем разрешённый Origin из константы wp-config.php или разрешаем все
        $allowed_origin = defined( 'NEXTJS_FRONTEND_URL' ) ? NEXTJS_FRONTEND_URL : '*';

        // Основные CORS-заголовки
        header( 'Access-Control-Allow-Origin: ' . $allowed_origin );
        header( 'Access-Control-Allow-Methods: GET, POST, OPTIONS' );
        header( 'Access-Control-Allow-Headers: Content-Type, Authorization' );
        header( 'Access-Control-Allow-Credentials: true' );

        return $served;
    } );

}, 15 );


/**
 * Обрабатываем OPTIONS preflight-запросы до загрузки WordPress.
 * Браузеры посылают preflight перед реальным запросом для проверки CORS.
 */
add_action( 'init', function (): void {

    // Проверяем, что это OPTIONS preflight-запрос к REST API
    if (
        isset( $_SERVER['REQUEST_METHOD'] ) &&
        'OPTIONS' === $_SERVER['REQUEST_METHOD'] &&
        isset( $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'] )
    ) {
        // Определяем разрешённый Origin
        $allowed_origin = defined( 'NEXTJS_FRONTEND_URL' ) ? NEXTJS_FRONTEND_URL : '*';

        // Отправляем preflight-ответ с разрешениями CORS
        header( 'Access-Control-Allow-Origin: ' . $allowed_origin );
        header( 'Access-Control-Allow-Methods: GET, POST, OPTIONS' );
        header( 'Access-Control-Allow-Headers: Content-Type, Authorization' );
        header( 'Access-Control-Allow-Credentials: true' );
        header( 'Access-Control-Max-Age: 86400' ); // Кешируем preflight на 24 часа

        // Отвечаем 204 No Content и завершаем выполнение
        status_header( 204 );
        exit;
    }
} );
