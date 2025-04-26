<?php
/**
 * Template Name: Shop Grid Layout
 * Template Post Type: page
 * 
 * This is a custom template for displaying products in a grid layout
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <div class="container">
            <div class="products-wrapper">
                <header class="page-header">
                    <h1 class="page-title"><?php 
                        if (is_page()) {
                            echo get_the_title();
                        } else {
                            echo esc_html__('Our Products', 'best-shop-child');
                        }
                    ?></h1>
                </header>

                <?php
                // Display page content if it exists
                if (is_page() && have_posts()) :
                    while (have_posts()) : the_post();
                        the_content();
                    endwhile;
                endif;
                ?>

                <div class="products-grid">
                    <?php
                    // WooCommerce product query
                    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                    $args = array(
                        'post_type' => 'product',
                        'posts_per_page' => 12,
                        'paged' => $paged,
                        'orderby' => 'date',
                        'order' => 'DESC',
                        'post_status' => 'publish'
                    );

                    // Add category filter if set
                    if (isset($_GET['category'])) {
                        $args['tax_query'] = array(
                            array(
                                'taxonomy' => 'product_cat',
                                'field' => 'slug',
                                'terms' => sanitize_text_field($_GET['category'])
                            )
                        );
                    }

                    $products = new WP_Query($args);

                    if ($products->have_posts()) :
                        // Display category filter
                        $categories = get_terms('product_cat', array('hide_empty' => true));
                        if (!empty($categories) && !is_wp_error($categories)) :
                            echo '<div class="product-categories">';
                            echo '<select id="category-filter" onchange="window.location.href=this.value">';
                            echo '<option value="' . esc_url(remove_query_arg('category')) . '">' . esc_html__('All Categories', 'best-shop-child') . '</option>';
                            foreach ($categories as $category) {
                                $selected = (isset($_GET['category']) && $_GET['category'] === $category->slug) ? 'selected' : '';
                                echo '<option value="' . esc_url(add_query_arg('category', $category->slug)) . '" ' . $selected . '>';
                                echo esc_html($category->name);
                                echo '</option>';
                            }
                            echo '</select>';
                            echo '</div>';
                        endif;

                        while ($products->have_posts()) : $products->the_post();
                            global $product;
                            if (!$product || !$product->is_visible()) {
                                continue;
                            }
                            ?>
                            <div class="product-card">
                                <a href="<?php echo esc_url(get_permalink()); ?>" class="product-link">
                                    <div class="product-image">
                                        <?php
                                        if (has_post_thumbnail()) {
                                            echo get_the_post_thumbnail(get_the_ID(), 'woocommerce_thumbnail');
                                        } else {
                                            echo '<img src="' . esc_url(wc_placeholder_img_src()) . '" alt="' . esc_attr__('Placeholder', 'best-shop-child') . '" />';
                                        }
                                        ?>
                                    </div>
                                    <div class="product-info">
                                        <h2 class="product-title"><?php the_title(); ?></h2>
                                        <div class="product-price">
                                            <?php echo $product->get_price_html(); ?>
                                        </div>
                                        <div class="product-excerpt">
                                            <?php echo wp_trim_words(get_the_excerpt(), 15); ?>
                                        </div>
                                    </div>
                                </a>
                                <div class="product-actions">
                                    <?php
                                    echo apply_filters('woocommerce_loop_add_to_cart_link',
                                        sprintf('<a href="%s" data-quantity="1" class="%s" %s>%s</a>',
                                            esc_url($product->add_to_cart_url()),
                                            esc_attr(implode(' ', array_filter(array(
                                                'button',
                                                'product_type_' . $product->get_type(),
                                                $product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
                                                $product->supports('ajax_add_to_cart') ? 'ajax_add_to_cart' : ''
                                            )))),
                                            wc_implode_html_attributes(array(
                                                'data-product_id' => $product->get_id(),
                                                'data-product_sku' => $product->get_sku(),
                                                'aria-label' => $product->add_to_cart_description(),
                                                'rel' => 'nofollow'
                                            )),
                                            esc_html($product->add_to_cart_text())
                                        ),
                                        $product
                                    );
                                    ?>
                                </div>
                            </div>
                            <?php
                        endwhile;

                        // Pagination
                        echo '<div class="pagination">';
                        echo paginate_links(array(
                            'total' => $products->max_num_pages,
                            'current' => $paged,
                            'prev_text' => '&larr;',
                            'next_text' => '&rarr;'
                        ));
                        echo '</div>';

                        wp_reset_postdata();
                    else :
                        echo '<p>' . esc_html__('No products found', 'best-shop-child') . '</p>';
                    endif;
                    ?>
                </div>
            </div>
        </div>
    </main>
</div>

<style>
.products-wrapper {
    padding: 40px 0;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 30px;
    margin-top: 30px;
}

.product-card {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
    overflow: hidden;
}

.product-card:hover {
    transform: translateY(-5px);
}

.product-link {
    text-decoration: none;
    color: inherit;
}

.product-image {
    position: relative;
    padding-top: 100%;
    overflow: hidden;
}

.product-image img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-card:hover .product-image img {
    transform: scale(1.05);
}

.product-info {
    padding: 20px;
}

.product-title {
    font-size: 18px;
    margin: 0 0 10px;
    color: #333;
}

.product-price {
    font-size: 16px;
    color: #e94444;
    margin-bottom: 10px;
}

.product-excerpt {
    font-size: 14px;
    color: #666;
    line-height: 1.4;
    margin-bottom: 15px;
}

.product-actions {
    padding: 0 20px 20px;
}

.product-actions .button {
    display: block;
    width: 100%;
    padding: 10px;
    text-align: center;
    background: #007bff;
    color: #fff;
    border: none;
    border-radius: 4px;
    text-decoration: none;
    transition: background-color 0.3s ease;
}

.product-actions .button:hover {
    background: #0056b3;
}

@media (max-width: 768px) {
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 20px;
    }

    .product-info {
        padding: 15px;
    }

    .product-title {
        font-size: 16px;
    }
}

/* Add new styles for category filter and pagination */
.product-categories {
    margin-bottom: 30px;
}

.product-categories select {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    min-width: 200px;
}

.pagination {
    margin-top: 40px;
    text-align: center;
}

.pagination .page-numbers {
    display: inline-block;
    padding: 8px 12px;
    margin: 0 4px;
    border: 1px solid #ddd;
    border-radius: 4px;
    text-decoration: none;
    color: #333;
}

.pagination .page-numbers.current {
    background: #007bff;
    color: #fff;
    border-color: #007bff;
}

.pagination .page-numbers:hover:not(.current) {
    background: #f5f5f5;
}
</style>

<?php get_footer(); ?> 