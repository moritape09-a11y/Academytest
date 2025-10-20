<?php
/**
 * Plugin Name: دایرکتوری آموزشی حرفه‌ای
 * Plugin URI: https://example.com
 * Description: سیستم جامع مدیریت آموزشگاه‌ها، مدارس و معلمین با AJAX، جستجوی پیشرفته و UI مدرن
 * Version: 3.0.0
 * Author: تیم توسعه
 * Author URI: https://example.com
 * Text Domain: educational-directory
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Security: Prevent direct access
if (!defined('ABSPATH')) {
    exit('Direct access not allowed.');
}

/**
 * Educational Directory Plugin - Main Class
 * 
 * @package EducationalDirectory
 * @version 3.0.0
 */
final class Educational_Directory_Plugin {
    
    /**
     * Plugin version
     * @var string
     */
    const VERSION = '3.0.0';
    
    /**
     * Minimum PHP version
     * @var string
     */
    const MIN_PHP_VERSION = '7.4';
    
    /**
     * Minimum WordPress version
     * @var string
     */
    const MIN_WP_VERSION = '5.8';
    
    /**
     * Single instance
     * @var Educational_Directory_Plugin
     */
    private static $instance = null;
    
    /**
     * Plugin path
     * @var string
     */
    private $plugin_path;
    
    /**
     * Plugin URL
     * @var string
     */
    private $plugin_url;
    
    /**
     * Get instance (Singleton pattern)
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->plugin_path = plugin_dir_path(__FILE__);
        $this->plugin_url = plugin_dir_url(__FILE__);
        
        // Check requirements
        if (!$this->check_requirements()) {
            return;
        }
        
        // Define constants
        $this->define_constants();
        
        // Load dependencies
        $this->load_dependencies();
        
        // Initialize hooks
        $this->init_hooks();
    }
    
    /**
     * Check requirements
     */
    private function check_requirements() {
        // Check PHP version
        if (version_compare(PHP_VERSION, self::MIN_PHP_VERSION, '<')) {
            add_action('admin_notices', function() {
                echo '<div class="error"><p>';
                printf(
                    __('دایرکتوری آموزشی نیاز به PHP نسخه %s یا بالاتر دارد. نسخه فعلی شما: %s', 'educational-directory'),
                    self::MIN_PHP_VERSION,
                    PHP_VERSION
                );
                echo '</p></div>';
            });
            return false;
        }
        
        // Check WordPress version
        global $wp_version;
        if (version_compare($wp_version, self::MIN_WP_VERSION, '<')) {
            add_action('admin_notices', function() use ($wp_version) {
                echo '<div class="error"><p>';
                printf(
                    __('دایرکتوری آموزشی نیاز به WordPress نسخه %s یا بالاتر دارد. نسخه فعلی شما: %s', 'educational-directory'),
                    self::MIN_WP_VERSION,
                    $wp_version
                );
                echo '</p></div>';
            });
            return false;
        }
        
        return true;
    }
    
    /**
     * Define constants
     */
    private function define_constants() {
        define('EDU_DIR_VERSION', self::VERSION);
        define('EDU_DIR_PATH', $this->plugin_path);
        define('EDU_DIR_URL', $this->plugin_url);
        define('EDU_DIR_INCLUDES', $this->plugin_path . 'includes/');
        define('EDU_DIR_TEMPLATES', $this->plugin_path . 'templates/');
        define('EDU_DIR_ASSETS', $this->plugin_url . 'assets/');
    }
    
    /**
     * Load dependencies
     */
    private function load_dependencies() {
        // Core classes
        require_once EDU_DIR_INCLUDES . 'class-post-types.php';
        require_once EDU_DIR_INCLUDES . 'class-taxonomies.php';
        require_once EDU_DIR_INCLUDES . 'class-meta-boxes.php';
        require_once EDU_DIR_INCLUDES . 'class-shortcodes.php';
        require_once EDU_DIR_INCLUDES . 'class-ajax-handler.php';
        require_once EDU_DIR_INCLUDES . 'class-assets.php';
        require_once EDU_DIR_INCLUDES . 'class-templates.php';
        
        // Initialize components
        EDU_Post_Types::init();
        EDU_Taxonomies::init();
        EDU_Meta_Boxes::init();
        EDU_Shortcodes::init();
        EDU_Ajax_Handler::init();
        EDU_Assets::init();
        EDU_Templates::init();
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Activation/Deactivation
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        // Plugin loaded
        add_action('plugins_loaded', array($this, 'plugins_loaded'));
        
        // Admin init
        add_action('admin_init', array($this, 'admin_init'));
        
        // Add settings link
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'add_action_links'));
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Register post types and taxonomies
        EDU_Post_Types::register_post_types();
        EDU_Taxonomies::register_taxonomies();
        
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Set default options
        $default_options = array(
            'version' => self::VERSION,
            'installed_date' => current_time('mysql'),
        );
        add_option('edu_dir_options', $default_options);
        
        // Add success notice
        set_transient('edu_dir_activation_notice', true, 30);
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
        
        // Clean up transients
        delete_transient('edu_dir_activation_notice');
    }
    
    /**
     * Plugins loaded hook
     */
    public function plugins_loaded() {
        // Load text domain
        load_plugin_textdomain('educational-directory', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    
    /**
     * Admin init hook
     */
    public function admin_init() {
        // Show activation notice
        if (get_transient('edu_dir_activation_notice')) {
            add_action('admin_notices', array($this, 'activation_notice'));
            delete_transient('edu_dir_activation_notice');
        }
    }
    
    /**
     * Activation notice
     */
    public function activation_notice() {
        ?>
        <div class="notice notice-success is-dismissible">
            <h3>🎉 پلاگین دایرکتوری آموزشی فعال شد!</h3>
            <p>
                <strong>برای شروع:</strong><br>
                • از منوی سمت راست، آموزشگاه/مدرسه/معلم اضافه کنید<br>
                • از شورت‌کدها استفاده کنید: <code>[edu_search]</code>, <code>[academies]</code>, <code>[schools]</code>, <code>[teachers]</code><br>
                • همه چیز با AJAX و بدون رفرش صفحه کار می‌کند! ✨
            </p>
        </div>
        <?php
    }
    
    /**
     * Add action links
     */
    public function add_action_links($links) {
        $custom_links = array(
            '<a href="' . admin_url('edit.php?post_type=academy') . '">آموزشگاه‌ها</a>',
            '<a href="' . admin_url('edit.php?post_type=school') . '">مدارس</a>',
            '<a href="' . admin_url('edit.php?post_type=teacher') . '">معلمین</a>',
        );
        return array_merge($custom_links, $links);
    }
    
    /**
     * Get plugin path
     */
    public function get_plugin_path() {
        return $this->plugin_path;
    }
    
    /**
     * Get plugin URL
     */
    public function get_plugin_url() {
        return $this->plugin_url;
    }
}

/**
 * Initialize the plugin
 */
function edu_dir_plugin() {
    return Educational_Directory_Plugin::get_instance();
}

// Let's go!
edu_dir_plugin();
