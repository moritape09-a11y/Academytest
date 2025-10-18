<?php
if (!defined('ABSPATH')) { exit; }

function ad_register_taxonomies() {
    // Organization type: institute, school
    register_taxonomy('org_type', array('edu_org'), array(
        'labels' => array(
            'name'          => __('Organization Types', 'academy-directory'),
            'singular_name' => __('Organization Type', 'academy-directory'),
        ),
        'public'       => true,
        'hierarchical' => true,
        'show_in_rest' => true,
        'rewrite'      => array('slug' => 'org-type'),
    ));

    // Subject: shared between orgs and teachers
    register_taxonomy('subject', array('edu_org', 'teacher'), array(
        'labels' => array(
            'name'          => __('Subjects', 'academy-directory'),
            'singular_name' => __('Subject', 'academy-directory'),
        ),
        'public'       => true,
        'hierarchical' => true,
        'show_in_rest' => true,
        'rewrite'      => array('slug' => 'subject'),
    ));

    // Teacher type: teacher or coach
    register_taxonomy('teacher_type', array('teacher'), array(
        'labels' => array(
            'name'          => __('Teacher Types', 'academy-directory'),
            'singular_name' => __('Teacher Type', 'academy-directory'),
        ),
        'public'       => true,
        'hierarchical' => false,
        'show_in_rest' => true,
        'rewrite'      => array('slug' => 'teacher-type'),
    ));
}
add_action('init', 'ad_register_taxonomies');
