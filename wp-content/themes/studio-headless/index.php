<?php
/**
 * Минимальный шаблон темы.
 * Перенаправляет все фронтенд-запросы на Next.js-приложение.
 *
 * @package StudioHeadless
 */

// Перенаправление на Next.js, если задана константа NEXTJS_FRONTEND_URL
if ( defined( 'NEXTJS_FRONTEND_URL' ) && NEXTJS_FRONTEND_URL ) {
    wp_redirect( NEXTJS_FRONTEND_URL, 301 );
    exit;
}

// Если константа не задана — выводим минимальную заглушку
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<p style="font-family: sans-serif; padding: 2rem;">
    <?php esc_html_e( 'Headless WordPress тема активна. Укажите NEXTJS_FRONTEND_URL в wp-config.php для перенаправления на фронтенд.', 'studio-headless' ); ?>
</p>
<?php wp_footer(); ?>
</body>
</html>
