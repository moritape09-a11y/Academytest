<?php
/**
 * مدیریت Shortcodes
 */

if (!defined('ABSPATH')) {
    exit;
}

class ED_Shortcodes {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_shortcode('ed_academies', array($this, 'academies_shortcode'));
        add_shortcode('ed_schools', array($this, 'schools_shortcode'));
        add_shortcode('ed_teachers', array($this, 'teachers_shortcode'));
        add_shortcode('ed_search', array($this, 'search_shortcode'));
    }
    
    /**
     * Shortcode نمایش آموزشگاه‌ها
     */
    public function academies_shortcode($atts) {
        $atts = shortcode_atts(array(
            'limit' => 12,
            'columns' => 3,
            'city' => '',
            'subject' => '',
            'orderby' => 'date',
            'order' => 'DESC',
        ), $atts);
        
        return $this->render_items('academy', $atts);
    }
    
    /**
     * Shortcode نمایش مدارس
     */
    public function schools_shortcode($atts) {
        $atts = shortcode_atts(array(
            'limit' => 12,
            'columns' => 3,
            'city' => '',
            'subject' => '',
            'orderby' => 'date',
            'order' => 'DESC',
        ), $atts);
        
        return $this->render_items('school', $atts);
    }
    
    /**
     * Shortcode نمایش معلمین
     */
    public function teachers_shortcode($atts) {
        $atts = shortcode_atts(array(
            'limit' => 12,
            'columns' => 4,
            'city' => '',
            'subject' => '',
            'specialty' => '',
            'orderby' => 'date',
            'order' => 'DESC',
        ), $atts);
        
        return $this->render_items('teacher', $atts);
    }
    
    /**
     * رندر آیتم‌ها
     */
    private function render_items($post_type, $atts) {
        $args = array(
            'post_type' => $post_type,
            'posts_per_page' => intval($atts['limit']),
            'orderby' => $atts['orderby'],
            'order' => $atts['order'],
            'post_status' => 'publish',
        );
        
        // فیلتر بر اساس تاکسونومی
        $tax_query = array('relation' => 'AND');
        
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
        
        if ($post_type === 'teacher' && !empty($atts['specialty'])) {
            $tax_query[] = array(
                'taxonomy' => 'specialty',
                'field' => 'slug',
                'terms' => $atts['specialty'],
            );
        }
        
        if (count($tax_query) > 1) {
            $args['tax_query'] = $tax_query;
        }
        
        $query = new WP_Query($args);
        
        ob_start();
        
        if ($query->have_posts()) {
            $columns = intval($atts['columns']);
            echo '<div class="ed-grid ed-grid-' . $columns . '">';
            
            while ($query->have_posts()) {
                $query->the_post();
                $this->render_card(get_post_type());
            }
            
            echo '</div>';
            wp_reset_postdata();
        } else {
            echo '<p class="ed-no-results">موردی یافت نشد.</p>';
        }
        
        return ob_get_clean();
    }
    
    /**
     * رندر کارت تک آیتم
     */
    private function render_card($post_type) {
        $post_id = get_the_ID();
        $rating = get_post_meta($post_id, '_ed_rating', true);
        $phone = get_post_meta($post_id, '_ed_phone', true);
        
        ?>
        <div class="ed-card <?php echo 'ed-card-' . $post_type; ?>">
            <div class="ed-card-image">
                <?php if (has_post_thumbnail()): ?>
                    <a href="<?php the_permalink(); ?>">
                        <?php the_post_thumbnail('medium'); ?>
                    </a>
                <?php else: ?>
                    <a href="<?php the_permalink(); ?>">
                        <img src="<?php echo ED_PLUGIN_URL . 'assets/images/placeholder.jpg'; ?>" alt="<?php the_title(); ?>">
                    </a>
                <?php endif; ?>
                <?php if ($rating): ?>
                    <div class="ed-rating">
                        <span class="ed-rating-stars"><?php echo str_repeat('★', floor($rating)) . (fmod($rating, 1) >= 0.5 ? '☆' : ''); ?></span>
                        <span class="ed-rating-number"><?php echo $rating; ?></span>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="ed-card-content">
                <h3 class="ed-card-title">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h3>
                
                <?php
                // نمایش تاکسونومی‌ها
                $taxonomies = $post_type === 'teacher' ? array('specialty', 'city') : array('subject', 'city');
                foreach ($taxonomies as $taxonomy) {
                    $terms = get_the_terms($post_id, $taxonomy);
                    if ($terms && !is_wp_error($terms)) {
                        echo '<div class="ed-card-meta ed-meta-' . $taxonomy . '">';
                        foreach ($terms as $term) {
                            echo '<span class="ed-tag">' . esc_html($term->name) . '</span>';
                        }
                        echo '</div>';
                    }
                }
                ?>
                
                <?php if (has_excerpt()): ?>
                    <div class="ed-card-excerpt">
                        <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($phone): ?>
                    <div class="ed-card-phone">
                        <span class="dashicons dashicons-phone"></span>
                        <?php echo esc_html($phone); ?>
                    </div>
                <?php endif; ?>
                
                <a href="<?php the_permalink(); ?>" class="ed-card-button">مشاهده جزئیات</a>
            </div>
        </div>
        <?php
    }
    
    /**
     * Shortcode فرم جستجو
     */
    public function search_shortcode($atts) {
        $atts = shortcode_atts(array(
            'type' => 'all', // all, academy, school, teacher
        ), $atts);
        
        ob_start();
        ?>
        <div class="ed-search-form">
            <form method="get" action="<?php echo esc_url(home_url('/')); ?>">
                <div class="ed-search-row">
                    <div class="ed-search-field">
                        <input type="text" name="s" placeholder="جستجو..." value="<?php echo get_search_query(); ?>">
                    </div>
                    
                    <?php if ($atts['type'] !== 'all'): ?>
                        <input type="hidden" name="post_type" value="<?php echo esc_attr($atts['type']); ?>">
                    <?php else: ?>
                        <div class="ed-search-field">
                            <select name="post_type">
                                <option value="">همه</option>
                                <option value="academy">آموزشگاه‌ها</option>
                                <option value="school">مدارس</option>
                                <option value="teacher">معلمین</option>
                            </select>
                        </div>
                    <?php endif; ?>
                    
                    <div class="ed-search-field">
                        <?php
                        $cities = get_terms(array('taxonomy' => 'city', 'hide_empty' => false));
                        if ($cities && !is_wp_error($cities)) {
                            echo '<select name="city">';
                            echo '<option value="">همه شهرها</option>';
                            foreach ($cities as $city) {
                                echo '<option value="' . $city->slug . '">' . $city->name . '</option>';
                            }
                            echo '</select>';
                        }
                        ?>
                    </div>
                    
                    <div class="ed-search-field">
                        <?php
                        $subjects = get_terms(array('taxonomy' => 'subject', 'hide_empty' => false));
                        if ($subjects && !is_wp_error($subjects)) {
                            echo '<select name="subject">';
                            echo '<option value="">همه رشته‌ها</option>';
                            foreach ($subjects as $subject) {
                                echo '<option value="' . $subject->slug . '">' . $subject->name . '</option>';
                            }
                            echo '</select>';
                        }
                        ?>
                    </div>
                    
                    <div class="ed-search-field">
                        <button type="submit" class="ed-search-button">جستجو</button>
                    </div>
                </div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }
}
