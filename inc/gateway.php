<?php
if( ! defined('ABSPATH') ) die('Not Allowed');

add_filter('woocommerce_payment_gateways', 'add_partially_gateway');

add_filter('woocommerce_available_payment_gateways', 'maybe_remove_partially');

add_action( 'woocommerce_blocks_loaded', 'partially_block_support' );

function partially_block_support() {
  if ( class_exists( 'Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType' ) ) {
    require_once PARTIALLY_PATH . "/classes/partially/blocks.php";

    add_action(
      'woocommerce_blocks_payment_method_type_registration',
      function( Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry ) {
        $payment_method_registry->register( new Partially_Blocks );
      }
    );
  }
}

function add_partially_gateway($methods) {
    $methods[] = 'WC_Gateway_Partially';
    return $methods;
}

function maybe_remove_partially($gateways) {
  // make sure the WC cart is available
  $wc_cart = WC()->cart;
  if (is_admin() || ! $wc_cart) return $gateways;

  $hasDisabled = false;

  // check for a global Partially min
  $cartTotal = $wc_cart->get_total(null);
  $gateway = WC_Gateway_Partially::instance();
  $globalMin = $gateway->get_option('min_amount');
  if ( ! empty($globalMin) && $globalMin > 0 && $cartTotal < $globalMin) $hasDisabled = true;

  // check for a global Partially max
  $globalMax = $gateway->get_option('max_amount');
  if ( ! empty($globalMax) && $globalMax > 0 && $cartTotal > $globalMax) $hasDisabled = true;

  // check for products in the cart with Partially disabled
  foreach ($wc_cart->get_cart() as $cart_item_key => $item) {
    if (get_post_meta( $item['product_id'], 'partially_disabled', true ) == 'yes') {
      $hasDisabled = true;
    }
  }

  if ($hasDisabled) unset($gateways['partially']);

  return $gateways;
}
