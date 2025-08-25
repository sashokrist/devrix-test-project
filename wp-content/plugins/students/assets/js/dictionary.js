/**
 * Dictionary Search JavaScript
 * 
 * Handles AJAX search functionality for Oxford dictionary
 */

jQuery(document).ready(function($) {
    'use strict';

    const $searchInput = $('#dictionary-search-input');
    const $searchButton = $('#dictionary-search-button');
    const $loading = $('#dictionary-loading');
    const $results = $('#dictionary-results');
    const $error = $('#dictionary-error');

    /**
     * Perform dictionary search
     */
    function performSearch() {
        console.log('performSearch called'); // Debug log
        const word = $searchInput.val().trim();
        console.log('Search word:', word); // Debug log
        
        if (empty(word)) {
            showError(dictionary_ajax.strings.enter_word);
            return;
        }

        // Show loading state
        showLoading();
        hideError();
        clearResults();

        console.log('Making AJAX request to:', dictionary_ajax.ajax_url); // Debug log
        console.log('AJAX data:', {action: 'search_oxford_dictionary', word: word, nonce: dictionary_ajax.nonce}); // Debug log

        $.ajax({
            url: dictionary_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'search_oxford_dictionary',
                word: word,
                nonce: dictionary_ajax.nonce
            },
            success: function(response) {
                hideLoading();
                console.log('AJAX response:', response); // Debug log
                
                if (response.success) {
                    // The data is nested: response.data.data
                    displayResults(response.data.data, response.data.cached);
                } else {
                    showError(response.data.message || dictionary_ajax.strings.error);
                }
            },
            error: function(xhr, status, error) {
                hideLoading();
                showError(dictionary_ajax.strings.error);
                console.error('Dictionary search error:', {xhr: xhr, status: status, error: error});
            }
        });
    }

    /**
     * Display search results
     */
    function displayResults(data, cached = false) {
        console.log('Displaying results:', data, 'cached:', cached); // Debug log
        
        let html = '<div class="dictionary-result">';
        
        // Word header
        html += '<h2 class="dictionary-word">' + escapeHtml(data.word) + '</h2>';
        
        // Cache status indicator
        if (cached) {
            html += '<div class="dictionary-cache-status">';
            html += '<span class="cache-indicator">📋 Cached result</span>';
            html += '</div>';
        }
        
        // Part of speech
        if (data.parts_of_speech && data.parts_of_speech.length > 0) {
            html += '<div class="dictionary-pos">';
            html += '<strong>' + escapeHtml(data.parts_of_speech.join(', ')) + '</strong>';
            html += '</div>';
        }
        
        // Pronunciation
        if (data.pronunciations && data.pronunciations.length > 0) {
            html += '<div class="dictionary-pronunciation">';
            html += '<strong>Pronunciation:</strong> ';
            html += escapeHtml(data.pronunciations.join(', '));
            html += '</div>';
        }
        
        // Definitions
        if (data.definitions && data.definitions.length > 0) {
            html += '<div class="dictionary-definitions">';
            html += '<h3>Definitions:</h3>';
            html += '<ol>';
            data.definitions.forEach(function(definition) {
                html += '<li>' + escapeHtml(definition) + '</li>';
            });
            html += '</ol>';
            html += '</div>';
        }
        
        // Examples
        if (data.examples && data.examples.length > 0) {
            html += '<div class="dictionary-examples">';
            html += '<h3>Examples:</h3>';
            html += '<ul>';
            data.examples.forEach(function(example) {
                html += '<li>' + escapeHtml(example) + '</li>';
            });
            html += '</ul>';
            html += '</div>';
        }
        
        // Raw content (fallback)
        if (data.raw_content) {
            html += '<div class="dictionary-raw-content">';
            html += '<h3>Definition:</h3>';
            html += '<div class="raw-content">' + escapeHtml(data.raw_content) + '</div>';
            html += '</div>';
        }
        
        // Source link
        if (data.source_url) {
            html += '<div class="dictionary-source">';
            html += '<a href="' + escapeHtml(data.source_url) + '" target="_blank" class="button button-secondary">';
            html += 'View on Oxford Dictionary';
            html += '</a>';
            html += '</div>';
        }
        
        html += '</div>';
        
        $results.html(html);
        $results.show();
    }

    /**
     * Show loading state
     */
    function showLoading() {
        $loading.show();
        $searchButton.prop('disabled', true);
    }

    /**
     * Hide loading state
     */
    function hideLoading() {
        $loading.hide();
        $searchButton.prop('disabled', false);
    }

    /**
     * Show error message
     */
    function showError(message) {
        $error.html('<div class="notice notice-error"><p>' + escapeHtml(message) + '</p></div>');
        $error.show();
    }

    /**
     * Hide error message
     */
    function hideError() {
        $error.hide();
    }

    /**
     * Clear results
     */
    function clearResults() {
        $results.hide().empty();
    }

    /**
     * Escape HTML
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Check if value is empty
     */
    function empty(value) {
        return value === null || value === undefined || value === '';
    }

    // Add initialization debugging
    console.log('Dictionary script loaded');
    console.log('Elements found:', {
        searchInput: $searchInput.length,
        searchButton: $searchButton.length,
        loading: $loading.length,
        results: $results.length,
        error: $error.length
    });

    // Event handlers
    $searchButton.on('click', performSearch);
    
    $searchInput.on('keypress', function(e) {
        if (e.which === 13) { // Enter key
            performSearch();
        }
    });

    // Cache settings functionality
    $('#save-cache-settings').on('click', function() {
        const $button = $(this);
        const $status = $('#cache-settings-status');
        const cacheDuration = $('#dictionary_cache_duration').val();
        
        $button.prop('disabled', true).text(dictionary_ajax.strings.saving);
        $status.hide();
        
        $.ajax({
            url: dictionary_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'save_dictionary_cache_settings',
                cache_duration: cacheDuration,
                nonce: dictionary_ajax.cache_nonce
            },
            success: function(response) {
                if (response.success) {
                    $status.html('<div class="notice notice-success"><p>' + response.data.message + '</p></div>').show();
                } else {
                    $status.html('<div class="notice notice-error"><p>' + response.data.message + '</p></div>').show();
                }
            },
            error: function() {
                $status.html('<div class="notice notice-error"><p>' + dictionary_ajax.strings.error + '</p></div>').show();
            },
            complete: function() {
                $button.prop('disabled', false).text('Save Cache Settings');
                setTimeout(function() {
                    $status.fadeOut();
                }, 3000);
            }
        });
    });
    
    $('#clear-dictionary-cache').on('click', function() {
        const $button = $(this);
        const $status = $('#cache-settings-status');
        
        if (!confirm('Are you sure you want to clear all dictionary cache? This will force fresh data to be fetched from Oxford dictionary.')) {
            return;
        }
        
        $button.prop('disabled', true).text(dictionary_ajax.strings.clearing);
        $status.hide();
        
        $.ajax({
            url: dictionary_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'clear_dictionary_cache',
                nonce: dictionary_ajax.cache_nonce
            },
            success: function(response) {
                if (response.success) {
                    $status.html('<div class="notice notice-success"><p>' + response.data.message + '</p></div>').show();
                } else {
                    $status.html('<div class="notice notice-error"><p>' + response.data.message + '</p></div>').show();
                }
            },
            error: function() {
                $status.html('<div class="notice notice-error"><p>' + dictionary_ajax.strings.error + '</p></div>').show();
            },
            complete: function() {
                $button.prop('disabled', false).text('Clear All Cache');
                setTimeout(function() {
                    $status.fadeOut();
                }, 3000);
            }
        });
    });

    // Auto-focus on search input
    $searchInput.focus();
});
