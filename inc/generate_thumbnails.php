<?php
/**
 * Thumbnail Generation Optimization
 * 
 * Handles deferred thumbnail creation and background processing
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class STM_Generate_Thumbnails {
    
    private static $instance = null;
    
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Initialize hooks
        $this->init_hooks();
    }
    
    private function init_hooks() {
      
        // Prevent immediate thumbnail generation during upload
        add_filter('wp_generate_attachment_metadata', array($this, 'defer_thumbnail_creation'), 10, 2);
        
        // Queue thumbnail generation after upload
        add_action('add_attachment', array($this, 'queue_thumbnail_generation'));
        
        // Process thumbnails in background
        add_action('process_delayed_thumbnails', array($this, 'process_delayed_thumbnails'));
        
        // Monitor uploads and cron health
        add_action('add_attachment', array($this, 'log_image_upload_process'));
        add_action('init', array($this, 'check_wp_cron_health'));
    }
    
    /**
     * Defer thumbnail creation during frontend uploads
     */
    public function defer_thumbnail_creation($metadata, $attachment_id) {
       
            $this->log_message("Deferring thumbnail creation for image ID: " . $attachment_id);
            return null;
        
        return $metadata;
    }
    
    /**
     * Queue thumbnail generation
     */
    public function queue_thumbnail_generation($post_id) {
        if (!wp_attachment_is_image($post_id)) {
            return;
        }

        // Get the parent post ID of the attachment
        $parent_id = wp_get_post_parent_id($post_id);
        if (!$parent_id) {
            return;
        }

        // Get the post type of the parent post
        $post_type = get_post_type($parent_id);
        
        // Define allowed post types
        $allowed_post_types = array(
            'listings',
            'caravan',
            'motor',
            'mc-atv',
            'mc-boat',
            'mc-car',
            'mc-truck',
            'mc-motorcycle'
        );

        // Only process if parent post is one of our allowed types
        if (in_array($post_type, $allowed_post_types)) {
            $this->log_message("Queuing thumbnail generation for image ID: {$post_id} (Parent post type: {$post_type})");
            
            wp_schedule_single_event(
                time() + 60,
                'process_delayed_thumbnails',
                array($post_id)
            );
        } else {
            $this->log_message("Skipping thumbnail generation for image ID: {$post_id} (Post type: {$post_type} not in allowed list)");
        }
    }
    
    /**
     * Process thumbnails in background
     */
    public function process_delayed_thumbnails($attachment_id) {
        $this->log_message("Starting thumbnail generation for image ID: " . $attachment_id);
        
        $file = get_attached_file($attachment_id);
        if ($file) {
            // Generate the thumbnails
            $metadata = wp_generate_attachment_metadata($attachment_id, $file);
            
            // Update the attachment metadata
            if ($metadata) {
                wp_update_attachment_metadata($attachment_id, $metadata);
                $this->log_message("Completed thumbnail generation for image ID: " . $attachment_id);
                
                // Hook for additional actions after thumbnail generation
                do_action('stm_after_thumbnail_generation', $attachment_id, $metadata);
            } else {
                $this->log_message("Failed to generate thumbnails for image ID: " . $attachment_id);
            }
        }
    }
    
    /**
     * Log image upload process
     */
    public function log_image_upload_process($attachment_id) {
        $this->log_message("New image uploaded - ID: " . $attachment_id);
    }
    
    /**
     * Check WP Cron health
     */
    public function check_wp_cron_health() {
        if (defined('DISABLE_WP_CRON') && DISABLE_WP_CRON) {
            $this->log_message("Warning: WP Cron is disabled. Thumbnail generation may not work.");
        }
    }
    
    /**
     * Logging helper function
     */
    private function log_message($message) {
        $upload_dir = wp_upload_dir();
        $log_file = $upload_dir['basedir'] . '/stm-thumbnails.log';
        
        $timestamp = current_time('Y-m-d H:i:s');
        $log_message = sprintf("[%s] %s\n", $timestamp, $message);
        
        file_put_contents($log_file, $log_message, FILE_APPEND);
    }
    
    /**
     * Get thumbnail generation status
     */
    public function get_generation_status($attachment_id) {
        return get_post_meta($attachment_id, '_thumbnail_generation_status', true);
    }
    
    /**
     * Update thumbnail generation status
     */
    private function update_generation_status($attachment_id, $status) {
        update_post_meta($attachment_id, '_thumbnail_generation_status', $status);
    }
}

// Initialize the class
function stm_init_thumbnail_generator() {
    return STM_Generate_Thumbnails::getInstance();
}

// Start the thumbnail generator
add_action('init', 'stm_init_thumbnail_generator');

/**
 * Helper function to check thumbnail generation status
 */
function stm_check_thumbnail_status($attachment_id) {
    $generator = STM_Generate_Thumbnails::getInstance();
    return $generator->get_generation_status($attachment_id);
} 