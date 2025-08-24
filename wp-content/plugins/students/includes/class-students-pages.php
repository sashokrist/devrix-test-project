<?php
/**
 * Students Pages Handler
 * 
 * Handles custom page routing for students, courses, and grade levels
 * that were moved from root directory to plugin
 *
 * @package Students
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Students Pages Class
 *
 * @since 1.0.0
 */
class Students_Pages {

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'init', array( $this, 'add_rewrite_rules' ) );
        add_action( 'template_redirect', array( $this, 'handle_custom_pages' ) );
        add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
        add_action( 'wp_loaded', array( $this, 'add_endpoints' ) );
        add_filter( 'template_include', array( $this, 'load_custom_templates' ) );
        add_action( 'init', array( $this, 'add_custom_endpoints' ) );
        add_action( 'init', array( $this, 'add_student_endpoints' ) );
    }

    /**
     * Add custom rewrite rules
     */
    public function add_rewrite_rules() {
        // Get rewrite rules from configuration
        $rewrite_rules = Students_Config::get_rewrite_rules();
        
        // Add each rewrite rule
        foreach ( $rewrite_rules as $pattern => $replacement ) {
            add_rewrite_rule( '^' . $pattern, $replacement, 'top' );
        }
    }

    /**
     * Add custom query variables
     */
    public function add_query_vars( $vars ) {
        $vars[] = 'students_page';
        $vars[] = 'course_page';
        $vars[] = 'grade_level_page';
        $vars[] = 'taxonomy_archive';
        $vars[] = 'taxonomy_type';
        $vars[] = 'taxonomy_term';
        return $vars;
    }

    /**
     * Add custom endpoints
     */
    public function add_endpoints() {
        add_rewrite_endpoint( 'taxonomy-archive', EP_ROOT );
    }

    /**
     * Add custom endpoints for students
     */
    public function add_custom_endpoints() {
        add_rewrite_endpoint( 'students', EP_ROOT );
    }

    /**
     * Add student endpoints
     */
    public function add_student_endpoints() {
        add_rewrite_endpoint( 'student', EP_ROOT );
    }

    /**
     * Load custom templates for students
     */
    public function load_custom_templates( $template ) {
        global $post, $wp_query;

        // Check if this is a student post type
        if ( is_singular( 'student' ) ) {
            $custom_template = STUDENTS_PLUGIN_DIR . 'templates/single-student.php';
            if ( file_exists( $custom_template ) ) {
                return $custom_template;
            }
        }

        // Check if this is a student archive
        if ( is_post_type_archive( 'student' ) ) {
            $custom_template = STUDENTS_PLUGIN_DIR . 'templates/archive-student.php';
            if ( file_exists( $custom_template ) ) {
                return $custom_template;
            }
        }

        // Check if this is a course taxonomy
        if ( is_tax( 'course' ) ) {
            $custom_template = STUDENTS_PLUGIN_DIR . 'templates/taxonomy-course.php';
            if ( file_exists( $custom_template ) ) {
                return $custom_template;
            }
        }

        // Check if this is a grade level taxonomy
        if ( is_tax( 'grade_level' ) ) {
            $custom_template = STUDENTS_PLUGIN_DIR . 'templates/taxonomy-grade_level.php';
            if ( file_exists( $custom_template ) ) {
                return $custom_template;
            }
        }

        return $template;
    }

    /**
     * Handle custom page requests
     */
    public function handle_custom_pages() {
        $request_uri = $_SERVER['REQUEST_URI'];
        
        // Remove the base directory from the path
        $home_path = parse_url(home_url(), PHP_URL_PATH);
        $relative_path = str_replace($home_path, '', $request_uri);
        $path_parts = explode('/', trim($relative_path, '/'));

        // Handle students pages
        if ( isset($path_parts[0]) && $path_parts[0] === 'students' ) {
            $this->handle_students_page($path_parts);
            return;
        }

        // Handle course pages
        if ( isset($path_parts[0]) && $path_parts[0] === 'course' ) {
            $this->handle_course_page($path_parts);
            return;
        }

        // Handle grade level pages
        if ( isset($path_parts[0]) && $path_parts[0] === 'grade-level' ) {
            $this->handle_grade_level_page($path_parts);
            return;
        }

        // Handle taxonomy archive pages
        if ( isset($path_parts[0]) && $path_parts[0] === 'taxonomy-archive' ) {
            $this->handle_taxonomy_archive_page($path_parts);
            return;
        }

        // Handle pagination pages
        if ( isset($path_parts[0]) && $path_parts[0] === 'page' ) {
            $this->handle_pagination_page($path_parts);
            return;
        }
    }

    /**
     * Handle students page requests
     */
    private function handle_students_page($path_parts) {
        if ( count($path_parts) === 1 ) {
            // Students archive page
            $this->load_students_archive();
        } else {
            // Individual student page
            $student_slug = $path_parts[1];
            $this->load_student_profile($student_slug);
        }
    }

    /**
     * Handle course page requests
     */
    private function handle_course_page($path_parts) {
        if ( count($path_parts) === 1 ) {
            // Course archive page
            $this->load_course_archive();
        } else {
            // Individual course page
            $course_slug = $path_parts[1];
            $this->load_course_page($course_slug);
        }
    }

    /**
     * Handle grade level page requests
     */
    private function handle_grade_level_page($path_parts) {
        if ( count($path_parts) === 1 ) {
            // Grade level archive page
            $this->load_grade_level_archive();
        } else {
            // Individual grade level page
            $grade_slug = $path_parts[1];
            $this->load_grade_level_page($grade_slug);
        }
    }

    /**
     * Load students archive page
     */
    private function load_students_archive() {
        // Set up query for students archive
        global $wp_query;
        $wp_query->set('post_type', 'student');
        $wp_query->set('posts_per_page', get_option('posts_per_page', 10));
        
        // Load the archive template
        $template = STUDENTS_PLUGIN_DIR . 'templates/archive-student.php';
        if ( file_exists($template) ) {
            include $template;
            exit;
        }
    }

    /**
     * Load individual student profile
     */
    private function load_student_profile($student_slug) {
        // Get student by slug
        $student = get_page_by_path($student_slug, OBJECT, 'student');
        
        if ( $student && $student->post_status === 'publish' ) {
            // Set up query for single student
            global $wp_query;
            $wp_query->set('post_type', 'student');
            $wp_query->set('p', $student->ID);
            $wp_query->set('name', $student_slug);
            
            // Load the single template
            $template = STUDENTS_PLUGIN_DIR . 'templates/single-student.php';
            if ( file_exists($template) ) {
                include $template;
                exit;
            }
        } else {
            // Student not found, redirect to archive
            wp_redirect(home_url('/students/'), 301);
            exit;
        }
    }

    /**
     * Load course archive page
     */
    private function load_course_archive() {
        // Set up query for course archive
        global $wp_query;
        $wp_query->set('taxonomy', 'course');
        
        // Load the taxonomy template
        $template = STUDENTS_PLUGIN_DIR . 'templates/taxonomy-course.php';
        if ( file_exists($template) ) {
            include $template;
            exit;
        }
    }

    /**
     * Load individual course page
     */
    private function load_course_page($course_slug) {
        // Get course term
        $course = get_term_by('slug', $course_slug, 'course');
        
        if ( $course && !is_wp_error($course) ) {
            // Set up query for course taxonomy
            global $wp_query;
            $wp_query->set('taxonomy', 'course');
            $wp_query->set('term', $course_slug);
            
            // Load the taxonomy template
            $template = STUDENTS_PLUGIN_DIR . 'templates/taxonomy-course.php';
            if ( file_exists($template) ) {
                include $template;
                exit;
            }
        } else {
            // Course not found, redirect to archive
            wp_redirect(home_url('/course/'), 301);
            exit;
        }
    }

    /**
     * Load grade level archive page
     */
    private function load_grade_level_archive() {
        // Set up query for grade level archive
        global $wp_query;
        $wp_query->set('taxonomy', 'grade_level');
        
        // Load the taxonomy template
        $template = STUDENTS_PLUGIN_DIR . 'templates/taxonomy-grade_level.php';
        if ( file_exists($template) ) {
            include $template;
            exit;
        }
    }

    /**
     * Load individual grade level page
     */
    private function load_grade_level_page($grade_slug) {
        // Get grade level term
        $grade = get_term_by('slug', $grade_slug, 'grade_level');
        
        if ( $grade && !is_wp_error($grade) ) {
            // Set up query for grade level taxonomy
            global $wp_query;
            $wp_query->set('taxonomy', 'grade_level');
            $wp_query->set('term', $grade_slug);
            
            // Load the taxonomy template
            $template = STUDENTS_PLUGIN_DIR . 'templates/taxonomy-grade_level.php';
            if ( file_exists($template) ) {
                include $template;
                exit;
            }
        } else {
            // Grade level not found, redirect to archive
            wp_redirect(home_url('/grade-level/'), 301);
            exit;
        }
    }

    /**
     * Handle pagination page requests
     */
    private function handle_pagination_page($path_parts) {
        if ( count($path_parts) >= 2 && is_numeric($path_parts[1]) ) {
            $page_number = intval($path_parts[1]);
            
            // Get query parameters
            $query_params = array();
            if ( isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING']) ) {
                parse_str($_SERVER['QUERY_STRING'], $query_params);
            }
            
            // Check if this is a post type archive pagination
            if ( isset($query_params['post_type']) ) {
                $post_type = $query_params['post_type'];
                
                if ( $post_type === 'student' ) {
                    // Redirect to students archive with pagination
                    $redirect_url = home_url('/?post_type=student&paged=' . $page_number);
                    wp_redirect($redirect_url, 301);
                    exit;
                } elseif ( $post_type === 'car' ) {
                    // Redirect to cars archive with pagination
                    $redirect_url = home_url('/?post_type=car&paged=' . $page_number);
                    wp_redirect($redirect_url, 301);
                    exit;
                }
            }
            
            // Default pagination redirect
            $redirect_url = home_url('/?paged=' . $page_number);
            
            // Add any other query parameters
            if ( !empty($query_params) ) {
                $redirect_url .= '&' . http_build_query($query_params);
            }
            
            wp_redirect($redirect_url, 301);
            exit;
        } else {
            // Invalid pagination, redirect to homepage
            wp_redirect(home_url('/'), 301);
            exit;
        }
    }

    /**
     * Handle taxonomy archive page requests
     */
    private function handle_taxonomy_archive_page($path_parts) {
        // Get query parameters
        $query_params = array();
        if ( isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING']) ) {
            parse_str($_SERVER['QUERY_STRING'], $query_params);
        }
        
        // Check if this is a taxonomy archive
        if ( isset($query_params['taxonomy']) ) {
            $taxonomy = $query_params['taxonomy'];
            
            if ( $taxonomy === 'course' ) {
                // Load course taxonomy template
                $this->load_course_archive();
                return;
            } elseif ( $taxonomy === 'grade_level' ) {
                // Load grade level taxonomy template
                $this->load_grade_level_archive();
                return;
            } elseif ( $taxonomy === 'brand' ) {
                // Handle brand taxonomy (for car post type)
                if ( isset($query_params['term']) ) {
                    $term_slug = $query_params['term'];
                    $this->load_brand_taxonomy_page($term_slug);
                } else {
                    $this->load_brand_archive();
                }
                return;
            }
        }
        
        // Default: load the taxonomy archive template
        $this->load_taxonomy_archive_template();
    }

    /**
     * Load brand taxonomy page
     */
    private function load_brand_taxonomy_page($term_slug) {
        // Get brand term
        $brand = get_term_by('slug', $term_slug, 'brand');
        
        if ( $brand && !is_wp_error($brand) ) {
            // Set up query for brand taxonomy
            global $wp_query;
            $wp_query->set('taxonomy', 'brand');
            $wp_query->set('term', $term_slug);
            
            // Load the taxonomy template
            $template = STUDENTS_PLUGIN_DIR . 'templates/taxonomy-brand.php';
            if ( file_exists($template) ) {
                include $template;
                exit;
            }
        }
        
        // Brand not found, redirect to homepage
        wp_redirect(home_url('/'), 301);
        exit;
    }

    /**
     * Load brand archive
     */
    private function load_brand_archive() {
        // Set up query for brand archive
        global $wp_query;
        $wp_query->set('taxonomy', 'brand');
        
        // Load the taxonomy template
        $template = STUDENTS_PLUGIN_DIR . 'templates/taxonomy-brand.php';
        if ( file_exists($template) ) {
            include $template;
            exit;
        }
        
        // Template not found, redirect to homepage
        wp_redirect(home_url('/'), 301);
        exit;
    }

    /**
     * Load taxonomy archive template
     */
    private function load_taxonomy_archive_template() {
        // Load the taxonomy archive template
        $template = STUDENTS_PLUGIN_DIR . 'page-taxonomy-archive.php';
        if ( file_exists($template) ) {
            include $template;
            exit;
        }
        
        // Template not found, redirect to homepage
        wp_redirect(home_url('/'), 301);
        exit;
    }
}
