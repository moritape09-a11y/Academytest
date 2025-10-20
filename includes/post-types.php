<?php
/**
 * ثبت Custom Post Types
 */

defined('ABSPATH') || exit;

add_action('init', 'edu_register_post_types');

function edu_register_post_types() {
    
    // آموزشگاه‌ها
    register_post_type('academy', array(
        'labels' => array(
            'name' => 'آموزشگاه‌ها',
            'singular_name' => 'آموزشگاه',
            'add_new' => 'افزودن آموزشگاه',
            'add_new_item' => 'آموزشگاه جدید',
            'edit_item' => 'ویرایش آموزشگاه',
            'view_item' => 'مشاهده آموزشگاه',
            'all_items' => 'همه آموزشگاه‌ها',
            'search_items' => 'جستجوی آموزشگاه',
        ),
        'public' => true,
        'has_archive' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-building',
        'menu_position' => 20,
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        'rewrite' => array('slug' => 'academy'),
        'show_in_rest' => true,
    ));
    
    // مدارس
    register_post_type('school', array(
        'labels' => array(
            'name' => 'مدارس',
            'singular_name' => 'مدرسه',
            'add_new' => 'افزودن مدرسه',
            'add_new_item' => 'مدرسه جدید',
            'edit_item' => 'ویرایش مدرسه',
            'view_item' => 'مشاهده مدرسه',
            'all_items' => 'همه مدارس',
            'search_items' => 'جستجوی مدرسه',
        ),
        'public' => true,
        'has_archive' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-admin-multisite',
        'menu_position' => 21,
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        'rewrite' => array('slug' => 'school'),
        'show_in_rest' => true,
    ));
    
    // معلمین
    register_post_type('teacher', array(
        'labels' => array(
            'name' => 'معلمین',
            'singular_name' => 'معلم',
            'add_new' => 'افزودن معلم',
            'add_new_item' => 'معلم جدید',
            'edit_item' => 'ویرایش معلم',
            'view_item' => 'مشاهده معلم',
            'all_items' => 'همه معلمین',
            'search_items' => 'جستجوی معلم',
        ),
        'public' => true,
        'has_archive' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-welcome-learn-more',
        'menu_position' => 22,
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        'rewrite' => array('slug' => 'teacher'),
        'show_in_rest' => true,
    ));
}
