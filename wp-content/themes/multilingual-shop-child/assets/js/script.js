jQuery(document).ready(function($) {
    // Product Gallery
    $('.product-gallery-thumbs img').click(function() {
        var newSrc = $(this).attr('src');
        $('.product-gallery-main img').attr('src', newSrc);
    });

    // Attribute Filtering
    $('.attribute-filter input[type="checkbox"]').change(function() {
        var selectedAttributes = {};
        
        $('.attribute-filter input[type="checkbox"]:checked').each(function() {
            var attribute = $(this).attr('name');
            if (!selectedAttributes[attribute]) {
                selectedAttributes[attribute] = [];
            }
            selectedAttributes[attribute].push($(this).val());
        });

        // AJAX request to filter products
        $.ajax({
            url: multilingual_shop.ajax_url,
            type: 'POST',
            data: {
                action: 'filter_products',
                attributes: selectedAttributes,
                nonce: multilingual_shop.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('.products-grid').html(response.data.html);
                }
            }
        });
    });

    // Category Filtering
    $('.category-filter a').click(function(e) {
        e.preventDefault();
        var category = $(this).attr('href');

        $.ajax({
            url: multilingual_shop.ajax_url,
            type: 'POST',
            data: {
                action: 'filter_by_category',
                category: category,
                nonce: multilingual_shop.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('.products-grid').html(response.data.html);
                }
            }
        });
    });

    // Product Variation Selection
    if ($('.product-variations select').length) {
        $('.product-variations select').change(function() {
            var selectedAttributes = {};
            $('.product-variations select').each(function() {
                selectedAttributes[$(this).attr('name')] = $(this).val();
            });

            $.ajax({
                url: multilingual_shop.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_variation_data',
                    attributes: selectedAttributes,
                    nonce: multilingual_shop.nonce
                },
                success: function(response) {
                    if (response.success) {
                        updateVariationData(response.data);
                    }
                }
            });
        });
    }

    function updateVariationData(data) {
        if (data.price) {
            $('.product-price').html(data.price);
        }
        if (data.stock_status) {
            $('.stock').html(data.stock_status);
        }
        if (data.add_to_cart_html) {
            $('.product-add-to-cart form').html(data.add_to_cart_html);
        }
    }

    // Mobile Menu Toggle
    $('.menu-toggle').click(function() {
        $('.nav-menu').toggleClass('active');
    });

    // Smooth Scroll
    $('a[href*="#"]').not('[href="#"]').not('[href="#0"]').click(function(event) {
        if (
            location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') 
            && 
            location.hostname == this.hostname
        ) {
            var target = $(this.hash);
            target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
            if (target.length) {
                event.preventDefault();
                $('html, body').animate({
                    scrollTop: target.offset().top - 100
                }, 1000);
            }
        }
    });
}); 