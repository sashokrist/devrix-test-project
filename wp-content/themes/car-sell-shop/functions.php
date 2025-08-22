<?php
// Examples: how to use the custom hooks from Car Sell Shop Core.

/**
 * Enqueue Students plugin styles
 */
function car_sell_shop_enqueue_students_styles() {
    if ( is_post_type_archive( 'student' ) || is_singular( 'student' ) || is_tax( 'course' ) || is_tax( 'grade_level' ) ) {
        wp_enqueue_style(
            'car-sell-shop-students',
            get_template_directory_uri() . '/assets/css/students.css',
            array(),
            '1.0.0'
        );
    }
}
add_action( 'wp_enqueue_scripts', 'car_sell_shop_enqueue_students_styles' );

/**
 * Load theme text domain for translations
 */
function car_sell_shop_load_textdomain() {
    // Load from theme directory first, then fallback to global languages directory
    $loaded = load_theme_textdomain( 'car-sell-shop', get_template_directory() . '/languages' );
    
    // If not loaded from theme directory, try global languages directory
    if ( ! $loaded ) {
        load_theme_textdomain( 'car-sell-shop', WP_CONTENT_DIR . '/languages/themes' );
    }
}
add_action( 'after_setup_theme', 'car_sell_shop_load_textdomain' );

/**
 * Log when a Car is saved (create/update)
 */
function car_sell_shop_log_car_saved( $post_id, $post, $is_update ) {
    error_log( sprintf( '[Car Sell Shop] Car %d saved (update: %s).', $post_id, $is_update ? 'yes' : 'no' ) );
}
add_action( 'car_sell_shop/car_saved', 'car_sell_shop_log_car_saved', 10, 3 );

/**
 * Change the Cars archive sort to newest first by modified date
 */
function car_sell_shop_archive_orderby() {
    return 'modified';
}
add_filter( 'car_sell_shop/car_archive_orderby', 'car_sell_shop_archive_orderby' );

function car_sell_shop_archive_order() {
    return 'DESC';
}
add_filter( 'car_sell_shop/car_archive_order', 'car_sell_shop_archive_order' );

/**
 * Prefix Car titles with the brand name if available
 */
function car_sell_shop_prefix_car_title( $title, $post_id ) {
    $brands = wp_get_post_terms( $post_id, 'brand', array( 'fields' => 'names' ) );
    if ( ! is_wp_error( $brands ) && ! empty( $brands ) ) {
        return sprintf( '%s — %s', $title, implode( ', ', $brands ) );
    }
    return $title;
}
add_filter( 'car_sell_shop/car_title', 'car_sell_shop_prefix_car_title', 10, 2 );

/**
 * Append a CTA after the Car content
 */
function car_sell_shop_append_cta() {
    return '<div class="car-cta"><a class="wp-element-button" href="#contact">Request a Quote</a></div>';
}
add_filter( 'car_sell_shop/after_car_content_html', 'car_sell_shop_append_cta' );

/**
 * Send email to admin when user updates their profile
 * Hooks into WordPress user profile update actions
 */
function notify_admin_user_profile_updated( $user_id, $old_user_data ) {
    // Get the updated user data
    $user = get_userdata( $user_id );
    
    // Get admin email
    $admin_email = get_option( 'admin_email' );
    
    // Prepare email content
    $subject = 'User Profile Updated - Car Sell Shop';
    $message = sprintf(
        "A user has updated their profile on your Car Sell Shop website.\n\n" .
        "User Details:\n" .
        "- User ID: %d\n" .
        "- Username: %s\n" .
        "- Display Name: %s\n" .
        "- Email: %s\n" .
        "- Role: %s\n" .
        "- Profile updated at: %s\n\n" .
        "You can view their profile at: %s",
        $user_id,
        $user->user_login,
        $user->display_name,
        $user->user_email,
        implode( ', ', $user->roles ),
        current_time( 'Y-m-d H:i:s' ),
        admin_url( 'user-edit.php?user_id=' . $user_id )
    );
    
    // Log the email attempt
    error_log( sprintf( '[Email Attempt] Trying to send profile update email to %s for user %d', $admin_email, $user_id ) );
    
    // Send the email
    $sent = wp_mail( $admin_email, $subject, $message );
    
    // Log the result
    if ( $sent ) {
        error_log( sprintf( '[Email Success] Profile update notification sent to admin for user %d', $user_id ) );
    } else {
        error_log( sprintf( '[Email Failed] Failed to send profile update notification for user %d', $user_id ) );
    }
    
    return $sent;
}

// Hook into user profile update actions
add_action( 'profile_update', 'notify_admin_user_profile_updated', 10, 2 );

/**
 * Send email to admin when new user registers
 */
function notify_admin_user_registered( $user_id ) {
    $user = get_userdata( $user_id );
    $admin_email = get_option( 'admin_email' );
    
    $subject = 'New User Registration - Car Sell Shop';
    $message = sprintf(
        "A new user has registered on your Car Sell Shop website.\n\n" .
        "User Details:\n" .
        "- User ID: %d\n" .
        "- Username: %s\n" .
        "- Display Name: %s\n" .
        "- Email: %s\n" .
        "- Registration date: %s\n\n" .
        "You can view their profile at: %s",
        $user_id,
        $user->user_login,
        $user->display_name,
        $user->user_email,
        $user->user_registered,
        admin_url( 'user-edit.php?user_id=' . $user_id )
    );
    
    wp_mail( $admin_email, $subject, $message );
}
add_action( 'user_register', 'notify_admin_user_registered' );

/**
 * Send email to admin when user resets password
 */
function notify_admin_password_reset( $user, $new_pass ) {
    $admin_email = get_option( 'admin_email' );
    
    $subject = 'User Password Reset - Car Sell Shop';
    $message = sprintf(
        "A user has reset their password on your Car Sell Shop website.\n\n" .
        "User Details:\n" .
        "- User ID: %d\n" .
        "- Username: %s\n" .
        "- Email: %s\n" .
        "- Password reset at: %s\n\n" .
        "You can view their profile at: %s",
        $user->ID,
        $user->user_login,
        $user->user_email,
        current_time( 'Y-m-d H:i:s' ),
        admin_url( 'user-edit.php?user_id=' . $user->ID )
    );
    
    wp_mail( $admin_email, $subject, $message );
}
add_action( 'password_reset', 'notify_admin_password_reset', 10, 2 );

/**
 * WordPress Filter Priority Exercise
 * Demonstrates how filters execute in priority order
 */

/**
 * Prepend "This is my filter" to content on singular posts only
 * Uses a custom filter to allow other developers to modify the text
 */
function prepend_filter_text( $content ) {
    // Only modify content on singular post pages
    if ( is_singular( 'post' ) && is_main_query() ) {
        // Get the filter text with a custom hook for extensibility
        $filter_text = apply_filters( 'car_sell_shop_filter_text', __( 'This is my filter', 'car-sell-shop' ) );
        return $filter_text . $content;
    }
    return $content;
}
add_filter( 'the_content', 'prepend_filter_text', 10, 1 );

/**
 * Example: How to use the custom filter to modify the filter text
 * This demonstrates how other developers can hook into your custom filter
 */
function modify_filter_text_example( $filter_text ) {
    // Change the text to "This is my extendable filter"
    return __( 'This is my extendable filter', 'car-sell-shop' );
}
add_filter( 'car_sell_shop_filter_text', 'modify_filter_text_example' );

/**
 * Custom Hook Documentation and Examples
 * 
 * The 'car_sell_shop_filter_text' filter allows other developers to modify
 * the text that appears before post content on singular post pages.
 * 
 * Hook Name: car_sell_shop_filter_text
 * Hook Type: Filter
 * Parameters: $filter_text (string) - The current filter text
 * Returns: string - The modified filter text
 * 
 * Usage Examples:
 * 
 * 1. Simple text replacement:
 * add_filter( 'car_sell_shop_filter_text', function( $text ) {
 *     return 'Custom text here';
 * });
 * 
 * 2. Conditional text based on user role:
 * add_filter( 'car_sell_shop_filter_text', function( $text ) {
 *     if ( current_user_can( 'administrator' ) ) {
 *         return 'Admin only text';
 *     }
 *     return $text;
 * });
 * 
 * 3. Adding HTML markup:
 * add_filter( 'car_sell_shop_filter_text', function( $text ) {
 *     return '<strong>' . $text . '</strong>';
 * });
 * 
 * 4. Multiple filters (they execute in order):
 * add_filter( 'car_sell_shop_filter_text', function( $text ) {
 *     return $text . ' - Modified by Plugin A';
 * });
 * add_filter( 'car_sell_shop_filter_text', function( $text ) {
 *     return $text . ' - Modified by Plugin B';
 * });
 */

/**
 * Append "<div>Two</div>" to content on singular posts only
 */
function append_div_two( $content ) {
    // Only modify content on singular post pages
    if ( is_singular( 'post' ) && is_main_query() ) {
        return $content . '<div>Two</div>';
    }
    return $content;
}
add_filter( 'the_content', 'append_div_two', 10, 1 );

/**
 * Append "<div>One</div>" BEFORE the "Two" div (higher priority = runs first)
 */
function append_div_one( $content ) {
    // Only modify content on singular post pages
    if ( is_singular( 'post' ) && is_main_query() ) {
        return $content . '<div>One</div>';
    }
    return $content;
}
add_filter( 'the_content', 'append_div_one', 5, 1 ); // Priority 5 (runs before priority 10)

/**
 * Append "<div>Three</div>" AFTER the "Two" div (lower priority = runs after)
 */
function append_div_three( $content ) {
    // Only modify content on singular post pages
    if ( is_singular( 'post' ) && is_main_query() ) {
        return $content . '<div>Three</div>';
    }
    return $content;
}
add_filter( 'the_content', 'append_div_three', 15, 1 ); // Priority 15 (runs after priority 10)

/**
 * Expected Result on singular post pages:
 * 
 * "This is my filter" + [Original Content] + "<div>One</div>" + "<div>Two</div>" + "<div>Three</div>"
 * 
 * Filter Execution Order:
 * 1. Priority 5:  Adds "<div>One</div>"
 * 2. Priority 10: Adds "This is my filter" (prepend) + "<div>Two</div>" (append)
 * 3. Priority 15: Adds "<div>Three</div>"
 */

/**
 * Email Configuration for Testing
 * Configure WordPress to use a test email setup
 */

/**
 * Log email errors for debugging
 */
function log_email_errors( $error ) {
    error_log( '[Email Error] ' . $error->get_error_message() );
}
add_action( 'wp_mail_failed', 'log_email_errors' );

/**
 * Test email function that can be called manually
 */
function test_email_functionality() {
    $admin_email = get_option( 'admin_email' );
    $subject = 'Test Email - Car Sell Shop';
    $message = 'This is a test email from WordPress to verify email functionality.';
    
    error_log( '[Email Test] Attempting to send test email to: ' . $admin_email );
    
    $result = wp_mail( $admin_email, $subject, $message );
    
    error_log( '[Email Test] Result: ' . ( $result ? 'SUCCESS' : 'FAILED' ) );
    
    return $result;
}

/**
 * Manual test function - call this via WP-CLI or browser
 */
function manual_email_test() {
    echo "Testing email functionality...\n";
    $result = test_email_functionality();
    echo "Email test result: " . ( $result ? 'SUCCESS' : 'FAILED' ) . "\n";
    return $result;
}

/**
 * Simple email test function
 */
function simple_email_test() {
    echo "=== EMAIL TEST ===\n";
    
    // Test 1: Check if wp_mail function exists
    echo "1. wp_mail function exists: " . (function_exists('wp_mail') ? 'YES' : 'NO') . "\n";
    
    // Test 2: Check admin email
    $admin_email = get_option('admin_email');
    echo "2. Admin email: $admin_email\n";
    
    // Test 3: Try to send email
    echo "3. Attempting to send email...\n";
    $result = wp_mail($admin_email, 'Test Email', 'This is a test email from WordPress.');
    echo "   Email result: " . ($result ? 'SUCCESS' : 'FAILED') . "\n";
    
    // Test 4: Check if SMTP is configured
    echo "4. SMTP configuration applied: " . (has_action('phpmailer_init', 'mailtrap') ? 'YES' : 'NO') . "\n";
    
    echo "=== END TEST ===\n";
    return $result;
}

/**
 * Add a test endpoint for email testing
 */
function add_email_test_endpoint() {
    if (isset($_GET['test_email']) && current_user_can('administrator')) {
        echo "<h1>Email Test Results</h1>";
        echo "<pre>";
        $result = simple_email_test();
        echo "</pre>";
        echo "<p><strong>Email Test Result:</strong> " . ($result ? 'SUCCESS' : 'FAILED') . "</p>";
        echo "<p><a href='http://localhost/wp-admin/'>Back to Admin</a></p>";
        exit;
    }
    
    // Simple test that doesn't require admin login
    if (isset($_GET['email_test'])) {
        echo "<h1>Simple Email Test</h1>";
        
        // Test if SMTP is configured
        echo "<p>SMTP Configuration: ";
        if (has_action('phpmailer_init', 'mailtrap')) {
            echo "✅ Applied</p>";
        } else {
            echo "❌ Not Applied</p>";
        }
        
        // Try to send email
        echo "<p>Attempting to send email...</p>";
        $result = wp_mail('test@example.com', 'Test Email', 'This is a test email from WordPress.');
        
        if ($result) {
            echo "<p>✅ Email sent successfully!</p>";
        } else {
            echo "<p>❌ Email failed to send</p>";
        }
        
        echo "<p><a href='http://localhost/'>Back to Home</a></p>";
        exit;
    }
}
add_action('init', 'add_email_test_endpoint');

/**
 * Mailtrap SMTP Configuration
 * Configure WordPress to use Mailtrap for email testing
 */
function mailtrap($phpmailer) {
    $phpmailer->isSMTP();
    $phpmailer->Host = 'sandbox.smtp.mailtrap.io';
    $phpmailer->SMTPAuth = true;
    $phpmailer->Port = 2525;
    $phpmailer->Username = 'b3afe85cc6bd7e';
    $phpmailer->Password = '6f636f14b6c7e7';
    
    // Add debugging
    $phpmailer->SMTPDebug = 0; // Set to 0 to disable debugging for now
    $phpmailer->Debugoutput = function( $str, $level ) {
        error_log( "[SMTP Debug] $str" );
    };
    
    // Set secure connection
    $phpmailer->SMTPSecure = false; // No SSL for port 2525
    
    // Set timeout
    $phpmailer->Timeout = 30;
    $phpmailer->SMTPKeepAlive = true;
}

// Apply SMTP configuration in multiple ways to ensure it works
add_action('phpmailer_init', 'mailtrap');
add_action( 'wp_mail_failed', 'log_email_errors' );

// Force SMTP configuration to be applied early
add_action('plugins_loaded', function() {
    add_action('phpmailer_init', 'mailtrap');
}, 1);

add_action('init', function() {
    add_action('phpmailer_init', 'mailtrap');
}, 1);

/**
 * Custom Navigation Menu Item for Profile Settings
 * Adds a "Profile Settings" menu item for logged-in users only
 */

/**
 * Add custom menu item for logged-in users
 */
function add_profile_settings_menu_item( $items, $args ) {
    // Check if user is logged in
    if ( is_user_logged_in() ) {
        // Create profile settings link
        $profile_link = admin_url( 'profile.php' );
        $profile_item = sprintf(
            '<li class="menu-item menu-item-profile-settings"><a href="%s" class="wp-block-navigation-item__content">%s</a></li>',
            esc_url( $profile_link ),
            esc_html__( 'Profile Settings', 'car-sell-shop' )
        );
        
        // Add the profile settings item to the menu
        $items .= $profile_item;
    }
    
    return $items;
}
add_filter( 'wp_nav_menu_items', 'add_profile_settings_menu_item', 10, 2 );

/**
 * Alternative method: Add menu item using wp_nav_menu_objects filter
 */
function add_profile_settings_menu_object( $menu_items, $args ) {
    // Only modify primary navigation
    if ( $args->theme_location === 'primary' || $args->menu_class === 'wp-block-navigation__container' ) {
        // Check if user is logged in
        if ( is_user_logged_in() ) {
            // Create a custom menu item object
            $profile_item = (object) array(
                'ID'               => 'profile-settings',
                'title'            => __( 'Profile Settings', 'car-sell-shop' ),
                'url'              => admin_url( 'profile.php' ),
                'menu_item_parent' => 0,
                'db_id'            => 'profile-settings',
                'classes'          => array( 'menu-item', 'menu-item-profile-settings' ),
                'xfn'              => '',
                'target'           => '',
                'current'          => is_admin() && $_SERVER['PHP_SELF'] === '/wp-admin/profile.php',
                'current_item_ancestor' => false,
                'current_item_parent' => false,
                'menu_order'       => 999, // Add at the end
                'object'           => 'custom',
                'object_id'        => 'profile-settings',
                'type'             => 'custom',
                'type_label'       => __( 'Custom Link', 'car-sell-shop' ),
            );
            
            // Add the item to the menu
            $menu_items[] = $profile_item;
        }
    }
    
    return $menu_items;
}
add_filter( 'wp_nav_menu_objects', 'add_profile_settings_menu_object', 10, 2 );

/**
 * For block themes: Add profile settings link using JavaScript
 */
function add_profile_settings_javascript() {
    if ( is_user_logged_in() ) {
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Target the specific navigation container found in the HTML
            const navContainer = document.querySelector('.wp-block-navigation__container');
            
            if (navContainer) {
                // Create profile settings link
                const profileLink = document.createElement('li');
                profileLink.className = 'wp-block-pages-list__item wp-block-navigation-item open-on-hover-click menu-item-profile-settings';
                profileLink.innerHTML = `
                    <a href="<?php echo admin_url('profile.php'); ?>" class="wp-block-pages-list__item__link wp-block-navigation-item__content">
                        <?php echo esc_html__('Profile Settings', 'car-sell-shop'); ?>
                    </a>
                `;
                
                // Add to navigation
                navContainer.appendChild(profileLink);
                console.log('Profile Settings menu item added successfully');
            } else {
                console.log('Navigation container not found');
            }
        });
        </script>
        <?php
    }
}
add_action( 'wp_footer', 'add_profile_settings_javascript' );

/**
 * For block themes: Add profile settings link using WordPress filters
 */
function add_profile_settings_block_theme( $items, $args ) {
    // Check if user is logged in
    if ( is_user_logged_in() ) {
        // Create profile settings link
        $profile_link = admin_url( 'profile.php' );
        $profile_item = sprintf(
            '<li class="wp-block-pages-list__item wp-block-navigation-item open-on-hover-click menu-item-profile-settings"><a href="%s" class="wp-block-pages-list__item__link wp-block-navigation-item__content">%s</a></li>',
            esc_url( $profile_link ),
            esc_html__( 'Profile Settings', 'car-sell-shop' )
        );
        
        // Add the profile settings item to the menu
        $items .= $profile_item;
    }
    
    return $items;
}
add_filter( 'wp_nav_menu_items', 'add_profile_settings_block_theme', 10, 2 );

/**
 * Alternative: Use wp_nav_menu_objects filter for more control
 */
function add_profile_settings_menu_object_block( $menu_items, $args ) {
    // Check if user is logged in
    if ( is_user_logged_in() ) {
        // Create a custom menu item object
        $profile_item = (object) array(
            'ID'               => 'profile-settings',
            'title'            => __( 'Profile Settings', 'car-sell-shop' ),
            'url'              => admin_url( 'profile.php' ),
            'menu_item_parent' => 0,
            'db_id'            => 'profile-settings',
            'classes'          => array( 'wp-block-pages-list__item', 'wp-block-navigation-item', 'open-on-hover-click', 'menu-item-profile-settings' ),
            'xfn'              => '',
            'target'           => '',
            'current'          => is_admin() && $_SERVER['PHP_SELF'] === '/wp-admin/profile.php',
            'current_item_ancestor' => false,
            'current_item_parent' => false,
            'menu_order'       => 999, // Add at the end
            'object'           => 'custom',
            'object_id'        => 'profile-settings',
            'type'             => 'custom',
            'type_label'       => __( 'Custom Link', 'car-sell-shop' ),
        );
        
        // Add the item to the menu
        $menu_items[] = $profile_item;
    }
    
    return $menu_items;
}
add_filter( 'wp_nav_menu_objects', 'add_profile_settings_menu_object_block', 10, 2 );

/**
 * Add a test endpoint to check navigation menu status
 */
function add_navigation_test_endpoint() {
    if (isset($_GET['test_navigation'])) {
        echo "<h1>Navigation Menu Test</h1>";
        
        echo "<h2>User Status:</h2>";
        echo "<p>User logged in: " . (is_user_logged_in() ? "✅ YES" : "❌ NO") . "</p>";
        
        if (is_user_logged_in()) {
            $current_user = wp_get_current_user();
            echo "<p>Current user: " . $current_user->display_name . " (ID: " . $current_user->ID . ")</p>";
            echo "<p>Profile URL: " . admin_url('profile.php') . "</p>";
            
            echo "<h2>Navigation Menu:</h2>";
            echo "<p>✅ Profile Settings menu item should be visible in the navigation</p>";
            echo "<p>🔗 <a href='" . admin_url('profile.php') . "'>Go to Profile Settings</a></p>";
        } else {
            echo "<p>❌ Profile Settings menu item will NOT be visible (user not logged in)</p>";
            echo "<p>🔗 <a href='" . wp_login_url() . "'>Login to see the menu item</a></p>";
        }
        
        echo "<h2>Instructions:</h2>";
        echo "<ol>";
        echo "<li>Make sure you are logged in to WordPress</li>";
        echo "<li>Visit the homepage: <a href='http://localhost/'>http://localhost/</a></li>";
        echo "<li>Look for 'Profile Settings' in the navigation menu</li>";
        echo "<li>Click on it to go to your profile settings page</li>";
        echo "</ol>";
        
        echo "<p><a href='http://localhost/'>← Back to Homepage</a></p>";
        exit;
    }
}
add_action('init', 'add_navigation_test_endpoint');

/**
 * Add CSS styles for the profile settings menu item
 */
function add_profile_settings_styles() {
    if ( is_user_logged_in() ) {
        ?>
        <style>
        .menu-item-profile-settings {
            /* Inherit styles from existing menu items */
            display: inline-block;
            margin: 0;
            padding: 0;
        }
        
        .menu-item-profile-settings a {
            /* Match existing navigation styles */
            text-decoration: none;
            color: inherit;
            padding: 0.5rem 1rem;
            display: block;
        }
        
        .menu-item-profile-settings a:hover {
            /* Add hover effect */
            background-color: rgba(0, 0, 0, 0.1);
        }
        
        /* Highlight current page */
        .menu-item-profile-settings.current-menu-item a {
            font-weight: bold;
        }
        </style>
        <?php
    }
}
add_action( 'wp_head', 'add_profile_settings_styles' );

/**
 * Add JavaScript to highlight current page
 */
function add_profile_settings_highlight() {
    if ( is_user_logged_in() ) {
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check if we're on the profile page
            if (window.location.href.includes('profile.php')) {
                const profileMenuItem = document.querySelector('.menu-item-profile-settings');
                if (profileMenuItem) {
                    profileMenuItem.classList.add('current-menu-item');
                }
            }
        });
        </script>
        <?php
    }
}
add_action( 'wp_footer', 'add_profile_settings_highlight' );

/**
 * Custom Action Hook for My Custom Template
 * 
 * This action hook is fired right after the content is displayed in the "My Custom Template".
 * Other developers can hook into this action to add custom functionality.
 * 
 * Hook Name: car_sell_shop_after_custom_template_content
 * Hook Type: Action
 * Location: My Custom Template (after post content, before post author)
 * 
 * Usage Examples:
 * 
 * 1. Add custom HTML content:
 * add_action( 'car_sell_shop_after_custom_template_content', function() {
 *     echo '<div class="custom-content">This is custom content added via action hook!</div>';
 * });
 * 
 * 2. Add social sharing buttons:
 * add_action( 'car_sell_shop_after_custom_template_content', function() {
 *     echo '<div class="social-sharing">Share this page: [Facebook] [Twitter] [LinkedIn]</div>';
 * });
 * 
 * 3. Add related posts:
 * add_action( 'car_sell_shop_after_custom_template_content', function() {
 *     // Add related posts logic here
 *     echo '<div class="related-posts">Related Posts: ...</div>';
 * });
 * 
 * 4. Add custom analytics tracking:
 * add_action( 'car_sell_shop_after_custom_template_content', function() {
 *     echo '<script>console.log("Custom template content viewed");</script>';
 * });
 */

/**
 * Example: Add custom content after the content in My Custom Template
 * This demonstrates how other developers can hook into the custom action
 */
function car_sell_shop_custom_template_content_example() {
    // Only show on pages using the My Custom Template
    if ( is_page_template( 'my-custom-template' ) || is_page_template( 'page-my-custom' ) ) {
        echo '<div class="custom-template-info">';
        echo '<h3>' . __( 'Custom Template Information', 'car-sell-shop' ) . '</h3>';
        echo '<p>' . __( 'This content was added via the custom action hook: car_sell_shop_after_custom_template_content', 'car-sell-shop' ) . '</p>';
        echo '<p>' . __( 'Current page ID:', 'car-sell-shop' ) . ' ' . get_the_ID() . '</p>';
        echo '<p>' . __( 'Page author:', 'car-sell-shop' ) . ' ' . get_the_author() . '</p>';
        echo '</div>';
    }
}
add_action( 'car_sell_shop_after_custom_template_content', 'car_sell_shop_custom_template_content_example' );

/**
 * Example: Add social sharing buttons after content
 */
function car_sell_shop_add_social_sharing() {
    if ( is_page_template( 'my-custom-template' ) || is_page_template( 'page-my-custom' ) ) {
        $current_url = get_permalink();
        $page_title = get_the_title();
        
        echo '<div class="social-sharing-buttons">';
        echo '<h4>' . __( 'Share this page:', 'car-sell-shop' ) . '</h4>';
        echo '<a href="https://www.facebook.com/sharer/sharer.php?u=' . urlencode( $current_url ) . '" target="_blank" class="social-button facebook">Facebook</a>';
        echo '<a href="https://twitter.com/intent/tweet?url=' . urlencode( $current_url ) . '&text=' . urlencode( $page_title ) . '" target="_blank" class="social-button twitter">Twitter</a>';
        echo '<a href="https://www.linkedin.com/sharing/share-offsite/?url=' . urlencode( $current_url ) . '" target="_blank" class="social-button linkedin">LinkedIn</a>';
        echo '</div>';
    }
}
add_action( 'car_sell_shop_after_custom_template_content', 'car_sell_shop_add_social_sharing' );

/**
 * Add CSS styles for the custom action hook content
 */
function car_sell_shop_custom_template_styles() {
    if ( is_page_template( 'my-custom-template' ) || is_page_template( 'page-my-custom' ) ) {
        ?>
        <style>
        .custom-action-hook-container {
            margin: 2rem 0;
            padding: 1.5rem;
            background: #f8f9fa;
            border-left: 4px solid #0073aa;
            border-radius: 4px;
        }
        
        .custom-template-info {
            margin-bottom: 2rem;
        }
        
        .custom-template-info h3 {
            color: #0073aa;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }
        
        .custom-template-info p {
            margin-bottom: 0.5rem;
            color: #333;
        }
        
        .social-sharing-buttons {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #ddd;
        }
        
        .social-sharing-buttons h4 {
            margin-bottom: 1rem;
            color: #333;
            font-size: 1.2rem;
        }
        
        .social-button {
            display: inline-block;
            margin-right: 1rem;
            padding: 0.5rem 1rem;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .social-button.facebook {
            background: #1877f2;
            color: white;
        }
        
        .social-button.twitter {
            background: #1da1f2;
            color: white;
        }
        
        .social-button.linkedin {
            background: #0077b5;
            color: white;
        }
        
        .social-button:hover {
            opacity: 0.8;
            transform: translateY(-2px);
        }
        </style>
        <?php
    }
}
add_action( 'wp_head', 'car_sell_shop_custom_template_styles' );

/**
 * Add professional navigation styling
 */
function car_sell_shop_navigation_styles() {
    ?>
    <style>
    /* Professional Navigation Styling */
    .wp-block-navigation {
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin: 1rem 0;
        padding: 0.5rem 1rem;
    }

    .wp-block-navigation__container {
        display: flex;
        align-items: center;
        gap: 0;
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .wp-block-navigation-item {
        position: relative;
        margin: 0;
    }

    .wp-block-navigation-item__content {
        display: block;
        padding: 0.75rem 1.25rem;
        color: #ffffff !important;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.95rem;
        border-radius: 6px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .wp-block-navigation-item__content:hover {
        background: rgba(255, 255, 255, 0.15);
        color: #ffffff !important;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .wp-block-navigation-item__content:active {
        transform: translateY(0);
    }

    /* Active/Current page styling */
    .wp-block-navigation-item.current-menu-item .wp-block-navigation-item__content {
        background: rgba(255, 255, 255, 0.2);
        color: #ffffff !important;
        font-weight: 600;
    }

    /* Separator between nav items */
    .wp-block-navigation-item:not(:last-child)::after {
        content: '';
        position: absolute;
        right: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 1px;
        height: 60%;
        background: rgba(255, 255, 255, 0.2);
    }

    /* Profile Settings special styling */
    .menu-item-profile-settings .wp-block-navigation-item__content {
        background: rgba(34, 197, 94, 0.8);
        color: #ffffff !important;
        font-weight: 600;
    }

    .menu-item-profile-settings .wp-block-navigation-item__content:hover {
        background: rgba(34, 197, 94, 1);
        transform: translateY(-1px);
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .wp-block-navigation__container {
            flex-direction: column;
            gap: 0.5rem;
        }

        .wp-block-navigation-item:not(:last-child)::after {
            display: none;
        }

        .wp-block-navigation-item__content {
            width: 100%;
            text-align: center;
        }
    }

    /* Traditional navigation styling for PHP templates */
    .main-navigation {
        margin: 1rem 0;
    }

    .nav-container {
        background: linear-gradient(135deg, #3b82f6 0%, #1e3a8a 100%);
        padding: 1rem 2rem;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        position: relative;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .nav-menu {
        display: flex;
        align-items: center;
        list-style: none;
        margin: 0;
        padding: 0;
        gap: 0;
    }

    .nav-item {
        position: relative;
        display: flex;
        align-items: center;
    }

    .nav-item:not(:last-child)::after {
        content: '';
        position: absolute;
        right: -0.5rem;
        top: 50%;
        transform: translateY(-50%);
        width: 1px;
        height: 1.5rem;
        background: rgba(255, 255, 255, 0.3);
    }

    .nav-item a {
        color: white !important;
        text-decoration: none;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        transition: all 0.3s ease;
        font-weight: 500;
        text-transform: capitalize;
        display: block;
    }

    .nav-item a:hover {
        background: rgba(255, 255, 255, 0.1);
        transform: translateY(-1px);
    }

    .nav-item.active a {
        background: rgba(255, 255, 255, 0.2);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .nav-profile-section {
        position: absolute;
        bottom: -2.5rem;
        left: 0;
        padding-left: 1rem;
    }

    .profile-settings-btn {
        background: #22c55e;
        color: white !important;
        text-decoration: none;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-block;
    }

    .profile-settings-btn:hover {
        background: rgba(34, 197, 94, 1);
        transform: translateY(-1px);
        color: white !important;
    }

    /* Navigation separator line */
    .nav-separator {
        border: none;
        height: 2px;
        background: linear-gradient(90deg, #1e3a8a 0%, #3b82f6 50%, #1e3a8a 100%);
        margin: 1rem 0;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    /* Responsive design for traditional navigation */
    @media (max-width: 768px) {
        .nav-menu {
            flex-direction: column;
            gap: 0.5rem;
        }

        .nav-item:not(:last-child)::after {
            display: none;
        }

        .nav-item a {
            width: 100%;
            text-align: center;
        }

        .nav-profile-section {
            position: static;
            margin-top: 1rem;
            text-align: center;
        }
    }

    /* Site title styling */
    .wp-block-site-title a {
        color: #1e3a8a !important;
        font-size: 2rem;
        font-weight: 700;
        text-decoration: none;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .wp-block-site-title a:hover {
        color: #3b82f6 !important;
        text-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    /* Site branding centering */
    .site-branding {
        text-align: center;
        margin-bottom: 1rem;
    }

    .site-title {
        text-align: center;
        margin-bottom: 0.5rem;
    }

    .site-title a {
        color: #1e3a8a;
        font-size: 2rem;
        font-weight: 700;
        text-decoration: none;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .site-title a:hover {
        color: #3b82f6;
        text-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .site-description {
        text-align: center;
        color: #6b7280;
        font-size: 1.1rem;
        margin: 0;
        font-style: italic;
    }

    /* Header container styling */
    .wp-block-group:has(.wp-block-site-title) {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        border-bottom: 3px solid #1e3a8a;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    </style>
    <?php
}
add_action( 'wp_head', 'car_sell_shop_navigation_styles' );

/**
 * Register custom block for action hook content
 */
function car_sell_shop_register_custom_block() {
    register_block_type( 'car-sell-shop/custom-action-hook', array(
        'editor_script' => 'car-sell-shop-custom-block',
        'render_callback' => 'car_sell_shop_render_custom_block',
        'attributes' => array(
            'content' => array(
                'type' => 'string',
                'default' => ''
            )
        )
    ) );
}
add_action( 'init', 'car_sell_shop_register_custom_block' );

/**
 * Render callback for custom block
 */
function car_sell_shop_render_custom_block( $attributes ) {
    ob_start();
    ?>
    <div class="custom-action-hook-container">
        <?php do_action( 'car_sell_shop_after_custom_template_content' ); ?>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Enqueue block editor script
 */
function car_sell_shop_enqueue_block_editor_script() {
    wp_enqueue_script(
        'car-sell-shop-custom-block',
        get_template_directory_uri() . '/js/custom-block.js',
        array( 'wp-blocks', 'wp-element', 'wp-editor' ),
        '1.0.0',
        true
    );
}
add_action( 'enqueue_block_editor_assets', 'car_sell_shop_enqueue_block_editor_script' );

/**
 * Uncomment the line below to test email functionality on every page load
 * add_action( 'init', 'test_email_functionality' );
 */

/**
 * Set posts per page for student archive
 */
function car_sell_shop_student_posts_per_page( $query ) {
    if ( ! is_admin() && $query->is_main_query() && is_post_type_archive( 'student' ) ) {
        $query->set( 'posts_per_page', 4 );
        $query->set( 'orderby', 'title' );
        $query->set( 'order', 'ASC' );
    }
}
add_action( 'pre_get_posts', 'car_sell_shop_student_posts_per_page' );

/**
 * Redirect homepage to Students page
 */
function car_sell_shop_redirect_homepage_to_students() {
    if ( is_front_page() && ! is_admin() ) {
        wp_redirect( home_url( '/students/' ), 301 );
        exit;
    }
}
add_action( 'template_redirect', 'car_sell_shop_redirect_homepage_to_students' );


