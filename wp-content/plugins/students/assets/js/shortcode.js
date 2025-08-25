jQuery(document).ready(function($) {
    'use strict';
    
    // Handle "Show More" button clicks
    $(document).on('click', '.load-more-students', function(e) {
        e.preventDefault();
        
        var $button = $(this);
        var $grid = $button.closest('.students-list-shortcode').find('.students-grid');
        var $footer = $button.closest('.students-list-footer');
        
        // Get data from the grid
        var total = parseInt($grid.data('total'));
        var shown = parseInt($grid.data('shown'));
        var count = parseInt($grid.data('count'));
        var max = parseInt($grid.data('max'));
        var orderby = $grid.data('orderby');
        var order = $grid.data('order');
        var status = $grid.data('status');
        var page = parseInt($button.data('page'));
        
        // Check if we've reached the maximum
        if (max > 0 && shown >= max) {
            $button.text(students_ajax.no_more_text).prop('disabled', true);
            return;
        }
        
        // Show loading state
        $button.text(students_ajax.loading_text).prop('disabled', true);
        
        // Make AJAX request
        $.ajax({
            url: students_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'load_more_students',
                nonce: students_ajax.nonce,
                page: page + 1,
                count: count,
                max: max,
                orderby: orderby,
                order: order,
                status: status
            },
            success: function(response) {
                if (response.success) {
                    // Append new students to the grid
                    $grid.append(response.data.html);
                    
                    // Update data attributes
                    var newShown = shown + response.data.total_shown - (page * count);
                    $grid.data('shown', newShown);
                    
                    // Update button
                    $button.data('page', page + 1);
                    
                    // Check if there are more students to load
                    if (response.data.has_more) {
                        $button.text('Show More').prop('disabled', false);
                    } else {
                        $button.text(students_ajax.no_more_text).prop('disabled', true);
                    }
                    
                    // Update count display
                    var $countDisplay = $footer.find('.students-count');
                    if ($countDisplay.length) {
                        $countDisplay.text('Showing ' + newShown + ' of ' + response.data.total_found + ' students');
                    }
                } else {
                    // Handle error
                    console.error('Error loading more students:', response.data);
                    $button.text('Error loading students').prop('disabled', true);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
                $button.text('Error loading students').prop('disabled', true);
            }
        });
    });
    
    // Initialize any existing shortcodes on page load
    $('.students-list-shortcode').each(function() {
        var $shortcode = $(this);
        var $grid = $shortcode.find('.students-grid');
        var $button = $shortcode.find('.load-more-students');
        
        if ($button.length && $grid.length) {
            var total = parseInt($grid.data('total'));
            var count = parseInt($grid.data('count'));
            var max = parseInt($grid.data('max'));
            
            // Hide button if no more students to show
            if (total <= count || (max > 0 && count >= max)) {
                $button.hide();
            }
        }
    });
});
