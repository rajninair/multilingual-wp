<?php
/**
 * The Template for displaying single product pages
 */

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <?php
        while (have_posts()) :
            the_post();
            global $product;

            if (!$product) {
                return;
            }
            ?>
            <div class="container">
                <div class="product-detail-wrapper">
                    <div class="product-gallery">
                        <?php
                        if (has_post_thumbnail()) {
                            $image_id = get_post_thumbnail_id();
                            $full_size_image = wp_get_attachment_image_src($image_id, 'full');
                            $thumbnail = wp_get_attachment_image_src($image_id, 'woocommerce_thumbnail');
                            
                            echo '<div class="main-image">';
                            echo '<a href="' . esc_url($full_size_image[0]) . '" class="zoom" data-fancybox="gallery">';
                            echo get_the_post_thumbnail($post->ID, 'woocommerce_single', array('class' => 'main-image'));
                            echo '</a>';
                            echo '</div>';

                            // Product Gallery
                            $attachment_ids = $product->get_gallery_image_ids();
                            if ($attachment_ids) {
                                echo '<div class="product-thumbnails">';
                                foreach ($attachment_ids as $attachment_id) {
                                    $full_size_image = wp_get_attachment_image_src($attachment_id, 'full');
                                    $thumbnail = wp_get_attachment_image_src($attachment_id, 'woocommerce_thumbnail');
                                    echo '<a href="' . esc_url($full_size_image[0]) . '" class="thumbnail-item" data-fancybox="gallery">';
                                    echo wp_get_attachment_image($attachment_id, 'woocommerce_thumbnail');
                                    echo '</a>';
                                }
                                echo '</div>';
                            }
                        }
                        ?>
                    </div>

                    <div class="product-summary">
                        <h1 class="product-title"><?php the_title(); ?></h1>
                        
                        <div class="product-price">
                            <?php echo $product->get_price_html(); ?>
                        </div>

                        <div class="product-short-description">
                            <?php echo apply_filters('the_excerpt', get_the_excerpt()); ?>
                        </div>

                        <?php if ($product->is_in_stock()) : ?>
                            <div class="product-add-to-cart">
                                <?php
                                if ($product->is_type('variable')) {
                                    woocommerce_variable_add_to_cart();
                                } else {
                                    ?>
                                    <form class="cart" method="post" enctype="multipart/form-data">
                                        <?php
                                        if ($product->is_sold_individually()) {
                                            echo '<input type="hidden" name="quantity" value="1" />';
                                        } else {
                                            woocommerce_quantity_input(array(
                                                'min_value' => 1,
                                                'max_value' => $product->get_max_purchase_quantity(),
                                            ));
                                        }
                                        ?>
                                        <button type="submit" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>" class="single_add_to_cart_button button alt">
                                            <?php echo esc_html($product->single_add_to_cart_text()); ?>
                                        </button>
                                    </form>
                                    <?php
                                }
                                ?>
                            </div>
                        <?php else : ?>
                            <p class="stock out-of-stock"><?php esc_html_e('Out of stock', 'best-shop-child'); ?></p>
                        <?php endif; ?>

                        <div class="product-meta">
                            <?php
                            echo wc_get_product_category_list($product->get_id(), ', ', '<span class="posted_in">' . _n('Category:', 'Categories:', count($product->get_category_ids()), 'best-shop-child') . ' ', '</span>');
                            echo wc_get_product_tag_list($product->get_id(), ', ', '<span class="tagged_as">' . _n('Tag:', 'Tags:', count($product->get_tag_ids()), 'best-shop-child') . ' ', '</span>');
                            ?>
                        </div>
                    </div>
                </div>

                <div class="product-tabs">
                    <ul class="tabs">
                        <li class="active" data-tab="description"><?php esc_html_e('Description', 'best-shop-child'); ?></li>
                        <li data-tab="additional"><?php esc_html_e('Additional Information', 'best-shop-child'); ?></li>
                        <li data-tab="reviews"><?php esc_html_e('Reviews', 'best-shop-child'); ?></li>
                    </ul>

                    <div class="tab-content">
                        <div id="tab-description" class="tab-panel active">
                            <?php the_content(); ?>
                        </div>

                        <div id="tab-additional" class="tab-panel">
                            <?php do_action('woocommerce_product_additional_information', $product); ?>
                        </div>

                        <div id="tab-reviews" class="tab-panel">
                            <?php
                            if (comments_open() || get_comments_number()) :
                                comments_template();
                            endif;
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </main>
</div>

<style>
.product-detail-wrapper {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 40px;
    padding: 40px 0;
}

.product-gallery {
    position: relative;
}

.main-image {
    margin-bottom: 20px;
    border-radius: 8px;
    overflow: hidden;
}

.main-image img {
    width: 100%;
    height: auto;
    display: block;
}

.product-thumbnails {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
}

.thumbnail-item {
    border-radius: 4px;
    overflow: hidden;
    display: block;
}

.thumbnail-item img {
    width: 100%;
    height: auto;
    display: block;
    transition: opacity 0.3s ease;
}

.thumbnail-item:hover img {
    opacity: 0.8;
}

.product-summary {
    padding: 20px;
}

.product-title {
    font-size: 28px;
    margin: 0 0 20px;
    color: #333;
}

.product-price {
    font-size: 24px;
    color: #e94444;
    margin-bottom: 20px;
}

.product-short-description {
    color: #666;
    margin-bottom: 30px;
}

.product-add-to-cart {
    margin-bottom: 30px;
}

.quantity {
    margin-bottom: 20px;
}

.single_add_to_cart_button {
    width: 100%;
    padding: 15px;
    background: #007bff;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.single_add_to_cart_button:hover {
    background: #0056b3;
}

.product-meta {
    padding-top: 20px;
    border-top: 1px solid #eee;
    font-size: 14px;
    color: #666;
}

.product-tabs {
    margin: 40px 0;
}

.tabs {
    display: flex;
    list-style: none;
    padding: 0;
    margin: 0;
    border-bottom: 1px solid #eee;
}

.tabs li {
    padding: 15px 30px;
    cursor: pointer;
    color: #666;
    transition: all 0.3s ease;
}

.tabs li.active {
    color: #333;
    border-bottom: 2px solid #007bff;
}

.tab-content {
    padding: 30px 0;
}

.tab-panel {
    display: none;
}

.tab-panel.active {
    display: block;
}

@media (max-width: 768px) {
    .product-detail-wrapper {
        grid-template-columns: 1fr;
        gap: 20px;
    }

    .product-title {
        font-size: 24px;
    }

    .tabs {
        flex-wrap: wrap;
    }

    .tabs li {
        padding: 10px 20px;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Tab functionality
    $('.tabs li').click(function() {
        var tab_id = $(this).attr('data-tab');

        $('.tabs li').removeClass('active');
        $('.tab-panel').removeClass('active');

        $(this).addClass('active');
        $("#tab-" + tab_id).addClass('active');
    });
});
</script>

<?php get_footer(); ?> 