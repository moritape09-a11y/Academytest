<?php
/**
 * Meta Boxes
 */

defined('ABSPATH') || exit;

// افزودن Meta Boxes
add_action('add_meta_boxes', 'edu_add_meta_boxes');

function edu_add_meta_boxes() {
    // برای آموزشگاه و مدرسه
    add_meta_box(
        'edu_contact',
        'اطلاعات تماس',
        'edu_contact_metabox',
        array('academy', 'school'),
        'normal'
    );
    
    // برای معلم
    add_meta_box(
        'edu_teacher_info',
        'اطلاعات معلم',
        'edu_teacher_metabox',
        'teacher',
        'normal'
    );
}

// متاباکس اطلاعات تماس
function edu_contact_metabox($post) {
    wp_nonce_field('edu_save_meta', 'edu_meta_nonce');
    
    $phone = get_post_meta($post->ID, 'phone', true);
    $email = get_post_meta($post->ID, 'email', true);
    $address = get_post_meta($post->ID, 'address', true);
    $website = get_post_meta($post->ID, 'website', true);
    ?>
    <table class="form-table">
        <tr>
            <th><label>تلفن</label></th>
            <td><input type="text" name="phone" value="<?php echo esc_attr($phone); ?>" class="regular-text"></td>
        </tr>
        <tr>
            <th><label>ایمیل</label></th>
            <td><input type="email" name="email" value="<?php echo esc_attr($email); ?>" class="regular-text"></td>
        </tr>
        <tr>
            <th><label>آدرس</label></th>
            <td><textarea name="address" class="large-text" rows="3"><?php echo esc_textarea($address); ?></textarea></td>
        </tr>
        <tr>
            <th><label>وبسایت</label></th>
            <td><input type="url" name="website" value="<?php echo esc_attr($website); ?>" class="regular-text"></td>
        </tr>
    </table>
    <?php
}

// متاباکس اطلاعات معلم
function edu_teacher_metabox($post) {
    wp_nonce_field('edu_save_meta', 'edu_meta_nonce');
    
    $phone = get_post_meta($post->ID, 'phone', true);
    $email = get_post_meta($post->ID, 'email', true);
    $experience = get_post_meta($post->ID, 'experience', true);
    $price = get_post_meta($post->ID, 'price', true);
    ?>
    <table class="form-table">
        <tr>
            <th><label>تلفن</label></th>
            <td><input type="text" name="phone" value="<?php echo esc_attr($phone); ?>" class="regular-text"></td>
        </tr>
        <tr>
            <th><label>ایمیل</label></th>
            <td><input type="email" name="email" value="<?php echo esc_attr($email); ?>" class="regular-text"></td>
        </tr>
        <tr>
            <th><label>سابقه (سال)</label></th>
            <td><input type="number" name="experience" value="<?php echo esc_attr($experience); ?>" class="small-text"> سال</td>
        </tr>
        <tr>
            <th><label>هزینه (تومان/ساعت)</label></th>
            <td><input type="number" name="price" value="<?php echo esc_attr($price); ?>" class="regular-text"> تومان</td>
        </tr>
    </table>
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
    
    $fields = array('phone', 'email', 'address', 'website', 'experience', 'price');
    
    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }
}
