<?php
/**
 * مدیریت جستجو و فیلتر
 */

if (!defined('ABSPATH')) {
    exit;
}

class ED_Search_Filter {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('pre_get_posts', array($this, 'modify_search_query'));
        add_action('wp_ajax_ed_filter', array($this, 'ajax_filter'));
        add_action('wp_ajax_nopriv_ed_filter', array($this, 'ajax_filter'));
    }
    
    /**
     * تغییر کوئری جستجو
     */
    public function modify_search_query($query) {
        if (!is_admin() && $query->is_search() && $query->is_main_query()) {
            // اضافه کردن custom post types به جستجو
            if (isset($_GET['post_type']) && !empty($_GET['post_type'])) {
                $query->set('post_type', sanitize_text_field($_GET['post_type']));
            } else {
                $query->set('post_type', array('post', 'academy', 'school', 'teacher'));
            }
            
            // فیلتر بر اساس تاکسونومی
            $tax_query = array('relation' => 'AND');
            
            if (isset($_GET['city']) && !empty($_GET['city'])) {
                $tax_query[] = array(
                    'taxonomy' => 'city',
                    'field' => 'slug',
                    'terms' => sanitize_text_field($_GET['city']),
                );
            }
            
            if (isset($_GET['subject']) && !empty($_GET['subject'])) {
                $tax_query[] = array(
                    'taxonomy' => 'subject',
                    'field' => 'slug',
                    'terms' => sanitize_text_field($_GET['subject']),
                );
            }
            
            if (isset($_GET['specialty']) && !empty($_GET['specialty'])) {
                $tax_query[] = array(
                    'taxonomy' => 'specialty',
                    'field' => 'slug',
                    'terms' => sanitize_text_field($_GET['specialty']),
                );
            }
            
            if (count($tax_query) > 1) {
                $query->set('tax_query', $tax_query);
            }
        }
        
        // تغییر آرشیوهای پیش‌فرض
        if (!is_admin() && $query->is_post_type_archive(array('academy', 'school', 'teacher')) && $query->is_main_query()) {
            $query->set('posts_per_page', 12);
        }
    }
    
    /**
     * فیلتر AJAX
     */
    public function ajax_filter() {
        check_ajax_referer('ed-nonce', 'nonce');
        
        $post_type = isset($_POST['post_type']) ? sanitize_text_field($_POST['post_type']) : 'academy';
        $city = isset($_POST['city']) ? sanitize_text_field($_POST['city']) : '';
        $subject = isset($_POST['subject']) ? sanitize_text_field($_POST['subject']) : '';
        $specialty = isset($_POST['specialty']) ? sanitize_text_field($_POST['specialty']) : '';
        $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;
        
        $args = array(
            'post_type' => $post_type,
            'posts_per_page' => 12,
            'paged' => $paged,
            'post_status' => 'publish',
        );
        
        $tax_query = array('relation' => 'AND');
        
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
        
        if ($post_type === 'teacher' && !empty($specialty)) {
            $tax_query[] = array(
                'taxonomy' => 'specialty',
                'field' => 'slug',
                'terms' => $specialty,
            );
        }
        
        if (count($tax_query) > 1) {
            $args['tax_query'] = $tax_query;
        }
        
        $query = new WP_Query($args);
        
        ob_start();
        
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                // استفاده از همان متد render_card از کلاس Shortcodes
                $this->render_ajax_card(get_post_type());
            }
            wp_reset_postdata();
        } else {
            echo '<p class="ed-no-results">موردی یافت نشد.</p>';
        }
        
        $html = ob_get_clean();
        
        wp_send_json_success(array(
            'html' => $html,
            'found_posts' => $query->found_posts,
            'max_pages' => $query->max_num_pages,
        ));
    }
    
    /**
     * رندر کارت برای AJAX
     */
    private function render_ajax_card($post_type) {
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
}
