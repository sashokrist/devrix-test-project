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


