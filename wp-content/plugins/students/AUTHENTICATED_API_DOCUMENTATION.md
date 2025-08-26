# Students Plugin Authenticated REST API Documentation

This document describes the authenticated REST API endpoints for the Students plugin that require administrator privileges.

## 🔐 Authentication Requirements

All authenticated endpoints require:
- **User must be logged in** to WordPress
- **User must have administrator privileges** (`manage_options` capability)
- **Valid nonce** for CSRF protection

## 📋 Available Authenticated Endpoints

### 1. Create New Student
- **Method**: POST
- **URL**: `/wp-json/students/v1/students`
- **Authentication**: Required (Admin only)

### 2. Update Existing Student
- **Method**: PUT
- **URL**: `/wp-json/students/v1/students/{id}`
- **Authentication**: Required (Admin only)

### 3. Delete Student
- **Method**: DELETE
- **URL**: `/wp-json/students/v1/students/{id}`
- **Authentication**: Required (Admin only)

## 🔧 Authentication Methods

### Method 1: WordPress Nonce (Recommended)
```javascript
// Get nonce from WordPress
const nonce = wpApiSettings.nonce;

// Use in requests
fetch('/wp-json/students/v1/students', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': nonce
    },
    body: JSON.stringify(data)
});
```

### Method 2: Application Passwords
```bash
# Generate application password in WordPress admin
# Use Basic Auth
curl -X POST "http://your-site.com/wp-json/students/v1/students" \
  -H "Content-Type: application/json" \
  -u "username:application_password" \
  -d '{"title":"New Student","student_id":"STU001"}'
```

### Method 3: JWT (if JWT plugin is installed)
```javascript
// Get JWT token first
const token = await getJWTToken();

// Use in requests
fetch('/wp-json/students/v1/students', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`
    },
    body: JSON.stringify(data)
});
```

## 📝 Endpoint Details

### 1. Create New Student

**Endpoint**: `POST /wp-json/students/v1/students`

**Required Fields**:
- `title` (string, max 200 chars)
- `student_id` (string, max 50 chars, must be unique)

**Optional Fields**:
- `content` (string, HTML allowed)
- `excerpt` (string)
- `student_email` (valid email)
- `student_phone` (string)
- `student_dob` (date, YYYY-MM-DD format)
- `student_address` (string)
- `student_country` (string)
- `student_city` (string)
- `student_class_grade` (string)
- `student_is_active` (string: 'active' or 'inactive')
- `courses` (array of strings)
- `grade_levels` (array of strings)

**Example Request**:
```json
{
    "title": "John Doe",
    "content": "John is a dedicated student...",
    "excerpt": "Dedicated student with excellent grades",
    "student_id": "STU2025001",
    "student_email": "john.doe@example.com",
    "student_phone": "+1234567890",
    "student_dob": "2000-01-15",
    "student_address": "123 Main Street",
    "student_country": "United States",
    "student_city": "New York",
    "student_class_grade": "Grade 12",
    "student_is_active": "active",
    "courses": ["Mathematics", "Physics"],
    "grade_levels": ["12"]
}
```

**Success Response** (201):
```json
{
    "success": true,
    "message": "Student created successfully",
    "data": {
        "id": 123,
        "title": "John Doe",
        "content": "John is a dedicated student...",
        "excerpt": "Dedicated student with excellent grades",
        "slug": "john-doe",
        "date": "2024-01-15 10:30:00",
        "modified": "2024-01-15 10:30:00",
        "status": "publish",
        "featured_image": null,
        "meta": {
            "student_id": "STU2025001",
            "student_email": "john.doe@example.com",
            "student_phone": "+1234567890",
            "student_dob": "2000-01-15",
            "student_address": "123 Main Street",
            "student_country": "United States",
            "student_city": "New York",
            "student_class_grade": "Grade 12",
            "student_is_active": "active"
        },
        "taxonomies": {
            "courses": ["Mathematics", "Physics"],
            "grade_levels": ["12"]
        },
        "links": {
            "self": "http://your-site.com/wp-json/students/v1/students/123",
            "collection": "http://your-site.com/wp-json/students/v1/students",
            "website": "http://your-site.com/students/john-doe/"
        }
    }
}
```

**Error Responses**:
- `401` - Authentication required
- `403` - Administrator privileges required
- `400` - Student ID already exists
- `500` - Failed to create student

### 2. Update Existing Student

**Endpoint**: `PUT /wp-json/students/v1/students/{id}`

**All Fields Optional** (only send fields to update):
- `title` (string, max 200 chars)
- `content` (string, HTML allowed)
- `excerpt` (string)
- `student_id` (string, max 50 chars, must be unique)
- `student_email` (valid email)
- `student_phone` (string)
- `student_dob` (date, YYYY-MM-DD format)
- `student_address` (string)
- `student_country` (string)
- `student_city` (string)
- `student_class_grade` (string)
- `student_is_active` (string: 'active' or 'inactive')
- `courses` (array of strings)
- `grade_levels` (array of strings)

**Example Request**:
```json
{
    "title": "John Doe Updated",
    "student_email": "john.updated@example.com",
    "student_is_active": "inactive"
}
```

**Success Response** (200):
```json
{
    "success": true,
    "message": "Student updated successfully",
    "data": {
        "id": 123,
        "title": "John Doe Updated",
        "meta": {
            "student_email": "john.updated@example.com",
            "student_is_active": "inactive"
        }
    }
}
```

**Error Responses**:
- `401` - Authentication required
- `403` - Administrator privileges required
- `404` - Student not found
- `400` - Student ID already exists
- `500` - Failed to update student

### 3. Delete Student

**Endpoint**: `DELETE /wp-json/students/v1/students/{id}`

**Parameters**:
- `id` (required): Student post ID
- `force` (optional, boolean): Permanently delete (default: false)

**Example Requests**:
```bash
# Move to trash (default)
curl -X DELETE "http://your-site.com/wp-json/students/v1/students/123" \
  -H "X-WP-Nonce: your_nonce_here"

# Permanently delete
curl -X DELETE "http://your-site.com/wp-json/students/v1/students/123?force=true" \
  -H "X-WP-Nonce: your_nonce_here"
```

**Success Response** (200):
```json
{
    "success": true,
    "message": "Student moved to trash",
    "data": {
        "id": 123,
        "deleted": true
    }
}
```

**Error Responses**:
- `401` - Authentication required
- `403` - Administrator privileges required
- `404` - Student not found
- `500` - Failed to delete student

## 🛡️ Security Features

### Input Validation & Sanitization
- **Title**: Sanitized with `sanitize_text_field()`, max 200 chars
- **Content**: Sanitized with `wp_kses_post()` (allows safe HTML)
- **Email**: Validated with `is_email()`, sanitized with `sanitize_email()`
- **Phone**: Sanitized with `sanitize_text_field()`
- **Date of Birth**: Validated with regex pattern `YYYY-MM-DD`
- **Status**: Validated against allowed values ('active', 'inactive')
- **Arrays**: Validated as arrays of strings

### Duplicate Prevention
- **Student ID**: Checked for uniqueness before creation/update
- **Error Response**: Returns 400 if student ID already exists

### Permission Checks
- **Authentication**: Verifies user is logged in
- **Authorization**: Verifies user has `manage_options` capability
- **CSRF Protection**: Requires valid WordPress nonce

## 📚 Usage Examples

### JavaScript (Frontend)
```javascript
// Create student
async function createStudent(studentData) {
    const response = await fetch('/wp-json/students/v1/students', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-WP-Nonce': wpApiSettings.nonce
        },
        body: JSON.stringify(studentData)
    });
    
    return await response.json();
}

// Update student
async function updateStudent(id, updateData) {
    const response = await fetch(`/wp-json/students/v1/students/${id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-WP-Nonce': wpApiSettings.nonce
        },
        body: JSON.stringify(updateData)
    });
    
    return await response.json();
}

// Delete student
async function deleteStudent(id, force = false) {
    const url = force 
        ? `/wp-json/students/v1/students/${id}?force=true`
        : `/wp-json/students/v1/students/${id}`;
        
    const response = await fetch(url, {
        method: 'DELETE',
        headers: {
            'X-WP-Nonce': wpApiSettings.nonce
        }
    });
    
    return await response.json();
}
```

### PHP (Backend)
```php
// Create student
$response = wp_remote_post(rest_url('students/v1/students'), array(
    'headers' => array(
        'Content-Type' => 'application/json',
        'X-WP-Nonce' => wp_create_nonce('wp_rest'),
    ),
    'body' => json_encode($student_data),
));

// Update student
$response = wp_remote_request(rest_url('students/v1/students/' . $id), array(
    'method' => 'PUT',
    'headers' => array(
        'Content-Type' => 'application/json',
        'X-WP-Nonce' => wp_create_nonce('wp_rest'),
    ),
    'body' => json_encode($update_data),
));

// Delete student
$response = wp_remote_request(rest_url('students/v1/students/' . $id), array(
    'method' => 'DELETE',
    'headers' => array(
        'X-WP-Nonce' => wp_create_nonce('wp_rest'),
    ),
));
```

### cURL (Command Line)
```bash
# Create student
curl -X POST "http://your-site.com/wp-json/students/v1/students" \
  -H "Content-Type: application/json" \
  -H "X-WP-Nonce: your_nonce_here" \
  -d '{
    "title": "New Student",
    "student_id": "STU001",
    "student_email": "student@example.com"
  }'

# Update student
curl -X PUT "http://your-site.com/wp-json/students/v1/students/123" \
  -H "Content-Type: application/json" \
  -H "X-WP-Nonce: your_nonce_here" \
  -d '{
    "title": "Updated Student",
    "student_is_active": "inactive"
  }'

# Delete student
curl -X DELETE "http://your-site.com/wp-json/students/v1/students/123" \
  -H "X-WP-Nonce: your_nonce_here"
```

## 🧪 Testing

Use the provided test script to verify the endpoints:
```
http://your-site.com/wp-content/plugins/students/test-authenticated-api.php
```

## ⚠️ Important Notes

1. **Authentication Required**: All endpoints require administrator privileges
2. **CSRF Protection**: Always include the WordPress nonce
3. **Input Validation**: All inputs are validated and sanitized
4. **Error Handling**: Always check response status codes
5. **Student ID Uniqueness**: Student IDs must be unique across all students
6. **Soft Delete**: By default, deletion moves students to trash (not permanent)

## 🔒 Security Best Practices

1. **Use HTTPS**: Always use HTTPS in production
2. **Validate Nonces**: Always verify nonces on the server side
3. **Limit Access**: Only grant administrator access to trusted users
4. **Monitor Logs**: Monitor API access logs for suspicious activity
5. **Rate Limiting**: Consider implementing rate limiting for production use
6. **Input Sanitization**: Never trust user input, always sanitize
