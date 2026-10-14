<?php
/**
 * Регистрация ACF-групп полей для темы Studio Headless.
 * Все группы регистрируются через acf_add_local_field_group() без записи в БД.
 * ACF Options Pages создаются для управления глобальными настройками секций.
 *
 * @package StudioHeadless
 */

// Запрещаем прямой доступ к файлу
defined( 'ABSPATH' ) || exit;

/**
 * Регистрация ACF Options Pages.
 * Страницы появляются в меню WordPress Admin и позволяют редактировать
 * глобальные настройки, не привязанные к конкретным записям.
 */
add_action( 'acf/init', function (): void {

    // Проверяем наличие функции ACF Options Pages
    if ( ! function_exists( 'acf_add_options_page' ) ) {
        return;
    }

    // Страница общих настроек студии
    acf_add_options_page( [
        'page_title'  => 'Настройки студии',
        'menu_title'  => 'Studio Settings',
        'menu_slug'   => 'studio-settings',
        'capability'  => 'manage_options',
        'icon_url'    => 'dashicons-admin-customizer',
        'position'    => 59,
        'redirect'    => false,
    ] );

    // Страница настроек Hero-секции
    acf_add_options_page( [
        'page_title'  => 'Hero-секция',
        'menu_title'  => 'Hero Section',
        'menu_slug'   => 'hero-section',
        'capability'  => 'manage_options',
        'icon_url'    => 'dashicons-format-image',
        'position'    => 60,
        'redirect'    => false,
    ] );

    // Страница настроек секции «О нас»
    acf_add_options_page( [
        'page_title'  => 'О нас',
        'menu_title'  => 'About Section',
        'menu_slug'   => 'about-section',
        'capability'  => 'manage_options',
        'icon_url'    => 'dashicons-info',
        'position'    => 61,
        'redirect'    => false,
    ] );
} );


/**
 * Регистрация всех ACF-групп полей.
 * Используем локальную регистрацию (PHP) — быстрее и не требует записи в БД.
 */
add_action( 'acf/init', function (): void {

    // Проверяем наличие функции регистрации групп ACF
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    // ─── 1. Глобальные настройки студии ──────────────────────────────────────
    acf_add_local_field_group( [
        'key'      => 'group_studio_settings',
        'title'    => 'Настройки студии',
        'fields'   => [
            [
                'key'   => 'field_site_name',
                'label' => 'Название сайта',
                'name'  => 'site_name',
                'type'  => 'text',
            ],
            [
                'key'   => 'field_logo',
                'label' => 'Логотип',
                'name'  => 'logo',
                'type'  => 'image',
                'return_format' => 'array',
                'preview_size'  => 'medium',
            ],
            [
                'key'   => 'field_favicon',
                'label' => 'Фавиконка',
                'name'  => 'favicon',
                'type'  => 'image',
                'return_format' => 'array',
                'preview_size'  => 'thumbnail',
            ],
            [
                'key'   => 'field_email',
                'label' => 'Email',
                'name'  => 'email',
                'type'  => 'email',
            ],
            [
                'key'   => 'field_phone',
                'label' => 'Телефон',
                'name'  => 'phone',
                'type'  => 'text',
            ],
            [
                'key'   => 'field_address',
                'label' => 'Адрес',
                'name'  => 'address',
                'type'  => 'textarea',
                'rows'  => 3,
            ],
            // Репитер социальных ссылок
            [
                'key'        => 'field_social_links',
                'label'      => 'Социальные сети',
                'name'       => 'social_links',
                'type'       => 'repeater',
                'layout'     => 'table',
                'button_label' => 'Добавить ссылку',
                'sub_fields' => [
                    [
                        'key'   => 'field_social_platform',
                        'label' => 'Платформа',
                        'name'  => 'platform',
                        'type'  => 'text',
                    ],
                    [
                        'key'   => 'field_social_url',
                        'label' => 'URL',
                        'name'  => 'url',
                        'type'  => 'url',
                    ],
                    [
                        'key'   => 'field_social_label',
                        'label' => 'Метка',
                        'name'  => 'label',
                        'type'  => 'text',
                    ],
                ],
            ],
            // SEO-поля
            [
                'key'   => 'field_seo_title',
                'label' => 'SEO-заголовок',
                'name'  => 'seo_title',
                'type'  => 'text',
            ],
            [
                'key'   => 'field_seo_description',
                'label' => 'SEO-описание',
                'name'  => 'seo_description',
                'type'  => 'textarea',
                'rows'  => 3,
            ],
            [
                'key'   => 'field_og_image',
                'label' => 'OG-изображение',
                'name'  => 'og_image',
                'type'  => 'image',
                'return_format' => 'array',
                'preview_size'  => 'medium',
            ],
            [
                'key'     => 'field_twitter_card',
                'label'   => 'Twitter Card',
                'name'    => 'twitter_card',
                'type'    => 'select',
                'choices' => [
                    'summary'             => 'Summary',
                    'summary_large_image' => 'Summary Large Image',
                ],
                'default_value' => 'summary_large_image',
            ],
            // Координаты офиса для карты
            [
                'key'   => 'field_map_lat',
                'label' => 'Широта (latitude)',
                'name'  => 'map_lat',
                'type'  => 'number',
                'step'  => 'any',
            ],
            [
                'key'   => 'field_map_lng',
                'label' => 'Долгота (longitude)',
                'name'  => 'map_lng',
                'type'  => 'number',
                'step'  => 'any',
            ],
            [
                'key'   => 'field_office_marker_label',
                'label' => 'Метка маркера на карте',
                'name'  => 'office_marker_label',
                'type'  => 'text',
            ],
        ],
        'location' => [
            [ [ 'param' => 'options_page', 'operator' => '==', 'value' => 'studio-settings' ] ],
        ],
        'active' => true,
    ] );

    // ─── 2. Hero-секция ───────────────────────────────────────────────────────
    acf_add_local_field_group( [
        'key'    => 'group_hero_section',
        'title'  => 'Hero-секция',
        'fields' => [
            [
                'key'   => 'field_hero_heading',
                'label' => 'Заголовок (UK)',
                'name'  => 'heading',
                'type'  => 'text',
            ],
            [
                'key'   => 'field_hero_heading_en',
                'label' => 'Заголовок (EN)',
                'name'  => 'heading_en',
                'type'  => 'text',
            ],
            [
                'key'   => 'field_hero_subheading',
                'label' => 'Подзаголовок (UK)',
                'name'  => 'subheading',
                'type'  => 'textarea',
                'rows'  => 3,
            ],
            [
                'key'   => 'field_hero_subheading_en',
                'label' => 'Подзаголовок (EN)',
                'name'  => 'subheading_en',
                'type'  => 'textarea',
                'rows'  => 3,
            ],
            [
                'key'   => 'field_hero_cta_text',
                'label' => 'Текст кнопки CTA (UK)',
                'name'  => 'cta_text',
                'type'  => 'text',
            ],
            [
                'key'   => 'field_hero_cta_text_en',
                'label' => 'Текст кнопки CTA (EN)',
                'name'  => 'cta_text_en',
                'type'  => 'text',
            ],
            [
                'key'         => 'field_hero_cta_anchor',
                'label'       => 'Якорь кнопки CTA',
                'name'        => 'cta_anchor',
                'type'        => 'text',
                'placeholder' => '#contact',
                'instructions' => 'Якорная ссылка, например #contact или #portfolio',
            ],
            [
                'key'     => 'field_hero_background_type',
                'label'   => 'Тип фона',
                'name'    => 'background_type',
                'type'    => 'select',
                'choices' => [
                    'gradient' => 'Градиент',
                    'image'    => 'Изображение',
                    'video'    => 'Видео',
                ],
                'default_value' => 'gradient',
            ],
            [
                'key'               => 'field_hero_background_gradient',
                'label'             => 'CSS-градиент фона',
                'name'              => 'background_gradient',
                'type'              => 'text',
                'placeholder'       => 'linear-gradient(135deg, #1a1a2e 0%, #16213e 100%)',
                'conditional_logic' => [
                    [ [ 'field' => 'field_hero_background_type', 'operator' => '==', 'value' => 'gradient' ] ],
                ],
            ],
            [
                'key'               => 'field_hero_background_image',
                'label'             => 'Фоновое изображение',
                'name'              => 'background_image',
                'type'              => 'image',
                'return_format'     => 'array',
                'preview_size'      => 'medium',
                'conditional_logic' => [
                    [ [ 'field' => 'field_hero_background_type', 'operator' => '==', 'value' => 'image' ] ],
                ],
            ],
            [
                'key'               => 'field_hero_background_video',
                'label'             => 'Фоновое видео',
                'name'              => 'background_video',
                'type'              => 'file',
                'return_format'     => 'array',
                'mime_types'        => 'mp4,webm',
                'conditional_logic' => [
                    [ [ 'field' => 'field_hero_background_type', 'operator' => '==', 'value' => 'video' ] ],
                ],
            ],
        ],
        'location' => [
            [ [ 'param' => 'options_page', 'operator' => '==', 'value' => 'hero-section' ] ],
        ],
        'active' => true,
    ] );

    // ─── 3. Секция «О нас» ────────────────────────────────────────────────────
    acf_add_local_field_group( [
        'key'    => 'group_about_section',
        'title'  => 'Секция «О нас»',
        'fields' => [
            [
                'key'   => 'field_about_title',
                'label' => 'Заголовок раздела (UK)',
                'name'  => 'about_title',
                'type'  => 'text',
            ],
            [
                'key'   => 'field_about_title_en',
                'label' => 'Заголовок раздела (EN)',
                'name'  => 'about_title_en',
                'type'  => 'text',
            ],
            [
                'key'     => 'field_about_history',
                'label'   => 'История (UK)',
                'name'    => 'history',
                'type'    => 'wysiwyg',
                'toolbar' => 'full',
                'media_upload' => 1,
            ],
            [
                'key'     => 'field_about_history_en',
                'label'   => 'История (EN)',
                'name'    => 'history_en',
                'type'    => 'wysiwyg',
                'toolbar' => 'full',
                'media_upload' => 1,
            ],
            [
                'key'   => 'field_about_mission',
                'label' => 'Місія (UK)',
                'name'  => 'mission',
                'type'  => 'textarea',
                'rows'  => 4,
            ],
            [
                'key'   => 'field_about_mission_en',
                'label' => 'Mission (EN)',
                'name'  => 'mission_en',
                'type'  => 'textarea',
                'rows'  => 4,
            ],
            // Репитер ценностей
            [
                'key'        => 'field_about_values',
                'label'      => 'Цінності (UK)',
                'name'       => 'values',
                'type'       => 'repeater',
                'layout'     => 'table',
                'button_label' => 'Додати цінність',
                'sub_fields' => [
                    [
                        'key'   => 'field_about_value_item',
                        'label' => 'Цінність',
                        'name'  => 'value_item',
                        'type'  => 'text',
                    ],
                ],
            ],
            [
                'key'        => 'field_about_values_en',
                'label'      => 'Values (EN)',
                'name'       => 'values_en',
                'type'       => 'repeater',
                'layout'     => 'table',
                'button_label' => 'Add value',
                'sub_fields' => [
                    [
                        'key'   => 'field_about_value_item_en',
                        'label' => 'Value',
                        'name'  => 'value_item',
                        'type'  => 'text',
                    ],
                ],
            ],
            // Репитер статистики
            [
                'key'        => 'field_about_stats',
                'label'      => 'Статистика',
                'name'       => 'stats',
                'type'       => 'repeater',
                'layout'     => 'table',
                'button_label' => 'Добавить показатель',
                'sub_fields' => [
                    [
                        'key'   => 'field_stat_label',
                        'label' => 'Подпись',
                        'name'  => 'label',
                        'type'  => 'text',
                    ],
                    [
                        'key'  => 'field_stat_value',
                        'label' => 'Значение',
                        'name'  => 'value',
                        'type'  => 'number',
                    ],
                    [
                        'key'   => 'field_stat_suffix',
                        'label' => 'Суффикс',
                        'name'  => 'suffix',
                        'type'  => 'text',
                        'placeholder' => '+',
                    ],
                ],
            ],
        ],
        'location' => [
            [ [ 'param' => 'options_page', 'operator' => '==', 'value' => 'about-section' ] ],
        ],
        'active' => true,
    ] );

    // ─── 4. Поля портфолио (CPT portfolio) ───────────────────────────────────
    acf_add_local_field_group( [
        'key'    => 'group_portfolio_fields',
        'title'  => 'Поля проекта',
        'fields' => [
            [
                'key'   => 'field_portfolio_short_desc',
                'label' => 'Краткое описание',
                'name'  => 'short_description',
                'type'  => 'textarea',
                'rows'  => 3,
            ],
            [
                'key'   => 'field_portfolio_full_desc',
                'label' => 'Полное описание',
                'name'  => 'full_description',
                'type'  => 'wysiwyg',
                'toolbar' => 'full',
            ],
            [
                'key'           => 'field_portfolio_gallery',
                'label'         => 'Галерея',
                'name'          => 'gallery',
                'type'          => 'gallery',
                'return_format' => 'array',
                'preview_size'  => 'medium',
            ],
            // Репитер технологий проекта
            [
                'key'        => 'field_portfolio_technologies',
                'label'      => 'Технологии',
                'name'       => 'technologies',
                'type'       => 'repeater',
                'layout'     => 'table',
                'button_label' => 'Добавить технологию',
                'sub_fields' => [
                    [
                        'key'   => 'field_portfolio_tech_name',
                        'label' => 'Название',
                        'name'  => 'tech_name',
                        'type'  => 'text',
                    ],
                ],
            ],
            [
                'key'   => 'field_portfolio_project_url',
                'label' => 'Ссылка на проект',
                'name'  => 'project_url',
                'type'  => 'url',
            ],
            [
                'key'           => 'field_portfolio_sort_order',
                'label'         => 'Порядок сортировки',
                'name'          => 'sort_order',
                'type'          => 'number',
                'default_value' => 0,
                'min'           => 0,
            ],
            [
                'key'           => 'field_portfolio_is_visible',
                'label'         => 'Отображать на сайте',
                'name'          => 'is_visible',
                'type'          => 'true_false',
                'default_value' => 1,
                'ui'            => 1,
            ],
        ],
        'location' => [
            [ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'portfolio' ] ],
        ],
        'active' => true,
    ] );

    // ─── 5. Поля услуг (CPT services) ─────────────────────────────────────────
    acf_add_local_field_group( [
        'key'    => 'group_services_fields',
        'title'  => 'Поля услуги',
        'fields' => [
            [
                'key'   => 'field_service_description',
                'label' => 'Описание',
                'name'  => 'description',
                'type'  => 'textarea',
                'rows'  => 4,
            ],
            [
                'key'           => 'field_service_icon',
                'label'         => 'Иконка',
                'name'          => 'icon',
                'type'          => 'image',
                'return_format' => 'array',
                'preview_size'  => 'thumbnail',
            ],
            [
                'key'           => 'field_service_sort_order',
                'label'         => 'Порядок сортировки',
                'name'          => 'sort_order',
                'type'          => 'number',
                'default_value' => 0,
                'min'           => 0,
            ],
            [
                'key'           => 'field_service_is_visible',
                'label'         => 'Отображать на сайте',
                'name'          => 'is_visible',
                'type'          => 'true_false',
                'default_value' => 1,
                'ui'            => 1,
            ],
        ],
        'location' => [
            [ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'services' ] ],
        ],
        'active' => true,
    ] );

    // ─── 6. Поля технологий (CPT technologies) ────────────────────────────────
    acf_add_local_field_group( [
        'key'    => 'group_technologies_fields',
        'title'  => 'Поля технологии',
        'fields' => [
            [
                'key'           => 'field_tech_logo',
                'label'         => 'Логотип',
                'name'          => 'logo',
                'type'          => 'image',
                'return_format' => 'array',
                'preview_size'  => 'thumbnail',
            ],
            [
                'key'     => 'field_tech_category',
                'label'   => 'Категория',
                'name'    => 'category',
                'type'    => 'select',
                'choices' => [
                    'Frontend' => 'Frontend',
                    'Backend'  => 'Backend',
                    'CMS'      => 'CMS',
                    'DevOps'   => 'DevOps',
                    'Mobile'   => 'Mobile',
                ],
                'default_value' => 'Frontend',
            ],
            [
                'key'           => 'field_tech_sort_order',
                'label'         => 'Порядок сортировки',
                'name'          => 'sort_order',
                'type'          => 'number',
                'default_value' => 0,
                'min'           => 0,
            ],
            [
                'key'           => 'field_tech_is_visible',
                'label'         => 'Отображать на сайте',
                'name'          => 'is_visible',
                'type'          => 'true_false',
                'default_value' => 1,
                'ui'            => 1,
            ],
        ],
        'location' => [
            [ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'technologies' ] ],
        ],
        'active' => true,
    ] );

    // ─── 7. Поля сотрудников (CPT team) ──────────────────────────────────────
    acf_add_local_field_group( [
        'key'    => 'group_team_fields',
        'title'  => 'Поля сотрудника',
        'fields' => [
            [
                'key'   => 'field_team_position',
                'label' => 'Должность',
                'name'  => 'position',
                'type'  => 'text',
            ],
            [
                'key'           => 'field_team_sort_order',
                'label'         => 'Порядок сортировки',
                'name'          => 'sort_order',
                'type'          => 'number',
                'default_value' => 0,
                'min'           => 0,
            ],
        ],
        'location' => [
            [ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'team' ] ],
        ],
        'active' => true,
    ] );

    // ─── 8. Поля географии (CPT geography) ────────────────────────────────────
    acf_add_local_field_group( [
        'key'    => 'group_geography_fields',
        'title'  => 'Поля страны',
        'fields' => [
            [
                'key'         => 'field_geo_country_code',
                'label'       => 'Код страны (ISO 3166-1 alpha-2)',
                'name'        => 'country_code',
                'type'        => 'text',
                'placeholder' => 'RU',
                'maxlength'   => 2,
            ],
            [
                'key'           => 'field_geo_projects_count',
                'label'         => 'Количество проектов',
                'name'          => 'projects_count',
                'type'          => 'number',
                'default_value' => 0,
                'min'           => 0,
            ],
            [
                'key'           => 'field_geo_clients_count',
                'label'         => 'Количество клиентов',
                'name'          => 'clients_count',
                'type'          => 'number',
                'default_value' => 0,
                'min'           => 0,
            ],
            [
                'key'   => 'field_geo_tooltip_text',
                'label' => 'Текст тултипа',
                'name'  => 'tooltip_text',
                'type'  => 'text',
            ],
        ],
        'location' => [
            [ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'geography' ] ],
        ],
        'active' => true,
    ] );
} );
