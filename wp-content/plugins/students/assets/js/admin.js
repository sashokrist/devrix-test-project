/**
 * Students Plugin - Admin JavaScript
 *
 * @package Students
 * @since 1.0.0
 */

(function($) {
    'use strict';

    // Initialize when document is ready
    $(document).ready(function() {
        StudentsAdmin.init();
    });

    // Students Admin Object
    var StudentsAdmin = {
        
        /**
         * Initialize the plugin
         */
        init: function() {
            this.bindEvents();
            this.initMetaBoxes();
            this.initSettings();
        },

        /**
         * Bind event handlers
         */
        bindEvents: function() {
            // Featured image change
            $(document).on('click', '#set-post-thumbnail', function() {
                StudentsAdmin.handleFeaturedImage();
            });

            // Student ID auto-generation
            $(document).on('blur', '#student_id', function() {
                StudentsAdmin.validateStudentId($(this));
            });

            // Email validation
            $(document).on('blur', '#student_email', function() {
                StudentsAdmin.validateEmail($(this));
            });

            // Phone number formatting
            $(document).on('input', '#student_phone', function() {
                StudentsAdmin.formatPhoneNumber($(this));
            });

            // Date of birth validation
            $(document).on('change', '#student_dob', function() {
                StudentsAdmin.validateDateOfBirth($(this));
            });

            // Settings form submission
            $(document).on('submit', '.students-settings form', function(e) {
                StudentsAdmin.handleSettingsSubmit(e);
            });
        },

        /**
         * Initialize meta boxes
         */
        initMetaBoxes: function() {
            // Auto-generate student ID if empty
            if ($('#student_id').val() === '') {
                StudentsAdmin.generateStudentId();
            }

            // Show photo preview if exists
            StudentsAdmin.updatePhotoPreview();
        },

        /**
         * Initialize settings page
         */
        initSettings: function() {
            // Add settings page enhancements
            $('.students-settings .form-table').addClass('enhanced');
            
            // Add help tooltips
            StudentsAdmin.addHelpTooltips();
        },

        /**
         * Handle featured image
         */
        handleFeaturedImage: function() {
            // Add custom behavior for student photos
            setTimeout(function() {
                StudentsAdmin.updatePhotoPreview();
            }, 1000);
        },

        /**
         * Update photo preview
         */
        updatePhotoPreview: function() {
            var $preview = $('.student-photo-preview');
            var $thumbnail = $('#post-thumbnail img');
            
            if ($thumbnail.length > 0) {
                if ($preview.length === 0) {
                    $preview = $('<div class="student-photo-preview"></div>');
                    $('#student_details').prepend($preview);
                }
                $preview.html($thumbnail.clone());
            } else {
                $preview.remove();
            }
        },

        /**
         * Generate student ID
         */
        generateStudentId: function() {
            var year = new Date().getFullYear();
            var random = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
            var studentId = 'STU' + year + random;
            $('#student_id').val(studentId);
        },

        /**
         * Validate student ID
         */
        validateStudentId: function($field) {
            var value = $field.val();
            var isValid = /^[A-Z]{3}\d{7}$/.test(value);
            
            if (!isValid && value !== '') {
                StudentsAdmin.showFieldError($field, 'Student ID should be in format: STU2024001');
            } else {
                StudentsAdmin.clearFieldError($field);
            }
        },

        /**
         * Validate email
         */
        validateEmail: function($field) {
            var value = $field.val();
            var isValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
            
            if (!isValid && value !== '') {
                StudentsAdmin.showFieldError($field, 'Please enter a valid email address');
            } else {
                StudentsAdmin.clearFieldError($field);
            }
        },

        /**
         * Format phone number
         */
        formatPhoneNumber: function($field) {
            var value = $field.val().replace(/\D/g, '');
            
            if (value.length >= 10) {
                var formatted = '(' + value.substring(0, 3) + ') ' + 
                               value.substring(3, 6) + '-' + 
                               value.substring(6, 10);
                $field.val(formatted);
            }
        },

        /**
         * Validate date of birth
         */
        validateDateOfBirth: function($field) {
            var value = $field.val();
            if (value) {
                var dob = new Date(value);
                var today = new Date();
                var age = today.getFullYear() - dob.getFullYear();
                
                if (age < 5 || age > 100) {
                    StudentsAdmin.showFieldError($field, 'Please enter a valid date of birth (age 5-100)');
                } else {
                    StudentsAdmin.clearFieldError($field);
                }
            }
        },

        /**
         * Show field error
         */
        showFieldError: function($field, message) {
            StudentsAdmin.clearFieldError($field);
            
            var $error = $('<div class="field-error">' + message + '</div>');
            $field.after($error);
            $field.addClass('error');
        },

        /**
         * Clear field error
         */
        clearFieldError: function($field) {
            $field.siblings('.field-error').remove();
            $field.removeClass('error');
        },

        /**
         * Handle settings submit
         */
        handleSettingsSubmit: function(e) {
            // Add loading state
            var $form = $(e.target);
            var $submit = $form.find('input[type="submit"]');
            
            $submit.prop('disabled', true).val('Saving...');
            
            // Re-enable after a short delay (form will submit normally)
            setTimeout(function() {
                $submit.prop('disabled', false).val('Save Changes');
            }, 2000);
        },

        /**
         * Add help tooltips
         */
        addHelpTooltips: function() {
            $('.students-settings .description').each(function() {
                var $desc = $(this);
                var text = $desc.text();
                
                if (text.length > 50) {
                    var shortText = text.substring(0, 50) + '...';
                    var $tooltip = $('<span class="help-tooltip" title="' + text + '">?</span>');
                    
                    $desc.text(shortText);
                    $desc.after($tooltip);
                }
            });
        },

        /**
         * Show admin notice
         */
        showNotice: function(message, type) {
            type = type || 'success';
            
            var $notice = $('<div class="students-notice ' + type + '">' + message + '</div>');
            $('.wrap h1').after($notice);
            
            // Auto-remove after 5 seconds
            setTimeout(function() {
                $notice.fadeOut(function() {
                    $(this).remove();
                });
            }, 5000);
        }
    };

    // Add admin styles dynamically
    var adminStyles = `
        <style>
        .field-error {
            color: #dc3232;
            font-size: 12px;
            margin-top: 5px;
            font-style: italic;
        }
        
        .field-error + input,
        .field-error + textarea {
            border-color: #dc3232;
        }
        
        .help-tooltip {
            display: inline-block;
            width: 16px;
            height: 16px;
            background: #0073aa;
            color: white;
            border-radius: 50%;
            text-align: center;
            line-height: 16px;
            font-size: 10px;
            cursor: help;
            margin-left: 5px;
        }
        
        .students-settings.enhanced .form-table {
            border-radius: 8px;
            overflow: hidden;
        }
        
        .students-settings.enhanced .form-table th {
            background: linear-gradient(135deg, #f9f9f9 0%, #f0f0f0 100%);
        }
        
        .student-photo-preview {
            margin-bottom: 15px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .student-photo-preview img {
            display: block;
            width: 100%;
            height: auto;
        }
        </style>
    `;

    $('head').append(adminStyles);

})(jQuery);
