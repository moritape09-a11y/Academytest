<?php
/**
 * مدیریت Custom Post Types
 */

if (!defined('ABSPATH')) {
    exit;
}

class ED_Post_Types {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // ثبت Post Types با اولویت بالا
        add_action('init', array($this, 'register_post_types'), 0);
        add_filter('post_updated_messages', array($this, 'custom_messages'));
    }
    
    /**
     * ثبت Custom Post Types
     */
    public function register_post_types() {
        // آموزشگاه‌ها (Academies)
        $academy_labels = array(
            'name' => 'آموزشگاه‌ها',
            'singular_name' => 'آموزشگاه',
            'menu_name' => 'آموزشگاه‌ها',
            'add_new' => 'افزودن آموزشگاه',
            'add_new_item' => 'افزودن آموزشگاه جدید',
            'edit_item' => 'ویرایش آموزشگاه',
            'new_item' => 'آموزشگاه جدید',
            'view_item' => 'مشاهده آموزشگاه',
            'search_items' => 'جستجوی آموزشگاه',
            'not_found' => 'آموزشگاهی یافت نشد',
            'not_found_in_trash' => 'آموزشگاهی در زباله‌دان یافت نشد',
            'all_items' => 'همه آموزشگاه‌ها',
        );
        
        $academy_args = array(
            'labels' => $academy_labels,
            'public' => true,
            'has_archive' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_rest' => true,
            'menu_position' => 5,
            'menu_icon' => 'dashicons-building',
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'comments', 'author'),
            'rewrite' => array('slug' => 'academy'),
            'capability_type' => 'post',
        );
        
        register_post_type('academy', $academy_args);
        
        // مدارس (Schools)
        $school_labels = array(
            'name' => 'مدارس',
            'singular_name' => 'مدرسه',
            'menu_name' => 'مدارس',
            'add_new' => 'افزودن مدرسه',
            'add_new_item' => 'افزودن مدرسه جدید',
            'edit_item' => 'ویرایش مدرسه',
            'new_item' => 'مدرسه جدید',
            'view_item' => 'مشاهده مدرسه',
            'search_items' => 'جستجوی مدرسه',
            'not_found' => 'مدرسه‌ای یافت نشد',
            'not_found_in_trash' => 'مدرسه‌ای در زباله‌دان یافت نشد',
            'all_items' => 'همه مدارس',
        );
        
        $school_args = array(
            'labels' => $school_labels,
            'public' => true,
            'has_archive' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_rest' => true,
            'menu_position' => 6,
            'menu_icon' => 'dashicons-admin-multisite',
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'comments', 'author'),
            'rewrite' => array('slug' => 'school'),
            'capability_type' => 'post',
        );
        
        register_post_type('school', $school_args);
        
        // معلمین (Teachers)
        $teacher_labels = array(
            'name' => 'معلمین',
            'singular_name' => 'معلم',
            'menu_name' => 'معلمین',
            'add_new' => 'افزودن معلم',
            'add_new_item' => 'افزودن معلم جدید',
            'edit_item' => 'ویرایش معلم',
            'new_item' => 'معلم جدید',
            'view_item' => 'مشاهده معلم',
            'search_items' => 'جستجوی معلم',
            'not_found' => 'معلمی یافت نشد',
            'not_found_in_trash' => 'معلمی در زباله‌دان یافت نشد',
            'all_items' => 'همه معلمین',
        );
        
        $teacher_args = array(
            'labels' => $teacher_labels,
            'public' => true,
            'has_archive' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_rest' => true,
            'menu_position' => 7,
            'menu_icon' => 'dashicons-welcome-learn-more',
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'comments', 'author'),
            'rewrite' => array('slug' => 'teacher'),
            'capability_type' => 'post',
        );
        
        register_post_type('teacher', $teacher_args);
    }
    
    /**
     * پیام‌های سفارشی برای پست تایپ‌ها
     */
    public function custom_messages($messages) {
        global $post;
        
        $messages['academy'] = array(
            0 => '',
            1 => 'آموزشگاه به‌روزرسانی شد.',
            2 => 'فیلد سفارشی به‌روزرسانی شد.',
            3 => 'فیلد سفارشی حذف شد.',
            4 => 'آموزشگاه به‌روزرسانی شد.',
            5 => isset($_GET['revision']) ? sprintf('آموزشگاه به نسخه %s بازگردانی شد', wp_post_revision_title((int) $_GET['revision'], false)) : false,
            6 => 'آموزشگاه منتشر شد.',
            7 => 'آموزشگاه ذخیره شد.',
            8 => 'آموزشگاه ارسال شد.',
            9 => sprintf('آموزشگاه برای تاریخ <strong>%1$s</strong> برنامه‌ریزی شد.', date_i18n('Y/m/d H:i', strtotime($post->post_date))),
            10 => 'پیش‌نویس آموزشگاه به‌روزرسانی شد.',
        );
        
        $messages['school'] = array(
            0 => '',
            1 => 'مدرسه به‌روزرسانی شد.',
            2 => 'فیلد سفارشی به‌روزرسانی شد.',
            3 => 'فیلد سفارشی حذف شد.',
            4 => 'مدرسه به‌روزرسانی شد.',
            5 => isset($_GET['revision']) ? sprintf('مدرسه به نسخه %s بازگردانی شد', wp_post_revision_title((int) $_GET['revision'], false)) : false,
            6 => 'مدرسه منتشر شد.',
            7 => 'مدرسه ذخیره شد.',
            8 => 'مدرسه ارسال شد.',
            9 => sprintf('مدرسه برای تاریخ <strong>%1$s</strong> برنامه‌ریزی شد.', date_i18n('Y/m/d H:i', strtotime($post->post_date))),
            10 => 'پیش‌نویس مدرسه به‌روزرسانی شد.',
        );
        
        $messages['teacher'] = array(
            0 => '',
            1 => 'معلم به‌روزرسانی شد.',
            2 => 'فیلد سفارشی به‌روزرسانی شد.',
            3 => 'فیلد سفارشی حذف شد.',
            4 => 'معلم به‌روزرسانی شد.',
            5 => isset($_GET['revision']) ? sprintf('معلم به نسخه %s بازگردانی شد', wp_post_revision_title((int) $_GET['revision'], false)) : false,
            6 => 'معلم منتشر شد.',
            7 => 'معلم ذخیره شد.',
            8 => 'معلم ارسال شد.',
            9 => sprintf('معلم برای تاریخ <strong>%1$s</strong> برنامه‌ریزی شد.', date_i18n('Y/m/d H:i', strtotime($post->post_date))),
            10 => 'پیش‌نویس معلم به‌روزرسانی شد.',
        );
        
        return $messages;
    }
}
