<?php
/**
 * Car Sell Shop Theme Functions
 * 
 * Clean functions file without problematic output
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Enqueue theme styles and scripts
 */
function car_sell_shop_enqueue_scripts() {
    // Enqueue your theme styles and scripts here
    wp_enqueue_style( 'car-sell-shop-style', get_stylesheet_uri() );
}
add_action( 'wp_enqueue_scripts', 'car_sell_shop_enqueue_scripts' );

/**
 * Add theme support
 */
function car_sell_shop_theme_setup() {
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'custom-logo' );
    add_theme_support( 'html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ) );
    
    // Add support for block theme
    add_theme_support( 'wp-block-styles' );
    add_theme_support( 'align-wide' );
}
add_action( 'after_setup_theme', 'car_sell_shop_theme_setup' );

/**
 * Add custom post type support for students
 */
function car_sell_shop_add_student_support() {
    add_theme_support( 'post-thumbnails', array( 'student' ) );
}
add_action( 'init', 'car_sell_shop_add_student_support' );

/**
 * Ensure proper navigation functionality
 */
function car_sell_shop_navigation_setup() {
    // Register navigation menus if needed
    register_nav_menus( array(
        'primary' => __( 'Primary Menu', 'car-sell-shop' ),
    ) );
}
add_action( 'after_setup_theme', 'car_sell_shop_navigation_setup' );

/**
 * ACF Options Page - Header/Footer Visibility Control
 */
function car_sell_shop_acf_options_setup() {
    // Check if ACF is active and fully loaded
    if ( ! function_exists( 'acf_add_options_page' ) ) {
        return;
    }
    
    // Check if ACF is ready
    if ( ! class_exists( 'ACF' ) ) {
        return;
    }
    
    // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound
    acf_add_options_page( array(
        'page_title' => __( 'ACF Options', 'car-sell-shop' ),
        'menu_title' => __( 'ACF Options', 'car-sell-shop' ),
        'menu_slug'  => 'acf-options',
        'capability' => 'manage_options', // Changed back to manage_options for admin access
        'position'   => '59.3',
        'icon_url'   => 'dashicons-admin-generic',
    ) );
}

// Register ACF options page only after ACF is fully loaded
add_action( 'acf/init', 'car_sell_shop_acf_options_setup' );

/**
 * Check if header should be hidden based on ACF fields
 */
function car_sell_shop_should_hide_header() {
    // Only check ACF fields if ACF is loaded
    if ( ! function_exists( 'get_field' ) || ! class_exists( 'ACF' ) ) {
        return false;
    }
    
    // First check current page/post
    $current_id = get_queried_object_id();
    
    // If we're on a specific page/post, check its ACF fields
    if ( $current_id && $current_id > 0 ) {
        $hide_header = get_field( 'hide_header', $current_id );
        if ( $hide_header !== false && ! empty( $hide_header ) ) {
            return in_array( 'hide header', $hide_header );
        }
    }
    
    // Also check if we're on a specific page by URL
    if ( is_page() ) {
        global $post;
        if ( $post && $post->ID ) {
            $hide_header = get_field( 'hide_header', $post->ID );
            if ( $hide_header !== false && ! empty( $hide_header ) ) {
                return in_array( 'hide header', $hide_header );
            }
        }
    }
    
    // Fallback to options
    $hide_header = get_field( 'hide_header', 'option' );
    return $hide_header && in_array( 'hide header', $hide_header );
}

/**
 * Check if footer should be hidden based on ACF fields
 */
function car_sell_shop_should_hide_footer() {
    // Only check ACF fields if ACF is loaded
    if ( ! function_exists( 'get_field' ) || ! class_exists( 'ACF' ) ) {
        return false;
    }
    
    // First check current page/post
    $current_id = get_queried_object_id();
    
    // If we're on a specific page/post, check its ACF fields
    if ( $current_id && $current_id > 0 ) {
        $hide_footer = get_field( 'hide_footer', $current_id );
        if ( $hide_footer !== false && ! empty( $hide_footer ) ) {
            return in_array( 'hide footer', $hide_footer );
        }
    }
    
    // Also check if we're on a specific page by URL
    if ( is_page() ) {
        global $post;
        if ( $post && $post->ID ) {
            $hide_footer = get_field( 'hide_footer', $post->ID );
            if ( $hide_footer !== false && ! empty( $hide_footer ) ) {
                return in_array( 'hide footer', $hide_footer );
            }
        }
    }
    
    // Fallback to options
    $hide_footer = get_field( 'hide_footer', 'option' );
    return $hide_footer && in_array( 'hide footer', $hide_footer );
}

/**
 * Ensure ACF options have correct autoload setting
 */
function car_sell_shop_fix_acf_autoload() {
    global $wpdb;
    
    // Fix autoload for ACF options
    $acf_options = array(
        'options_hide_header',
        'options_hide_footer',
        '_options_hide_header',
        '_options_hide_footer'
    );
    
    foreach ( $acf_options as $option_name ) {
        $wpdb->update(
            $wpdb->options,
            array( 'autoload' => 'yes' ),
            array( 'option_name' => $option_name ),
            array( '%s' ),
            array( '%s' )
        );
    }
}
add_action( 'init', 'car_sell_shop_fix_acf_autoload' );

/**
 * Fix WPForms chmod permission warnings and ACF translation loading errors
 */
function car_sell_shop_fix_wpforms_permissions() {
    // Add error handler to suppress chmod warnings and ACF translation errors
    set_error_handler( function( $error_level, $error_message, $error_file, $error_line ) {
        // Suppress chmod warnings
        if ( strpos( $error_message, 'chmod(): Operation not permitted' ) !== false ) {
            return true;
        }
        
        // Suppress ACF translation loading errors (WordPress 6.7.0+ issue)
        if ( strpos( $error_message, '_load_textdomain_just_in_time was called incorrectly' ) !== false && 
             strpos( $error_message, 'acf domain was triggered too early' ) !== false ) {
            return true;
        }
        
        // Suppress ALL ACF translation loading errors (multiple patterns)
        if ( strpos( $error_message, '_load_textdomain_just_in_time was called incorrectly' ) !== false ) {
            return true;
        }
        
        // Suppress any ACF domain related errors
        if ( strpos( $error_message, 'acf domain' ) !== false ) {
            return true;
        }
        
        return false; // Don't suppress other errors
    } );
}

// Run this after plugins are loaded to avoid early loading issues
add_action( 'init', 'car_sell_shop_fix_wpforms_permissions' );

/**
 * Clean ACF field value debugging - works with any ACF field save
 */
function car_sell_shop_debug_acf_clean() {
    // Only run if ACF is fully loaded
    if ( ! function_exists( 'get_field' ) || ! class_exists( 'ACF' ) ) {
        return;
    }
    
    // Check if this is any ACF save action
    if ( isset( $_POST['action'] ) && $_POST['action'] === 'acf/save_post' ) {
        
        $post_id = isset( $_POST['_acf_post_id'] ) ? $_POST['_acf_post_id'] : ( isset( $_POST['post_ID'] ) ? $_POST['post_ID'] : 'unknown' );
        
        error_log( '=== ACF SAVE DETECTED ===' );
        error_log( 'Post ID: ' . $post_id );
        error_log( 'POST data: ' . print_r( $_POST, true ) );
        
        // Get current values before save (for both options and regular posts)
        $hide_header_before = get_field( 'hide_header', $post_id );
        $hide_footer_before = get_field( 'hide_footer', $post_id );
        
        error_log( 'Current header value: ' . print_r( $hide_header_before, true ) );
        error_log( 'Current footer value: ' . print_r( $hide_footer_before, true ) );
        
        // Hook into after save to compare values
        add_action( 'acf/save_post', function( $saved_post_id ) use ( $hide_header_before, $hide_footer_before, $post_id ) {
            // Only run if ACF is still loaded
            if ( ! function_exists( 'get_field' ) || ! class_exists( 'ACF' ) ) {
                return;
            }
            
            // Check if this is the same post we're monitoring
            if ( $saved_post_id == $post_id ) {
                $hide_header_after = get_field( 'hide_header', $saved_post_id );
                $hide_footer_after = get_field( 'hide_footer', $saved_post_id );
                
                error_log( 'New header value: ' . print_r( $hide_header_after, true ) );
                error_log( 'New footer value: ' . print_r( $hide_footer_after, true ) );
                
                // Only log if values actually changed
                if ( $hide_header_before !== $hide_header_after ) {
                    error_log( 'Header value was: ' . print_r( $hide_header_before, true ) . ' and was changed to: ' . print_r( $hide_header_after, true ) );
                }
                
                if ( $hide_footer_before !== $hide_footer_after ) {
                    error_log( 'Footer value was: ' . print_r( $hide_footer_before, true ) . ' and was changed to: ' . print_r( $hide_footer_after, true ) );
                }
                
                error_log( '=== ACF SAVE COMPLETED ===' );
            }
        }, 20 );
    }
}
add_action( 'admin_init', 'car_sell_shop_debug_acf_clean', 1 );

/**
 * Monitor ACF field updates directly
 */
function car_sell_shop_debug_acf_field_updates( $value, $post_id, $field ) {
    // Only run if ACF is fully loaded
    if ( ! function_exists( 'get_field' ) || ! class_exists( 'ACF' ) ) {
        return $value;
    }
    
    // Only monitor hide_header and hide_footer fields
    if ( $field['name'] === 'hide_header' || $field['name'] === 'hide_footer' ) {
        error_log( '=== ACF FIELD UPDATE ===' );
        error_log( 'Field: ' . $field['name'] );
        error_log( 'Post ID: ' . $post_id );
        error_log( 'New value: ' . print_r( $value, true ) );
        error_log( '=== ACF FIELD UPDATE END ===' );
    }
    
    return $value;
}
// add_filter( 'acf/update_value', 'car_sell_shop_debug_acf_field_updates', 10, 3 );

/**
 * Comprehensive form submission tracing - filtered to avoid spam
 */
function car_sell_shop_trace_form_submissions() {
    // Only run in admin
    if ( ! is_admin() ) {
        return;
    }
    
    // Check if this is a POST request
    if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
        $action = isset( $_POST['action'] ) ? $_POST['action'] : '';
        
        // Filter out spam actions
        $excluded_actions = array(
            'cdp_action_handling',  // Task management plugin
            'heartbeat',           // WordPress heartbeat
            'autosave',            // WordPress autosave
            'wp_rest_autosave',    // Gutenberg autosave
            'update-plugin',       // Plugin updates
            'update-theme',        // Theme updates
        );
        
        // Only trace if it's not an excluded action
        if ( ! in_array( $action, $excluded_actions ) ) {
            error_log( '=== FORM SUBMISSION TRACE START ===' );
            error_log( 'Time: ' . date('Y-m-d H:i:s') );
            error_log( 'URL: ' . $_SERVER['REQUEST_URI'] );
            error_log( 'Action: ' . $action );
            error_log( 'Post ID: ' . ( isset( $_POST['post_ID'] ) ? $_POST['post_ID'] : 'No post ID' ) );
            
            // Log POST data (but limit it to avoid spam)
            if ( ! empty( $_POST ) && ( strpos( $action, 'acf' ) !== false || strpos( $action, 'save' ) !== false || isset( $_POST['acf'] ) ) ) {
                error_log( '=== POST DATA ===' );
                foreach ( $_POST as $key => $value ) {
                    if ( is_array( $value ) ) {
                        error_log( 'POST[' . $key . ']: ' . print_r( $value, true ) );
                    } else {
                        error_log( 'POST[' . $key . ']: ' . $value );
                    }
                }
            }
            
            // Specifically look for ACF data
            if ( isset( $_POST['acf'] ) ) {
                error_log( '=== ACF DATA FOUND ===' );
                error_log( 'ACF data: ' . print_r( $_POST['acf'], true ) );
                
                // Look for hide_header and hide_footer specifically
                if ( isset( $_POST['acf']['hide_header'] ) ) {
                    error_log( 'ACF hide_header value: ' . print_r( $_POST['acf']['hide_header'], true ) );
                }
                if ( isset( $_POST['acf']['hide_footer'] ) ) {
                    error_log( 'ACF hide_footer value: ' . print_r( $_POST['acf']['hide_footer'], true ) );
                }
            }
            
            error_log( '=== FORM SUBMISSION TRACE END ===' );
        }
    }
}
// add_action( 'admin_init', 'car_sell_shop_trace_form_submissions', 1 );

/**
 * Trace WordPress post save - only for pages with ACF fields
 */
function car_sell_shop_trace_post_save( $post_id, $post, $update ) {
    // Only trace if ACF is loaded
    if ( ! function_exists( 'get_field' ) || ! class_exists( 'ACF' ) ) {
        return;
    }
    
    // Check if this post has ACF fields we care about
    $hide_header = get_field( 'hide_header', $post_id );
    $hide_footer = get_field( 'hide_footer', $post_id );
    
    // Only log if there are ACF fields or if it's a page/post we're monitoring
    if ( $hide_header !== false || $hide_footer !== false || $post->post_type === 'page' ) {
        error_log( '=== POST SAVE TRACE ===' );
        error_log( 'Post ID: ' . $post_id );
        error_log( 'Post Type: ' . $post->post_type );
        error_log( 'Post Status: ' . $post->post_status );
        error_log( 'Is Update: ' . ( $update ? 'Yes' : 'No' ) );
        
        if ( $hide_header !== false ) {
            error_log( 'ACF hide_header after save: ' . print_r( $hide_header, true ) );
        }
        if ( $hide_footer !== false ) {
            error_log( 'ACF hide_footer after save: ' . print_r( $hide_footer, true ) );
        }
        
        error_log( '=== POST SAVE TRACE END ===' );
    }
}
// add_action( 'save_post', 'car_sell_shop_trace_post_save', 10, 3 );

/**
 * Simple ACF save tracing - only shows ACF field changes
 */
function car_sell_shop_simple_acf_trace( $post_id ) {
    // Only run if ACF is loaded
    if ( ! function_exists( 'get_field' ) || ! class_exists( 'ACF' ) ) {
        return;
    }
    
    // Only trace pages and posts
    $post_type = get_post_type( $post_id );
    if ( ! in_array( $post_type, array( 'page', 'post' ) ) ) {
        return;
    }
    
    // Check for ACF fields we care about
    $hide_header = get_field( 'hide_header', $post_id );
    $hide_footer = get_field( 'hide_footer', $post_id );
    
    // Only log if we have ACF fields
    if ( $hide_header !== false || $hide_footer !== false ) {
        error_log( '=== ACF SAVE: Post ' . $post_id . ' (' . $post_type . ') ===' );
        if ( $hide_header !== false ) {
            error_log( 'Header: ' . print_r( $hide_header, true ) );
        }
        if ( $hide_footer !== false ) {
            error_log( 'Footer: ' . print_r( $hide_footer, true ) );
        }
        error_log( '=== ACF SAVE END ===' );
    }
}
add_action( 'acf/save_post', 'car_sell_shop_simple_acf_trace', 20 );

/**
 * Trace AJAX requests - filtered to avoid spam
 */
function car_sell_shop_trace_ajax_requests() {
    // Check if this is an AJAX request
    if ( wp_doing_ajax() ) {
        // Filter out repetitive/spam AJAX calls
        $action = isset( $_POST['action'] ) ? $_POST['action'] : '';
        $excluded_actions = array(
            'cdp_action_handling',  // Task management plugin
            'heartbeat',           // WordPress heartbeat
            'autosave',            // WordPress autosave
            'wp_rest_autosave',    // Gutenberg autosave
            'update-plugin',       // Plugin updates
            'update-theme',        // Theme updates
            'wp_ajax_nopriv_',     // Non-logged in AJAX
        );
        
        // Only log if it's not an excluded action
        if ( ! in_array( $action, $excluded_actions ) && ! strpos( $action, 'wp_ajax_nopriv_' ) === 0 ) {
            error_log( '=== AJAX REQUEST TRACE ===' );
            error_log( 'Time: ' . date('Y-m-d H:i:s') );
            error_log( 'Action: ' . $action );
            error_log( 'Request URI: ' . $_SERVER['REQUEST_URI'] );
            
            // Log POST data for AJAX (only if relevant)
            if ( ! empty( $_POST ) && ( strpos( $action, 'acf' ) !== false || strpos( $action, 'save' ) !== false ) ) {
                error_log( 'AJAX POST data: ' . print_r( $_POST, true ) );
            }
            
            error_log( '=== AJAX REQUEST TRACE END ===' );
        }
    }
}
// add_action( 'admin_init', 'car_sell_shop_trace_ajax_requests', 1 );

/**
 * Trace REST API requests (Gutenberg uses REST API) - DISABLED
 */
function car_sell_shop_trace_rest_requests( $response, $handler, $request ) {
    // Disabled to reduce log noise
    return $response;
}
add_filter( 'rest_pre_dispatch', 'car_sell_shop_trace_rest_requests', 10, 3 );

/**
 * Trace Gutenberg save process specifically - DISABLED
 */
function car_sell_shop_trace_gutenberg_save() {
    // Disabled to reduce log noise
}
add_action( 'rest_api_init', 'car_sell_shop_trace_gutenberg_save' );

/**
 * Trace the specific WordPress save functions - DISABLED
 */
function car_sell_shop_trace_save_functions() {
    // Disabled to reduce log noise
}

// Hook into various save functions
add_action( 'wp_insert_post', 'car_sell_shop_trace_save_functions', 1 );
add_action( 'wp_update_post', 'car_sell_shop_trace_save_functions', 1 );
add_action( 'save_post', 'car_sell_shop_trace_save_functions', 1 );
add_action( 'acf/save_post', 'car_sell_shop_trace_save_functions', 1 );

/**
 * Show current ACF values on admin page load
 */
function car_sell_shop_show_current_acf_values() {
    // Only run if ACF is fully loaded
    if ( ! function_exists( 'get_field' ) || ! class_exists( 'ACF' ) ) {
        return;
    }
    
    // Only on ACF options page
    if ( isset( $_GET['page'] ) && $_GET['page'] === 'acf-options' ) {
        $hide_header = get_field( 'hide_header', 'option' );
        $hide_footer = get_field( 'hide_footer', 'option' );
        
        error_log( '=== CURRENT ACF VALUES ===' );
        error_log( 'Header value: ' . print_r( $hide_header, true ) );
        error_log( 'Footer value: ' . print_r( $hide_footer, true ) );
        error_log( '=== CURRENT ACF VALUES END ===' );
    }
}
add_action( 'admin_init', 'car_sell_shop_show_current_acf_values', 5 );

/**
 * Debug WordPress options update
 */
function car_sell_shop_debug_options_update( $option, $old_value, $value ) {
    if ( strpos( $option, 'options_hide_' ) === 0 ) {
        error_log( '=== WORDPRESS OPTION UPDATE ===' );
        error_log( 'Option: ' . $option );
        error_log( 'Old value: ' . print_r( $old_value, true ) );
        error_log( 'New value: ' . print_r( $value, true ) );
        error_log( '=== WORDPRESS OPTION UPDATE END ===' );
    }
}
add_action( 'updated_option', 'car_sell_shop_debug_options_update', 10, 3 );



/**
 * Add CSS to hide header/footer when needed
 */
function car_sell_shop_hide_header_footer_css() {
    $hide_header = car_sell_shop_should_hide_header();
    $hide_footer = car_sell_shop_should_hide_footer();
    
    // Debug output
    if ( WP_DEBUG ) {
        echo '<!-- DEBUG: hide_header=' . ( $hide_header ? 'true' : 'false' ) . ', hide_footer=' . ( $hide_footer ? 'true' : 'false' ) . ' -->';
    }
    
    if ( $hide_header || $hide_footer ) {
        echo '<style type="text/css">';
        
        if ( $hide_header ) {
            echo '
            /* Hide Header - Traditional PHP templates */
            .site-header,
            header#masthead,
            .main-navigation {
                display: none !important;
            }
            
            /* Hide Header - Block theme parts */
            .wp-block-group:has(.main-navigation),
            .wp-block-group:has(.nav-container) {
                display: none !important;
            }
            ';
        }
        
        if ( $hide_footer ) {
            echo '
            /* Hide Footer - Traditional PHP templates */
            .site-footer,
            footer#colophon {
                display: none !important;
            }
            
            /* Hide Footer - Block theme parts */
            .wp-block-group:has(.has-text-align-center:contains("©")),
            .wp-block-group:has(p:contains("©")),
            .wp-block-group:has(.has-text-align-center) {
                display: none !important;
            }
            
            /* Hide any footer-like content */
            footer,
            .site-footer,
            #colophon,
            .wp-block-group:last-child {
                display: none !important;
            }
            ';
        }
        
        echo '</style>';
    }
}
add_action( 'wp_head', 'car_sell_shop_hide_header_footer_css' );

/**
 * Filter block theme parts to hide header/footer
 */
function car_sell_shop_filter_block_template_parts( $template_part, $area, $args ) {
    $hide_header = car_sell_shop_should_hide_header();
    $hide_footer = car_sell_shop_should_hide_footer();
    
    // Hide header template part
    if ( $area === 'header' && $hide_header ) {
        return '';
    }
    
    // Hide footer template part
    if ( $area === 'footer' && $hide_footer ) {
        return '';
    }
    
    return $template_part;
}
add_filter( 'get_block_template_part', 'car_sell_shop_filter_block_template_parts', 10, 3 );
