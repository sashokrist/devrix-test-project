<?php
/**
 * Students Dictionary Class
 * 
 * Handles Oxford dictionary integration and search functionality
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Students_Dictionary {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_dictionary_menu' ) );
        add_action( 'wp_ajax_search_oxford_dictionary', array( $this, 'ajax_search_dictionary' ) );
        add_action( 'wp_ajax_save_dictionary_cache_settings', array( $this, 'ajax_save_cache_settings' ) );
        add_action( 'wp_ajax_clear_dictionary_cache', array( $this, 'ajax_clear_dictionary_cache' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_dictionary_scripts' ) );
    }
    
    /**
     * Add dictionary menu
     */
    public function add_dictionary_menu() {
        add_menu_page(
            __( 'Dictionary', 'students' ),
            __( 'Dictionary', 'students' ),
            'manage_options',
            'students-dictionary',
            array( $this, 'dictionary_page' ),
            'dashicons-book-alt',
            26 // Position after Students menu (25)
        );
    }
    
    /**
     * Dictionary page content
     */
    public function dictionary_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <p class="description"><?php esc_html_e( 'Search the Oxford Learner\'s Dictionary for word definitions, pronunciations, and examples.', 'students' ); ?></p>
            
            <div class="dictionary-search-container">
                <div class="dictionary-search-form">
                    <input type="text" 
                           id="dictionary-search-input" 
                           placeholder="<?php esc_attr_e( 'Enter a word to search...', 'students' ); ?>" 
                           class="regular-text" />
                    <button type="button" id="dictionary-search-button" class="button button-primary">
                        <?php esc_html_e( 'Search', 'students' ); ?>
                    </button>
                    <span id="dictionary-loading" class="spinner" style="float: none; margin-top: 0; display: none;"></span>
                </div>
                
                <div id="dictionary-results" class="dictionary-results">
                    <!-- Results will be displayed here -->
                </div>
                
                <div id="dictionary-error" class="dictionary-error" style="display: none;">
                    <!-- Error messages will be displayed here -->
                </div>
            </div>
            
            <?php $this->add_cache_settings(); ?>
        </div>
        <?php
    }
    
    /**
     * Enqueue dictionary scripts and styles
     */
    public function enqueue_dictionary_scripts( $hook ) {
        if ( 'toplevel_page_students-dictionary' !== $hook ) {
            return;
        }
        
        wp_enqueue_script(
            'students-dictionary',
            STUDENTS_PLUGIN_URL . 'assets/js/dictionary.js',
            array( 'jquery' ),
            STUDENTS_VERSION,
            true
        );
        
        wp_enqueue_style(
            'students-dictionary',
            STUDENTS_PLUGIN_URL . 'assets/css/dictionary.css',
            array(),
            STUDENTS_VERSION
        );
        
        wp_localize_script( 'students-dictionary', 'dictionary_ajax', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'dictionary_search_nonce' ),
            'cache_nonce' => wp_create_nonce( 'dictionary_cache_settings_nonce' ),
            'strings' => array(
                'searching' => __( 'Searching...', 'students' ),
                'no_results' => __( 'No results found for this word.', 'students' ),
                'error' => __( 'An error occurred while searching. Please try again.', 'students' ),
                'enter_word' => __( 'Please enter a word to search.', 'students' ),
                'cache_saved' => __( 'Cache settings saved successfully!', 'students' ),
                'cache_cleared' => __( 'Cache cleared successfully!', 'students' ),
                'saving' => __( 'Saving...', 'students' ),
                'clearing' => __( 'Clearing...', 'students' )
            )
        ) );
    }
    
        /**
     * AJAX handler for dictionary search
     */
    public function ajax_search_dictionary() {
        // Verify nonce
        if ( ! wp_verify_nonce( $_POST['nonce'], 'dictionary_search_nonce' ) ) {
            wp_send_json_error( array(
                'message' => __( 'Security check failed.', 'students' )
            ) );
        }

        // Check permissions
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array(
                'message' => __( 'Insufficient permissions.', 'students' )
            ) );
        }

        $word = sanitize_text_field( $_POST['word'] );
        
        if ( empty( $word ) ) {
            wp_send_json_error( array(
                'message' => __( 'Please enter a word to search.', 'students' )
            ) );
        }
        
        // Check if we have cached results
        $cache_key = 'dictionary_' . sanitize_key( $word );
        $cached_result = get_transient( $cache_key );
        
        if ( false !== $cached_result ) {
            // Return cached results
            wp_send_json_success( array(
                'word' => $word,
                'data' => $cached_result,
                'cached' => true
            ) );
        }
        
        // Fetch dictionary data from Oxford
        $result = $this->fetch_oxford_dictionary_data( $word );
        
        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array(
                'message' => $result->get_error_message()
            ) );
        }
        
        // Cache the results
        $cache_duration = $this->get_cache_duration();
        set_transient( $cache_key, $result, $cache_duration );
        
        wp_send_json_success( array(
            'word' => $word,
            'data' => $result,
            'cached' => false
        ) );
    }
    
    /**
     * Fetch Oxford dictionary data
     */
    private function fetch_oxford_dictionary_data( $word ) {
        // Clean the word for URL
        $clean_word = strtolower( trim( $word ) );
        $clean_word = preg_replace( '/[^a-z0-9\s-]/', '', $clean_word );
        $clean_word = preg_replace( '/\s+/', '-', $clean_word );
        
        $url = 'https://www.oxfordlearnersdictionaries.com/definition/english/' . $clean_word;
        
        // Use WordPress HTTP API
        $response = wp_remote_get( $url, array(
            'timeout' => 30,
            'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
        ) );
        
        if ( is_wp_error( $response ) ) {
            return new WP_Error( 'fetch_error', __( 'Failed to fetch dictionary data.', 'students' ) );
        }
        
        $body = wp_remote_retrieve_body( $response );
        $status_code = wp_remote_retrieve_response_code( $response );
        
        if ( $status_code !== 200 ) {
            return new WP_Error( 'not_found', __( 'Word not found in dictionary.', 'students' ) );
        }
        
        // Parse the HTML content
        return $this->parse_oxford_content( $body, $word );
    }
    
    /**
     * Parse Oxford dictionary HTML content
     */
    private function parse_oxford_content( $html, $word ) {
        // Create a DOMDocument to parse the HTML
        $dom = new DOMDocument();
        
        // Suppress warnings for malformed HTML
        libxml_use_internal_errors( true );
        $dom->loadHTML( $html );
        libxml_clear_errors();
        
        $xpath = new DOMXPath( $dom );
        
        // Extract definition
        $definition_nodes = $xpath->query( '//span[@class="def"]' );
        $definitions = array();
        foreach ( $definition_nodes as $node ) {
            $definitions[] = trim( $node->textContent );
        }
        
        // Extract pronunciation
        $pronunciation_nodes = $xpath->query( '//span[@class="phon"]' );
        $pronunciations = array();
        foreach ( $pronunciation_nodes as $node ) {
            $pronunciations[] = trim( $node->textContent );
        }
        
        // Extract examples
        $example_nodes = $xpath->query( '//span[@class="x"]' );
        $examples = array();
        foreach ( $example_nodes as $node ) {
            $examples[] = trim( $node->textContent );
        }
        
        // Extract part of speech
        $pos_nodes = $xpath->query( '//span[@class="pos"]' );
        $parts_of_speech = array();
        foreach ( $pos_nodes as $node ) {
            $parts_of_speech[] = trim( $node->textContent );
        }
        
        // If we couldn't extract structured data, return the raw content
        if ( empty( $definitions ) && empty( $pronunciations ) && empty( $examples ) ) {
            // Try to extract any meaningful content
            $content_nodes = $xpath->query( '//div[@class="entry"] | //div[@class="definition"] | //div[@class="sense"]' );
            $raw_content = '';
            foreach ( $content_nodes as $node ) {
                $raw_content .= $node->textContent . "\n";
            }
            
            if ( ! empty( $raw_content ) ) {
                return array(
                    'word' => $word,
                    'raw_content' => trim( $raw_content ),
                    'source_url' => 'https://www.oxfordlearnersdictionaries.com/definition/english/' . strtolower( str_replace( ' ', '-', $word ) )
                );
            }
        }
        
        return array(
            'word' => $word,
            'definitions' => $definitions,
            'pronunciations' => $pronunciations,
            'examples' => $examples,
            'parts_of_speech' => $parts_of_speech,
            'source_url' => 'https://www.oxfordlearnersdictionaries.com/definition/english/' . strtolower( str_replace( ' ', '-', $word ) )
        );
    }
    
    /**
     * Get cache duration in seconds
     */
    private function get_cache_duration() {
        $options = get_option( 'students_options', array() );
        $cache_duration = isset( $options['dictionary_cache_duration'] ) ? $options['dictionary_cache_duration'] : 3600; // Default 1 hour
        
        return intval( $cache_duration );
    }
    
    /**
     * Add cache settings to the dictionary page
     */
    private function add_cache_settings() {
        $options = get_option( 'students_options', array() );
        $current_duration = isset( $options['dictionary_cache_duration'] ) ? $options['dictionary_cache_duration'] : 3600;
        
        ?>
        <div class="dictionary-cache-settings">
            <h3><?php esc_html_e( 'Cache Settings', 'students' ); ?></h3>
            <p><?php esc_html_e( 'Configure how long dictionary results are cached to improve performance.', 'students' ); ?></p>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="dictionary_cache_duration"><?php esc_html_e( 'Cache Duration', 'students' ); ?></label>
                    </th>
                    <td>
                        <select id="dictionary_cache_duration" name="dictionary_cache_duration">
                            <option value="10" <?php selected( $current_duration, 10 ); ?>><?php esc_html_e( '10 seconds (for testing)', 'students' ); ?></option>
                            <option value="300" <?php selected( $current_duration, 300 ); ?>><?php esc_html_e( '5 minutes', 'students' ); ?></option>
                            <option value="900" <?php selected( $current_duration, 900 ); ?>><?php esc_html_e( '15 minutes', 'students' ); ?></option>
                            <option value="1800" <?php selected( $current_duration, 1800 ); ?>><?php esc_html_e( '30 minutes', 'students' ); ?></option>
                            <option value="3600" <?php selected( $current_duration, 3600 ); ?>><?php esc_html_e( '1 hour', 'students' ); ?></option>
                            <option value="7200" <?php selected( $current_duration, 7200 ); ?>><?php esc_html_e( '2 hours', 'students' ); ?></option>
                            <option value="86400" <?php selected( $current_duration, 86400 ); ?>><?php esc_html_e( '1 day', 'students' ); ?></option>
                        </select>
                        <p class="description"><?php esc_html_e( 'How long to cache dictionary results before fetching fresh data from Oxford.', 'students' ); ?></p>
                    </td>
                </tr>
            </table>
            
            <p>
                <button type="button" id="save-cache-settings" class="button button-primary">
                    <?php esc_html_e( 'Save Cache Settings', 'students' ); ?>
                </button>
                <button type="button" id="clear-dictionary-cache" class="button button-secondary">
                    <?php esc_html_e( 'Clear All Cache', 'students' ); ?>
                </button>
                <span id="cache-settings-status" class="cache-status" style="display: none;"></span>
            </p>
        </div>
        <?php
    }
    
    /**
     * AJAX handler for saving cache settings
     */
    public function ajax_save_cache_settings() {
        // Verify nonce
        if ( ! wp_verify_nonce( $_POST['nonce'], 'dictionary_cache_settings_nonce' ) ) {
            wp_send_json_error( array(
                'message' => __( 'Security check failed.', 'students' )
            ) );
        }

        // Check permissions
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array(
                'message' => __( 'Insufficient permissions.', 'students' )
            ) );
        }

        $cache_duration = intval( $_POST['cache_duration'] );
        
        if ( $cache_duration < 1 ) {
            wp_send_json_error( array(
                'message' => __( 'Invalid cache duration.', 'students' )
            ) );
        }
        
        // Update options
        $options = get_option( 'students_options', array() );
        $options['dictionary_cache_duration'] = $cache_duration;
        update_option( 'students_options', $options );
        
        wp_send_json_success( array(
            'message' => __( 'Cache settings saved successfully!', 'students' ),
            'cache_duration' => $cache_duration
        ) );
    }
    
    /**
     * AJAX handler for clearing dictionary cache
     */
    public function ajax_clear_dictionary_cache() {
        // Verify nonce
        if ( ! wp_verify_nonce( $_POST['nonce'], 'dictionary_cache_settings_nonce' ) ) {
            wp_send_json_error( array(
                'message' => __( 'Security check failed.', 'students' )
            ) );
        }

        // Check permissions
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array(
                'message' => __( 'Insufficient permissions.', 'students' )
            ) );
        }
        
        // Clear all dictionary transients
        global $wpdb;
        $deleted = $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
                $wpdb->esc_like( '_transient_dictionary_%' )
            )
        );
        
        wp_send_json_success( array(
            'message' => sprintf( __( 'Cache cleared successfully! %d entries removed.', 'students' ), $deleted ),
            'deleted_count' => $deleted
        ) );
    }
}
