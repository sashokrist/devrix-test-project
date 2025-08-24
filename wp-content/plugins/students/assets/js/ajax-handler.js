/**
 * AJAX Handler JavaScript for Students Plugin
 *
 * @package Students
 * @version 1.0.0
 */

(function($) {
    'use strict';

    // Students AJAX Handler
    var StudentsAjaxHandler = {
        
        // Initialize the handler
        init: function() {
            this.bindEvents();
            this.setupNonce();
        },

        // Setup nonce for AJAX requests
        setupNonce: function() {
            this.nonce = students_ajax.nonce || '';
            this.ajaxUrl = students_ajax.ajax_url || '';
        },

        // Bind events
        bindEvents: function() {
            // Auto-save functionality
            $(document).on('blur', '.student-form input, .student-form textarea, .student-form select', function() {
                StudentsAjaxHandler.autoSave($(this).closest('form'));
            });

            // Form submission
            $(document).on('submit', '.student-form', function(e) {
                e.preventDefault();
                StudentsAjaxHandler.saveStudent($(this));
            });

            // Delete student
            $(document).on('click', '.delete-student', function(e) {
                e.preventDefault();
                StudentsAjaxHandler.deleteStudent($(this).data('id'));
            });

            // Search students
            $(document).on('input', '.student-search', function() {
                StudentsAjaxHandler.searchStudents($(this).val());
            });

            // Field validation
            $(document).on('blur', '.student-form input[data-validate]', function() {
                StudentsAjaxHandler.validateField($(this));
            });
        },

        // Auto-save student data
        autoSave: function($form) {
            var formData = this.serializeForm($form);
            formData.action = 'students_auto_save';
            formData.nonce = this.nonce;

            $.ajax({
                url: this.ajaxUrl,
                type: 'POST',
                data: formData,
                beforeSend: function() {
                    StudentsAjaxHandler.showLoading($form);
                },
                success: function(response) {
                    if (response.success) {
                        StudentsAjaxHandler.showMessage($form, 'Auto-saved successfully', 'success');
                        // Update post ID if it's a new post
                        if (response.data.post_id) {
                            $form.find('input[name="post_id"]').val(response.data.post_id);
                        }
                    } else {
                        StudentsAjaxHandler.showMessage($form, response.data || 'Auto-save failed', 'error');
                    }
                },
                error: function() {
                    StudentsAjaxHandler.showMessage($form, 'Auto-save failed', 'error');
                },
                complete: function() {
                    StudentsAjaxHandler.hideLoading($form);
                }
            });
        },

        // Save student
        saveStudent: function($form) {
            var formData = this.serializeForm($form);
            formData.action = 'students_save_student';
            formData.nonce = this.nonce;

            $.ajax({
                url: this.ajaxUrl,
                type: 'POST',
                data: formData,
                beforeSend: function() {
                    StudentsAjaxHandler.showLoading($form);
                    StudentsAjaxHandler.disableForm($form);
                },
                success: function(response) {
                    if (response.success) {
                        StudentsAjaxHandler.showMessage($form, response.data.message, 'success');
                        // Redirect if provided
                        if (response.data.redirect_url) {
                            setTimeout(function() {
                                window.location.href = response.data.redirect_url;
                            }, 1500);
                        }
                    } else {
                        StudentsAjaxHandler.showMessage($form, response.data || 'Save failed', 'error');
                    }
                },
                error: function() {
                    StudentsAjaxHandler.showMessage($form, 'Save failed', 'error');
                },
                complete: function() {
                    StudentsAjaxHandler.hideLoading($form);
                    StudentsAjaxHandler.enableForm($form);
                }
            });
        },

        // Delete student
        deleteStudent: function(studentId) {
            if (!confirm('Are you sure you want to delete this student?')) {
                return;
            }

            $.ajax({
                url: this.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'students_delete_student',
                    post_id: studentId,
                    nonce: this.nonce
                },
                beforeSend: function() {
                    StudentsAjaxHandler.showGlobalLoading();
                },
                success: function(response) {
                    if (response.success) {
                        StudentsAjaxHandler.showGlobalMessage(response.data.message, 'success');
                        // Remove from DOM or redirect
                        if (response.data.redirect_url) {
                            setTimeout(function() {
                                window.location.href = response.data.redirect_url;
                            }, 1500);
                        }
                    } else {
                        StudentsAjaxHandler.showGlobalMessage(response.data || 'Delete failed', 'error');
                    }
                },
                error: function() {
                    StudentsAjaxHandler.showGlobalMessage('Delete failed', 'error');
                },
                complete: function() {
                    StudentsAjaxHandler.hideGlobalLoading();
                }
            });
        },

        // Search students
        searchStudents: function(searchTerm) {
            var searchData = {
                action: 'students_search_students',
                search: searchTerm,
                nonce: this.nonce
            };

            // Add filters if they exist
            var $courseFilter = $('.course-filter');
            var $gradeFilter = $('.grade-level-filter');
            
            if ($courseFilter.length) {
                searchData.course = $courseFilter.val();
            }
            if ($gradeFilter.length) {
                searchData.grade_level = $gradeFilter.val();
            }

            $.ajax({
                url: this.ajaxUrl,
                type: 'POST',
                data: searchData,
                success: function(response) {
                    if (response.success) {
                        StudentsAjaxHandler.updateSearchResults(response.data.students);
                    }
                }
            });
        },

        // Validate field
        validateField: function($field) {
            var fieldName = $field.attr('name');
            var fieldValue = $field.val();
            var postId = $field.closest('form').find('input[name="post_id"]').val() || 0;

            $.ajax({
                url: this.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'students_validate_field',
                    field_name: fieldName,
                    field_value: fieldValue,
                    post_id: postId,
                    nonce: this.nonce
                },
                success: function(response) {
                    if (response.success) {
                        StudentsAjaxHandler.showFieldValidation($field, '', 'valid');
                    } else {
                        StudentsAjaxHandler.showFieldValidation($field, response.data.message, 'invalid');
                    }
                }
            });
        },

        // Serialize form data
        serializeForm: function($form) {
            var formData = {};
            $form.find('input, textarea, select').each(function() {
                var $field = $(this);
                var name = $field.attr('name');
                var value = $field.val();
                
                if (name) {
                    // Handle arrays (like checkboxes)
                    if (name.endsWith('[]')) {
                        name = name.slice(0, -2);
                        if (!formData[name]) {
                            formData[name] = [];
                        }
                        formData[name].push(value);
                    } else {
                        formData[name] = value;
                    }
                }
            });
            return formData;
        },

        // Show loading indicator
        showLoading: function($form) {
            $form.find('.submit-button').prop('disabled', true).text('Saving...');
        },

        // Hide loading indicator
        hideLoading: function($form) {
            $form.find('.submit-button').prop('disabled', false).text('Save Student');
        },

        // Show global loading
        showGlobalLoading: function() {
            $('body').append('<div id="students-loading" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center; justify-content: center;"><div style="background: white; padding: 20px; border-radius: 5px;">Loading...</div></div>');
        },

        // Hide global loading
        hideGlobalLoading: function() {
            $('#students-loading').remove();
        },

        // Show message
        showMessage: function($form, message, type) {
            var $messageDiv = $form.find('.message');
            if (!$messageDiv.length) {
                $messageDiv = $('<div class="message"></div>');
                $form.prepend($messageDiv);
            }
            
            $messageDiv.removeClass('success error').addClass(type).text(message).show();
            
            setTimeout(function() {
                $messageDiv.fadeOut();
            }, 3000);
        },

        // Show global message
        showGlobalMessage: function(message, type) {
            var $messageDiv = $('#students-global-message');
            if (!$messageDiv.length) {
                $messageDiv = $('<div id="students-global-message" style="position: fixed; top: 20px; right: 20px; z-index: 10000; padding: 15px; border-radius: 5px; color: white;"></div>');
                $('body').append($messageDiv);
            }
            
            $messageDiv.removeClass('success error').addClass(type).text(message).show();
            
            setTimeout(function() {
                $messageDiv.fadeOut();
            }, 3000);
        },

        // Show field validation
        showFieldValidation: function($field, message, type) {
            var $validationDiv = $field.siblings('.field-validation');
            if (!$validationDiv.length) {
                $validationDiv = $('<div class="field-validation"></div>');
                $field.after($validationDiv);
            }
            
            $validationDiv.removeClass('valid invalid').addClass(type).text(message).show();
            
            if (type === 'valid') {
                setTimeout(function() {
                    $validationDiv.fadeOut();
                }, 2000);
            }
        },

        // Update search results
        updateSearchResults: function(students) {
            var $resultsContainer = $('.students-search-results');
            if (!$resultsContainer.length) {
                return;
            }

            var html = '';
            if (students.length > 0) {
                students.forEach(function(student) {
                    html += '<div class="student-result">';
                    html += '<h4><a href="' + student.url + '">' + student.title + '</a></h4>';
                    html += '<p>ID: ' + (student.student_id || 'N/A') + '</p>';
                    html += '<p>Email: ' + (student.student_email || 'N/A') + '</p>';
                    if (student.courses && student.courses.length > 0) {
                        html += '<p>Courses: ' + student.courses.join(', ') + '</p>';
                    }
                    if (student.grade_levels && student.grade_levels.length > 0) {
                        html += '<p>Grade Levels: ' + student.grade_levels.join(', ') + '</p>';
                    }
                    html += '</div>';
                });
            } else {
                html = '<p>No students found.</p>';
            }
            
            $resultsContainer.html(html);
        },

        // Disable form
        disableForm: function($form) {
            $form.find('input, textarea, select, button').prop('disabled', true);
        },

        // Enable form
        enableForm: function($form) {
            $form.find('input, textarea, select, button').prop('disabled', false);
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        StudentsAjaxHandler.init();
    });

})(jQuery);
