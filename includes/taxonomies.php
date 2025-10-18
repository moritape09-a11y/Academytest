<?php
/**
 * ثبت Taxonomies
 */

defined('ABSPATH') || exit;

add_action('init', 'edu_register_taxonomies');

function edu_register_taxonomies() {
    
    // شهرها
    register_taxonomy('city', array('academy', 'school', 'teacher'), array(
        'labels' => array(
            'name' => 'شهرها',
            'singular_name' => 'شهر',
            'add_new_item' => 'افزودن شهر',
            'search_items' => 'جستجوی شهر',
        ),
        'hierarchical' => true,
        'show_admin_column' => true,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'city'),
    ));
    
    // رشته‌ها
    register_taxonomy('subject', array('academy', 'school', 'teacher'), array(
        'labels' => array(
            'name' => 'رشته‌ها',
            'singular_name' => 'رشته',
            'add_new_item' => 'افزودن رشته',
            'search_items' => 'جستجوی رشته',
        ),
        'hierarchical' => true,
        'show_admin_column' => true,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'subject'),
    ));
    
    // مقطع تحصیلی
    register_taxonomy('grade', array('academy', 'school', 'teacher'), array(
        'labels' => array(
            'name' => 'مقاطع',
            'singular_name' => 'مقطع',
            'add_new_item' => 'افزودن مقطع',
        ),
        'hierarchical' => true,
        'show_admin_column' => true,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'grade'),
    ));
}
