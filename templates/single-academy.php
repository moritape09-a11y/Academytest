<?php
/**
 * تمپلیت تک آموزشگاه
 */

get_header();

while (have_posts()) : the_post();
    $phone = get_post_meta(get_the_ID(), 'phone', true);
    $email = get_post_meta(get_the_ID(), 'email', true);
    $address = get_post_meta(get_the_ID(), 'address', true);
    $website = get_post_meta(get_the_ID(), 'website', true);
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
        <?php if ($phone || $email || $website): ?>
            <div class="edu-info-box">
                <h3>اطلاعات تماس</h3>
                <?php if ($phone): ?>
                    <p><strong>📞 تلفن:</strong> <a href="tel:<?php echo esc_attr($phone); ?>"><?php echo esc_html($phone); ?></a></p>
                <?php endif; ?>
                <?php if ($email): ?>
                    <p><strong>📧 ایمیل:</strong> <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a></p>
                <?php endif; ?>
                <?php if ($website): ?>
                    <p><strong>🌐 وبسایت:</strong> <a href="<?php echo esc_url($website); ?>" target="_blank"><?php echo esc_html($website); ?></a></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($address): ?>
            <div class="edu-info-box">
                <h3>آدرس</h3>
                <p><?php echo nl2br(esc_html($address)); ?></p>
            </div>
        <?php endif; ?>
        
        <?php
        $subjects = get_the_terms(get_the_ID(), 'subject');
        if ($subjects):
        ?>
            <div class="edu-info-box">
                <h3>رشته‌های آموزشی</h3>
                <div class="edu-tags">
                    <?php foreach ($subjects as $subject): ?>
                        <a href="<?php echo get_term_link($subject); ?>" class="edu-tag"><?php echo esc_html($subject->name); ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</article>

<?php
endwhile;

get_footer();
