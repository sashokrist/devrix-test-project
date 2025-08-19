<?php
// Examples: how to use the custom hooks from Car Sell Shop Core.

// 1) Listen when a Car is saved (create/update) and log it.
add_action( 'car_sell_shop/car_saved', function ( $post_id, $post, $is_update ) {
    error_log( sprintf( '[Car Sell Shop] Car %d saved (update: %s).', $post_id, $is_update ? 'yes' : 'no' ) );
} , 10, 3 );

// 2) Change the Cars archive sort to newest first by modified date.
add_filter( 'car_sell_shop/car_archive_orderby', function () {
    return 'modified';
} );

add_filter( 'car_sell_shop/car_archive_order', function () {
    return 'DESC';
} );

// 3) Prefix Car titles with the brand name if available.
add_filter( 'car_sell_shop/car_title', function ( $title, $post_id ) {
    $brands = wp_get_post_terms( $post_id, 'brand', array( 'fields' => 'names' ) );
    if ( ! is_wp_error( $brands ) && ! empty( $brands ) ) {
        return sprintf( '%s — %s', $title, implode( ', ', $brands ) );
    }
    return $title;
}, 10, 2 );

// 4) Append a CTA after the Car content.
add_filter( 'car_sell_shop/after_car_content_html', function () {
    return '<div class="car-cta"><a class="wp-element-button" href="#contact">Request a Quote</a></div>';
} );


