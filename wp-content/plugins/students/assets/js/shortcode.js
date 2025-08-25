jQuery(document).ready(function($) {
    'use strict';
    
    // Function to load more students
    function loadMoreStudents($container, page) {
        var $grid = $container.find('.students-grid');
        var $footer = $container.find('.students-list-footer');
        var $loader = $container.find('.infinite-scroll-loader');
        
        // Get data from the grid
        var total = parseInt($grid.data('total'));
        var shown = parseInt($grid.data('shown'));
        var count = parseInt($grid.data('count'));
        var max = parseInt($grid.data('max'));
        var orderby = $grid.data('orderby');
        var order = $grid.data('order');
        var status = $grid.data('status');
        
        // Check if we've reached the maximum
        if (max > 0 && shown >= max) {
            return false;
        }
        
        // Show loading state
        if ($loader.length) {
            $loader.show();
        }
        
        // Make AJAX request
        $.ajax({
            url: students_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'load_more_students',
                nonce: students_ajax.nonce,
                page: page,
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
                    var newShown = shown + response.data.total_shown - ((page - 1) * count);
                    $grid.data('shown', newShown);
                    
                    // Hide loader
                    if ($loader.length) {
                        $loader.hide();
                    }
                    
                    // Update count display
                    var $countDisplay = $footer.find('.students-count');
                    if ($countDisplay.length) {
                        $countDisplay.text('Showing ' + newShown + ' of ' + response.data.total_found + ' students');
                    }
                    
                    // Return whether there are more students
                    return response.data.has_more;
                } else {
                    // Handle error
                    console.error('Error loading more students:', response.data);
                    if ($loader.length) {
                        $loader.hide();
                    }
                    return false;
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
                if ($loader.length) {
                    $loader.hide();
                }
                return false;
            }
        });
    }
    
    // Handle "Show More" button clicks
    $(document).on('click', '.load-more-students', function(e) {
        e.preventDefault();
        
        var $button = $(this);
        var $container = $button.closest('.students-list-shortcode');
        var page = parseInt($button.data('page'));
        
        // Show loading state
        $button.text(students_ajax.loading_text).prop('disabled', true);
        
        // Load more students
        loadMoreStudents($container, page + 1).then(function(hasMore) {
            // Update button
            $button.data('page', page + 1);
            
            if (hasMore) {
                $button.text('Show More').prop('disabled', false);
            } else {
                $button.text(students_ajax.no_more_text).prop('disabled', true);
            }
        });
    });
    
    // Infinite scroll functionality
    var scrollTimeout;
    var isLoading = false;
    
    // Function to check if element is in viewport (mobile-friendly)
    function isElementInViewport($element) {
        var rect = $element[0].getBoundingClientRect();
        var windowHeight = window.innerHeight || $(window).height();
        
        return (
            rect.top <= windowHeight &&
            rect.bottom >= 0
        );
    }
    
    // Function to check if we should load more content
    function shouldLoadMore($shortcode) {
        var $grid = $shortcode.find('.students-grid');
        var infiniteScroll = $grid.data('infinite-scroll') === 'true';
        
        if (!infiniteScroll || isLoading) {
            return false;
        }
        
        var total = parseInt($grid.data('total'));
        var shown = parseInt($grid.data('shown'));
        var count = parseInt($grid.data('count'));
        var max = parseInt($grid.data('max'));
        
        // Check if we've reached the end
        if (max > 0 && shown >= max) {
            return false;
        }
        
        if (total <= shown) {
            return false;
        }
        
        return true;
    }
    
    // Function to handle infinite scroll
    function handleInfiniteScroll() {
        $('.students-list-shortcode').each(function() {
            var $shortcode = $(this);
            
            if (!shouldLoadMore($shortcode)) {
                return;
            }
            
            var $grid = $shortcode.find('.students-grid');
            var $loader = $shortcode.find('.infinite-scroll-loader');
            
            // Check if loader is visible or near the viewport
            if ($loader.length && isElementInViewport($loader)) {
                var total = parseInt($grid.data('total'));
                var shown = parseInt($grid.data('shown'));
                var count = parseInt($grid.data('count'));
                
                // Debug logging for mobile
                console.log('Infinite scroll triggered:', {
                    total: total,
                    shown: shown,
                    count: count,
                    isLoading: isLoading,
                    userAgent: navigator.userAgent
                });
                
                isLoading = true;
                
                // Calculate next page
                var currentPage = Math.ceil(shown / count) + 1;
                
                // Load more students
                loadMoreStudents($shortcode, currentPage).then(function(hasMore) {
                    isLoading = false;
                    console.log('Infinite scroll completed, hasMore:', hasMore);
                });
            }
        });
    }
    
    // Handle scroll events (desktop and mobile)
    $(window).on('scroll', function() {
        clearTimeout(scrollTimeout);
        
        scrollTimeout = setTimeout(function() {
            handleInfiniteScroll();
        }, 150); // Slightly longer debounce for mobile
    });
    
    // Handle touch events for mobile
    $(document).on('touchmove', function() {
        clearTimeout(scrollTimeout);
        
        scrollTimeout = setTimeout(function() {
            handleInfiniteScroll();
        }, 150);
    });
    
    // Handle resize events (orientation change on mobile)
    $(window).on('resize', function() {
        clearTimeout(scrollTimeout);
        
        scrollTimeout = setTimeout(function() {
            handleInfiniteScroll();
        }, 200);
    });
    
    // Initialize any existing shortcodes on page load
    $('.students-list-shortcode').each(function() {
        var $shortcode = $(this);
        var $grid = $shortcode.find('.students-grid');
        var $button = $shortcode.find('.load-more-students');
        var infiniteScroll = $grid.data('infinite-scroll') === 'true';
        
        if ($button.length && $grid.length && !infiniteScroll) {
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
