<?php
/**
 * Car Templates Class
 *
 * @package Car_Sell_Shop_Core
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Car Templates Class
 */
class Car_Sell_Shop_Core_Templates {

    /**
     * Constructor
     */
    public function __construct() {
        add_filter( 'single_template', array( $this, 'load_single_car_template' ) );
        add_filter( 'archive_template', array( $this, 'load_archive_car_template' ) );
        add_filter( 'taxonomy_template', array( $this, 'load_taxonomy_templates' ) );
        add_filter( 'page_template', array( $this, 'load_page_templates' ) );
        add_filter( 'template_include', array( $this, 'force_brands_template' ), 999 );
        add_filter( 'theme_templates', array( $this, 'remove_brands_theme_template' ) );
        add_action( 'init', array( $this, 'force_brands_template_early' ) );
    }

    /**
     * Load single car template
     *
     * @param string $template Template path
     * @return string
     */
    public function load_single_car_template( $template ) {
        if ( is_singular( 'car' ) ) {
            // Force use of plugin template only
            $plugin_template = CAR_SELL_SHOP_CORE_PLUGIN_DIR . 'templates/single-car.php';
            if ( file_exists( $plugin_template ) ) {
                return $plugin_template;
            }
        }
        return $template;
    }

    /**
     * Load archive car template
     *
     * @param string $template Template path
     * @return string
     */
    public function load_archive_car_template( $template ) {
        if ( is_post_type_archive( 'car' ) ) {
            // Force use of plugin template only
            $plugin_template = CAR_SELL_SHOP_CORE_PLUGIN_DIR . 'templates/archive-car.php';
            if ( file_exists( $plugin_template ) ) {
                return $plugin_template;
            }
        }
        return $template;
    }

    /**
     * Load taxonomy templates
     *
     * @param string $template Template path
     * @return string
     */
    public function load_taxonomy_templates( $template ) {
        if ( is_tax( 'brand' ) ) {
            // Force use of plugin template only
            $plugin_template = CAR_SELL_SHOP_CORE_PLUGIN_DIR . 'templates/taxonomy-brand.php';
            if ( file_exists( $plugin_template ) ) {
                return $plugin_template;
            }
        }
        return $template;
    }

    /**
     * Load page templates
     *
     * @param string $template Template path
     * @return string
     */
    public function load_page_templates( $template ) {
        global $post;
        
        if ( $post && is_page() ) {
            // Check if this is a brands page by slug or title
            if ( $post->post_name === 'brands' || 
                 strpos( strtolower( $post->post_title ), 'brand' ) !== false ) {
                
                $plugin_template = CAR_SELL_SHOP_CORE_PLUGIN_DIR . 'templates/page-brands.php';
                if ( file_exists( $plugin_template ) ) {
                    return $plugin_template;
                }
            }
        }
        
        return $template;
    }

    /**
     * Force brands template to use plugin template
     *
     * @param string $template Template path
     * @return string
     */
    public function force_brands_template( $template ) {
        global $post;
        
        // Check if we're on a brands page
        if ( $post && is_page() && ( $post->post_name === 'brands' || strpos( strtolower( $post->post_title ), 'brand' ) !== false ) ) {
            
            // Force use of plugin template
            $plugin_template = CAR_SELL_SHOP_CORE_PLUGIN_DIR . 'templates/page-brands.php';
            if ( file_exists( $plugin_template ) ) {
                // Debug: Log that we're using the plugin template
                error_log( 'Car Sell Shop Core: Using plugin template for brands page: ' . $plugin_template );
                return $plugin_template;
            }
        }
        
        return $template;
    }

    /**
     * Remove brands theme template from template hierarchy
     *
     * @param array $templates Templates array
     * @return array
     */
    public function remove_brands_theme_template( $templates ) {
        global $post;
        
        // If we're on a brands page, remove any theme templates
        if ( $post && is_page() && ( $post->post_name === 'brands' || strpos( strtolower( $post->post_title ), 'brand' ) !== false ) ) {
            // Remove any theme templates that might interfere
            $templates = array_filter( $templates, function( $template ) {
                return strpos( $template, 'page-brands.html' ) === false;
            } );
        }
        
        return $templates;
    }

    /**
     * Force brands template early in the process
     */
    public function force_brands_template_early() {
        global $post;
        
        // Check if we're on a brands page
        if ( $post && is_page() && ( $post->post_name === 'brands' || strpos( strtolower( $post->post_title ), 'brand' ) !== false ) ) {
            
            // Force use of plugin template
            $plugin_template = CAR_SELL_SHOP_CORE_PLUGIN_DIR . 'templates/page-brands.php';
            if ( file_exists( $plugin_template ) ) {
                // Set a global variable to indicate we're using the plugin template
                global $car_sell_shop_brands_template;
                $car_sell_shop_brands_template = $plugin_template;
                
                // Add a filter to force the template
                add_filter( 'template_include', function( $template ) use ( $plugin_template ) {
                    return $plugin_template;
                }, 9999 );
            }
        }
    }
}
