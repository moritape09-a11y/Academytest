<?php
/**
 * Template Loader
 */

defined('ABSPATH') || exit;

// بارگذاری تمپلیت single
add_filter('single_template', 'edu_single_template');
function edu_single_template($template) {
    global $post;
    
    if (in_array($post->post_type, array('academy', 'school', 'teacher'))) {
        $plugin_template = EDU_PATH . 'templates/single-' . $post->post_type . '.php';
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
    }
    
    return $template;
}

// بارگذاری تمپلیت archive
add_filter('archive_template', 'edu_archive_template');
function edu_archive_template($template) {
    if (is_post_type_archive(array('academy', 'school', 'teacher'))) {
        $post_type = get_query_var('post_type');
        $plugin_template = EDU_PATH . 'templates/archive-' . $post_type . '.php';
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
    }
    
    return $template;
}
