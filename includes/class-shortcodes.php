<?php
/**
 * Shortcodes Handler
 * 
 * @package EducationalDirectory
 * @since 3.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class EDU_Shortcodes {
    
    /**
     * Initialize
     */
    public static function init() {
        add_shortcode('edu_search', array(__CLASS__, 'search_shortcode'));
        add_shortcode('academies', array(__CLASS__, 'academies_shortcode'));
        add_shortcode('schools', array(__CLASS__, 'schools_shortcode'));
        add_shortcode('teachers', array(__CLASS__, 'teachers_shortcode'));
    }
    
    /**
     * Main search shortcode with AJAX
     */
    public static function search_shortcode($atts) {
        $atts = shortcode_atts(array(
            'show' => 'all',
        ), $atts);
        
        ob_start();
        
        // Get filter data
        $cities = get_terms(array('taxonomy' => 'city', 'hide_empty' => false));
        $subjects = get_terms(array('taxonomy' => 'subject', 'hide_empty' => false));
        
        ?>
        <div class="edu-search-wrapper" data-show="<?php echo esc_attr($atts['show']); ?>">
            <div class="edu-search-container">
                <!-- Header -->
                <div class="edu-search-header">
                    <h2 class="edu-search-title">🔍 جستجوی پیشرفته</h2>
                    <p class="edu-search-subtitle">آموزشگاه، مدرسه یا معلم مورد نظر خود را پیدا کنید</p>
                </div>
                
                <!-- Main Search Input -->
                <div class="edu-search-main">
                    <input type="text" 
                           class="edu-search-input-main" 
                           placeholder="🔎 جستجو کنید... (مثال: زبان انگلیسی، ریاضی، طراحی)" 
                           id="edu-main-search">
                </div>
                
                <!-- Filters -->
                <div class="edu-search-filters">
                    <!-- Type Filter -->
                    <div class="edu-filter-group">
                        <label class="edu-filter-label">
                            <span class="edu-filter-icon">📋</span>
                            نوع
                        </label>
                        <select class="edu-filter-select" id="edu-filter-type">
                            <option value="all">همه</option>
                            <option value="academy">آموزشگاه</option>
                            <option value="school">مدرسه</option>
                            <option value="teacher">معلم</option>
                        </select>
                    </div>
                    
                    <!-- City Filter -->
                    <div class="edu-filter-group">
                        <label class="edu-filter-label">
                            <span class="edu-filter-icon">📍</span>
                            شهر
                        </label>
                        <select class="edu-filter-select" id="edu-filter-city">
                            <option value="">همه شهرها</option>
                            <?php if (!empty($cities) && !is_wp_error($cities)): ?>
                                <?php foreach ($cities as $city): ?>
                                    <option value="<?php echo esc_attr($city->slug); ?>">
                                        <?php echo esc_html($city->name); ?>
                                        <?php if ($city->count > 0): ?>
                                            (<?php echo $city->count; ?>)
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <!-- Subject Filter -->
                    <div class="edu-filter-group">
                        <label class="edu-filter-label">
                            <span class="edu-filter-icon">📚</span>
                            رشته
                        </label>
                        <select class="edu-filter-select" id="edu-filter-subject">
                            <option value="">همه رشته‌ها</option>
                            <?php if (!empty($subjects) && !is_wp_error($subjects)): ?>
                                <?php foreach ($subjects as $subject): ?>
                                    <option value="<?php echo esc_attr($subject->slug); ?>">
                                        <?php echo esc_html($subject->name); ?>
                                        <?php if ($subject->count > 0): ?>
                                            (<?php echo $subject->count; ?>)
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <!-- Rating Filter -->
                    <div class="edu-filter-group">
                        <label class="edu-filter-label">
                            <span class="edu-filter-icon">⭐</span>
                            امتیاز
                        </label>
                        <select class="edu-filter-select" id="edu-filter-rating">
                            <option value="">همه امتیازها</option>
                            <option value="5">⭐⭐⭐⭐⭐ (5 ستاره)</option>
                            <option value="4">⭐⭐⭐⭐ (4+ ستاره)</option>
                            <option value="3">⭐⭐⭐ (3+ ستاره)</option>
                            <option value="2">⭐⭐ (2+ ستاره)</option>
                            <option value="1">⭐ (1+ ستاره)</option>
                        </select>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="edu-search-actions">
                    <button type="button" class="edu-btn edu-btn-search">
                        <span class="edu-btn-icon">🔍</span>
                        جستجو
                    </button>
                    <button type="button" class="edu-btn edu-btn-reset">
                        <span class="edu-btn-icon">🔄</span>
                        پاک کردن
                    </button>
                </div>
                
                <!-- Results Container -->
                <div class="edu-search-results" id="edu-search-results"></div>
            </div>
        </div>
        <?php
        
        return ob_get_clean();
    }
    
    /**
     * Academies listing shortcode
     */
    public static function academies_shortcode($atts) {
        $atts = shortcode_atts(array(
            'limit' => 12,
            'city' => '',
            'subject' => '',
        ), $atts);
        
        return self::render_post_grid('academy', $atts);
    }
    
    /**
     * Schools listing shortcode
     */
    public static function schools_shortcode($atts) {
        $atts = shortcode_atts(array(
            'limit' => 12,
            'city' => '',
            'subject' => '',
        ), $atts);
        
        return self::render_post_grid('school', $atts);
    }
    
    /**
     * Teachers listing shortcode
     */
    public static function teachers_shortcode($atts) {
        $atts = shortcode_atts(array(
            'limit' => 12,
            'city' => '',
            'subject' => '',
        ), $atts);
        
        return self::render_post_grid('teacher', $atts);
    }
    
    /**
     * Render post grid
     */
    private static function render_post_grid($post_type, $atts) {
        $args = array(
            'post_type' => $post_type,
            'posts_per_page' => intval($atts['limit']),
            'post_status' => 'publish',
            'orderby' => array(
                'meta_value_num' => 'ASC',
                'date' => 'DESC',
            ),
            'meta_key' => 'display_priority',
        );
        
        // Add tax query if needed
        $tax_query = array();
        
        if (!empty($atts['city'])) {
            $tax_query[] = array(
                'taxonomy' => 'city',
                'field' => 'slug',
                'terms' => $atts['city'],
            );
        }
        
        if (!empty($atts['subject'])) {
            $tax_query[] = array(
                'taxonomy' => 'subject',
                'field' => 'slug',
                'terms' => $atts['subject'],
            );
        }
        
        if (!empty($tax_query)) {
            $args['tax_query'] = $tax_query;
            if (count($tax_query) > 1) {
                $args['tax_query']['relation'] = 'AND';
            }
        }
        
        $query = new WP_Query($args);
        
        ob_start();
        
        if ($query->have_posts()) {
            echo '<div class="edu-grid">';
            
            while ($query->have_posts()) {
                $query->the_post();
                self::render_card(get_post());
            }
            
            echo '</div>';
        } else {
            echo '<div class="edu-no-results">';
            echo '<div class="edu-no-results-icon">😕</div>';
            echo '<p>موردی یافت نشد.</p>';
            echo '</div>';
        }
        
        wp_reset_postdata();
        
        return ob_get_clean();
    }
    
    /**
     * Render single card
     */
    private static function render_card($post) {
        $post_id = $post->ID;
        $rating = floatval(get_post_meta($post_id, 'rating', true));
        $phone = get_post_meta($post_id, 'phone', true);
        $city_terms = wp_get_post_terms($post_id, 'city');
        $subject_terms = wp_get_post_terms($post_id, 'subject');
        
        $thumbnail = get_the_post_thumbnail_url($post_id, 'medium');
        if (!$thumbnail) {
            $thumbnail = EDU_DIR_ASSETS . 'images/placeholder.jpg';
        }
        
        $excerpt = has_excerpt($post_id) ? get_the_excerpt($post_id) : wp_trim_words($post->post_content, 25);
        
        ?>
        <div class="edu-card">
            <!-- Image -->
            <div class="edu-card-image">
                <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr(get_the_title($post_id)); ?>">
                <?php if ($rating > 0): ?>
                    <span class="edu-rating-badge">
                        <span class="edu-rating-badge-icon">⭐</span>
                        <?php echo number_format($rating, 1); ?>
                    </span>
                <?php endif; ?>
            </div>
            
            <!-- Content -->
            <div class="edu-card-content">
                <h3 class="edu-card-title">
                    <a href="<?php echo get_permalink($post_id); ?>">
                        <?php echo get_the_title($post_id); ?>
                    </a>
                </h3>
                
                <?php if ($rating > 0): ?>
                    <div class="edu-rating">
                        <?php echo self::render_stars($rating); ?>
                    </div>
                <?php endif; ?>
                
                <!-- Meta Info -->
                <?php if (!empty($city_terms) || !empty($subject_terms)): ?>
                    <div class="edu-card-meta">
                        <?php if (!empty($city_terms) && !is_wp_error($city_terms)): ?>
                            <span class="edu-meta-item">
                                <span class="edu-meta-icon">📍</span>
                                <?php echo esc_html($city_terms[0]->name); ?>
                            </span>
                        <?php endif; ?>
                        
                        <?php if (!empty($subject_terms) && !is_wp_error($subject_terms)): ?>
                            <span class="edu-meta-item">
                                <span class="edu-meta-icon">📚</span>
                                <?php 
                                $subjects = array_slice($subject_terms, 0, 2);
                                echo esc_html(implode('، ', array_map(function($term) { 
                                    return $term->name; 
                                }, $subjects)));
                                if (count($subject_terms) > 2) {
                                    echo ' +' . (count($subject_terms) - 2);
                                }
                                ?>
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Excerpt -->
                <?php if ($excerpt): ?>
                    <p class="edu-excerpt"><?php echo esc_html($excerpt); ?></p>
                <?php endif; ?>
                
                <!-- Actions -->
                <div class="edu-card-actions">
                    <a href="<?php echo get_permalink($post_id); ?>" class="edu-btn-primary">
                        مشاهده بیشتر
                    </a>
                    <?php if ($phone): ?>
                        <a href="tel:<?php echo esc_attr($phone); ?>" class="edu-btn-secondary">
                            تماس
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render star rating
     */
    private static function render_stars($rating) {
        $output = '';
        $full_stars = floor($rating);
        $has_half = ($rating - $full_stars) >= 0.5;
        $empty_stars = 5 - $full_stars - ($has_half ? 1 : 0);
        
        // Full stars
        for ($i = 0; $i < $full_stars; $i++) {
            $output .= '<span class="edu-star edu-star-full">★</span>';
        }
        
        // Half star
        if ($has_half) {
            $output .= '<span class="edu-star edu-star-half">★</span>';
        }
        
        // Empty stars
        for ($i = 0; $i < $empty_stars; $i++) {
            $output .= '<span class="edu-star edu-star-empty">☆</span>';
        }
        
        return $output;
    }
}
