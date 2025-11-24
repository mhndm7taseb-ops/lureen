<?php

/**
 * Check out final
 */
/**
 * Lureen — Custom WooCommerce Checkout (Mobile-First)
 * Ultra-simple checkout optimized for single mobile screen
 * 
 * Features:
 * - Minimal fields: Name, Phone, Region, Address
 * - Auto delivery fee by region
 * - Beautiful Arabic thank-you page
 * - Mobile-optimized (single column, large touch targets)
 */

if (!defined('ABSPATH')) { exit; }

/* =========================================================
   MOBILE-FIRST STYLES - Single Column, Large Touch Targets
========================================================= */
add_action('wp_head', function () {
    if (function_exists('is_checkout') && is_checkout()) : ?>
<style>
/* Container */
.lureen-checkout-card {
    max-width: 500px;
    margin: 16px auto;
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,.08);
    overflow: hidden;
}

/* Header */
.lureen-checkout-head {
    padding: 24px 20px;
    background: linear-gradient(135deg, #9b59b6, #8e44ad);
    color: #fff;
    font-weight: 800;
    font-size: 22px;
    text-align: center;
    letter-spacing: 0.3px;
}

/* Body - Single Column */
.lureen-checkout-body {
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 16px;
    direction: rtl;
}

/* Form Fields */
.lureen-field {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.lureen-field label {
    font-weight: 700;
    font-size: 15px;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 4px;
}

.lureen-field label .required {
    color: #e74c3c;
    font-size: 16px;
}

.lureen-field input,
.lureen-field select,
.lureen-field textarea {
    border: 2px solid #e6e6ee;
    border-radius: 14px;
    padding: 16px;
    font-size: 16px;
    background: #fafbff;
    outline: none;
    transition: all .2s;
    width: 100%;
    -webkit-appearance: none;
    font-family: 'Tajawal', sans-serif;
}

.lureen-field input:focus,
.lureen-field select:focus,
.lureen-field textarea:focus {
    border-color: #9b59b6;
    box-shadow: 0 0 0 4px rgba(155,89,182,.12);
    background: #fff;
}

/* Region Select - Special Styling */
.lureen-field select {
    background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="%239b59b6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"%3e%3cpolyline points="6 9 12 15 18 9"%3e%3c/polyline%3e%3c/svg%3e');
    background-repeat: no-repeat;
    background-position: left 12px center;
    background-size: 20px;
    padding-left: 40px;
    cursor: pointer;
    font-weight: 600;
}

/* Region Note */
.lureen-region-note {
    font-size: 13px;
    color: #7f8c8d;
    background: #f8f9fa;
    padding: 10px 12px;
    border-radius: 10px;
    margin-top: -4px;
    line-height: 1.5;
}

/* Order Review */
.woocommerce-checkout-review-order {
    border: 2px solid #f0f1f5;
    background: #fcfcff;
    border-radius: 16px;
    padding: 16px;
    margin-top: 8px;
}

.woocommerce-checkout-review-order table {
    width: 100%;
    border-collapse: collapse;
}

.woocommerce-checkout-review-order th,
.woocommerce-checkout-review-order td {
    padding: 12px 0;
    text-align: right;
    border-bottom: 1px solid #f5f5f5;
}

.woocommerce-checkout-review-order tfoot th,
.woocommerce-checkout-review-order tfoot td {
    font-weight: 800;
    font-size: 16px;
    padding-top: 16px;
    border-bottom: none;
}

/* Submit Button */
.lureen-submit {
    padding: 8px 0;
    display: flex;
    justify-content: center;
}

.lureen-submit .button.alt,
.lureen-submit button[type=submit] {
    background: linear-gradient(135deg, #9b59b6, #8e44ad) !important;
    border: none !important;
    color: #fff !important;
    font-weight: 800;
    border-radius: 50px !important;
    padding: 18px 48px !important;
    font-size: 17px !important;
    width: 100%;
    max-width: 100%;
    transition: all .2s;
    box-shadow: 0 12px 28px rgba(155,89,182,.3);
    cursor: pointer;
}

.lureen-submit .button.alt:hover {
    transform: translateY(-2px);
    box-shadow: 0 16px 36px rgba(155,89,182,.4);
}

/* Hide WooCommerce default elements */
.woocommerce-checkout .woocommerce-billing-fields h3,
.woocommerce-checkout .woocommerce-additional-fields h3,
#order_review_heading {
    display: none;
}

/* Responsive refinements */
@media (max-width: 640px) {
    .lureen-checkout-card {
        margin: 8px;
        border-radius: 16px;
    }
    
    .lureen-checkout-head {
        padding: 20px 16px;
        font-size: 20px;
    }
    
    .lureen-checkout-body {
        padding: 16px;
        gap: 14px;
    }
    
    .lureen-field input,
    .lureen-field select,
    .lureen-field textarea {
        padding: 15px;
        font-size: 16px;
    }
    
    .lureen-submit .button.alt,
    .lureen-submit button[type=submit] {
        padding: 16px 40px !important;
        font-size: 16px !important;
    }
}

@media (max-width: 400px) {
    .lureen-checkout-card {
        margin: 4px;
    }
    
    .lureen-checkout-body {
        padding: 12px;
    }
}
</style>
<?php endif;
});

/* =========================================================
   CHECKOUT FIELDS - Minimal Required Set
========================================================= */
add_filter('woocommerce_checkout_fields', function ($fields) {
    // Remove all unnecessary fields
    $remove = [
        'billing_last_name', 'billing_company', 'billing_address_2',
        'billing_country', 'billing_state', 'billing_city', 
        'billing_postcode', 'billing_email'
    ];
    foreach ($remove as $key) {
        if (isset($fields['billing'][$key])) {
            unset($fields['billing'][$key]);
        }
    }

    // الاسم (Full Name)
    $fields['billing']['billing_first_name'] = [
        'label'       => 'الاسم',
        'placeholder' => 'اكتب اسمك الكامل',
        'required'    => true,
        'priority'    => 10,
        'class'       => ['lureen-field'],
        'input_class' => ['lureen-input'],
    ];

    // رقم الهاتف (Phone)
    $fields['billing']['billing_phone'] = [
        'label'       => 'رقم الهاتف',
        'placeholder' => '05XXXXXXXX',
        'required'    => true,
        'priority'    => 20,
        'class'       => ['lureen-field'],
        'input_class' => ['lureen-input'],
        'validate'    => ['phone'],
    ];

    // المنطقة (Region with delivery fees)
    $fields['billing']['lureen_region'] = [
        'type'        => 'select',
        'label'       => 'المنطقة',
        'required'    => true,
        'priority'    => 30,
        'class'       => ['lureen-field'],
        'options'     => [
            ''          => 'اختر المنطقة',
            'westbank'  => 'الضفة - 15 شيكل',
            'jerusalem' => 'القدس - 30 شيكل',
            'inside48'  => 'الداخل - 70 شيكل',
        ],
    ];

    // العنوان (Address)
    $fields['billing']['billing_address_1'] = [
        'label'       => 'العنوان',
        'placeholder' => 'المدينة / الحي / أقرب معلم',
        'required'    => true,
        'priority'    => 40,
        'class'       => ['lureen-field'],
        'input_class' => ['lureen-input'],
    ];

    return $fields;
});

/* Add helpful note below region field */
add_filter('woocommerce_form_field_args', function($args, $key) {
    if ($key === 'lureen_region') {
        $args['description'] = '<div class="lureen-region-note">💡 سيتم إضافة تكلفة التوصيل حسب المنطقة تلقائياً</div>';
    }
    return $args;
}, 10, 2);

/* =========================================================
   DYNAMIC DELIVERY FEE BY REGION
========================================================= */

/* Store region in session for live updates */
add_action('woocommerce_checkout_update_order_review', function($post_data) {
    parse_str($post_data, $parsed);
    if (isset($parsed['lureen_region'])) {
        WC()->session->set('lureen_region', sanitize_text_field($parsed['lureen_region']));
    }
});

/* Add delivery fee to cart */
add_action('woocommerce_cart_calculate_fees', function($cart) {
    if (is_admin() && !defined('DOING_AJAX')) return;

    $region = WC()->session ? WC()->session->get('lureen_region') : '';
    $fee = 0;
    $label = 'التوصيل';

    switch ($region) {
        case 'westbank':
            $fee = 15;
            $label = 'التوصيل - الضفة';
            break;
        case 'jerusalem':
            $fee = 30;
            $label = 'التوصيل - القدس';
            break;
        case 'inside48':
            $fee = 70;
            $label = 'التوصيل - الداخل';
            break;
    }

    if ($fee > 0) {
        $cart->add_fee($label, $fee, true);
    }
}, 20);

/* Save region to order meta */
add_action('woocommerce_checkout_create_order', function($order, $data) {
    $region_raw = isset($data['lureen_region']) ? $data['lureen_region'] : WC()->session->get('lureen_region');
    $region_raw = sanitize_text_field($region_raw);
    
    if ($region_raw) {
        $map = [
            'westbank'  => 'الضفة (15 شيكل)',
            'jerusalem' => 'القدس (30 شيكل)',
            'inside48'  => 'الداخل (70 شيكل)'
        ];
        $order->update_meta_data('_lureen_region', isset($map[$region_raw]) ? $map[$region_raw] : $region_raw);
    }
}, 10, 2);

/* Auto-refresh totals when region changes */
add_action('wp_footer', function() {
    if (function_exists('is_checkout') && is_checkout() && !is_order_received_page()) : ?>
<script>
document.addEventListener('change', function(e) {
    if (e.target && e.target.name === 'lureen_region') {
        if (window.jQuery) {
            jQuery('body').trigger('update_checkout');
        }
    }
}, true);
</script>
<?php endif;
});

/* =========================================================
   CUSTOM THANK-YOU PAGE (Mobile Optimized)
========================================================= */

/* Generate custom thank-you URL */
function lureen_get_thanks_url($order) {
    if (!$order) return home_url('/');
    return add_query_arg([
        'lureen_thanks' => 1,
        'order_id'      => $order->get_id(),
        'key'           => $order->get_order_key(),
    ], home_url('/'));
}

/* Redirect to custom thank-you */
add_filter('woocommerce_get_checkout_order_received_url', function($url, $order) {
    if ($order instanceof WC_Order) {
        return lureen_get_thanks_url($order);
    }
    return $url;
}, 10, 2);

/* Render thank-you page */
add_action('template_redirect', function () {
    if (!isset($_GET['lureen_thanks'])) return;

    $order_id = isset($_GET['order_id']) ? absint($_GET['order_id']) : 0;
    $key      = isset($_GET['key']) ? sanitize_text_field($_GET['key']) : '';
    $order    = $order_id ? wc_get_order($order_id) : false;

    status_header(200);
    nocache_headers();

    // Invalid order
    if (!$order || $key !== $order->get_order_key()) {
        ?>
        <!DOCTYPE html>
        <html dir="rtl" lang="ar">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>شكراً لك</title>
            <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;800&display=swap" rel="stylesheet">
            <style>
                body { font-family: 'Tajawal', sans-serif; background: #fafafa; margin: 0; padding: 20px; }
                .error-card { max-width: 500px; margin: 40px auto; background: #fff; border-radius: 20px; padding: 32px; text-align: center; box-shadow: 0 10px 40px rgba(0,0,0,.08); }
                h2 { font-size: 24px; color: #e74c3c; margin-bottom: 16px; }
                p { color: #666; line-height: 1.6; margin-bottom: 24px; }
                .btn { display: inline-block; padding: 14px 32px; background: linear-gradient(135deg, #9b59b6, #8e44ad); color: #fff; text-decoration: none; border-radius: 50px; font-weight: 800; }
            </style>
        </head>
        <body>
            <div class="error-card">
                <h2>⚠️ لم نتمكن من عرض تفاصيل الطلب</h2>
                <p>تم إتمام عملية الشراء، ولكن حدث خطأ في عرض التفاصيل. لا تقلق، طلبك مسجل لدينا.</p>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="btn">العودة للمتجر</a>
            </div>
        </body>
        </html>
        <?php
        exit;
    }

    // Valid order - show thank you
    $name    = $order->get_billing_first_name();
    $phone   = $order->get_billing_phone();
    $address = $order->get_billing_address_1();
    $region  = $order->get_meta('_lureen_region');
    ?>
    <!DOCTYPE html>
    <html dir="rtl" lang="ar">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>تم استلام طلبك 🎉</title>
        <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;800&display=swap" rel="stylesheet">
        <style>
            body {
                font-family: 'Tajawal', sans-serif;
                background: #fafafa;
                margin: 0;
                padding: 12px;
                direction: rtl;
            }
            .thanks-card {
                max-width: 500px;
                margin: 0 auto;
                background: #fff;
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 10px 40px rgba(0,0,0,.08);
            }
            .thanks-header {
                padding: 32px 20px;
                background: linear-gradient(135deg, #2ecc71, #27ae60);
                color: #fff;
                text-align: center;
            }
            .thanks-header h1 {
                margin: 0 0 8px 0;
                font-size: 26px;
                font-weight: 800;
            }
            .thanks-header p {
                margin: 0;
                font-size: 15px;
                opacity: 0.95;
            }
            .thanks-body {
                padding: 24px 20px;
            }
            .info-grid {
                display: grid;
                gap: 16px;
                margin-bottom: 24px;
            }
            .info-item {
                background: #f8f9fa;
                padding: 14px;
                border-radius: 12px;
            }
            .info-label {
                font-size: 13px;
                color: #666;
                margin-bottom: 4px;
                font-weight: 600;
            }
            .info-value {
                font-size: 16px;
                color: #2c3e50;
                font-weight: 800;
            }
            .order-summary {
                background: #fcfcff;
                border: 2px solid #f0f1f5;
                border-radius: 16px;
                padding: 16px;
                margin-bottom: 20px;
            }
            .order-summary h3 {
                margin: 0 0 12px 0;
                font-size: 18px;
                color: #2c3e50;
            }
            .order-item {
                display: flex;
                justify-content: space-between;
                padding: 10px 0;
                border-bottom: 1px solid #f5f5f5;
            }
            .order-item:last-child {
                border-bottom: none;
            }
            .order-total {
                display: flex;
                justify-content: space-between;
                padding-top: 12px;
                margin-top: 12px;
                border-top: 2px solid #e0e0e0;
                font-weight: 800;
                font-size: 18px;
            }
            .success-message {
                background: #e8f5e9;
                border: 2px dashed #4caf50;
                padding: 16px;
                border-radius: 14px;
                text-align: center;
                margin-bottom: 20px;
                line-height: 1.6;
            }
            .back-btn {
                display: block;
                width: 100%;
                padding: 16px;
                background: linear-gradient(135deg, #9b59b6, #8e44ad);
                color: #fff;
                text-align: center;
                text-decoration: none;
                border-radius: 50px;
                font-weight: 800;
                font-size: 16px;
                box-shadow: 0 10px 25px rgba(155,89,182,.3);
                transition: transform .2s;
            }
            .back-btn:hover {
                transform: translateY(-2px);
            }
            @media (max-width: 400px) {
                body { padding: 8px; }
                .thanks-header h1 { font-size: 22px; }
                .thanks-body { padding: 20px 16px; }
            }
        </style>
    </head>
    <body>
        <div class="thanks-card">
            <div class="thanks-header">
                <h1>🎉 تم استلام طلبك بنجاح</h1>
                <p>رقم الطلب: <?php echo esc_html($order->get_order_number()); ?></p>
            </div>
            
            <div class="thanks-body">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">👤 الاسم</div>
                        <div class="info-value"><?php echo esc_html($name); ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">📱 رقم الهاتف</div>
                        <div class="info-value"><?php echo esc_html($phone); ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">📍 المنطقة</div>
                        <div class="info-value"><?php echo esc_html($region); ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">🏠 العنوان</div>
                        <div class="info-value"><?php echo esc_html($address); ?></div>
                    </div>
                </div>

                <div class="order-summary">
                    <h3>📦 ملخص الطلب</h3>
                    <?php foreach ($order->get_items() as $item) : ?>
                        <div class="order-item">
                            <span><?php echo esc_html($item->get_name()); ?> × <?php echo esc_html($item->get_quantity()); ?></span>
                            <span><?php echo wp_kses_post($order->get_formatted_line_subtotal($item)); ?></span>
                        </div>
                    <?php endforeach; ?>
                    
                    <div class="order-item" style="color: #666; font-size: 15px;">
                        <span>المجموع الفرعي</span>
                        <span><?php echo wc_price($order->get_subtotal()); ?></span>
                    </div>
                    
                    <?php foreach ($order->get_fees() as $fee) : ?>
                        <div class="order-item" style="color: #9b59b6; font-weight: 700;">
                            <span>🚚 <?php echo esc_html($fee->get_name()); ?></span>
                            <span><?php echo wc_price($fee->get_total()); ?></span>
                        </div>
                    <?php endforeach; ?>
                    
                    <div class="order-total">
                        <span>المجموع الكلي</span>
                        <span><?php echo wp_kses_post($order->get_formatted_order_total()); ?></span>
                    </div>
                </div>

                <div class="success-message">
                    <strong>✅ شكراً لك!</strong><br>
                    تم استلام طلبك وسنتواصل معك قريباً لتأكيد التفاصيل وموعد التسليم.
                </div>

                <a href="<?php echo esc_url(home_url('/')); ?>" class="back-btn">
                    العودة للتسوق 🛍️
                </a>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
});

/* =========================================================
   SHOW REGION IN ADMIN ORDER PANEL
========================================================= */
add_action('woocommerce_admin_order_data_after_billing_address', function($order) {
    $region = $order->get_meta('_lureen_region');
    if ($region) {
        echo '<p><strong>المنطقة:</strong> ' . esc_html($region) . '</p>';
    }
});
