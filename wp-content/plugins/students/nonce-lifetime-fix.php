<?php
/**
 * Nonce Lifetime Configuration
 * 
 * This file shows different ways to handle nonce expiration:
 * 1. Global WordPress nonce lifetime change
 * 2. Custom nonce for REST API
 * 3. Auto-refresh nonce functionality
 */

// Option 1: Change global WordPress nonce lifetime to 10 minutes (600 seconds)
// Add this to your wp-config.php or theme's functions.php
if (!function_exists('custom_nonce_lifetime')) {
    function custom_nonce_lifetime() {
        return 600; // 10 minutes in seconds
    }
    add_filter('nonce_life', 'custom_nonce_lifetime');
}

// Option 2: Custom nonce for REST API with longer lifetime
if (!function_exists('create_custom_rest_nonce')) {
    function create_custom_rest_nonce($user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        
        // Create a custom nonce that expires in 10 minutes
        $expiry = time() + 600; // 10 minutes from now
        $nonce_data = array(
            'user_id' => $user_id,
            'expiry' => $expiry,
            'action' => 'wp_rest'
        );
        
        // Create a hash of the nonce data
        $nonce = wp_hash(serialize($nonce_data));
        
        // Store the nonce data in user meta (for verification)
        update_user_meta($user_id, '_custom_rest_nonce_' . $nonce, $nonce_data);
        
        return $nonce;
    }
}

// Option 3: Verify custom nonce
if (!function_exists('verify_custom_rest_nonce')) {
    function verify_custom_rest_nonce($nonce, $user_id = null) {
        if (!$user_id) {
            $user_id = get_current_user_id();
        }
        
        // Get stored nonce data
        $nonce_data = get_user_meta($user_id, '_custom_rest_nonce_' . $nonce, true);
        
        if (!$nonce_data) {
            return false;
        }
        
        // Check if nonce has expired
        if (time() > $nonce_data['expiry']) {
            // Clean up expired nonce
            delete_user_meta($user_id, '_custom_rest_nonce_' . $nonce);
            return false;
        }
        
        // Verify the nonce hash
        $expected_nonce = wp_hash(serialize($nonce_data));
        if ($nonce !== $expected_nonce) {
            return false;
        }
        
        return true;
    }
}

// Option 4: Clean up expired nonces (run periodically)
if (!function_exists('cleanup_expired_nonces')) {
    function cleanup_expired_nonces() {
        global $wpdb;
        
        $current_time = time();
        
        // Get all custom nonce meta
        $nonce_metas = $wpdb->get_results(
            "SELECT user_id, meta_key, meta_value 
             FROM {$wpdb->usermeta} 
             WHERE meta_key LIKE '_custom_rest_nonce_%'"
        );
        
        foreach ($nonce_metas as $meta) {
            $nonce_data = maybe_unserialize($meta->meta_value);
            
            if (is_array($nonce_data) && isset($nonce_data['expiry'])) {
                if ($current_time > $nonce_data['expiry']) {
                    delete_user_meta($meta->user_id, $meta->meta_key);
                }
            }
        }
    }
}

// Option 5: Add custom nonce endpoint to REST API
if (!function_exists('register_custom_nonce_endpoint')) {
    function register_custom_nonce_endpoint() {
        register_rest_route('students/v1', '/nonce', array(
            'methods' => 'GET',
            'callback' => 'get_custom_nonce',
            'permission_callback' => function() {
                return is_user_logged_in() && current_user_can('manage_options');
            }
        ));
    }
    add_action('rest_api_init', 'register_custom_nonce_endpoint');
}

function get_custom_nonce() {
    $nonce = create_custom_rest_nonce();
    return array(
        'nonce' => $nonce,
        'expires_in' => 600, // 10 minutes
        'expires_at' => date('Y-m-d H:i:s', time() + 600)
    );
}

// Option 6: Modify the existing REST API class to use custom nonce
// Add this to your class-students-rest-api.php file:

/*
// Replace the permission_callback in your REST API class
public function check_admin_permissions() {
    // Check if user is logged in
    if (!is_user_logged_in()) {
        return new WP_Error(
            'rest_forbidden',
            'Authentication required.',
            array('status' => 401)
        );
    }
    
    // Check if user has admin privileges
    if (!current_user_can('manage_options')) {
        return new WP_Error(
            'rest_forbidden',
            'Administrator privileges required.',
            array('status' => 403)
        );
    }
    
    // Check for custom nonce in header
    $nonce = $_SERVER['HTTP_X_WP_NONCE'] ?? '';
    if (!empty($nonce)) {
        if (verify_custom_rest_nonce($nonce)) {
            return true;
        }
    }
    
    // Fallback to WordPress default nonce
    return check_ajax_referer('wp_rest', 'X-WP-Nonce', false);
}
*/

// Instructions for implementation:
echo "=== NONCE LIFETIME CONFIGURATION ===\n\n";
echo "To implement 10-minute nonce expiration:\n\n";
echo "1. GLOBAL CHANGE (Recommended):\n";
echo "   Add this to your wp-config.php:\n";
echo "   define('NONCE_LIFE', 600);\n\n";
echo "2. OR add to your theme's functions.php:\n";
echo "   add_filter('nonce_life', function() { return 600; });\n\n";
echo "3. CUSTOM NONCE (Advanced):\n";
echo "   Use the functions above for custom nonce handling\n\n";
echo "4. REST API ENDPOINT:\n";
echo "   GET /?rest_route=/students/v1/nonce\n";
echo "   Returns: {\"nonce\": \"...\", \"expires_in\": 600, \"expires_at\": \"...\"}\n\n";
echo "5. UPDATE YOUR API CLASS:\n";
echo "   Replace permission_callback with custom nonce verification\n\n";
echo "=== END CONFIGURATION ===\n";
?>
