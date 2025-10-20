<?php
/**
 * Templates Handler
 * 
 * @package EducationalDirectory
 * @since 3.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class EDU_Templates {
    
    /**
     * Initialize
     */
    public static function init() {
        add_filter('template_include', array(__CLASS__, 'template_loader'));
    }
    
    /**
     * Load custom templates
     */
    public static function template_loader($template) {
        if (is_singular(array('academy', 'school', 'teacher'))) {
            $post_type = get_post_type();
            $custom_template = EDU_DIR_TEMPLATES . 'single-' . $post_type . '.php';
            
            if (file_exists($custom_template)) {
                return $custom_template;
            }
        }
        
        return $template;
    }
}
