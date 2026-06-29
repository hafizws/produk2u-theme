<?php
/**
 * Produk2U Theme Functions
 *
 * @package Produk2U
 * @version 1.0.0
 */

defined('ABSPATH') || exit;

// Theme Constants
define('P2U_VERSION', '1.0.0');
define('P2U_DIR', get_template_directory());
define('P2U_URI', get_template_directory_uri());
define('P2U_INC', P2U_DIR . '/inc');

/**
 * Load Theme Components
 */
require_once P2U_INC . '/class-theme-setup.php';
require_once P2U_INC . '/class-custom-post-types.php';
require_once P2U_INC . '/class-acf-fields.php';
require_once P2U_INC . '/class-gutenberg-blocks.php';
require_once P2U_INC . '/class-seo-schema.php';
require_once P2U_INC . '/class-affiliate.php';
require_once P2U_INC . '/class-performance.php';
require_once P2U_INC . '/class-rankmath.php';

/**
 * Initialize Theme
 */
function p2u_init() {
    new P2U_Theme_Setup();
    new P2U_Custom_Post_Types();
    new P2U_ACF_Fields();
    new P2U_Gutenberg_Blocks();
    new P2U_SEO_Schema();
    new P2U_Affiliate();
    new P2U_Performance();
    new P2U_RankMath_Integration();
}
add_action('after_setup_theme', 'p2u_init', 5);

/**
 * Helper: Get ACF Field with fallback
 */
function p2u_field($field_name, $post_id = null) {
    if (function_exists('get_field')) {
        return get_field($field_name, $post_id);
    }
    return get_post_meta($post_id ?: get_the_ID(), $field_name, true);
}

/**
 * Helper: Get Product Rating
 */
function p2u_get_rating($post_id = null) {
    $rating = p2u_field('rating', $post_id);
    return $rating ? floatval($rating) : 0;
}

/**
 * Helper: Render Star Rating HTML
 */
function p2u_render_stars($rating, $echo = true) {
    $full_stars = floor($rating);
    $half_star = ($rating - $full_stars) >= 0.5;
    $empty_stars = 5 - $full_stars - ($half_star ? 1 : 0);
    
    $html = '<div class="p2u-stars" aria-label="Rating: ' . esc_attr($rating) . ' daripada 5">';
    
    for ($i = 0; $i < $full_stars; $i++) {
        $html .= '<svg class="p2u-stars__star" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>';
    }
    
    if ($half_star) {
        $html .= '<svg class="p2u-stars__star" viewBox="0 0 20 20"><defs><linearGradient id="half"><stop offset="50%" stop-color="currentColor"/><stop offset="50%" stop-color="#CBD5E1"/></linearGradient></defs><path fill="url(#half)" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>';
    }
    
    for ($i = 0; $i < $empty_stars; $i++) {
        $html .= '<svg class="p2u-stars__star p2u-stars__star--empty" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>';
    }
    
    $html .= '<span class="p2u-stars__value">' . esc_html(number_format($rating, 1)) . '</span>';
    $html .= '</div>';
    
    if ($echo) echo $html;
    return $html;
}

/**
 * Helper: Get Affiliate Button HTML
 */
function p2u_affiliate_buttons($post_id = null) {
    $post_id = $post_id ?: get_the_ID();
    $shopee = p2u_field('shopee_url', $post_id);
    $lazada = p2u_field('lazada_url', $post_id);
    $tiktok = p2u_field('tiktok_url', $post_id);
    
    if (!$shopee && !$lazada && !$tiktok) return '';
    
    $html = '<div class="p2u-cta-buttons">';
    
    if ($shopee) {
        $html .= '<a href="' . esc_url($shopee) . '" class="p2u-aff-btn p2u-aff-btn--shopee" target="_blank" rel="nofollow sponsored noopener">';
        $html .= '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h-2v-6h2v6zm4 0h-2v-6h2v6zm-2-8c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z"/></svg>';
        $html .= 'Semak Harga di Shopee</a>';
    }
    
    if ($lazada) {
        $html .= '<a href="' . esc_url($lazada) . '" class="p2u-aff-btn p2u-aff-btn--lazada" target="_blank" rel="nofollow sponsored noopener">';
        $html .= '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h-2v-6h2v6zm4 0h-2v-6h2v6zm-2-8c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z"/></svg>';
        $html .= 'Semak Harga di Lazada</a>';
    }
    
    if ($tiktok) {
        $html .= '<a href="' . esc_url($tiktok) . '" class="p2u-aff-btn p2u-aff-btn--tiktok" target="_blank" rel="nofollow sponsored noopener">';
        $html .= '<svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h-2v-6h2v6zm4 0h-2v-6h2v6zm-2-8c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z"/></svg>';
        $html .= 'Semak Harga di TikTok Shop</a>';
    }
    
    $html .= '</div>';
    
    return $html;
}

/**
 * Helper: Format Price in RM
 */
function p2u_format_price($price) {
    if (empty($price)) return '';
    return 'RM ' . number_format(floatval($price), 2);
}

/**
 * Helper: Get reading time
 */
function p2u_reading_time($post_id = null) {
    $content = get_post_field('post_content', $post_id ?: get_the_ID());
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200); // 200 words per minute for BM
    return max(1, $reading_time);
}

/**
 * Custom excerpt length
 */
function p2u_excerpt_length($length) {
    return 25;
}
add_filter('excerpt_length', 'p2u_excerpt_length');

/**
 * Custom excerpt more
 */
function p2u_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'p2u_excerpt_more');

/**
 * Add category to single post URL for better SEO
 */
function p2u_add_category_to_permalink($permalink, $post, $leavename) {
    if ($post->post_type !== 'post' || $post->post_type === 'page') {
        return $permalink;
    }
    
    $terms = get_the_terms($post->ID, 'category');
    if ($terms && !is_wp_error($terms)) {
        $category = $terms[0]->slug;
        $permalink = str_replace('%category%', $category, $permalink);
    }
    
    return $permalink;
}
add_filter('post_link', 'p2u_add_category_to_permalink', 10, 3);

/**
 * Disable emoji scripts for performance
 */
function p2u_disable_emojis() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
}
add_action('init', 'p2u_disable_emojis');

/**
 * Remove unnecessary head tags
 */
function p2u_cleanup_head() {
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wp_shortlink_wp_head');
    remove_action('wp_head', 'rest_output_link_wp_head');
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
}
add_action('init', 'p2u_cleanup_head');