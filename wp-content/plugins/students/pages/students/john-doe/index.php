<?php
/**
 * Redirect John Doe student profile to WordPress query parameter format
 */
header('Location: ../../?post_type=student&p=78', true, 301);
exit;
?>