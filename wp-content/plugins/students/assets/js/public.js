/**
 * Students Plugin - Public JavaScript
 *
 * @package Students
 * @since 1.0.0
 */

(function($) {
    'use strict';

    // Initialize when document is ready
    $(document).ready(function() {
        StudentsPublic.init();
    });

    // Students Public Object
    var StudentsPublic = {
        
        /**
         * Initialize the plugin
         */
        init: function() {
            this.bindEvents();
            this.initStudentCards();
        },

        /**
         * Bind event handlers
         */
        bindEvents: function() {
            // Student card hover effects
            $(document).on('mouseenter', '.student-card', function() {
                $(this).addClass('hover');
            });

            $(document).on('mouseleave', '.student-card', function() {
                $(this).removeClass('hover');
            });

            // Student photo click to enlarge
            $(document).on('click', '.student-photo img', function(e) {
                e.preventDefault();
                StudentsPublic.openPhotoModal($(this).attr('src'), $(this).attr('alt'));
            });

            // Close modal on background click
            $(document).on('click', '.students-modal-overlay', function() {
                StudentsPublic.closePhotoModal();
            });

            // Close modal on escape key
            $(document).on('keydown', function(e) {
                if (e.keyCode === 27) { // ESC key
                    StudentsPublic.closePhotoModal();
                }
            });
        },

        /**
         * Initialize student cards
         */
        initStudentCards: function() {
            // Add loading animation
            $('.student-card').each(function(index) {
                var $card = $(this);
                setTimeout(function() {
                    $card.addClass('loaded');
                }, index * 100);
            });
        },

        /**
         * Open photo modal
         */
        openPhotoModal: function(src, alt) {
            var modal = $('<div class="students-modal-overlay">' +
                '<div class="students-modal">' +
                '<div class="students-modal-close">&times;</div>' +
                '<img src="' + src + '" alt="' + alt + '">' +
                '</div>' +
                '</div>');

            $('body').append(modal);
            
            // Close on X click
            modal.find('.students-modal-close').on('click', function() {
                StudentsPublic.closePhotoModal();
            });

            // Animate in
            setTimeout(function() {
                modal.addClass('active');
            }, 10);
        },

        /**
         * Close photo modal
         */
        closePhotoModal: function() {
            $('.students-modal-overlay').removeClass('active');
            setTimeout(function() {
                $('.students-modal-overlay').remove();
            }, 300);
        },

        /**
         * Search students (if search is enabled)
         */
        searchStudents: function(query) {
            if (!query) {
                $('.student-card').show();
                return;
            }

            $('.student-card').each(function() {
                var $card = $(this);
                var text = $card.text().toLowerCase();
                var matches = text.indexOf(query.toLowerCase()) !== -1;
                
                if (matches) {
                    $card.show();
                } else {
                    $card.hide();
                }
            });
        },

        /**
         * Filter students by taxonomy
         */
        filterStudents: function(taxonomy, terms) {
            $('.student-card').each(function() {
                var $card = $(this);
                var hasTerm = false;

                if (terms.length === 0) {
                    hasTerm = true;
                } else {
                    $card.find('[data-taxonomy="' + taxonomy + '"]').each(function() {
                        var term = $(this).data('term');
                        if (terms.indexOf(term) !== -1) {
                            hasTerm = true;
                            return false;
                        }
                    });
                }

                if (hasTerm) {
                    $card.show();
                } else {
                    $card.hide();
                }
            });
        }
    };

    // Add modal styles dynamically
    var modalStyles = `
        <style>
        .students-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .students-modal-overlay.active {
            opacity: 1;
        }
        
        .students-modal {
            position: relative;
            max-width: 90%;
            max-height: 90%;
        }
        
        .students-modal img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }
        
        .students-modal-close {
            position: absolute;
            top: -40px;
            right: 0;
            color: white;
            font-size: 30px;
            cursor: pointer;
            background: none;
            border: none;
            padding: 0;
            width: 30px;
            height: 30px;
            line-height: 30px;
            text-align: center;
        }
        
        .student-card {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.5s ease, transform 0.5s ease;
        }
        
        .student-card.loaded {
            opacity: 1;
            transform: translateY(0);
        }
        
        .student-card.hover {
            transform: translateY(-5px);
        }
        </style>
    `;

    $('head').append(modalStyles);

})(jQuery);
