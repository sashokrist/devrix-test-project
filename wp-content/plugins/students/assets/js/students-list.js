/**
 * Students List JavaScript
 * 
 * Handles interactive student status updates in the admin list
 */

jQuery(document).ready(function($) {
    'use strict';

    // Use event delegation to handle dynamically loaded content (pagination)
    $(document).on('change', '.student-active-checkbox', function() {
        const $checkbox = $(this);
        const studentId = $checkbox.data('student-id');
        const nonce = $checkbox.data('nonce');
        const isActive = $checkbox.is(':checked');
        const $statusMessage = $('#status-' + studentId);
        
        // Show updating status
        $statusMessage.html('<span class="updating">' + students_list_ajax.strings.updating + '</span>');
        $statusMessage.show();
        
        // Disable checkbox during update
        $checkbox.prop('disabled', true);

        $.ajax({
            url: students_list_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'update_student_status',
                student_id: studentId,
                is_active: isActive,
                nonce: nonce
            },
            success: function(response) {
                if (response.success) {
                    // Show success message
                    $statusMessage.html('<span class="success">' + response.data.message + '</span>');
                    
                    // Update the status column text - find the status span in the same row
                    const $row = $checkbox.closest('tr');
                    const $statusSpan = $row.find('span[data-colname="Status"]');
                    
                    if ($statusSpan.length > 0) {
                        if (isActive) {
                            $statusSpan.html('Active').css({
                                'color': 'green',
                                'font-weight': 'bold'
                            });
                        } else {
                            $statusSpan.html('Inactive').css({
                                'color': 'red',
                                'font-weight': 'bold'
                            });
                        }
                    }
                    
                    // Hide status message after 2 seconds
                    setTimeout(function() {
                        $statusMessage.fadeOut();
                    }, 2000);
                } else {
                    // Show error message and revert checkbox
                    $statusMessage.html('<span class="error">' + response.data.message + '</span>');
                    $checkbox.prop('checked', !isActive); // Revert to previous state
                    
                    // Hide status message after 3 seconds
                    setTimeout(function() {
                        $statusMessage.fadeOut();
                    }, 3000);
                }
            },
            error: function(xhr, status, error) {
                // Show error message and revert checkbox
                $statusMessage.html('<span class="error">' + students_list_ajax.strings.error + '</span>');
                $checkbox.prop('checked', !isActive); // Revert to previous state
                
                // Hide status message after 3 seconds
                setTimeout(function() {
                    $statusMessage.fadeOut();
                }, 3000);
            },
            complete: function() {
                // Re-enable checkbox
                $checkbox.prop('disabled', false);
            }
        });
    });

    // Initialize status messages for existing elements
    $('.student-status-message').hide();
});
