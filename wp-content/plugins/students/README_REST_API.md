# Students Plugin REST API

This document explains the REST API implementation for the Students plugin.

## Overview

The Students plugin now includes a complete REST API that allows you to access student data programmatically. The API provides two main endpoints:

1. **Get All Students** - Retrieve a list of all published students
2. **Get Student by ID** - Retrieve a specific student by their ID

## Implementation Details

### Files Created/Modified

1. **`includes/class-students-rest-api.php`** - Main REST API class
2. **`students.php`** - Updated to include and initialize the REST API
3. **`REST_API_DOCUMENTATION.md`** - Complete API documentation
4. **`test-rest-api.php`** - Test script for the API

### Key Features

- **Public Endpoints**: No authentication required
- **JSON Responses**: All responses are in JSON format
- **Pagination Support**: Built-in pagination for large datasets
- **Filtering Options**: Filter by course, grade level, and status
- **Complete Data**: Includes all student meta fields and taxonomies
- **Error Handling**: Proper error responses for invalid requests
- **Featured Images**: Includes image URLs and metadata

## Testing the API

### Method 1: Using the Test Script

1. Access the test script in your browser:
   ```
   http://your-site.com/wp-content/plugins/students/test-rest-api.php
   ```

2. The script will run comprehensive tests and show results.

### Method 2: Direct API Testing

#### Test Get All Students
```bash
curl "http://your-site.com/wp-json/students/v1/students?per_page=5"
```

#### Test Get Student by ID
```bash
curl "http://your-site.com/wp-json/students/v1/students/123"
```

#### Test with Filters
```bash
curl "http://your-site.com/wp-json/students/v1/students?status=active&course=mathematics"
```

### Method 3: JavaScript Testing

```javascript
// Test in browser console
fetch('/wp-json/students/v1/students?per_page=3')
    .then(response => response.json())
    .then(data => {
        console.log('Students:', data.data.students);
        console.log('Pagination:', data.data.pagination);
    });
```

## API Endpoints

### 1. Get All Students
- **URL**: `/wp-json/students/v1/students`
- **Method**: GET
- **Parameters**:
  - `per_page` (optional): Number of students per page (1-100, default: 10)
  - `page` (optional): Page number (default: 1)
  - `course` (optional): Filter by course slug
  - `grade_level` (optional): Filter by grade level slug
  - `status` (optional): Filter by status ('active' or 'inactive')

### 2. Get Student by ID
- **URL**: `/wp-json/students/v1/students/{id}`
- **Method**: GET
- **Parameters**:
  - `id` (required): Student post ID

### 3. Get Active Students Only
- **URL**: `/wp-json/students/v1/students/active`
- **Method**: GET
- **Parameters**:
  - `per_page` (optional): Number of students per page (1-100, default: 10)
  - `page` (optional): Page number (default: 1)
  - `course` (optional): Filter by course slug
  - `grade_level` (optional): Filter by grade level slug

## Response Format

### Success Response
```json
{
    "success": true,
    "data": {
        "students": [...], // Array of student objects
        "pagination": {    // Only for list endpoint
            "current_page": 1,
            "per_page": 10,
            "total_posts": 25,
            "total_pages": 3
        }
    }
}
```

### Student Object Structure
```json
{
    "id": 123,
    "title": "Student Name",
    "content": "Student description",
    "excerpt": "Student excerpt",
    "slug": "student-slug",
    "date": "2024-01-15 10:30:00",
    "modified": "2024-01-15 10:30:00",
    "status": "publish",
    "featured_image": {
        "url": "https://example.com/image.jpg",
        "width": 300,
        "height": 200,
        "alt": "Student photo"
    },
    "meta": {
        "student_id": "STU001",
        "student_email": "student@example.com",
        "student_phone": "+1234567890",
        "student_dob": "2000-01-15",
        "student_address": "123 Main St",
        "student_country": "USA",
        "student_city": "New York",
        "student_class_grade": "Grade 10",
        "student_is_active": "active"
    },
    "taxonomies": {
        "courses": ["Mathematics", "Physics"],
        "grade_levels": ["Grade 10"]
    },
    "links": {
        "self": "https://example.com/wp-json/students/v1/students/123",
        "collection": "https://example.com/wp-json/students/v1/students",
        "website": "https://example.com/students/student-slug/"
    }
}
```

### Error Response
```json
{
    "code": "student_not_found",
    "message": "Student not found",
    "data": {
        "status": 404
    }
}
```

## Security Considerations

- All endpoints are public (no authentication required)
- Only published students are returned
- Input validation and sanitization are implemented
- No sensitive information is exposed beyond what's already public

## Troubleshooting

### Common Issues

1. **404 Not Found**: Make sure the plugin is activated and WordPress REST API is enabled
2. **No Students Returned**: Check if there are published students in the database
3. **Permission Denied**: Ensure the student post status is 'publish'

### Debug Steps

1. Check if the plugin is loaded:
   ```php
   if ( class_exists( 'Students_REST_API' ) ) {
       echo "REST API class loaded";
   }
   ```

2. Check if routes are registered:
   ```php
   $routes = rest_get_server()->get_routes();
   foreach ( $routes as $route => $handlers ) {
       if ( strpos( $route, 'students/v1' ) !== false ) {
           echo "Found route: $route\n";
       }
   }
   ```

3. Test WordPress REST API:
   ```bash
   curl "http://your-site.com/wp-json/"
   ```

## Future Enhancements

Potential improvements for future versions:
- Authentication and authorization
- Additional filtering options
- Bulk operations
- Search functionality
- Rate limiting
- Caching support
