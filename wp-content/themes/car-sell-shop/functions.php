<?php
/**
 * Car Sell Shop Theme Functions
 * 
 * Minimal functions file to prevent output issues
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
}
add_action( 'after_setup_theme', 'car_sell_shop_theme_setup' );
