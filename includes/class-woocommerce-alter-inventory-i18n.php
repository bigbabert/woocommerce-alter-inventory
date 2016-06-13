<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://www.altertech.it/
 * @since    1.2.3
 *
 * @package    Woocommerce_Alter_Inventory
 * @subpackage Woocommerce_Alter_Inventory/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since    1.2.3
 * @package    Woocommerce_Alter_Inventory
 * @subpackage Woocommerce_Alter_Inventory/includes
 * @author     Bigbabert <bigbabert@gmail.com>
 */
class Woocommerce_Alter_Inventory_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.2.3
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'woocommerce-alter-inventory',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
