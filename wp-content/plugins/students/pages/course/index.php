<?php
/**
 * Redirect course directory to WordPress course taxonomy
 */

// Redirect to the WordPress course taxonomy archive page
header('Location: ../taxonomy-archive/?taxonomy=course', true, 301);
exit;
?>
