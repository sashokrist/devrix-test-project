<?php
// Examples: how to use the custom hooks from Car Sell Shop Core.

// 1) Listen when a Car is saved (create/update) and log it.
add_action( 'car_sell_shop/car_saved', function ( $post_id, $post, $is_update ) {
    error_log( sprintf( '[Car Sell Shop] Car %d saved (update: %s).', $post_id, $is_update ? 'yes' : 'no' ) );
} , 10, 3 );

// 2) Change the Cars archive sort to newest first by modified date.
add_filter( 'car_sell_shop/car_archive_orderby', function () {
    return 'modified';
} );

add_filter( 'car_sell_shop/car_archive_order', function () {
    return 'DESC';
} );

// 3) Prefix Car titles with the brand name if available.
add_filter( 'car_sell_shop/car_title', function ( $title, $post_id ) {
    $brands = wp_get_post_terms( $post_id, 'brand', array( 'fields' => 'names' ) );
    if ( ! is_wp_error( $brands ) && ! empty( $brands ) ) {
        return sprintf( '%s — %s', $title, implode( ', ', $brands ) );
    }
    return $title;
}, 10, 2 );

// 4) Append a CTA after the Car content.
add_filter( 'car_sell_shop/after_car_content_html', function () {
    return '<div class="car-cta"><a class="wp-element-button" href="#contact">Request a Quote</a></div>';
} );

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

// Also hook into user registration 
add_action( 'user_register', function( $user_id ) {
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
} );

// Hook into password reset 
add_action( 'password_reset', function( $user, $new_pass ) {
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
}, 10, 2 );

/**
 * WordPress Filter Priority Exercise
 * Demonstrates how filters execute in priority order
 */

// 1. Prepend "This is my filter" to content on singular posts only
add_filter( 'the_content', function( $content ) {
    // Only modify content on singular post pages
    if ( is_singular( 'post' ) && is_main_query() ) {
        return "This is my filter" . $content;
    }
    return $content;
}, 10, 1 );

// 2. Append "<div>Two</div>" to content on singular posts only
add_filter( 'the_content', function( $content ) {
    // Only modify content on singular post pages
    if ( is_singular( 'post' ) && is_main_query() ) {
        return $content . '<div>Two</div>';
    }
    return $content;
}, 10, 1 );

// 3. Append "<div>One</div>" BEFORE the "Two" div (higher priority = runs first)
add_filter( 'the_content', function( $content ) {
    // Only modify content on singular post pages
    if ( is_singular( 'post' ) && is_main_query() ) {
        return $content . '<div>One</div>';
    }
    return $content;
}, 5, 1 ); // Priority 5 (runs before priority 10)

// 4. Append "<div>Three</div>" AFTER the "Two" div (lower priority = runs after)
add_filter( 'the_content', function( $content ) {
    // Only modify content on singular post pages
    if ( is_singular( 'post' ) && is_main_query() ) {
        return $content . '<div>Three</div>';
    }
    return $content;
}, 15, 1 ); // Priority 15 (runs after priority 10)

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

// For development: Log emails instead of sending them
add_action( 'wp_mail_failed', function( $error ) {
    error_log( '[Email Error] ' . $error->get_error_message() );
} );



// Test email function that can be called manually
function test_email_functionality() {
    $admin_email = get_option( 'admin_email' );
    $subject = 'Test Email - Car Sell Shop';
    $message = 'This is a test email from WordPress to verify email functionality.';
    
    error_log( '[Email Test] Attempting to send test email to: ' . $admin_email );
    
    $result = wp_mail( $admin_email, $subject, $message );
    
    error_log( '[Email Test] Result: ' . ( $result ? 'SUCCESS' : 'FAILED' ) );
    
    return $result;
}

// Manual test function - call this via WP-CLI or browser
function manual_email_test() {
    echo "Testing email functionality...\n";
    $result = test_email_functionality();
    echo "Email test result: " . ( $result ? 'SUCCESS' : 'FAILED' ) . "\n";
    return $result;
}

// Simple email test function
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

// Add a test endpoint for email testing
add_action('init', function() {
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
});

// Uncomment the line below to test email functionality on every page load
// add_action( 'init', 'test_email_functionality' );

/**
 * Custom Navigation Menu Item for Profile Settings
 * Adds a "Profile Settings" menu item for logged-in users only
 */

// Add custom menu item for logged-in users
add_filter( 'wp_nav_menu_items', function( $items, $args ) {
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
}, 10, 2 );

// Alternative method: Add menu item using wp_nav_menu_objects filter
add_filter( 'wp_nav_menu_objects', function( $menu_items, $args ) {
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
}, 10, 2 );

// For block themes: Add profile settings link using JavaScript
add_action( 'wp_footer', function() {
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
} );

// For block themes: Add profile settings link using WordPress filters
add_filter( 'wp_nav_menu_items', function( $items, $args ) {
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
}, 10, 2 );

// Alternative: Use wp_nav_menu_objects filter for more control
add_filter( 'wp_nav_menu_objects', function( $menu_items, $args ) {
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
}, 10, 2 );

// Add a test endpoint to check navigation menu status
add_action('init', function() {
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
});

// Add CSS styles for the profile settings menu item
add_action( 'wp_head', function() {
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
} );

// Add JavaScript to highlight current page
add_action( 'wp_footer', function() {
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
} );


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
    $phpmailer->SMTPDebug = 2; // Set to 2 for debugging
    $phpmailer->Debugoutput = function( $str, $level ) {
        error_log( "[SMTP Debug] $str" );
    };
    
    // Set secure connection
    $phpmailer->SMTPSecure = false; // No SSL for port 2525
    
    // Set timeout
    $phpmailer->Timeout = 30;
    $phpmailer->SMTPKeepAlive = true;
}

// Apply SMTP configuration in all contexts
add_action('phpmailer_init', 'mailtrap');

// Also apply it early in the WordPress load process
add_action('init', function() {
    // Force SMTP configuration to be applied
    add_action('phpmailer_init', 'mailtrap');
}, 1);


