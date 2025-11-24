<?php

/**
 * Syncing related products to sticky cart
 */
/**
 * Keep sticky cart in sync on Twenty Twenty-Five single product pages (incl. "منتجات ذات صلة")
 *
 * What it does:
 * 1) Registers your sticky-cart container + count as WooCommerce cart fragments.
 * 2) Ensures simple related-product buttons use AJAX add-to-cart.
 * 3) Triggers fragment refresh on add-to-cart (classic template & Blocks).
 *
 * HOW TO USE — EDIT THESE 2 SELECTORS to match your markup:
 *   - STICKY_CART_SELECTOR: the *outer wrapper* that can be safely replaced
 *   - STICKY_CART_COUNT_SELECTOR: a small element that just shows the cart count
 *
 * Example: if your sticky cart root is <div class="sticky-cart">…</div>
 *          and the count lives in <span class="sticky-cart__count">0</span>
 *          set the selectors below to ".sticky-cart" and ".sticky-cart__count"
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

const STICKY_CART_SELECTOR        = '.sticky-cart';          // <-- EDIT ME
const STICKY_CART_COUNT_SELECTOR  = '.sticky-cart__count';   // <-- EDIT ME

/**
 * Server-side renderer for your sticky cart’s HTML.
 * IMPORTANT: we return HTML that includes the same root class used by STICKY_CART_SELECTOR
 * so Woo can replace it 1:1 during fragment refresh.
 */
function tt5_render_sticky_cart_html() {
    if ( ! class_exists( 'WC' ) || ! WC()->cart ) {
        return '<div class="' . esc_attr( ltrim( STICKY_CART_SELECTOR, '.' ) ) . '"></div>';
    }

    $count    = WC()->cart->get_cart_contents_count();
    $subtotal = WC()->cart->get_cart_subtotal();

    // Build minimal, safe markup; keep your existing inner structure if you want.
    ob_start();
    ?>
    <div class="<?php echo esc_attr( ltrim( STICKY_CART_SELECTOR, '.' ) ); ?>" data-sticky-cart>
        <button class="sticky-cart__toggle" type="button" aria-expanded="false">
            <span class="<?php echo esc_attr( ltrim( STICKY_CART_COUNT_SELECTOR, '.' ) ); ?>" data-sticky-cart-count><?php echo esc_html( $count ); ?></span>
            <span class="sticky-cart__subtotal" data-sticky-cart-subtotal><?php echo wp_kses_post( $subtotal ); ?></span>
        </button>
        <!-- Optional: list items/mini-cart, or keep your own markup -->
    </div>
    <?php
    return trim( ob_get_clean() );
}

/**
 * 1) Register sticky cart fragments so Woo will refresh/replace them after adds/removes.
 */
add_filter( 'woocommerce_add_to_cart_fragments', function( $fragments ) {
    $fragments[ STICKY_CART_SELECTOR ] = tt5_render_sticky_cart_html();

    if ( STICKY_CART_COUNT_SELECTOR ) {
        $count = ( class_exists( 'WC' ) && WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;
        $fragments[ STICKY_CART_COUNT_SELECTOR ] = sprintf(
            '<span class="%s" data-sticky-cart-count>%d</span>',
            esc_attr( ltrim( STICKY_CART_COUNT_SELECTOR, '.' ) ),
            (int) $count
        );
    }

    return $fragments;
} );

/**
 * 2) Help Woo’s client cache know things changed (edge caches/CDN friendliness).
 */
add_filter( 'woocommerce_add_to_cart_hash', function( $hash ) {
    if ( class_exists( 'WC' ) && WC()->cart ) {
        $hash .= '|' . WC()->cart->get_cart_contents_count() . '|' . WC()->cart->get_cart_subtotal();
    }
    return $hash;
} );

/**
 * 3) Prefer AJAX add-to-cart for simple products in related/upsell areas on single product.
 * (So we stay on the page and fragments can update.)
 */
add_filter( 'woocommerce_loop_add_to_cart_link', function( $html, $product, $args ) {
    if ( ! function_exists( 'is_product' ) || ! is_product() ) return $html;
    if ( ! $product || ! is_a( $product, 'WC_Product' ) )   return $html;

    $supports_ajax = method_exists( $product, 'supports' ) && $product->supports( 'ajax_add_to_cart' );
    if ( ! $supports_ajax || ! $product->is_purchasable() || ! $product->is_in_stock() ) {
        return $html;
    }

    if ( strpos( $html, 'ajax_add_to_cart' ) === false ) {
        $html = preg_replace( '/class=("|\')(.*?)\1/i', 'class=$1$2 ajax_add_to_cart$1', $html, 1 );
    }
    return $html;
}, 10, 3 );

/**
 * 4) Front-end: make sure fragment refreshes fire for both Classic & Blocks flows on TT25.
 */
add_action( 'wp_enqueue_scripts', function () {
    if ( ! function_exists( 'is_product' ) || ! is_product() ) return;

    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'wc-cart-fragments' ); // still present and used by Woo for fragment lifecycle

    $js = <<<JS
(function($){
  'use strict';

  // Debounced refresh helper
  var t;
  function refreshFragments(now){
    if (t) clearTimeout(t);
    t = setTimeout(function(){
      $(document.body).trigger('wc_fragment_refresh');
    }, now ? 0 : 120);
  }

  // Classic templates (ajax add-to-cart) -> Woo fires these automatically
  $(document.body).on('added_to_cart removed_from_cart updated_cart_totals', function(){
    refreshFragments(true);
  });

  // Related/Upsells clicks: nudge refresh even if button wasn't ajax-enabled
  $(document).on('click', '.related .add_to_cart_button, .upsells .add_to_cart_button, .cross-sells .add_to_cart_button', function(){
    refreshFragments(false);
  });

  // Woo Blocks (TT25): some blocks dispatch custom events; listen if present
  ['wc-blocks_added_to_cart','wc-blocks_item_added','wc-cart-change'].forEach(function(evt){
    window.addEventListener(evt, function(){ refreshFragments(true); }, { passive: true });
    document.addEventListener(evt, function(){ refreshFragments(true); }, { passive: true });
  });

  // Keep any open state if your sticky cart is a modal/toggle
  $(document.body).on('wc_fragments_refreshed', function(){
    var root = $('{STICKY_CART_SELECTOR}');
    if (!root.length) return;
    // Example: preserve "open" state on a data attribute or class
    // (Adjust if your UI uses a different signal)
    var wasOpen = root.is('[data-open="1"]') || root.hasClass('is-open');
    if (wasOpen) {
      root.attr('data-open','1').addClass('is-open');
    }
  });

  // If landing with ?add-to-cart= in URL (non-ajax flow), refresh once.
  if (/[?&]add-to-cart=/.test(window.location.search)) {
    refreshFragments(true);
  }
})(jQuery);
JS;
    // Inject after Woo's fragments script so our listeners are registered last.
    wp_add_inline_script( 'wc-cart-fragments', str_replace('{STICKY_CART_SELECTOR}', esc_js(STICKY_CART_SELECTOR), $js), 'after' );
}, 20 );
