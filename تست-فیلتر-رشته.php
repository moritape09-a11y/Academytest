<?php
/**
 * اسکریپت تست فیلتر رشته
 * 
 * این اسکریپت tax_query را تست می‌کند
 */

// بارگذاری WordPress
require_once(__DIR__ . '/../../../wp-load.php');

echo "<!DOCTYPE html>";
echo "<html lang='fa' dir='rtl'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<title>تست فیلتر رشته</title>";
echo "<style>";
echo "body { font-family: Tahoma, Arial; padding: 2rem; background: #f5f5f5; }";
echo ".test-box { background: white; padding: 2rem; margin: 1rem 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }";
echo ".success { color: #22c55e; font-weight: bold; }";
echo ".error { color: #ef4444; font-weight: bold; }";
echo ".info { color: #3b82f6; }";
echo "pre { background: #f9fafb; padding: 1rem; border-radius: 4px; overflow-x: auto; direction: ltr; text-align: left; }";
echo "h2 { color: #1f2937; border-bottom: 2px solid #3b82f6; padding-bottom: 0.5rem; }";
echo "h3 { color: #374151; }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<h1>🔍 تست فیلتر رشته</h1>";

// 1. لیست رشته‌ها
echo "<div class='test-box'>";
echo "<h2>📚 رشته‌های موجود:</h2>";
$subjects = get_terms(array(
    'taxonomy' => 'subject',
    'hide_empty' => false,
));

if ($subjects && !is_wp_error($subjects)) {
    echo "<p class='success'>✅ تعداد رشته‌ها: " . count($subjects) . "</p>";
    echo "<ul>";
    foreach ($subjects as $subject) {
        echo "<li>";
        echo "<strong>" . esc_html($subject->name) . "</strong> ";
        echo "<span class='info'>(slug: " . esc_html($subject->slug) . ", ID: " . $subject->term_id . ")</span>";
        
        // تعداد پست‌ها
        $count = wp_count_posts('academy')->publish + wp_count_posts('school')->publish + wp_count_posts('teacher')->publish;
        echo " - <span class='info'>تعداد کل پست‌ها: " . $count . "</span>";
        echo "</li>";
    }
    echo "</ul>";
} else {
    echo "<p class='error'>❌ رشته‌ای پیدا نشد!</p>";
}
echo "</div>";

// 2. تست query با رشته اول
if (!empty($subjects)) {
    $first_subject = $subjects[0];
    
    echo "<div class='test-box'>";
    echo "<h2>🧪 تست 1: جستجو با slug</h2>";
    echo "<p>رشته: <strong>" . $first_subject->name . "</strong> (slug: " . $first_subject->slug . ")</p>";
    
    $args = array(
        'post_type' => array('academy', 'school', 'teacher'),
        'posts_per_page' => 10,
        'post_status' => 'publish',
        'tax_query' => array(
            array(
                'taxonomy' => 'subject',
                'field' => 'slug',
                'terms' => $first_subject->slug,
                'operator' => 'IN',
            ),
        ),
    );
    
    echo "<h3>Query Args:</h3>";
    echo "<pre>" . print_r($args, true) . "</pre>";
    
    $query = new WP_Query($args);
    
    if ($query->have_posts()) {
        echo "<p class='success'>✅ پیدا شد! تعداد: " . $query->found_posts . "</p>";
        echo "<ul>";
        while ($query->have_posts()) {
            $query->the_post();
            $type = get_post_type();
            $type_label = $type == 'academy' ? 'آموزشگاه' : ($type == 'school' ? 'مدرسه' : 'معلم');
            echo "<li><strong>" . get_the_title() . "</strong> (" . $type_label . ")</li>";
        }
        echo "</ul>";
        wp_reset_postdata();
    } else {
        echo "<p class='error'>❌ هیچ پستی پیدا نشد!</p>";
        
        // بررسی کنیم چند پست این رشته را دارند
        echo "<h3>بررسی:</h3>";
        $all_posts = get_posts(array(
            'post_type' => array('academy', 'school', 'teacher'),
            'posts_per_page' => -1,
            'post_status' => 'publish',
        ));
        
        echo "<p>تعداد کل پست‌ها: " . count($all_posts) . "</p>";
        
        $found = 0;
        foreach ($all_posts as $post) {
            $terms = wp_get_post_terms($post->ID, 'subject');
            foreach ($terms as $term) {
                if ($term->slug == $first_subject->slug) {
                    $found++;
                    echo "<p class='info'>✓ پست '<strong>" . $post->post_title . "</strong>' این رشته را دارد</p>";
                }
            }
        }
        
        if ($found == 0) {
            echo "<p class='error'>❌ هیچ پستی این رشته را ندارد! لطفاً رشته را به پست‌ها اضافه کنید.</p>";
        }
    }
    echo "</div>";
    
    // تست 2: با term_id
    echo "<div class='test-box'>";
    echo "<h2>🧪 تست 2: جستجو با term_id</h2>";
    
    $args2 = array(
        'post_type' => array('academy', 'school', 'teacher'),
        'posts_per_page' => 10,
        'post_status' => 'publish',
        'tax_query' => array(
            array(
                'taxonomy' => 'subject',
                'field' => 'term_id',
                'terms' => $first_subject->term_id,
                'operator' => 'IN',
            ),
        ),
    );
    
    $query2 = new WP_Query($args2);
    
    if ($query2->have_posts()) {
        echo "<p class='success'>✅ با term_id هم کار کرد! تعداد: " . $query2->found_posts . "</p>";
        wp_reset_postdata();
    } else {
        echo "<p class='error'>❌ با term_id هم کار نکرد!</p>";
    }
    echo "</div>";
}

// 3. نمونه کد درست
echo "<div class='test-box'>";
echo "<h2>💡 راه حل:</h2>";
echo "<p>برای اینکه فیلتر رشته کار کند:</p>";
echo "<ol>";
echo "<li>از پنل مدیریت، یک آموزشگاه/مدرسه/معلم باز کنید</li>";
echo "<li>در سمت راست، جعبه 'رشته' را پیدا کنید</li>";
echo "<li>یک یا چند رشته را <strong>تیک</strong> بزنید</li>";
echo "<li>روی 'بروزرسانی' کلیک کنید</li>";
echo "<li>حالا فیلتر رشته کار می‌کند! ✅</li>";
echo "</ol>";
echo "</div>";

echo "</body>";
echo "</html>";
