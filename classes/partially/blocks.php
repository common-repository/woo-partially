<?php
if( ! defined('ABSPATH') ) die('Not Allowed');

use Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType;

class Partially_Blocks extends AbstractPaymentMethodType {
    private $gateway;
	
	protected $name = 'partially';

	public function initialize() {
		// get payment gateway settings
		$this->settings = get_option( "woocommerce_{$this->name}_settings", array() );
	}

	public function is_active() {
		return ! empty( $this->settings[ 'enabled' ] ) && 'yes' === $this->settings[ 'enabled' ];
	}

	public function get_payment_method_script_handles() {

		wp_register_script(
			'wc-partially-blocks-integration',
			plugin_dir_url( PARTIALLY_PATH . '/woocommerce-partially.php' ) . 'build/index.js',
			array(
				'wc-blocks-registry',
				'wc-settings',
				'wp-element',
				'wp-html-entities',
			),
			null,
			true
		);

		return array( 'wc-partially-blocks-integration' );

	}

	public function get_payment_method_data() {
		return array(
			'title'        => $this->get_setting( 'title' ),
			'description'  => $this->get_setting( 'description' ),
            'icon' => 'https://d2nacfpe3n8791.cloudfront.net/images/glyph-gradient-sm.png'
		);
	}

}