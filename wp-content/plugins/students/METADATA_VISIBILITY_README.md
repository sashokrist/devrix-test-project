# Metadata Visibility Settings - Students Plugin

## Overview

This document describes the metadata visibility settings feature that allows administrators to control which student metadata fields are displayed on single student pages.

## Features

### 1. Settings Page Location

The settings page is accessible in two locations:
- **Students → Settings** (submenu under Students)
- **Settings → Students** (submenu under main Settings menu)

### 2. Metadata Visibility Controls

The following metadata fields can be controlled:

| Field | Setting Key | Description |
|-------|-------------|-------------|
| Student ID | `show_student_id` | Show/hide student ID on single pages |
| Email | `show_email` | Show/hide email address on single pages |
| Phone | `show_phone` | Show/hide phone number on single pages |
| Date of Birth | `show_dob` | Show/hide date of birth on single pages |
| Address | `show_address` | Show/hide address on single pages |
| Country | `show_country` | Show/hide country on single pages |
| City | `show_city` | Show/hide city on single pages |
| Class/Grade | `show_class_grade` | Show/hide class/grade on single pages |
| Status | `show_status` | Show/hide student status on single pages |

### 3. Programmatic Implementation

All settings are added programmatically using a field definition array, making it easy to add new fields in the future.

## How It Works

### 1. Settings Storage

Settings are stored in the WordPress options table under the key `students_options`:

```php
$options = array(
    'show_student_id' => true,
    'show_email' => true,
    'show_phone' => false,
    'show_dob' => true,
    // ... etc
);
```

### 2. Visibility Checking

The `Students_Sanitizer::should_display_field()` method checks if a field should be displayed:

```php
if ( Students_Sanitizer::should_display_field( 'email' ) ) {
    // Display email field
}
```

### 3. Front-End Implementation

All templates and shortcodes check visibility settings before displaying fields:

- **Single Student Template** (`single-student.php`)
- **Student Profile Shortcode** (`[student_profile]`)
- **Student Meta Fields Shortcode** (`[student_meta_fields]`)

## Usage Examples

### 1. Check Field Visibility in Code

```php
// Check if email should be displayed
if ( Students_Sanitizer::should_display_field( 'email' ) ) {
    echo '<div class="email-field">' . esc_html( $email ) . '</div>';
}
```

### 2. Get All Visibility Settings

```php
$options = get_option( 'students_options', array() );
$show_email = isset( $options['show_email'] ) ? $options['show_email'] : true;
```

### 3. Update Settings Programmatically

```php
$options = get_option( 'students_options', array() );
$options['show_email'] = false;
update_option( 'students_options', $options );
```

## Default Behavior

- All fields default to **visible** (`true`)
- If a setting doesn't exist, the field is shown by default
- Settings are per-field, allowing granular control

## Location Field Special Handling

The Location field has special logic:
- Shows if either Country OR City is enabled
- Only displays enabled sub-fields (Country and/or City)
- Combines enabled fields with comma separation

## Security Considerations

- All settings are properly sanitized on save
- Settings are only accessible to users with `manage_options` capability
- Default values ensure backward compatibility

## Adding New Fields

To add a new metadata field:

1. **Add to field definition array** in `add_metadata_visibility_fields()`:
```php
'show_new_field' => array(
    'label' => __( 'New Field', 'students' ),
    'description' => __( 'Show new field on single student pages', 'students' )
),
```

2. **Add default value** in `set_default_options()`:
```php
'show_new_field' => true,
```

3. **Update templates** to check visibility:
```php
if ( Students_Sanitizer::should_display_field( 'new_field' ) ) {
    // Display the field
}
```

## Testing

### 1. Test Settings Page
1. Go to **Settings → Students**
2. Uncheck some fields
3. Save settings
4. Verify changes are saved

### 2. Test Front-End Display
1. Go to a single student page
2. Verify that unchecked fields are hidden
3. Verify that checked fields are visible
4. Test with different combinations

### 3. Test Location Field
1. Hide only Country - verify City still shows
2. Hide only City - verify Country still shows
3. Hide both - verify Location field is hidden

## Troubleshooting

### Common Issues

1. **Field still showing after hiding**
   - Clear any caching plugins
   - Check if the setting was saved correctly
   - Verify the template is using the visibility check

2. **Settings not saving**
   - Check user permissions (`manage_options`)
   - Verify nonce is valid
   - Check for JavaScript errors

3. **Default values not working**
   - Verify default options are set on plugin activation
   - Check if options exist in database

### Debug Information

To debug visibility settings:

```php
// Check current settings
$options = get_option( 'students_options', array() );
var_dump( $options );

// Check specific field
$show_email = Students_Sanitizer::should_display_field( 'email' );
var_dump( $show_email );
```

## Future Enhancements

Potential improvements:
- Per-user visibility settings
- Role-based visibility controls
- Conditional visibility based on field values
- Bulk visibility operations
- Import/export settings

## Support

For issues or questions about the metadata visibility settings:
1. Check the troubleshooting section
2. Verify settings are saved correctly
3. Test with default settings
4. Review the code implementation
