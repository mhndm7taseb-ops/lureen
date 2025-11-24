<?php

/**
 * Cart final
 */
/**
 * Lureen — Custom WooCommerce Cart (Mobile-First)
 * Beautiful, simple cart page optimized for single screen view
 * 
 * Features:
 * - Product thumbnails with quantity controls
 * - +/- buttons for easy quantity adjustment
 * - Delivery fee notice
 * - Mobile-optimized single column layout
 * - Lureen brand styling
 */

if (!defined('ABSPATH')) { exit; }

/* =========================================================
   HIDE DEFAULT WOOCOMMERCE CART ELEMENTS
========================================================= */
add_action('wp_head', function() {
    if (function_exists('is_cart') && is_cart()) : ?>
<style>
/* Hide default WooCommerce cart table and elements */
.woocommerce-cart-form,
.cart_totals,
.woocommerce table.shop_table,
.woocommerce .cart-collaterals,
.cart-empty,
.return-to-shop {
    display: none !important;
}
</style>
<?php endif;
});

/* =========================================================
   CUSTOM CART STYLES - MOBILE FIRST
========================================================= */
add_action('wp_head', function() {
    if (function_exists('is_cart') && is_cart()) : ?>
<style>
/* Container */
.lureen-cart-wrapper {
    max-width: 500px;
    margin: 16px auto;
    padding: 0 12px;
    direction: rtl;
}

/* Header */
.lureen-cart-header {
    background: linear-gradient(135deg, #9b59b6, #8e44ad);
    color: #fff;
    padding: 24px 20px;
    border-radius: 20px 20px 0 0;
    text-align: center;
}

.lureen-cart-header h1 {
    margin: 0;
    font-size: 24px;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

/* Cart Container */
.lureen-cart-container {
    background: #fff;
    border-radius: 0 0 20px 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,.08);
    overflow: hidden;
}

/* Cart Items */
.lureen-cart-items {
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 16px;
    max-height: 50vh;
    overflow-y: auto;
}

/* Single Cart Item */
.lureen-cart-item {
    display: grid;
    grid-template-columns: 80px 1fr auto;
    gap: 14px;
    padding: 16px;
    background: #fafbff;
    border-radius: 16px;
    border: 2px solid #f0f1f5;
    transition: all .2s;
    align-items: center;
}

.lureen-cart-item:hover {
    border-color: #e0e0f0;
    box-shadow: 0 4px 12px rgba(0,0,0,.04);
}

/* Product Image */
.lureen-item-image {
    width: 80px;
    height: 80px;
    border-radius: 12px;
    overflow: hidden;
    background: #fff;
}

.lureen-item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Product Info */
.lureen-item-info {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.lureen-item-name {
    font-weight: 800;
    font-size: 16px;
    color: #2c3e50;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.lureen-item-price {
    font-size: 18px;
    font-weight: 800;
    color: #9b59b6;
}

/* Quantity Controls */
.lureen-qty-wrapper {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #fff;
    border: 2px solid #e6e6ee;
    border-radius: 12px;
    padding: 4px;
}

.lureen-qty-btn {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    color: #9b59b6;
    font-weight: 800;
    transition: all .2s;
}

.lureen-qty-btn:hover {
    background: #9b59b6;
    color: #fff;
}

.lureen-qty-btn:active {
    transform: scale(0.95);
}

.lureen-qty-display {
    min-width: 36px;
    text-align: center;
    font-weight: 800;
    font-size: 16px;
    color: #2c3e50;
}

/* Remove Button */
.lureen-item-remove {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
}

.lureen-remove-btn {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fff0f0;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-size: 18px;
    color: #e74c3c;
    transition: all .2s;
}

.lureen-remove-btn:hover {
    background: #e74c3c;
    color: #fff;
}

/* Empty Cart */
.lureen-empty-cart {
    text-align: center;
    padding: 60px 20px;
    color: #999;
}

.lureen-empty-cart i {
    font-size: 64px;
    opacity: 0.3;
    margin-bottom: 16px;
}

.lureen-empty-cart p {
    font-size: 18px;
    font-weight: 600;
    margin: 16px 0;
}

.lureen-empty-cart .back-shop-btn {
    display: inline-block;
    margin-top: 16px;
    padding: 14px 32px;
    background: linear-gradient(135deg, #9b59b6, #8e44ad);
    color: #fff;
    text-decoration: none;
    border-radius: 50px;
    font-weight: 800;
    transition: all .2s;
}

.lureen-empty-cart .back-shop-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 28px rgba(155,89,182,.3);
}

/* Cart Summary */
.lureen-cart-summary {
    padding: 20px;
    background: #fcfcff;
    border-top: 2px solid #f0f1f5;
}

.lureen-summary-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    font-size: 15px;
    color: #666;
}

.lureen-summary-row.total {
    padding-top: 16px;
    margin-top: 12px;
    border-top: 2px solid #e0e0e0;
    font-size: 20px;
    font-weight: 800;
    color: #2c3e50;
}

/* Delivery Notice */
.lureen-delivery-notice {
    background: #fff9e6;
    border: 2px dashed #ffc107;
    padding: 14px 16px;
    border-radius: 12px;
    margin: 16px 20px;
    text-align: center;
    line-height: 1.5;
    font-size: 14px;
    color: #856404;
}

.lureen-delivery-notice strong {
    display: block;
    margin-bottom: 4px;
    font-size: 15px;
}

/* Action Buttons */
.lureen-cart-actions {
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.lureen-checkout-btn {
    width: 100%;
    padding: 18px;
    background: linear-gradient(135deg, #9b59b6, #8e44ad);
    color: #fff;
    border: none;
    border-radius: 50px;
    font-size: 17px;
    font-weight: 800;
    cursor: pointer;
    text-align: center;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    box-shadow: 0 12px 28px rgba(155,89,182,.3);
    transition: all .2s;
}

.lureen-checkout-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 16px 36px rgba(155,89,182,.4);
}

.lureen-continue-btn {
    width: 100%;
    padding: 16px;
    background: #f8f9fa;
    color: #666;
    border: 2px solid #e0e0e0;
    border-radius: 50px;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    text-align: center;
    text-decoration: none;
    transition: all .2s;
}

.lureen-continue-btn:hover {
    background: #fff;
    border-color: #9b59b6;
    color: #9b59b6;
}

/* Loading State */
.lureen-cart-item.updating {
    opacity: 0.6;
    pointer-events: none;
}

/* Responsive */
@media (max-width: 400px) {
    .lureen-cart-wrapper {
        margin: 8px auto;
        padding: 0 8px;
    }
    
    .lureen-cart-header {
        padding: 20px 16px;
    }
    
    .lureen-cart-header h1 {
        font-size: 20px;
    }
    
    .lureen-cart-items {
        padding: 16px;
        gap: 12px;
    }
    
    .lureen-cart-item {
        grid-template-columns: 70px 1fr auto;
        gap: 12px;
        padding: 14px;
    }
    
    .lureen-item-image {
        width: 70px;
        height: 70px;
    }
    
    .lureen-item-name {
        font-size: 15px;
    }
    
    .lureen-item-price {
        font-size: 16px;
    }
}

/* Custom Scrollbar */
.lureen-cart-items::-webkit-scrollbar {
    width: 6px;
}

.lureen-cart-items::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.lureen-cart-items::-webkit-scrollbar-thumb {
    background: #9b59b6;
    border-radius: 10px;
}
</style>
<?php endif;
});

/* =========================================================
   CUSTOM CART CONTENT
========================================================= */
add_action('woocommerce_before_cart', function() {
    if (!WC()->cart || WC()->cart->is_empty()) {
        ?>
        <div class="lureen-cart-wrapper">
            <div class="lureen-cart-header">
                <h1>🛒 سلة التسوق</h1>
            </div>
            <div class="lureen-cart-container">
                <div class="lureen-empty-cart">
                    <i class="fas fa-shopping-bag" style="font-size: 64px; opacity: 0.3;"></i>
                    <p>سلة التسوق فارغة</p>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="back-shop-btn">
                        متابعة التسوق
                    </a>
                </div>
            </div>
        </div>
        <?php
        return;
    }

    $cart = WC()->cart;
    ?>
    <div class="lureen-cart-wrapper">
        <div class="lureen-cart-header">
            <h1>🛒 سلة التسوق</h1>
        </div>
        
        <div class="lureen-cart-container">
            <div class="lureen-cart-items">
                <?php
                foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
                    $product = $cart_item['data'];
                    if (!$product || !$product->exists()) continue;
                    
                    $product_id = $cart_item['product_id'];
                    $quantity = $cart_item['quantity'];
                    $product_permalink = $product->get_permalink($cart_item);
                    ?>
                    <div class="lureen-cart-item" data-cart-key="<?php echo esc_attr($cart_item_key); ?>">
                        <div class="lureen-item-image">
                            <?php
                            $thumbnail = $product->get_image('thumbnail');
                            if ($product_permalink) {
                                echo '<a href="' . esc_url($product_permalink) . '">' . $thumbnail . '</a>';
                            } else {
                                echo $thumbnail;
                            }
                            ?>
                        </div>
                        
                        <div class="lureen-item-info">
                            <div class="lureen-item-name">
                                <?php
                                if ($product_permalink) {
                                    echo '<a href="' . esc_url($product_permalink) . '" style="color: inherit; text-decoration: none;">' . esc_html($product->get_name()) . '</a>';
                                } else {
                                    echo esc_html($product->get_name());
                                }
                                ?>
                            </div>
                            <div class="lureen-item-price">
                                <?php echo WC()->cart->get_product_price($product); ?>
                            </div>
                            <div class="lureen-qty-wrapper">
                                <button class="lureen-qty-btn lureen-qty-minus" data-action="minus">−</button>
                                <span class="lureen-qty-display"><?php echo esc_html($quantity); ?></span>
                                <button class="lureen-qty-btn lureen-qty-plus" data-action="plus">+</button>
                            </div>
                        </div>
                        
                        <div class="lureen-item-remove">
                            <button class="lureen-remove-btn" title="حذف المنتج">
                                🗑️
                            </button>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
            
            <div class="lureen-delivery-notice">
                <strong>📍 ملاحظة مهمة</strong>
                الأسعار لا تشمل رسوم التوصيل. سيتم إضافة رسوم التوصيل عند اختيار المنطقة في صفحة الدفع.
            </div>
            
            <div class="lureen-cart-summary">
                <div class="lureen-summary-row total">
                    <span>المجموع الإجمالي</span>
                    <span><?php echo WC()->cart->get_cart_total(); ?></span>
                </div>
            </div>
            
            <div class="lureen-cart-actions">
                <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="lureen-checkout-btn">
                    إتمام الشراء 🎉
                </a>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="lureen-continue-btn">
                    متابعة التسوق
                </a>
            </div>
        </div>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // Update quantity
        $('.lureen-qty-btn').on('click', function(e) {
            e.preventDefault();
            
            var $btn = $(this);
            var $item = $btn.closest('.lureen-cart-item');
            var $display = $item.find('.lureen-qty-display');
            var cartKey = $item.data('cart-key');
            var action = $btn.data('action');
            var currentQty = parseInt($display.text());
            var newQty = action === 'plus' ? currentQty + 1 : Math.max(0, currentQty - 1);
            
            // Add loading state
            $item.addClass('updating');
            
            // Update cart
            $.ajax({
                url: wc_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'update_cart_item_qty'),
                type: 'POST',
                data: {
                    cart_item_key: cartKey,
                    qty: newQty
                },
                success: function(response) {
                    if (newQty === 0) {
                        // Remove item with animation
                        $item.fadeOut(300, function() {
                            $(this).remove();
                            // Reload if no items left
                            if ($('.lureen-cart-item').length === 0) {
                                location.reload();
                            }
                        });
                    } else {
                        // Update display
                        $display.text(newQty);
                        $item.removeClass('updating');
                    }
                    
                    // Update totals
                    updateCartTotals();
                },
                error: function() {
                    $item.removeClass('updating');
                    alert('حدث خطأ في تحديث الكمية. يرجى المحاولة مرة أخرى.');
                }
            });
        });
        
        // Remove item
        $('.lureen-remove-btn').on('click', function(e) {
            e.preventDefault();
            
            if (!confirm('هل أنت متأكد من حذف هذا المنتج؟')) {
                return;
            }
            
            var $item = $(this).closest('.lureen-cart-item');
            var cartKey = $item.data('cart-key');
            
            $item.addClass('updating');
            
            $.ajax({
                url: wc_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'remove_cart_item'),
                type: 'POST',
                data: {
                    cart_item_key: cartKey
                },
                success: function(response) {
                    $item.fadeOut(300, function() {
                        $(this).remove();
                        // Reload if no items left
                        if ($('.lureen-cart-item').length === 0) {
                            location.reload();
                        }
                    });
                    updateCartTotals();
                },
                error: function() {
                    $item.removeClass('updating');
                    alert('حدث خطأ في حذف المنتج. يرجى المحاولة مرة أخرى.');
                }
            });
        });
        
        // Update cart totals via AJAX
        function updateCartTotals() {
            $.ajax({
                url: wc_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'get_cart_totals'),
                type: 'POST',
                success: function(response) {
                    if (response && response.fragments) {
                        // Update fragments if available
                        $.each(response.fragments, function(key, value) {
                            $(key).replaceWith(value);
                        });
                    }
                    // Force page reload to update totals properly
                    setTimeout(function() {
                        location.reload();
                    }, 500);
                }
            });
        }
    });
    </script>
    <?php
}, 5);

/* =========================================================
   AJAX HANDLERS FOR CART UPDATES
========================================================= */

// Update cart item quantity
add_action('wc_ajax_update_cart_item_qty', function() {
    check_ajax_referer('wc_ajax', 'security', false);
    
    $cart_item_key = isset($_POST['cart_item_key']) ? sanitize_text_field($_POST['cart_item_key']) : '';
    $qty = isset($_POST['qty']) ? absint($_POST['qty']) : 1;
    
    if ($cart_item_key && WC()->cart) {
        if ($qty === 0) {
            WC()->cart->remove_cart_item($cart_item_key);
        } else {
            WC()->cart->set_quantity($cart_item_key, $qty, true);
        }
        WC()->cart->calculate_totals();
        wp_send_json_success();
    }
    
    wp_send_json_error();
});

// Remove cart item
add_action('wc_ajax_remove_cart_item', function() {
    check_ajax_referer('wc_ajax', 'security', false);
    
    $cart_item_key = isset($_POST['cart_item_key']) ? sanitize_text_field($_POST['cart_item_key']) : '';
    
    if ($cart_item_key && WC()->cart) {
        WC()->cart->remove_cart_item($cart_item_key);
        WC()->cart->calculate_totals();
        wp_send_json_success();
    }
    
    wp_send_json_error();
});

// Get updated cart totals
add_action('wc_ajax_get_cart_totals', function() {
    check_ajax_referer('wc_ajax', 'security', false);
    
    WC()->cart->calculate_totals();
    
    wp_send_json_success([
        'subtotal' => WC()->cart->get_cart_subtotal(),
        'total' => WC()->cart->get_cart_total(),
    ]);
});
