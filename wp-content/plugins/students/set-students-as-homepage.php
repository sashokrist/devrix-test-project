<?php
/**
 * Set Students Page as Homepage
 * 
 * Access this file directly in browser to set the Students page as homepage
 */

// Load WordPress
require_once( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-load.php' );

echo '<h2>Setting Students Page as Homepage</h2>';

// Method 1: Set as Static Front Page
echo '<h3>Method 1: Set as Static Front Page (Recommended)</h3>';
echo '<p>This will make the Students page your homepage while keeping the blog posts accessible at /blog/</p>';

// Get the Students archive page URL
$students_url = home_url( '/students/' );

echo '<h4>Steps to Follow:</h4>';
echo '<ol>';
echo '<li>Go to <strong>WordPress Admin → Settings → Reading</strong></li>';
echo '<li>Under "Your homepage displays", select <strong>"A static page"</strong></li>';
echo '<li>For "Homepage", create a new page or select an existing page</li>';
echo '<li>For "Posts page", you can leave blank or create a "Blog" page</li>';
echo '<li>Click <strong>"Save Changes"</strong></li>';
echo '</ol>';

echo '<h4>Alternative: Redirect Homepage to Students</h4>';
echo '<p>If you want to automatically redirect visitors from the homepage to the Students page, add this code to your theme\'s <code>functions.php</code>:</p>';

echo '<pre><code>';
echo '// Redirect homepage to Students page
add_action( "template_redirect", function() {
    if ( is_home() && ! is_admin() ) {
        wp_redirect( home_url( "/students/" ), 301 );
        exit;
    }
});';
echo '</code></pre>';

// Method 2: Custom Redirect
echo '<h3>Method 2: Custom Redirect (Automatic)</h3>';
echo '<p>This will automatically redirect visitors from the homepage to the Students page:</p>';

echo '<h4>Add to Theme functions.php:</h4>';
echo '<pre><code>';
echo '// Redirect homepage to Students page
add_action( "template_redirect", function() {
    if ( is_front_page() && ! is_admin() ) {
        wp_redirect( home_url( "/students/" ), 301 );
        exit;
    }
});';
echo '</code></pre>';

// Method 3: Custom Homepage Template
echo '<h3>Method 3: Custom Homepage Template</h3>';
echo '<p>Create a custom homepage template that displays the Students content:</p>';

echo '<h4>Create front-page.php in your theme:</h4>';
echo '<pre><code>';
echo '<?php
// Custom homepage template
get_header();

// Display Students content
echo do_shortcode( "[students_list posts_per_page=\"4\" orderby=\"title\" order=\"ASC\"]" );

get_footer();';
echo '</code></pre>';

echo '<h3>Current Settings:</h3>';
echo '<ul>';
echo '<li><strong>Homepage URL:</strong> ' . home_url( '/' ) . '</li>';
echo '<li><strong>Students URL:</strong> ' . $students_url . '</li>';
echo '<li><strong>Reading Settings:</strong> ' . ( get_option( 'show_on_front' ) === 'page' ? 'Static Page' : 'Blog Posts' ) . '</li>';
echo '</ul>';

echo '<h3>Quick Setup:</h3>';
echo '<p><strong>Option A:</strong> <a href="' . admin_url( 'options-reading.php' ) . '">Go to Reading Settings</a></p>';
echo '<p><strong>Option B:</strong> <a href="' . admin_url( 'post-new.php?post_type=page' ) . '">Create a new page</a> and set it as homepage</p>';

echo '<h3>Test Links:</h3>';
echo '<ul>';
echo '<li><a href="' . home_url( '/' ) . '">Current Homepage</a></li>';
echo '<li><a href="' . $students_url . '">Students Page</a></li>';
echo '</ul>';

echo '<p><strong>Recommendation:</strong> Use Method 1 (Static Front Page) for the best user experience and SEO.</p>';
?>
