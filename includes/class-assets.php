<?php
/**
 * Assets Manager - CSS & JavaScript
 * 
 * @package EducationalDirectory
 * @since 3.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class EDU_Assets {
    
    /**
     * Initialize
     */
    public static function init() {
        add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue_frontend_assets'));
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_admin_assets'));
    }
    
    /**
     * Enqueue frontend assets
     */
    public static function enqueue_frontend_assets() {
        // Modern UI Styles
        wp_enqueue_style(
            'edu-dir-modern-ui',
            EDU_DIR_ASSETS . 'css/modern-ui.css',
            array(),
            EDU_DIR_VERSION . '.1'
        );
        
        // Scripts
        wp_enqueue_script('jquery');
        
        wp_enqueue_script(
            'edu-dir-search',
            EDU_DIR_ASSETS . 'js/search.js',
            array('jquery'),
            EDU_DIR_VERSION,
            true
        );
        
        // Localize script
        wp_localize_script('edu-dir-search', 'eduDirData', array(
            'restUrl' => rest_url('edu-dir/v1/'),
            'nonce' => wp_create_nonce('wp_rest'),
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'homeUrl' => home_url('/'),
            'assetsUrl' => EDU_DIR_ASSETS,
        ));
    }
    
    /**
     * Enqueue admin assets
     */
    public static function enqueue_admin_assets($hook) {
        // Only on our CPT edit screens
        global $post_type;
        
        if (!in_array($post_type, array('academy', 'school', 'teacher'))) {
            return;
        }
        
        wp_enqueue_style(
            'edu-dir-admin',
            EDU_DIR_ASSETS . 'css/admin.css',
            array(),
            EDU_DIR_VERSION
        );
    }
}
