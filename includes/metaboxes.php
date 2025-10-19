<?php
/**
 * Meta Boxes
 */

defined('ABSPATH') || exit;

// افزودن Meta Boxes
add_action('add_meta_boxes', 'edu_add_meta_boxes');

function edu_add_meta_boxes() {
    $post_types = array('academy', 'school', 'teacher');
    
    foreach ($post_types as $type) {
        add_meta_box(
            'edu_contact',
            'اطلاعات تماس و شبکه‌های اجتماعی',
            'edu_contact_metabox',
            $type,
            'normal',
            'high'
        );
        
        add_meta_box(
            'edu_display_settings',
            'تنظیمات نمایش (اولویت و امتیاز)',
            'edu_display_settings_metabox',
            $type,
            'side',
            'default'
        );
    }
    
    add_meta_box(
        'edu_teacher_info',
        'اطلاعات تدریس',
        'edu_teacher_metabox',
        'teacher',
        'normal',
        'high'
    );
}

// متاباکس اطلاعات تماس
function edu_contact_metabox($post) {
    wp_nonce_field('edu_save_meta', 'edu_meta_nonce');
    
    $phone = get_post_meta($post->ID, 'phone', true);
    $email = get_post_meta($post->ID, 'email', true);
    $address = get_post_meta($post->ID, 'address', true);
    $website = get_post_meta($post->ID, 'website', true);
    $telegram = get_post_meta($post->ID, 'telegram', true);
    $instagram = get_post_meta($post->ID, 'instagram', true);
    $whatsapp = get_post_meta($post->ID, 'whatsapp', true);
    ?>
    <table class="form-table">
        <tr>
            <th><label>📞 تلفن</label></th>
            <td><input type="text" name="phone" value="<?php echo esc_attr($phone); ?>" class="regular-text" placeholder="021-12345678"></td>
        </tr>
        <tr>
            <th><label>📧 ایمیل</label></th>
            <td><input type="email" name="email" value="<?php echo esc_attr($email); ?>" class="regular-text" placeholder="info@example.com"></td>
        </tr>
        <tr>
            <th><label>🌐 وبسایت</label></th>
            <td><input type="url" name="website" value="<?php echo esc_attr($website); ?>" class="regular-text" placeholder="https://example.com"></td>
        </tr>
        <tr>
            <th><label>📍 آدرس</label></th>
            <td><textarea name="address" class="large-text" rows="3"><?php echo esc_textarea($address); ?></textarea></td>
        </tr>
        <tr>
            <td colspan="2" style="padding: 1rem 0; border-top: 2px solid #ddd;">
                <h3 style="margin: 0 0 1rem 0; color: #2563eb;">شبکه‌های اجتماعی</h3>
            </td>
        </tr>
        <tr>
            <th><label>✈️ تلگرام</label></th>
            <td>
                <input type="text" name="telegram" value="<?php echo esc_attr($telegram); ?>" class="regular-text" placeholder="@username یا لینک">
                <p class="description">مثال: @myacademy یا https://t.me/myacademy</p>
            </td>
        </tr>
        <tr>
            <th><label>📷 اینستاگرام</label></th>
            <td>
                <input type="text" name="instagram" value="<?php echo esc_attr($instagram); ?>" class="regular-text" placeholder="@username یا لینک">
                <p class="description">مثال: @myacademy یا https://instagram.com/myacademy</p>
            </td>
        </tr>
        <tr>
            <th><label>💬 واتساپ</label></th>
            <td>
                <input type="text" name="whatsapp" value="<?php echo esc_attr($whatsapp); ?>" class="regular-text" placeholder="989123456789">
                <p class="description">شماره موبایل با کد کشور (بدون + و صفر اول). مثال: 989123456789</p>
            </td>
        </tr>
    </table>
    <?php
}

// متاباکس اطلاعات معلم
function edu_teacher_metabox($post) {
    $experience = get_post_meta($post->ID, 'experience', true);
    $price = get_post_meta($post->ID, 'price', true);
    ?>
    <table class="form-table">
        <tr>
            <th><label>📚 سابقه تدریس (سال)</label></th>
            <td><input type="number" name="experience" value="<?php echo esc_attr($experience); ?>" class="small-text" min="0"> سال</td>
        </tr>
        <tr>
            <th><label>💰 هزینه (تومان/ساعت)</label></th>
            <td><input type="number" name="price" value="<?php echo esc_attr($price); ?>" class="regular-text" min="0" step="10000"> تومان</td>
        </tr>
    </table>
    <?php
}

// متاباکس تنظیمات نمایش
function edu_display_settings_metabox($post) {
    $priority = get_post_meta($post->ID, 'display_priority', true);
    $rating = get_post_meta($post->ID, 'rating', true);
    
    if (empty($priority)) $priority = 0;
    if (empty($rating)) $rating = 5;
    ?>
    <div style="padding: 10px;">
        <p>
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">🔢 اولویت نمایش</label>
            <input type="number" name="display_priority" value="<?php echo esc_attr($priority); ?>" style="width: 100%;" min="0" step="1">
            <small style="color: #666; display: block; margin-top: 0.5rem;">
                عدد کوچکتر = نمایش در بالای لیست<br>
                مثال: 1 بالاتر از 10 نمایش داده می‌شود
            </small>
        </p>
        
        <p style="border-top: 1px solid #ddd; padding-top: 1rem; margin-top: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">⭐ امتیاز</label>
            <select name="rating" style="width: 100%; padding: 0.5rem;">
                <option value="5" <?php selected($rating, 5); ?>>⭐⭐⭐⭐⭐ عالی (5)</option>
                <option value="4.5" <?php selected($rating, 4.5); ?>>⭐⭐⭐⭐✨ خیلی خوب (4.5)</option>
                <option value="4" <?php selected($rating, 4); ?>>⭐⭐⭐⭐ خوب (4)</option>
                <option value="3.5" <?php selected($rating, 3.5); ?>>⭐⭐⭐✨ متوسط+ (3.5)</option>
                <option value="3" <?php selected($rating, 3); ?>>⭐⭐⭐ متوسط (3)</option>
                <option value="2.5" <?php selected($rating, 2.5); ?>>⭐⭐✨ ضعیف+ (2.5)</option>
                <option value="2" <?php selected($rating, 2); ?>>⭐⭐ ضعیف (2)</option>
                <option value="1.5" <?php selected($rating, 1.5); ?>>⭐✨ خیلی ضعیف+ (1.5)</option>
                <option value="1" <?php selected($rating, 1); ?>>⭐ خیلی ضعیف (1)</option>
            </select>
            <small style="color: #666; display: block; margin-top: 0.5rem;">
                امتیاز با ستاره نمایش داده می‌شود
            </small>
        </p>
    </div>
    <?php
}

// ذخیره Meta
add_action('save_post', 'edu_save_meta');

function edu_save_meta($post_id) {
    if (!isset($_POST['edu_meta_nonce']) || !wp_verify_nonce($_POST['edu_meta_nonce'], 'edu_save_meta')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    $fields = array(
        'phone', 'email', 'address', 'website', 
        'telegram', 'instagram', 'whatsapp',
        'experience', 'price', 'display_priority', 'rating'
    );
    
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }
}
