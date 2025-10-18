<?php
if (!defined('ABSPATH')) { exit; }

function ad_register_post_types() {
    // Educational organizations (both institutes and schools)
    register_post_type('edu_org', array(
        'labels' => array(
            'name'          => __('Educational Orgs', 'academy-directory'),
            'singular_name' => __('Educational Org', 'academy-directory'),
            'add_new_item'  => __('Add New Organization', 'academy-directory'),
            'edit_item'     => __('Edit Organization', 'academy-directory'),
        ),
        'public'       => true,
        'has_archive'  => true,
        'menu_icon'    => 'dashicons-welcome-learn-more',
        'supports'     => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'show_in_rest' => true,
        'rewrite'      => array('slug' => 'organizations'),
        'taxonomies'   => array('org_type', 'subject'),
    ));

    // Teachers / Coaches
    register_post_type('teacher', array(
        'labels' => array(
            'name'          => __('Teachers', 'academy-directory'),
            'singular_name' => __('Teacher', 'academy-directory'),
            'add_new_item'  => __('Add New Teacher', 'academy-directory'),
            'edit_item'     => __('Edit Teacher', 'academy-directory'),
        ),
        'public'       => true,
        'has_archive'  => true,
        'menu_icon'    => 'dashicons-welcome-learn-more',
        'supports'     => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'show_in_rest' => true,
        'rewrite'      => array('slug' => 'teachers'),
        'taxonomies'   => array('subject', 'teacher_type'),
    ));
}
add_action('init', 'ad_register_post_types');
