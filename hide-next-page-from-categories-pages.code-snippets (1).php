<?php

/**
 * Hide next page from categories pages
 */
/**
 * Hide pagination (Next page + page numbers) on WooCommerce product category pages.
 * Paste into Code Snippets and run on Front-end only.
 */
add_action('wp', function () {
    if ( function_exists('is_product_category') && is_product_category() ) {
        // Remove WooCommerce's own outputs
        remove_action( 'woocommerce_after_shop_loop',  'woocommerce_pagination', 10 );
        remove_action( 'woocommerce_before_shop_loop', 'woocommerce_pagination', 10 );
        remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
    }
});

add_action('wp_enqueue_scripts', function () {
    if ( function_exists('is_product_category') && is_product_category() ) {
        // Fallback: hide any theme/WordPress pagination markup/text
        $css = '
        nav.woocommerce-pagination,
        .woocommerce nav.woocommerce-pagination,
        .woocommerce .woocommerce-pagination,
        .woocommerce .page-numbers,
        .woocommerce .pagination,
        .woocommerce .nav-links,
        .navigation.pagination,
        .page-numbers,
        .nav-links,
        .wp-block-query-pagination { display:none !important; }';
        wp_register_style('hide-cat-pagination', false);
        wp_enqueue_style('hide-cat-pagination');
        wp_add_inline_style('hide-cat-pagination', $css);
    }
});
