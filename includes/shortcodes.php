<?php
/**
 * Shortcodes
 */

defined('ABSPATH') || exit;

// شورت کد آموزشگاه‌ها
function edu_academies_shortcode($atts) {
    $atts = shortcode_atts(array(
        'limit' => 12,
        'city' => '',
        'subject' => '',
    ), $atts);
    
    return edu_render_posts('academy', $atts);
}
add_shortcode('academies', 'edu_academies_shortcode');

// شورت کد مدارس
function edu_schools_shortcode($atts) {
    $atts = shortcode_atts(array(
        'limit' => 12,
        'city' => '',
        'subject' => '',
    ), $atts);
    
    return edu_render_posts('school', $atts);
}
add_shortcode('schools', 'edu_schools_shortcode');

// شورت کد معلمین
function edu_teachers_shortcode($atts) {
    $atts = shortcode_atts(array(
        'limit' => 12,
        'city' => '',
        'subject' => '',
    ), $atts);
    
    return edu_render_posts('teacher', $atts);
}
add_shortcode('teachers', 'edu_teachers_shortcode');

// تابع نمایش پست‌ها
function edu_render_posts($post_type, $atts) {
    $args = array(
        'post_type' => $post_type,
        'posts_per_page' => intval($atts['limit']),
        'post_status' => 'publish',
        'meta_key' => 'display_priority',
        'orderby' => array(
            'meta_value_num' => 'ASC',
            'date' => 'DESC',
        ),
    );
    
    // فیلتر تاکسونومی
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
    }
    
    $query = new WP_Query($args);
    
    ob_start();
    
    if ($query->have_posts()) {
        echo '<div class="edu-grid">';
        
        while ($query->have_posts()) {
            $query->the_post();
            edu_render_card();
        }
        
        echo '</div>';
    } else {
        echo '<p class="edu-no-results">موردی یافت نشد.</p>';
    }
    
    wp_reset_postdata();
    
    return ob_get_clean();
}

// تابع نمایش کارت
function edu_render_card() {
    $phone = get_post_meta(get_the_ID(), 'phone', true);
    $rating = get_post_meta(get_the_ID(), 'rating', true);
    if (empty($rating)) $rating = 5;
    ?>
    <div class="edu-card">
        <?php if (has_post_thumbnail()): ?>
            <div class="edu-card-image">
                <a href="<?php the_permalink(); ?>">
                    <?php the_post_thumbnail('medium'); ?>
                </a>
                <?php if ($rating): ?>
                    <div class="edu-rating-badge">
                        <?php echo edu_render_rating($rating); ?>
                        <span class="edu-rating-number"><?php echo number_format($rating, 1); ?></span>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <div class="edu-card-content">
            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
            
            <?php
            // نمایش امتیاز
            if ($rating): ?>
                <div class="edu-rating">
                    <?php echo edu_render_rating($rating); ?>
                    <span class="edu-rating-text">(<?php echo number_format($rating, 1); ?>)</span>
                </div>
            <?php endif; ?>
            
            <?php
            $terms = get_the_terms(get_the_ID(), 'city');
            if ($terms) {
                echo '<div class="edu-meta">';
                foreach ($terms as $term) {
                    echo '<span class="edu-tag">' . esc_html($term->name) . '</span>';
                }
                echo '</div>';
            }
            ?>
            
            <?php
            // نمایش خلاصه
            $excerpt = '';
            if (has_excerpt()) {
                $excerpt = get_the_excerpt();
            } else {
                $content = get_the_content();
                $content = strip_shortcodes($content);
                $content = wp_strip_all_tags($content);
                $excerpt = $content;
            }
            
            if (!empty($excerpt)) {
                echo '<p class="edu-excerpt">' . wp_trim_words($excerpt, 25, '...') . '</p>';
            }
            ?>
            
            <?php if ($phone): ?>
                <p class="edu-phone">📞 <?php echo esc_html($phone); ?></p>
            <?php endif; ?>
            
            <a href="<?php the_permalink(); ?>" class="edu-button">مشاهده جزئیات</a>
        </div>
    </div>
    <?php
}

// تابع نمایش ستاره‌ها
function edu_render_rating($rating) {
    $rating = floatval($rating);
    $full_stars = floor($rating);
    $half_star = ($rating - $full_stars) >= 0.5 ? 1 : 0;
    $empty_stars = 5 - $full_stars - $half_star;
    
    $output = '';
    
    // ستاره‌های پر
    for ($i = 0; $i < $full_stars; $i++) {
        $output .= '<span class="edu-star edu-star-full">⭐</span>';
    }
    
    // ستاره نیمه
    if ($half_star) {
        $output .= '<span class="edu-star edu-star-half">✨</span>';
    }
    
    // ستاره‌های خالی
    for ($i = 0; $i < $empty_stars; $i++) {
        $output .= '<span class="edu-star edu-star-empty">☆</span>';
    }
    
    return $output;
}

// شورت کد جستجوگر پیشرفته
function edu_search_shortcode($atts) {
    $atts = shortcode_atts(array(
        'show' => 'all', // all, academies, schools, teachers
    ), $atts);
    
    ob_start();
    
    // دریافت مقادیر جستجو
    $search_query = isset($_GET['edu_search']) ? sanitize_text_field($_GET['edu_search']) : '';
    $search_type = isset($_GET['edu_type']) ? sanitize_text_field($_GET['edu_type']) : 'all';
    $search_city = isset($_GET['edu_city']) ? sanitize_text_field($_GET['edu_city']) : '';
    $search_subject = isset($_GET['edu_subject']) ? sanitize_text_field($_GET['edu_subject']) : '';
    $search_rating = isset($_GET['edu_rating']) ? sanitize_text_field($_GET['edu_rating']) : '';
    
    ?>
    <div class="edu-advanced-search">
        <!-- هدر جستجو -->
        <div class="edu-search-header">
            <h2 class="edu-search-title">🔍 جستجوی پیشرفته</h2>
            <p class="edu-search-subtitle">آموزشگاه، مدرسه یا معلم مورد نظر خود را پیدا کنید</p>
        </div>
        
        <!-- فرم جستجو -->
        <form method="get" class="edu-search-form-advanced" action="">
            <!-- جستجوی اصلی -->
            <div class="edu-search-main">
                <div class="edu-search-icon">🔍</div>
                <input 
                    type="text" 
                    name="edu_search" 
                    class="edu-search-input-main" 
                    placeholder="نام آموزشگاه، مدرسه، معلم یا رشته را جستجو کنید..."
                    value="<?php echo esc_attr($search_query); ?>"
                >
                <button type="submit" class="edu-search-btn-main">جستجو</button>
            </div>
            
            <!-- فیلترهای پیشرفته -->
            <div class="edu-search-filters">
                <div class="edu-filter-group">
                    <label class="edu-filter-label">
                        <span class="edu-filter-icon">📋</span>
                        نوع
                    </label>
                    <select name="edu_type" class="edu-filter-select">
                        <option value="all" <?php selected($search_type, 'all'); ?>>همه</option>
                        <option value="academy" <?php selected($search_type, 'academy'); ?>>آموزشگاه</option>
                        <option value="school" <?php selected($search_type, 'school'); ?>>مدرسه</option>
                        <option value="teacher" <?php selected($search_type, 'teacher'); ?>>معلم</option>
                    </select>
                </div>
                
                <div class="edu-filter-group">
                    <label class="edu-filter-label">
                        <span class="edu-filter-icon">📍</span>
                        شهر
                    </label>
                    <select name="edu_city" class="edu-filter-select">
                        <option value="">همه شهرها</option>
                        <?php
                        $cities = get_terms(array('taxonomy' => 'city', 'hide_empty' => false));
                        if ($cities && !is_wp_error($cities)) {
                            foreach ($cities as $city) {
                                echo '<option value="' . esc_attr($city->slug) . '" ' . selected($search_city, $city->slug, false) . '>' . esc_html($city->name) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                
                <div class="edu-filter-group">
                    <label class="edu-filter-label">
                        <span class="edu-filter-icon">📚</span>
                        رشته
                    </label>
                    <select name="edu_subject" class="edu-filter-select">
                        <option value="">همه رشته‌ها</option>
                        <?php
                        $subjects = get_terms(array('taxonomy' => 'subject', 'hide_empty' => false));
                        if ($subjects && !is_wp_error($subjects)) {
                            foreach ($subjects as $subject) {
                                echo '<option value="' . esc_attr($subject->slug) . '" ' . selected($search_subject, $subject->slug, false) . '>' . esc_html($subject->name) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                
                <div class="edu-filter-group">
                    <label class="edu-filter-label">
                        <span class="edu-filter-icon">⭐</span>
                        امتیاز
                    </label>
                    <select name="edu_rating" class="edu-filter-select">
                        <option value="">همه امتیازات</option>
                        <option value="5" <?php selected($search_rating, '5'); ?>>⭐⭐⭐⭐⭐ (5)</option>
                        <option value="4" <?php selected($search_rating, '4'); ?>>⭐⭐⭐⭐ (4+)</option>
                        <option value="3" <?php selected($search_rating, '3'); ?>>⭐⭐⭐ (3+)</option>
                        <option value="2" <?php selected($search_rating, '2'); ?>>⭐⭐ (2+)</option>
                    </select>
                </div>
            </div>
            
            <!-- دکمه‌های اضافی -->
            <div class="edu-search-actions">
                <button type="submit" class="edu-btn-search">
                    <span class="edu-btn-icon">🔍</span>
                    اعمال فیلترها
                </button>
                <a href="<?php echo strtok($_SERVER['REQUEST_URI'], '?'); ?>" class="edu-btn-reset">
                    <span class="edu-btn-icon">🔄</span>
                    پاک کردن فیلترها
                </a>
            </div>
        </form>
        
        <?php
        // نمایش نتایج
        if ($search_query || $search_type != 'all' || $search_city || $search_subject || $search_rating) {
            echo '<div class="edu-search-results">';
            
            // شمارش کل نتایج
            $total_results = 0;
            $results_html = '';
            
            // تعیین نوع‌های مورد جستجو
            $post_types = array();
            if ($search_type == 'all' || $atts['show'] == 'all') {
                $post_types = array('academy', 'school', 'teacher');
            } elseif ($search_type != 'all') {
                $post_types = array($search_type);
            } else {
                if ($atts['show'] == 'academies') $post_types = array('academy');
                if ($atts['show'] == 'schools') $post_types = array('school');
                if ($atts['show'] == 'teachers') $post_types = array('teacher');
            }
            
            foreach ($post_types as $post_type) {
                $search_results = edu_advanced_search($post_type, $search_query, $search_city, $search_subject, $search_rating);
                
                if ($search_results->have_posts()) {
                    $total_results += $search_results->found_posts;
                    
                    $type_label = '';
                    if ($post_type == 'academy') $type_label = 'آموزشگاه‌ها';
                    if ($post_type == 'school') $type_label = 'مدارس';
                    if ($post_type == 'teacher') $type_label = 'معلمین';
                    
                    $results_html .= '<div class="edu-results-section">';
                    $results_html .= '<h3 class="edu-results-section-title">' . $type_label . ' <span class="edu-results-count">(' . $search_results->found_posts . ')</span></h3>';
                    $results_html .= '<div class="edu-grid">';
                    
                    while ($search_results->have_posts()) {
                        $search_results->the_post();
                        ob_start();
                        edu_render_card();
                        $results_html .= ob_get_clean();
                    }
                    
                    $results_html .= '</div></div>';
                }
                wp_reset_postdata();
            }
            
            // نمایش هدر نتایج
            echo '<div class="edu-results-header">';
            if ($total_results > 0) {
                echo '<h3 class="edu-results-title">✨ نتایج جستجو</h3>';
                echo '<p class="edu-results-meta">' . $total_results . ' مورد یافت شد</p>';
            } else {
                echo '<div class="edu-no-results-box">';
                echo '<div class="edu-no-results-icon">😕</div>';
                echo '<h3 class="edu-no-results-title">موردی یافت نشد</h3>';
                echo '<p class="edu-no-results-text">لطفاً فیلترهای مختلفی امتحان کنید یا عبارت دیگری جستجو کنید.</p>';
                echo '</div>';
            }
            echo '</div>';
            
            // نمایش نتایج
            echo $results_html;
            
            echo '</div>';
        }
        ?>
    </div>
    <?php
    
    return ob_get_clean();
}
add_shortcode('edu_search', 'edu_search_shortcode');

// تابع جستجوی پیشرفته
function edu_advanced_search($post_type, $search_query = '', $city = '', $subject = '', $rating = '') {
    $args = array(
        'post_type' => $post_type,
        'posts_per_page' => 50,
        'post_status' => 'publish',
        'meta_key' => 'display_priority',
        'orderby' => array(
            'meta_value_num' => 'ASC',
            'date' => 'DESC',
        ),
    );
    
    // جستجو در عنوان و محتوا
    if (!empty($search_query)) {
        $args['s'] = $search_query;
    }
    
    // فیلتر تاکسونومی
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
    
    if (count($tax_query) > 0) {
        $args['tax_query'] = $tax_query;
    }
    
    // فیلتر امتیاز
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
    
    return new WP_Query($args);
}
