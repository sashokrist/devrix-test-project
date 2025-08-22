<?php
/**
 * Custom Homepage Template
 * 
 * This template displays the Students content as the homepage
 * instead of redirecting to the Students page
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        
        <header class="page-header">
            <div style="background: #e7f3ff; border: 2px solid #0073aa; padding: 10px; margin: 10px 0; border-radius: 5px; text-align: center;">
                <strong>✅ STUDENTS HOMEPAGE</strong> - Welcome to our Student Management System
            </div>
            <h1 class="page-title">Students</h1>
            <p class="page-description">Browse and manage all students in our system.</p>
        </header>

        <?php
        // Display Students using the shortcode
        echo do_shortcode( '[students_list posts_per_page="4" orderby="title" order="ASC"]' );
        ?>

    </main>
</div>

<?php get_footer(); ?>
