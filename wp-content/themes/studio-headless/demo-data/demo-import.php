<?php
/**
 * Скрипт импорта демо-данных для темы Studio Headless.
 * Запуск: wp eval-file demo-data/demo-import.php
 *
 * @package StudioHeadless
 */

defined( 'ABSPATH' ) || exit;

echo "🚀 Начинаем импорт демо-данных...\n";

// Читаем JSON с демо-данными
$json_file = __DIR__ . '/demo-content.json';
if ( ! file_exists( $json_file ) ) {
    WP_CLI::error( 'Файл demo-content.json не найден.' );
    exit;
}

$data = json_decode( file_get_contents( $json_file ), true );
if ( ! $data ) {
    WP_CLI::error( 'Ошибка чтения demo-content.json.' );
    exit;
}

// ─── Импорт глобальных настроек (ACF Options) ────────────────────────────────
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
    echo "✅ Настройки студии импортированы\n";
}

// ─── Импорт Hero-секции ───────────────────────────────────────────────────────
if ( function_exists( 'update_field' ) && isset( $data['hero'] ) ) {
    $h = $data['hero'];
    update_field( 'heading', $h['heading'], 'option' );
    update_field( 'subheading', $h['subheading'], 'option' );
    update_field( 'cta_text', $h['cta_text'], 'option' );
    update_field( 'cta_anchor', $h['cta_anchor'], 'option' );
    update_field( 'background_type', $h['background_type'], 'option' );
    update_field( 'background_gradient', $h['background_gradient'], 'option' );
    // EN поля
    if ( ! empty( $h['heading_en'] ) ) update_field( 'heading_en', $h['heading_en'], 'option' );
    if ( ! empty( $h['subheading_en'] ) ) update_field( 'subheading_en', $h['subheading_en'], 'option' );
    if ( ! empty( $h['cta_text_en'] ) ) update_field( 'cta_text_en', $h['cta_text_en'], 'option' );
    echo "✅ Hero-секция импортирована\n";
}

// ─── Импорт секции «О нас» ────────────────────────────────────────────────────
if ( function_exists( 'update_field' ) && isset( $data['about'] ) ) {
    $a = $data['about'];
    update_field( 'about_title', $a['about_title'], 'option' );
    update_field( 'history', $a['history'], 'option' );
    update_field( 'mission', $a['mission'], 'option' );

    $values_data = array_map( fn( $v ) => [ 'value_item' => $v ], $a['values'] );
    update_field( 'values', $values_data, 'option' );
    update_field( 'stats', $a['stats'], 'option' );

    // EN поля
    if ( ! empty( $a['about_title_en'] ) ) update_field( 'about_title_en', $a['about_title_en'], 'option' );
    if ( ! empty( $a['history_en'] ) ) update_field( 'history_en', $a['history_en'], 'option' );
    if ( ! empty( $a['mission_en'] ) ) update_field( 'mission_en', $a['mission_en'], 'option' );
    if ( ! empty( $a['values_en'] ) ) {
        $values_en_data = array_map( fn( $v ) => [ 'value_item' => $v ], $a['values_en'] );
        update_field( 'values_en', $values_en_data, 'option' );
    }

    echo "✅ Секция «О нас» импортирована\n";
}

// ─── Универсальная функция создания CPT-записи ────────────────────────────────

/**
 * Создаёт или пропускает CPT-запись по slug (идемпотентность).
 */
function studio_create_post( string $post_type, string $title, string $slug, array $meta = [] ): int {
    // Проверяем существование записи
    $existing = get_page_by_path( $slug, OBJECT, $post_type );
    if ( $existing ) {
        echo "  ⏭ Запись '{$title}' уже существует, пропускаем\n";
        return $existing->ID;
    }

    $post_id = wp_insert_post( [
        'post_type'   => $post_type,
        'post_title'  => $title,
        'post_name'   => $slug,
        'post_status' => 'publish',
        'meta_input'  => $meta,
    ] );

    if ( is_wp_error( $post_id ) ) {
        echo "  ❌ Ошибка создания '{$title}': " . $post_id->get_error_message() . "\n";
        return 0;
    }

    return $post_id;
}

// ─── Импорт услуг ─────────────────────────────────────────────────────────────
if ( isset( $data['services'] ) ) {
    echo "📦 Импортируем услуги...\n";
    foreach ( $data['services'] as $i => $service ) {
        $slug = sanitize_title( $service['title'] );
        $post_id = studio_create_post( 'services', $service['title'], $slug );
        if ( $post_id && function_exists( 'update_field' ) ) {
            update_field( 'description', $service['description'], $post_id );
            update_field( 'sort_order', $service['sort_order'], $post_id );
            update_field( 'is_visible', $service['is_visible'] ? 1 : 0, $post_id );
            if ( ! empty( $service['title_en'] ) ) update_field( 'title_en', $service['title_en'], $post_id );
            if ( ! empty( $service['description_en'] ) ) update_field( 'description_en', $service['description_en'], $post_id );
        }
        echo "  ✅ Услуга: {$service['title']}\n";
    }
}

// ─── Импорт технологий ────────────────────────────────────────────────────────
if ( isset( $data['technologies'] ) ) {
    echo "📦 Импортируем технологии...\n";
    foreach ( $data['technologies'] as $tech ) {
        $slug = sanitize_title( $tech['name'] );
        $post_id = studio_create_post( 'technologies', $tech['name'], $slug );
        if ( $post_id && function_exists( 'update_field' ) ) {
            update_field( 'category', $tech['category'], $post_id );
            update_field( 'sort_order', $tech['sort_order'], $post_id );
            update_field( 'is_visible', $tech['is_visible'] ? 1 : 0, $post_id );
        }
        echo "  ✅ Технология: {$tech['name']}\n";
    }
}

// ─── Импорт команды ───────────────────────────────────────────────────────────
if ( isset( $data['team'] ) ) {
    echo "📦 Импортируем команду...\n";
    foreach ( $data['team'] as $member ) {
        $slug = sanitize_title( $member['name'] );
        $post_id = studio_create_post( 'team', $member['name'], $slug );
        if ( $post_id && function_exists( 'update_field' ) ) {
            update_field( 'position', $member['position'], $post_id );
            update_field( 'sort_order', $member['sort_order'], $post_id );
        }
        echo "  ✅ Сотрудник: {$member['name']}\n";
    }
}

// ─── Импорт портфолио ─────────────────────────────────────────────────────────
if ( isset( $data['portfolio'] ) ) {
    echo "📦 Импортируем портфолио...\n";
    foreach ( $data['portfolio'] as $project ) {
        $post_id = studio_create_post( 'portfolio', $project['title'], $project['slug'] );
        if ( $post_id && function_exists( 'update_field' ) ) {
            update_field( 'short_description', $project['short_description'], $post_id );
            update_field( 'full_description', $project['full_description'], $post_id );
            update_field( 'project_url', $project['project_url'] ?? '', $post_id );
            update_field( 'sort_order', $project['sort_order'], $post_id );
            update_field( 'is_visible', 1, $post_id );
            $tech_rows = array_map( fn( $t ) => [ 'tech_name' => $t ], $project['technologies'] );
            update_field( 'technologies', $tech_rows, $post_id );
            if ( ! empty( $project['short_description_en'] ) ) update_field( 'short_description_en', $project['short_description_en'], $post_id );
            if ( ! empty( $project['full_description_en'] ) ) update_field( 'full_description_en', $project['full_description_en'], $post_id );
        }
        echo "  ✅ Проект: {$project['title']}\n";
    }
}

// ─── Импорт географии ─────────────────────────────────────────────────────────
if ( isset( $data['geography'] ) ) {
    echo "📦 Импортируем географию...\n";
    foreach ( $data['geography'] as $country ) {
        $slug = strtolower( $country['country_code'] );
        $post_id = studio_create_post( 'geography', $country['country_name'], $slug );
        if ( $post_id && function_exists( 'update_field' ) ) {
            update_field( 'country_code', $country['country_code'], $post_id );
            update_field( 'projects_count', $country['projects_count'], $post_id );
            update_field( 'clients_count', $country['clients_count'], $post_id );
            update_field( 'tooltip_text', $country['tooltip_text'], $post_id );
        }
        echo "  ✅ Страна: {$country['country_name']}\n";
    }
}

echo "\n✨ Импорт демо-данных завершён!\n";
echo "   Теперь можно запустить Next.js-фронтенд и проверить результат.\n";
