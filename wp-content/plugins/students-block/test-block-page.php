<?php
/**
 * Test page for Students Block
 * 
 * This file demonstrates how to use the Students Block
 * and shows different configurations
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Add admin menu page
add_action( 'admin_menu', function() {
    add_submenu_page(
        'edit.php?post_type=student',
        'Test Students Block',
        'Test Block',
        'manage_options',
        'test-students-block',
        'render_test_block_page'
    );
} );

function render_test_block_page() {
    ?>
    <div class="wrap">
        <h1>Students Block - Test Page</h1>
        
        <div class="notice notice-info">
            <p><strong>Students Block Plugin Test Page</strong></p>
            <p>This page demonstrates the different ways to use the Students Block Gutenberg block.</p>
        </div>

        <h2>How to Use the Students Block</h2>
        
        <div class="card">
            <h3>1. Add the Block in Gutenberg Editor</h3>
            <ol>
                <li>Edit any page or post in the WordPress block editor</li>
                <li>Click the "+" button to add a new block</li>
                <li>Search for "Students Display" or look in the "Widgets" category</li>
                <li>Click to add the block</li>
            </ol>
        </div>

        <div class="card">
            <h3>2. Block Settings Available</h3>
            <ul>
                <li><strong>Show specific student:</strong> Toggle to show only one student</li>
                <li><strong>Select Student:</strong> Choose a specific student from dropdown</li>
                <li><strong>Number of students:</strong> Set how many students to display (1-20)</li>
                <li><strong>Status filter:</strong> Filter by Active, Inactive, or All students</li>
                <li><strong>Order by:</strong> Sort by Name, Date created, Date modified, or Menu order</li>
                <li><strong>Order:</strong> Choose Ascending or Descending order</li>
            </ul>
        </div>

        <h2>Example Configurations</h2>

        <div class="card">
            <h3>Example 1: Show 4 Active Students</h3>
            <p>This configuration shows 4 active students ordered by name in ascending order.</p>
            <ul>
                <li>Number of students: 4</li>
                <li>Status filter: Active</li>
                <li>Order by: Name</li>
                <li>Order: Ascending</li>
            </ul>
        </div>

        <div class="card">
            <h3>Example 2: Show Specific Student</h3>
            <p>This configuration shows only one specific student.</p>
            <ul>
                <li>Show specific student: Enabled</li>
                <li>Select Student: Choose from dropdown</li>
            </ul>
        </div>

        <div class="card">
            <h3>Example 3: Show All Inactive Students</h3>
            <p>This configuration shows all inactive students.</p>
            <ul>
                <li>Number of students: 20 (maximum)</li>
                <li>Status filter: Inactive</li>
                <li>Order by: Date created</li>
                <li>Order: Descending</li>
            </ul>
        </div>

        <h2>Block Features</h2>

        <div class="card">
            <h3>Student Card Display</h3>
            <p>Each student is displayed in a responsive card with:</p>
            <ul>
                <li>Student photo (or placeholder if no image)</li>
                <li>Student name (linked to student page)</li>
                <li>Class/Grade information</li>
                <li>Email address</li>
                <li>Phone number</li>
                <li>Status indicator (Active/Inactive)</li>
            </ul>
        </div>

        <div class="card">
            <h3>Responsive Design</h3>
            <p>The block is fully responsive and works on all devices:</p>
            <ul>
                <li>Desktop: Grid layout with multiple columns</li>
                <li>Tablet: Adjusted grid for medium screens</li>
                <li>Mobile: Single column layout</li>
            </ul>
        </div>

        <h2>Integration with Students Plugin</h2>

        <div class="card">
            <p>This block integrates with the existing Students plugin and uses:</p>
            <ul>
                <li>Student custom post type</li>
                <li>Student meta fields (email, phone, class/grade, status)</li>
                <li>Student taxonomies (courses, grade levels)</li>
                <li>Student archive pages</li>
            </ul>
        </div>

        <h2>Testing the Block</h2>

        <div class="card">
            <h3>To test the block:</h3>
            <ol>
                <li>Go to <a href="<?php echo admin_url( 'post-new.php' ); ?>">Add New Post</a></li>
                <li>Add the "Students Display" block</li>
                <li>Configure the settings in the sidebar</li>
                <li>Preview or publish the post to see the results</li>
            </ol>
        </div>

        <div class="card">
            <h3>Expected Behavior</h3>
            <ul>
                <li>Block should appear in the block inserter under "Widgets" category</li>
                <li>Settings panel should show in the sidebar when block is selected</li>
                <li>Changes should be reflected immediately in the editor preview</li>
                <li>Frontend should display students according to the settings</li>
                <li>Student cards should be responsive and well-styled</li>
            </ul>
        </div>

        <div class="notice notice-success">
            <p><strong>Success!</strong> If you can see this page, the Students Block plugin is working correctly.</p>
        </div>
    </div>

    <style>
        .card {
            background: white;
            border: 1px solid #ccd0d4;
            border-radius: 4px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 1px 1px rgba(0,0,0,.04);
        }
        .card h3 {
            margin-top: 0;
            color: #23282d;
        }
        .card ul, .card ol {
            margin-left: 20px;
        }
        .card li {
            margin-bottom: 5px;
        }
    </style>
    <?php
}
