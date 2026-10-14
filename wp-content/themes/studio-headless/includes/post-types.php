<?php
/**
 * Регистрация кастомных типов записей (CPT) для темы Studio Headless.
 * Все CPT доступны через REST API и отображаются в WordPress Admin.
 *
 * @package StudioHeadless
 */

// Запрещаем прямой доступ к файлу
defined( 'ABSPATH' ) || exit;

/**
 * Регистрирует все кастомные типы записей темы.
 * Вызывается через хук 'init'.
 */
function studio_register_post_types(): void {

    // ─── Портфолио ────────────────────────────────────────────────────────────
    register_post_type( 'portfolio', [
        'labels'       => [
            'name'               => 'Портфолио',
            'singular_name'      => 'Проект',
            'menu_name'          => 'Портфолио',
            'add_new'            => 'Добавить проект',
            'add_new_item'       => 'Добавить новый проект',
            'edit_item'          => 'Редактировать проект',
            'new_item'           => 'Новый проект',
            'view_item'          => 'Смотреть проект',
            'search_items'       => 'Найти проекты',
            'not_found'          => 'Проекты не найдены',
            'not_found_in_trash' => 'В корзине нет проектов',
        ],
        'public'        => true,
        'show_in_rest'  => true,
        'menu_icon'     => 'dashicons-portfolio',
        'supports'      => [ 'title', 'thumbnail', 'custom-fields' ],
        'has_archive'   => false,
        'rewrite'       => [ 'slug' => 'portfolio' ],
    ] );

    // ─── Услуги ───────────────────────────────────────────────────────────────
    register_post_type( 'services', [
        'labels'       => [
            'name'               => 'Услуги',
            'singular_name'      => 'Услуга',
            'menu_name'          => 'Услуги',
            'add_new'            => 'Добавить услугу',
            'add_new_item'       => 'Добавить новую услугу',
            'edit_item'          => 'Редактировать услугу',
            'new_item'           => 'Новая услуга',
            'view_item'          => 'Смотреть услугу',
            'search_items'       => 'Найти услуги',
            'not_found'          => 'Услуги не найдены',
            'not_found_in_trash' => 'В корзине нет услуг',
        ],
        'public'        => true,
        'show_in_rest'  => true,
        'menu_icon'     => 'dashicons-admin-tools',
        'supports'      => [ 'title', 'thumbnail', 'custom-fields' ],
        'has_archive'   => false,
        'rewrite'       => [ 'slug' => 'services' ],
    ] );

    // ─── Технологии ───────────────────────────────────────────────────────────
    register_post_type( 'technologies', [
        'labels'       => [
            'name'               => 'Технологии',
            'singular_name'      => 'Технология',
            'menu_name'          => 'Технологии',
            'add_new'            => 'Добавить технологию',
            'add_new_item'       => 'Добавить новую технологию',
            'edit_item'          => 'Редактировать технологию',
            'new_item'           => 'Новая технология',
            'view_item'          => 'Смотреть технологию',
            'search_items'       => 'Найти технологии',
            'not_found'          => 'Технологии не найдены',
            'not_found_in_trash' => 'В корзине нет технологий',
        ],
        'public'        => true,
        'show_in_rest'  => true,
        'menu_icon'     => 'dashicons-editor-code',
        'supports'      => [ 'title', 'thumbnail', 'custom-fields' ],
        'has_archive'   => false,
        'rewrite'       => [ 'slug' => 'technologies' ],
    ] );

    // ─── Команда ──────────────────────────────────────────────────────────────
    register_post_type( 'team', [
        'labels'       => [
            'name'               => 'Команда',
            'singular_name'      => 'Сотрудник',
            'menu_name'          => 'Команда',
            'add_new'            => 'Добавить сотрудника',
            'add_new_item'       => 'Добавить нового сотрудника',
            'edit_item'          => 'Редактировать сотрудника',
            'new_item'           => 'Новый сотрудник',
            'view_item'          => 'Смотреть профиль',
            'search_items'       => 'Найти сотрудников',
            'not_found'          => 'Сотрудники не найдены',
            'not_found_in_trash' => 'В корзине нет сотрудников',
        ],
        'public'        => true,
        'show_in_rest'  => true,
        'menu_icon'     => 'dashicons-groups',
        'supports'      => [ 'title', 'thumbnail', 'custom-fields' ],
        'has_archive'   => false,
        'rewrite'       => [ 'slug' => 'team' ],
    ] );

    // ─── География ────────────────────────────────────────────────────────────
    register_post_type( 'geography', [
        'labels'       => [
            'name'               => 'География',
            'singular_name'      => 'Страна',
            'menu_name'          => 'География',
            'add_new'            => 'Добавить страну',
            'add_new_item'       => 'Добавить новую страну',
            'edit_item'          => 'Редактировать страну',
            'new_item'           => 'Новая страна',
            'view_item'          => 'Смотреть страну',
            'search_items'       => 'Найти страны',
            'not_found'          => 'Страны не найдены',
            'not_found_in_trash' => 'В корзине нет стран',
        ],
        'public'        => true,
        'show_in_rest'  => true,
        'menu_icon'     => 'dashicons-location-alt',
        'supports'      => [ 'title', 'thumbnail', 'custom-fields' ],
        'has_archive'   => false,
        'rewrite'       => [ 'slug' => 'geography' ],
    ] );

    // ─── Заявки с контактной формы ────────────────────────────────────────────
    // Не публичный тип — только для хранения заявок в Admin
    register_post_type( 'contact_request', [
        'labels'       => [
            'name'               => 'Заявки',
            'singular_name'      => 'Заявка',
            'menu_name'          => 'Заявки',
            'add_new'            => 'Добавить заявку',
            'add_new_item'       => 'Добавить новую заявку',
            'edit_item'          => 'Просмотреть заявку',
            'new_item'           => 'Новая заявка',
            'view_item'          => 'Смотреть заявку',
            'search_items'       => 'Найти заявки',
            'not_found'          => 'Заявки не найдены',
            'not_found_in_trash' => 'В корзине нет заявок',
        ],
        'public'        => false,      // Не отображается на фронтенде
        'show_ui'       => true,       // Но показывается в Admin
        'show_in_rest'  => false,      // Не в REST API (конфиденциальность)
        'show_in_menu'  => true,
        'menu_icon'     => 'dashicons-email-alt',
        'supports'      => [ 'title', 'custom-fields' ],
        'capabilities'  => [
            'create_posts' => 'do_not_allow', // Только программное создание
        ],
        'map_meta_cap'  => true,
    ] );
}

// Подключаем регистрацию CPT к хуку инициализации WordPress
add_action( 'init', 'studio_register_post_types' );
