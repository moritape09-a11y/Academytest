<?php
/**
 * تمپلیت تک معلم
 */

get_header();
?>

<div class="edu-single-wrapper">
    <?php
    while (have_posts()) : the_post();
        $phone = get_post_meta(get_the_ID(), 'phone', true);
        $email = get_post_meta(get_the_ID(), 'email', true);
        $experience = get_post_meta(get_the_ID(), 'experience', true);
        $price = get_post_meta(get_the_ID(), 'price', true);
    ?>
    
    <article class="edu-single edu-single-teacher">
        
        <!-- هدر صفحه -->
        <div class="edu-single-header">
            <div class="edu-back-link">
                <a href="<?php echo get_post_type_archive_link('teacher'); ?>">← بازگشت به لیست</a>
            </div>
            
            <h1 class="edu-title"><?php the_title(); ?></h1>
            
            <div class="edu-meta-top">
                <?php
                $city_terms = get_the_terms(get_the_ID(), 'city');
                if ($city_terms) {
                    echo '<div class="edu-terms">';
                    foreach ($city_terms as $term) {
                        echo '<span class="edu-term edu-term-city"><span class="dashicons dashicons-location"></span> ' . esc_html($term->name) . '</span>';
                    }
                    echo '</div>';
                }
                
                $subject_terms = get_the_terms(get_the_ID(), 'subject');
                if ($subject_terms) {
                    echo '<div class="edu-terms">';
                    foreach ($subject_terms as $term) {
                        echo '<span class="edu-term edu-term-subject"><span class="dashicons dashicons-book"></span> ' . esc_html($term->name) . '</span>';
                    }
                    echo '</div>';
                }
                ?>
            </div>
        </div>
        
        <!-- تصویر شاخص -->
        <?php if (has_post_thumbnail()): ?>
            <div class="edu-featured-image">
                <?php the_post_thumbnail('large'); ?>
            </div>
        <?php endif; ?>
        
        <!-- محتوای اصلی -->
        <div class="edu-main-content">
            <div class="edu-content-area">
                <h2 class="edu-section-title">درباره معلم</h2>
                <div class="edu-single-content">
                    <?php the_content(); ?>
                </div>
            </div>
            
            <!-- سایدبار اطلاعات -->
            <aside class="edu-sidebar">
                
                <?php if ($experience || $price): ?>
                <div class="edu-info-box edu-teaching-box">
                    <h3><span class="dashicons dashicons-welcome-learn-more"></span> اطلاعات تدریس</h3>
                    <ul class="edu-info-list">
                        <?php if ($experience): ?>
                            <li class="edu-info-item">
                                <span class="edu-info-icon">📚</span>
                                <div class="edu-info-details">
                                    <strong>سابقه تدریس:</strong>
                                    <span><?php echo esc_html($experience); ?> سال</span>
                                </div>
                            </li>
                        <?php endif; ?>
                        
                        <?php if ($price): ?>
                            <li class="edu-info-item">
                                <span class="edu-info-icon">💰</span>
                                <div class="edu-info-details">
                                    <strong>هزینه تدریس:</strong>
                                    <span><?php echo number_format($price); ?> تومان/ساعت</span>
                                </div>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
                <?php endif; ?>
                
                <?php if ($phone || $email): ?>
                <div class="edu-info-box edu-contact-box">
                    <h3><span class="dashicons dashicons-phone"></span> اطلاعات تماس</h3>
                    <ul class="edu-contact-list">
                        <?php if ($phone): ?>
                            <li class="edu-contact-item">
                                <span class="edu-contact-icon">📞</span>
                                <div class="edu-contact-info">
                                    <strong>تلفن:</strong>
                                    <a href="tel:<?php echo esc_attr($phone); ?>" class="edu-contact-link"><?php echo esc_html($phone); ?></a>
                                </div>
                            </li>
                        <?php endif; ?>
                        
                        <?php if ($email): ?>
                            <li class="edu-contact-item">
                                <span class="edu-contact-icon">📧</span>
                                <div class="edu-contact-info">
                                    <strong>ایمیل:</strong>
                                    <a href="mailto:<?php echo esc_attr($email); ?>" class="edu-contact-link"><?php echo esc_html($email); ?></a>
                                </div>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
                <?php endif; ?>
                
                <?php
                $grades = get_the_terms(get_the_ID(), 'grade');
                if ($grades):
                ?>
                <div class="edu-info-box edu-grades-box">
                    <h3><span class="dashicons dashicons-welcome-learn-more"></span> مقاطع تدریس</h3>
                    <div class="edu-tags">
                        <?php foreach ($grades as $grade): ?>
                            <span class="edu-tag"><?php echo esc_html($grade->name); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
            </aside>
        </div>
        
        <!-- معلمین مشابه -->
        <?php
        $related = new WP_Query(array(
            'post_type' => 'teacher',
            'posts_per_page' => 3,
            'post__not_in' => array(get_the_ID()),
            'orderby' => 'rand',
        ));
        
        if ($related->have_posts()):
        ?>
        <div class="edu-related-section">
            <h2 class="edu-section-title">معلمین دیگر</h2>
            <div class="edu-related-grid">
                <?php while ($related->have_posts()): $related->the_post(); ?>
                    <div class="edu-related-item">
                        <?php if (has_post_thumbnail()): ?>
                            <div class="edu-related-image">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('medium'); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        <h3 class="edu-related-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
                    </div>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </div>
        <?php endif; ?>
        
    </article>
    
    <?php endwhile; ?>
</div>

<?php get_footer(); ?>
