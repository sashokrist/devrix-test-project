<?php
/**
 * Redirect brands directory to WordPress brand taxonomy
 */

// Redirect to the WordPress brand taxonomy archive page
header('Location: ../taxonomy-archive/?taxonomy=brand', true, 301);
exit;
?>
