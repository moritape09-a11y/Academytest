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
        wp_reset_postdata();
    } else {
        echo '<p class="edu-no-results">موردی یافت نشد.</p>';
    }
    
    return ob_get_clean();
}

// تابع نمایش کارت
function edu_render_card() {
    $phone = get_post_meta(get_the_ID(), 'phone', true);
    ?>
    <div class="edu-card">
        <?php if (has_post_thumbnail()): ?>
            <div class="edu-card-image">
                <a href="<?php the_permalink(); ?>">
                    <?php the_post_thumbnail('medium'); ?>
                </a>
            </div>
        <?php endif; ?>
        
        <div class="edu-card-content">
            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
            
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
            
            <?php if (has_excerpt()): ?>
                <p><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
            <?php endif; ?>
            
            <?php if ($phone): ?>
                <p class="edu-phone">📞 <?php echo esc_html($phone); ?></p>
            <?php endif; ?>
            
            <a href="<?php the_permalink(); ?>" class="edu-button">مشاهده جزئیات</a>
        </div>
    </div>
    <?php
}

// شورت کد فرم جستجو و فیلتر
function edu_search_shortcode($atts) {
    $atts = shortcode_atts(array(
        'show' => 'all', // all, academies, schools, teachers
    ), $atts);
    
    ob_start();
    ?>
    <div class="edu-search-box">
        <form method="get" class="edu-search-form">
            <div class="edu-search-row">
                <div class="edu-search-field">
                    <input type="text" name="s" placeholder="جستجو..." value="<?php echo get_search_query(); ?>" class="edu-search-input">
                </div>
                
                <div class="edu-search-field">
                    <select name="edu_city" class="edu-search-select">
                        <option value="">همه شهرها</option>
                        <?php
                        $cities = get_terms(array('taxonomy' => 'city', 'hide_empty' => false));
                        if ($cities && !is_wp_error($cities)) {
                            foreach ($cities as $city) {
                                $selected = (isset($_GET['edu_city']) && $_GET['edu_city'] == $city->slug) ? 'selected' : '';
                                echo '<option value="' . esc_attr($city->slug) . '" ' . $selected . '>' . esc_html($city->name) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                
                <div class="edu-search-field">
                    <select name="edu_subject" class="edu-search-select">
                        <option value="">همه رشته‌ها</option>
                        <?php
                        $subjects = get_terms(array('taxonomy' => 'subject', 'hide_empty' => false));
                        if ($subjects && !is_wp_error($subjects)) {
                            foreach ($subjects as $subject) {
                                $selected = (isset($_GET['edu_subject']) && $_GET['edu_subject'] == $subject->slug) ? 'selected' : '';
                                echo '<option value="' . esc_attr($subject->slug) . '" ' . $selected . '>' . esc_html($subject->name) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                
                <div class="edu-search-field">
                    <button type="submit" class="edu-search-button">جستجو</button>
                </div>
            </div>
        </form>
    </div>
    
    <?php
    // نمایش نتایج فیلتر شده
    if (isset($_GET['edu_city']) || isset($_GET['edu_subject']) || !empty(get_search_query())) {
        $city = isset($_GET['edu_city']) ? sanitize_text_field($_GET['edu_city']) : '';
        $subject = isset($_GET['edu_subject']) ? sanitize_text_field($_GET['edu_subject']) : '';
        
        $filter_atts = array(
            'limit' => 12,
            'city' => $city,
            'subject' => $subject,
        );
        
        echo '<h3 class="edu-results-title">نتایج جستجو</h3>';
        
        if ($atts['show'] == 'all' || $atts['show'] == 'academies') {
            echo '<h4>آموزشگاه‌ها</h4>';
            echo edu_render_posts('academy', $filter_atts);
        }
        
        if ($atts['show'] == 'all' || $atts['show'] == 'schools') {
            echo '<h4>مدارس</h4>';
            echo edu_render_posts('school', $filter_atts);
        }
        
        if ($atts['show'] == 'all' || $atts['show'] == 'teachers') {
            echo '<h4>معلمین</h4>';
            echo edu_render_posts('teacher', $filter_atts);
        }
    }
    ?>
    
    <?php
    return ob_get_clean();
}
add_shortcode('edu_search', 'edu_search_shortcode');
