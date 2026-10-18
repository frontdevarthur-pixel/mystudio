<?php
/**
 * Регистрация ACF-групп полей для темы Studio Headless.
 *
 * @package StudioHeadless
 */

defined( 'ABSPATH' ) || exit;

// ─── Options Pages ────────────────────────────────────────────────────────────
add_action( 'acf/init', function (): void {
    if ( ! function_exists( 'acf_add_options_page' ) ) {
        return;
    }

    acf_add_options_page( [
        'page_title' => 'Studio Settings',
        'menu_title' => 'Studio Settings',
        'menu_slug'  => 'studio-settings',
        'capability' => 'manage_options',
        'icon_url'   => 'dashicons-admin-customizer',
        'position'   => 59,
        'redirect'   => false,
    ] );

    acf_add_options_page( [
        'page_title' => 'Hero Section',
        'menu_title' => 'Hero Section',
        'menu_slug'  => 'hero-section',
        'capability' => 'manage_options',
        'icon_url'   => 'dashicons-format-image',
        'position'   => 60,
        'redirect'   => false,
    ] );

    acf_add_options_page( [
        'page_title' => 'About Section',
        'menu_title' => 'About Section',
        'menu_slug'  => 'about-section',
        'capability' => 'manage_options',
        'icon_url'   => 'dashicons-info',
        'position'   => 61,
        'redirect'   => false,
    ] );

    acf_add_options_page( [
        'page_title' => 'Services Section',
        'menu_title' => 'Services Section',
        'menu_slug'  => 'services-section',
        'capability' => 'manage_options',
        'icon_url'   => 'dashicons-screenoptions',
        'position'   => 62,
        'redirect'   => false,
    ] );

    acf_add_options_page( [
        'page_title' => 'Other Sections',
        'menu_title' => 'Other Sections',
        'menu_slug'  => 'other-sections',
        'capability' => 'manage_options',
        'icon_url'   => 'dashicons-layout',
        'position'   => 63,
        'redirect'   => false,
    ] );
} );

// ─── Field Groups ─────────────────────────────────────────────────────────────
add_action( 'acf/init', function (): void {
    if ( ! function_exists( 'acf_add_local_field_group' ) ) {
        return;
    }

    // ─── 1. Studio Settings ──────────────────────────────────────────────────
    acf_add_local_field_group( [
        'key'    => 'group_studio_settings',
        'title'  => 'Studio Settings',
        'fields' => [
            [ 'key' => 'field_site_name',           'label' => 'Site Name (UK)',    'name' => 'site_name',           'type' => 'text' ],
            [ 'key' => 'field_site_name_en',        'label' => 'Site Name (EN)',    'name' => 'site_name_en',        'type' => 'text' ],
            [ 'key' => 'field_site_description',    'label' => 'Site Description (UK)', 'name' => 'site_description',    'type' => 'textarea', 'rows' => 2 ],
            [ 'key' => 'field_site_description_en', 'label' => 'Site Description (EN)', 'name' => 'site_description_en', 'type' => 'textarea', 'rows' => 2 ],
            [ 'key' => 'field_logo',                'label' => 'Logo',              'name' => 'logo',                'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium' ],
            [ 'key' => 'field_favicon',             'label' => 'Favicon',           'name' => 'favicon',             'type' => 'image', 'return_format' => 'array', 'preview_size' => 'thumbnail' ],
            [ 'key' => 'field_email',               'label' => 'Email',             'name' => 'email',               'type' => 'email' ],
            [ 'key' => 'field_phone',               'label' => 'Phone',             'name' => 'phone',               'type' => 'text' ],
            [ 'key' => 'field_address',             'label' => 'Address',           'name' => 'address',             'type' => 'textarea', 'rows' => 3 ],
            [ 'key' => 'field_address_en',          'label' => 'Address (EN)',      'name' => 'address_en',          'type' => 'textarea', 'rows' => 3 ],
            [
                'key'          => 'field_social_links',
                'label'        => 'Social Links',
                'name'         => 'social_links',
                'type'         => 'repeater',
                'layout'       => 'table',
                'button_label' => 'Add link',
                'sub_fields'   => [
                    [ 'key' => 'field_social_platform', 'label' => 'Platform', 'name' => 'platform', 'type' => 'text' ],
                    [ 'key' => 'field_social_url',      'label' => 'URL',      'name' => 'url',      'type' => 'url' ],
                    [ 'key' => 'field_social_label',    'label' => 'Label',    'name' => 'label',    'type' => 'text' ],
                ],
            ],
            [ 'key' => 'field_seo_title',           'label' => 'SEO Title',         'name' => 'seo_title',           'type' => 'text' ],
            [ 'key' => 'field_seo_title_en',        'label' => 'SEO Title (EN)',    'name' => 'seo_title_en',        'type' => 'text' ],
            [ 'key' => 'field_seo_description',     'label' => 'SEO Description',   'name' => 'seo_description',     'type' => 'textarea', 'rows' => 3 ],
            [ 'key' => 'field_seo_description_en',  'label' => 'SEO Description (EN)', 'name' => 'seo_description_en', 'type' => 'textarea', 'rows' => 3 ],
            [ 'key' => 'field_og_image',            'label' => 'OG Image',          'name' => 'og_image',            'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium' ],
            [ 'key' => 'field_twitter_card',        'label' => 'Twitter Card',      'name' => 'twitter_card',        'type' => 'select', 'choices' => [ 'summary' => 'Summary', 'summary_large_image' => 'Summary Large Image' ], 'default_value' => 'summary_large_image' ],
            [ 'key' => 'field_map_lat',             'label' => 'Map Latitude',      'name' => 'map_lat',             'type' => 'number', 'step' => 'any' ],
            [ 'key' => 'field_map_lng',             'label' => 'Map Longitude',     'name' => 'map_lng',             'type' => 'number', 'step' => 'any' ],
            [ 'key' => 'field_office_marker_label', 'label' => 'Map Marker Label',  'name' => 'office_marker_label', 'type' => 'text' ],
            [ 'key' => 'field_office_marker_label_en', 'label' => 'Map Marker Label (EN)', 'name' => 'office_marker_label_en', 'type' => 'text' ],
        ],
        'location' => [ [ [ 'param' => 'options_page', 'operator' => '==', 'value' => 'studio-settings' ] ] ],
        'active'   => true,
    ] );


    // ─── 2. Hero Section ─────────────────────────────────────────────────────
    acf_add_local_field_group( [
        'key'    => 'group_hero_section',
        'title'  => 'Hero Section',
        'fields' => [
            [ 'key' => 'field_hero_heading',            'label' => 'Heading (UK)',           'name' => 'heading',            'type' => 'text' ],
            [ 'key' => 'field_hero_heading_en',         'label' => 'Heading (EN)',           'name' => 'heading_en',         'type' => 'text' ],
            [ 'key' => 'field_hero_subheading',         'label' => 'Subheading (UK)',        'name' => 'subheading',         'type' => 'textarea', 'rows' => 3 ],
            [ 'key' => 'field_hero_subheading_en',      'label' => 'Subheading (EN)',        'name' => 'subheading_en',      'type' => 'textarea', 'rows' => 3 ],
            [ 'key' => 'field_hero_cta_text',           'label' => 'CTA Button (UK)',        'name' => 'cta_text',           'type' => 'text' ],
            [ 'key' => 'field_hero_cta_text_en',        'label' => 'CTA Button (EN)',        'name' => 'cta_text_en',        'type' => 'text' ],
            [ 'key' => 'field_hero_cta_anchor',         'label' => 'CTA Anchor',             'name' => 'cta_anchor',         'type' => 'text', 'placeholder' => '#contact' ],
            [ 'key' => 'field_hero_secondary_cta',      'label' => 'Secondary Button (UK)',  'name' => 'hero_secondary_cta',    'type' => 'text', 'default_value' => 'Дивитися роботи' ],
            [ 'key' => 'field_hero_secondary_cta_en',   'label' => 'Secondary Button (EN)',  'name' => 'hero_secondary_cta_en', 'type' => 'text', 'default_value' => 'View portfolio' ],
            [
                'key'     => 'field_hero_background_type',
                'label'   => 'Background Type',
                'name'    => 'background_type',
                'type'    => 'select',
                'choices' => [ 'gradient' => 'Gradient', 'image' => 'Image', 'video' => 'Video' ],
                'default_value' => 'gradient',
            ],
            [ 'key' => 'field_hero_background_gradient', 'label' => 'Background Gradient', 'name' => 'background_gradient', 'type' => 'text',
              'conditional_logic' => [ [ [ 'field' => 'field_hero_background_type', 'operator' => '==', 'value' => 'gradient' ] ] ] ],
            [ 'key' => 'field_hero_background_image', 'label' => 'Background Image', 'name' => 'background_image', 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'medium',
              'conditional_logic' => [ [ [ 'field' => 'field_hero_background_type', 'operator' => '==', 'value' => 'image' ] ] ] ],
            [ 'key' => 'field_hero_background_video', 'label' => 'Background Video', 'name' => 'background_video', 'type' => 'file', 'return_format' => 'array', 'mime_types' => 'mp4,webm',
              'conditional_logic' => [ [ [ 'field' => 'field_hero_background_type', 'operator' => '==', 'value' => 'video' ] ] ] ],
        ],
        'location' => [ [ [ 'param' => 'options_page', 'operator' => '==', 'value' => 'hero-section' ] ] ],
        'active'   => true,
    ] );


    // ─── 3. About Section ────────────────────────────────────────────────────
    acf_add_local_field_group( [
        'key'    => 'group_about_section',
        'title'  => 'About Section',
        'fields' => [
            [ 'key' => 'field_about_title',         'label' => 'Section Title (UK)', 'name' => 'about_title',    'type' => 'text' ],
            [ 'key' => 'field_about_title_en',      'label' => 'Section Title (EN)', 'name' => 'about_title_en', 'type' => 'text' ],
            [ 'key' => 'field_about_history',       'label' => 'History (UK)',       'name' => 'history',        'type' => 'wysiwyg', 'toolbar' => 'full', 'media_upload' => 1 ],
            [ 'key' => 'field_about_history_en',    'label' => 'History (EN)',       'name' => 'history_en',     'type' => 'wysiwyg', 'toolbar' => 'full', 'media_upload' => 1 ],
            [ 'key' => 'field_about_mission',       'label' => 'Mission (UK)',       'name' => 'mission',        'type' => 'textarea', 'rows' => 4 ],
            [ 'key' => 'field_about_mission_en',    'label' => 'Mission (EN)',       'name' => 'mission_en',     'type' => 'textarea', 'rows' => 4 ],
            [
                'key' => 'field_about_values', 'label' => 'Values (UK)', 'name' => 'values', 'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Add value',
                'sub_fields' => [ [ 'key' => 'field_about_value_item', 'label' => 'Value', 'name' => 'value_item', 'type' => 'text' ] ],
            ],
            [
                'key' => 'field_about_values_en', 'label' => 'Values (EN)', 'name' => 'values_en', 'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Add value',
                'sub_fields' => [ [ 'key' => 'field_about_value_item_en', 'label' => 'Value', 'name' => 'value_item', 'type' => 'text' ] ],
            ],
            [
                'key' => 'field_about_stats', 'label' => 'Stats', 'name' => 'stats', 'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Add stat',
                'sub_fields' => [
                    [ 'key' => 'field_stat_label',    'label' => 'Label (UK)', 'name' => 'label',    'type' => 'text' ],
                    [ 'key' => 'field_stat_label_en', 'label' => 'Label (EN)', 'name' => 'label_en', 'type' => 'text' ],
                    [ 'key' => 'field_stat_value',    'label' => 'Value',      'name' => 'value',    'type' => 'number' ],
                    [ 'key' => 'field_stat_suffix',   'label' => 'Suffix',     'name' => 'suffix',   'type' => 'text', 'placeholder' => '+' ],
                ],
            ],
            // Stats CTA — прямо под статистикой
            [ 'key' => 'field_stats_cta_text',    'label' => 'Stats CTA Text (UK)',   'name' => 'stats_cta_text',    'type' => 'text', 'default_value' => 'Готові стати частиною цієї статистики?' ],
            [ 'key' => 'field_stats_cta_text_en', 'label' => 'Stats CTA Text (EN)',   'name' => 'stats_cta_text_en', 'type' => 'text', 'default_value' => 'Ready to be part of this statistics?' ],
            [ 'key' => 'field_stats_cta_btn',     'label' => 'Stats CTA Button (UK)', 'name' => 'stats_cta_btn',     'type' => 'text', 'default_value' => 'Обговорити проект →' ],
            [ 'key' => 'field_stats_cta_btn_en',  'label' => 'Stats CTA Button (EN)', 'name' => 'stats_cta_btn_en',  'type' => 'text', 'default_value' => 'Discuss project →' ],
            // Team
            [ 'key' => 'field_about_team_title',    'label' => 'Team Title (UK)',  'name' => 'about_team_title',    'type' => 'text', 'default_value' => 'Команда' ],
            [ 'key' => 'field_about_team_title_en', 'label' => 'Team Title (EN)',  'name' => 'about_team_title_en', 'type' => 'text', 'default_value' => 'Team' ],
            [ 'key' => 'field_about_cta',           'label' => 'CTA Button (UK)',  'name' => 'about_cta',           'type' => 'text', 'default_value' => 'Обговорити проект →' ],
            [ 'key' => 'field_about_cta_en',        'label' => 'CTA Button (EN)',  'name' => 'about_cta_en',        'type' => 'text', 'default_value' => 'Discuss project →' ],
        ],
        'location' => [ [ [ 'param' => 'options_page', 'operator' => '==', 'value' => 'about-section' ] ] ],
        'active'   => true,
    ] );


    // ─── 4. Services Section ─────────────────────────────────────────────────
    acf_add_local_field_group( [
        'key'    => 'group_services_section',
        'title'  => 'Services Section',
        'fields' => [
            [ 'key' => 'field_services_title',    'label' => 'Section Title (UK)',       'name' => 'services_title',       'type' => 'text', 'default_value' => 'Що ми вміємо' ],
            [ 'key' => 'field_services_title_en', 'label' => 'Section Title (EN)',       'name' => 'services_title_en',    'type' => 'text', 'default_value' => 'What we do' ],
            [ 'key' => 'field_services_desc',     'label' => 'Description (UK)',         'name' => 'services_section_desc',    'type' => 'textarea', 'rows' => 3, 'default_value' => 'Повний цикл — від ідеї та дизайну до запуску і підтримки' ],
            [ 'key' => 'field_services_desc_en',  'label' => 'Description (EN)',         'name' => 'services_section_desc_en', 'type' => 'textarea', 'rows' => 3, 'default_value' => 'Full cycle — from idea and design to launch and support' ],
            [ 'key' => 'field_services_cta',      'label' => 'CTA Button (UK)',          'name' => 'services_cta',         'type' => 'text', 'default_value' => 'Обговорити проект →' ],
            [ 'key' => 'field_services_cta_en',   'label' => 'CTA Button (EN)',          'name' => 'services_cta_en',      'type' => 'text', 'default_value' => 'Discuss project →' ],
        ],
        'location' => [ [ [ 'param' => 'options_page', 'operator' => '==', 'value' => 'services-section' ] ] ],
        'active'   => true,
    ] );

    // ─── 5. Other Sections (Technologies, Portfolio, Contact) ────────────────
    acf_add_local_field_group( [
        'key'    => 'group_other_sections',
        'title'  => 'Other Sections',
        'fields' => [
            // Technologies
            [ 'key' => 'field_tech_section_title',       'label' => 'Technologies Title (UK)',    'name' => 'technologies_title',      'type' => 'text', 'default_value' => 'Технології' ],
            [ 'key' => 'field_tech_section_title_en',    'label' => 'Technologies Title (EN)',    'name' => 'technologies_title_en',   'type' => 'text', 'default_value' => 'Technologies' ],
            [ 'key' => 'field_tech_section_subtitle',    'label' => 'Technologies Subtitle (UK)', 'name' => 'technologies_subtitle',   'type' => 'text', 'default_value' => 'Інструменти, якими ми будуємо продукти' ],
            [ 'key' => 'field_tech_section_subtitle_en', 'label' => 'Technologies Subtitle (EN)', 'name' => 'technologies_subtitle_en','type' => 'text', 'default_value' => 'Tools we use to build products' ],
            // Portfolio
            [ 'key' => 'field_portfolio_section_title',    'label' => 'Portfolio Title (UK)', 'name' => 'portfolio_title',    'type' => 'text', 'default_value' => 'Вибрані проекти' ],
            [ 'key' => 'field_portfolio_section_title_en', 'label' => 'Portfolio Title (EN)', 'name' => 'portfolio_title_en', 'type' => 'text', 'default_value' => 'Selected projects' ],
            // Contact
            [ 'key' => 'field_contact_section_title',    'label' => 'Contact Title (UK)', 'name' => 'contact_title',    'type' => 'text', 'default_value' => 'Готові починати?' ],
            [ 'key' => 'field_contact_section_title_en', 'label' => 'Contact Title (EN)', 'name' => 'contact_title_en', 'type' => 'text', 'default_value' => 'Ready to start?' ],
        ],
        'location' => [ [ [ 'param' => 'options_page', 'operator' => '==', 'value' => 'other-sections' ] ] ],
        'active'   => true,
    ] );


    // ─── 6. Portfolio CPT ────────────────────────────────────────────────────
    acf_add_local_field_group( [
        'key'    => 'group_portfolio_fields',
        'title'  => 'Portfolio Project',
        'fields' => [
            [ 'key' => 'field_portfolio_title_en',      'label' => 'Title (EN)',             'name' => 'title_en',          'type' => 'text' ],
            [ 'key' => 'field_portfolio_short_desc',    'label' => 'Short Description (UK)', 'name' => 'short_description',    'type' => 'textarea', 'rows' => 3 ],
            [ 'key' => 'field_portfolio_short_desc_en', 'label' => 'Short Description (EN)', 'name' => 'short_description_en', 'type' => 'textarea', 'rows' => 3 ],
            [ 'key' => 'field_portfolio_full_desc',     'label' => 'Full Description (UK)',  'name' => 'full_description',     'type' => 'wysiwyg', 'toolbar' => 'full' ],
            [ 'key' => 'field_portfolio_full_desc_en',  'label' => 'Full Description (EN)',  'name' => 'full_description_en',  'type' => 'wysiwyg', 'toolbar' => 'full' ],
            [ 'key' => 'field_portfolio_gallery',    'label' => 'Gallery',           'name' => 'gallery',           'type' => 'gallery', 'return_format' => 'array', 'preview_size' => 'medium' ],
            [
                'key' => 'field_portfolio_technologies', 'label' => 'Technologies', 'name' => 'technologies', 'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Add technology',
                'sub_fields' => [ [ 'key' => 'field_portfolio_tech_name', 'label' => 'Name', 'name' => 'tech_name', 'type' => 'text' ] ],
            ],
            [ 'key' => 'field_portfolio_project_url',  'label' => 'Project URL',     'name' => 'project_url',  'type' => 'url' ],
            [ 'key' => 'field_portfolio_sort_order',   'label' => 'Sort Order',      'name' => 'sort_order',   'type' => 'number', 'default_value' => 0, 'min' => 0 ],
            [ 'key' => 'field_portfolio_is_visible',   'label' => 'Show on site',    'name' => 'is_visible',   'type' => 'true_false', 'default_value' => 1, 'ui' => 1 ],
        ],
        'location' => [ [ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'portfolio' ] ] ],
        'active'   => true,
    ] );

    // ─── 7. Services CPT ─────────────────────────────────────────────────────
    acf_add_local_field_group( [
        'key'    => 'group_services_fields',
        'title'  => 'Service',
        'fields' => [
            [ 'key' => 'field_service_title_en',       'label' => 'Title (EN)',      'name' => 'title_en',       'type' => 'text' ],
            [ 'key' => 'field_service_description',    'label' => 'Description (UK)', 'name' => 'description',    'type' => 'textarea', 'rows' => 4 ],
            [ 'key' => 'field_service_description_en', 'label' => 'Description (EN)', 'name' => 'description_en', 'type' => 'textarea', 'rows' => 4 ],
            [ 'key' => 'field_service_icon',        'label' => 'Icon',        'name' => 'icon',        'type' => 'image', 'return_format' => 'array', 'preview_size' => 'thumbnail' ],
            [ 'key' => 'field_service_sort_order',  'label' => 'Sort Order',  'name' => 'sort_order',  'type' => 'number', 'default_value' => 0, 'min' => 0 ],
            [ 'key' => 'field_service_is_visible',  'label' => 'Show on site','name' => 'is_visible',  'type' => 'true_false', 'default_value' => 1, 'ui' => 1 ],
        ],
        'location' => [ [ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'services' ] ] ],
        'active'   => true,
    ] );

    // ─── 8. Technologies CPT ─────────────────────────────────────────────────
    acf_add_local_field_group( [
        'key'    => 'group_technologies_fields',
        'title'  => 'Technology',
        'fields' => [
            [ 'key' => 'field_tech_logo',       'label' => 'Logo',       'name' => 'logo',       'type' => 'image', 'return_format' => 'array', 'preview_size' => 'thumbnail' ],
            [ 'key' => 'field_tech_category',   'label' => 'Category',   'name' => 'category',   'type' => 'select',
              'choices' => [ 'Frontend' => 'Frontend', 'Backend' => 'Backend', 'CMS' => 'CMS', 'DevOps' => 'DevOps', 'Mobile' => 'Mobile' ],
              'default_value' => 'Frontend' ],
            [ 'key' => 'field_tech_sort_order', 'label' => 'Sort Order', 'name' => 'sort_order', 'type' => 'number', 'default_value' => 0, 'min' => 0 ],
            [ 'key' => 'field_tech_is_visible', 'label' => 'Show on site','name' => 'is_visible', 'type' => 'true_false', 'default_value' => 1, 'ui' => 1 ],
        ],
        'location' => [ [ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'technologies' ] ] ],
        'active'   => true,
    ] );

    // ─── 9. Team CPT ─────────────────────────────────────────────────────────
    acf_add_local_field_group( [
        'key'    => 'group_team_fields',
        'title'  => 'Team Member',
        'fields' => [
            [ 'key' => 'field_team_name_en',     'label' => 'Name (EN)',     'name' => 'name_en',     'type' => 'text' ],
            [ 'key' => 'field_team_position',    'label' => 'Position (UK)', 'name' => 'position',    'type' => 'text' ],
            [ 'key' => 'field_team_position_en', 'label' => 'Position (EN)', 'name' => 'position_en', 'type' => 'text' ],
            [ 'key' => 'field_team_sort_order',  'label' => 'Sort Order',    'name' => 'sort_order',  'type' => 'number', 'default_value' => 0, 'min' => 0 ],
        ],
        'location' => [ [ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'team' ] ] ],
        'active'   => true,
    ] );

    // ─── 10. Geography CPT ───────────────────────────────────────────────────
    acf_add_local_field_group( [
        'key'    => 'group_geography_fields',
        'title'  => 'Country',
        'fields' => [
            [ 'key' => 'field_geo_country_code',    'label' => 'Country Code (ISO)',  'name' => 'country_code',    'type' => 'text', 'maxlength' => 2 ],
            [ 'key' => 'field_geo_country_name_en', 'label' => 'Country Name (EN)',   'name' => 'country_name_en', 'type' => 'text' ],
            [ 'key' => 'field_geo_projects_count',  'label' => 'Projects Count',      'name' => 'projects_count',  'type' => 'number', 'default_value' => 0, 'min' => 0 ],
            [ 'key' => 'field_geo_clients_count',   'label' => 'Clients Count',       'name' => 'clients_count',   'type' => 'number', 'default_value' => 0, 'min' => 0 ],
            [ 'key' => 'field_geo_tooltip_text',    'label' => 'Tooltip Text (UK)',   'name' => 'tooltip_text',    'type' => 'text' ],
            [ 'key' => 'field_geo_tooltip_text_en', 'label' => 'Tooltip Text (EN)',   'name' => 'tooltip_text_en', 'type' => 'text' ],
        ],
        'location' => [ [ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'geography' ] ] ],
        'active'   => true,
    ] );

} );
