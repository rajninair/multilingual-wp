<?php
/**
 * Template Name: Our Products
 */

get_header();

// Check if WooCommerce is active
if (!function_exists('WC')) {
    echo '<div class="woocommerce-error">' . esc_html__('WooCommerce is required for this page to work properly.', 'multilingual-shop-child') . '</div>';
    get_footer();
    return;
}
?>

<div class="products-page-container">
    <div class="products-sidebar">
        <div class="filter-section">
            <h3><?php esc_html_e('Categories', 'multilingual-shop-child'); ?></h3>
            <?php
            $product_categories = get_terms(array(
                'taxonomy' => 'product_cat',
                'hide_empty' => true,
            ));
            if (!empty($product_categories) && !is_wp_error($product_categories)) {
                echo '<ul class="category-filter">';
                foreach ($product_categories as $category) {
                    echo '<li>';
                    echo '<a href="' . esc_url(get_term_link($category)) . '">';
                    echo esc_html($category->name);
                    echo '</a>';
                    echo '</li>';
                }
                echo '</ul>';
            }
            ?>
        </div>

        <div class="filter-section">
            <h3><?php esc_html_e('Attributes', 'multilingual-shop-child'); ?></h3>
            <?php
            if (function_exists('wc_get_attribute_taxonomies')) {
                $attributes = wc_get_attribute_taxonomies();
                foreach ($attributes as $attribute) {
                    $terms = get_terms(array(
                        'taxonomy' => 'pa_' . $attribute->attribute_name,
                        'hide_empty' => true,
                    ));
                    if (!empty($terms) && !is_wp_error($terms)) {
                        echo '<div class="attribute-filter">';
                        echo '<h4>' . esc_html($attribute->attribute_label) . '</h4>';
                        echo '<ul>';
                        foreach ($terms as $term) {
                            echo '<li>';
                            echo '<label>';
                            echo '<input type="checkbox" name="' . esc_attr($attribute->attribute_name) . '" value="' . esc_attr($term->slug) . '">';
                            echo esc_html($term->name);
                            echo '</label>';
                            echo '</li>';
                        }
                        echo '</ul>';
                        echo '</div>';
                    }
                }
            }
            ?>
        </div>
    </div>

    <div class="products-content">
        <div class="products-grid">
            <?php
            $args = array(
                'post_type' => 'product',
                'posts_per_page' => 12,
                'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
            );
            $products = new WP_Query($args);

            if ($products->have_posts()) {
                while ($products->have_posts()) {
                    $products->the_post();
                    ?>
                    <div class="product-card">
                        <a href="<?php the_permalink(); ?>" class="product-link">
                            <div class="product-image">
                                <?php
                                if (has_post_thumbnail()) {
                                    the_post_thumbnail('medium');
                                }
                                ?>
                            </div>
                            <div class="product-info">
                                <h2 class="product-title"><?php the_title(); ?></h2>
                                <div class="product-price">
                                    <?php
                                    $product = wc_get_product(get_the_ID());
                                    if ($product) {
                                        echo $product->get_price_html();
                                    }
                                    ?>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php
                }
                wp_reset_postdata();
            } else {
                echo '<p class="no-products">' . esc_html__('No products found.', 'multilingual-shop-child') . '</p>';
            }
            ?>
        </div>

        <div class="pagination">
            <?php
            echo paginate_links(array(
                'total' => $products->max_num_pages,
                'current' => max(1, get_query_var('paged')),
            ));
            ?>
        </div>
    </div>
</div>

<?php
get_footer(); 