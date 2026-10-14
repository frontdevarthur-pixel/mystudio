<?php
/**
 * Основной файл темы Studio Headless.
 * Подключает все модули: типы записей, ACF-поля, REST API, CORS, ISR-вебхук и др.
 *
 * @package StudioHeadless
 */

// Запрещаем прямой доступ к файлу
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Базовый путь к директории includes/
define( 'STUDIO_THEME_DIR', get_template_directory() );

/**
 * Подключение модулей темы.
 * Файлы будут добавляться по мере реализации соответствующих задач.
 */

// Регистрация кастомных типов записей (CPT)
require_once STUDIO_THEME_DIR . '/includes/post-types.php';

// Регистрация ACF-групп полей
require_once STUDIO_THEME_DIR . '/includes/acf-fields.php';

// Кастомные REST API эндпоинты
require_once STUDIO_THEME_DIR . '/includes/rest-api.php';

// Настройка CORS-заголовков
require_once STUDIO_THEME_DIR . '/includes/cors.php';

// ISR-вебхук для регенерации Next.js
require_once STUDIO_THEME_DIR . '/includes/isr-webhook.php';

// Обработчик контактных заявок
require_once STUDIO_THEME_DIR . '/includes/contact-handler.php';

// Страница настроек темы в WordPress Admin
require_once STUDIO_THEME_DIR . '/includes/admin-settings.php';

/**
 * Базовая поддержка функций темы.
 */
add_action( 'after_setup_theme', function () {
    // Поддержка миниатюр записей
    add_theme_support( 'post-thumbnails' );

    // Поддержка тега <title>
    add_theme_support( 'title-tag' );
} );
