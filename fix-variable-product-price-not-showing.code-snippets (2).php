<?php

/**
 * fix variable product price not showing
 */
/**
 * Fix "0" price for variable products in product cards / custom loops.
 * - Works even if the layout reads $product->get_price() OR raw _price meta.
 * - Leaves the single product page untouched.
 * - Also re-builds price HTML for variable products in cards.
 */

if ( ! function_exists( 'lureen_is_loop_context' ) ) {
    function lureen_is_loop_context() {
        // Any front-end context that is NOT the single product page
        return ( ! is_admin() || wp_doing_ajax() ) && ( ! function_exists( 'is_product' ) || ! is_product() );
    }
}

if ( ! function_exists( 'lureen_min_var_price' ) ) {
    function lureen_min_var_price( WC_Product $product, $which = 'price' ) {
        $incl_tax = true; // respect display incl./excl. tax
        $min      = $product->get_variation_price( 'min', $incl_tax );
        $min_reg  = $product->get_variation_regular_price( 'min', $incl_tax );
        $min_sale = $product->get_variation_sale_price( 'min', $incl_tax );

        switch ( $which ) {
            case 'regular': return $min_reg ?: $min;
            case 'sale':    return $min_sale ?: $min;
            default:        return $min ?: $min_reg ?: $min_sale;
        }
    }
}

/* 1) Force correct value for get_price()/regular/sale on parent variable products (cards/loops). */
add_filter( 'woocommerce_product_get_price', 'lureen_fix_parent_var_get_price', 9999, 2 );
add_filter( 'woocommerce_product_get_regular_price', 'lureen_fix_parent_var_get_regular', 9999, 2 );
add_filter( 'woocommerce_product_get_sale_price', 'lureen_fix_parent_var_get_sale', 9999, 2 );

function lureen_fix_parent_var_get_price( $price, $product ) {
    if ( lureen_is_loop_context() && $product instanceof WC_Product && $product->is_type( 'variable' ) ) {
        if ( $price === '' || (float) $price == 0 ) {
            $price = lureen_min_var_price( $product, 'price' );
        }
    }
    return $price;
}
function lureen_fix_parent_var_get_regular( $price, $product ) {
    if ( lureen_is_loop_context() && $product instanceof WC_Product && $product->is_type( 'variable' ) ) {
        if ( $price === '' || (float) $price == 0 ) {
            $price = lureen_min_var_price( $product, 'regular' );
        }
    }
    return $price;
}
function lureen_fix_parent_var_get_sale( $price, $product ) {
    if ( lureen_is_loop_context() && $product instanceof WC_Product && $product->is_type( 'variable' ) ) {
        if ( $price === '' || (float) $price == 0 ) {
            $price = lureen_min_var_price( $product, 'sale' );
        }
    }
    return $price;
}

/* 2) Some builders read the raw _price meta on the parent product (ugh!).
      Intercept that and return the min variation price instead (cards only). */
add_filter( 'get_post_metadata', 'lureen_fix_raw_price_meta_for_cards', 10, 4 );
function lureen_fix_raw_price_meta_for_cards( $value, $object_id, $meta_key, $single ) {
    if ( ! lureen_is_loop_context() || '_price' !== $meta_key ) {
        return $value;
    }
    $product = wc_get_product( $object_id );
    if ( $product && $product->is_type( 'variable' ) ) {
        $min = lureen_min_var_price( $product, 'price' );
        if ( $min > 0 ) {
            // Short-circuit meta: return our computed min price.
            return $single ? $min : array( $min );
        }
    }
    return $value;
}

/* 3) Ensure the price HTML shown in cards is never "0" for variable products. */
add_filter( 'woocommerce_get_price_html', 'lureen_fix_price_html_for_cards', 99, 2 );
function lureen_fix_price_html_for_cards( $html, $product ) {
    if ( ! lureen_is_loop_context() || ! ( $product instanceof WC_Product ) ) {
        return $html;
    }
    if ( $product->is_type( 'variable' ) ) {
        $min     = $product->get_variation_price( 'min', true );
        $max     = $product->get_variation_price( 'max', true );
        $min_reg = $product->get_variation_regular_price( 'min', true );
        $min_sale= $product->get_variation_sale_price( 'min', true );

        if ( $min <= 0 && $max <= 0 ) {
            return $html;
        }

        // Build core-like HTML (sale handling + range).
        $from_html = ( $min_sale && $min_sale < $min_reg )
            ? wc_format_sale_price( wc_price( $min_reg ), wc_price( $min_sale ) )
            : wc_price( $min );

        return ( $min !== $max )
            ? sprintf( '%s – %s', $from_html, wc_price( $max ) )
            : $from_html;
    }
    return $html;
}
