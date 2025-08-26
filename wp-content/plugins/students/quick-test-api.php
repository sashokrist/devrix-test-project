<?php
/**
 * Quick API Test Script
 * This script handles authentication and API testing automatically
 */

// Load WordPress
require_once dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-load.php';

// Check if user is logged in
if ( ! is_user_logged_in() ) {
    echo "❌ Please log in to WordPress admin first: http://localhost/devrix-test-project/wp-admin/\n";
    exit;
}

// Check if user has admin privileges
if ( ! current_user_can( 'manage_options' ) ) {
    echo "❌ Administrator privileges required\n";
    exit;
}

// Get current user info
$current_user = wp_get_current_user();
echo "✅ Logged in as: " . $current_user->user_login . " (Administrator)\n\n";

// Create fresh nonce
$nonce = wp_create_nonce( 'wp_rest' );
echo "🔐 Fresh Nonce: " . $nonce . "\n\n";

// Test API endpoints
echo "🧪 Testing API Endpoints...\n";
echo "================================\n\n";

// 1. Test GET all students
echo "1️⃣ Testing GET all students...\n";
$response = wp_remote_get( home_url( '/?rest_route=/students/v1/students' ) );
if ( is_wp_error( $response ) ) {
    echo "❌ Error: " . $response->get_error_message() . "\n";
} else {
    $body = wp_remote_retrieve_body( $response );
    $data = json_decode( $body, true );
    echo "✅ Status: " . wp_remote_retrieve_response_code( $response ) . "\n";
    echo "📊 Found " . count( $data['data'] ) . " students\n\n";
}

// 2. Test POST create student
echo "2️⃣ Testing POST create student...\n";
$test_data = array(
    'title' => 'Quick Test Student ' . time(),
    'student_id' => 'QUICK_' . time(),
    'student_email' => 'quick.test@example.com',
    'student_is_active' => 'active'
);

$response = wp_remote_post( home_url( '/?rest_route=/students/v1/students' ), array(
    'headers' => array(
        'Content-Type' => 'application/json',
        'X-WP-Nonce' => $nonce
    ),
    'body' => json_encode( $test_data )
));

if ( is_wp_error( $response ) ) {
    echo "❌ Error: " . $response->get_error_message() . "\n";
} else {
    $body = wp_remote_retrieve_body( $response );
    $data = json_decode( $body, true );
    echo "✅ Status: " . wp_remote_retrieve_response_code( $response ) . "\n";
    
    if ( isset( $data['success'] ) && $data['success'] ) {
        $student_id = $data['data']['id'];
        echo "🎉 Student created with ID: " . $student_id . "\n";
        echo "📝 Title: " . $data['data']['title'] . "\n";
        
        // 3. Test PUT update student
        echo "\n3️⃣ Testing PUT update student (ID: $student_id)...\n";
        $update_data = array(
            'title' => 'Updated Quick Test Student',
            'student_is_active' => 'inactive'
        );
        
        $response = wp_remote_request( home_url( "/?rest_route=/students/v1/students/$student_id" ), array(
            'method' => 'PUT',
            'headers' => array(
                'Content-Type' => 'application/json',
                'X-WP-Nonce' => $nonce
            ),
            'body' => json_encode( $update_data )
        ));
        
        if ( is_wp_error( $response ) ) {
            echo "❌ Error: " . $response->get_error_message() . "\n";
        } else {
            $body = wp_remote_retrieve_body( $response );
            $data = json_decode( $body, true );
            echo "✅ Status: " . wp_remote_retrieve_response_code( $response ) . "\n";
            
            if ( isset( $data['success'] ) && $data['success'] ) {
                echo "🎉 Student updated successfully\n";
                echo "📝 New title: " . $data['data']['title'] . "\n";
                echo "📊 Status: " . $data['data']['meta']['student_is_active'] . "\n";
                
                // 4. Test DELETE student
                echo "\n4️⃣ Testing DELETE student (ID: $student_id)...\n";
                $response = wp_remote_request( home_url( "/?rest_route=/students/v1/students/$student_id" ), array(
                    'method' => 'DELETE',
                    'headers' => array(
                        'X-WP-Nonce' => $nonce
                    )
                ));
                
                if ( is_wp_error( $response ) ) {
                    echo "❌ Error: " . $response->get_error_message() . "\n";
                } else {
                    $body = wp_remote_retrieve_body( $response );
                    $data = json_decode( $body, true );
                    echo "✅ Status: " . wp_remote_retrieve_response_code( $response ) . "\n";
                    
                    if ( isset( $data['success'] ) && $data['success'] ) {
                        echo "🎉 Student deleted successfully\n";
                    } else {
                        echo "❌ Delete failed: " . $data['message'] . "\n";
                    }
                }
            } else {
                echo "❌ Update failed: " . $data['message'] . "\n";
            }
        }
    } else {
        echo "❌ Create failed: " . $data['message'] . "\n";
    }
}

echo "\n================================\n";
echo "🎯 API Testing Complete!\n";
echo "📋 Nonce used: " . $nonce . "\n";
echo "⏰ Generated at: " . date( 'Y-m-d H:i:s' ) . "\n";
?>
