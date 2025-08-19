<?php
/**
 * Enqueue parent and child theme styles.
 */
function twentytwentyfive_child_enqueue_styles() {
    $parent_style = 'twentytwentyfive-style';

    // Parent style.
    wp_enqueue_style(
        $parent_style,
        get_template_directory_uri() . '/style.css',
        array(),
        wp_get_theme( get_template() )->get( 'Version' )
    );

    // Child style.
    wp_enqueue_style(
        'twentytwentyfive-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get( 'Version' )
    );
}
add_action( 'wp_enqueue_scripts', 'twentytwentyfive_child_enqueue_styles' );


/**
 * Register the 'car' custom post type and 'brand' taxonomy.
 * Kept for backward compatibility. Prefer the plugin 'Car Sell Shop Core'.
 */
function car_rent_register_content_types() {
    // Register Car post type.
    $car_labels = array(
        'name'                  => __( 'Cars', 'twentytwentyfive-child' ),
        'singular_name'         => __( 'Car', 'twentytwentyfive-child' ),
        'add_new'               => __( 'Add New', 'twentytwentyfive-child' ),
        'add_new_item'          => __( 'Add New Car', 'twentytwentyfive-child' ),
        'edit_item'             => __( 'Edit Car', 'twentytwentyfive-child' ),
        'new_item'              => __( 'New Car', 'twentytwentyfive-child' ),
        'view_item'             => __( 'View Car', 'twentytwentyfive-child' ),
        'view_items'            => __( 'View Cars', 'twentytwentyfive-child' ),
        'search_items'          => __( 'Search Cars', 'twentytwentyfive-child' ),
        'not_found'             => __( 'No cars found', 'twentytwentyfive-child' ),
        'not_found_in_trash'    => __( 'No cars found in Trash', 'twentytwentyfive-child' ),
        'all_items'             => __( 'All Cars', 'twentytwentyfive-child' ),
        'archives'              => __( 'Car Archives', 'twentytwentyfive-child' ),
        'attributes'            => __( 'Car Attributes', 'twentytwentyfive-child' ),
        'menu_name'             => __( 'Cars', 'twentytwentyfive-child' ),
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
        'name'              => __( 'Brands', 'twentytwentyfive-child' ),
        'singular_name'     => __( 'Brand', 'twentytwentyfive-child' ),
        'search_items'      => __( 'Search Brands', 'twentytwentyfive-child' ),
        'all_items'         => __( 'All Brands', 'twentytwentyfive-child' ),
        'parent_item'       => __( 'Parent Brand', 'twentytwentyfive-child' ),
        'parent_item_colon' => __( 'Parent Brand:', 'twentytwentyfive-child' ),
        'edit_item'         => __( 'Edit Brand', 'twentytwentyfive-child' ),
        'update_item'       => __( 'Update Brand', 'twentytwentyfive-child' ),
        'add_new_item'      => __( 'Add New Brand', 'twentytwentyfive-child' ),
        'new_item_name'     => __( 'New Brand Name', 'twentytwentyfive-child' ),
        'menu_name'         => __( 'Brands', 'twentytwentyfive-child' ),
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
}
// Avoid duplicate registration if the plugin is active.
if ( ! defined( 'CAR_SELL_SHOP_CORE_LOADED' ) ) {
    add_action( 'init', 'car_rent_register_content_types' );
}

/**
 * Flush rewrite rules on theme switch to register CPT/tax permalinks.
 */
function car_rent_flush_rewrite_on_switch() {
    if ( ! defined( 'CAR_SELL_SHOP_CORE_LOADED' ) ) {
        car_rent_register_content_types();
    }
    flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'car_rent_flush_rewrite_on_switch' );

