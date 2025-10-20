<?php
/**
 * Custom Post Types Handler
 * 
 * @package EducationalDirectory
 * @since 3.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class EDU_Post_Types {
    
    /**
     * Initialize
     */
    public static function init() {
        add_action('init', array(__CLASS__, 'register_post_types'), 0);
    }
    
    /**
     * Register all custom post types
     */
    public static function register_post_types() {
        self::register_academy();
        self::register_school();
        self::register_teacher();
    }
    
    /**
     * Register Academy CPT
     */
    private static function register_academy() {
        $labels = array(
            'name'               => 'آموزشگاه‌ها',
            'singular_name'      => 'آموزشگاه',
            'menu_name'          => 'آموزشگاه‌ها',
            'add_new'            => 'افزودن آموزشگاه',
            'add_new_item'       => 'افزودن آموزشگاه جدید',
            'edit_item'          => 'ویرایش آموزشگاه',
            'new_item'           => 'آموزشگاه جدید',
            'view_item'          => 'مشاهده آموزشگاه',
            'search_items'       => 'جستجوی آموزشگاه',
            'not_found'          => 'آموزشگاهی یافت نشد',
            'not_found_in_trash' => 'آموزشگاهی در زباله‌دان یافت نشد',
            'all_items'          => 'همه آموزشگاه‌ها',
        );
        
        $args = array(
            'labels'              => $labels,
            'public'              => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_rest'        => true,
            'query_var'           => true,
            'rewrite'             => array('slug' => 'academy', 'with_front' => false),
            'capability_type'     => 'post',
            'has_archive'         => true,
            'hierarchical'        => false,
            'menu_position'       => 20,
            'menu_icon'           => 'dashicons-building',
            'supports'            => array('title', 'editor', 'thumbnail', 'excerpt', 'comments'),
            'taxonomies'          => array('city', 'subject', 'grade'),
        );
        
        register_post_type('academy', $args);
    }
    
    /**
     * Register School CPT
     */
    private static function register_school() {
        $labels = array(
            'name'               => 'مدارس',
            'singular_name'      => 'مدرسه',
            'menu_name'          => 'مدارس',
            'add_new'            => 'افزودن مدرسه',
            'add_new_item'       => 'افزودن مدرسه جدید',
            'edit_item'          => 'ویرایش مدرسه',
            'new_item'           => 'مدرسه جدید',
            'view_item'          => 'مشاهده مدرسه',
            'search_items'       => 'جستجوی مدرسه',
            'not_found'          => 'مدرسه‌ای یافت نشد',
            'not_found_in_trash' => 'مدرسه‌ای در زباله‌دان یافت نشد',
            'all_items'          => 'همه مدارس',
        );
        
        $args = array(
            'labels'              => $labels,
            'public'              => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_rest'        => true,
            'query_var'           => true,
            'rewrite'             => array('slug' => 'school', 'with_front' => false),
            'capability_type'     => 'post',
            'has_archive'         => true,
            'hierarchical'        => false,
            'menu_position'       => 21,
            'menu_icon'           => 'dashicons-welcome-learn-more',
            'supports'            => array('title', 'editor', 'thumbnail', 'excerpt', 'comments'),
            'taxonomies'          => array('city', 'subject', 'grade'),
        );
        
        register_post_type('school', $args);
    }
    
    /**
     * Register Teacher CPT
     */
    private static function register_teacher() {
        $labels = array(
            'name'               => 'معلمین',
            'singular_name'      => 'معلم',
            'menu_name'          => 'معلمین',
            'add_new'            => 'افزودن معلم',
            'add_new_item'       => 'افزودن معلم جدید',
            'edit_item'          => 'ویرایش معلم',
            'new_item'           => 'معلم جدید',
            'view_item'          => 'مشاهده معلم',
            'search_items'       => 'جستجوی معلم',
            'not_found'          => 'معلمی یافت نشد',
            'not_found_in_trash' => 'معلمی در زباله‌دان یافت نشد',
            'all_items'          => 'همه معلمین',
        );
        
        $args = array(
            'labels'              => $labels,
            'public'              => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_rest'        => true,
            'query_var'           => true,
            'rewrite'             => array('slug' => 'teacher', 'with_front' => false),
            'capability_type'     => 'post',
            'has_archive'         => true,
            'hierarchical'        => false,
            'menu_position'       => 22,
            'menu_icon'           => 'dashicons-welcome-learn-more',
            'supports'            => array('title', 'editor', 'thumbnail', 'excerpt', 'comments'),
            'taxonomies'          => array('city', 'subject', 'grade'),
        );
        
        register_post_type('teacher', $args);
    }
}
