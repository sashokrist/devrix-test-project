<?php
/**
 * Register the Students custom post type
 *
 * @package Students
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Students Post Type Class
 *
 * @since 1.0.0
 */
class Students_Post_Type {

    use Students_Meta_Box;

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'init', array( $this, 'register_post_type' ) );
        add_action( 'init', array( $this, 'register_taxonomies' ) );
        add_action( 'add_meta_boxes_student', array( $this, 'add_meta_boxes' ) );
        add_action( 'save_post', array( $this, 'save_meta_boxes' ) );
    }

    /**
     * Register the Students post type
     */
    public function register_post_type() {
        $labels = array(
            'name'                  => _x( 'Students', 'Post type general name', 'students' ),
            'singular_name'         => _x( 'Student', 'Post type singular name', 'students' ),
            'menu_name'             => _x( 'Students', 'Admin Menu text', 'students' ),
            'name_admin_bar'        => _x( 'Student', 'Add New on Toolbar', 'students' ),
            'add_new'               => __( 'Add New', 'students' ),
            'add_new_item'          => __( 'Add New Student', 'students' ),
            'new_item'              => __( 'New Student', 'students' ),
            'edit_item'             => __( 'Edit Student', 'students' ),
            'view_item'             => __( 'View Student', 'students' ),
            'all_items'             => __( 'All Students', 'students' ),
            'search_items'          => __( 'Search Students', 'students' ),
            'parent_item_colon'     => __( 'Parent Students:', 'students' ),
            'not_found'             => __( 'No students found.', 'students' ),
            'not_found_in_trash'    => __( 'No students found in Trash.', 'students' ),
            'featured_image'        => _x( 'Student Photo', 'Overrides the "Featured Image" phrase', 'students' ),
            'set_featured_image'    => _x( 'Set student photo', 'Overrides the "Set featured image" phrase', 'students' ),
            'remove_featured_image' => _x( 'Remove student photo', 'Overrides the "Remove featured image" phrase', 'students' ),
            'use_featured_image'    => _x( 'Use as student photo', 'Overrides the "Use as featured image" phrase', 'students' ),
            'archives'              => _x( 'Student archives', 'The post type archive label', 'students' ),
            'insert_into_item'      => _x( 'Insert into student', 'Overrides the "Insert into post" phrase', 'students' ),
            'uploaded_to_this_item' => _x( 'Uploaded to this student', 'Overrides the "Uploaded to this post" phrase', 'students' ),
            'filter_items_list'     => _x( 'Filter students list', 'Screen reader text for the filter links', 'students' ),
            'items_list_navigation' => _x( 'Students list navigation', 'Screen reader text for the pagination', 'students' ),
            'items_list'            => _x( 'Students list', 'Screen reader text for the items list', 'students' ),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => false, // Let admin class handle the menu
            'query_var'          => true, // Enable query var
            'rewrite'            => array( 'slug' => 'students' ), // Enable rewrite rules with 'students' slug
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 20,
            'menu_icon'          => 'dashicons-groups',
            'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
            'show_in_rest'       => true,
        );

        register_post_type( Students_Config::POST_TYPE, $args );
    }

    /**
     * Register taxonomies for Students
     */
    public function register_taxonomies() {
        // Register Course taxonomy
        $course_labels = array(
            'name'              => _x( 'Courses', 'taxonomy general name', 'students' ),
            'singular_name'     => _x( 'Course', 'taxonomy singular name', 'students' ),
            'search_items'      => __( 'Search Courses', 'students' ),
            'all_items'         => __( 'All Courses', 'students' ),
            'parent_item'       => __( 'Parent Course', 'students' ),
            'parent_item_colon' => __( 'Parent Course:', 'students' ),
            'edit_item'         => __( 'Edit Course', 'students' ),
            'update_item'       => __( 'Update Course', 'students' ),
            'add_new_item'      => __( 'Add New Course', 'students' ),
            'new_item_name'     => __( 'New Course Name', 'students' ),
            'menu_name'         => __( 'Courses', 'students' ),
        );

        $course_args = array(
            'hierarchical'      => true,
            'labels'            => $course_labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'course' ),
            'show_in_rest'      => true,
        );

        register_taxonomy( 'course', array( 'student' ), $course_args );

        // Register Grade Level taxonomy
        $grade_labels = array(
            'name'              => _x( 'Grade Levels', 'taxonomy general name', 'students' ),
            'singular_name'     => _x( 'Grade Level', 'taxonomy singular name', 'students' ),
            'search_items'      => __( 'Search Grade Levels', 'students' ),
            'all_items'         => __( 'All Grade Levels', 'students' ),
            'edit_item'         => __( 'Edit Grade Level', 'students' ),
            'update_item'       => __( 'Update Grade Level', 'students' ),
            'add_new_item'      => __( 'Add New Grade Level', 'students' ),
            'new_item_name'     => __( 'New Grade Level Name', 'students' ),
            'menu_name'         => __( 'Grade Levels', 'students' ),
        );

        $grade_args = array(
            'hierarchical'      => false,
            'labels'            => $grade_labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'grade-level' ),
            'show_in_rest'      => true,
        );

        register_taxonomy( 'grade_level', array( 'student' ), $grade_args );
    }

    /**
     * Add meta boxes for student information
     */
    public function add_meta_boxes() {
        add_meta_box(
            'student_details',
            __( 'Student Details', 'students' ),
            array( $this, 'student_details_meta_box' ),
            'student',
            'normal',
            'high'
        );
    }

    /**
     * Student details meta box callback
     *
     * @param WP_Post $post The post object
     */
    public function student_details_meta_box( $post ) {
        $this->render_meta_box_content( $post );
    }

    /**
     * Save meta box data
     *
     * @param int $post_id The post ID
     */
    public function save_meta_boxes( $post_id ) {
        $meta_field_names = Students_Config::get_meta_field_names();
        $this->save_meta_fields( $post_id, $meta_field_names );
    }
}
