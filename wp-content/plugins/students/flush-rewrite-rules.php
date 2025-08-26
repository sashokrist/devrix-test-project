<?php
/**
 * Temporary script to flush rewrite rules
 * Run this once to fix pagination issues
 */

// Load WordPress
require_once('../../../wp-load.php');

// Flush rewrite rules
flush_rewrite_rules();

echo "Rewrite rules flushed successfully!";
echo "<br>Pagination should now work correctly.";
echo "<br><a href='/devrix-test-project/students/'>Go to Students Page</a>";

// Delete this file after use
unlink(__FILE__);
?>
