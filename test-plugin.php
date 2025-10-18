<?php
/**
 * فایل تست پلاگین
 * این کد را در functions.php تم خود قرار دهید تا پلاگین را تست کنید
 */

// تست 1: بررسی فعال بودن پلاگین
add_action('admin_notices', 'ed_test_plugin_active');
function ed_test_plugin_active() {
    if (!function_exists('ED')) {
        echo '<div class="notice notice-error is-dismissible">';
        echo '<p><strong>❌ خطا:</strong> پلاگین دایرکتوری آموزشی فعال نیست یا به درستی نصب نشده!</p>';
        echo '<p>لطفاً از بخش افزونه‌ها پلاگین را فعال کنید.</p>';
        echo '</div>';
        return;
    }
    
    echo '<div class="notice notice-success is-dismissible">';
    echo '<p><strong>✅ موفق:</strong> پلاگین دایرکتوری آموزشی فعال است!</p>';
    echo '</div>';
}

// تست 2: بررسی Custom Post Types
add_action('admin_notices', 'ed_test_post_types');
function ed_test_post_types() {
    if (!function_exists('ED')) {
        return;
    }
    
    $post_types = get_post_types(array('_builtin' => false), 'names');
    
    echo '<div class="notice notice-info">';
    echo '<h3>🔍 بررسی Custom Post Types:</h3>';
    echo '<ul style="list-style: disc; margin-left: 20px;">';
    
    // بررسی آموزشگاه‌ها
    if (in_array('academy', $post_types)) {
        echo '<li>✅ <strong>آموزشگاه‌ها (Academy)</strong> ثبت شده است</li>';
    } else {
        echo '<li>❌ <strong>آموزشگاه‌ها (Academy)</strong> ثبت نشده است!</li>';
    }
    
    // بررسی مدارس
    if (in_array('school', $post_types)) {
        echo '<li>✅ <strong>مدارس (School)</strong> ثبت شده است</li>';
    } else {
        echo '<li>❌ <strong>مدارس (School)</strong> ثبت نشده است!</li>';
    }
    
    // بررسی معلمین
    if (in_array('teacher', $post_types)) {
        echo '<li>✅ <strong>معلمین (Teacher)</strong> ثبت شده است</li>';
    } else {
        echo '<li>❌ <strong>معلمین (Teacher)</strong> ثبت نشده است!</li>';
    }
    
    echo '</ul>';
    echo '</div>';
}

// تست 3: بررسی Taxonomies
add_action('admin_notices', 'ed_test_taxonomies');
function ed_test_taxonomies() {
    if (!function_exists('ED')) {
        return;
    }
    
    echo '<div class="notice notice-info">';
    echo '<h3>🏷️ بررسی Taxonomies:</h3>';
    echo '<ul style="list-style: disc; margin-left: 20px;">';
    
    $taxonomies = array(
        'subject' => 'رشته‌ها',
        'city' => 'شهرها',
        'institution_type' => 'نوع موسسه',
        'specialty' => 'تخصص‌ها',
        'grade_level' => 'مقاطع تحصیلی'
    );
    
    foreach ($taxonomies as $tax => $label) {
        if (taxonomy_exists($tax)) {
            echo '<li>✅ <strong>' . $label . ' (' . $tax . ')</strong> ثبت شده است</li>';
        } else {
            echo '<li>❌ <strong>' . $label . ' (' . $tax . ')</strong> ثبت نشده است!</li>';
        }
    }
    
    echo '</ul>';
    echo '</div>';
}

// تست 4: بررسی فایل‌ها
add_action('admin_notices', 'ed_test_files');
function ed_test_files() {
    if (!function_exists('ED')) {
        return;
    }
    
    $plugin_dir = WP_PLUGIN_DIR . '/educational-directory/';
    
    echo '<div class="notice notice-info">';
    echo '<h3>📁 بررسی فایل‌ها:</h3>';
    echo '<ul style="list-style: disc; margin-left: 20px;">';
    
    $required_files = array(
        'educational-directory.php' => 'فایل اصلی',
        'includes/class-post-types.php' => 'کلاس Post Types',
        'includes/class-taxonomies.php' => 'کلاس Taxonomies',
        'includes/class-meta-boxes.php' => 'کلاس Meta Boxes',
        'includes/class-shortcodes.php' => 'کلاس Shortcodes',
        'assets/css/frontend.css' => 'فایل CSS',
        'assets/js/frontend.js' => 'فایل JavaScript'
    );
    
    foreach ($required_files as $file => $label) {
        if (file_exists($plugin_dir . $file)) {
            echo '<li>✅ <strong>' . $label . '</strong> موجود است</li>';
        } else {
            echo '<li>❌ <strong>' . $label . '</strong> موجود نیست! (' . $file . ')</li>';
        }
    }
    
    echo '</ul>';
    echo '</div>';
}

// تست 5: بررسی Shortcodes
add_action('admin_notices', 'ed_test_shortcodes');
function ed_test_shortcodes() {
    if (!function_exists('ED')) {
        return;
    }
    
    global $shortcode_tags;
    
    echo '<div class="notice notice-info">';
    echo '<h3>📝 بررسی Shortcodes:</h3>';
    echo '<ul style="list-style: disc; margin-left: 20px;">';
    
    $required_shortcodes = array(
        'ed_academies' => 'نمایش آموزشگاه‌ها',
        'ed_schools' => 'نمایش مدارس',
        'ed_teachers' => 'نمایش معلمین',
        'ed_search' => 'فرم جستجو'
    );
    
    foreach ($required_shortcodes as $shortcode => $label) {
        if (array_key_exists($shortcode, $shortcode_tags)) {
            echo '<li>✅ <strong>[' . $shortcode . ']</strong> - ' . $label . '</li>';
        } else {
            echo '<li>❌ <strong>[' . $shortcode . ']</strong> - ' . $label . ' ثبت نشده!</li>';
        }
    }
    
    echo '</ul>';
    echo '</div>';
}

// تست 6: نمایش اطلاعات سیستم
add_action('admin_notices', 'ed_test_system_info');
function ed_test_system_info() {
    global $wp_version;
    
    echo '<div class="notice notice-info">';
    echo '<h3>⚙️ اطلاعات سیستم:</h3>';
    echo '<ul style="list-style: disc; margin-left: 20px;">';
    echo '<li><strong>نسخه وردپرس:</strong> ' . $wp_version . ' ' . ($wp_version >= '5.0' ? '✅' : '❌ (نیاز به 5.0+)') . '</li>';
    echo '<li><strong>نسخه PHP:</strong> ' . PHP_VERSION . ' ' . (version_compare(PHP_VERSION, '7.2.0', '>=') ? '✅' : '❌ (نیاز به 7.2+)') . '</li>';
    echo '<li><strong>پوشه پلاگین:</strong> ' . WP_PLUGIN_DIR . '/educational-directory/</li>';
    echo '</ul>';
    echo '</div>';
}

// دستور حذف این تست‌ها
add_action('admin_notices', 'ed_test_remove_notice');
function ed_test_remove_notice() {
    echo '<div class="notice notice-warning">';
    echo '<p><strong>⚠️ توجه:</strong> این کدهای تست را بعد از بررسی از functions.php حذف کنید!</p>';
    echo '</div>';
}
