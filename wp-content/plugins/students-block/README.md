# Students Block

A WordPress Gutenberg block for displaying students with filtering options.

## Description

The Students Block plugin provides a Gutenberg block that allows you to display students on your WordPress pages and posts with various filtering and display options. This block integrates with the existing Students plugin to show student information in a beautiful, responsive layout.

## Features

- **Number of Students**: Choose how many students to display (1-20)
- **Status Filtering**: Filter by Active, Inactive, or All students
- **Specific Student**: Option to show just one specific student
- **Ordering Options**: Sort by name, date created, date modified, or menu order
- **Responsive Design**: Beautiful, mobile-friendly student cards
- **Real-time Preview**: See changes immediately in the block editor

## Requirements

- WordPress 5.0 or higher
- Students plugin (must be installed and activated)
- PHP 7.4 or higher

## Installation

1. Upload the `students-block` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Install dependencies and build assets:
   ```bash
   cd wp-content/plugins/students-block
   npm install
   npm run build
   ```

## Usage

### Adding the Block

1. Edit any page or post in the WordPress block editor
2. Click the "+" button to add a new block
3. Search for "Students Display" or look in the "Widgets" category
4. Click to add the block

### Block Settings

The block includes a settings panel in the sidebar with the following options:

#### General Settings
- **Show specific student**: Toggle to show only one student
- **Select Student**: Choose a specific student from the dropdown (when "Show specific student" is enabled)

#### Multiple Students Settings
- **Number of students to show**: Set how many students to display (1-20)
- **Status filter**: Choose between Active, Inactive, or All students
- **Order by**: Sort by Name, Date created, Date modified, or Menu order
- **Order**: Choose Ascending or Descending order

### Block Display

The block displays students in a responsive grid layout with:

- Student photo (or placeholder if no image)
- Student name (linked to student page)
- Class/Grade information
- Email address
- Phone number
- Status indicator (Active/Inactive)
- "View All Students" link (when showing multiple students)

## Development

### Building Assets

To build the JavaScript and CSS assets:

```bash
npm run build
```

For development with hot reloading:

```bash
npm run start
```

### File Structure

```
students-block/
├── src/
│   ├── index.js          # Main block registration
│   ├── edit.js           # Editor component
│   ├── save.js           # Save component
│   ├── editor.scss       # Editor styles
│   └── style.scss        # Frontend styles
├── build/                # Compiled assets (generated)
├── students-block.php    # Main plugin file
├── package.json          # Dependencies and scripts
├── webpack.config.js     # Build configuration
└── README.md            # This file
```

## Changelog

### 1.0.0
- Initial release
- Gutenberg block with filtering options
- Responsive student card layout
- Integration with Students plugin

## Support

For support and questions, please refer to the plugin documentation or contact the development team.

## License

This plugin is licensed under the GPL v2 or later.
