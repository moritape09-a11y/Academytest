<?php
/**
 * Single School Template - Premium Design
 * 3D Effects, Glassmorphism, Modern UI/UX
 * 
 * @package EducationalDirectory
 * @version 3.1.0
 */

get_header();

while (have_posts()) : the_post();
    
    // Get meta data
    $phone = get_post_meta(get_the_ID(), 'phone', true);
    $email = get_post_meta(get_the_ID(), 'email', true);
    $website = get_post_meta(get_the_ID(), 'website', true);
    $address = get_post_meta(get_the_ID(), 'address', true);
    $telegram = get_post_meta(get_the_ID(), 'telegram', true);
    $instagram = get_post_meta(get_the_ID(), 'instagram', true);
    $whatsapp = get_post_meta(get_the_ID(), 'whatsapp', true);
    $rating = floatval(get_post_meta(get_the_ID(), 'rating', true));
    
    // Get taxonomies
    $cities = wp_get_post_terms(get_the_ID(), 'city');
    $subjects = wp_get_post_terms(get_the_ID(), 'subject');
    $grades = wp_get_post_terms(get_the_ID(), 'grade');
    
    ?>
    
    <article id="post-<?php the_ID(); ?>" <?php post_class('edu-single-post'); ?>>
        
        <!-- Hero Section -->
        <div class="edu-single-hero edu-hero-school">
            <div class="edu-hero-background"></div>
            <div class="edu-hero-overlay"></div>
            <div class="edu-hero-content">
                <div class="edu-hero-badge edu-badge-school">
                    <span class="edu-badge-icon">🏫</span>
                    <span class="edu-badge-text">مدرسه</span>
                </div>
                
                <h1 class="edu-hero-title"><?php the_title(); ?></h1>
                
                <?php if ($rating > 0): ?>
                    <div class="edu-hero-rating">
                        <?php 
                        $full_stars = floor($rating);
                        $has_half = ($rating - $full_stars) >= 0.5;
                        $empty_stars = 5 - $full_stars - ($has_half ? 1 : 0);
                        
                        for ($i = 0; $i < $full_stars; $i++) {
                            echo '<span class="edu-star edu-star-full">★</span>';
                        }
                        if ($has_half) {
                            echo '<span class="edu-star edu-star-half">★</span>';
                        }
                        for ($i = 0; $i < $empty_stars; $i++) {
                            echo '<span class="edu-star edu-star-empty">☆</span>';
                        }
                        ?>
                        <span class="edu-rating-number"><?php echo number_format($rating, 1); ?></span>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($cities) || !empty($subjects)): ?>
                    <div class="edu-hero-meta">
                        <?php if (!empty($cities)): ?>
                            <span class="edu-hero-meta-item">
                                <span class="edu-meta-icon">📍</span>
                                <?php echo esc_html($cities[0]->name); ?>
                            </span>
                        <?php endif; ?>
                        
                        <?php if (!empty($subjects)): ?>
                            <span class="edu-hero-meta-item">
                                <span class="edu-meta-icon">📚</span>
                                <?php echo count($subjects); ?> رشته
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Main Content Grid -->
        <div class="edu-single-container">
            <div class="edu-single-grid">
                
                <!-- Main Content -->
                <div class="edu-main-content">
                    
                    <!-- Featured Image Card -->
                    <?php if (has_post_thumbnail()): ?>
                        <div class="edu-content-card edu-image-card">
                            <div class="edu-card-glow"></div>
                            <?php the_post_thumbnail('large', array('class' => 'edu-featured-img')); ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Content Card -->
                    <div class="edu-content-card">
                        <div class="edu-card-header">
                            <h2 class="edu-card-title">
                                <span class="edu-title-icon">📖</span>
                                درباره مدرسه
                            </h2>
                        </div>
                        <div class="edu-card-body">
                            <?php the_content(); ?>
                        </div>
                    </div>
                    
                    <!-- Subjects Card -->
                    <?php if (!empty($subjects)): ?>
                        <div class="edu-content-card">
                            <div class="edu-card-header">
                                <h2 class="edu-card-title">
                                    <span class="edu-title-icon">📚</span>
                                    رشته‌های تدریس
                                </h2>
                            </div>
                            <div class="edu-card-body">
                                <div class="edu-tags-grid">
                                    <?php foreach ($subjects as $subject): ?>
                                        <a href="<?php echo get_term_link($subject); ?>" class="edu-tag">
                                            <span class="edu-tag-icon">✨</span>
                                            <?php echo esc_html($subject->name); ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                </div>
                
                <!-- Sidebar -->
                <aside class="edu-sidebar">
                    
                    <!-- Contact Card -->
                    <?php if ($phone || $email || $website || $address): ?>
                        <div class="edu-sidebar-card">
                            <div class="edu-card-header">
                                <h3 class="edu-card-title">
                                    <span class="edu-title-icon">📞</span>
                                    اطلاعات تماس
                                </h3>
                            </div>
                            <div class="edu-card-body">
                                <?php if ($phone): ?>
                                    <div class="edu-contact-item">
                                        <span class="edu-contact-icon">📱</span>
                                        <div class="edu-contact-content">
                                            <span class="edu-contact-label">تلفن</span>
                                            <a href="tel:<?php echo esc_attr($phone); ?>" class="edu-contact-link">
                                                <?php echo esc_html($phone); ?>
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($email): ?>
                                    <div class="edu-contact-item">
                                        <span class="edu-contact-icon">✉️</span>
                                        <div class="edu-contact-content">
                                            <span class="edu-contact-label">ایمیل</span>
                                            <a href="mailto:<?php echo esc_attr($email); ?>" class="edu-contact-link">
                                                <?php echo esc_html($email); ?>
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($website): ?>
                                    <div class="edu-contact-item">
                                        <span class="edu-contact-icon">🌐</span>
                                        <div class="edu-contact-content">
                                            <span class="edu-contact-label">وب‌سایت</span>
                                            <a href="<?php echo esc_url($website); ?>" target="_blank" class="edu-contact-link">
                                                مشاهده سایت
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($address): ?>
                                    <div class="edu-contact-item">
                                        <span class="edu-contact-icon">📍</span>
                                        <div class="edu-contact-content">
                                            <span class="edu-contact-label">آدرس</span>
                                            <p class="edu-contact-text"><?php echo esc_html($address); ?></p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Social Media Card -->
                    <?php if ($telegram || $instagram || $whatsapp): ?>
                        <div class="edu-sidebar-card">
                            <div class="edu-card-header">
                                <h3 class="edu-card-title">
                                    <span class="edu-title-icon">📱</span>
                                    شبکه‌های اجتماعی
                                </h3>
                            </div>
                            <div class="edu-card-body">
                                <div class="edu-social-links">
                                    <?php if ($telegram): ?>
                                        <a href="<?php echo strpos($telegram, 'http') === 0 ? esc_url($telegram) : 'https://t.me/' . ltrim($telegram, '@'); ?>" 
                                           target="_blank" class="edu-social-btn edu-social-telegram">
                                            <span class="edu-social-icon">📢</span>
                                            <span class="edu-social-text">تلگرام</span>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($instagram): ?>
                                        <a href="<?php echo strpos($instagram, 'http') === 0 ? esc_url($instagram) : 'https://instagram.com/' . ltrim($instagram, '@'); ?>" 
                                           target="_blank" class="edu-social-btn edu-social-instagram">
                                            <span class="edu-social-icon">📸</span>
                                            <span class="edu-social-text">اینستاگرام</span>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($whatsapp): ?>
                                        <a href="https://wa.me/<?php echo esc_attr($whatsapp); ?>" 
                                           target="_blank" class="edu-social-btn edu-social-whatsapp">
                                            <span class="edu-social-icon">💬</span>
                                            <span class="edu-social-text">واتساپ</span>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Grades Card -->
                    <?php if (!empty($grades)): ?>
                        <div class="edu-sidebar-card">
                            <div class="edu-card-header">
                                <h3 class="edu-card-title">
                                    <span class="edu-title-icon">🎯</span>
                                    پایه‌های تحصیلی
                                </h3>
                            </div>
                            <div class="edu-card-body">
                                <div class="edu-grades-list">
                                    <?php foreach ($grades as $grade): ?>
                                        <div class="edu-grade-item">
                                            <span class="edu-grade-icon">✓</span>
                                            <?php echo esc_html($grade->name); ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                </aside>
                
            </div>
        </div>
        
    </article>
    
    <?php
endwhile;

get_footer();
