/**
 * AJAX Settings JavaScript
 * 
 * Handles automatic saving of settings when checkboxes are changed
 */

jQuery(document).ready(function($) {
    'use strict';

    // Debounce function to prevent too many AJAX requests
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Save setting via AJAX
    function saveSetting(settingKey, settingValue, $statusElement) {
        // Show saving status
        $statusElement.html('<span class="saving">' + students_ajax_settings.strings.saving + '</span>');
        
        $.ajax({
            url: students_ajax_settings.ajax_url,
            type: 'POST',
            data: {
                action: 'save_students_setting',
                setting: settingKey,
                value: settingValue,
                nonce: students_ajax_settings.nonce
            },
            success: function(response) {
                console.log('AJAX Response:', response); // Debug log
                
                if (response.success) {
                    // Show success status
                    $statusElement.html('<span class="saved">' + students_ajax_settings.strings.saved_successfully + '</span>');
                    
                    // Show global success message
                    showGlobalMessage(students_ajax_settings.strings.saved_successfully, 'success');
                    
                    // Force cache refresh by adding timestamp to any open student pages
                    if (response.data && response.data.timestamp) {
                        // Add a small delay to ensure the setting is saved before suggesting refresh
                        setTimeout(function() {
                            showGlobalMessage('Settings updated! Please refresh any open student pages to see changes.', 'info');
                        }, 1000);
                    }
                    
                    // Hide status after 2 seconds
                    setTimeout(function() {
                        $statusElement.fadeOut();
                    }, 2000);
                } else {
                    // Show error status with specific message
                    var errorMessage = response.data && response.data.message ? response.data.message : students_ajax_settings.strings.error;
                    $statusElement.html('<span class="error">' + errorMessage + '</span>');
                    
                    // Show global error message
                    showGlobalMessage(errorMessage, 'error');
                }
            },
            error: function(xhr, status, error) {
                console.log('AJAX Error:', {xhr: xhr, status: status, error: error}); // Debug log
                
                // Show error status
                $statusElement.html('<span class="error">' + students_ajax_settings.strings.error + '</span>');
                
                // Show global error message
                showGlobalMessage(students_ajax_settings.strings.error, 'error');
            }
        });
    }

    // Debounced save function
    const debouncedSave = debounce(saveSetting, 500);

    // Show global message
    function showGlobalMessage(message, type) {
        const $statusContainer = $('#ajax-settings-status');
        const messageClass = type === 'success' ? 'notice-success' : 'notice-error';
        
        $statusContainer.html('<div class="notice ' + messageClass + ' is-dismissible"><p>' + message + '</p></div>');
        $statusContainer.fadeIn();
        
        // Auto-hide after 3 seconds
        setTimeout(function() {
            $statusContainer.fadeOut();
        }, 3000);
    }

    // Handle checkbox changes
    $('.ajax-setting-checkbox').on('change', function() {
        const $checkbox = $(this);
        const settingKey = $checkbox.data('setting');
        const settingValue = $checkbox.is(':checked');
        const $statusElement = $checkbox.closest('.ajax-setting-item').find('.setting-status');
        
        // Show status element
        $statusElement.show();
        
        // Save setting
        debouncedSave(settingKey, settingValue, $statusElement);
    });

    // Initialize status elements
    $('.setting-status').hide();
});
