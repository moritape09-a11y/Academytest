<?php
/**
 * Shortcodes
 */

defined('ABSPATH') || exit;

// شورت کد آموزشگاه‌ها
add_shortcode('academies', 'edu_academies_shortcode');
function edu_academies_shortcode($atts) {
    $atts = shortcode_atts(array(
        'limit' => 12,
        'city' => '',
        'subject' => '',
    ), $atts);
    
    return edu_render_posts('academy', $atts);
}

// شورت کد مدارس
add_shortcode('schools', 'edu_schools_shortcode');
function edu_schools_shortcode($atts) {
    $atts = shortcode_atts(array(
        'limit' => 12,
        'city' => '',
        'subject' => '',
    ), $atts);
    
    return edu_render_posts('school', $atts);
}

// شورت کد معلمین
add_shortcode('teachers', 'edu_teachers_shortcode');
function edu_teachers_shortcode($atts) {
    $atts = shortcode_atts(array(
        'limit' => 12,
        'city' => '',
        'subject' => '',
    ), $atts);
    
    return edu_render_posts('teacher', $atts);
}

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
