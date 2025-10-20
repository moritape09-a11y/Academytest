<?php
/**
 * فایل دیباگ پلاگین دایرکتوری آموزشی
 * 
 * نحوه استفاده:
 * 1. این فایل را در پوشه wp-content/plugins/educational-directory/ قرار دهید
 * 2. در مرورگر به آدرس زیر بروید:
 *    http://your-site.com/wp-content/plugins/educational-directory/debug-plugin.php
 */

// بارگذاری وردپرس
require_once('../../../wp-load.php');

// بررسی دسترسی ادمین
if (!current_user_can('manage_options')) {
    die('شما دسترسی ندارید!');
}

?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دیباگ پلاگین دایرکتوری آموزشی</title>
    <style>
        body {
            font-family: Tahoma, Arial, sans-serif;
            margin: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2563eb;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 10px;
        }
        h2 {
            color: #10b981;
            margin-top: 30px;
        }
        .success {
            background: #d4edda;
            border: 2px solid #28a745;
            color: #155724;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .error {
            background: #f8d7da;
            border: 2px solid #dc3545;
            color: #721c24;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .warning {
            background: #fff3cd;
            border: 2px solid #ffc107;
            color: #856404;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .info {
            background: #d1ecf1;
            border: 2px solid #17a2b8;
            color: #0c5460;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
        }
        ul {
            list-style: none;
            padding: 0;
        }
        li {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .icon-success { color: #28a745; font-weight: bold; }
        .icon-error { color: #dc3545; font-weight: bold; }
        code {
            background: #f8f9fa;
            padding: 2px 6px;
            border-radius: 3px;
            color: #e83e8c;
        }
        .button {
            display: inline-block;
            background: #2563eb;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
        }
        .button:hover {
            background: #1e40af;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 دیباگ پلاگین دایرکتوری آموزشی</h1>
        
        <?php
        // 1. بررسی فایل اصلی
        echo '<h2>📄 بررسی فایل‌های پلاگین</h2>';
        
        $plugin_dir = WP_PLUGIN_DIR . '/educational-directory/';
        $required_files = array(
            'educational-directory.php' => 'فایل اصلی پلاگین',
            'includes/class-post-types.php' => 'کلاس Post Types',
            'includes/class-taxonomies.php' => 'کلاس Taxonomies',
            'includes/class-meta-boxes.php' => 'کلاس Meta Boxes',
            'includes/class-shortcodes.php' => 'کلاس Shortcodes',
            'includes/class-templates.php' => 'کلاس Templates',
            'includes/class-search-filter.php' => 'کلاس Search Filter'
        );
        
        $all_files_ok = true;
        foreach ($required_files as $file => $label) {
            if (file_exists($plugin_dir . $file)) {
                echo '<div class="success"><span class="icon-success">✅</span> ' . $label . ' موجود است: <code>' . $file . '</code></div>';
            } else {
                echo '<div class="error"><span class="icon-error">❌</span> ' . $label . ' موجود نیست: <code>' . $file . '</code></div>';
                $all_files_ok = false;
            }
        }
        
        // 2. بررسی فعال بودن پلاگین
        echo '<h2>⚡ بررسی فعال بودن پلاگین</h2>';
        
        if (function_exists('ED')) {
            echo '<div class="success"><span class="icon-success">✅</span> پلاگین فعال است!</div>';
        } else {
            echo '<div class="error"><span class="icon-error">❌</span> پلاگین فعال نیست! لطفاً از پنل مدیریت فعالش کنید.</div>';
        }
        
        // 3. بررسی کلاس‌ها
        echo '<h2>🎯 بررسی کلاس‌ها</h2>';
        
        $classes = array(
            'Educational_Directory' => 'کلاس اصلی پلاگین',
            'ED_Post_Types' => 'کلاس Post Types',
            'ED_Taxonomies' => 'کلاس Taxonomies',
            'ED_Meta_Boxes' => 'کلاس Meta Boxes',
            'ED_Shortcodes' => 'کلاس Shortcodes',
            'ED_Templates' => 'کلاس Templates',
            'ED_Search_Filter' => 'کلاس Search Filter'
        );
        
        $all_classes_ok = true;
        foreach ($classes as $class => $label) {
            if (class_exists($class)) {
                echo '<div class="success"><span class="icon-success">✅</span> ' . $label . ' بارگذاری شده: <code>' . $class . '</code></div>';
            } else {
                echo '<div class="error"><span class="icon-error">❌</span> ' . $label . ' بارگذاری نشده: <code>' . $class . '</code></div>';
                $all_classes_ok = false;
            }
        }
        
        // 4. بررسی Custom Post Types
        echo '<h2>📝 بررسی Custom Post Types</h2>';
        
        $post_types = get_post_types(array('_builtin' => false), 'objects');
        $required_post_types = array('academy', 'school', 'teacher');
        $post_types_ok = true;
        
        foreach ($required_post_types as $pt) {
            if (isset($post_types[$pt])) {
                $pt_obj = $post_types[$pt];
                echo '<div class="success">';
                echo '<span class="icon-success">✅</span> <strong>' . $pt_obj->labels->name . '</strong> ثبت شده است<br>';
                echo '&nbsp;&nbsp;&nbsp;&nbsp;Slug: <code>' . $pt . '</code><br>';
                echo '&nbsp;&nbsp;&nbsp;&nbsp;Public: ' . ($pt_obj->public ? 'بله' : 'خیر') . '<br>';
                echo '&nbsp;&nbsp;&nbsp;&nbsp;Show in Menu: ' . ($pt_obj->show_in_menu ? 'بله' : 'خیر');
                echo '</div>';
            } else {
                echo '<div class="error"><span class="icon-error">❌</span> <strong>' . $pt . '</strong> ثبت نشده است!</div>';
                $post_types_ok = false;
            }
        }
        
        // 5. بررسی Taxonomies
        echo '<h2>🏷️ بررسی Taxonomies</h2>';
        
        $required_taxonomies = array(
            'subject' => 'رشته‌ها',
            'city' => 'شهرها',
            'institution_type' => 'نوع موسسه',
            'specialty' => 'تخصص‌ها',
            'grade_level' => 'مقاطع تحصیلی'
        );
        
        $taxonomies_ok = true;
        foreach ($required_taxonomies as $tax => $label) {
            if (taxonomy_exists($tax)) {
                $tax_obj = get_taxonomy($tax);
                echo '<div class="success">';
                echo '<span class="icon-success">✅</span> <strong>' . $label . '</strong> ثبت شده است<br>';
                echo '&nbsp;&nbsp;&nbsp;&nbsp;Slug: <code>' . $tax . '</code><br>';
                echo '&nbsp;&nbsp;&nbsp;&nbsp;Post Types: ' . implode(', ', $tax_obj->object_type);
                echo '</div>';
            } else {
                echo '<div class="error"><span class="icon-error">❌</span> <strong>' . $label . '</strong> ثبت نشده است!</div>';
                $taxonomies_ok = false;
            }
        }
        
        // 6. بررسی Shortcodes
        echo '<h2>📄 بررسی Shortcodes</h2>';
        
        global $shortcode_tags;
        $required_shortcodes = array(
            'ed_academies' => 'نمایش آموزشگاه‌ها',
            'ed_schools' => 'نمایش مدارس',
            'ed_teachers' => 'نمایش معلمین',
            'ed_search' => 'فرم جستجو'
        );
        
        $shortcodes_ok = true;
        foreach ($required_shortcodes as $sc => $label) {
            if (array_key_exists($sc, $shortcode_tags)) {
                echo '<div class="success"><span class="icon-success">✅</span> <strong>[' . $sc . ']</strong> - ' . $label . '</div>';
            } else {
                echo '<div class="error"><span class="icon-error">❌</span> <strong>[' . $sc . ']</strong> - ' . $label . ' ثبت نشده!</div>';
                $shortcodes_ok = false;
            }
        }
        
        // 7. اطلاعات سیستم
        echo '<h2>⚙️ اطلاعات سیستم</h2>';
        
        global $wp_version;
        echo '<div class="info">';
        echo '<ul>';
        echo '<li><strong>نسخه وردپرس:</strong> ' . $wp_version . ' ' . (version_compare($wp_version, '5.0', '>=') ? '<span class="icon-success">✅</span>' : '<span class="icon-error">❌ نیاز به 5.0+</span>') . '</li>';
        echo '<li><strong>نسخه PHP:</strong> ' . PHP_VERSION . ' ' . (version_compare(PHP_VERSION, '7.2.0', '>=') ? '<span class="icon-success">✅</span>' : '<span class="icon-error">❌ نیاز به 7.2+</span>') . '</li>';
        echo '<li><strong>مسیر پلاگین:</strong> <code>' . $plugin_dir . '</code></li>';
        echo '<li><strong>URL پلاگین:</strong> <code>' . plugins_url('', $plugin_dir . 'educational-directory.php') . '</code></li>';
        echo '</ul>';
        echo '</div>';
        
        // 8. خلاصه نتیجه
        echo '<h2>📊 خلاصه نتیجه</h2>';
        
        if ($all_files_ok && $all_classes_ok && $post_types_ok && $taxonomies_ok && $shortcodes_ok) {
            echo '<div class="success">';
            echo '<h3 style="color: #28a745;">🎉 همه چیز عالی است!</h3>';
            echo '<p>پلاگین به درستی نصب و راه‌اندازی شده است. می‌توانید از آن استفاده کنید.</p>';
            echo '<a href="' . admin_url('edit.php?post_type=academy') . '" class="button">افزودن آموزشگاه</a>';
            echo '<a href="' . admin_url('edit.php?post_type=school') . '" class="button">افزودن مدرسه</a>';
            echo '<a href="' . admin_url('edit.php?post_type=teacher') . '" class="button">افزودن معلم</a>';
            echo '</div>';
        } else {
            echo '<div class="error">';
            echo '<h3 style="color: #dc3545;">⚠️ مشکلاتی وجود دارد!</h3>';
            echo '<p>موارد زیر را بررسی کنید:</p>';
            echo '<ul>';
            if (!$all_files_ok) echo '<li>برخی فایل‌ها موجود نیستند</li>';
            if (!$all_classes_ok) echo '<li>برخی کلاس‌ها بارگذاری نشده‌اند</li>';
            if (!$post_types_ok) echo '<li>Custom Post Types ثبت نشده‌اند</li>';
            if (!$taxonomies_ok) echo '<li>Taxonomies ثبت نشده‌اند</li>';
            if (!$shortcodes_ok) echo '<li>Shortcodes ثبت نشده‌اند</li>';
            echo '</ul>';
            echo '<p><strong>راه‌حل:</strong></p>';
            echo '<ol>';
            echo '<li>پلاگین را غیرفعال و دوباره فعال کنید</li>';
            echo '<li>به تنظیمات > پیوندهای یکتا بروید و روی "ذخیره تغییرات" کلیک کنید</li>';
            echo '<li>کش مرورگر و سرور را پاک کنید</li>';
            echo '</ol>';
            echo '<a href="' . admin_url('plugins.php') . '" class="button">مدیریت افزونه‌ها</a>';
            echo '</div>';
        }
        
        // 9. دکمه‌های اضافی
        echo '<div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #eee;">';
        echo '<a href="' . admin_url() . '" class="button">بازگشت به پیشخوان</a>';
        echo '<a href="' . admin_url('admin.php?page=site-health') . '" class="button">سلامت سایت</a>';
        echo '<a href="' . admin_url('options-permalink.php') . '" class="button">تنظیمات Permalink</a>';
        echo '</div>';
        ?>
        
    </div>
</body>
</html>
