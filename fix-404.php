<?php
/**
 * اسکریپت رفع مشکل 404
 * این فایل را فقط یکبار اجرا کنید و بعد پاکش کنید!
 */

// بارگذاری WordPress
require_once('../../../wp-load.php');

if (!current_user_can('manage_options')) {
    die('شما دسترسی ندارید!');
}

echo '<html dir="rtl"><head><meta charset="UTF-8">';
echo '<style>body{font-family:Tahoma;padding:2rem;background:#f0f0f0;}';
echo '.box{background:#fff;padding:2rem;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.1);max-width:800px;margin:0 auto;}';
echo 'h1{color:#2563eb;border-bottom:3px solid #2563eb;padding-bottom:1rem;}';
echo '.success{background:#dcfce7;border:2px solid #16a34a;color:#15803d;padding:1rem;border-radius:8px;margin:1rem 0;}';
echo '.info{background:#dbeafe;border:2px solid #2563eb;color:#1e40af;padding:1rem;border-radius:8px;margin:1rem 0;}';
echo 'code{background:#f1f5f9;padding:0.25rem 0.5rem;border-radius:4px;font-size:0.9em;}';
echo 'ol{line-height:2;}';
echo '</style></head><body>';

echo '<div class="box">';
echo '<h1>🔧 رفع مشکل 404</h1>';

// فعال کردن پلاگین
if (!is_plugin_active('educational-directory/educational-directory.php')) {
    activate_plugin('educational-directory/educational-directory.php');
    echo '<div class="info">✅ پلاگین فعال شد</div>';
}

// ثبت مجدد CPT ها
if (function_exists('edu_register_post_types')) {
    edu_register_post_types();
    echo '<div class="success">✅ Custom Post Types ثبت شدند</div>';
}

// ثبت مجدد Taxonomies
if (function_exists('edu_register_taxonomies')) {
    edu_register_taxonomies();
    echo '<div class="success">✅ Taxonomies ثبت شدند</div>';
}

// فلاش Rewrite Rules
flush_rewrite_rules();
echo '<div class="success">✅ Permalink Rules بازنویسی شدند</div>';

// چک کردن تمپلیت‌ها
echo '<h2>📁 چک تمپلیت‌ها</h2>';
$templates = array(
    'single-academy.php',
    'single-school.php',
    'single-teacher.php'
);

foreach ($templates as $template) {
    $path = WP_PLUGIN_DIR . '/educational-directory/templates/' . $template;
    if (file_exists($path)) {
        echo '<div class="success">✅ ' . $template . ' موجود است</div>';
    } else {
        echo '<div class="error">❌ ' . $template . ' پیدا نشد!</div>';
    }
}

// چک کردن CPT ها
echo '<h2>📝 چک Custom Post Types</h2>';
$post_types = array('academy', 'school', 'teacher');
foreach ($post_types as $pt) {
    if (post_type_exists($pt)) {
        $count = wp_count_posts($pt);
        echo '<div class="success">✅ ' . $pt . ' ثبت شده (تعداد: ' . $count->publish . ')</div>';
        
        // لینک نمونه
        $posts = get_posts(array('post_type' => $pt, 'numberposts' => 1));
        if (!empty($posts)) {
            $link = get_permalink($posts[0]->ID);
            echo '<div class="info">🔗 لینک نمونه: <a href="' . $link . '" target="_blank">' . $link . '</a></div>';
        }
    } else {
        echo '<div class="error">❌ ' . $pt . ' ثبت نشده!</div>';
    }
}

echo '<h2>✅ انجام شد!</h2>';
echo '<div class="success">';
echo '<strong>حالا این کارها را انجام دهید:</strong><br><br>';
echo '<ol>';
echo '<li>این فایل را پاک کنید (fix-404.php) - مهم! 🔥</li>';
echo '<li>کش مرورگر را پاک کنید (Ctrl+F5)</li>';
echo '<li>یک آموزشگاه/معلم/مدرسه باز کنید</li>';
echo '<li>باید صفحه زیبا را ببینید! 🎉</li>';
echo '</ol>';
echo '</div>';

echo '<p><strong>اگر باز هم 404 می‌دید:</strong></p>';
echo '<div class="info">';
echo 'تنظیمات > پیوندهای یکتا > روی "نام نوشته" کلیک کنید > ذخیره تغییرات';
echo '</div>';

echo '</div></body></html>';
?>