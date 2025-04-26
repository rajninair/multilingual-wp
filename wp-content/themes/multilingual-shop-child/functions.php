<?php
/**
 * Multilingual Shop Child Theme functions and definitions
 */

// Check if WooCommerce is active
function multilingual_shop_is_woocommerce_active() {
    return in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')));
}

// Enqueue parent and child theme styles
function multilingual_shop_child_enqueue_styles() {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/style.css', array('parent-style'));
    
    // Enqueue custom scripts and styles
    wp_enqueue_style('multilingual-shop-style', get_stylesheet_directory_uri() . '/assets/css/style.css');
    wp_enqueue_script('multilingual-shop-script', get_stylesheet_directory_uri() . '/assets/js/script.js', array('jquery'), '1.0.0', true);
    
    // Localize script for AJAX
    wp_localize_script('multilingual-shop-script', 'multilingual_shop', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('multilingual_shop_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'multilingual_shop_child_enqueue_styles');

// Register custom post types and taxonomies if needed
function multilingual_shop_register_post_types() {
    // Add custom post types here if needed
}
add_action('init', 'multilingual_shop_register_post_types');

// Add language switcher to header
function multilingual_shop_language_switcher() {
    if (function_exists('icl_get_languages')) {
        $languages = icl_get_languages('skip_missing=0');
        if (!empty($languages)) {
            echo '<div class="language-switcher">';
            echo '<select id="language-select" onchange="changeLanguage(this.value)">';
            foreach ($languages as $language) {
                echo '<option value="' . esc_attr($language['url']) . '" ' . selected($language['active'], 1, false) . '>';
                echo esc_html($language['native_name']);
                echo '</option>';
            }
            echo '</select>';
            echo '</div>';
        }
    }
}

// Add language switcher JavaScript
function multilingual_shop_add_language_script() {
    ?>
    <script>
    function changeLanguage(url) {
        window.location.href = url;
    }
    </script>
    <?php
}
add_action('wp_footer', 'multilingual_shop_add_language_script');

// Custom template for products page
function multilingual_shop_products_template($template) {
    if (is_page('our-products')) {
        $new_template = locate_template(array('templates/our-products.php'));
        if (!empty($new_template)) {
            return $new_template;
        }
    }
    if (is_page('product-details')) {
        $new_template = locate_template(array('templates/product-details.php'));
        if (!empty($new_template)) {
            return $new_template;
        }
    }
    return $template;
}
add_filter('template_include', 'multilingual_shop_products_template');

// AJAX handlers for product filtering
function multilingual_shop_ajax_handlers() {
    add_action('wp_ajax_filter_products', 'multilingual_shop_filter_products');
    add_action('wp_ajax_nopriv_filter_products', 'multilingual_shop_filter_products');
    add_action('wp_ajax_filter_by_category', 'multilingual_shop_filter_by_category');
    add_action('wp_ajax_nopriv_filter_by_category', 'multilingual_shop_filter_by_category');
    add_action('wp_ajax_get_variation_data', 'multilingual_shop_get_variation_data');
    add_action('wp_ajax_nopriv_get_variation_data', 'multilingual_shop_get_variation_data');
}
add_action('init', 'multilingual_shop_ajax_handlers');

// Filter products by attributes
function multilingual_shop_filter_products() {
    check_ajax_referer('multilingual_shop_nonce', 'nonce');
    
    if (!multilingual_shop_is_woocommerce_active()) {
        wp_send_json_error('WooCommerce is not active');
        return;
    }

    $attributes = isset($_POST['attributes']) ? $_POST['attributes'] : array();
    $tax_query = array();

    foreach ($attributes as $attribute => $terms) {
        if (!empty($terms)) {
            $tax_query[] = array(
                'taxonomy' => 'pa_' . sanitize_title($attribute),
                'field' => 'slug',
                'terms' => $terms
            );
        }
    }

    $args = array(
        'post_type' => 'product',
        'posts_per_page' => 12,
        'tax_query' => $tax_query
    );

    $products = new WP_Query($args);
    ob_start();

    if ($products->have_posts()) {
        while ($products->have_posts()) {
            $products->the_post();
            wc_get_template_part('content', 'product');
        }
    }

    $html = ob_get_clean();
    wp_reset_postdata();

    wp_send_json_success(array('html' => $html));
}

// Filter products by category
function multilingual_shop_filter_by_category() {
    check_ajax_referer('multilingual_shop_nonce', 'nonce');
    
    if (!multilingual_shop_is_woocommerce_active()) {
        wp_send_json_error('WooCommerce is not active');
        return;
    }

    $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '';
    
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => 12,
        'tax_query' => array(
            array(
                'taxonomy' => 'product_cat',
                'field' => 'slug',
                'terms' => $category
            )
        )
    );

    $products = new WP_Query($args);
    ob_start();

    if ($products->have_posts()) {
        while ($products->have_posts()) {
            $products->the_post();
            wc_get_template_part('content', 'product');
        }
    }

    $html = ob_get_clean();
    wp_reset_postdata();

    wp_send_json_success(array('html' => $html));
}

// Get variation data
function multilingual_shop_get_variation_data() {
    check_ajax_referer('multilingual_shop_nonce', 'nonce');
    
    if (!multilingual_shop_is_woocommerce_active()) {
        wp_send_json_error('WooCommerce is not active');
        return;
    }

    $attributes = isset($_POST['attributes']) ? $_POST['attributes'] : array();
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    
    if (!$product_id) {
        wp_send_json_error('Invalid product ID');
        return;
    }

    $product = wc_get_product($product_id);
    if (!$product || !$product->is_type('variable')) {
        wp_send_json_error('Invalid product');
        return;
    }

    $variation = $product->get_matching_variation($attributes);
    if ($variation) {
        $variation_obj = wc_get_product($variation);
        $response = array(
            'price' => $variation_obj->get_price_html(),
            'stock_status' => $variation_obj->get_stock_status(),
            'add_to_cart_html' => woocommerce_variable_add_to_cart()
        );
        wp_send_json_success($response);
    } else {
        wp_send_json_error('No matching variation found');
    }
} 