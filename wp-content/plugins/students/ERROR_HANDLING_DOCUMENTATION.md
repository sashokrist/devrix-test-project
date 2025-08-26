# 🚨 Students API Error Handling Documentation

## Overview

The Students REST API includes comprehensive error handling to provide clear, actionable feedback when things go wrong. This document outlines all error scenarios and their corresponding responses.

## Error Response Format

All error responses follow this consistent format:

```json
{
  "code": "error_code",
  "message": "Human-readable error message",
  "data": {
    "status": 400,
    "message": "Detailed error description",
    "suggestion": "Actionable suggestion for fixing the error",
    "additional_fields": "Context-specific information"
  }
}
```

## HTTP Status Codes

- **200**: Success
- **400**: Bad Request (validation errors, invalid parameters)
- **401**: Unauthorized (authentication required)
- **403**: Forbidden (insufficient permissions, nonce invalid)
- **404**: Not Found (student doesn't exist)
- **409**: Conflict (duplicate student ID)
- **500**: Internal Server Error

## Error Scenarios

### 1. 📋 GET Students - Parameter Validation Errors

#### Invalid `per_page` Parameter
```bash
GET /?rest_route=/students/v1/students&per_page=150
```

**Response:**
```json
{
  "code": "rest_invalid_param",
  "message": "Invalid parameter(s): per_page",
  "data": {
    "status": 400,
    "params": {
      "per_page": "Invalid parameter."
    }
  }
}
```

#### Invalid `page` Parameter
```bash
GET /?rest_route=/students/v1/students&page=0
```

**Response:**
```json
{
  "code": "rest_invalid_param",
  "message": "Invalid parameter(s): page",
  "data": {
    "status": 400,
    "params": {
      "page": "Invalid parameter."
    }
  }
}
```

#### Invalid `status` Parameter
```bash
GET /?rest_route=/students/v1/students&status=invalid
```

**Response:**
```json
{
  "code": "rest_invalid_param",
  "message": "Invalid parameter(s): status",
  "data": {
    "status": 400,
    "params": {
      "status": "Invalid parameter."
    }
  }
}
```

### 2. 👤 GET Single Student - Not Found Errors

#### Non-existent Student
```bash
GET /?rest_route=/students/v1/students/99999
```

**Response:**
```json
{
  "code": "student_not_found",
  "message": "Student not found",
  "data": {
    "status": 404,
    "message": "No student found with ID 99999",
    "provided_id": "99999",
    "suggestion": "Please check the student ID or try listing all students first"
  }
}
```

#### Invalid Student ID Format
```bash
GET /?rest_route=/students/v1/students/abc
```

**Response:**
```json
{
  "code": "rest_no_route",
  "message": "No route was found matching the URL and request method.",
  "data": {
    "status": 404
  }
}
```

#### Student Exists But Wrong Post Type
```bash
GET /?rest_route=/students/v1/students/1
```

**Response:**
```json
{
  "code": "invalid_post_type",
  "message": "Invalid post type",
  "data": {
    "status": 400,
    "message": "Post ID 1 is not a student (type: post)",
    "provided_id": "1",
    "post_type": "post"
  }
}
```

#### Student Not Published
```bash
GET /?rest_route=/students/v1/students/123
```

**Response:**
```json
{
  "code": "student_not_accessible",
  "message": "Student is not accessible",
  "data": {
    "status": 403,
    "message": "Student ID 123 exists but is not published (status: draft)",
    "provided_id": "123",
    "post_status": "draft",
    "suggestion": "Only published students are accessible via the public API"
  }
}
```

### 3. ➕ CREATE Student - Validation Errors

#### Missing Required Fields
```bash
POST /?rest_route=/students/v1/students
Content-Type: application/json
X-WP-Nonce: valid-nonce

{
  "student_email": "test@example.com"
}
```

**Response:**
```json
{
  "code": "validation_failed",
  "message": "Validation failed",
  "data": {
    "status": 400,
    "errors": [
      "Student name is required",
      "Student ID is required"
    ],
    "message": "Please correct the following errors: Student name is required, Student ID is required"
  }
}
```

#### Invalid Email Format
```bash
POST /?rest_route=/students/v1/students
Content-Type: application/json
X-WP-Nonce: valid-nonce

{
  "title": "Test Student",
  "student_id": "TEST001",
  "student_email": "invalid-email"
}
```

**Response:**
```json
{
  "code": "validation_failed",
  "message": "Validation failed",
  "data": {
    "status": 400,
    "errors": [
      "Invalid email format"
    ],
    "message": "Please correct the following errors: Invalid email format"
  }
}
```

#### Invalid Student ID Format
```bash
POST /?rest_route=/students/v1/students
Content-Type: application/json
X-WP-Nonce: valid-nonce

{
  "title": "Test Student",
  "student_id": "test@123",
  "student_email": "test@example.com"
}
```

**Response:**
```json
{
  "code": "validation_failed",
  "message": "Validation failed",
  "data": {
    "status": 400,
    "errors": [
      "Student ID must contain only uppercase letters, numbers, hyphens, and underscores"
    ],
    "message": "Please correct the following errors: Student ID must contain only uppercase letters, numbers, hyphens, and underscores"
  }
}
```

#### Invalid Date of Birth
```bash
POST /?rest_route=/students/v1/students
Content-Type: application/json
X-WP-Nonce: valid-nonce

{
  "title": "Test Student",
  "student_id": "TEST001",
  "student_email": "test@example.com",
  "student_dob": "invalid-date"
}
```

**Response:**
```json
{
  "code": "validation_failed",
  "message": "Validation failed",
  "data": {
    "status": 400,
    "errors": [
      "Date of birth must be in YYYY-MM-DD format"
    ],
    "message": "Please correct the following errors: Date of birth must be in YYYY-MM-DD format"
  }
}
```

#### Invalid Status
```bash
POST /?rest_route=/students/v1/students
Content-Type: application/json
X-WP-Nonce: valid-nonce

{
  "title": "Test Student",
  "student_id": "TEST001",
  "student_email": "test@example.com",
  "student_is_active": "invalid-status"
}
```

**Response:**
```json
{
  "code": "validation_failed",
  "message": "Validation failed",
  "data": {
    "status": 400,
    "errors": [
      "Status must be either \"active\" or \"inactive\""
    ],
    "message": "Please correct the following errors: Status must be either \"active\" or \"inactive\""
  }
}
```

#### Duplicate Student ID
```bash
POST /?rest_route=/students/v1/students
Content-Type: application/json
X-WP-Nonce: valid-nonce

{
  "title": "Test Student",
  "student_id": "STU2025386",
  "student_email": "test@example.com"
}
```

**Response:**
```json
{
  "code": "student_id_exists",
  "message": "Student ID already exists",
  "data": {
    "status": 409,
    "existing_student_id": 65,
    "message": "A student with ID \"STU2025386\" already exists (ID: 65)"
  }
}
```

### 4. ✏️ UPDATE Student - Not Found & Validation Errors

#### Update Non-existent Student
```bash
PUT /?rest_route=/students/v1/students/99999
Content-Type: application/json
X-WP-Nonce: valid-nonce

{
  "title": "Updated Student"
}
```

**Response:**
```json
{
  "code": "student_not_found",
  "message": "Student not found",
  "data": {
    "status": 404,
    "message": "No student found with ID 99999",
    "provided_id": "99999",
    "suggestion": "Please check the student ID or try listing all students first"
  }
}
```

#### Update with Invalid Data
```bash
PUT /?rest_route=/students/v1/students/65
Content-Type: application/json
X-WP-Nonce: valid-nonce

{
  "student_email": "invalid-email",
  "student_is_active": "invalid-status"
}
```

**Response:**
```json
{
  "code": "validation_failed",
  "message": "Validation failed",
  "data": {
    "status": 400,
    "errors": [
      "Invalid email format",
      "Status must be either \"active\" or \"inactive\""
    ],
    "message": "Please correct the following errors: Invalid email format, Status must be either \"active\" or \"inactive\""
  }
}
```

### 5. 🗑️ DELETE Student - Not Found Errors

#### Delete Non-existent Student
```bash
DELETE /?rest_route=/students/v1/students/99999
X-WP-Nonce: valid-nonce
```

**Response:**
```json
{
  "code": "student_not_found",
  "message": "Student not found",
  "data": {
    "status": 404,
    "message": "No student found with ID 99999",
    "provided_id": "99999",
    "suggestion": "Please check the student ID or try listing all students first"
  }
}
```

### 6. 🔐 Authentication & Permission Errors

#### Missing Nonce
```bash
POST /?rest_route=/students/v1/students
Content-Type: application/json

{
  "title": "Test Student",
  "student_id": "TEST001"
}
```

**Response:**
```json
{
  "code": "rest_forbidden",
  "message": "Authentication required.",
  "data": {
    "status": 401
  }
}
```

#### Invalid Nonce
```bash
POST /?rest_route=/students/v1/students
Content-Type: application/json
X-WP-Nonce: invalid-nonce

{
  "title": "Test Student",
  "student_id": "TEST001"
}
```

**Response:**
```json
{
  "code": "rest_cookie_invalid_nonce",
  "message": "Cookie check failed",
  "data": {
    "status": 403
  }
}
```

#### Insufficient Permissions
```bash
POST /?rest_route=/students/v1/students
Content-Type: application/json
X-WP-Nonce: valid-nonce

{
  "title": "Test Student",
  "student_id": "TEST001"
}
```

**Response:**
```json
{
  "code": "rest_forbidden",
  "message": "Administrator privileges required.",
  "data": {
    "status": 403
  }
}
```

## Validation Rules

### Student ID Format
- Must contain only uppercase letters, numbers, hyphens, and underscores
- Example: `STU2025386`, `TEST-001`, `STUDENT_123`

### Email Format
- Must be a valid email address
- Uses WordPress `is_email()` function for validation

### Date of Birth
- Must be in `YYYY-MM-DD` format
- Must represent an age between 5 and 100 years
- Example: `2000-01-01`

### Status
- Must be either `"active"` or `"inactive"`

### Phone Number
- Must contain only digits, spaces, hyphens, plus signs, and parentheses
- Example: `+1 (555) 123-4567`

### Pagination Parameters
- `per_page`: Must be between 1 and 100
- `page`: Must be a positive integer

## Testing Error Handling

Use the comprehensive error handling test page:

```
http://localhost/devrix-test-project/wp-content/plugins/students/error-handling-test.html
```

This page includes buttons to test all error scenarios and displays the responses in a user-friendly format.

## Best Practices for API Consumers

1. **Always check the HTTP status code** before processing the response
2. **Read the error message** for actionable feedback
3. **Follow the suggestions** provided in the error response
4. **Validate data** before sending to the API
5. **Handle authentication errors** by refreshing nonces
6. **Use proper error handling** in your application code

## Error Recovery Strategies

### For Validation Errors (400)
- Fix the data according to the error messages
- Re-submit the request with corrected data

### For Not Found Errors (404)
- Verify the student ID exists
- Use the list endpoint to get valid student IDs

### For Authentication Errors (401/403)
- Refresh the nonce using the terminal helper
- Ensure you're logged in as an administrator
- Check that the nonce hasn't expired (10-minute lifetime)

### For Conflict Errors (409)
- Choose a different student ID
- Update the existing student instead of creating a new one

## Support

If you encounter errors not covered in this documentation, please check:
1. The WordPress error logs
2. The browser's developer console
3. The terminal output for additional debugging information
