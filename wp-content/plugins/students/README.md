# Students Plugin

A WordPress plugin for managing students and their information.

## Description

The Students plugin provides a comprehensive solution for educational institutions to manage student information within WordPress. It includes a custom post type for students, taxonomies for courses and grade levels, and various features for displaying student information.

## Features

- **Custom Post Type**: Students with custom fields
- **Taxonomies**: Courses and Grade Levels
- **Shortcodes**: Display student lists and profiles
- **Admin Interface**: Custom columns and settings
- **Frontend Display**: Responsive student cards and profiles
- **Settings Page**: Configurable options
- **Internationalization**: Ready for translation

## Installation

1. Upload the `students` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to 'Students' in the admin menu to start adding students

## Usage

### Adding Students

1. Go to **Students > Add New** in the admin menu
2. Enter the student's name as the title
3. Add a description in the content area
4. Set a featured image for the student's photo
5. Fill in the student details in the "Student Details" meta box:
   - Student ID
   - Email
   - Phone
   - Date of Birth
   - Address
6. Assign courses and grade levels using the taxonomies
7. Publish the student

### Shortcodes

#### Students List
Display a list of students:
```
[students_list]
```

With options:
```
[students_list posts_per_page="12" course="math,science" grade_level="10th" orderby="title" order="ASC"]
```

#### Student Profile
Display a specific student's profile:
```
[student_profile id="123"]
```

### Settings

Go to **Students > Settings** to configure:
- Students per page
- Enable/disable search functionality
- Show/hide email addresses publicly

## File Structure

```
students/
├── students.php                 # Main plugin file
├── README.md                   # This file
├── includes/
│   ├── class-students-loader.php
│   ├── class-students-post-type.php
│   ├── class-students-admin.php
│   └── class-students-public.php
├── assets/
│   ├── css/
│   │   ├── admin.css
│   │   └── public.css
│   └── js/
│       ├── admin.js
│       └── public.js
├── templates/
│   ├── single-student.php
│   └── archive-student.php
└── languages/
    └── students.pot
```

## Customization

### Templates
The plugin looks for custom templates in the following order:
1. Your theme's `single-student.php` or `archive-student.php`
2. Plugin's `templates/single-student.php` or `templates/archive-student.php`
3. Default WordPress templates

### Styling
Add custom CSS to your theme to override the plugin's styles. The plugin uses these CSS classes:
- `.students-list` - Container for student lists
- `.students-grid` - Grid layout for student cards
- `.student-card` - Individual student card
- `.student-profile` - Student profile page
- `.student-details` - Student information section

### Hooks and Filters

The plugin provides several hooks for customization:

```php
// Modify student query
add_filter( 'students_query_args', function( $args ) {
    // Modify query arguments
    return $args;
});

// Add custom fields to student cards
add_action( 'students_card_after_info', function( $post_id ) {
    // Add custom content
});
```

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher

## Changelog

### 1.0.0
- Initial release
- Custom post type for students
- Course and grade level taxonomies
- Student details meta box
- Shortcodes for displaying students
- Admin settings page
- Frontend styling

## Support

For support and feature requests, please contact the plugin author.

## License

This plugin is licensed under the GPL v2 or later.

## Credits

Developed following WordPress plugin development best practices and boilerplate structure.
