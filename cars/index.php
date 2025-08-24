<?php
/**
 * Redirect cars directory to WordPress cars post type
 */

// Redirect to the WordPress cars archive
header('Location: ../?post_type=car', true, 301);
exit;
?>
