<?php
/**
 * Meta Boxes Handler
 * 
 * @package EducationalDirectory
 * @since 3.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class EDU_Meta_Boxes {
    
    /**
     * Initialize
     */
    public static function init() {
        add_action('add_meta_boxes', array(__CLASS__, 'add_meta_boxes'));
        add_action('save_post', array(__CLASS__, 'save_meta_boxes'), 10, 2);
    }
    
    /**
     * Add all meta boxes
     */
    public static function add_meta_boxes() {
        $post_types = array('academy', 'school', 'teacher');
        
        foreach ($post_types as $post_type) {
            // Contact Info
            add_meta_box(
                'edu_contact_info',
                '📞 اطلاعات تماس',
                array(__CLASS__, 'render_contact_info_meta_box'),
                $post_type,
                'normal',
                'high'
            );
            
            // Social Media
            add_meta_box(
                'edu_social_media',
                '📱 شبکه‌های اجتماعی',
                array(__CLASS__, 'render_social_media_meta_box'),
                $post_type,
                'side',
                'default'
            );
            
            // Display Settings
            add_meta_box(
                'edu_display_settings',
                '⚙️ تنظیمات نمایش',
                array(__CLASS__, 'render_display_settings_meta_box'),
                $post_type,
                'side',
                'default'
            );
        }
        
        // Teacher-specific meta box
        add_meta_box(
            'edu_teacher_info',
            '👨‍🏫 اطلاعات معلم',
            array(__CLASS__, 'render_teacher_info_meta_box'),
            'teacher',
            'normal',
            'high'
        );
    }
    
    /**
     * Render Contact Info Meta Box
     */
    public static function render_contact_info_meta_box($post) {
        wp_nonce_field('edu_contact_info_nonce', 'edu_contact_info_nonce_field');
        
        $phone = get_post_meta($post->ID, 'phone', true);
        $email = get_post_meta($post->ID, 'email', true);
        $website = get_post_meta($post->ID, 'website', true);
        $address = get_post_meta($post->ID, 'address', true);
        ?>
        <div class="edu-meta-box">
            <p>
                <label for="edu_phone"><strong>📞 تلفن:</strong></label><br>
                <input type="text" id="edu_phone" name="edu_phone" value="<?php echo esc_attr($phone); ?>" 
                       class="widefat" placeholder="مثال: 021-12345678">
            </p>
            
            <p>
                <label for="edu_email"><strong>✉️ ایمیل:</strong></label><br>
                <input type="email" id="edu_email" name="edu_email" value="<?php echo esc_attr($email); ?>" 
                       class="widefat" placeholder="example@domain.com">
            </p>
            
            <p>
                <label for="edu_website"><strong>🌐 وب‌سایت:</strong></label><br>
                <input type="url" id="edu_website" name="edu_website" value="<?php echo esc_attr($website); ?>" 
                       class="widefat" placeholder="https://example.com">
            </p>
            
            <p>
                <label for="edu_address"><strong>📍 آدرس:</strong></label><br>
                <textarea id="edu_address" name="edu_address" class="widefat" rows="3" 
                          placeholder="آدرس کامل را وارد کنید"><?php echo esc_textarea($address); ?></textarea>
            </p>
        </div>
        <?php
    }
    
    /**
     * Render Social Media Meta Box
     */
    public static function render_social_media_meta_box($post) {
        wp_nonce_field('edu_social_media_nonce', 'edu_social_media_nonce_field');
        
        $telegram = get_post_meta($post->ID, 'telegram', true);
        $instagram = get_post_meta($post->ID, 'instagram', true);
        $whatsapp = get_post_meta($post->ID, 'whatsapp', true);
        ?>
        <div class="edu-meta-box">
            <p>
                <label for="edu_telegram"><strong>📢 تلگرام:</strong></label><br>
                <input type="text" id="edu_telegram" name="edu_telegram" value="<?php echo esc_attr($telegram); ?>" 
                       class="widefat" placeholder="@username یا لینک کامل">
                <small>مثال: @myacademy یا https://t.me/myacademy</small>
            </p>
            
            <p>
                <label for="edu_instagram"><strong>📸 اینستاگرام:</strong></label><br>
                <input type="text" id="edu_instagram" name="edu_instagram" value="<?php echo esc_attr($instagram); ?>" 
                       class="widefat" placeholder="@username یا لینک کامل">
                <small>مثال: @myacademy یا https://instagram.com/myacademy</small>
            </p>
            
            <p>
                <label for="edu_whatsapp"><strong>💬 واتساپ:</strong></label><br>
                <input type="text" id="edu_whatsapp" name="edu_whatsapp" value="<?php echo esc_attr($whatsapp); ?>" 
                       class="widefat" placeholder="شماره با کد کشور">
                <small>مثال: 989123456789</small>
            </p>
        </div>
        <?php
    }
    
    /**
     * Render Display Settings Meta Box
     */
    public static function render_display_settings_meta_box($post) {
        wp_nonce_field('edu_display_settings_nonce', 'edu_display_settings_nonce_field');
        
        $priority = get_post_meta($post->ID, 'display_priority', true);
        $rating = get_post_meta($post->ID, 'rating', true);
        
        if (empty($priority)) $priority = 50;
        if (empty($rating)) $rating = 0;
        ?>
        <div class="edu-meta-box">
            <p>
                <label for="edu_priority"><strong>🔝 اولویت نمایش:</strong></label><br>
                <input type="number" id="edu_priority" name="edu_display_priority" 
                       value="<?php echo esc_attr($priority); ?>" class="small-text" min="0" max="100">
                <small>عدد کمتر = اولویت بالاتر (0-100)</small>
            </p>
            
            <p>
                <label for="edu_rating"><strong>⭐ امتیاز:</strong></label><br>
                <select id="edu_rating" name="edu_rating" class="widefat">
                    <option value="0" <?php selected($rating, '0'); ?>>بدون امتیاز</option>
                    <option value="1" <?php selected($rating, '1'); ?>>⭐ (1 ستاره)</option>
                    <option value="1.5" <?php selected($rating, '1.5'); ?>>⭐✨ (1.5 ستاره)</option>
                    <option value="2" <?php selected($rating, '2'); ?>>⭐⭐ (2 ستاره)</option>
                    <option value="2.5" <?php selected($rating, '2.5'); ?>>⭐⭐✨ (2.5 ستاره)</option>
                    <option value="3" <?php selected($rating, '3'); ?>>⭐⭐⭐ (3 ستاره)</option>
                    <option value="3.5" <?php selected($rating, '3.5'); ?>>⭐⭐⭐✨ (3.5 ستاره)</option>
                    <option value="4" <?php selected($rating, '4'); ?>>⭐⭐⭐⭐ (4 ستاره)</option>
                    <option value="4.5" <?php selected($rating, '4.5'); ?>>⭐⭐⭐⭐✨ (4.5 ستاره)</option>
                    <option value="5" <?php selected($rating, '5'); ?>>⭐⭐⭐⭐⭐ (5 ستاره)</option>
                </select>
            </p>
        </div>
        <?php
    }
    
    /**
     * Render Teacher Info Meta Box
     */
    public static function render_teacher_info_meta_box($post) {
        wp_nonce_field('edu_teacher_info_nonce', 'edu_teacher_info_nonce_field');
        
        $experience = get_post_meta($post->ID, 'experience', true);
        $price = get_post_meta($post->ID, 'price', true);
        ?>
        <div class="edu-meta-box">
            <p>
                <label for="edu_experience"><strong>💼 سابقه تدریس:</strong></label><br>
                <input type="text" id="edu_experience" name="edu_experience" 
                       value="<?php echo esc_attr($experience); ?>" class="widefat" 
                       placeholder="مثال: 10 سال">
            </p>
            
            <p>
                <label for="edu_price"><strong>💰 شهریه/قیمت:</strong></label><br>
                <input type="text" id="edu_price" name="edu_price" 
                       value="<?php echo esc_attr($price); ?>" class="widefat" 
                       placeholder="مثال: 500,000 تومان">
            </p>
        </div>
        <?php
    }
    
    /**
     * Save all meta boxes
     */
    public static function save_meta_boxes($post_id, $post) {
        // Check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        // Check post type
        if (!in_array($post->post_type, array('academy', 'school', 'teacher'))) {
            return;
        }
        
        // Save Contact Info
        if (isset($_POST['edu_contact_info_nonce_field']) && 
            wp_verify_nonce($_POST['edu_contact_info_nonce_field'], 'edu_contact_info_nonce')) {
            
            $fields = array('phone', 'email', 'website', 'address');
            foreach ($fields as $field) {
                if (isset($_POST['edu_' . $field])) {
                    update_post_meta($post_id, $field, sanitize_text_field($_POST['edu_' . $field]));
                }
            }
        }
        
        // Save Social Media
        if (isset($_POST['edu_social_media_nonce_field']) && 
            wp_verify_nonce($_POST['edu_social_media_nonce_field'], 'edu_social_media_nonce')) {
            
            $fields = array('telegram', 'instagram', 'whatsapp');
            foreach ($fields as $field) {
                if (isset($_POST['edu_' . $field])) {
                    update_post_meta($post_id, $field, sanitize_text_field($_POST['edu_' . $field]));
                }
            }
        }
        
        // Save Display Settings
        if (isset($_POST['edu_display_settings_nonce_field']) && 
            wp_verify_nonce($_POST['edu_display_settings_nonce_field'], 'edu_display_settings_nonce')) {
            
            if (isset($_POST['edu_display_priority'])) {
                update_post_meta($post_id, 'display_priority', intval($_POST['edu_display_priority']));
            }
            if (isset($_POST['edu_rating'])) {
                update_post_meta($post_id, 'rating', floatval($_POST['edu_rating']));
            }
        }
        
        // Save Teacher Info
        if ($post->post_type === 'teacher' && 
            isset($_POST['edu_teacher_info_nonce_field']) && 
            wp_verify_nonce($_POST['edu_teacher_info_nonce_field'], 'edu_teacher_info_nonce')) {
            
            if (isset($_POST['edu_experience'])) {
                update_post_meta($post_id, 'experience', sanitize_text_field($_POST['edu_experience']));
            }
            if (isset($_POST['edu_price'])) {
                update_post_meta($post_id, 'price', sanitize_text_field($_POST['edu_price']));
            }
        }
    }
}
