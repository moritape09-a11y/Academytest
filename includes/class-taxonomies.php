<?php
/**
 * مدیریت Taxonomies
 */

if (!defined('ABSPATH')) {
    exit;
}

class ED_Taxonomies {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('init', array($this, 'register_taxonomies'));
    }
    
    /**
     * ثبت Taxonomies
     */
    public function register_taxonomies() {
        
        // دسته‌بندی رشته تحصیلی/آموزشی
        $subject_labels = array(
            'name' => 'رشته‌ها',
            'singular_name' => 'رشته',
            'search_items' => 'جستجوی رشته',
            'all_items' => 'همه رشته‌ها',
            'parent_item' => 'رشته والد',
            'parent_item_colon' => 'رشته والد:',
            'edit_item' => 'ویرایش رشته',
            'update_item' => 'به‌روزرسانی رشته',
            'add_new_item' => 'افزودن رشته جدید',
            'new_item_name' => 'نام رشته جدید',
            'menu_name' => 'رشته‌ها',
        );
        
        register_taxonomy('subject', array('academy', 'school', 'teacher'), array(
            'hierarchical' => true,
            'labels' => $subject_labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_rest' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'subject'),
        ));
        
        // دسته‌بندی شهر
        $city_labels = array(
            'name' => 'شهرها',
            'singular_name' => 'شهر',
            'search_items' => 'جستجوی شهر',
            'all_items' => 'همه شهرها',
            'parent_item' => 'استان',
            'parent_item_colon' => 'استان:',
            'edit_item' => 'ویرایش شهر',
            'update_item' => 'به‌روزرسانی شهر',
            'add_new_item' => 'افزودن شهر جدید',
            'new_item_name' => 'نام شهر جدید',
            'menu_name' => 'شهرها',
        );
        
        register_taxonomy('city', array('academy', 'school', 'teacher'), array(
            'hierarchical' => true,
            'labels' => $city_labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_rest' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'city'),
        ));
        
        // دسته‌بندی نوع (برای مدارس: دولتی، غیرانتفاعی، خصوصی و...)
        $type_labels = array(
            'name' => 'انواع',
            'singular_name' => 'نوع',
            'search_items' => 'جستجوی نوع',
            'all_items' => 'همه انواع',
            'edit_item' => 'ویرایش نوع',
            'update_item' => 'به‌روزرسانی نوع',
            'add_new_item' => 'افزودن نوع جدید',
            'new_item_name' => 'نام نوع جدید',
            'menu_name' => 'انواع',
        );
        
        register_taxonomy('institution_type', array('academy', 'school'), array(
            'hierarchical' => false,
            'labels' => $type_labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_rest' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'type'),
        ));
        
        // دسته‌بندی تخصص معلم
        $specialty_labels = array(
            'name' => 'تخصص‌ها',
            'singular_name' => 'تخصص',
            'search_items' => 'جستجوی تخصص',
            'all_items' => 'همه تخصص‌ها',
            'edit_item' => 'ویرایش تخصص',
            'update_item' => 'به‌روزرسانی تخصص',
            'add_new_item' => 'افزودن تخصص جدید',
            'new_item_name' => 'نام تخصص جدید',
            'menu_name' => 'تخصص‌ها',
        );
        
        register_taxonomy('specialty', array('teacher'), array(
            'hierarchical' => false,
            'labels' => $specialty_labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_rest' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'specialty'),
        ));
        
        // دسته‌بندی مقطع تحصیلی
        $grade_labels = array(
            'name' => 'مقاطع تحصیلی',
            'singular_name' => 'مقطع تحصیلی',
            'search_items' => 'جستجوی مقطع',
            'all_items' => 'همه مقاطع',
            'parent_item' => 'مقطع والد',
            'parent_item_colon' => 'مقطع والد:',
            'edit_item' => 'ویرایش مقطع',
            'update_item' => 'به‌روزرسانی مقطع',
            'add_new_item' => 'افزودن مقطع جدید',
            'new_item_name' => 'نام مقطع جدید',
            'menu_name' => 'مقاطع تحصیلی',
        );
        
        register_taxonomy('grade_level', array('academy', 'school', 'teacher'), array(
            'hierarchical' => true,
            'labels' => $grade_labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_rest' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'grade'),
        ));
    }
}
