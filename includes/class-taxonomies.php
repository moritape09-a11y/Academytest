<?php
/**
 * Custom Taxonomies Handler
 * 
 * @package EducationalDirectory
 * @since 3.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class EDU_Taxonomies {
    
    /**
     * Initialize
     */
    public static function init() {
        add_action('init', array(__CLASS__, 'register_taxonomies'), 0);
    }
    
    /**
     * Register all taxonomies
     */
    public static function register_taxonomies() {
        self::register_city();
        self::register_subject();
        self::register_grade();
    }
    
    /**
     * Register City Taxonomy
     */
    private static function register_city() {
        $labels = array(
            'name'              => 'شهرها',
            'singular_name'     => 'شهر',
            'search_items'      => 'جستجوی شهر',
            'all_items'         => 'همه شهرها',
            'parent_item'       => 'شهر والد',
            'parent_item_colon' => 'شهر والد:',
            'edit_item'         => 'ویرایش شهر',
            'update_item'       => 'بروزرسانی شهر',
            'add_new_item'      => 'افزودن شهر جدید',
            'new_item_name'     => 'نام شهر جدید',
            'menu_name'         => 'شهرها',
        );
        
        $args = array(
            'labels'            => $labels,
            'hierarchical'      => true,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_in_rest'      => true,
            'show_tagcloud'     => false,
            'rewrite'           => array('slug' => 'city', 'with_front' => false),
        );
        
        register_taxonomy('city', array('academy', 'school', 'teacher'), $args);
    }
    
    /**
     * Register Subject Taxonomy
     */
    private static function register_subject() {
        $labels = array(
            'name'              => 'رشته‌ها',
            'singular_name'     => 'رشته',
            'search_items'      => 'جستجوی رشته',
            'all_items'         => 'همه رشته‌ها',
            'parent_item'       => 'رشته والد',
            'parent_item_colon' => 'رشته والد:',
            'edit_item'         => 'ویرایش رشته',
            'update_item'       => 'بروزرسانی رشته',
            'add_new_item'      => 'افزودن رشته جدید',
            'new_item_name'     => 'نام رشته جدید',
            'menu_name'         => 'رشته‌ها',
        );
        
        $args = array(
            'labels'            => $labels,
            'hierarchical'      => true,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_in_rest'      => true,
            'show_tagcloud'     => true,
            'rewrite'           => array('slug' => 'subject', 'with_front' => false),
        );
        
        register_taxonomy('subject', array('academy', 'school', 'teacher'), $args);
    }
    
    /**
     * Register Grade Taxonomy
     */
    private static function register_grade() {
        $labels = array(
            'name'              => 'پایه‌ها',
            'singular_name'     => 'پایه',
            'search_items'      => 'جستجوی پایه',
            'all_items'         => 'همه پایه‌ها',
            'parent_item'       => 'پایه والد',
            'parent_item_colon' => 'پایه والد:',
            'edit_item'         => 'ویرایش پایه',
            'update_item'       => 'بروزرسانی پایه',
            'add_new_item'      => 'افزودن پایه جدید',
            'new_item_name'     => 'نام پایه جدید',
            'menu_name'         => 'پایه‌ها',
        );
        
        $args = array(
            'labels'            => $labels,
            'hierarchical'      => true,
            'public'            => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_in_rest'      => true,
            'show_tagcloud'     => false,
            'rewrite'           => array('slug' => 'grade', 'with_front' => false),
        );
        
        register_taxonomy('grade', array('academy', 'school', 'teacher'), $args);
    }
}
