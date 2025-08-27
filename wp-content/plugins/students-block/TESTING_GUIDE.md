# Students Block Plugin - Testing Guide

## Overview

The Students Block plugin creates a Gutenberg block for displaying students with various filtering and display options. This guide explains how to test the plugin and verify all functionality.

## Prerequisites

1. **Students Plugin**: The Students Block plugin requires the main Students plugin to be installed and activated
2. **WordPress 5.0+**: Gutenberg block editor support
3. **Students Data**: At least a few students should be created with various statuses (active/inactive)

## Installation & Setup

1. **Activate the Plugin**:
   - Go to WordPress Admin → Plugins
   - Find "Students Block" and click "Activate"

2. **Verify Plugin Files**:
   - Check that all files are present in `/wp-content/plugins/students-block/`
   - Ensure the `build/` directory contains compiled assets

3. **Check Dependencies**:
   - Students plugin should be active
   - Student custom post type should be registered
   - Some students should exist in the database

## Testing the Block Editor

### 1. Add the Block

1. **Create a New Post/Page**:
   - Go to Posts → Add New or Pages → Add New
   - Open the block editor

2. **Insert the Block**:
   - Click the "+" button to add a new block
   - Search for "Students Display" or look in "Widgets" category
   - Click to add the block

3. **Verify Block Appearance**:
   - Block should appear with a preview
   - Settings panel should show in the sidebar
   - Block should have a dashed border and description

### 2. Test Block Settings

#### General Settings
- **Show specific student**: Toggle on/off
- **Select Student**: Dropdown should populate with existing students

#### Multiple Students Settings
- **Number of students**: Test range 1-20
- **Status filter**: Test "Active", "Inactive", "All"
- **Order by**: Test "Name", "Date created", "Date modified", "Menu order"
- **Order**: Test "Ascending", "Descending"

### 3. Test Real-time Preview

1. **Change Settings**: Modify any setting in the sidebar
2. **Verify Updates**: Block preview should update immediately
3. **Test All Combinations**: Try different combinations of settings

## Testing Frontend Display

### 1. Publish and View

1. **Publish the Post/Page**: Click "Publish" or "Update"
2. **View Frontend**: Click "View Post" or visit the page URL
3. **Verify Display**: Students should display according to settings

### 2. Test Different Configurations

#### Configuration 1: Active Students Only
```json
{
  "numberOfStudents": 4,
  "status": "active",
  "orderBy": "title",
  "order": "ASC"
}
```

#### Configuration 2: All Students
```json
{
  "numberOfStudents": 6,
  "status": "all",
  "orderBy": "date",
  "order": "DESC"
}
```

#### Configuration 3: Specific Student
```json
{
  "showSpecificStudent": true,
  "specificStudentId": 1
}
```

### 3. Test Responsive Design

1. **Desktop**: Check grid layout with multiple columns
2. **Tablet**: Resize browser to tablet width
3. **Mobile**: Resize browser to mobile width
4. **Verify**: Layout should adapt appropriately

## Testing Student Data Display

### 1. Student Information

Each student card should display:
- **Photo**: Student image or placeholder
- **Name**: Clickable link to student page
- **Class/Grade**: If available
- **Email**: If available
- **Phone**: If available
- **Status**: Active/Inactive indicator

### 2. Student Status Filtering

1. **Active Students**: Should only show students with `_student_is_active = '1'`
2. **Inactive Students**: Should only show students with `_student_is_active = '0'`
3. **All Students**: Should show both active and inactive

### 3. Student Links

1. **Student Names**: Should link to individual student pages
2. **View All Students**: Should link to students archive page

## Testing Edge Cases

### 1. No Students

1. **Create Block**: Add block to a page
2. **Set Filter**: Use a filter that returns no results
3. **Verify**: Should show "No students found" message

### 2. Invalid Student ID

1. **Set Specific Student**: Choose a non-existent student ID
2. **Verify**: Should handle gracefully and show appropriate message

### 3. Empty Student Data

1. **Create Student**: Create student with minimal data
2. **Display**: Verify block handles missing fields gracefully

## Testing Integration

### 1. Students Plugin Integration

1. **Student Post Type**: Block should query 'student' post type
2. **Student Meta**: Should access student custom fields
3. **Student Taxonomies**: Should work with courses and grade levels

### 2. WordPress Integration

1. **Block Editor**: Should work in Gutenberg editor
2. **Classic Editor**: Should work if classic editor is used
3. **Themes**: Should work with different themes

## Demo Page

### 1. Access Demo Page

1. **Plugin Activation**: Demo page is created automatically
2. **URL**: Visit `/students-block-demo/` on your site
3. **Content**: Should show multiple block examples

### 2. Test Demo Examples

1. **Example 1**: 4 active students
2. **Example 2**: 6 students (all statuses)
3. **Example 3**: Inactive students only
4. **Example 4**: Specific student

## Admin Test Page

### 1. Access Test Page

1. **Admin Menu**: Go to Students → Test Block
2. **Content**: Should show testing instructions and examples

### 2. Verify Information

1. **Instructions**: Should be clear and comprehensive
2. **Examples**: Should show different configurations
3. **Links**: Should link to relevant pages

## Troubleshooting

### Common Issues

1. **Block Not Appearing**:
   - Check if Gutenberg is available
   - Verify plugin is activated
   - Check browser console for JavaScript errors

2. **No Students Displayed**:
   - Verify Students plugin is active
   - Check if students exist in database
   - Verify student status meta fields

3. **Styling Issues**:
   - Check if CSS files are loading
   - Verify theme compatibility
   - Check for CSS conflicts

4. **Settings Not Working**:
   - Check JavaScript console for errors
   - Verify block attributes are being saved
   - Test with different browsers

### Debug Information

1. **Check Plugin Status**: Verify plugin is active in admin
2. **Check File Permissions**: Ensure all files are readable
3. **Check Database**: Verify students exist and have correct meta
4. **Check Console**: Look for JavaScript errors in browser

## Expected Results

### Successful Implementation

1. **Block Editor**: Block appears and functions correctly
2. **Settings Panel**: All options work and update preview
3. **Frontend Display**: Students display according to settings
4. **Responsive Design**: Works on all device sizes
5. **Integration**: Works with existing Students plugin

### Performance

1. **Loading Speed**: Block should load quickly
2. **Query Efficiency**: Should use efficient database queries
3. **Asset Loading**: CSS/JS should load only when needed

## Conclusion

After completing all tests, the Students Block plugin should provide a fully functional Gutenberg block for displaying students with comprehensive filtering and display options. The block should integrate seamlessly with the existing Students plugin and provide a great user experience in both the editor and frontend.
