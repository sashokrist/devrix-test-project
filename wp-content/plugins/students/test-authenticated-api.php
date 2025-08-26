<?php
/**
 * Test script for Authenticated Students REST API
 * 
 * This script demonstrates how to use the authenticated endpoints
 * Run this from the WordPress admin context to test the API
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    // Load WordPress if not already loaded
    require_once dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-load.php';
}

// Test the authenticated REST API endpoints
class Students_Authenticated_API_Test {
    
    private $test_student_id;
    
    public function __construct() {
        // Check if user is logged in and has admin privileges
        if ( ! is_user_logged_in() ) {
            echo "<h2>❌ Authentication Required</h2>\n";
            echo "<p>Please log in as an administrator to test these endpoints.</p>\n";
            return;
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            echo "<h2>❌ Administrator Privileges Required</h2>\n";
            echo "<p>You need administrator privileges to test these endpoints.</p>\n";
            return;
        }

        echo "<h2>🔐 Students Authenticated REST API Test</h2>\n";
        echo "<p><strong>User:</strong> " . wp_get_current_user()->user_login . "</p>\n";
        echo "<p><strong>Role:</strong> " . implode( ', ', wp_get_current_user()->roles ) . "</p>\n";
        
        // Test the endpoints
        $this->test_create_student();
        $this->test_update_student();
        $this->test_delete_student();
    }
    
    public function test_create_student() {
        echo "<h3>1. Testing Create Student Endpoint</h3>\n";
        
        // Create test data
        $test_data = array(
            'title' => 'API Test Student',
            'content' => 'This student was created via REST API',
            'excerpt' => 'API test student',
            'student_id' => 'API_TEST_' . time(),
            'student_email' => 'api.test@example.com',
            'student_phone' => '+1234567890',
            'student_dob' => '2000-01-01',
            'student_address' => '123 API Street',
            'student_country' => 'Test Country',
            'student_city' => 'Test City',
            'student_class_grade' => 'Grade 10',
            'student_is_active' => 'active',
            'courses' => array( 'IT', 'Math' ),
            'grade_levels' => array( '10' ),
        );
        
        // Make the API call
        $response = wp_remote_post( rest_url( 'students/v1/students' ), array(
            'headers' => array(
                'Content-Type' => 'application/json',
                'X-WP-Nonce' => wp_create_nonce( 'wp_rest' ),
            ),
            'body' => json_encode( $test_data ),
        ) );
        
        if ( is_wp_error( $response ) ) {
            echo "❌ Error: " . $response->get_error_message() . "<br>\n";
            return;
        }
        
        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );
        $status = wp_remote_retrieve_response_code( $response );
        
        if ( $status === 201 && isset( $data['success'] ) && $data['success'] ) {
            echo "✅ Student created successfully!<br>\n";
            echo "&nbsp;&nbsp;• Student ID: " . $data['data']['id'] . "<br>\n";
            echo "&nbsp;&nbsp;• Title: " . $data['data']['title'] . "<br>\n";
            echo "&nbsp;&nbsp;• Student ID: " . $data['data']['meta']['student_id'] . "<br>\n";
            
            // Store the created student ID for update/delete tests
            $this->test_student_id = $data['data']['id'];
        } else {
            echo "❌ Failed to create student<br>\n";
            echo "&nbsp;&nbsp;• Status: " . $status . "<br>\n";
            echo "&nbsp;&nbsp;• Response: " . $body . "<br>\n";
        }
    }
    
    public function test_update_student() {
        if ( ! isset( $this->test_student_id ) ) {
            echo "<h3>2. Testing Update Student Endpoint</h3>\n";
            echo "⚠️ Skipped - No student created in previous test<br>\n";
            return;
        }
        
        echo "<h3>2. Testing Update Student Endpoint</h3>\n";
        
        // Update data
        $update_data = array(
            'title' => 'Updated API Test Student',
            'student_email' => 'updated.api.test@example.com',
            'student_phone' => '+0987654321',
            'student_is_active' => 'inactive',
        );
        
        // Make the API call
        $response = wp_remote_request( rest_url( 'students/v1/students/' . $this->test_student_id ), array(
            'method' => 'PUT',
            'headers' => array(
                'Content-Type' => 'application/json',
                'X-WP-Nonce' => wp_create_nonce( 'wp_rest' ),
            ),
            'body' => json_encode( $update_data ),
        ) );
        
        if ( is_wp_error( $response ) ) {
            echo "❌ Error: " . $response->get_error_message() . "<br>\n";
            return;
        }
        
        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );
        $status = wp_remote_retrieve_response_code( $response );
        
        if ( $status === 200 && isset( $data['success'] ) && $data['success'] ) {
            echo "✅ Student updated successfully!<br>\n";
            echo "&nbsp;&nbsp;• New Title: " . $data['data']['title'] . "<br>\n";
            echo "&nbsp;&nbsp;• New Email: " . $data['data']['meta']['student_email'] . "<br>\n";
            echo "&nbsp;&nbsp;• New Status: " . $data['data']['meta']['student_is_active'] . "<br>\n";
        } else {
            echo "❌ Failed to update student<br>\n";
            echo "&nbsp;&nbsp;• Status: " . $status . "<br>\n";
            echo "&nbsp;&nbsp;• Response: " . $body . "<br>\n";
        }
    }
    
    public function test_delete_student() {
        if ( ! isset( $this->test_student_id ) ) {
            echo "<h3>3. Testing Delete Student Endpoint</h3>\n";
            echo "⚠️ Skipped - No student created in previous test<br>\n";
            return;
        }
        
        echo "<h3>3. Testing Delete Student Endpoint</h3>\n";
        
        // Make the API call to move to trash
        $response = wp_remote_request( rest_url( 'students/v1/students/' . $this->test_student_id ), array(
            'method' => 'DELETE',
            'headers' => array(
                'X-WP-Nonce' => wp_create_nonce( 'wp_rest' ),
            ),
        ) );
        
        if ( is_wp_error( $response ) ) {
            echo "❌ Error: " . $response->get_error_message() . "<br>\n";
            return;
        }
        
        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );
        $status = wp_remote_retrieve_response_code( $response );
        
        if ( $status === 200 && isset( $data['success'] ) && $data['success'] ) {
            echo "✅ Student deleted successfully!<br>\n";
            echo "&nbsp;&nbsp;• Message: " . $data['message'] . "<br>\n";
            echo "&nbsp;&nbsp;• Student ID: " . $data['data']['id'] . "<br>\n";
        } else {
            echo "❌ Failed to delete student<br>\n";
            echo "&nbsp;&nbsp;• Status: " . $status . "<br>\n";
            echo "&nbsp;&nbsp;• Response: " . $body . "<br>\n";
        }
        
        // Test permanent deletion
        echo "<h4>Testing Permanent Deletion</h4>\n";
        
        $response = wp_remote_request( rest_url( 'students/v1/students/' . $this->test_student_id . '?force=true' ), array(
            'method' => 'DELETE',
            'headers' => array(
                'X-WP-Nonce' => wp_create_nonce( 'wp_rest' ),
            ),
        ) );
        
        if ( is_wp_error( $response ) ) {
            echo "❌ Error: " . $response->get_error_message() . "<br>\n";
            return;
        }
        
        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );
        $status = wp_remote_retrieve_response_code( $response );
        
        if ( $status === 200 && isset( $data['success'] ) && $data['success'] ) {
            echo "✅ Student permanently deleted!<br>\n";
            echo "&nbsp;&nbsp;• Message: " . $data['message'] . "<br>\n";
        } else {
            echo "❌ Failed to permanently delete student<br>\n";
            echo "&nbsp;&nbsp;• Status: " . $status . "<br>\n";
            echo "&nbsp;&nbsp;• Response: " . $body . "<br>\n";
        }
    }
}

// Run the test if this file is accessed directly
if ( basename( __FILE__ ) === basename( $_SERVER['SCRIPT_NAME'] ) ) {
    new Students_Authenticated_API_Test();
}
