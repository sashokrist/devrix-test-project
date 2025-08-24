<?php
/**
 * Load taxonomy archive page directly
 */

// Include WordPress
require_once('../wp-config.php');
require_once('../wp-load.php');

// Set up the query to load the specific page
global $wp_query;
$wp_query = new WP_Query(array(
    'p' => 80,
    'post_type' => 'page'
));

// Set the page template
add_filter('page_template', function($template) {
    return __DIR__ . '/../page-taxonomy-archive.php';
});

// Load the page
if ($wp_query->have_posts()) {
    $wp_query->the_post();
    include(__DIR__ . '/../page-taxonomy-archive.php');
} else {
    echo "Page not found.";
}
?>
