<?php
/**
 * Students Sanitizer Class
 * 
 * Handles all sanitization and validation for the Students plugin
 *
 * @package Students
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Students_Sanitizer Class
 *
 * @since 1.0.0
 */
class Students_Sanitizer {

    /**
     * Sanitize country field
     *
     * @param string $country The country value to sanitize
     * @return string Sanitized country value
     */
    public static function sanitize_country( $country ) {
        if ( ! is_string( $country ) ) {
            return '';
        }
        
        // Remove any non-alphabetic characters except spaces, hyphens, dots, and apostrophes
        $country = preg_replace( '/[^a-zA-Z\s\-\.\']/', '', $country );
        
        // Trim whitespace
        $country = trim( $country );
        
        // Limit length
        if ( strlen( $country ) > 100 ) {
            $country = substr( $country, 0, 100 );
        }
        
        return $country;
    }

    /**
     * Sanitize city field
     *
     * @param string $city The city value to sanitize
     * @return string Sanitized city value
     */
    public static function sanitize_city( $city ) {
        if ( ! is_string( $city ) ) {
            return '';
        }
        
        // Remove any non-alphabetic characters except spaces, hyphens, dots, and apostrophes
        $city = preg_replace( '/[^a-zA-Z\s\-\.\']/', '', $city );
        
        // Trim whitespace
        $city = trim( $city );
        
        // Limit length
        if ( strlen( $city ) > 100 ) {
            $city = substr( $city, 0, 100 );
        }
        
        return $city;
    }

    /**
     * Sanitize class/grade field
     *
     * @param string $class_grade The class/grade value to sanitize
     * @return string Sanitized class/grade value
     */
    public static function sanitize_class_grade( $class_grade ) {
        if ( ! is_string( $class_grade ) ) {
            return '';
        }
        
        // Allow letters, numbers, spaces, hyphens, dots, and apostrophes
        $class_grade = preg_replace( '/[^a-zA-Z0-9\s\-\.\']/', '', $class_grade );
        
        // Trim whitespace
        $class_grade = trim( $class_grade );
        
        // Limit length
        if ( strlen( $class_grade ) > 50 ) {
            $class_grade = substr( $class_grade, 0, 50 );
        }
        
        return $class_grade;
    }

    /**
     * Sanitize and validate status field
     *
     * @param string $status The status value to sanitize
     * @return string Sanitized status value ('0' or '1')
     */
    public static function sanitize_status( $status ) {
        if ( ! is_string( $status ) ) {
            return '0';
        }
        
        // Only allow '0' or '1'
        if ( in_array( $status, array( '0', '1' ), true ) ) {
            return $status;
        }
        
        // Default to inactive if invalid
        return '0';
    }

    /**
     * Sanitize email field
     *
     * @param string $email The email value to sanitize
     * @return string Sanitized email value
     */
    public static function sanitize_email( $email ) {
        if ( ! is_string( $email ) ) {
            return '';
        }
        
        // Use WordPress built-in email sanitization
        $email = sanitize_email( $email );
        
        // Validate email format
        if ( ! is_email( $email ) ) {
            return '';
        }
        
        return $email;
    }

    /**
     * Sanitize phone field
     *
     * @param string $phone The phone value to sanitize
     * @return string Sanitized phone value
     */
    public static function sanitize_phone( $phone ) {
        if ( ! is_string( $phone ) ) {
            return '';
        }
        
        // Allow digits, spaces, hyphens, parentheses, and plus signs
        $phone = preg_replace( '/[^0-9\s\-\(\)\+]/', '', $phone );
        
        // Trim whitespace
        $phone = trim( $phone );
        
        // Limit length
        if ( strlen( $phone ) > 20 ) {
            $phone = substr( $phone, 0, 20 );
        }
        
        return $phone;
    }

    /**
     * Sanitize date of birth field
     *
     * @param string $dob The date of birth value to sanitize
     * @return string Sanitized date value
     */
    public static function sanitize_date( $dob ) {
        if ( ! is_string( $dob ) ) {
            return '';
        }
        
        // Trim whitespace
        $dob = trim( $dob );
        
        // Try to parse the date
        $timestamp = strtotime( $dob );
        if ( $timestamp === false ) {
            return '';
        }
        
        // Format as Y-m-d for storage
        return date( 'Y-m-d', $timestamp );
    }

    /**
     * Sanitize address field
     *
     * @param string $address The address value to sanitize
     * @return string Sanitized address value
     */
    public static function sanitize_address( $address ) {
        if ( ! is_string( $address ) ) {
            return '';
        }
        
        // Allow letters, numbers, spaces, and common punctuation
        $address = preg_replace( '/[^a-zA-Z0-9\s\-\.\,\#\&]/', '', $address );
        
        // Trim whitespace
        $address = trim( $address );
        
        // Limit length
        if ( strlen( $address ) > 200 ) {
            $address = substr( $address, 0, 200 );
        }
        
        return $address;
    }

    /**
     * Sanitize student ID field
     *
     * @param string $student_id The student ID value to sanitize
     * @return string Sanitized student ID value
     */
    public static function sanitize_student_id( $student_id ) {
        if ( ! is_string( $student_id ) ) {
            return '';
        }
        
        // Allow letters, numbers, and hyphens
        $student_id = preg_replace( '/[^a-zA-Z0-9\-]/', '', $student_id );
        
        // Trim whitespace
        $student_id = trim( $student_id );
        
        // Limit length
        if ( strlen( $student_id ) > 50 ) {
            $student_id = substr( $student_id, 0, 50 );
        }
        
        return $student_id;
    }

    /**
     * Validate and sanitize meta data for display
     *
     * @param mixed $value The value to validate
     * @param string $type The type of field
     * @return mixed Sanitized value
     */
    public static function validate_for_display( $value, $type = 'text' ) {
        switch ( $type ) {
            case 'country':
                return self::sanitize_country( $value );
            case 'city':
                return self::sanitize_city( $value );
            case 'class_grade':
                return self::sanitize_class_grade( $value );
            case 'status':
                return self::sanitize_status( $value );
            case 'email':
                return self::sanitize_email( $value );
            case 'phone':
                return self::sanitize_phone( $value );
            case 'date':
                return self::sanitize_date( $value );
            case 'address':
                return self::sanitize_address( $value );
            case 'student_id':
                return self::sanitize_student_id( $value );
            default:
                return is_string( $value ) ? sanitize_text_field( $value ) : '';
        }
    }

    /**
     * Escape and display meta value safely
     *
     * @param mixed $value The value to escape
     * @param string $context The context (html, attr, url)
     * @return string Escaped value
     */
    public static function escape_for_display( $value, $context = 'html' ) {
        if ( ! is_string( $value ) ) {
            return '';
        }
        
        switch ( $context ) {
            case 'attr':
                return esc_attr( $value );
            case 'url':
                return esc_url( $value );
            case 'html':
            default:
                return esc_html( $value );
        }
    }
}
