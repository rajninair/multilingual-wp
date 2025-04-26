<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ){
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );

if ( !function_exists( 'chld_thm_cfg_parent_css' ) ):
    function chld_thm_cfg_parent_css() {
        wp_enqueue_style( 'chld_thm_cfg_parent', trailingslashit( get_template_directory_uri() ) . 'style.css', array( 'best-shop-bootstrap' ) );
    }
endif;
add_action( 'wp_enqueue_scripts', 'chld_thm_cfg_parent_css', 10 );
         
if ( !function_exists( 'child_theme_configurator_css' ) ):
    function child_theme_configurator_css() {
        wp_enqueue_style( 'chld_thm_cfg_separate', trailingslashit( get_stylesheet_directory_uri() ) . 'ctc-style.css', array( 'chld_thm_cfg_parent','best-shop-style' ) );
    }
endif;
add_action( 'wp_enqueue_scripts', 'child_theme_configurator_css', 10 );

// END ENQUEUE PARENT ACTION

// Register Navigation Menus
function best_shop_child_register_menus() {
    register_nav_menus(array(
        'primary-menu' => esc_html__('Primary Menu', 'best-shop-child'),
    ));
}
add_action('after_setup_theme', 'best_shop_child_register_menus');

// Add theme support for necessary features
function best_shop_child_theme_support() {
    add_theme_support('menus');
    add_theme_support('custom-logo');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array('navigation-widgets'));
}
add_action('after_setup_theme', 'best_shop_child_theme_support');

// Enqueue custom scripts and styles
function best_shop_child_enqueue_scripts() {
    // Enqueue navigation script
    wp_enqueue_script(
        'best-shop-child-navigation',
        get_stylesheet_directory_uri() . '/js/navigation.js',
        array('jquery'),
        filemtime(get_stylesheet_directory() . '/js/navigation.js'),
        true
    );

    // Enqueue child theme styles
    wp_enqueue_style(
        'best-shop-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array('best-shop-style'),
        filemtime(get_stylesheet_directory() . '/style.css')
    );
}
add_action('wp_enqueue_scripts', 'best_shop_child_enqueue_scripts', 20);

// Enqueue video background script
function best_shop_child_video_background_script() {
    wp_enqueue_script(
        'best-shop-child-video-background',
        get_stylesheet_directory_uri() . '/js/video-background.js',
        array('jquery'),
        filemtime(get_stylesheet_directory() . '/js/video-background.js'),
        true
    );
}
add_action('wp_enqueue_scripts', 'best_shop_child_video_background_script', 20);

// Add Customizer Settings for Video Background
function best_shop_child_customizer_settings($wp_customize) {
    // Add a new section for video background
    $wp_customize->add_section('video_background_section', array(
        'title'    => __('Video Background', 'best-shop-child'),
        'priority' => 30,
    ));

    // Add setting for video URL
    $wp_customize->add_setting('video_background_url', array(
        'default'           => '',
        'transport'         => 'refresh',
        'sanitize_callback' => 'absint'  // Changed to absint since we're storing an attachment ID
    ));

    // Add control for video upload
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'video_background_url', array(
        'label'    => __('Upload Video Background', 'best-shop-child'),
        'section'  => 'video_background_section',
        'settings' => 'video_background_url',
        'mime_type' => 'video',
        'button_labels' => array(
            'select'       => __('Select Video', 'best-shop-child'),
            'change'       => __('Change Video', 'best-shop-child'),
            'remove'       => __('Remove', 'best-shop-child'),
            'default'      => __('Default', 'best-shop-child'),
            'placeholder'  => __('No video selected', 'best-shop-child'),
            'frame_title'  => __('Select Video', 'best-shop-child'),
            'frame_button' => __('Choose Video', 'best-shop-child'),
        ),
    )));

    // Add setting for mobile video display
    $wp_customize->add_setting('show_video_on_mobile', array(
        'default'           => false,
        'transport'         => 'refresh',
        'sanitize_callback' => 'best_shop_child_sanitize_checkbox',
    ));

    // Add control for mobile video display
    $wp_customize->add_control('show_video_on_mobile', array(
        'label'    => __('Show video on mobile devices', 'best-shop-child'),
        'section'  => 'video_background_section',
        'type'     => 'checkbox',
    ));

    // Add Hero Section Settings
    $wp_customize->add_section('hero_section', array(
        'title'    => __('Hero Section', 'best-shop-child'),
        'priority' => 31,
    ));

    // Hero Title
    $wp_customize->add_setting('hero_title', array(
        'default'           => __('Welcome to Our Store', 'best-shop-child'),
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('hero_title', array(
        'label'    => __('Hero Title', 'best-shop-child'),
        'section'  => 'hero_section',
        'type'     => 'text',
    ));

    // Hero Subtitle
    $wp_customize->add_setting('hero_subtitle', array(
        'default'           => __('Discover our premium products', 'best-shop-child'),
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('hero_subtitle', array(
        'label'    => __('Hero Subtitle', 'best-shop-child'),
        'section'  => 'hero_section',
        'type'     => 'text',
    ));

    // Hero Button Text
    $wp_customize->add_setting('hero_button_text', array(
        'default'           => __('Shop Now', 'best-shop-child'),
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('hero_button_text', array(
        'label'    => __('Button Text', 'best-shop-child'),
        'section'  => 'hero_section',
        'type'     => 'text',
    ));
}
add_action('customize_register', 'best_shop_child_customizer_settings');

// Sanitize checkbox
function best_shop_child_sanitize_checkbox($checked) {
    return ((isset($checked) && true == $checked) ? true : false);
}
