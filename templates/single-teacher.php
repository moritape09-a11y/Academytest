<?php
/**
 * تمپلیت تک معلم
 */

get_header();

while (have_posts()) : the_post();
    $phone = get_post_meta(get_the_ID(), 'phone', true);
    $email = get_post_meta(get_the_ID(), 'email', true);
    $experience = get_post_meta(get_the_ID(), 'experience', true);
    $price = get_post_meta(get_the_ID(), 'price', true);
?>

<article class="edu-single">
    <div class="edu-single-header">
        <h1><?php the_title(); ?></h1>
        
        <?php
        $terms = get_the_terms(get_the_ID(), 'city');
        if ($terms) {
            echo '<div class="edu-terms">';
            foreach ($terms as $term) {
                echo '<span class="edu-term">' . esc_html($term->name) . '</span>';
            }
            echo '</div>';
        }
        ?>
    </div>
    
    <?php if (has_post_thumbnail()): ?>
        <div class="edu-featured-image">
            <?php the_post_thumbnail('large'); ?>
        </div>
    <?php endif; ?>
    
    <div class="edu-single-content">
        <?php the_content(); ?>
    </div>
    
    <div class="edu-info-boxes">
        <?php if ($experience || $price): ?>
            <div class="edu-info-box">
                <h3>اطلاعات تدریس</h3>
                <?php if ($experience): ?>
                    <p><strong>📚 سابقه تدریس:</strong> <?php echo esc_html($experience); ?> سال</p>
                <?php endif; ?>
                <?php if ($price): ?>
                    <p><strong>💰 هزینه:</strong> <?php echo number_format($price); ?> تومان/ساعت</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($phone || $email): ?>
            <div class="edu-info-box">
                <h3>اطلاعات تماس</h3>
                <?php if ($phone): ?>
                    <p><strong>📞 تلفن:</strong> <a href="tel:<?php echo esc_attr($phone); ?>"><?php echo esc_html($phone); ?></a></p>
                <?php endif; ?>
                <?php if ($email): ?>
                    <p><strong>📧 ایمیل:</strong> <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <?php
        $subjects = get_the_terms(get_the_ID(), 'subject');
        $grades = get_the_terms(get_the_ID(), 'grade');
        ?>
        
        <?php if ($subjects): ?>
            <div class="edu-info-box">
                <h3>رشته‌های تدریس</h3>
                <div class="edu-tags">
                    <?php foreach ($subjects as $subject): ?>
                        <a href="<?php echo get_term_link($subject); ?>" class="edu-tag"><?php echo esc_html($subject->name); ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if ($grades): ?>
            <div class="edu-info-box">
                <h3>مقاطع</h3>
                <div class="edu-tags">
                    <?php foreach ($grades as $grade): ?>
                        <span class="edu-tag"><?php echo esc_html($grade->name); ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</article>

<?php
endwhile;

get_footer();
