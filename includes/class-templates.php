<?php
/**
 * مدیریت تمپلیت‌ها
 */

if (!defined('ABSPATH')) {
    exit;
}

class ED_Templates {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_filter('single_template', array($this, 'load_single_template'));
        add_filter('archive_template', array($this, 'load_archive_template'));
        add_filter('the_content', array($this, 'add_custom_content'));
    }
    
    /**
     * بارگذاری تمپلیت تک صفحه
     */
    public function load_single_template($template) {
        global $post;
        
        if (in_array($post->post_type, array('academy', 'school', 'teacher'))) {
            $plugin_template = ED_PLUGIN_DIR . 'templates/single-' . $post->post_type . '.php';
            if (file_exists($plugin_template)) {
                return $plugin_template;
            }
        }
        
        return $template;
    }
    
    /**
     * بارگذاری تمپلیت آرشیو
     */
    public function load_archive_template($template) {
        if (is_post_type_archive(array('academy', 'school', 'teacher'))) {
            $post_type = get_query_var('post_type');
            $plugin_template = ED_PLUGIN_DIR . 'templates/archive-' . $post_type . '.php';
            if (file_exists($plugin_template)) {
                return $plugin_template;
            }
        }
        
        return $template;
    }
    
    /**
     * افزودن محتوای سفارشی به صفحات تک
     */
    public function add_custom_content($content) {
        if (!is_singular(array('academy', 'school', 'teacher'))) {
            return $content;
        }
        
        global $post;
        $post_type = get_post_type();
        
        ob_start();
        ?>
        <div class="ed-single-content">
            <?php echo $content; ?>
            
            <div class="ed-info-boxes">
                <?php if (in_array($post_type, array('academy', 'school'))): ?>
                    <?php $this->render_institution_info($post->ID); ?>
                <?php elseif ($post_type === 'teacher'): ?>
                    <?php $this->render_teacher_info($post->ID); ?>
                <?php endif; ?>
            </div>
            
            <div class="ed-taxonomies">
                <?php $this->render_taxonomies($post->ID, $post_type); ?>
            </div>
            
            <?php if (in_array($post_type, array('academy', 'school'))): ?>
                <div class="ed-facilities">
                    <?php $this->render_facilities($post->ID); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($post_type === 'teacher'): ?>
                <div class="ed-related-institutions">
                    <?php $this->render_related_institutions($post->ID); ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
        
        return ob_get_clean();
    }
    
    /**
     * نمایش اطلاعات آموزشگاه/مدرسه
     */
    private function render_institution_info($post_id) {
        $phone = get_post_meta($post_id, '_ed_phone', true);
        $email = get_post_meta($post_id, '_ed_email', true);
        $website = get_post_meta($post_id, '_ed_website', true);
        $address = get_post_meta($post_id, '_ed_address', true);
        $rating = get_post_meta($post_id, '_ed_rating', true);
        ?>
        <div class="ed-info-box ed-contact-box">
            <h3>اطلاعات تماس</h3>
            <?php if ($phone): ?>
                <p><span class="dashicons dashicons-phone"></span> <strong>تلفن:</strong> <a href="tel:<?php echo esc_attr($phone); ?>"><?php echo esc_html($phone); ?></a></p>
            <?php endif; ?>
            <?php if ($email): ?>
                <p><span class="dashicons dashicons-email"></span> <strong>ایمیل:</strong> <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a></p>
            <?php endif; ?>
            <?php if ($website): ?>
                <p><span class="dashicons dashicons-admin-site"></span> <strong>وبسایت:</strong> <a href="<?php echo esc_url($website); ?>" target="_blank"><?php echo esc_html($website); ?></a></p>
            <?php endif; ?>
            <?php if ($address): ?>
                <p><span class="dashicons dashicons-location"></span> <strong>آدرس:</strong> <?php echo esc_html($address); ?></p>
            <?php endif; ?>
            <?php if ($rating): ?>
                <p><span class="dashicons dashicons-star-filled"></span> <strong>امتیاز:</strong> <?php echo esc_html($rating); ?> از 5</p>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * نمایش اطلاعات معلم
     */
    private function render_teacher_info($post_id) {
        $experience = get_post_meta($post_id, '_ed_experience', true);
        $education = get_post_meta($post_id, '_ed_education', true);
        $phone = get_post_meta($post_id, '_ed_phone', true);
        $email = get_post_meta($post_id, '_ed_email', true);
        $price = get_post_meta($post_id, '_ed_price', true);
        $rating = get_post_meta($post_id, '_ed_rating', true);
        
        $education_labels = array(
            'diploma' => 'دیپلم',
            'associate' => 'کاردانی',
            'bachelor' => 'کارشناسی',
            'master' => 'کارشناسی ارشد',
            'phd' => 'دکترا',
        );
        ?>
        <div class="ed-info-box ed-teacher-box">
            <h3>اطلاعات معلم</h3>
            <?php if ($experience): ?>
                <p><span class="dashicons dashicons-awards"></span> <strong>سابقه تدریس:</strong> <?php echo esc_html($experience); ?> سال</p>
            <?php endif; ?>
            <?php if ($education): ?>
                <p><span class="dashicons dashicons-book"></span> <strong>مدرک تحصیلی:</strong> <?php echo esc_html($education_labels[$education] ?? $education); ?></p>
            <?php endif; ?>
            <?php if ($phone): ?>
                <p><span class="dashicons dashicons-phone"></span> <strong>تلفن:</strong> <a href="tel:<?php echo esc_attr($phone); ?>"><?php echo esc_html($phone); ?></a></p>
            <?php endif; ?>
            <?php if ($email): ?>
                <p><span class="dashicons dashicons-email"></span> <strong>ایمیل:</strong> <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a></p>
            <?php endif; ?>
            <?php if ($price): ?>
                <p><span class="dashicons dashicons-money"></span> <strong>هزینه:</strong> <?php echo number_format($price); ?> تومان/ساعت</p>
            <?php endif; ?>
            <?php if ($rating): ?>
                <p><span class="dashicons dashicons-star-filled"></span> <strong>امتیاز:</strong> <?php echo esc_html($rating); ?> از 5</p>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * نمایش تاکسونومی‌ها
     */
    private function render_taxonomies($post_id, $post_type) {
        $taxonomies = $post_type === 'teacher' 
            ? array('subject' => 'رشته‌ها', 'specialty' => 'تخصص‌ها', 'city' => 'شهر')
            : array('subject' => 'رشته‌ها', 'city' => 'شهر', 'grade_level' => 'مقطع تحصیلی');
        
        foreach ($taxonomies as $taxonomy => $label) {
            $terms = get_the_terms($post_id, $taxonomy);
            if ($terms && !is_wp_error($terms)) {
                echo '<div class="ed-taxonomy-group">';
                echo '<strong>' . esc_html($label) . ':</strong> ';
                $term_names = array_map(function($term) {
                    return '<a href="' . get_term_link($term) . '">' . esc_html($term->name) . '</a>';
                }, $terms);
                echo implode(', ', $term_names);
                echo '</div>';
            }
        }
    }
    
    /**
     * نمایش امکانات
     */
    private function render_facilities($post_id) {
        $facilities = get_post_meta($post_id, '_ed_facilities', true);
        if (!empty($facilities) && is_array($facilities)) {
            $facility_labels = array(
                'parking' => 'پارکینگ',
                'library' => 'کتابخانه',
                'lab' => 'آزمایشگاه',
                'cafeteria' => 'سلف‌سرویس/بوفه',
                'gym' => 'سالن ورزشی',
                'pool' => 'استخر',
                'wifi' => 'وای‌فای رایگان',
                'ac' => 'سیستم گرمایش و سرمایش',
                'security' => 'سیستم امنیتی',
                'bus' => 'سرویس ایاب و ذهاب',
            );
            
            echo '<h3>امکانات و ویژگی‌ها</h3>';
            echo '<ul class="ed-facilities-list">';
            foreach ($facilities as $facility) {
                if (isset($facility_labels[$facility])) {
                    echo '<li><span class="dashicons dashicons-yes"></span> ' . esc_html($facility_labels[$facility]) . '</li>';
                }
            }
            echo '</ul>';
        }
    }
    
    /**
     * نمایش آموزشگاه‌ها/مدارس مرتبط با معلم
     */
    private function render_related_institutions($post_id) {
        $related = get_post_meta($post_id, '_ed_related_institutions', true);
        if (!empty($related) && is_array($related)) {
            echo '<h3>آموزشگاه‌ها و مدارس</h3>';
            echo '<ul class="ed-related-list">';
            foreach ($related as $item) {
                list($type, $id) = explode('_', $item);
                $post = get_post($id);
                if ($post) {
                    echo '<li><a href="' . get_permalink($id) . '">' . esc_html($post->post_title) . '</a></li>';
                }
            }
            echo '</ul>';
        }
    }
}
