<?php
/**
 * Test script for Students REST API
 * 
 * This script tests the REST API endpoints programmatically
 * Run this from the WordPress context to test the API
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    // Load WordPress if not already loaded
    require_once dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-load.php';
}

// Test the REST API class
class Students_REST_API_Test {
    
    private $rest_api;
    
    public function __construct() {
        // Initialize the REST API
        $this->rest_api = new Students_REST_API();
        
        // Test the endpoints
        $this->test_endpoints();
    }
    
    public function test_endpoints() {
        echo "<h2>Students REST API Test Results</h2>\n";
        
        // Test 1: Check if routes are registered
        $this->test_route_registration();
        
        // Test 2: Test get_students method
        $this->test_get_students();
        
        // Test 3: Test get_student method
        $this->test_get_student();
        
        // Test 4: Test data formatting
        $this->test_data_formatting();
    }
    
    private function test_route_registration() {
        echo "<h3>1. Testing Route Registration</h3>\n";
        
        // Check if the REST API class exists
        if ( class_exists( 'Students_REST_API' ) ) {
            echo "✅ Students_REST_API class exists<br>\n";
        } else {
            echo "❌ Students_REST_API class not found<br>\n";
            return;
        }
        
        // Check if routes are registered
        $routes = rest_get_server()->get_routes();
        $students_routes = array();
        
        foreach ( $routes as $route => $handlers ) {
            if ( strpos( $route, 'students/v1' ) !== false ) {
                $students_routes[] = $route;
            }
        }
        
        if ( ! empty( $students_routes ) ) {
            echo "✅ Students routes registered:<br>\n";
            foreach ( $students_routes as $route ) {
                echo "&nbsp;&nbsp;• {$route}<br>\n";
            }
        } else {
            echo "❌ No students routes found<br>\n";
        }
    }
    
    private function test_get_students() {
        echo "<h3>2. Testing Get Students Method</h3>\n";
        
        // Create a mock request
        $request = new WP_REST_Request( 'GET', '/students/v1/students' );
        $request->set_param( 'per_page', 5 );
        $request->set_param( 'page', 1 );
        
        // Call the method
        $response = $this->rest_api->get_students( $request );
        
        if ( is_wp_error( $response ) ) {
            echo "❌ Error in get_students: " . $response->get_error_message() . "<br>\n";
        } else {
            echo "✅ get_students method works<br>\n";
            $data = $response->get_data();
            echo "&nbsp;&nbsp;• Response status: " . $response->get_status() . "<br>\n";
            echo "&nbsp;&nbsp;• Students count: " . count( $data['data']['students'] ) . "<br>\n";
            echo "&nbsp;&nbsp;• Total posts: " . $data['data']['pagination']['total_posts'] . "<br>\n";
        }
    }
    
    private function test_get_student() {
        echo "<h3>3. Testing Get Student Method</h3>\n";
        
        // Get a sample student ID
        $students = get_posts( array(
            'post_type' => 'student',
            'post_status' => 'publish',
            'numberposts' => 1,
        ) );
        
        if ( empty( $students ) ) {
            echo "⚠️ No students found in database<br>\n";
            return;
        }
        
        $student_id = $students[0]->ID;
        
        // Create a mock request
        $request = new WP_REST_Request( 'GET', "/students/v1/students/{$student_id}" );
        $request->set_param( 'id', $student_id );
        
        // Call the method
        $response = $this->rest_api->get_student( $request );
        
        if ( is_wp_error( $response ) ) {
            echo "❌ Error in get_student: " . $response->get_error_message() . "<br>\n";
        } else {
            echo "✅ get_student method works<br>\n";
            $data = $response->get_data();
            echo "&nbsp;&nbsp;• Student ID: " . $data['id'] . "<br>\n";
            echo "&nbsp;&nbsp;• Student Title: " . $data['title'] . "<br>\n";
        }
        
        // Test with non-existent ID
        $request = new WP_REST_Request( 'GET', '/students/v1/students/999999' );
        $request->set_param( 'id', 999999 );
        
        $response = $this->rest_api->get_student( $request );
        
        if ( is_wp_error( $response ) ) {
            echo "✅ Error handling works for non-existent student<br>\n";
        } else {
            echo "❌ Should return error for non-existent student<br>\n";
        }
    }
    
    private function test_data_formatting() {
        echo "<h3>4. Testing Data Formatting</h3>\n";
        
        // Get a sample student
        $students = get_posts( array(
            'post_type' => 'student',
            'post_status' => 'publish',
            'numberposts' => 1,
        ) );
        
        if ( empty( $students ) ) {
            echo "⚠️ No students found for data formatting test<br>\n";
            return;
        }
        
        $student = $students[0];
        
        // Test the private method using reflection
        $reflection = new ReflectionClass( $this->rest_api );
        $method = $reflection->getMethod( 'format_student_data' );
        $method->setAccessible( true );
        
        $formatted_data = $method->invoke( $this->rest_api, $student );
        
        echo "✅ Data formatting works<br>\n";
        echo "&nbsp;&nbsp;• Formatted data keys: " . implode( ', ', array_keys( $formatted_data ) ) . "<br>\n";
        echo "&nbsp;&nbsp;• Meta fields: " . implode( ', ', array_keys( $formatted_data['meta'] ) ) . "<br>\n";
        echo "&nbsp;&nbsp;• Taxonomies: " . implode( ', ', array_keys( $formatted_data['taxonomies'] ) ) . "<br>\n";
    }
}

// Run the test if this file is accessed directly
if ( basename( __FILE__ ) === basename( $_SERVER['SCRIPT_NAME'] ) ) {
    new Students_REST_API_Test();
}
