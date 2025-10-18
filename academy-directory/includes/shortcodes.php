<?php
if (!defined('ABSPATH')) { exit; }

function ad_get_current_url() {
    $scheme = is_ssl() ? 'https://' : 'http://';
    $host   = $_SERVER['HTTP_HOST'] ?? '';
    $uri    = strtok($_SERVER['REQUEST_URI'] ?? '', '?');
    return esc_url($scheme . $host . $uri);
}

function ad_render_search_form($form_id, $context, $extra_fields = array()) {
    $keyword = isset($_GET['ad_s']) && isset($_GET['ad_form']) && $_GET['ad_form'] === $form_id ? sanitize_text_field(wp_unslash($_GET['ad_s'])) : '';
    $subject = isset($_GET['ad_subject']) && isset($_GET['ad_form']) && $_GET['ad_form'] === $form_id ? sanitize_text_field(wp_unslash($_GET['ad_subject'])) : '';

    $subjects = get_terms(array(
        'taxonomy'   => 'subject',
        'hide_empty' => false,
    ));

    ob_start();
    ?>
    <form class="ad-search-form" method="get" action="<?php echo ad_get_current_url(); ?>">
        <input type="hidden" name="ad_form" value="<?php echo esc_attr($form_id); ?>" />
        <div class="ad-search-row">
            <input type="text" name="ad_s" value="<?php echo esc_attr($keyword); ?>" placeholder="<?php esc_attr_e('Search...', 'academy-directory'); ?>" />
            <select name="ad_subject">
                <option value=""><?php esc_html_e('All subjects', 'academy-directory'); ?></option>
                <?php foreach ($subjects as $term): ?>
                    <option value="<?php echo esc_attr($term->slug); ?>" <?php selected($subject, $term->slug); ?>><?php echo esc_html($term->name); ?></option>
                <?php endforeach; ?>
            </select>
            <?php foreach ($extra_fields as $html) { echo $html; } ?>
            <button type="submit" class="ad-btn"><?php esc_html_e('Search', 'academy-directory'); ?></button>
        </div>
    </form>
    <?php
    return ob_get_clean();
}

function ad_render_cards($query) {
    ob_start();
    ?>
    <div class="ad-cards">
        <?php if ($query->have_posts()): ?>
            <?php while ($query->have_posts()): $query->the_post(); ?>
                <article class="ad-card">
                    <a class="ad-card-thumb" href="<?php the_permalink(); ?>" aria-label="<?php the_title_attribute(); ?>">
                        <?php if (has_post_thumbnail()) {
                            the_post_thumbnail('medium_large');
                        } else {
                            echo '<div class="ad-thumb-placeholder"></div>';
                        } ?>
                    </a>
                    <div class="ad-card-body">
                        <h3 class="ad-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        <div class="ad-card-excerpt"><?php echo esc_html(wp_trim_words(get_the_excerpt() ?: wp_strip_all_tags(get_the_content()), 24)); ?></div>
                        <div class="ad-card-meta">
                            <?php
                            $subject_links = get_the_term_list(get_the_ID(), 'subject', '', ', ', '');
                            if ($subject_links) {
                                echo '<span class="ad-meta-item">' . wp_kses_post($subject_links) . '</span>';
                            }
                            ?>
                        </div>
                    </div>
                </article>
            <?php endwhile; wp_reset_postdata(); ?>
        <?php else: ?>
            <div class="ad-empty"><?php esc_html_e('No results found.', 'academy-directory'); ?></div>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}

function ad_build_query_args($base_args, $form_id, $extra_tax_queries = array()) {
    $args = $base_args;

    $submitted = isset($_GET['ad_form']) && $_GET['ad_form'] === $form_id;
    if ($submitted) {
        if (!empty($_GET['ad_s'])) {
            $args['s'] = sanitize_text_field(wp_unslash($_GET['ad_s']));
        }
        $tax_query = array();
        if (!empty($_GET['ad_subject'])) {
            $tax_query[] = array(
                'taxonomy' => 'subject',
                'field'    => 'slug',
                'terms'    => sanitize_text_field(wp_unslash($_GET['ad_subject'])),
            );
        }
        foreach ($extra_tax_queries as $extra) {
            $tax_query[] = $extra;
        }
        if (!empty($tax_query)) {
            $args['tax_query'] = count($tax_query) > 1 ? array_merge(array('relation' => 'AND'), $tax_query) : $tax_query;
        }
    }

    return $args;
}

// [edu_institutes]
function ad_shortcode_institutes($atts) {
    $atts = shortcode_atts(array(
        'posts_per_page' => 12,
    ), $atts, 'edu_institutes');

    $form_id = 'ad_ins_' . wp_generate_password(6, false, false);

    $extra_fields = array();
    // No extra fields beyond subject/keyword for institutes

    $args = ad_build_query_args(array(
        'post_type'      => 'edu_org',
        'posts_per_page' => intval($atts['posts_per_page']),
        'tax_query'      => array(
            array(
                'taxonomy' => 'org_type',
                'field'    => 'slug',
                'terms'    => array('institute'),
            )
        ),
    ), $form_id);

    $query = new WP_Query($args);

    $html  = ad_render_search_form($form_id, 'institutes', $extra_fields);
    $html .= ad_render_cards($query);
    return $html;
}
add_shortcode('edu_institutes', 'ad_shortcode_institutes');

// [edu_schools]
function ad_shortcode_schools($atts) {
    $atts = shortcode_atts(array(
        'posts_per_page' => 12,
    ), $atts, 'edu_schools');

    $form_id = 'ad_sch_' . wp_generate_password(6, false, false);

    $args = ad_build_query_args(array(
        'post_type'      => 'edu_org',
        'posts_per_page' => intval($atts['posts_per_page']),
        'tax_query'      => array(
            array(
                'taxonomy' => 'org_type',
                'field'    => 'slug',
                'terms'    => array('school'),
            )
        ),
    ), $form_id);

    $query = new WP_Query($args);

    $html  = ad_render_search_form($form_id, 'schools');
    $html .= ad_render_cards($query);
    return $html;
}
add_shortcode('edu_schools', 'ad_shortcode_schools');

// [edu_teachers]
function ad_shortcode_teachers($atts) {
    $atts = shortcode_atts(array(
        'posts_per_page' => 12,
    ), $atts, 'edu_teachers');

    $form_id = 'ad_tch_' . wp_generate_password(6, false, false);

    // Extra field: teacher_type select
    $teacher_type = isset($_GET['ad_teacher_type']) && isset($_GET['ad_form']) && $_GET['ad_form'] === $form_id ? sanitize_text_field(wp_unslash($_GET['ad_teacher_type'])) : '';
    $teacher_types = get_terms(array(
        'taxonomy'   => 'teacher_type',
        'hide_empty' => false,
    ));

    $extra_fields = array();
    if (!is_wp_error($teacher_types)) {
        ob_start();
        ?>
        <select name="ad_teacher_type">
            <option value=""><?php esc_html_e('All teacher types', 'academy-directory'); ?></option>
            <?php foreach ($teacher_types as $term): ?>
                <option value="<?php echo esc_attr($term->slug); ?>" <?php selected($teacher_type, $term->slug); ?>><?php echo esc_html($term->name); ?></option>
            <?php endforeach; ?>
        </select>
        <?php
        $extra_fields[] = ob_get_clean();
    }

    $extra_tax = array();
    if (!empty($teacher_type) && isset($_GET['ad_form']) && $_GET['ad_form'] === $form_id) {
        $extra_tax[] = array(
            'taxonomy' => 'teacher_type',
            'field'    => 'slug',
            'terms'    => $teacher_type,
        );
    }

    $args = ad_build_query_args(array(
        'post_type'      => 'teacher',
        'posts_per_page' => intval($atts['posts_per_page']),
    ), $form_id, $extra_tax);

    $query = new WP_Query($args);

    $html  = ad_render_search_form($form_id, 'teachers', $extra_fields);
    $html .= ad_render_cards($query);
    return $html;
}
add_shortcode('edu_teachers', 'ad_shortcode_teachers');
