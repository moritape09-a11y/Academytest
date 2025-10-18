<?php
/**
 * Plugin Name: Academy Directory
 * Description: Directory of institutes, schools, teachers/coaches with separate shortcodes and search.
 * Version: 0.1.0
 * Author: Academy Team
 * Text Domain: academy-directory
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define plugin constants
if (!defined('AD_PLUGIN_FILE')) {
    define('AD_PLUGIN_FILE', __FILE__);
}
if (!defined('AD_PLUGIN_DIR')) {
    define('AD_PLUGIN_DIR', plugin_dir_path(__FILE__));
}
if (!defined('AD_PLUGIN_URL')) {
    define('AD_PLUGIN_URL', plugin_dir_url(__FILE__));
}

// Includes
require_once AD_PLUGIN_DIR . 'includes/cpt.php';
require_once AD_PLUGIN_DIR . 'includes/taxonomies.php';
require_once AD_PLUGIN_DIR . 'includes/shortcodes.php';

// Activation: register types first, seed default terms, then flush
function ad_on_activation() {
    ad_register_post_types();
    ad_register_taxonomies();

    // Seed default terms if missing
    $org_terms = array(
        'institute' => __('Institute', 'academy-directory'),
        'school'    => __('School', 'academy-directory'),
    );
    foreach ($org_terms as $slug => $label) {
        if (!term_exists($slug, 'org_type')) {
            wp_insert_term($label, 'org_type', array('slug' => $slug));
        }
    }

    $teacher_terms = array(
        'teacher' => __('Teacher', 'academy-directory'),
        'coach'   => __('Coach', 'academy-directory'),
    );
    foreach ($teacher_terms as $slug => $label) {
        if (!term_exists($slug, 'teacher_type')) {
            wp_insert_term($label, 'teacher_type', array('slug' => $slug));
        }
    }

    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'ad_on_activation');

// Deactivation: flush
function ad_on_deactivation() {
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'ad_on_deactivation');

// Assets
function ad_enqueue_assets() {
    wp_enqueue_style(
        'academy-directory',
        AD_PLUGIN_URL . 'assets/css/academy-directory.css',
        array(),
        '0.1.0'
    );
}
add_action('wp_enqueue_scripts', 'ad_enqueue_assets');
