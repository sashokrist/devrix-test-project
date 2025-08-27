<?php
/**
 * Car Sell Shop Theme Functions
 * 
 * Clean functions file without problematic output
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Enqueue theme styles and scripts
 */
function car_sell_shop_enqueue_scripts() {
    // Enqueue your theme styles and scripts here
    wp_enqueue_style( 'car-sell-shop-style', get_stylesheet_uri() );
}
add_action( 'wp_enqueue_scripts', 'car_sell_shop_enqueue_scripts' );

/**
 * Add theme support
 */
function car_sell_shop_theme_setup() {
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'custom-logo' );
    add_theme_support( 'html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ) );
    
    // Add support for block theme
    add_theme_support( 'wp-block-styles' );
    add_theme_support( 'align-wide' );
}
add_action( 'after_setup_theme', 'car_sell_shop_theme_setup' );

/**
 * Add custom post type support for students
 */
function car_sell_shop_add_student_support() {
    add_theme_support( 'post-thumbnails', array( 'student' ) );
}
add_action( 'init', 'car_sell_shop_add_student_support' );

/**
 * Ensure proper navigation functionality
 */
function car_sell_shop_navigation_setup() {
    // Register navigation menus if needed
    register_nav_menus( array(
        'primary' => __( 'Primary Menu', 'car-sell-shop' ),
    ) );
}
add_action( 'after_setup_theme', 'car_sell_shop_navigation_setup' );
