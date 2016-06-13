<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.altertech.it/
 *  Version: 1.2.3
 * @package           Woocommerce_Alter_Inventory
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce - Alter Inventory
 * Plugin URI:        http://www.altertech.it/woocommerce-alter-inventory/
 * Description:       This plugin display all your Woocommerce inventory products and variable products as variation, in user friendly mode on front-end in a reserved page, you can create this page simply adding a shortcode [alterinventory] to a new page. You also can simply use your woocommerce to make CMR for direct sells and keep all report of yours sells in a page with shortcode [altereports]. Tested on Wordpress Versione 4.5.2 and Woocommerce 2.5.5
 * Version:           1.2.3
 * Author:            Alberto Cocchiara
 * Author URI:        http://www.altertech.it/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woocommerce-alter-inventory
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-woocommerce-alter-inventory-activator.php
 */
function activate_woocommerce_alter_inventory() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-alter-inventory-activator.php';
	Woocommerce_Alter_Inventory_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-woocommerce-alter-inventory-deactivator.php
 */
function deactivate_woocommerce_alter_inventory() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-alter-inventory-deactivator.php';
	Woocommerce_Alter_Inventory_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_woocommerce_alter_inventory' );
register_deactivation_hook( __FILE__, 'deactivate_woocommerce_alter_inventory' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-alter-inventory.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.2.3
 */
function run_woocommerce_alter_inventory() {

	$plugin = new Woocommerce_Alter_Inventory();
	$plugin->run();

}
/**
 * Check if WooCommerce is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    run_woocommerce_alter_inventory();
    Woocommerce_Alter_Inventory_Admin::init();

} else {
    	function check_woo_notices_alter_inventory($message) {
                        $message = "<strong>". __('WARNING', 'woocommerce-alter-inventory') ."</strong>:". __('WooCommerce is not active and Alter Inventory can not work!', 'woocommerce-alter-inventory'); 
			ob_start();
			?><div class="error">
			<h1 style="color:#F00"><p><?= $message ?></p></h1>
			</div><?php
			echo ob_get_clean();
	}
	add_action('admin_notices', 'check_woo_notices_alter_inventory');
}
