<?php

/**
 * Sign up and empty check out handler
 */
/**
 * Helper: better empty-checkout handling + enable sign-up on My Account
 * - Redirect empty checkout to cart with a friendly notice.
 * - Force WooCommerce to show the native registration form on the My Account page.
 */

/**
 * Utility: detect “checkout-like” page for this site
 * - Works for both native Woo checkout and the custom “checkout-new” page your main snippet creates.
 */
if ( ! function_exists( 'lureen_helper_is_checkout_like' ) ) {
	function lureen_helper_is_checkout_like() : bool {
		// Native Woo checkout?
		if ( function_exists( 'is_checkout' ) && is_checkout() ) {
			return true;
		}
		// Custom checkout page created by your main snippet?
		$ids = get_option( 'lureen_custom_pages' );
		if ( is_array( $ids ) && ! empty( $ids['checkout'] ) && is_page( (int) $ids['checkout'] ) ) {
			return true;
		}
		return false;
	}
}

/**
 * 1) Better handling when cart is empty and user hits "إتمام الشراء"
 * - Avoids blank/empty screens.
 * - Allows order-pay / order-received endpoints to pass through.
 */
add_action( 'template_redirect', function () {
	if ( ! function_exists( 'WC' ) || ! lureen_helper_is_checkout_like() ) {
		return;
	}

	// Let Woo’s special endpoints through (e.g., after payment).
	if ( function_exists( 'is_wc_endpoint_url' ) ) {
		$allow_endpoints = array( 'order-pay', 'order-received', 'add-payment-method' );
		foreach ( $allow_endpoints as $ep ) {
			if ( is_wc_endpoint_url( $ep ) ) {
				return;
			}
		}
	}

	$cart = WC()->cart ?? null;
	if ( ! $cart || $cart->is_empty() ) {
		// Friendly notice in Arabic
		if ( function_exists( 'wc_add_notice' ) ) {
			wc_add_notice( 'سلة التسوق فارغة — لا يمكنك المتابعة إلى الدفع.', 'error' );
		}
		$cart_url = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/cart' );
		wp_safe_redirect( $cart_url );
		exit;
	}
} );

/**
 * 2) Show native WooCommerce registration form on the My Account page
 * - Forces the “Enable registration on the My account page” option to “yes”
 *   without changing settings in the DB.
 * - Woo will render the built-in sign-up form next to the login form.
 */
add_filter( 'pre_option_woocommerce_enable_myaccount_registration', function( $pre ) {
	// Force-enable registration on My Account page.
	return 'yes';
});

/**
 * (Optional but recommended) clarify registration UX with a gentle cue under login.
 * If your theme/template already shows the two-column login/register, this just adds a small hint.
 */
add_action( 'woocommerce_login_form_end', function () {
	if ( is_account_page() ) {
		echo '<p style="margin-top:12px; font-size:14px;">ليس لديك حساب؟ يمكنك إنشاء حساب جديد من النموذج المجاور.</p>';
	}
} );
