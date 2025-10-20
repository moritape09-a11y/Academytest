<?php
/**
 * AJAX Handler - REST API Implementation
 * 
 * @package EducationalDirectory
 * @since 3.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class EDU_Ajax_Handler {
    
    /**
     * Initialize
     */
    public static function init() {
        add_action('rest_api_init', array(__CLASS__, 'register_rest_routes'));
    }
    
    /**
     * Register REST API routes
     */
    public static function register_rest_routes() {
        // Main search endpoint
        register_rest_route('edu-dir/v1', '/search', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'handle_search'),
            'permission_callback' => '__return_true',
            'args' => array(
                'query' => array(
                    'type' => 'string',
                    'default' => '',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'type' => array(
                    'type' => 'string',
                    'default' => 'all',
                    'enum' => array('all', 'academy', 'school', 'teacher'),
                ),
                'city' => array(
                    'type' => 'string',
                    'default' => '',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'subject' => array(
                    'type' => 'string',
                    'default' => '',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'rating' => array(
                    'type' => 'string',
                    'default' => '',
                ),
                'page' => array(
                    'type' => 'integer',
                    'default' => 1,
                ),
            ),
        ));
        
        // Get filters data
        register_rest_route('edu-dir/v1', '/filters', array(
            'methods' => 'GET',
            'callback' => array(__CLASS__, 'get_filters_data'),
            'permission_callback' => '__return_true',
        ));
    }
    
    /**
     * Handle search request
     */
    public static function handle_search($request) {
        $query_string = $request->get_param('query');
        $type = $request->get_param('type');
        $city = $request->get_param('city');
        $subject = $request->get_param('subject');
        $rating = $request->get_param('rating');
        $page = intval($request->get_param('page'));
        
        // Determine post types
        $post_types = ($type === 'all') 
            ? array('academy', 'school', 'teacher') 
            : array($type);
        
        $results = array();
        $total = 0;
        
        foreach ($post_types as $post_type) {
            $query_results = self::perform_search($post_type, $query_string, $city, $subject, $rating, $page);
            
            if (!empty($query_results['posts'])) {
                $results[] = array(
                    'type' => $post_type,
                    'label' => self::get_post_type_label($post_type),
                    'count' => $query_results['total'],
                    'posts' => $query_results['posts'],
                );
                $total += $query_results['total'];
            }
        }
        
        return new WP_REST_Response(array(
            'success' => true,
            'total' => $total,
            'results' => $results,
            'query' => $query_string,
            'filters' => array(
                'type' => $type,
                'city' => $city,
                'subject' => $subject,
                'rating' => $rating,
            ),
        ), 200);
    }
    
    /**
     * Perform search query
     */
    private static function perform_search($post_type, $query_string, $city, $subject, $rating, $page = 1) {
        $args = array(
            'post_type' => $post_type,
            'post_status' => 'publish',
            'posts_per_page' => 12,
            'paged' => $page,
            'orderby' => array(
                'meta_value_num' => 'ASC',
                'date' => 'DESC',
            ),
            'meta_key' => 'display_priority',
        );
        
        // Search query
        if (!empty($query_string)) {
            $args['s'] = $query_string;
        }
        
        // Tax query
        $tax_query = array();
        
        if (!empty($city)) {
            $tax_query[] = array(
                'taxonomy' => 'city',
                'field' => 'slug',
                'terms' => $city,
            );
        }
        
        if (!empty($subject)) {
            $tax_query[] = array(
                'taxonomy' => 'subject',
                'field' => 'slug',
                'terms' => $subject,
            );
        }
        
        if (!empty($tax_query)) {
            $args['tax_query'] = $tax_query;
            if (count($tax_query) > 1) {
                $args['tax_query']['relation'] = 'AND';
            }
        }
        
        // Rating filter
        if (!empty($rating)) {
            $args['meta_query'] = array(
                array(
                    'key' => 'rating',
                    'value' => floatval($rating),
                    'compare' => '>=',
                    'type' => 'DECIMAL',
                ),
            );
        }
        
        $query = new WP_Query($args);
        
        $posts = array();
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $posts[] = self::format_post(get_post());
            }
            wp_reset_postdata();
        }
        
        return array(
            'posts' => $posts,
            'total' => $query->found_posts,
        );
    }
    
    /**
     * Format post for JSON
     */
    private static function format_post($post) {
        $post_id = $post->ID;
        
        // Get meta data
        $rating = floatval(get_post_meta($post_id, 'rating', true));
        $phone = get_post_meta($post_id, 'phone', true);
        $address = get_post_meta($post_id, 'address', true);
        
        // Get taxonomies
        $city_terms = wp_get_post_terms($post_id, 'city');
        $subject_terms = wp_get_post_terms($post_id, 'subject');
        
        // Get thumbnail
        $thumbnail = get_the_post_thumbnail_url($post_id, 'medium');
        if (!$thumbnail) {
            $thumbnail = EDU_DIR_ASSETS . 'images/placeholder.jpg';
        }
        
        // Get excerpt
        $excerpt = has_excerpt($post_id) ? get_the_excerpt($post_id) : wp_trim_words($post->post_content, 25);
        
        return array(
            'id' => $post_id,
            'title' => get_the_title($post_id),
            'permalink' => get_permalink($post_id),
            'excerpt' => $excerpt,
            'thumbnail' => $thumbnail,
            'rating' => $rating,
            'phone' => $phone,
            'address' => $address,
            'city' => !empty($city_terms) && !is_wp_error($city_terms) ? $city_terms[0]->name : '',
            'subjects' => !empty($subject_terms) && !is_wp_error($subject_terms) 
                ? array_map(function($term) { return $term->name; }, $subject_terms) 
                : array(),
            'type' => $post->post_type,
        );
    }
    
    /**
     * Get filters data
     */
    public static function get_filters_data() {
        $cities = get_terms(array(
            'taxonomy' => 'city',
            'hide_empty' => false,
        ));
        
        $subjects = get_terms(array(
            'taxonomy' => 'subject',
            'hide_empty' => false,
        ));
        
        $city_data = array();
        if (!empty($cities) && !is_wp_error($cities)) {
            foreach ($cities as $city) {
                $city_data[] = array(
                    'slug' => $city->slug,
                    'name' => $city->name,
                    'count' => $city->count,
                );
            }
        }
        
        $subject_data = array();
        if (!empty($subjects) && !is_wp_error($subjects)) {
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
     * Get post type label
     */
    private static function get_post_type_label($post_type) {
        $labels = array(
            'academy' => 'آموزشگاه‌ها',
            'school' => 'مدارس',
            'teacher' => 'معلمین',
        );
        return isset($labels[$post_type]) ? $labels[$post_type] : '';
    }
}
