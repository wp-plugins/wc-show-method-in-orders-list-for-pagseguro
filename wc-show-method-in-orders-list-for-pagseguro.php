<?php
/*
Plugin Name: WC Show Method in Orders List for PagSeguro
Plugin URI: https://wordpress.org/plugins/wc-show-method-in-orders-list-for-pagseguro/
Description: Este plugin atualiza o método de pagamento junto com a descrição do gateway utilizado na listagem de pedidos após a confirmação do pagamento.
Version: 1.0
Author: dimdavid
Author URI: http://dimdavid.wordpress.com/
License: GPLv2 or later
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_Show_Method_In_Orders' ) ) :

class WC_Show_Method_In_Orders {

	const VERSION = '1.0.0';
	protected static $instance = null;

	private function __construct() {
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.1', '>=' ) ) {
			if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
				$this->admin_includes();
			}
			add_action( 'woocommerce_payment_complete', array( $this, 'update_gateway_info' ), 10 );
			$this->includes();
		} else {
			add_action( 'admin_notices', array( $this, 'woocommerce_is_missing_notice' ) );
		}	
	}

	public function woocommerce_is_missing_notice() {
		echo '<div class="error"><p><strong>As descrições de métodos internos de pagamento</strong> funcionam apenas para instalações superiores à versão 2.1 do <a href="http://wordpress.org/plugins/woocommerce/">WooCommerce</a></p></div>';
	}
	
	public function includes(){
	}
	
	public function admin_includes(){
	}
	
	public static function get_instance() {

		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		
		return self::$instance;
	}
	
	public function update_gateway_info($order_id){
		$data = get_post_meta( $order_id, '_wc_pagseguro_payment_data', true );
		if (isset( $data['method'] )){
			$title = get_post_meta( $order_id, '_payment_method_title', true );
			$title = $data['method'] . ' com ' . $title;
			update_post_meta($order_id, '_payment_method_title', $title);
		}		
	}
	
}

add_action( 'plugins_loaded', array( 'WC_Show_Method_In_Orders', 'get_instance' ) );
	
endif;