<?php

/**
 * Hide all products from menu
 */
/**
 * Hide "جميع المنتجات" from Lureen custom layout
 * - Removes the nav item that points to the Store/All Products page
 * - (Optional) hides the H2 title inside the All Products page
 */
add_action('wp_footer', function () {
    if ( ! function_exists('lureen_get_pages_option')) return;

    $pages     = lureen_get_pages_option();
    $store_url = ! empty($pages['store']) ? get_permalink($pages['store']) : home_url('/shop');

    // Remove the "All Products" nav item by URL match
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
      var hrefs = [
        <?php echo json_encode(esc_url_raw($store_url)); ?>,
        <?php echo json_encode(esc_url_raw(trailingslashit($store_url))); ?>
      ];
      hrefs.forEach(function (h) {
        document.querySelectorAll('.lureen-custom-nav a[href="' + h + '"]').forEach(function (a) {
          var li = a.closest('li');
          if (li) li.remove();
        });
      });
    });
    </script>
    <?php
}, 99);

/* (اختياري) إخفاء عنوان H2 داخل صفحة "جميع المنتجات" نفسها */
add_action('wp_head', function () {
    if ( ! function_exists('lureen_get_pages_option')) return;

    $pages = lureen_get_pages_option();
    if ( ! empty($pages['store'])) {
        $store_id = (int) $pages['store'];
        echo '<style>body.page-id-' . $store_id . ' .lureen-section-title{display:none !important;}</style>';
    }
}, 101);
