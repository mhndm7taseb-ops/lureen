<?php

/**
 * Show single product name 
 */
/**
 * Helper: show product names again (single + related only)
 * - Leaves the original snippet’s behavior intact
 * - Restores visibility of titles that were hidden via global CSS
 */
add_action('wp_head', function () { ?>
  <style id="lureen-helper-show-product-names">
    /* Single product title (classic + block themes) */
    body.single-product .product .product_title.entry-title,
    body.single-product .entry-title.product_title,
    body.single-product .wp-block-post-title {
      display: block !important;
      visibility: visible !important;
    }

    /* Related / Upsells / Cross-sells product names (classic + blocks) */
    body.single-product .related ul.products li.product .woocommerce-loop-product__title,
    body.single-product .upsells ul.products li.product .woocommerce-loop-product__title,
    body.woocommerce-cart .cross-sells ul.products li.product .woocommerce-loop-product__title,
    body.single-product .related .wc-block-grid__product-title,
    body.single-product .related .wc-block-components-product-name {
      display: block !important;
      visibility: visible !important;
    }
  </style>
<?php }, 999);
