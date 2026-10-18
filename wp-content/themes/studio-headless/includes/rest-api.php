<?php
/**
 * Регистрация кастомных REST API эндпоинтов для Studio Headless.
 * Все эндпоинты доступны по пути /wp-json/studio/v1/*.
 * Данные формируются строго в соответствии с TypeScript-интерфейсами (types/api.ts).
 *
 * @package StudioHeadless
 */

// Запрещаем прямой доступ к файлу
defined( 'ABSPATH' ) || exit;

/**
 * Регистрация всех эндпоинтов при инициализации REST API.
 */
add_action( 'rest_api_init', function (): void {

    $namespace = 'studio/v1';

    $lang_arg = [
        'lang' => [
            'default'           => 'uk',
            'sanitize_callback' => 'sanitize_key',
        ],
    ];

    // ─── GET /settings ────────────────────────────────────────────────────────
    register_rest_route( $namespace, '/settings', [
        'methods'             => 'GET',
        'callback'            => 'studio_rest_get_settings',
        'permission_callback' => '__return_true',
        'args'                => $lang_arg,
    ] );

    // ─── GET /hero ────────────────────────────────────────────────────────────
    register_rest_route( $namespace, '/hero', [
        'methods'             => 'GET',
        'callback'            => 'studio_rest_get_hero',
        'permission_callback' => '__return_true',
        'args'                => $lang_arg,
    ] );

    // ─── GET /about ───────────────────────────────────────────────────────────
    register_rest_route( $namespace, '/about', [
        'methods'             => 'GET',
        'callback'            => 'studio_rest_get_about',
        'permission_callback' => '__return_true',
        'args'                => $lang_arg,
    ] );

    // ─── GET /services ────────────────────────────────────────────────────────
    register_rest_route( $namespace, '/services', [
        'methods'             => 'GET',
        'callback'            => 'studio_rest_get_services',
        'permission_callback' => '__return_true',
        'args'                => $lang_arg,
    ] );

    // ─── GET /portfolio ───────────────────────────────────────────────────────
    register_rest_route( $namespace, '/portfolio', [
        'methods'             => 'GET',
        'callback'            => 'studio_rest_get_portfolio',
        'permission_callback' => '__return_true',
        'args'                => $lang_arg,
    ] );

    // ─── GET /technologies ────────────────────────────────────────────────────
    register_rest_route( $namespace, '/technologies', [
        'methods'             => 'GET',
        'callback'            => 'studio_rest_get_technologies',
        'permission_callback' => '__return_true',
        'args'                => $lang_arg,
    ] );

    // ─── GET /team ────────────────────────────────────────────────────────────
    register_rest_route( $namespace, '/team', [
        'methods'             => 'GET',
        'callback'            => 'studio_rest_get_team',
        'permission_callback' => '__return_true',
        'args'                => $lang_arg,
    ] );

    // ─── GET /geography ───────────────────────────────────────────────────────
    register_rest_route( $namespace, '/geography', [
        'methods'             => 'GET',
        'callback'            => 'studio_rest_get_geography',
        'permission_callback' => '__return_true',
        'args'                => $lang_arg,
    ] );

    // ─── GET /labels ──────────────────────────────────────────────────────────
    register_rest_route( $namespace, '/labels', [
        'methods'             => 'GET',
        'callback'            => 'studio_rest_get_labels',
        'permission_callback' => '__return_true',
        'args'                => $lang_arg,
    ] );
} );


// ─── Вспомогательные функции ──────────────────────────────────────────────────

/**
 * Безопасно получает ACF-поле с поддержкой языка.
 * Если lang=en и есть поле field_en — возвращает его, иначе основное.
 *
 * @param string $field_name Имя поля ACF.
 * @param string $lang       Язык ('uk' или 'en').
 * @param mixed  $default    Значение по умолчанию.
 * @return mixed
 */
function studio_get_option_field( string $field_name, $default = '', string $lang = 'uk' ) {
    $target_field = ( 'en' === $lang ) ? $field_name . '_en' : $field_name;

    if ( function_exists( 'get_field' ) ) {
        $value = get_field( $target_field, 'option' );
        
        // Если для EN поле пустое, пробуем взять базовое (UK)
        if ( 'en' === $lang && ( $value === false || $value === null || $value === '' ) ) {
            $value = get_field( $field_name, 'option' );
        }
        
        return ( $value !== false && $value !== null && $value !== '' ) ? $value : $default;
    }

    // Запасной вариант через стандартные опции WP
    $value = get_option( 'options_' . $target_field, null );
    if ( 'en' === $lang && ( $value === null || $value === '' ) ) {
        $value = get_option( 'options_' . $field_name, null );
    }

    return ( $value !== null && $value !== '' ) ? $value : $default;
}

/**
 * Получает lang из запроса.
 */
function studio_get_lang( WP_REST_Request $request ): string {
    $lang = sanitize_key( $request->get_param( 'lang' ) ?? 'uk' );
    return in_array( $lang, [ 'uk', 'en' ], true ) ? $lang : 'uk';
}

/**
 * Получает URL изображения из ACF-поля типа image.
 * ACF может вернуть массив, ID вложения или URL — обрабатываем все случаи.
 *
 * @param mixed  $image_field Значение ACF image-поля.
 * @param string $size        Размер изображения (large, medium и т.д.).
 * @return string URL изображения или пустая строка.
 */
function studio_get_image_url( $image_field, string $size = 'full' ): string {
    if ( empty( $image_field ) ) {
        return '';
    }
    // Если ACF вернул массив — используем нужный размер
    if ( is_array( $image_field ) ) {
        return $image_field['sizes'][ $size ] ?? $image_field['url'] ?? '';
    }
    // Если это числовой ID вложения
    if ( is_numeric( $image_field ) ) {
        return wp_get_attachment_image_url( (int) $image_field, $size ) ?: '';
    }
    // Если это уже URL-строка
    return is_string( $image_field ) ? $image_field : '';
}

/**
 * Получает alt-текст изображения из ACF-поля.
 *
 * @param mixed  $image_field Значение ACF image-поля.
 * @param string $fallback    Запасной alt-текст.
 * @return string
 */
function studio_get_image_alt( $image_field, string $fallback = '' ): string {
    if ( is_array( $image_field ) ) {
        return $image_field['alt'] ?? $fallback;
    }
    if ( is_numeric( $image_field ) ) {
        return get_post_meta( (int) $image_field, '_wp_attachment_image_alt', true ) ?: $fallback;
    }
    return $fallback;
}

/**
 * Получает URL файла из ACF-поля типа file.
 *
 * @param mixed $file_field Значение ACF file-поля.
 * @return string URL файла или пустая строка.
 */
function studio_get_file_url( $file_field ): string {
    if ( empty( $file_field ) ) {
        return '';
    }
    if ( is_array( $file_field ) ) {
        return $file_field['url'] ?? '';
    }
    if ( is_numeric( $file_field ) ) {
        return wp_get_attachment_url( (int) $file_field ) ?: '';
    }
    return is_string( $file_field ) ? $file_field : '';
}


// ─── Callback-функции эндпоинтов ──────────────────────────────────────────────

/**
 * GET /settings
 * Возвращает глобальные настройки студии.
 * Структура соответствует интерфейсу SiteSettings из types/api.ts.
 */
function studio_rest_get_settings( WP_REST_Request $request ): WP_REST_Response {
    $lang = studio_get_lang( $request );

    $logo_field    = studio_get_option_field( 'logo', '', $lang );
    $favicon_field = studio_get_option_field( 'favicon', '', $lang );
    $og_image      = studio_get_option_field( 'og_image', '', $lang );

    $social_links_raw = studio_get_option_field( 'social_links', [], $lang );
    $social_links = [];
    if ( is_array( $social_links_raw ) ) {
        foreach ( $social_links_raw as $link ) {
            $social_links[] = [
                'platform' => $link['platform'] ?? '',
                'url'      => $link['url'] ?? '',
                'label'    => $link['label'] ?? '',
            ];
        }
    }

    $data = [
        'site_name'           => studio_get_option_field( 'site_name', get_bloginfo( 'name' ), $lang ),
        'site_description'    => studio_get_option_field( 'site_description', '', $lang ) ?: get_bloginfo( 'description' ),
        'logo_url'            => studio_get_image_url( $logo_field ),
        'favicon_url'         => studio_get_image_url( $favicon_field, 'thumbnail' ),
        'email'               => studio_get_option_field( 'email', get_option( 'admin_email' ), $lang ),
        'phone'               => studio_get_option_field( 'phone', '', $lang ),
        'address'             => studio_get_option_field( 'address', '', $lang ),
        'social_links'        => $social_links,
        'seo'                 => [
            'title'         => studio_get_option_field( 'seo_title', get_bloginfo( 'name' ), $lang ),
            'description'   => studio_get_option_field( 'seo_description', get_bloginfo( 'description' ), $lang ),
            'og_image_url'  => studio_get_image_url( $og_image ),
            'og_type'       => 'website',
            'twitter_card'  => studio_get_option_field( 'twitter_card', 'summary_large_image', $lang ),
        ],
        'map_coordinates'     => [
            'lat' => (float) studio_get_option_field( 'map_lat', 0, $lang ),
            'lng' => (float) studio_get_option_field( 'map_lng', 0, $lang ),
        ],
        'office_marker_label' => studio_get_option_field( 'office_marker_label', '', $lang ),
    ];

    return new WP_REST_Response( $data, 200 );
}


/**
 * GET /hero
 * Возвращает данные Hero-секции.
 * Структура соответствует интерфейсу HeroSection из types/api.ts.
 */
function studio_rest_get_hero( WP_REST_Request $request ): WP_REST_Response {
    $lang = studio_get_lang( $request );

    $background_type  = studio_get_option_field( 'background_type', 'gradient', $lang );
    $background_image = studio_get_option_field( 'background_image', '', $lang );
    $background_video = studio_get_option_field( 'background_video', '', $lang );

    $data = [
        'heading'               => studio_get_option_field( 'heading', '', $lang ),
        'subheading'            => studio_get_option_field( 'subheading', '', $lang ),
        'cta_text'              => studio_get_option_field( 'cta_text', '', $lang ),
        'cta_anchor'            => studio_get_option_field( 'cta_anchor', '#contact', $lang ),
        'background_type'       => $background_type,
        'background_gradient'   => studio_get_option_field( 'background_gradient', '', $lang ),
        'background_image_url'  => studio_get_image_url( $background_image ),
        'background_video_url'  => studio_get_file_url( $background_video ),
    ];

    return new WP_REST_Response( $data, 200 );
}


/**
 * GET /about
 * Возвращает данные секции «О нас» и список команды.
 * Структура соответствует интерфейсу AboutSection из types/api.ts.
 */
function studio_rest_get_about( WP_REST_Request $request ): WP_REST_Response {
    $lang = studio_get_lang( $request );

    $values_raw = studio_get_option_field( 'values', [], $lang );
    $values = [];
    if ( is_array( $values_raw ) ) {
        foreach ( $values_raw as $row ) {
            if ( ! empty( $row['value_item'] ) ) {
                $values[] = $row['value_item'];
            }
        }
    }

    $stats_raw = studio_get_option_field( 'stats', [], $lang );
    $stats = [];
    if ( is_array( $stats_raw ) ) {
        foreach ( $stats_raw as $row ) {
            $label = $row['label'] ?? '';
            if ( 'en' === $lang && ! empty( $row['label_en'] ) ) {
                $label = $row['label_en'];
            }
            $stats[] = [
                'label'  => $label,
                'value'  => (int) ( $row['value'] ?? 0 ),
                'suffix' => $row['suffix'] ?? '',
            ];
        }
    }

    $data = [
        'title'   => studio_get_option_field( 'about_title', '', $lang ),
        'history' => studio_get_option_field( 'history', '', $lang ),
        'mission' => studio_get_option_field( 'mission', '', $lang ),
        'values'  => $values,
        'stats'   => $stats,
    ];

    return new WP_REST_Response( $data, 200 );
}


/**
 * GET /services
 * Возвращает список услуг (только видимые, отсортированные по sort_order).
 * Структура соответствует интерфейсу Service[] из types/api.ts.
 */
function studio_rest_get_services( WP_REST_Request $request ): WP_REST_Response {
    $lang = studio_get_lang( $request );

    $query = new WP_Query( [
        'post_type'      => 'services',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'meta_query'     => [ [ 'key' => 'is_visible', 'value' => '1', 'compare' => '=' ] ],
        'meta_key'       => 'sort_order',
        'orderby'        => 'meta_value_num',
        'order'          => 'ASC',
        'no_found_rows'  => true,
    ] );

    $services = [];

    if ( $query->have_posts() ) {
        foreach ( $query->posts as $post ) {
            $icon_field = function_exists( 'get_field' ) ? get_field( 'icon', $post->ID ) : null;

            // Для EN сначала ищем title_en и description_en
            $title = $post->post_title;
            $description_uk = (string) get_post_meta( $post->ID, 'description', true );
            if ( ! $description_uk && function_exists( 'get_field' ) ) {
                $description_uk = (string) ( get_field( 'description', $post->ID ) ?? '' );
            }
            $description_en = (string) get_post_meta( $post->ID, 'description_en', true );
            $title_en = (string) get_post_meta( $post->ID, 'title_en', true );

            if ( 'en' === $lang ) {
                if ( $title_en !== '' ) $title = $title_en;
                $description = $description_en !== '' ? $description_en : $description_uk;
            } else {
                $description = $description_uk;
            }

            $services[] = [
                'id'          => $post->ID,
                'title'       => $title,
                'description' => $description,
                'icon_url'    => studio_get_image_url( $icon_field ),
                'icon_alt'    => studio_get_image_alt( $icon_field, $title ),
                'sort_order'  => (int) get_post_meta( $post->ID, 'sort_order', true ),
                'is_visible'  => true,
            ];
        }
    }

    wp_reset_postdata();
    return new WP_REST_Response( $services, 200 );
}


/**
 * GET /portfolio
 * Возвращает список проектов портфолио (только видимые, отсортированные по sort_order).
 * Структура соответствует интерфейсу PortfolioProject[] из types/api.ts.
 */
function studio_rest_get_portfolio( WP_REST_Request $request ): WP_REST_Response {
    $lang = studio_get_lang( $request );

    $query = new WP_Query( [
        'post_type'      => 'portfolio',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'meta_query'     => [
            [
                'key'     => 'is_visible',
                'value'   => '1',
                'compare' => '=',
            ],
        ],
        'meta_key'  => 'sort_order',
        'orderby'   => 'meta_value_num',
        'order'     => 'ASC',
        'no_found_rows' => true,
    ] );

    $portfolio = [];

    if ( $query->have_posts() ) {
        foreach ( $query->posts as $post ) {
            // Получаем миниатюру как основной скриншот
            $screenshot_url = get_the_post_thumbnail_url( $post->ID, 'large' ) ?: '';
            $screenshot_alt = get_post_meta(
                get_post_thumbnail_id( $post->ID ),
                '_wp_attachment_image_alt',
                true
            ) ?: $post->post_title;

            // Галерея проекта
            $gallery_raw = function_exists( 'get_field' )
                ? ( get_field( 'gallery', $post->ID ) ?: [] )
                : [];
            $gallery = [];
            if ( is_array( $gallery_raw ) ) {
                foreach ( $gallery_raw as $img ) {
                    $gallery[] = [
                        'url' => is_array( $img ) ? ( $img['url'] ?? '' ) : wp_get_attachment_url( (int) $img ),
                        'alt' => is_array( $img ) ? ( $img['alt'] ?? '' ) : '',
                    ];
                }
            }

            // Технологии проекта из репитера
            $tech_raw = function_exists( 'get_field' )
                ? ( get_field( 'technologies', $post->ID ) ?: [] )
                : [];
            $technologies = [];
            if ( is_array( $tech_raw ) ) {
                foreach ( $tech_raw as $row ) {
                    if ( ! empty( $row['tech_name'] ) ) {
                        $technologies[] = $row['tech_name'];
                    }
                }
            }

            $short_desc_uk = function_exists( 'get_field' ) ? (string) ( get_field( 'short_description', $post->ID ) ?? '' ) : (string) get_post_meta( $post->ID, 'short_description', true );
            $short_desc_en = (string) get_post_meta( $post->ID, 'short_description_en', true );
            $full_desc_uk  = function_exists( 'get_field' ) ? (string) ( get_field( 'full_description', $post->ID ) ?? '' ) : (string) get_post_meta( $post->ID, 'full_description', true );
            $full_desc_en  = (string) get_post_meta( $post->ID, 'full_description_en', true );
            $title_en      = (string) get_post_meta( $post->ID, 'title_en', true );

            $portfolio[] = [
                'id'                => $post->ID,
                'slug'              => $post->post_name,
                'title'             => ( 'en' === $lang && $title_en !== '' ) ? $title_en : $post->post_title,
                'short_description' => ( 'en' === $lang && $short_desc_en !== '' ) ? $short_desc_en : $short_desc_uk,
                'full_description'  => ( 'en' === $lang && $full_desc_en !== '' ) ? $full_desc_en : $full_desc_uk,
                'screenshot_url'    => $screenshot_url,
                'screenshot_alt'    => $screenshot_alt,
                'gallery'           => $gallery,
                'technologies'      => $technologies,
                'project_url'       => function_exists( 'get_field' )
                    ? (string) ( get_field( 'project_url', $post->ID ) ?? '' )
                    : (string) get_post_meta( $post->ID, 'project_url', true ),
                'sort_order'        => (int) get_post_meta( $post->ID, 'sort_order', true ),
                'is_visible'        => true,
                'published_at'      => get_the_date( 'c', $post->ID ), // ISO 8601
            ];
        }
    }

    wp_reset_postdata();

    return new WP_REST_Response( $portfolio, 200 );
}


/**
 * GET /technologies
 * Возвращает список технологий, сгруппированных по категориям.
 * Структура соответствует интерфейсу TechnologiesSection из types/api.ts.
 */
function studio_rest_get_technologies( WP_REST_Request $request ): WP_REST_Response {

    $query = new WP_Query( [
        'post_type'      => 'technologies',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'meta_query'     => [
            [
                'key'     => 'is_visible',
                'value'   => '1',
                'compare' => '=',
            ],
        ],
        'meta_key'  => 'sort_order',
        'orderby'   => 'meta_value_num',
        'order'     => 'ASC',
        'no_found_rows' => true,
    ] );

    $items      = [];
    $categories = [];

    if ( $query->have_posts() ) {
        foreach ( $query->posts as $post ) {
            $logo_field = function_exists( 'get_field' )
                ? get_field( 'logo', $post->ID )
                : null;

            $category = function_exists( 'get_field' )
                ? (string) ( get_field( 'category', $post->ID ) ?? 'Frontend' )
                : (string) get_post_meta( $post->ID, 'category', true );

            $item = [
                'id'         => $post->ID,
                'name'       => $post->post_title,
                'logo_url'   => studio_get_image_url( $logo_field, 'thumbnail' ),
                'logo_alt'   => studio_get_image_alt( $logo_field, $post->post_title ),
                'category'   => $category,
                'sort_order' => (int) get_post_meta( $post->ID, 'sort_order', true ),
                'is_visible' => true,
            ];

            $items[] = $item;

            // Собираем уникальный список категорий (порядок сохраняется)
            if ( ! in_array( $category, $categories, true ) ) {
                $categories[] = $category;
            }
        }
    }

    wp_reset_postdata();

    return new WP_REST_Response( [
        'categories' => $categories,
        'items'      => $items,
    ], 200 );
}


/**
 * GET /team
 * Возвращает список сотрудников, отсортированных по sort_order.
 * Структура соответствует интерфейсу TeamMember[] из types/api.ts.
 */
function studio_rest_get_team( WP_REST_Request $request ): WP_REST_Response {
    $lang = studio_get_lang( $request );

    $query = new WP_Query( [
        'post_type'      => 'team',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'meta_key'       => 'sort_order',
        'orderby'        => 'meta_value_num',
        'order'          => 'ASC',
        'no_found_rows'  => true,
    ] );

    $team = [];

    if ( $query->have_posts() ) {
        foreach ( $query->posts as $post ) {
            // Фото сотрудника — миниатюра записи
            $photo_id  = get_post_thumbnail_id( $post->ID );
            $photo_url = get_the_post_thumbnail_url( $post->ID, 'medium' ) ?: '';
            $photo_alt = $photo_id
                ? ( get_post_meta( $photo_id, '_wp_attachment_image_alt', true ) ?: $post->post_title )
                : $post->post_title;

            $position_uk = (string) get_post_meta( $post->ID, 'position', true );
            if ( ! $position_uk && function_exists( 'get_field' ) ) {
                $position_uk = (string) ( get_field( 'position', $post->ID ) ?? '' );
            }
            $position_en = (string) get_post_meta( $post->ID, 'position_en', true );
            $name_en     = (string) get_post_meta( $post->ID, 'name_en', true );

            $team[] = [
                'id'         => $post->ID,
                'name'       => ( 'en' === $lang && $name_en !== '' ) ? $name_en : $post->post_title,
                'position'   => ( 'en' === $lang && $position_en !== '' ) ? $position_en : $position_uk,
                'photo_url'  => $photo_url,
                'photo_alt'  => $photo_alt,
                'sort_order' => (int) get_post_meta( $post->ID, 'sort_order', true ),
            ];
        }
    }

    wp_reset_postdata();

    return new WP_REST_Response( $team, 200 );
}


/**
 * GET /geography
 * Возвращает данные географии клиентов.
 * Структура соответствует интерфейсу GeographySection из types/api.ts.
 */
function studio_rest_get_geography( WP_REST_Request $request ): WP_REST_Response {
    $lang = studio_get_lang( $request );

    $query = new WP_Query( [
        'post_type'      => 'geography',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'no_found_rows'  => true,
    ] );

    $countries = [];

    if ( $query->have_posts() ) {
        foreach ( $query->posts as $post ) {
            $countries[] = [
                'id'             => $post->ID,
                'country_code'   => function_exists( 'get_field' )
                    ? (string) ( get_field( 'country_code', $post->ID ) ?? '' )
                    : (string) get_post_meta( $post->ID, 'country_code', true ),
                'country_name'   => ( 'en' === $lang && (string) get_post_meta( $post->ID, 'country_name_en', true ) !== '' )
                    ? (string) get_post_meta( $post->ID, 'country_name_en', true )
                    : $post->post_title,
                'projects_count' => (int) ( function_exists( 'get_field' )
                    ? ( get_field( 'projects_count', $post->ID ) ?? 0 )
                    : get_post_meta( $post->ID, 'projects_count', true ) ),
                'clients_count'  => (int) ( function_exists( 'get_field' )
                    ? ( get_field( 'clients_count', $post->ID ) ?? 0 )
                    : get_post_meta( $post->ID, 'clients_count', true ) ),
                'tooltip_text'   => ( 'en' === $lang && (string) get_post_meta( $post->ID, 'tooltip_text_en', true ) !== '' )
                    ? (string) get_post_meta( $post->ID, 'tooltip_text_en', true )
                    : ( function_exists( 'get_field' ) ? (string) ( get_field( 'tooltip_text', $post->ID ) ?? '' ) : (string) get_post_meta( $post->ID, 'tooltip_text', true ) ),
            ];
        }
    }

    wp_reset_postdata();

    $data = [
        'title'       => 'uk' === $lang
            ? __( 'Географія клієнтів', 'studio-headless' )
            : __( 'Client Geography', 'studio-headless' ),
        'description' => 'uk' === $lang
            ? __( 'Ми працюємо з клієнтами по всьому світу', 'studio-headless' )
            : __( 'We work with clients worldwide', 'studio-headless' ),
        'countries'   => $countries,
    ];

    return new WP_REST_Response( $data, 200 );
}


/**
 * GET /labels
 * Возвращает тексты UI-элементов и заголовков секций.
 */
function studio_rest_get_labels( WP_REST_Request $request ): WP_REST_Response {
    $lang = studio_get_lang( $request );

    $fields = [
        'services_title', 'services_section_desc', 'services_cta',
        'technologies_title', 'technologies_subtitle',
        'portfolio_title',
        'stats_cta_text', 'stats_cta_btn',
        'contact_title',
        'hero_secondary_cta',
        'about_team_title', 'about_cta',
    ];

    $data = [];
    foreach ( $fields as $field ) {
        $data[ $field ] = studio_get_option_field( $field, '', $lang );
    }

    return new WP_REST_Response( $data, 200 );
}
