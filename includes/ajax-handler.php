<?php
/**
 * AJAX Handler - REST API برای جستجوی حرفه‌ای
 */

defined('ABSPATH') || exit;

// ثبت REST API endpoint
add_action('rest_api_init', function() {
    register_rest_route('edu/v1', '/search', array(
        'methods' => 'GET',
        'callback' => 'edu_ajax_search',
        'permission_callback' => '__return_true',
        'args' => array(
            'query' => array('default' => ''),
            'type' => array('default' => 'all'),
            'city' => array('default' => ''),
            'subject' => array('default' => ''),
            'rating' => array('default' => ''),
            'page' => array('default' => 1),
        ),
    ));
    
    register_rest_route('edu/v1', '/filters', array(
        'methods' => 'GET',
        'callback' => 'edu_ajax_get_filters',
        'permission_callback' => '__return_true',
    ));
});

/**
 * AJAX Search Handler
 */
function edu_ajax_search($request) {
    $query = sanitize_text_field($request->get_param('query'));
    $type = sanitize_text_field($request->get_param('type'));
    $city = sanitize_text_field($request->get_param('city'));
    $subject = sanitize_text_field($request->get_param('subject'));
    $rating = sanitize_text_field($request->get_param('rating'));
    $page = intval($request->get_param('page'));
    
    // تعیین نوع‌های مورد جستجو
    $post_types = array();
    if ($type == 'all') {
        $post_types = array('academy', 'school', 'teacher');
    } else {
        $post_types = array($type);
    }
    
    $all_results = array();
    $total_found = 0;
    
    foreach ($post_types as $post_type) {
        $search_results = edu_advanced_search($post_type, $query, $city, $subject, $rating, $page);
        
        if ($search_results->have_posts()) {
            $type_label = '';
            if ($post_type == 'academy') $type_label = 'آموزشگاه‌ها';
            if ($post_type == 'school') $type_label = 'مدارس';
            if ($post_type == 'teacher') $type_label = 'معلمین';
            
            $posts = array();
            while ($search_results->have_posts()) {
                $search_results->the_post();
                
                $rating_value = floatval(get_post_meta(get_the_ID(), 'rating', true));
                $phone = get_post_meta(get_the_ID(), 'phone', true);
                $city_terms = wp_get_post_terms(get_the_ID(), 'city');
                $subject_terms = wp_get_post_terms(get_the_ID(), 'subject');
                
                $posts[] = array(
                    'id' => get_the_ID(),
                    'title' => get_the_title(),
                    'permalink' => get_permalink(),
                    'excerpt' => edu_get_excerpt(),
                    'thumbnail' => get_the_post_thumbnail_url(get_the_ID(), 'medium'),
                    'rating' => $rating_value,
                    'phone' => $phone,
                    'city' => !empty($city_terms) ? $city_terms[0]->name : '',
                    'subjects' => array_map(function($term) {
                        return $term->name;
                    }, $subject_terms),
                    'type' => $post_type,
                );
            }
            
            $all_results[] = array(
                'type' => $post_type,
                'label' => $type_label,
                'count' => $search_results->found_posts,
                'posts' => $posts,
            );
            
            $total_found += $search_results->found_posts;
        }
        
        wp_reset_postdata();
    }
    
    return new WP_REST_Response(array(
        'success' => true,
        'total' => $total_found,
        'results' => $all_results,
        'query' => $query,
        'filters' => array(
            'type' => $type,
            'city' => $city,
            'subject' => $subject,
            'rating' => $rating,
        ),
    ), 200);
}

/**
 * Get available filters with counts
 */
function edu_ajax_get_filters() {
    $cities = get_terms(array(
        'taxonomy' => 'city',
        'hide_empty' => false,
    ));
    
    $subjects = get_terms(array(
        'taxonomy' => 'subject',
        'hide_empty' => false,
    ));
    
    $city_data = array();
    if ($cities && !is_wp_error($cities)) {
        foreach ($cities as $city) {
            $city_data[] = array(
                'slug' => $city->slug,
                'name' => $city->name,
                'count' => $city->count,
            );
        }
    }
    
    $subject_data = array();
    if ($subjects && !is_wp_error($subjects)) {
        foreach ($subjects as $subject) {
            $subject_data[] = array(
                'slug' => $subject->slug,
                'name' => $subject->name,
                'count' => $subject->count,
            );
        }
    }
    
    return new WP_REST_Response(array(
        'success' => true,
        'cities' => $city_data,
        'subjects' => $subject_data,
    ), 200);
}

/**
 * Get excerpt helper
 */
function edu_get_excerpt() {
    if (has_excerpt()) {
        return get_the_excerpt();
    }
    
    $content = get_the_content();
    $content = strip_shortcodes($content);
    $content = wp_strip_all_tags($content);
    $words = explode(' ', $content);
    $excerpt = implode(' ', array_slice($words, 0, 25));
    
    return $excerpt . '...';
}
