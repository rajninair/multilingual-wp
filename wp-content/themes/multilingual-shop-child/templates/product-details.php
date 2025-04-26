<?php
/**
 * Template Name: Product Details
 */

get_header();

// Check if WooCommerce is active
if (!function_exists('WC')) {
    echo '<div class="woocommerce-error">' . esc_html__('WooCommerce is required for this page to work properly.', 'multilingual-shop-child') . '</div>';
    get_footer();
    return;
}

global $product;
$product_id = get_query_var('product_id');
if ($product_id) {
    $product = wc_get_product($product_id);
}
?>

<div class="product-details-container">
    <?php if ($product) : ?>
        <div class="product-details-content">
            <div class="product-gallery">
                <?php
                $attachment_ids = $product->get_gallery_image_ids();
                if ($attachment_ids) {
                    echo '<div class="product-gallery-main">';
                    echo wp_get_attachment_image($product->get_image_id(), 'large');
                    echo '</div>';
                    echo '<div class="product-gallery-thumbs">';
                    foreach ($attachment_ids as $attachment_id) {
                        echo wp_get_attachment_image($attachment_id, 'thumbnail');
                    }
                    echo '</div>';
                } else {
                    echo wp_get_attachment_image($product->get_image_id(), 'large');
                }
                ?>
            </div>

            <div class="product-info">
                <h1 class="product-title"><?php echo esc_html($product->get_name()); ?></h1>
                
                <div class="product-price">
                    <?php echo $product->get_price_html(); ?>
                </div>

                <div class="product-description">
                    <?php echo apply_filters('the_content', $product->get_description()); ?>
                </div>

                <?php if ($product->is_type('variable')) : ?>
                    <div class="product-variations">
                        <?php
                        $attributes = $product->get_variation_attributes();
                        foreach ($attributes as $attribute_name => $options) {
                            echo '<div class="variation-select">';
                            echo '<label>' . wc_attribute_label($attribute_name) . '</label>';
                            echo '<select name="attribute_' . sanitize_title($attribute_name) . '">';
                            foreach ($options as $option) {
                                echo '<option value="' . esc_attr($option) . '">' . esc_html($option) . '</option>';
                            }
                            echo '</select>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                <?php endif; ?>

                <div class="product-add-to-cart">
                    <?php if ($product->is_in_stock()) : ?>
                        <form class="cart" method="post" enctype="multipart/form-data">
                            <?php
                            if ($product->is_type('variable')) {
                                woocommerce_variable_add_to_cart();
                            } else {
                                woocommerce_simple_add_to_cart();
                            }
                            ?>
                        </form>
                    <?php else : ?>
                        <p class="stock out-of-stock"><?php esc_html_e('Out of stock', 'multilingual-shop-child'); ?></p>
                    <?php endif; ?>
                </div>

                <div class="product-meta">
                    <?php if ($product->get_sku()) : ?>
                        <span class="sku-wrapper">
                            <?php esc_html_e('SKU:', 'multilingual-shop-child'); ?> 
                            <span class="sku"><?php echo esc_html($product->get_sku()); ?></span>
                        </span>
                    <?php endif; ?>

                    <?php 
                    $categories = wc_get_product_category_list($product->get_id(), ', ');
                    if ($categories) {
                        echo '<span class="posted-in">' . 
                             _n('Category:', 'Categories:', count($product->get_category_ids()), 'multilingual-shop-child') . 
                             ' ' . $categories . 
                             '</span>';
                    }
                    ?>
                </div>
            </div>
        </div>

        <div class="product-tabs">
            <?php
            $tabs = apply_filters('woocommerce_product_tabs', array());
            if (!empty($tabs)) :
                echo '<div class="woocommerce-tabs">';
                foreach ($tabs as $key => $tab) :
                    echo '<div class="tab-content" id="tab-' . esc_attr($key) . '">';
                    echo '<h2>' . esc_html($tab['title']) . '</h2>';
                    echo $tab['content'];
                    echo '</div>';
                endforeach;
                echo '</div>';
            endif;
            ?>
        </div>
    <?php else : ?>
        <p class="product-not-found"><?php esc_html_e('Product not found.', 'multilingual-shop-child'); ?></p>
    <?php endif; ?>
</div>

<?php
get_footer(); 