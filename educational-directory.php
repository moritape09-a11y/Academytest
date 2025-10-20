<?php
/**
 * Plugin Name: دایرکتوری آموزشی
 * Description: معرفی آموزشگاه‌ها، مدارس و معلمین - ساده و کاربردی
 * Version: 2.0.0
 * Author: نویسنده
 * Text Domain: edu-dir
 */

defined('ABSPATH') || exit;

// ثابت‌ها
define('EDU_VERSION', '2.0.0');
define('EDU_PATH', plugin_dir_path(__FILE__));
define('EDU_URL', plugin_dir_url(__FILE__));

// بارگذاری فایل‌ها
require_once EDU_PATH . 'includes/post-types.php';
require_once EDU_PATH . 'includes/taxonomies.php';
require_once EDU_PATH . 'includes/metaboxes.php';
require_once EDU_PATH . 'includes/shortcodes.php';
require_once EDU_PATH . 'includes/templates.php';
require_once EDU_PATH . 'includes/ajax-handler.php';

// بارگذاری استایل‌ها
function edu_enqueue_assets() {
    wp_enqueue_style('dashicons');
    wp_enqueue_style('edu-style', EDU_URL . 'assets/style.css', array(), EDU_VERSION);
    wp_enqueue_script('edu-script', EDU_URL . 'assets/script.js', array('jquery'), EDU_VERSION, true);
    
    // AJAX Script
    wp_enqueue_script('edu-ajax-search', EDU_URL . 'assets/ajax-search.js', array('jquery'), EDU_VERSION, true);
    
    // Localize script
    wp_localize_script('edu-ajax-search', 'eduAjaxData', array(
        'restUrl' => rest_url(),
        'nonce' => wp_create_nonce('wp_rest'),
        'homeUrl' => home_url(),
    ));
}
add_action('wp_enqueue_scripts', 'edu_enqueue_assets');

// بارگذاری استایل ادمین
function edu_admin_assets() {
    wp_enqueue_style('edu-admin', EDU_URL . 'assets/admin.css', array(), EDU_VERSION);
}
add_action('admin_enqueue_scripts', 'edu_admin_assets');

// فعال‌سازی
register_activation_hook(__FILE__, 'edu_activate');
function edu_activate() {
    // فراخوانی تابع ثبت Post Types
    edu_register_post_types();
    edu_register_taxonomies();
    
    // بازنویسی rewrite rules
    flush_rewrite_rules();
}

// غیرفعال‌سازی
register_deactivation_hook(__FILE__, 'edu_deactivate');
function edu_deactivate() {
    flush_rewrite_rules();
}
