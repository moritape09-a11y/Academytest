<?php
/**
 * Plugin Name: دایرکتوری آموزشی
 * Plugin URI: https://example.com/educational-directory
 * Description: پلاگین جامع برای معرفی آموزشگاه‌ها، مدارس و معلمین با طراحی مدرن و زیبا
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * Text Domain: educational-directory
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.2
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// جلوگیری از دسترسی مستقیم
if (!defined('ABSPATH')) {
    exit;
}

// تعریف ثابت‌های پلاگین
define('ED_VERSION', '1.0.0');
define('ED_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ED_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ED_PLUGIN_FILE', __FILE__);

/**
 * کلاس اصلی پلاگین
 */
class Educational_Directory {
    
    private static $instance = null;
    
    /**
     * دریافت نمونه از کلاس (Singleton Pattern)
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * سازنده کلاس
     */
    private function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
    }
    
    /**
     * بارگذاری فایل‌های وابسته
     */
    private function load_dependencies() {
        // بارگذاری فایل‌ها
        $includes = array(
            'includes/class-post-types.php',
            'includes/class-taxonomies.php',
            'includes/class-meta-boxes.php',
            'includes/class-shortcodes.php',
            'includes/class-templates.php',
            'includes/class-search-filter.php'
        );
        
        foreach ($includes as $file) {
            $filepath = ED_PLUGIN_DIR . $file;
            if (file_exists($filepath)) {
                require_once $filepath;
            }
        }
    }
    
    /**
     * تنظیم هوک‌های اولیه
     */
    private function init_hooks() {
        // بارگذاری ترجمه‌ها
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        
        // راه‌اندازی اجزا - اولویت 0 برای اطمینان از اجرای اول
        add_action('init', array($this, 'init_components'), 0);
        
        // بارگذاری assets
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        
        // فعال‌سازی پلاگین
        register_activation_hook(ED_PLUGIN_FILE, array($this, 'activate'));
        register_deactivation_hook(ED_PLUGIN_FILE, array($this, 'deactivate'));
    }
    
    /**
     * بارگذاری ترجمه‌ها
     */
    public function load_textdomain() {
        load_plugin_textdomain('educational-directory', false, dirname(plugin_basename(ED_PLUGIN_FILE)) . '/languages');
    }
    
    /**
     * بارگذاری فایل‌های CSS و JS برای فرانت‌اند
     */
    public function enqueue_frontend_assets() {
        wp_enqueue_style('ed-frontend', ED_PLUGIN_URL . 'assets/css/frontend.css', array(), ED_VERSION);
        wp_enqueue_script('ed-frontend', ED_PLUGIN_URL . 'assets/js/frontend.js', array('jquery'), ED_VERSION, true);
        
        // ارسال داده‌ها به جاوا اسکریپت
        wp_localize_script('ed-frontend', 'edData', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ed-nonce')
        ));
    }
    
    /**
     * بارگذاری فایل‌های CSS و JS برای پنل مدیریت
     */
    public function enqueue_admin_assets($hook) {
        $post_types = array('academy', 'school', 'teacher');
        
        if (in_array(get_post_type(), $post_types) || in_array($hook, array('post-new.php', 'post.php'))) {
            wp_enqueue_style('ed-admin', ED_PLUGIN_URL . 'assets/css/admin.css', array(), ED_VERSION);
            wp_enqueue_script('ed-admin', ED_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), ED_VERSION, true);
            wp_enqueue_media();
        }
    }
    
    /**
     * راه‌اندازی اجزای پلاگین
     */
    public function init_components() {
        // بررسی وجود کلاس‌ها و راه‌اندازی
        if (class_exists('ED_Post_Types')) {
            ED_Post_Types::get_instance();
        }
        
        if (class_exists('ED_Taxonomies')) {
            ED_Taxonomies::get_instance();
        }
        
        if (class_exists('ED_Meta_Boxes')) {
            ED_Meta_Boxes::get_instance();
        }
        
        if (class_exists('ED_Shortcodes')) {
            ED_Shortcodes::get_instance();
        }
        
        if (class_exists('ED_Templates')) {
            ED_Templates::get_instance();
        }
        
        if (class_exists('ED_Search_Filter')) {
            ED_Search_Filter::get_instance();
        }
    }
    
    /**
     * فعال‌سازی پلاگین
     */
    public function activate() {
        // بارگذاری فایل‌های مورد نیاز
        $this->load_dependencies();
        
        // ایجاد custom post types و taxonomies
        if (class_exists('ED_Post_Types')) {
            ED_Post_Types::get_instance();
        }
        
        if (class_exists('ED_Taxonomies')) {
            ED_Taxonomies::get_instance();
        }
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // ایجاد صفحه‌های پیش‌فرض
        $this->create_default_pages();
    }
    
    /**
     * غیرفعال‌سازی پلاگین
     */
    public function deactivate() {
        flush_rewrite_rules();
    }
    
    /**
     * ایجاد صفحه‌های پیش‌فرض
     */
    private function create_default_pages() {
        $pages = array(
            array(
                'title' => 'آموزشگاه‌ها',
                'content' => '[ed_academies]',
                'slug' => 'academies'
            ),
            array(
                'title' => 'مدارس',
                'content' => '[ed_schools]',
                'slug' => 'schools'
            ),
            array(
                'title' => 'معلمین',
                'content' => '[ed_teachers]',
                'slug' => 'teachers'
            )
        );
        
        foreach ($pages as $page) {
            $existing = get_page_by_path($page['slug']);
            if (!$existing) {
                wp_insert_post(array(
                    'post_title' => $page['title'],
                    'post_content' => $page['content'],
                    'post_status' => 'publish',
                    'post_type' => 'page',
                    'post_name' => $page['slug']
                ));
            }
        }
    }
}

// راه‌اندازی پلاگین
function ED() {
    return Educational_Directory::get_instance();
}

// شروع پلاگین
ED();
