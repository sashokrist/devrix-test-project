<?php
/**
 * Redirect grade-level directory to WordPress grade_level taxonomy
 */

// Redirect to the WordPress grade_level taxonomy archive page
header('Location: ../taxonomy-archive/?taxonomy=grade_level', true, 301);
exit;
?>
