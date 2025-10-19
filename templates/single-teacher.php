<?php
/**
 * تمپلیت تک معلم
 */

get_header();

if (have_posts()) :
    while (have_posts()) : the_post();
        $phone = get_post_meta(get_the_ID(), 'phone', true);
        $email = get_post_meta(get_the_ID(), 'email', true);
        $experience = get_post_meta(get_the_ID(), 'experience', true);
        $price = get_post_meta(get_the_ID(), 'price', true);
        $telegram = get_post_meta(get_the_ID(), 'telegram', true);
        $instagram = get_post_meta(get_the_ID(), 'instagram', true);
        $whatsapp = get_post_meta(get_the_ID(), 'whatsapp', true);
        $rating = get_post_meta(get_the_ID(), 'rating', true);
        if (empty($rating)) $rating = 5;
?>

<div class="edu-single-wrapper" style="max-width: 1200px; margin: 2rem auto; padding: 0 1rem;">
    <article class="edu-single edu-single-teacher" style="background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        
        <!-- هدر -->
        <div class="edu-single-header" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: #fff; padding: 2rem;">
            <div class="edu-back-link" style="margin-bottom: 1rem;">
                <a href="<?php echo get_post_type_archive_link('teacher'); ?>" style="color: #fff; text-decoration: none; opacity: 0.9;">← بازگشت به لیست</a>
            </div>
            
            <h1 style="font-size: 2.5rem; margin: 1rem 0; font-weight: 700;"><?php the_title(); ?></h1>
            
            <?php if ($rating): ?>
                <div style="margin: 1rem 0;">
                    <div class="edu-rating" style="font-size: 1.5rem;">
                        <?php 
                        if (function_exists('edu_render_rating')) {
                            echo edu_render_rating($rating);
                        }
                        ?>
                        <span style="font-size: 1.25rem; font-weight: 700; margin-right: 0.5rem;"><?php echo number_format($rating, 1); ?></span>
                        <span style="font-size: 1rem; opacity: 0.9;">(امتیاز)</span>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="edu-meta-top">
                <?php
                $city_terms = get_the_terms(get_the_ID(), 'city');
                if ($city_terms) {
                    echo '<div class="edu-terms" style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin: 0.5rem 0;">';
                    foreach ($city_terms as $term) {
                        echo '<span style="padding: 0.5rem 1rem; background: rgba(255,255,255,0.2); border-radius: 20px;">📍 ' . esc_html($term->name) . '</span>';
                    }
                    echo '</div>';
                }
                
                $subject_terms = get_the_terms(get_the_ID(), 'subject');
                if ($subject_terms) {
                    echo '<div class="edu-terms" style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin: 0.5rem 0;">';
                    foreach ($subject_terms as $term) {
                        echo '<span style="padding: 0.5rem 1rem; background: rgba(255,255,255,0.2); border-radius: 20px;">📚 ' . esc_html($term->name) . '</span>';
                    }
                    echo '</div>';
                }
                ?>
            </div>
        </div>
        
        <!-- عکس -->
        <?php if (has_post_thumbnail()): ?>
            <div class="edu-featured-image">
                <?php the_post_thumbnail('large', array('style' => 'width: 100%; height: auto; display: block;')); ?>
            </div>
        <?php endif; ?>
        
        <!-- محتوا -->
        <div style="display: grid; grid-template-columns: 1fr 350px; gap: 2rem; padding: 2rem;">
            <div class="edu-content-area">
                <h2 style="font-size: 1.75rem; color: #1a202c; margin: 0 0 1.5rem 0; padding-bottom: 0.75rem; border-bottom: 3px solid #e5e7eb;">درباره معلم</h2>
                <div class="edu-single-content" style="font-size: 1.1rem; line-height: 1.8; color: #374151;">
                    <?php the_content(); ?>
                </div>
            </div>
            
            <!-- سایدبار -->
            <aside class="edu-sidebar">
                <?php if ($experience || $price): ?>
                <div style="background: #f9fafb; padding: 1.5rem; border-radius: 12px; border: 2px solid #e5e7eb; margin-bottom: 1.5rem;">
                    <h3 style="color: #2563eb; font-size: 1.25rem; margin: 0 0 1rem 0; border-bottom: 2px solid #dbeafe; padding-bottom: 0.75rem;">📚 اطلاعات تدریس</h3>
                    <?php if ($experience): ?>
                        <p style="margin: 0.75rem 0;"><strong>سابقه تدریس:</strong> <?php echo esc_html($experience); ?> سال</p>
                    <?php endif; ?>
                    <?php if ($price): ?>
                        <p style="margin: 0.75rem 0;"><strong>هزینه:</strong> <?php echo number_format($price); ?> تومان/ساعت</p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <?php if ($phone || $email): ?>
                <div style="background: #f9fafb; padding: 1.5rem; border-radius: 12px; border: 2px solid #e5e7eb; margin-bottom: 1.5rem;">
                    <h3 style="color: #2563eb; font-size: 1.25rem; margin: 0 0 1rem 0; border-bottom: 2px solid #dbeafe; padding-bottom: 0.75rem;">📞 اطلاعات تماس</h3>
                    <?php if ($phone): ?>
                        <p style="margin: 0.75rem 0;"><strong>تلفن:</strong> <a href="tel:<?php echo esc_attr($phone); ?>" style="color: #2563eb;"><?php echo esc_html($phone); ?></a></p>
                    <?php endif; ?>
                    <?php if ($email): ?>
                        <p style="margin: 0.75rem 0;"><strong>ایمیل:</strong> <a href="mailto:<?php echo esc_attr($email); ?>" style="color: #2563eb;"><?php echo esc_html($email); ?></a></p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <?php if ($telegram || $instagram || $whatsapp): ?>
                <div style="background: #f9fafb; padding: 1.5rem; border-radius: 12px; border: 2px solid #e5e7eb;">
                    <h3 style="color: #2563eb; font-size: 1.25rem; margin: 0 0 1rem 0; border-bottom: 2px solid #dbeafe; padding-bottom: 0.75rem;">🌐 شبکه‌های اجتماعی</h3>
                    <div class="edu-social-links">
                        <?php if ($telegram): 
                            $tg_link = (strpos($telegram, 'http') === 0) ? $telegram : 'https://t.me/' . ltrim($telegram, '@');
                        ?>
                            <a href="<?php echo esc_url($tg_link); ?>" target="_blank" class="edu-social-link edu-social-telegram">
                                <span class="edu-social-icon">✈️</span>
                                <span>تلگرام</span>
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($instagram): 
                            $ig_link = (strpos($instagram, 'http') === 0) ? $instagram : 'https://instagram.com/' . ltrim($instagram, '@');
                        ?>
                            <a href="<?php echo esc_url($ig_link); ?>" target="_blank" class="edu-social-link edu-social-instagram">
                                <span class="edu-social-icon">📷</span>
                                <span>اینستاگرام</span>
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($whatsapp): ?>
                            <a href="https://wa.me/<?php echo esc_attr($whatsapp); ?>" target="_blank" class="edu-social-link edu-social-whatsapp">
                                <span class="edu-social-icon">💬</span>
                                <span>واتساپ</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </aside>
        </div>
        
    </article>
</div>

<?php
    endwhile;
endif;

get_footer();
?>