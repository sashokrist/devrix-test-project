<?php
/**
 * Demo page for Students Block
 * 
 * This page demonstrates the Students Block functionality
 * by showing different configurations
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Create demo page on plugin activation
add_action( 'init', function() {
    // Check if demo page already exists
    $demo_page = get_page_by_path( 'students-block-demo' );
    
    if ( ! $demo_page ) {
        // Create demo page
        $page_data = array(
            'post_title'    => 'Students Block Demo',
            'post_name'     => 'students-block-demo',
            'post_status'   => 'publish',
            'post_type'     => 'page',
            'post_content'  => '
<!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group">
<!-- wp:heading {"textAlign":"center"} -->
<h2 class="wp-block-heading has-text-align-center">Students Block Demo Page</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"align":"center"} -->
<p class="has-text-align-center">This page demonstrates the Students Block Gutenberg block with different configurations.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Example 1: Show 4 Active Students</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>This block shows 4 active students ordered by name in ascending order.</p>
<!-- /wp:paragraph -->

<!-- wp:students-block/students-display {"numberOfStudents":4,"status":"active","orderBy":"title","order":"ASC"} -->
<div class="wp-block-students-block-students-display"></div>
<!-- /wp:students-block/students-display -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Example 2: Show 6 Students (All Statuses)</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>This block shows 6 students regardless of their status, ordered by date created.</p>
<!-- /wp:paragraph -->

<!-- wp:students-block/students-display {"numberOfStudents":6,"status":"all","orderBy":"date","order":"DESC"} -->
<div class="wp-block-students-block-students-display"></div>
<!-- /wp:students-block/students-display -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Example 3: Show Inactive Students Only</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>This block shows only inactive students.</p>
<!-- /wp:paragraph -->

<!-- wp:students-block/students-display {"numberOfStudents":8,"status":"inactive","orderBy":"title","order":"ASC"} -->
<div class="wp-block-students-block-students-display"></div>
<!-- /wp:students-block/students-display -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Example 4: Show Specific Student</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>This block shows a specific student (you can change this in the block settings).</p>
<!-- /wp:paragraph -->

<!-- wp:students-block/students-display {"showSpecificStudent":true,"specificStudentId":1} -->
<div class="wp-block-students-block-students-display"></div>
<!-- /wp:students-block/students-display -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Block Features</h3>
<!-- /wp:heading -->

<!-- wp:list -->
<ul>
<li><strong>Responsive Design:</strong> The block adapts to different screen sizes</li>
<li><strong>Student Information:</strong> Shows photo, name, class/grade, email, phone, and status</li>
<li><strong>Filtering Options:</strong> Filter by active/inactive status or show all students</li>
<li><strong>Ordering Options:</strong> Sort by name, date created, date modified, or menu order</li>
<li><strong>Specific Student:</strong> Option to show just one specific student</li>
<li><strong>Customizable Count:</strong> Choose how many students to display (1-20)</li>
</ul>
<!-- /wp:list -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">How to Use</h3>
<!-- /wp:heading -->

<!-- wp:list -->
<ul>
<li>Edit any page or post in the WordPress block editor</li>
<li>Click the "+" button to add a new block</li>
<li>Search for "Students Display" or look in the "Widgets" category</li>
<li>Configure the settings in the sidebar panel</li>
<li>Preview or publish to see the results</li>
</ul>
<!-- /wp:list -->

<!-- wp:paragraph -->
<p><em>Note: This demo page was automatically created by the Students Block plugin. You can edit it to see the block configurations or delete it if not needed.</em></p>
<!-- /wp:paragraph -->
',
            'page_template' => 'default'
        );
        
        wp_insert_post( $page_data );
    }
} );

// Add admin notice about demo page
add_action( 'admin_notices', function() {
    if ( isset( $_GET['page'] ) && $_GET['page'] === 'test-students-block' ) {
        $demo_page = get_page_by_path( 'students-block-demo' );
        if ( $demo_page ) {
            ?>
            <div class="notice notice-info">
                <p>
                    <strong>Demo Page Available:</strong> 
                    <a href="<?php echo get_permalink( $demo_page->ID ); ?>" target="_blank">View Students Block Demo Page</a>
                </p>
            </div>
            <?php
        }
    }
} );
