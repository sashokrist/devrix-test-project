# Students Plugin REST API Documentation

This document describes the REST API endpoints available for the Students plugin.

## Base URL

For this WordPress installation, use the following format:
- **Standard format**: `http://localhost/devrix-test-project/?rest_route=/students/v1`
- **Alternative format**: `http://localhost/devrix-test-project/wp-json/students/v1` (may redirect)

## Endpoints

### 1. Get All Students

**Endpoint:** `GET http://localhost/devrix-test-project/?rest_route=/students/v1/students`

**Description:** Retrieves a list of all published students with optional filtering and pagination.

**Parameters:**
- `per_page` (optional): Number of students per page (default: 10, max: 100)
- `page` (optional): Page number (default: 1)
- `course` (optional): Filter by course slug
- `grade_level` (optional): Filter by grade level slug
- `status` (optional): Filter by status ('active' or 'inactive')

**Example Request:**
```
GET http://localhost/devrix-test-project/?rest_route=/students/v1/students&per_page=5&page=1&status=active
```

**Example Response:**
```json
{
    "success": true,
    "data": {
        "students": [
            {
                "id": 65,
                "title": "Bob 33",
                "content": "Bob is a creative and innovative student...",
                "excerpt": "A tech-savvy student passionate about programming and problem-solving.",
                "slug": "bob",
                "date": "2025-08-21 12:13:06",
                "modified": "2025-08-22 12:42:47",
                "status": "publish",
                "featured_image": {
                    "url": "http://localhost/devrix-test-project/wp-content/uploads/2025/08/dino-ferrari.jpeg",
                    "width": 284,
                    "height": 177,
                    "alt": ""
                },
                "meta": {
                    "student_id": "STU2025386",
                    "student_email": "bob@test.com",
                    "student_phone": "4444 555",
                    "student_dob": "1999-06-08",
                    "student_address": "LA",
                    "student_country": "United States",
                    "student_city": "New York",
                    "student_class_grade": "Grade 12",
                    "student_is_active": "active"
                },
                "taxonomies": {
                    "courses": ["IT", "Math"],
                    "grade_levels": ["4"]
                },
                "links": {
                    "self": "http://localhost/devrix-test-project/wp-json/students/v1/students/65",
                    "collection": "http://localhost/devrix-test-project/wp-json/students/v1/students",
                    "website": "http://localhost/devrix-test-project/students/bob/"
                }
            }
        ],
        "pagination": {
            "current_page": 1,
            "per_page": 5,
            "total_posts": 25,
            "total_pages": 5
        }
    }
}
```

### 2. Get Student by ID

**Endpoint:** `GET http://localhost/devrix-test-project/?rest_route=/students/v1/students/{id}`

**Description:** Retrieves a specific student by their ID.

### 3. Get Active Students Only

**Endpoint:** `GET http://localhost/devrix-test-project/?rest_route=/students/v1/students/active`

**Description:** Retrieves only active students with optional filtering and pagination.

**Parameters:**
- `per_page` (optional): Number of students per page (default: 10, max: 100)
- `page` (optional): Page number (default: 1)
- `course` (optional): Filter by course slug
- `grade_level` (optional): Filter by grade level slug

**Example Request:**
```
GET http://localhost/devrix-test-project/?rest_route=/students/v1/students/active&per_page=5&page=1
```

**Example Response:**
```json
{
    "success": true,
    "data": {
        "students": [
            {
                "id": 65,
                "title": "Bob 33",
                "content": "Bob is a creative and innovative student...",
                "excerpt": "A tech-savvy student passionate about programming and problem-solving.",
                "slug": "bob",
                "date": "2025-08-21 12:13:06",
                "modified": "2025-08-22 12:42:47",
                "status": "publish",
                "featured_image": {
                    "url": "http://localhost/devrix-test-project/wp-content/uploads/2025/08/dino-ferrari.jpeg",
                    "width": 284,
                    "height": 177,
                    "alt": ""
                },
                "meta": {
                    "student_id": "STU2025386",
                    "student_email": "bob@test.com",
                    "student_phone": "4444 555",
                    "student_dob": "1999-06-08",
                    "student_address": "LA",
                    "student_country": "United States",
                    "student_city": "New York",
                    "student_class_grade": "Grade 12",
                    "student_is_active": "active"
                },
                "taxonomies": {
                    "courses": ["IT", "Math"],
                    "grade_levels": ["4"]
                },
                "links": {
                    "self": "http://localhost/devrix-test-project/wp-json/students/v1/students/65",
                    "collection": "http://localhost/devrix-test-project/wp-json/students/v1/students",
                    "website": "http://localhost/devrix-test-project/students/bob/"
                }
            }
        ],
        "pagination": {
            "current_page": 1,
            "per_page": 5,
            "total_posts": 12,
            "total_pages": 3
        },
        "filter": "active_only"
    }
}
```

**Parameters:**
- `id` (required): The student's post ID

**Example Request:**
```
GET http://localhost/devrix-test-project/?rest_route=/students/v1/students/65
```

**Example Response:**
```json
{
    "success": true,
    "data": {
        "id": 65,
        "title": "Bob 33",
        "content": "Bob is a creative and innovative student...",
        "excerpt": "A tech-savvy student passionate about programming and problem-solving.",
        "slug": "bob",
        "date": "2025-08-21 12:13:06",
        "modified": "2025-08-22 12:42:47",
        "status": "publish",
        "featured_image": {
            "url": "http://localhost/devrix-test-project/wp-content/uploads/2025/08/dino-ferrari.jpeg",
            "width": 284,
            "height": 177,
            "alt": ""
        },
        "meta": {
            "student_id": "STU2025386",
            "student_email": "bob@test.com",
            "student_phone": "4444 555",
            "student_dob": "1999-06-08",
            "student_address": "LA",
            "student_country": "United States",
            "student_city": "New York",
            "student_class_grade": "Grade 12",
            "student_is_active": "active"
        },
        "taxonomies": {
            "courses": ["IT", "Math"],
            "grade_levels": ["4"]
        },
        "links": {
            "self": "http://localhost/devrix-test-project/wp-json/students/v1/students/65",
            "collection": "http://localhost/devrix-test-project/wp-json/students/v1/students",
            "website": "http://localhost/devrix-test-project/students/bob/"
        }
    }
}
```

**Error Response (404):**
```json
{
    "code": "student_not_found",
    "message": "Student not found",
    "data": {
        "status": 404
    }
}
```

## Usage Examples

### JavaScript (Fetch API)

```javascript
// Get all students
fetch('http://localhost/devrix-test-project/?rest_route=/students/v1/students&per_page=10&status=active')
    .then(response => response.json())
    .then(data => {
        console.log(data.data.students);
    });

// Get specific student
fetch('http://localhost/devrix-test-project/?rest_route=/students/v1/students/65')
    .then(response => response.json())
    .then(data => {
        console.log(data.data);
    });

// Get active students only
fetch('http://localhost/devrix-test-project/?rest_route=/students/v1/students/active&per_page=5')
    .then(response => response.json())
    .then(data => {
        console.log(data.data.students);
    });
```

### PHP (cURL)

```php
// Get all students
$url = 'http://localhost/devrix-test-project/?rest_route=/students/v1/students&per_page=10';
$response = wp_remote_get($url);
$data = json_decode(wp_remote_retrieve_body($response), true);

// Get specific student
$url = 'http://localhost/devrix-test-project/?rest_route=/students/v1/students/65';
$response = wp_remote_get($url);
$data = json_decode(wp_remote_retrieve_body($response), true);
```

### cURL Commands

```bash
# Get all students
curl "http://localhost/devrix-test-project/?rest_route=/students/v1/students&per_page=5"

# Get specific student
curl "http://localhost/devrix-test-project/?rest_route=/students/v1/students/65"

# Get students with filters
curl "http://localhost/devrix-test-project/?rest_route=/students/v1/students&status=active&per_page=3"

# Get active students only
curl "http://localhost/devrix-test-project/?rest_route=/students/v1/students/active&per_page=5"

# Get active students with course filter
curl "http://localhost/devrix-test-project/?rest_route=/students/v1/students/active&course=IT&per_page=3"
```

## Notes

- All endpoints are public and require no authentication
- Only published students are returned
- The API returns JSON responses
- Pagination is supported for the students list endpoint
- Filtering by course, grade level, and status is available
- Featured images are included with URL, dimensions, and alt text
- All student meta fields are included in the response
- Taxonomy terms (courses and grade levels) are included
- Links to self, collection, and website are provided for navigation

## ✅ Tested and Working

The API has been successfully tested and is working correctly with the following results:
- ✅ Get all students endpoint working
- ✅ Get student by ID endpoint working  
- ✅ Get active students only endpoint working
- ✅ Pagination working
- ✅ Filtering by status working
- ✅ Filtering by course and grade level working
- ✅ Complete student data including metadata
- ✅ Featured images included
- ✅ Taxonomy data included
