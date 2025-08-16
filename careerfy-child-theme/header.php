<?php
/**
 * Header template for Careerfy Child theme.
 * Provides modern responsive navigation.
 *
 * @package Careerfy Child
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="cfy-site-header">
    <div class="container">
        <a class="logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
            <?php bloginfo( 'name' ); ?>
        </a>
        <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">&#9776;</button>
        <?php
        wp_nav_menu(
            array(
                'theme_location' => 'primary',
                'menu_id'        => 'primary-menu',
                'container'      => 'nav',
            )
        );
        ?>
    </div>
</header>
