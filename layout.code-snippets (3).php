<?php

/**
 * layout
 */
/**
 * Custom layout 
 */
/**
 * Lureen Online Store – Production Ready Version (Mobile Optimized)
 * 
 * Improvements:
 * - Fixed Arabic category slug handling
 * - Fixed store page redirect loop
 * - Reduced margins for tighter layout
 * - Fixed category card sizing on desktop
 * - Uses native WooCommerce product loops
 * 
 * Installation: Add to Code Snippets Pro or functions.php
 * Requirements: WooCommerce
 */

if (!defined('ABSPATH')) { exit; }

define('LUREEN_NS', 'lureen_');
define('LUREEN_NONCE_ACTION', 'lureen_nonce');
define('LUREEN_PRODUCTS_PER_PAGE', 12);

/** Helper to generate AJAX nonce */
function lureen_nonce() { return wp_create_nonce(LUREEN_NONCE_ACTION); }

/** Helpers to get/set custom page IDs option */
function lureen_get_pages_option() {
    $opt = get_option('lureen_custom_pages');
    return is_array($opt) ? $opt : array();
}
function lureen_set_pages_option($ids) { update_option('lureen_custom_pages', $ids); }

/* -----------------------------------------------------------
   0) BODY CLASS
----------------------------------------------------------- */
add_filter('body_class', function($classes){
    $classes[] = 'lureen-layout';
    return $classes;
});

/* -----------------------------------------------------------
   1) HIDE DEFAULT THEME HEADERS/FOOTERS/TITLES/BREADCRUMBS
----------------------------------------------------------- */
add_action('wp_head', function() { ?>
<style>
.site-header, .site-branding, .site-title, .site-description,
.main-navigation, nav.primary-navigation, header.header, .top-header,
.custom-logo-link, #masthead, #site-navigation,
.site-footer, footer, #colophon,
.sidebar, .widget-area, #secondary { display: none !important; }
.main-navigation ul, nav.primary-navigation ul, #site-navigation ul { display: none !important; }
.woocommerce-breadcrumb { display: none !important; }
.woocommerce-products-header,
.woocommerce-products-header *,
.entry-title, .page-title, .wp-block-post-title,
header.wp-block-template-part,
footer.wp-block-template-part { display: none !important; }

body.post-type-archive-product h1,
body.tax-product_cat h1,
body.tax-product_tag h1,
body.post-type-archive-product .page-title,
body.tax-product_cat .page-title,
body.tax-product_tag .page-title,
body.post-type-archive-product .wp-block-query-title,
body.tax-product_cat .wp-block-query-title,
body.tax-product_tag .wp-block-query-title,
body.post-type-archive-product .wp-block-archive-title,
body.tax-product_cat .wp-block-archive-title,
body.tax-product_tag .wp-block-archive-title,
body.tax-product_cat .archive-title,
body.tax-product_tag .archive-title { display: none !important; }

body.post-type-archive-product ul.products,
body.tax-product_cat ul.products,
body.tax-product_tag ul.products,
body.post-type-archive-product .woocommerce-pagination,
body.tax-product_cat .woocommerce-pagination,
body.tax-product_tag .woocommerce-pagination,
body.post-type-archive-product .wc-block-grid,
body.tax-product_cat .wc-block-grid,
body.tax-product_tag .wc-block-grid,
body.post-type-archive-product .wc-block-grid__products,
body.tax-product_cat .wc-block-grid__products,
body.tax-product_tag .wc-block-grid__products,
body.post-type-archive-product .wp-block-woocommerce-product-template,
body.tax-product_cat .wp-block-woocommerce-product-template,
body.tax-product_tag .wp-block-woocommerce-product-template,
body.post-type-archive-product .wp-block-query .wp-block-post-template,
body.tax-product_cat .wp-block-query .wp-block-post-template,
body.tax-product_tag .wp-block-query .wp-block-post-template { display: none !important; }

.storefront-sorting,
.woocommerce-result-count,
.woocommerce-ordering,
.woocommerce-notices,
.woocommerce-notices-wrapper { display: none !important; }
</style>
<?php
}, 10);

add_filter('wp_nav_menu_items', function($items, $args) { return ''; }, 10, 2);

/* -----------------------------------------------------------
2) ENQUEUE SCRIPTS & STYLES
----------------------------------------------------------- */
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('wc-cart-fragments');
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css', array(), '6.5.2');
    wp_enqueue_style('tajawal-font', 'https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap', array(), null);

    wp_register_script('lureen-inline', false, array('jquery','wc-cart-fragments'), null, true);
    wp_enqueue_script('lureen-inline');
    wp_add_inline_script('lureen-inline', sprintf(
        'window.lureenAjax = { ajax_url:%s, wc_ajax_url:%s, nonce:%s };',
        json_encode(admin_url('admin-ajax.php')),
        json_encode(\WC_AJAX::get_endpoint('%%endpoint%%')),
        json_encode(lureen_nonce())
    ));
}, 5);

/* -----------------------------------------------------------
3) CUSTOM DOCUMENT TITLE
----------------------------------------------------------- */
add_filter('pre_get_document_title', function($title) {
    if (is_front_page()) { return 'لورين اونلاين - متجرك سيدتي'; }
    return $title;
}, 50);

/* -----------------------------------------------------------
4) CUSTOM NAVIGATION BAR
----------------------------------------------------------- */
function lureen_output_nav() {
    $pages = lureen_get_pages_option();
    $store_url      = !empty($pages['store']) ? get_permalink($pages['store']) : home_url('/shop');
    $categories_url = !empty($pages['categories']) ? get_permalink($pages['categories']) : home_url('/#categories');
    $account_url    = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : home_url('/my-account'); ?>
    <nav class="lureen-custom-nav">
        <div class="lureen-nav-container">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="lureen-custom-logo">لورا سيلفر </a>
            <button class="lureen-mobile-toggle" onclick="this.nextElementSibling.classList.toggle('active')"><i class="fas fa-bars"></i></button>
            <ul class="lureen-nav-menu">
                <li><a href="<?php echo esc_url(home_url('/')); ?>" <?php if(is_front_page()) echo 'class="active"'; ?>>الرئيسية</a></li>
                <li><a href="<?php echo esc_url($categories_url); ?>" <?php if((!empty($pages['categories']) && is_page($pages['categories'])) || is_product_category()) echo 'class="active"'; ?>>التصنيفات</a></li>
                <li><a href="<?php echo esc_url($store_url); ?>" <?php if((!empty($pages['store']) && is_page($pages['store']))) echo 'class="active"'; ?>>جميع المنتجات</a></li>
                <li><a href="<?php echo esc_url($account_url); ?>" <?php if(is_account_page()) echo 'class="active"'; ?>>حسابي</a></li>
            </ul>
        </div>
    </nav>
<?php }
add_action('wp_body_open', 'lureen_output_nav', 5);
add_action('wp_footer', function(){ if (! did_action('wp_body_open')) { lureen_output_nav(); } }, 1);

/* -----------------------------------------------------------
5) PRODUCT CARD RENDERER (UNIFIED STRUCTURE)
----------------------------------------------------------- */
function lureen_is_new_product($product, $days = 30) {
    if (! $product instanceof WC_Product) { return false; }
    $created = $product->get_date_created();
    if (!$created) { return false; }
    return ( time() - $created->getTimestamp() ) < ($days * DAY_IN_SECONDS);
}

function lureen_render_product_card($product) {
    if (! $product instanceof WC_Product) { return ''; }
    $is_new = lureen_is_new_product($product);
    ob_start(); ?>
    <div class="lureen-product-card" data-product-id="<?php echo esc_attr($product->get_id()); ?>">
        <?php if ($is_new): ?>
            <div class="lureen-product-badge new">جديد</div>
        <?php elseif ($product->is_on_sale()): ?>
            <div class="lureen-product-badge sale">تخفيض</div>
        <?php endif; ?>
        <a href="<?php echo esc_url($product->get_permalink()); ?>" class="lureen-product-image-wrapper">
            <?php echo $product->get_image('woocommerce_thumbnail', array(
                'class'   => 'lureen-product-image',
                'loading' => 'lazy',
                'alt'     => esc_attr($product->get_name())
            )); ?>
        </a>
        <div class="lureen-product-info">
            <div class="lureen-product-name">
                <a href="<?php echo esc_url($product->get_permalink()); ?>"><?php echo esc_html($product->get_name()); ?></a>
            </div>
            <?php if ($product->is_on_sale()): ?>
                <div class="lureen-product-price">
                    <span class="lureen-price-sale"><?php echo wc_price($product->get_sale_price()); ?></span>
                    <span class="lureen-price-regular"><?php echo wc_price($product->get_regular_price()); ?></span>
                </div>
            <?php else: ?>
                <div class="lureen-product-price">
                    <span class="lureen-price-normal"><?php echo wc_price($product->get_price()); ?></span>
                </div>
            <?php endif; ?>
            <?php if ($product->is_type(array('variable','grouped'))): ?>
                <a class="lureen-add-to-cart-btn" href="<?php echo esc_url($product->get_permalink()); ?>">
                    <i class="fas fa-eye"></i> شوفي الخيارات
                </a>
            <?php else: ?>
                <button class="lureen-add-to-cart-btn" data-product_id="<?php echo esc_attr($product->get_id()); ?>">
                    <i class="fas fa-shopping-cart"></i> أضف للسلة
                </button>
            <?php endif; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/* -----------------------------------------------------------
6) AJAX CART UI
----------------------------------------------------------- */
function lureen_render_cart_items_html() {
    if (!function_exists('WC') || WC()->cart->is_empty()) {
        return '<div class="lureen-empty-cart"><i class="fas fa-shopping-bag"></i><p>السلة فارغة</p></div>';
    }
    ob_start();
    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        $product = $cart_item['data'];
        if (!$product || !$product->exists()) continue;
        $thumb = $product->get_image('thumbnail', array('loading' => 'lazy'));
        $name  = $product->get_name();
        $price = wc_price($product->get_price());
        $qty   = (int) $cart_item['quantity']; ?>
        <div class="lureen-cart-item" data-key="<?php echo esc_attr($cart_item_key); ?>">
            <?php echo $thumb; ?>
            <div class="lureen-cart-item-info">
                <div class="lureen-cart-item-name"><?php echo esc_html($name); ?></div>
                <div class="lureen-cart-item-price"><?php echo $price; ?> × <span class="lureen-ci-qty"><?php echo $qty; ?></span></div>
                <div class="lureen-qty-controls">
                    <button class="lureen-qty minus" aria-label="طرح"><i class="fas fa-minus"></i></button>
                    <input class="lureen-qty-input" type="number" min="0" step="1" value="<?php echo esc_attr($qty); ?>" />
                    <button class="lureen-qty plus" aria-label="جمع"><i class="fas fa-plus"></i></button>
                </div>
            </div>
            <button class="lureen-remove-item" title="حذف"><i class="fas fa-trash"></i></button>
        </div>
    <?php }
    return ob_get_clean();
}

function lureen_render_cart_modal_inner() {
    $items_html = lureen_render_cart_items_html();
    $total_html = function_exists('WC') ? WC()->cart->get_cart_total() : wc_price(0);
    ob_start(); ?>
    <div class="lureen-cart-content">
        <div class="lureen-cart-header">
            <h2>سلة التسوق</h2>
            <button class="lureen-close-btn" onclick="document.querySelector('.lureen-cart-modal').classList.remove('active')">×</button>
        </div>
        <div class="lureen-cart-items"><?php echo $items_html; ?></div>
        <div class="lureen-cart-footer">
            <div class="lureen-cart-total">
                <span>الإجمالي:</span>
                <span class="lureen-cart-total-amount"><?php echo $total_html; ?></span>
            </div>
            <?php
            $pages = lureen_get_pages_option();
            $cart_url     = !empty($pages['cart'])     ? get_permalink($pages['cart'])     : (function_exists('wc_get_cart_url') ? wc_get_cart_url() : '#');
            $checkout_url = !empty($pages['checkout']) ? get_permalink($pages['checkout']) : (function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : '#'); ?>
            <div class="lureen-cart-actions">
                <a href="<?php echo esc_url($cart_url); ?>" class="lureen-view-cart">عرض السلة</a>
                <a href="<?php echo esc_url($checkout_url); ?>" class="lureen-checkout-btn">إتمام الشراء</a>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

add_action('wp_footer', function() {
    if (!function_exists('WC')) return;
    $count = WC()->cart->get_cart_contents_count(); ?>
    <div class="lureen-sticky-cart" title="عرض السلة">
        <i class="fas fa-shopping-bag lureen-cart-icon"></i>
        <div class="lureen-cart-count"><span class="cart-count-number"><?php echo (int)$count; ?></span></div>
    </div>
    <div class="lureen-cart-modal" onclick="if(event.target.classList.contains('lureen-cart-modal')) this.classList.remove('active')">
        <?php echo lureen_render_cart_modal_inner(); ?>
    </div>
    <script>
    (function($){
        function wcAjax(ep){ return window.lureenAjax.wc_ajax_url.replace('%%endpoint%%', ep); }
        function refreshFragments(cb){
            var wasOpen = $('.lureen-cart-modal').hasClass('active');
            $.post(wcAjax('get_refreshed_fragments'),{},function(resp){
                if(resp && resp.fragments){
                    $.each(resp.fragments,function(sel,html){ $(sel).replaceWith(html); });
                    if(wasOpen) $('.lureen-cart-modal').addClass('active');
                    if(typeof cb==='function') cb();
                }
            },'json');
        }
        function openModal(){ $('.lureen-cart-modal').addClass('active'); }
        function closeModal(){ $('.lureen-cart-modal').removeClass('active'); }

        $(document).on('click','.lureen-sticky-cart',function(){ openModal(); });

        $(document).on('click','.lureen-add-to-cart-btn',function(e){
            var $btn=$(this), pid=$btn.data('product_id');
            if(!pid) return;
            if($btn.is('button')){
                e.preventDefault();
                $btn.addClass('loading').prop('disabled',true).html('<i class="fas fa-spinner fa-spin"></i> جاري الإضافة...');
                $.ajax({
                    url: window.lureenAjax.ajax_url, type:'POST', dataType:'json',
                    data:{ action:'lureen_add_to_cart', product_id:pid, nonce:window.lureenAjax.nonce },
                    success:function(){ $btn.removeClass('loading').addClass('added').html('<i class="fas fa-check"></i> تمت الإضافة'); refreshFragments(function(){ openModal(); }); },
                    error:function(){ $btn.removeClass('loading').prop('disabled',false).html('<i class="fas fa-shopping-cart"></i> أضف للسلة'); alert('حدث خطأ في الاتصال، حاول مرة أخرى'); }
                });
            }
        });

        $(document).on('click','.lureen-remove-item',function(e){
            e.preventDefault();
            var key=$(this).closest('.lureen-cart-item').data('key');
            if(!key) return;
            $(this).prop('disabled',true).addClass('loading');
            $.post(window.lureenAjax.ajax_url,{ action:'lureen_remove_from_cart', key:key, nonce:window.lureenAjax.nonce },function(){ refreshFragments(); },'json');
        });

        $(document).on('click','.lureen-qty',function(){
            var $wrap=$(this).closest('.lureen-cart-item'), $input=$wrap.find('.lureen-qty-input'),
                current=parseInt($input.val(),10)||0;
            if($(this).hasClass('plus')){ $input.val(current+1).trigger('change'); }
            else{ $input.val(Math.max(0,current-1)).trigger('change'); }
        });

        $(document).on('change','.lureen-qty-input',function(){
            var $wrap=$(this).closest('.lureen-cart-item'), key=$wrap.data('key'), qty=parseInt($(this).val(),10)||0;
            $.post(window.lureenAjax.ajax_url,{ action:'lureen_update_cart_item', key:key, qty:qty, nonce:window.lureenAjax.nonce },function(){ refreshFragments(); },'json');
        });

        $(document).on('keyup',function(e){ if(e.key==='Escape') closeModal(); });

        function debounce(fn,wait){ var t; return function(){ clearTimeout(t); t=setTimeout(fn, wait||150); }; }
        function fitCategoryNames(){
            var isMobile = window.matchMedia('(max-width: 768px)').matches;
            var nodes = document.querySelectorAll('.lureen-category-card .lureen-category-name');
            nodes.forEach(function(el){
                el.style.fontSize = '';
                el.style.lineHeight = '';
                el.style.whiteSpace = '';
                el.style.hyphens = '';
                el.style.wordBreak = '';
                el.style.overflow = '';

                if(!isMobile) return;

                var base = 20;
                var minFont = 11;

                el.style.whiteSpace = 'nowrap';
                el.style.hyphens = 'none';
                el.style.wordBreak = 'normal';
                el.style.overflow = 'hidden';
                el.style.lineHeight = '1.2';
                el.style.fontSize = base + 'px';

                var fs = base, guard = 24;
                while (el.scrollWidth > el.clientWidth && fs > minFont && guard--){
                    fs -= 1;
                    el.style.fontSize = fs + 'px';
                }
            });
        }
        document.addEventListener('DOMContentLoaded', fitCategoryNames);
        window.addEventListener('load', fitCategoryNames);
        window.addEventListener('resize', debounce(fitCategoryNames, 200));
    })(jQuery);
    </script>
<?php }, 100);

/* -----------------------------------------------------------
7) AJAX HANDLERS - ADD TO CART
----------------------------------------------------------- */
add_action('wp_ajax_' . LUREEN_NS . 'add_to_cart',        LUREEN_NS . 'add_to_cart_cb');
add_action('wp_ajax_nopriv_' . LUREEN_NS . 'add_to_cart', LUREEN_NS . 'add_to_cart_cb');
function lureen_add_to_cart_cb() {
    check_ajax_referer(LUREEN_NONCE_ACTION, 'nonce');
    if (!function_exists('WC')) { wp_send_json_error(array('message' => 'WooCommerce غير مفعل')); }
    $pid = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
    if (!$pid) { wp_send_json_error(array('message' => 'معرف المنتج غير صحيح')); }
    $product = wc_get_product($pid);
    if (!$product || !$product->exists()) { wp_send_json_error(array('message' => 'لا يمكن شراء هذا المنتج')); }
    if ($product->is_type(array('variable','grouped'))) {
        wp_send_json_error(array('message' => 'يرجى اختيار المقاس/الخيار من صفحة المنتج'));
    }
    if (!$product->is_in_stock()) { wp_send_json_error(array('message' => 'المنتج غير متوفر')); }
    $added = WC()->cart->add_to_cart($pid, 1);
    if ($added) { wp_send_json_success(array('count' => WC()->cart->get_cart_contents_count())); }
    wp_send_json_error(array('message' => 'فشلت الإضافة للسلة'));
}

/* -----------------------------------------------------------
8) AJAX HANDLERS - REMOVE FROM CART
----------------------------------------------------------- */
add_action('wp_ajax_' . LUREEN_NS . 'remove_from_cart',        LUREEN_NS . 'remove_from_cart_cb');
add_action('wp_ajax_nopriv_' . LUREEN_NS . 'remove_from_cart', LUREEN_NS . 'remove_from_cart_cb');
function lureen_remove_from_cart_cb() {
    check_ajax_referer(LUREEN_NONCE_ACTION, 'nonce');
    if (!function_exists('WC')) { wp_send_json_error(); }
    $key = sanitize_text_field($_POST['key'] ?? '');
    if (!$key) { wp_send_json_error(); }
    $removed = WC()->cart->remove_cart_item($key);
    if ($removed) { wp_send_json_success(array('count' => WC()->cart->get_cart_contents_count())); }
    wp_send_json_error();
}

/* -----------------------------------------------------------
9) AJAX HANDLERS - UPDATE CART QUANTITY
----------------------------------------------------------- */
add_action('wp_ajax_' . LUREEN_NS . 'update_cart_item',        LUREEN_NS . 'update_cart_item_cb');
add_action('wp_ajax_nopriv_' . LUREEN_NS . 'update_cart_item', LUREEN_NS . 'update_cart_item_cb');
function lureen_update_cart_item_cb() {
    check_ajax_referer(LUREEN_NONCE_ACTION, 'nonce');
    if (!function_exists('WC')) { wp_send_json_error(); }
    $key = sanitize_text_field($_POST['key'] ?? '');
    $qty = max(0, intval($_POST['qty'] ?? 0));
    if (!$key) { wp_send_json_error(); }
    $set = WC()->cart->set_quantity($key, $qty, true);
    if ($set !== false) { wp_send_json_success(array('count' => WC()->cart->get_cart_contents_count())); }
    wp_send_json_error();
}

/* -----------------------------------------------------------
10) AJAX HANDLERS - LOAD MORE PRODUCTS
----------------------------------------------------------- */
add_action('wp_ajax_' . LUREEN_NS . 'load_products',        LUREEN_NS . 'load_products_cb');
add_action('wp_ajax_nopriv_' . LUREEN_NS . 'load_products', LUREEN_NS . 'load_products_cb');
function lureen_load_products_cb() {
    check_ajax_referer(LUREEN_NONCE_ACTION, 'nonce');
    
    $page     = isset($_POST['page']) ? absint($_POST['page']) : 1;
    $orderby  = isset($_POST['orderby']) ? sanitize_text_field($_POST['orderby']) : 'date';
    $search   = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
    $category = isset($_POST['category']) ? absint($_POST['category']) : 0;
    $tag      = isset($_POST['tag']) ? sanitize_text_field($_POST['tag']) : '';
    
    $args = array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => LUREEN_PRODUCTS_PER_PAGE,
        'paged'          => $page,
        'fields'         => 'ids',
    );
    
    switch ($orderby) {
        case 'price':
            $args['meta_key'] = '_price';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'ASC';
            break;
        case 'price-desc':
            $args['meta_key'] = '_price';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'DESC';
            break;
        case 'popularity':
            $args['meta_key'] = 'total_sales';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'DESC';
            break;
        case 'rating':
            $args['meta_key'] = '_wc_average_rating';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'DESC';
            break;
        case 'date':
        default:
            $args['orderby'] = 'date';
            $args['order'] = 'DESC';
            break;
    }
    
    if (!empty($search)) {
        $args['s'] = $search;
    }
    
    if (!empty($category)) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $category,
            ),
        );
    }
    
    if (!empty($tag)) {
        if (isset($args['tax_query'])) {
            $args['tax_query']['relation'] = 'AND';
        } else {
            $args['tax_query'] = array();
        }
        $args['tax_query'][] = array(
            'taxonomy' => 'product_tag',
            'field'    => 'slug',
            'terms'    => $tag,
        );
    }
    
    if (!isset($args['tax_query'])) {
        $args['tax_query'] = array();
    }
    $args['tax_query'][] = array(
        'taxonomy' => 'product_visibility',
        'field'    => 'name',
        'terms'    => array('exclude-from-catalog', 'exclude-from-search'),
        'operator' => 'NOT IN',
    );
    
    $query = new WP_Query($args);
    $product_ids = $query->posts;
    
    if (empty($product_ids)) {
        wp_send_json_success(array(
            'html' => '',
            'has_more' => false
        ));
    }
    
    $has_more = $query->max_num_pages > $page;
    
    ob_start();
    foreach ($product_ids as $product_id) {
        $product = wc_get_product($product_id);
        if ($product) {
            echo lureen_render_product_card($product);
        }
    }
    $html = ob_get_clean();
    
    wp_send_json_success(array(
        'html' => $html,
        'has_more' => $has_more,
        'page' => $page
    ));
}

/* -----------------------------------------------------------
11) CART FRAGMENTS
----------------------------------------------------------- */
add_filter('woocommerce_add_to_cart_fragments', function($fragments) {
    if (!function_exists('WC')) return $fragments;
    ob_start(); ?>
    <div class="lureen-cart-count"><span class="cart-count-number"><?php echo (int) WC()->cart->get_cart_contents_count(); ?></span></div>
    <?php $fragments['.lureen-cart-count'] = ob_get_clean();

    ob_start(); ?>
    <div class="lureen-cart-modal" onclick="if(event.target.classList.contains('lureen-cart-modal')) this.classList.remove('active')">
        <?php echo lureen_render_cart_modal_inner(); ?>
    </div>
    <?php $fragments['.lureen-cart-modal'] = ob_get_clean();

    return $fragments;
});

/* -----------------------------------------------------------
12) HOME PAGE CONTENT
----------------------------------------------------------- */
function lureen_home_markup() {
    ob_start(); ?>
    <div class="lureen-hero">
        <div class="lureen-hero-content">
            <h1>مجموعة ربيع 2025</h1>
            <p>تألقي بأحدث صيحات الموسم</p>
            <a href="#new-arrivals" class="lureen-cta-btn">تسوقي الآن</a>
        </div>
    </div>

    <div class="lureen-section" id="categories">
        <h2 class="lureen-section-title">تسوقي حسب الفئة</h2>
        <div class="lureen-categories-grid">
            <?php
            $categories = get_terms(array('taxonomy'=>'product_cat','hide_empty'=>true,'parent'=>0));
            if (!empty($categories) && !is_wp_error($categories)) {
                foreach ($categories as $cat) {
                    $thumb_id = get_term_meta($cat->term_id, 'thumbnail_id', true);
                    $img_url  = $thumb_id ? wp_get_attachment_url($thumb_id) : wc_placeholder_img_src(); ?>
                    <a href="<?php echo esc_url(get_term_link($cat)); ?>" class="lureen-category-card">
                        <img src="<?php echo esc_url($img_url); ?>" class="lureen-category-image" alt="<?php echo esc_attr($cat->name); ?>" loading="lazy" />
                        <div class="lureen-category-info">
                            <div class="lureen-category-name"><?php echo esc_html($cat->name); ?></div>
                            <div class="lureen-category-count"><?php echo intval($cat->count); ?> منتج</div>
                        </div>
                    </a>
                <?php }
            } ?>
        </div>
    </div>

    <div class="lureen-section" id="new-arrivals">
        <h2 class="lureen-section-title">وصل حديثاً</h2>
        <div class="lureen-products-grid" id="lureen-home-products">
            <?php
            $args = array(
                'post_type'      => 'product',
                'post_status'    => 'publish',
                'posts_per_page' => LUREEN_PRODUCTS_PER_PAGE,
                'orderby'        => 'date',
                'order'          => 'DESC',
            );
            $query = new WP_Query($args);
            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    $product = wc_get_product(get_the_ID());
                    if ($product) {
                        echo lureen_render_product_card($product);
                    }
                }
                wp_reset_postdata();
            }
            ?>
        </div>
        <div class="lureen-load-more-wrapper" style="text-align:center; margin-top:30px;">
            <button class="lureen-load-more-btn" data-page="1" data-context="home">
                تحميل المزيد <i class="fas fa-chevron-down"></i>
            </button>
        </div>
    </div>
    
    <script>
    jQuery(document).ready(function($){
        var $container = $('#lureen-home-products');
        var $loadBtn = $('.lureen-load-more-btn[data-context="home"]');
        var loading = false;
        
        function loadMoreProducts() {
            if (loading) return;
            loading = true;
            
            var page = parseInt($loadBtn.data('page')) + 1;
            
            $loadBtn.html('<i class="fas fa-spinner fa-spin"></i> جاري التحميل...').prop('disabled', true);
            
            $.ajax({
                url: window.lureenAjax.ajax_url,
                type: 'POST',
                data: {
                    action: 'lureen_load_products',
                    nonce: window.lureenAjax.nonce,
                    page: page,
                    orderby: 'date'
                },
                success: function(response) {
                    if (response.success && response.data.html) {
                        $container.append(response.data.html);
                        $loadBtn.data('page', page);
                        
                        if (!response.data.has_more) {
                            $loadBtn.html('لا يوجد المزيد من المنتجات').prop('disabled', true);
                        } else {
                            $loadBtn.html('تحميل المزيد <i class="fas fa-chevron-down"></i>').prop('disabled', false);
                        }
                    } else {
                        $loadBtn.html('لا يوجد المزيد من المنتجات').prop('disabled', true);
                    }
                    loading = false;
                },
                error: function() {
                    $loadBtn.html('تحميل المزيد <i class="fas fa-chevron-down"></i>').prop('disabled', false);
                    loading = false;
                }
            });
        }
        
        $loadBtn.on('click', loadMoreProducts);
        
        $(window).on('scroll', function() {
            if ($loadBtn.is(':visible') && !$loadBtn.prop('disabled')) {
                var btnOffset = $loadBtn.offset().top;
                var scrollTop = $(window).scrollTop();
                var windowHeight = $(window).height();
                
                if (scrollTop + windowHeight > btnOffset - 200) {
                    loadMoreProducts();
                }
            }
        });
    });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('lureen_home', 'lureen_home_markup');
add_filter('the_content', function($content) {
    if (is_front_page() && in_the_loop() && is_main_query()) { return lureen_home_markup(); }
    return $content;
}, 20);

/* -----------------------------------------------------------
13) CUSTOM SHOP & CATEGORY ARCHIVES
----------------------------------------------------------- */
function lureen_shop_grid_markup($atts = array()) {
    $is_category = is_product_category();
    $is_tag = is_product_tag();
    $is_custom_page = !empty($atts['custom_page']);
    
    $title = 'جميع المنتجات';
    $category_id = 0;
    $tag = '';
    
    if ($is_category) {
        $term = get_queried_object();
        if ($term && !is_wp_error($term)) {
            $title = esc_html($term->name);
            $category_id = $term->term_id;
        }
    }
    
    if ($is_tag) {
        $term = get_queried_object();
        if ($term && !is_wp_error($term)) {
            $title = esc_html($term->name);
            $tag = $term->slug;
        }
    }

    $orderby = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'date';
    $search  = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
    
    $args = array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => LUREEN_PRODUCTS_PER_PAGE,
        'paged'          => 1,
    );
    
    switch ($orderby) {
        case 'price':
            $args['meta_key'] = '_price';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'ASC';
            break;
        case 'price-desc':
            $args['meta_key'] = '_price';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'DESC';
            break;
        case 'popularity':
            $args['meta_key'] = 'total_sales';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'DESC';
            break;
        case 'rating':
            $args['meta_key'] = '_wc_average_rating';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'DESC';
            break;
        case 'date':
        default:
            $args['orderby'] = 'date';
            $args['order'] = 'DESC';
            break;
    }
    
    if (!empty($search)) {
        $args['s'] = $search;
    }
    
    if (!empty($category_id)) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $category_id,
            ),
        );
    }
    
    if (!empty($tag)) {
        if (isset($args['tax_query'])) {
            $args['tax_query']['relation'] = 'AND';
        } else {
            $args['tax_query'] = array();
        }
        $args['tax_query'][] = array(
            'taxonomy' => 'product_tag',
            'field'    => 'slug',
            'terms'    => $tag,
        );
    }
    
    if (!isset($args['tax_query'])) {
        $args['tax_query'] = array();
    }
    $args['tax_query'][] = array(
        'taxonomy' => 'product_visibility',
        'field'    => 'name',
        'terms'    => array('exclude-from-catalog', 'exclude-from-search'),
        'operator' => 'NOT IN',
    );

    $query = new WP_Query($args);

    ob_start(); ?>
    <div class="lureen-section">
        <h2 class="lureen-section-title" style="margin-top:40px;"><?php echo $title; ?></h2>

        <?php
        if ($is_category && !empty($category_id)) {
            $children = get_terms(array('taxonomy'=>'product_cat','hide_empty'=>true,'parent'=>$category_id));
            if (!empty($children) && !is_wp_error($children)) {
                echo '<div class="lureen-categories-grid lureen-subcategories-grid" style="margin-bottom:40px;">';
                foreach ($children as $child) {
                    $thumb_id = get_term_meta($child->term_id, 'thumbnail_id', true);
                    $img_url  = $thumb_id ? wp_get_attachment_url($thumb_id) : wc_placeholder_img_src();
                    echo '<a href="' . esc_url(get_term_link($child)) . '" class="lureen-category-card">';
                    echo '<img src="' . esc_url($img_url) . '" class="lureen-category-image" alt="' . esc_attr($child->name) . '" loading="lazy" />';
                    echo '<div class="lureen-category-info"><div class="lureen-category-name">' . esc_html($child->name) . '</div><div class="lureen-category-count">' . intval($child->count) . ' منتج</div></div>';
                    echo '</a>';
                }
                echo '</div>';
            }
        }
        ?>

        <div class="lureen-filters-bar">
            <div class="lureen-ordering">
                <label for="lureen-orderby" style="font-weight: 700; margin-left: 8px;">ترتيب حسب:</label>
                <select name="orderby" class="orderby" aria-label="ترتيب المنتجات" id="lureen-orderby">
                    <?php 
                    $sort_options = array(
                        'date'       => 'الأحدث أولاً',
                        'popularity' => 'الأكثر مبيعاً',
                        'rating'     => 'الأعلى تقييماً',
                        'price'      => 'السعر: من الأقل للأعلى',
                        'price-desc' => 'السعر: من الأعلى للأقل',
                    );
                    foreach ($sort_options as $value => $label): ?>
                        <option value="<?php echo esc_attr($value); ?>" <?php selected($orderby, $value); ?>><?php echo esc_html($label); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <form class="lureen-search-form" role="search" method="get" id="lureen-search-form">
                <label class="screen-reader-text" for="lureen-search-input">البحث عن:</label>
                <input id="lureen-search-input" type="search" name="s" value="<?php echo esc_attr($search); ?>" placeholder="ابحث عن منتجات..." />
                <input type="hidden" name="post_type" value="product" />
                <?php if (!empty($category_id)): ?>
                    <input type="hidden" name="product_cat_id" value="<?php echo esc_attr($category_id); ?>" />
                <?php endif; ?>
                <?php if (!empty($tag)): ?>
                    <input type="hidden" name="product_tag" value="<?php echo esc_attr($tag); ?>" />
                <?php endif; ?>
                <button type="submit" aria-label="بحث"><i class="fas fa-search"></i></button>
            </form>
        </div>

        <div class="lureen-products-grid" id="lureen-products-container" 
             data-category="<?php echo esc_attr($category_id); ?>"
             data-tag="<?php echo esc_attr($tag); ?>"
             data-orderby="<?php echo esc_attr($orderby); ?>"
             data-search="<?php echo esc_attr($search); ?>">
            <?php
            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    $product = wc_get_product(get_the_ID());
                    if ($product) {
                        echo lureen_render_product_card($product);
                    }
                }
                wp_reset_postdata();
            } else {
                echo '<p style="text-align:center; font-size:18px; color:#555; grid-column: 1/-1;">لا توجد منتجات متاحة هنا حالياً.</p>';
            }
            ?>
        </div>
        
        <?php if ($query->have_posts()): ?>
        <div class="lureen-load-more-wrapper" style="text-align:center; margin-top:30px;">
            <button class="lureen-load-more-btn" data-page="1" data-context="shop">
                تحميل المزيد <i class="fas fa-chevron-down"></i>
            </button>
        </div>
        <?php endif; ?>
    </div>
    
    <script>
    jQuery(document).ready(function($){
        var $container = $('#lureen-products-container');
        var $loadBtn = $('.lureen-load-more-btn[data-context="shop"]');
        var loading = false;
        
        function loadMoreProducts() {
            if (loading) return;
            loading = true;
            
            var page = parseInt($loadBtn.data('page')) + 1;
            var category = $container.data('category');
            var tag = $container.data('tag');
            var orderby = $container.data('orderby');
            var search = $container.data('search');
            
            $loadBtn.html('<i class="fas fa-spinner fa-spin"></i> جاري التحميل...').prop('disabled', true);
            
            $.ajax({
                url: window.lureenAjax.ajax_url,
                type: 'POST',
                data: {
                    action: 'lureen_load_products',
                    nonce: window.lureenAjax.nonce,
                    page: page,
                    category: category,
                    tag: tag,
                    orderby: orderby,
                    search: search
                },
                success: function(response) {
                    if (response.success && response.data.html) {
                        $container.append(response.data.html);
                        $loadBtn.data('page', page);
                        
                        if (!response.data.has_more) {
                            $loadBtn.html('لا يوجد المزيد من المنتجات').prop('disabled', true);
                        } else {
                            $loadBtn.html('تحميل المزيد <i class="fas fa-chevron-down"></i>').prop('disabled', false);
                        }
                    } else {
                        $loadBtn.html('لا يوجد المزيد من المنتجات').prop('disabled', true);
                    }
                    loading = false;
                },
                error: function() {
                    $loadBtn.html('تحميل المزيد <i class="fas fa-chevron-down"></i>').prop('disabled', false);
                    loading = false;
                }
            });
        }
        
        $loadBtn.on('click', loadMoreProducts);
        
        $(window).on('scroll', function() {
            if ($loadBtn.is(':visible') && !$loadBtn.prop('disabled')) {
                var btnOffset = $loadBtn.offset().top;
                var scrollTop = $(window).scrollTop();
                var windowHeight = $(window).height();
                
                if (scrollTop + windowHeight > btnOffset - 200) {
                    loadMoreProducts();
                }
            }
        });
        
        $('#lureen-orderby').on('change', function() {
            var orderby = $(this).val();
            var currentUrl = window.location.href.split('?')[0];
            var params = new URLSearchParams(window.location.search);
            
            params.set('orderby', orderby);
            
            window.location.href = currentUrl + '?' + params.toString();
        });
        
        $('#lureen-search-form').on('submit', function(e) {
            e.preventDefault();
            
            var search = $(this).find('#lureen-search-input').val();
            var category = $container.data('category');
            var tag = $container.data('tag');
            var orderby = $('#lureen-orderby').val();
            
            $container.data('search', search);
            $container.data('category', category);
            $container.data('tag', tag);
            $container.data('orderby', orderby);
            
            $loadBtn.data('page', 0);
            $container.empty();
            loadMoreProducts();
            
            var currentUrl = window.location.href.split('?')[0];
            var params = new URLSearchParams();
            
            if (search) params.set('s', search);
            if (category) params.set('product_cat_id', category);
            if (tag) params.set('product_tag', tag);
            if (orderby) params.set('orderby', orderby);
            
            var newUrl = currentUrl + (params.toString() ? '?' + params.toString() : '');
            window.history.pushState({path: newUrl}, '', newUrl);
        });
    });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('lureen_store', 'lureen_shop_grid_markup');

/* -----------------------------------------------------------
14) CATEGORY LIST PAGE
----------------------------------------------------------- */
function lureen_categories_markup() {
    ob_start(); ?>
    <div class="lureen-section">
        <h2 class="lureen-section-title">التصنيفات</h2>
        <div class="lureen-categories-grid">
            <?php
            $categories = get_terms(array('taxonomy'=>'product_cat','hide_empty'=>true,'parent'=>0));
            if (!empty($categories) && !is_wp_error($categories)) {
                foreach ($categories as $cat) {
                    $thumb_id = get_term_meta($cat->term_id, 'thumbnail_id', true);
                    $img_url  = $thumb_id ? wp_get_attachment_url($thumb_id) : wc_placeholder_img_src(); ?>
                    <a href="<?php echo esc_url(get_term_link($cat)); ?>" class="lureen-category-card">
                        <img src="<?php echo esc_url($img_url); ?>" class="lureen-category-image" alt="<?php echo esc_attr($cat->name); ?>" loading="lazy" />
                        <div class="lureen-category-info">
                            <div class="lureen-category-name"><?php echo esc_html($cat->name); ?></div>
                            <div class="lureen-category-count"><?php echo intval($cat->count); ?> منتج</div>
                        </div>
                    </a>
                <?php }
            } else {
                echo '<p style="text-align:center; font-size:18px; color:#555;">لا توجد تصنيفات متاحة حالياً.</p>';
            } ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('lureen_categories', 'lureen_categories_markup');

/* -----------------------------------------------------------
15) REMOVE LEGACY WOOCOMMERCE ELEMENTS & INJECT CUSTOM LAYOUT
----------------------------------------------------------- */
add_filter('woocommerce_show_page_title', '__return_false');
remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
remove_action('woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10);
remove_action('woocommerce_archive_description', 'woocommerce_product_archive_description', 10);
remove_action('woocommerce_before_shop_loop', 'woocommerce_output_all_notices', 10);
remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);

add_action('woocommerce_before_shop_loop', function() {
    if (is_product_category() || is_product_tag()) {
        echo lureen_shop_grid_markup();
        echo '<style>
            ul.products, .woocommerce-pagination,
            .woocommerce-products-header, .woocommerce-products-header__title,
            .woocommerce-result-count, .woocommerce-ordering,
            .woocommerce-notices, .woocommerce-notices-wrapper,
            .storefront-sorting,
            h1.page-title, .archive-title, .wp-block-query-title, .wp-block-archive-title,
            .wc-block-grid, .wc-block-grid__products,
            .wp-block-woocommerce-product-template,
            .wp-block-query .wp-block-post-template { display: none !important; }
        </style>';
    }
}, 15);

/* -----------------------------------------------------------
16) AUTO-CREATE CUSTOM PAGES (NO REDIRECT FOR SHOP)
----------------------------------------------------------- */
add_action('init', function() {
    $ids = lureen_get_pages_option();
    $ensure_page = function($title, $slug, $shortcode) {
        $existing = get_page_by_path($slug);
        if ($existing) return $existing->ID;
        return wp_insert_post(array(
            'post_title'=>$title,'post_name'=>$slug,'post_type'=>'page',
            'post_status'=>'publish','post_content'=>$shortcode
        ));
    };
    if (empty($ids['store']))      { $ids['store']      = $ensure_page('جميع المنتجات',      'all-products',      '[lureen_store custom_page="true"]'); }
    if (empty($ids['cart']))       { $ids['cart']       = $ensure_page('السلة (جديدة)',       'cart-new',       '[woocommerce_cart]'); }
    if (empty($ids['checkout']))   { $ids['checkout']   = $ensure_page('الدفع (جديد)',        'checkout-new',   '[woocommerce_checkout]'); }
    if (empty($ids['categories'])) { $ids['categories'] = $ensure_page('التصنيفات (جديدة)',   'categories-new', '[lureen_categories]'); }
    lureen_set_pages_option($ids);
});

add_action('template_redirect', function() {
    if (!function_exists('WC')) return;
    $ids = lureen_get_pages_option();
    $is_new_cart     = !empty($ids['cart'])     && is_page($ids['cart']);
    $is_new_checkout = !empty($ids['checkout']) && is_page($ids['checkout']);
    
    if (is_cart() && !$is_new_cart && !empty($ids['cart']))   { wp_safe_redirect(get_permalink($ids['cart']), 301); exit; }
    if (is_checkout() && !$is_new_checkout && !empty($ids['checkout'])) { wp_safe_redirect(get_permalink($ids['checkout']), 301); exit; }
});

add_filter('wp_page_menu_args', function($args) {
    if (function_exists('wc_get_page_id')) {
        $exclude_ids = array_filter(array(
            wc_get_page_id('cart'),
            wc_get_page_id('checkout'), 
            wc_get_page_id('myaccount'),
        ));
        if (!empty($exclude_ids)) { $args['exclude'] = implode(',', $exclude_ids); }
    }
    return $args;
});

add_action('pre_get_posts', function($q) {
    if ($q->is_main_query() && $q->is_search() && function_exists('wc_get_page_id')) {
        $exclude_ids = array_filter(array(
            wc_get_page_id('cart'),
            wc_get_page_id('checkout'), 
            wc_get_page_id('myaccount'),
        ));
        if (!empty($exclude_ids)) { $q->set('post__not_in', $exclude_ids); }
    }
});

/* -----------------------------------------------------------
17) CUSTOM STYLES (MOBILE OPTIMIZED - REDUCED MARGINS)
----------------------------------------------------------- */
add_action('wp_head', function() { ?>
<style>
/* BASE STYLES */
html, body { font-family:'Tajawal',sans-serif !important; direction:rtl !important; margin:0; padding:0; background:#fafafa; color:#2c3e50; }
*{ box-sizing:border-box; }

/* NAVIGATION */
.lureen-custom-nav{ background:#fff; box-shadow:0 4px 25px rgba(0,0,0,.08); position:sticky; top:0; width:100%; z-index:9999; }
.lureen-nav-container{ max-width:1400px; margin:0 auto; padding:12px 20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; }
.lureen-custom-logo{ font-size:30px; font-weight:800; background:linear-gradient(135deg,#9b59b6,#e74c3c); -webkit-background-clip:text; -webkit-text-fill-color:transparent; text-decoration:none; }
.lureen-nav-menu{ display:flex; gap:24px; list-style:none; margin:0; padding:0; }
.lureen-nav-menu li a{ color:#2c3e50; text-decoration:none; font-weight:700; font-size:16px; position:relative; padding:8px 0; transition:color .25s; }
.lureen-nav-menu li a:hover{ color:#9b59b6; }
.lureen-nav-menu li a.active{ color:#9b59b6; }
.lureen-nav-menu li a::after{ content:''; position:absolute; bottom:0; right:0; width:0; height:3px; background:linear-gradient(90deg,#9b59b6,#e74c3c); transition:.25s; }
.lureen-nav-menu li a:hover::after, .lureen-nav-menu li a.active::after{ width:100%; left:0; }
.lureen-mobile-toggle{ display:none; background:none; border:none; font-size:26px; color:#9b59b6; cursor:pointer; }

/* HERO */
.lureen-hero{ height:400px; background:linear-gradient(135deg,#9b59b6,#e74c3c); color:#fff; display:flex; align-items:center; justify-content:center; position:relative; overflow:hidden; }
.lureen-hero::before{ content:''; position:absolute; width:580px; height:580px; background:rgba(255,255,255,0.08); border-radius:50%; top:-290px; right:-290px; }
.lureen-hero-content{ text-align:center; z-index:1; padding:20px; }
.lureen-hero-content h1{ font-size:48px; margin-bottom:16px; font-weight:800; text-shadow:0 4px 15px rgba(0,0,0,0.2); }
.lureen-hero-content p{ font-size:20px; margin-bottom:28px; opacity:0.95; }
.lureen-cta-btn{ padding:14px 36px; background:#fff; color:#9b59b6; border:0; border-radius:50px; font-size:17px; font-weight:800; text-decoration:none; display:inline-block; transition:transform .25s; }
.lureen-cta-btn:hover{ transform:translateY(-3px); }

/* SECTION - REDUCED MARGINS */
.lureen-section{ max-width:1400px; margin:50px auto; padding:0 16px; }
.lureen-section-title{ text-align:center; font-size:34px; margin-bottom:30px; color:#2c3e50; font-weight:800; position:relative; padding-bottom:12px; }
.lureen-section-title::after{ content:''; position:absolute; bottom:0; left:50%; transform:translateX(-50%); width:84px; height:4px; background:linear-gradient(90deg,#9b59b6,#e74c3c); }

/* CATEGORIES GRID - MAX 4 COLUMNS ON DESKTOP */
.lureen-categories-grid{ display:grid; grid-template-columns:repeat(auto-fit, minmax(200px,1fr)); gap:20px; max-width:100%; }
@media(min-width:769px){
    .lureen-categories-grid{ grid-template-columns:repeat(auto-fit, minmax(250px,280px)); justify-content:center; }
}
.lureen-category-card{ background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 8px 28px rgba(0,0,0,0.08); transition:transform .35s, box-shadow .35s; text-decoration:none; display:flex; flex-direction:column; }
.lureen-category-card:hover{ transform:translateY(-8px); box-shadow:0 18px 44px rgba(155,89,182,0.28); }
.lureen-category-image{ width:100%; aspect-ratio:16/9; object-fit:cover; transition:transform .35s; }
.lureen-category-card:hover .lureen-category-image{ transform:scale(1.06); }
.lureen-category-info{ padding:16px; text-align:center; display:flex; flex-direction:column; gap:6px; }
.lureen-category-name{ font-size:20px; font-weight:800; color:#2c3e50; margin-bottom:4px; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; text-overflow:ellipsis; }
.lureen-category-count{ color:#9b59b6; font-size:14px; font-weight:700; }
.lureen-subcategories-grid{ margin-bottom:30px; }

/* PRODUCTS GRID - UNIFIED STRUCTURE */
.lureen-products-grid{ display:grid; align-items:stretch; grid-template-columns:repeat(auto-fill, minmax(240px,1fr)); gap:18px; }

/* PRODUCT CARD - FIXED SIZE */
.lureen-product-card{ 
    background:#fff; 
    border-radius:20px; 
    overflow:hidden; 
    box-shadow:0 8px 28px rgba(0,0,0,0.08); 
    transition:transform .35s, box-shadow .35s; 
    position:relative; 
    display:flex; 
    flex-direction:column; 
    height:100%; 
}
.lureen-product-card:hover{ transform:translateY(-8px); box-shadow:0 18px 44px rgba(155,89,182,0.28); }
.lureen-product-badge{ position:absolute; top:12px; right:12px; padding:6px 12px; border-radius:18px; font-size:12px; font-weight:800; color:#fff; z-index:2; }
.lureen-product-badge.new{ background:linear-gradient(135deg,#2ecc71,#27ae60); }
.lureen-product-badge.sale{ background:linear-gradient(135deg,#e74c3c,#c0392b); }
.lureen-product-image-wrapper{ display:block; width:100%; aspect-ratio:1/1; overflow:hidden; }
.lureen-product-image{ width:100%; height:100%; object-fit:cover; display:block; }
.lureen-product-info{ padding:14px; display:flex; flex-direction:column; gap:8px; flex:1; }
.lureen-product-name{ font-size:17px; font-weight:800; color:#2c3e50; line-height:1.35; }
.lureen-product-name a{ text-decoration:none; color:inherit; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; text-overflow:ellipsis; }
.lureen-product-price{ margin-top:-2px; display:flex; align-items:baseline; gap:8px; flex-wrap:wrap; }
.lureen-price-normal{ font-size:20px; font-weight:800; color:#9b59b6; }
.lureen-price-regular{ font-size:16px; text-decoration:line-through; color:#999; }
.lureen-price-sale{ font-size:20px; font-weight:800; color:#e74c3c; }
.lureen-add-to-cart-btn{ margin-top:auto; width:100%; padding:11px; background:linear-gradient(135deg,#9b59b6,#8e44ad); color:#fff; border:none; border-radius:40px; font-size:15px; font-weight:800; cursor:pointer; transition:transform .25s, opacity .25s; text-align:center; text-decoration:none; display:inline-flex; align-items:center; justify-content:center; gap:8px; }
.lureen-add-to-cart-btn:hover{ transform:translateY(-2px); }
.lureen-add-to-cart-btn.loading{ opacity:.7; pointer-events:none; }
.lureen-add-to-cart-btn.added{ background:linear-gradient(135deg,#2ecc71,#27ae60); }

/* FILTERS BAR */
.lureen-filters-bar{ display:flex !important; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px; background:#fff; padding:14px; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,0.05); }
.lureen-ordering{ display:flex !important; align-items:center; gap:8px; }
.lureen-ordering select.orderby{ padding:10px 14px; border:2px solid #e0e0e0; border-radius:8px; font-size:15px; min-width:220px; font-weight:600; background:#fff; cursor:pointer; transition:border-color .25s; appearance:none; background-image:url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="%239b59b6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"%3e%3cpolyline points="6 9 12 15 18 9"%3e%3c/polyline%3e%3c/svg%3e'); background-repeat:no-repeat; background-position:left 10px center; background-size:20px; padding-left:40px; }
.lureen-ordering select.orderby:hover, .lureen-ordering select.orderby:focus{ border-color:#9b59b6; outline:none; }
.lureen-search-form{ position:relative; max-width:320px; flex:1; text-align:right; display:flex !important; }
.lureen-search-form input[type="search"]{ width:100%; padding:10px 42px 10px 14px; border:2px solid #e0e0e0; border-radius:20px; font-size:15px; transition:border-color .25s; }
.lureen-search-form input[type="search"]:focus{ border-color:#9b59b6; outline:none; }
.lureen-search-form button{ position:absolute; top:50%; right:10px; transform:translateY(-50%); background:none; border:none; font-size:18px; color:#9b59b6; cursor:pointer; transition:transform .25s; }
.lureen-search-form button:hover{ transform:translateY(-50%) scale(1.1); }
.screen-reader-text{ position:absolute; width:1px; height:1px; padding:0; margin:-1px; overflow:hidden; clip:rect(0,0,0,0); white-space:nowrap; border-width:0; }

/* LOAD MORE */
.lureen-load-more-wrapper{ text-align:center; margin-top:25px; }
.lureen-load-more-btn{ padding:14px 40px; background:linear-gradient(135deg,#9b59b6,#8e44ad); color:#fff; border:none; border-radius:50px; font-size:16px; font-weight:800; cursor:pointer; transition:transform .25s, opacity .25s; display:inline-flex; align-items:center; gap:10px; }
.lureen-load-more-btn:hover:not(:disabled){ transform:translateY(-2px); }
.lureen-load-more-btn:disabled{ opacity:0.6; cursor:not-allowed; }

/* STICKY CART */
.lureen-sticky-cart{ position:fixed; left:18px; bottom:18px; background:linear-gradient(135deg,#9b59b6,#8e44ad); color:#fff; border-radius:50%; box-shadow:0 12px 36px rgba(142,68,173,0.35); cursor:pointer; z-index:99999; width:60px; height:60px; display:flex; align-items:center; justify-content:center; border:3px solid #fff; transition:transform .25s; }
.lureen-sticky-cart:hover{ transform:scale(1.12) rotate(4deg); }
.lureen-cart-icon{ font-size:24px; }
.lureen-cart-count{ position:absolute; top:-8px; right:-8px; background:#e74c3c; color:#fff; border-radius:50%; width:24px; height:24px; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:800; border:2px solid #fff; }

/* CART MODAL */
.lureen-cart-modal{ display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,.75); z-index:100000; align-items:center; justify-content:center; padding:16px; }
.lureen-cart-modal.active{ display:flex !important; }
.lureen-cart-content{ background:#fff; width:480px; max-width:100%; max-height:84vh; border-radius:20px; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,0.3); display:flex; flex-direction:column; }
.lureen-cart-header{ background:linear-gradient(135deg,#9b59b6,#8e44ad); color:#fff; padding:18px; display:flex; justify-content:space-between; align-items:center; }
.lureen-cart-header h2{ margin:0; font-size:20px; font-weight:800; }
.lureen-close-btn{ background:rgba(255,255,255,.2); border:none; color:#fff; font-size:26px; width:38px; height:38px; border-radius:50%; cursor:pointer; transition:transform .25s; }
.lureen-close-btn:hover{ background:rgba(255,255,255,.3); transform:rotate(90deg); }
.lureen-cart-items{ padding:14px 18px; overflow-y:auto; flex:1; }
.lureen-empty-cart{ text-align:center; padding:40px 20px; color:#999; }
.lureen-empty-cart i{ font-size:64px; margin-bottom:16px; opacity:0.3; }
.lureen-empty-cart p{ font-size:18px; font-weight:600; }
.lureen-cart-item{ display:flex; gap:14px; padding:14px 0; border-bottom:1px solid #eee; align-items:center; }
.lureen-cart-item img{ width:60px; height:60px; object-fit:cover; border-radius:10px; }
.lureen-cart-item-info{ flex:1; }
.lureen-cart-item-name{ font-weight:800; color:#2c3e50; margin-bottom:5px; }
.lureen-cart-item-price{ color:#9b59b6; font-weight:800; font-size:14px; margin-bottom:8px; }
.lureen-qty-controls{ display:flex; align-items:center; gap:8px; }
.lureen-qty{ border:0; background:#f0f0f5; width:34px; height:34px; border-radius:10px; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:background .25s; }
.lureen-qty:hover{ background:#e0e0e5; }
.lureen-qty-input{ width:56px; height:34px; border:1px solid #eee; border-radius:10px; text-align:center; font-weight:700; }
.lureen-remove-item{ background:#fff0f0; border:0; color:#e74c3c; font-size:18px; width:38px; height:38px; border-radius:10px; cursor:pointer; transition:background .25s; }
.lureen-remove-item:hover{ background:#ffe0e0; }
.lureen-cart-footer{ padding:16px 18px; border-top:2px solid #f1f1f1; background:#f9fafb; }
.lureen-cart-total{ display:flex; justify-content:space-between; margin-bottom:12px; font-size:17px; font-weight:800; }
.lureen-cart-actions{ display:flex; gap:10px; }
.lureen-view-cart{ flex:1; text-align:center; background:#eef0ff; color:#4e5bdc !important; text-decoration:none; font-weight:800; padding:12px; border-radius:50px; transition:background .25s; }
.lureen-view-cart:hover{ background:#dde0ff; }
.lureen-checkout-btn{ flex:1; text-align:center; background:linear-gradient(135deg,#9b59b6,#8e44ad); color:#fff !important; text-decoration:none; font-size:16px; font-weight:800; padding:12px; border-radius:50px; display:inline-block; transition:transform .25s; }
.lureen-checkout-btn:hover{ transform:translateY(-2px); }

/* MOBILE OPTIMIZATIONS - 2 COLUMN GRID WITH MINIMAL MARGINS */
@media(max-width:768px){
    /* Navigation */
    .lureen-mobile-toggle{ display:block; }
    .lureen-nav-menu{ display:none; flex-direction:column; gap:14px; width:100%; }
    .lureen-nav-menu.active{ display:flex; }
    .lureen-nav-container{ padding:12px 16px; }
    
    /* Hero */
    .lureen-hero{ height:350px; }
    .lureen-hero-content h1{ font-size:34px; }
    
    /* Section - Minimal padding on mobile */
    .lureen-section{ padding:0 4px; margin:30px auto; }
    .lureen-section-title{ font-size:28px; margin-bottom:20px; }
    
    /* Categories Grid - 2 columns with minimal gap */
    .lureen-categories-grid{ 
        grid-template-columns:repeat(2,1fr); 
        gap:6px; 
    }
    .lureen-category-card{ border-radius:8px; }
    .lureen-category-info{ padding:12px 8px; }
    .lureen-category-name{ 
        display:block; 
        white-space:nowrap; 
        hyphens:none; 
        word-break:normal; 
        overflow:hidden; 
        text-overflow:clip; 
        line-height:1.2; 
        font-size:20px; 
    }
    
    /* Products Grid - FIXED 2 COLUMNS WITH MINIMAL MARGINS */
    .lureen-products-grid{ 
        grid-template-columns:repeat(2,1fr); 
        gap:6px; 
    }
    
    /* Product Cards - Consistent size across all pages */
    .lureen-product-card{ 
        border-radius:12px;
    }
    .lureen-product-badge{ 
        top:8px; 
        right:8px; 
        padding:4px 8px; 
        font-size:10px; 
    }
    .lureen-product-info{ 
        padding:12px 10px; 
        gap:6px;
    }
    .lureen-product-name{ font-size:15px; line-height:1.3; }
    .lureen-product-name a{ -webkit-line-clamp:2; }
    .lureen-price-normal, .lureen-price-sale{ font-size:18px; }
    .lureen-price-regular{ font-size:14px; }
    .lureen-add-to-cart-btn{ 
        padding:9px; 
        font-size:13px; 
        border-radius:30px; 
        gap:6px;
        margin-top:6px;
    }
    
    /* Filters Bar */
    .lureen-filters-bar{ 
        flex-direction:column; 
        align-items:stretch; 
        padding:12px 8px; 
        gap:12px;
        margin-bottom:14px;
    }
    .lureen-ordering{ 
        max-width:100%; 
        width:100%; 
        flex-direction:column;
        align-items:stretch;
        gap:8px;
    }
    .lureen-ordering label{ display:block; font-size:14px; }
    .lureen-ordering select.orderby{ 
        width:100%; 
        min-width:100%; 
        padding:10px 40px 10px 12px;
    }
    .lureen-search-form{ 
        max-width:100%; 
        width:100%; 
    }
    .lureen-search-form input[type="search"]{ 
        padding:10px 40px 10px 12px; 
        font-size:14px;
    }
    
    /* Load More */
    .lureen-load-more-wrapper{ margin-top:18px; }
    .lureen-load-more-btn{ 
        padding:12px 32px; 
        font-size:15px; 
    }
    
    /* Sticky Cart - Smaller on mobile */
    .lureen-sticky-cart{ 
        width:55px; 
        height:55px; 
        left:12px; 
        bottom:12px; 
    }
    .lureen-cart-icon{ font-size:22px; }
    .lureen-cart-count{ 
        width:22px; 
        height:22px; 
        font-size:12px; 
    }
}

/* EXTRA SMALL MOBILE - Keep 2 columns */
@media(max-width:480px){
    .lureen-section{ padding:0 2px; }
    .lureen-products-grid{ 
        grid-template-columns:repeat(2,1fr); 
        gap:4px; 
    }
    .lureen-categories-grid{ 
        grid-template-columns:repeat(2,1fr); 
        gap:4px; 
    }
    .lureen-product-card{ 
        border-radius:10px;
    }
    .lureen-product-info{ 
        padding:10px 8px;
        gap:4px;
    }
    .lureen-product-name{ font-size:14px; }
    .lureen-add-to-cart-btn{ 
        font-size:12px; 
        padding:8px; 
        margin-top:4px;
    }
}

/* PERFORMANCE OPTIMIZATIONS */
.lureen-product-card, .lureen-category-card{ will-change:transform; }
.lureen-product-image, .lureen-category-image{ content-visibility:auto; }
</style>
<?php
}, 100);
