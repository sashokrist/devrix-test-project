<?php
/**
 * Plugin Name: Car Sell Shop Core
 * Description: Core functionality for the Car Sell Shop theme: Car CPT and Brand taxonomy.
 * Version: 1.0.0
 * Author: Your Name
 * Text Domain: car-sell-shop-core
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'CAR_SELL_SHOP_CORE_LOADED', true );
define( 'CAR_SELL_SHOP_CORE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CAR_SELL_SHOP_CORE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Load template functionality
require_once CAR_SELL_SHOP_CORE_PLUGIN_DIR . 'includes/class-car-templates.php';

add_action( 'init', function () {
    // Register Car post type.
    $car_labels = array(
        'name'                  => __( 'Cars', 'car-sell-shop-core' ),
        'singular_name'         => __( 'Car', 'car-sell-shop-core' ),
        'add_new'               => __( 'Add New', 'car-sell-shop-core' ),
        'add_new_item'          => __( 'Add New Car', 'car-sell-shop-core' ),
        'edit_item'             => __( 'Edit Car', 'car-sell-shop-core' ),
        'new_item'              => __( 'New Car', 'car-sell-shop-core' ),
        'view_item'             => __( 'View Car', 'car-sell-shop-core' ),
        'view_items'            => __( 'View Cars', 'car-sell-shop-core' ),
        'search_items'          => __( 'Search Cars', 'car-sell-shop-core' ),
        'not_found'             => __( 'No cars found', 'car-sell-shop-core' ),
        'not_found_in_trash'    => __( 'No cars found in Trash', 'car-sell-shop-core' ),
        'all_items'             => __( 'All Cars', 'car-sell-shop-core' ),
        'archives'              => __( 'Car Archives', 'car-sell-shop-core' ),
        'attributes'            => __( 'Car Attributes', 'car-sell-shop-core' ),
        'menu_name'             => __( 'Cars', 'car-sell-shop-core' ),
    );

    $car_args = array(
        'labels'             => $car_labels,
        'public'             => true,
        'show_in_rest'       => true,
        'has_archive'        => true,
        'rewrite'            => array( 'slug' => 'cars' ),
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-car',
        'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
        'taxonomies'         => array( 'brand' ),
    );

    register_post_type( 'car', $car_args );

    // Register Brand taxonomy.
    $brand_labels = array(
        'name'              => __( 'Brands', 'car-sell-shop-core' ),
        'singular_name'     => __( 'Brand', 'car-sell-shop-core' ),
        'search_items'      => __( 'Search Brands', 'car-sell-shop-core' ),
        'all_items'         => __( 'All Brands', 'car-sell-shop-core' ),
        'parent_item'       => __( 'Parent Brand', 'car-sell-shop-core' ),
        'parent_item_colon' => __( 'Parent Brand:', 'car-sell-shop-core' ),
        'edit_item'         => __( 'Edit Brand', 'car-sell-shop-core' ),
        'update_item'       => __( 'Update Brand', 'car-sell-shop-core' ),
        'add_new_item'      => __( 'Add New Brand', 'car-sell-shop-core' ),
        'new_item_name'     => __( 'New Brand Name', 'car-sell-shop-core' ),
        'menu_name'         => __( 'Brands', 'car-sell-shop-core' ),
    );

    $brand_args = array(
        'hierarchical'      => true,
        'labels'            => $brand_labels,
        'show_ui'           => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'brand' ),
    );

    register_taxonomy( 'brand', array( 'car' ), $brand_args );
} );

// Initialize template loading
new Car_Sell_Shop_Core_Templates();

// Enqueue car styles
add_action( 'wp_enqueue_scripts', function() {
    global $post;
    
    // Check if we're on car-related pages or the brands page
    if ( is_post_type_archive( 'car' ) || 
         is_singular( 'car' ) || 
         is_tax( 'brand' ) ||
         ( $post && is_page() && ( $post->post_name === 'brands' || strpos( strtolower( $post->post_title ), 'brand' ) !== false ) ) ) {
        
        wp_enqueue_style( 
            'car-sell-shop-core-styles', 
            CAR_SELL_SHOP_CORE_PLUGIN_URL . 'assets/css/car-styles.css', 
            array(), 
            '1.0.0' 
        );
    }
});

/**
 * Custom hooks for Car Sell Shop.
 *
 * Hooks provided:
 * - Action:  car_sell_shop/car_saved( int $post_id, WP_Post $post, bool $is_update )
 * - Filter:  car_sell_shop/car_archive_orderby( string $orderby ) → default 'date'
 * - Filter:  car_sell_shop/car_archive_order( string $order ) → default 'DESC'
 * - Filter:  car_sell_shop/car_title( string $title, int $post_id )
 * - Filter:  car_sell_shop/after_car_content_html( string $html ) → append to single car content
 */

// Fire a custom action whenever a Car is saved (create/update from admin, REST, or CLI).
add_action( 'save_post_car', function ( $post_id, $post, $update ) {
    /**
     * @fires car_sell_shop/car_saved
     */
    do_action( 'car_sell_shop/car_saved', $post_id, $post, (bool) $update );
}, 10, 3 );

// Let integrators control Cars archive sorting via filters.
add_action( 'pre_get_posts', function ( WP_Query $query ) {
    if ( is_admin() || ! $query->is_main_query() ) {
        return;
    }

    if ( $query->is_post_type_archive( 'car' ) ) {
        $orderby = apply_filters( 'car_sell_shop/car_archive_orderby', 'date' );
        $order   = apply_filters( 'car_sell_shop/car_archive_order', 'DESC' );
        $query->set( 'orderby', $orderby );
        $query->set( 'order', $order );
    }
} );

// Allow filtering of Car titles only for the 'car' post type.
add_filter( 'the_title', function ( $title, $post_id ) {
    $post = get_post( $post_id );
    if ( $post && $post->post_type === 'car' ) {
        return apply_filters( 'car_sell_shop/car_title', $title, $post_id );
    }
    return $title;
}, 10, 2 );

// Append custom HTML after the content on single Car pages.
add_filter( 'the_content', function ( $content ) {
    if ( is_singular( 'car' ) && is_main_query() ) {
        $extra = apply_filters( 'car_sell_shop/after_car_content_html', '' );
        if ( ! empty( $extra ) ) {
            $content .= $extra;
        }
    }
    return $content;
}, 10 );

register_activation_hook( __FILE__, function () {
    // Ensure rewrites for CPT/tax are registered and flushed.
    do_action( 'init' );
    flush_rewrite_rules();
} );

register_deactivation_hook( __FILE__, function () {
    flush_rewrite_rules();
} );


