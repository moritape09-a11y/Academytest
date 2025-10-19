<?php
/**
 * تست شورت کدها
 * این فایل را در ریشه سایت قرار دهید و در مرورگر باز کنید
 * مثال: http://your-site.com/test-shortcodes.php
 */

// بارگذاری وردپرس
require_once('wp-load.php');

// بررسی دسترسی ادمین
if (!current_user_can('manage_options')) {
    die('شما دسترسی ندارید!');
}

?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تست شورت کدها</title>
    <style>
        body { font-family: Tahoma; padding: 20px; background: #f5f5f5; }
        .box { background: white; padding: 20px; margin: 10px 0; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .success { background: #d4edda; border: 2px solid #28a745; color: #155724; }
        .error { background: #f8d7da; border: 2px solid #dc3545; color: #721c24; }
        .code { background: #f8f9fa; padding: 10px; border-radius: 5px; font-family: monospace; margin: 10px 0; }
        h1 { color: #2563eb; }
        h2 { color: #1a202c; margin-top: 30px; }
    </style>
</head>
<body>

<h1>🔍 تست شورت کدهای پلاگین</h1>

<?php
// 1. بررسی فایل shortcodes.php
echo '<div class="box">';
echo '<h2>1️⃣ بررسی فایل shortcodes.php</h2>';
$shortcode_file = WP_PLUGIN_DIR . '/educational-directory/includes/shortcodes.php';
if (file_exists($shortcode_file)) {
    echo '<div class="success">✅ فایل موجود است: ' . $shortcode_file . '</div>';
} else {
    echo '<div class="error">❌ فایل موجود نیست!</div>';
}
echo '</div>';

// 2. بررسی توابع
echo '<div class="box">';
echo '<h2>2️⃣ بررسی توابع</h2>';
$functions = array('edu_academies_shortcode', 'edu_schools_shortcode', 'edu_teachers_shortcode', 'edu_render_posts', 'edu_render_card');
foreach ($functions as $func) {
    if (function_exists($func)) {
        echo '<div class="success">✅ تابع وجود دارد: ' . $func . '</div>';
    } else {
        echo '<div class="error">❌ تابع وجود ندارد: ' . $func . '</div>';
    }
}
echo '</div>';

// 3. بررسی ثبت شورت کدها
echo '<div class="box">';
echo '<h2>3️⃣ بررسی ثبت شورت کدها</h2>';
global $shortcode_tags;
$our_shortcodes = array('academies', 'schools', 'teachers');
foreach ($our_shortcodes as $sc) {
    if (isset($shortcode_tags[$sc])) {
        echo '<div class="success">✅ شورت کد ثبت شده: [' . $sc . '] -> ' . $shortcode_tags[$sc] . '</div>';
    } else {
        echo '<div class="error">❌ شورت کد ثبت نشده: [' . $sc . ']</div>';
    }
}
echo '</div>';

// 4. تست اجرای شورت کد
echo '<div class="box">';
echo '<h2>4️⃣ تست اجرای شورت کد</h2>';

// تست academies
echo '<h3>تست [academies limit="3"]:</h3>';
$output = do_shortcode('[academies limit="3"]');
if (!empty($output)) {
    echo '<div class="success">✅ شورت کد اجرا شد</div>';
    echo '<div class="code">' . esc_html(substr($output, 0, 200)) . '...</div>';
} else {
    echo '<div class="error">❌ شورت کد خروجی ندارد!</div>';
}

echo '</div>';

// 5. بررسی پست‌ها
echo '<div class="box">';
echo '<h2>5️⃣ بررسی پست‌ها</h2>';

$post_types = array('academy', 'school', 'teacher');
foreach ($post_types as $pt) {
    $count = wp_count_posts($pt);
    if ($count) {
        echo '<div class="success">✅ ' . $pt . ': ' . $count->publish . ' پست منتشر شده</div>';
    } else {
        echo '<div class="error">❌ ' . $pt . ': هیچ پستی وجود ندارد</div>';
    }
}

echo '</div>';

// 6. تست نهایی با نمایش واقعی
echo '<div class="box">';
echo '<h2>6️⃣ نمایش واقعی شورت کد</h2>';
echo '<p>خروجی [academies limit="3"]:</p>';
echo '<div style="background: #f9fafb; padding: 20px; border-radius: 8px;">';
echo do_shortcode('[academies limit="3"]');
echo '</div>';
echo '</div>';

// 7. راه‌حل‌ها
echo '<div class="box">';
echo '<h2>7️⃣ راه‌حل‌ها</h2>';
echo '<ul>';
echo '<li>اگر شورت کدها ثبت نشده‌اند: پلاگین را غیرفعال و دوباره فعال کنید</li>';
echo '<li>اگر پست‌ها وجود ندارند: یک آموزشگاه/مدرسه/معلم تستی اضافه کنید</li>';
echo '<li>اگر خروجی خالی است: Permalink را بازنویسی کنید (تنظیمات > پیوندهای یکتا > ذخیره)</li>';
echo '<li>کش مرورگر و سرور را پاک کنید</li>';
echo '</ul>';
echo '</div>';

?>

<div class="box">
    <h2>📝 نحوه استفاده از شورت کدها</h2>
    <div class="code">
        [academies]<br>
        [academies limit="12"]<br>
        [academies city="tehran"]<br>
        [academies subject="math"]<br>
        [academies city="tehran" subject="english" limit="6"]<br><br>
        
        [schools]<br>
        [schools limit="9"]<br>
        [schools city="mashhad"]<br><br>
        
        [teachers]<br>
        [teachers limit="8"]<br>
        [teachers subject="physics"]
    </div>
</div>

</body>
</html>
