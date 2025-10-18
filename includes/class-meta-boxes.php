<?php
/**
 * مدیریت Meta Boxes و فیلدهای سفارشی
 */

if (!defined('ABSPATH')) {
    exit;
}

class ED_Meta_Boxes {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_meta_boxes'), 10, 2);
    }
    
    /**
     * افزودن Meta Boxes
     */
    public function add_meta_boxes() {
        // اطلاعات تماس برای آموزشگاه و مدرسه
        add_meta_box(
            'ed_contact_info',
            'اطلاعات تماس',
            array($this, 'render_contact_info'),
            array('academy', 'school'),
            'normal',
            'high'
        );
        
        // اطلاعات مکانی
        add_meta_box(
            'ed_location_info',
            'اطلاعات مکانی',
            array($this, 'render_location_info'),
            array('academy', 'school'),
            'normal',
            'high'
        );
        
        // اطلاعات معلم
        add_meta_box(
            'ed_teacher_info',
            'اطلاعات معلم',
            array($this, 'render_teacher_info'),
            'teacher',
            'normal',
            'high'
        );
        
        // رابطه معلم با آموزشگاه/مدرسه
        add_meta_box(
            'ed_teacher_institution',
            'آموزشگاه / مدرسه',
            array($this, 'render_teacher_institution'),
            'teacher',
            'side',
            'default'
        );
        
        // امکانات
        add_meta_box(
            'ed_facilities',
            'امکانات و ویژگی‌ها',
            array($this, 'render_facilities'),
            array('academy', 'school'),
            'normal',
            'default'
        );
        
        // رتبه‌بندی
        add_meta_box(
            'ed_rating',
            'رتبه‌بندی',
            array($this, 'render_rating'),
            array('academy', 'school', 'teacher'),
            'side',
            'default'
        );
    }
    
    /**
     * نمایش فیلدهای اطلاعات تماس
     */
    public function render_contact_info($post) {
        wp_nonce_field('ed_meta_box', 'ed_meta_box_nonce');
        
        $phone = get_post_meta($post->ID, '_ed_phone', true);
        $email = get_post_meta($post->ID, '_ed_email', true);
        $website = get_post_meta($post->ID, '_ed_website', true);
        $instagram = get_post_meta($post->ID, '_ed_instagram', true);
        $telegram = get_post_meta($post->ID, '_ed_telegram', true);
        ?>
        <table class="form-table">
            <tr>
                <th><label for="ed_phone">شماره تماس</label></th>
                <td><input type="text" id="ed_phone" name="ed_phone" value="<?php echo esc_attr($phone); ?>" class="regular-text" placeholder="021-12345678"></td>
            </tr>
            <tr>
                <th><label for="ed_email">ایمیل</label></th>
                <td><input type="email" id="ed_email" name="ed_email" value="<?php echo esc_attr($email); ?>" class="regular-text" placeholder="info@example.com"></td>
            </tr>
            <tr>
                <th><label for="ed_website">وبسایت</label></th>
                <td><input type="url" id="ed_website" name="ed_website" value="<?php echo esc_attr($website); ?>" class="regular-text" placeholder="https://example.com"></td>
            </tr>
            <tr>
                <th><label for="ed_instagram">اینستاگرام</label></th>
                <td><input type="text" id="ed_instagram" name="ed_instagram" value="<?php echo esc_attr($instagram); ?>" class="regular-text" placeholder="@username"></td>
            </tr>
            <tr>
                <th><label for="ed_telegram">تلگرام</label></th>
                <td><input type="text" id="ed_telegram" name="ed_telegram" value="<?php echo esc_attr($telegram); ?>" class="regular-text" placeholder="@username"></td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * نمایش فیلدهای اطلاعات مکانی
     */
    public function render_location_info($post) {
        $address = get_post_meta($post->ID, '_ed_address', true);
        $postal_code = get_post_meta($post->ID, '_ed_postal_code', true);
        $latitude = get_post_meta($post->ID, '_ed_latitude', true);
        $longitude = get_post_meta($post->ID, '_ed_longitude', true);
        ?>
        <table class="form-table">
            <tr>
                <th><label for="ed_address">آدرس کامل</label></th>
                <td><textarea id="ed_address" name="ed_address" rows="3" class="large-text"><?php echo esc_textarea($address); ?></textarea></td>
            </tr>
            <tr>
                <th><label for="ed_postal_code">کد پستی</label></th>
                <td><input type="text" id="ed_postal_code" name="ed_postal_code" value="<?php echo esc_attr($postal_code); ?>" class="regular-text" placeholder="1234567890"></td>
            </tr>
            <tr>
                <th><label for="ed_latitude">عرض جغرافیایی (Latitude)</label></th>
                <td><input type="text" id="ed_latitude" name="ed_latitude" value="<?php echo esc_attr($latitude); ?>" class="regular-text" placeholder="35.6892"></td>
            </tr>
            <tr>
                <th><label for="ed_longitude">طول جغرافیایی (Longitude)</label></th>
                <td><input type="text" id="ed_longitude" name="ed_longitude" value="<?php echo esc_attr($longitude); ?>" class="regular-text" placeholder="51.3890"></td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * نمایش فیلدهای اطلاعات معلم
     */
    public function render_teacher_info($post) {
        wp_nonce_field('ed_meta_box', 'ed_meta_box_nonce');
        
        $experience = get_post_meta($post->ID, '_ed_experience', true);
        $education = get_post_meta($post->ID, '_ed_education', true);
        $phone = get_post_meta($post->ID, '_ed_phone', true);
        $email = get_post_meta($post->ID, '_ed_email', true);
        $certificate = get_post_meta($post->ID, '_ed_certificate', true);
        $price = get_post_meta($post->ID, '_ed_price', true);
        ?>
        <table class="form-table">
            <tr>
                <th><label for="ed_experience">سابقه تدریس (سال)</label></th>
                <td><input type="number" id="ed_experience" name="ed_experience" value="<?php echo esc_attr($experience); ?>" class="small-text" min="0"> سال</td>
            </tr>
            <tr>
                <th><label for="ed_education">مدرک تحصیلی</label></th>
                <td>
                    <select id="ed_education" name="ed_education" class="regular-text">
                        <option value="">انتخاب کنید</option>
                        <option value="diploma" <?php selected($education, 'diploma'); ?>>دیپلم</option>
                        <option value="associate" <?php selected($education, 'associate'); ?>>کاردانی</option>
                        <option value="bachelor" <?php selected($education, 'bachelor'); ?>>کارشناسی</option>
                        <option value="master" <?php selected($education, 'master'); ?>>کارشناسی ارشد</option>
                        <option value="phd" <?php selected($education, 'phd'); ?>>دکترا</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="ed_certificate">مدارک و گواهینامه‌ها</label></th>
                <td><textarea id="ed_certificate" name="ed_certificate" rows="3" class="large-text"><?php echo esc_textarea($certificate); ?></textarea></td>
            </tr>
            <tr>
                <th><label for="ed_phone">شماره تماس</label></th>
                <td><input type="text" id="ed_phone" name="ed_phone" value="<?php echo esc_attr($phone); ?>" class="regular-text" placeholder="0912-1234567"></td>
            </tr>
            <tr>
                <th><label for="ed_email">ایمیل</label></th>
                <td><input type="email" id="ed_email" name="ed_email" value="<?php echo esc_attr($email); ?>" class="regular-text" placeholder="teacher@example.com"></td>
            </tr>
            <tr>
                <th><label for="ed_price">هزینه تدریس (تومان/ساعت)</label></th>
                <td><input type="number" id="ed_price" name="ed_price" value="<?php echo esc_attr($price); ?>" class="regular-text" min="0" placeholder="500000"> تومان</td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * نمایش فیلد رابطه معلم با آموزشگاه/مدرسه
     */
    public function render_teacher_institution($post) {
        $related_institutions = get_post_meta($post->ID, '_ed_related_institutions', true);
        if (!is_array($related_institutions)) {
            $related_institutions = array();
        }
        
        $academies = get_posts(array('post_type' => 'academy', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC'));
        $schools = get_posts(array('post_type' => 'school', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC'));
        ?>
        <p><strong>آموزشگاه‌ها:</strong></p>
        <?php if (!empty($academies)): ?>
            <?php foreach ($academies as $academy): ?>
                <label style="display: block; margin-bottom: 5px;">
                    <input type="checkbox" name="ed_related_institutions[]" value="academy_<?php echo $academy->ID; ?>" <?php checked(in_array('academy_' . $academy->ID, $related_institutions)); ?>>
                    <?php echo esc_html($academy->post_title); ?>
                </label>
            <?php endforeach; ?>
        <?php else: ?>
            <p><em>آموزشگاهی یافت نشد</em></p>
        <?php endif; ?>
        
        <p style="margin-top: 15px;"><strong>مدارس:</strong></p>
        <?php if (!empty($schools)): ?>
            <?php foreach ($schools as $school): ?>
                <label style="display: block; margin-bottom: 5px;">
                    <input type="checkbox" name="ed_related_institutions[]" value="school_<?php echo $school->ID; ?>" <?php checked(in_array('school_' . $school->ID, $related_institutions)); ?>>
                    <?php echo esc_html($school->post_title); ?>
                </label>
            <?php endforeach; ?>
        <?php else: ?>
            <p><em>مدرسه‌ای یافت نشد</em></p>
        <?php endif; ?>
        <?php
    }
    
    /**
     * نمایش فیلدهای امکانات
     */
    public function render_facilities($post) {
        $facilities = get_post_meta($post->ID, '_ed_facilities', true);
        if (!is_array($facilities)) {
            $facilities = array();
        }
        
        $all_facilities = array(
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
        ?>
        <div style="column-count: 2;">
            <?php foreach ($all_facilities as $key => $label): ?>
                <label style="display: block; margin-bottom: 8px;">
                    <input type="checkbox" name="ed_facilities[]" value="<?php echo $key; ?>" <?php checked(in_array($key, $facilities)); ?>>
                    <?php echo esc_html($label); ?>
                </label>
            <?php endforeach; ?>
        </div>
        <?php
    }
    
    /**
     * نمایش فیلد رتبه‌بندی
     */
    public function render_rating($post) {
        $rating = get_post_meta($post->ID, '_ed_rating', true);
        if (empty($rating)) {
            $rating = 5;
        }
        ?>
        <p>
            <label for="ed_rating">امتیاز (از 5):</label><br>
            <select id="ed_rating" name="ed_rating">
                <?php for ($i = 1; $i <= 5; $i += 0.5): ?>
                    <option value="<?php echo $i; ?>" <?php selected($rating, $i); ?>><?php echo $i; ?> ستاره</option>
                <?php endfor; ?>
            </select>
        </p>
        <?php
    }
    
    /**
     * ذخیره Meta Boxes
     */
    public function save_meta_boxes($post_id, $post) {
        // بررسی nonce
        if (!isset($_POST['ed_meta_box_nonce']) || !wp_verify_nonce($_POST['ed_meta_box_nonce'], 'ed_meta_box')) {
            return $post_id;
        }
        
        // بررسی autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }
        
        // بررسی دسترسی کاربر
        if (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }
        
        // ذخیره فیلدهای مختلف
        $fields = array(
            'ed_phone', 'ed_email', 'ed_website', 'ed_instagram', 'ed_telegram',
            'ed_address', 'ed_postal_code', 'ed_latitude', 'ed_longitude',
            'ed_experience', 'ed_education', 'ed_certificate', 'ed_price', 'ed_rating'
        );
        
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
            }
        }
        
        // ذخیره امکانات
        if (isset($_POST['ed_facilities'])) {
            update_post_meta($post_id, '_ed_facilities', array_map('sanitize_text_field', $_POST['ed_facilities']));
        } else {
            delete_post_meta($post_id, '_ed_facilities');
        }
        
        // ذخیره رابطه معلم با آموزشگاه/مدرسه
        if (isset($_POST['ed_related_institutions'])) {
            update_post_meta($post_id, '_ed_related_institutions', array_map('sanitize_text_field', $_POST['ed_related_institutions']));
        } else {
            delete_post_meta($post_id, '_ed_related_institutions');
        }
    }
}
