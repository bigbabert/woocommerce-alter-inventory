<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.altertech.it/
 * @since    1.2.3
 *
 * @package    Woocommerce_Alter_Inventory
 * @subpackage Woocommerce_Alter_Inventory/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woocommerce_Alter_Inventory
 * @subpackage Woocommerce_Alter_Inventory/public
 * @author     Bigbabert <bigbabert@gmail.com>
 */
class Woocommerce_Alter_Inventory_Public {

        /**
         * Plugin version, used for cache-busting of style and script file references.
         *
         * @since   1.2.3
         *
         * @var     string
         */
        const VERSION = '1.2.3';

        /**
         *
         * Unique identifier for your plugin.
         *
         *
         * The variable name is used as the text domain when internationalizing strings
         * of text. Its value should match the Text Domain file header in the main
         * plugin file.
         *
         * @since    1.2.3
         *
         * @var      string
         */
        protected static $plugin_slug = 'woocommerce-alter-inventory';

        /**
         *
         * Unique identifier for your plugin.
         *
         *
         * @since    1.2.3
         *
         * @var      string
         */
        protected static $plugin_name = 'Woocommerce_Alter_Inventory';

        /**
         * Instance of this class.
         *
         * @since    1.2.3
         *
         * @var      object
         */
        protected static $instance = null;

        /**
         * Initialize the plugin by setting localization and loading public scripts
         * and styles.
         *
         * @since     1.0.0
         */
	public function __construct() {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.2.3
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woocommerce_Alter_Inventory_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woocommerce_Alter_Inventory_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
                include_once(ABSPATH . 'wp-includes/pluggable.php');
                if (get_option('alter_inventory_bill_ship_section') == "yes") {
                    if (current_user_can('manage_options')) {
                    wp_enqueue_style( $this->plugin_name.'_remove_fields', plugin_dir_url( __FILE__ ) . 'css/woocommerce-alter-inventory-public_remove_fields.css', array(), $this->version, 'all' );
                   }
                } 
                if (get_option('alter_inventory_shortcodes_section') == "yes") {
                     wp_enqueue_style( $this->plugin_name.'_public', plugin_dir_url( __FILE__ ) . 'css/woocommerce-alter-inventory-public.css', array(), $this->version, 'all' );
                }
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.2.3
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woocommerce_Alter_Inventory_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woocommerce_Alter_Inventory_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
                if (get_option('alter_inventory_shortcodes_section') == "yes") {
                    if (current_user_can('manage_options')) {
		wp_enqueue_script( $this->plugin_name.'_bootstap_number', plugin_dir_url( __FILE__ ) . 'js/bootstrap-number-input.js', array( 'jquery' ), $this->version, true );
                wp_enqueue_script( $this->plugin_name.'_at_shortcode_js', plugin_dir_url( __FILE__ ) . 'js/woocommerce-alter-inventory-shortcode.js', array( 'jquery' ), $this->version, true );
                }
                
                    }		
	}
    /**
     * Return the plugin slug.
     *
     * @since    1.0.0
     *
     * @return    Plugin slug variable.
     */
    public function get_plugin_slug() {
        return self::$plugin_slug;
    }

    /**
     * Return the plugin name.
     *
     * @since    1.0.0
     *
     * @return    Plugin name variable.
     */
    public function get_plugin_name() {
        return self::$plugin_name;
    }

    /**
     * Return the version
     *
     * @since    1.0.0
     *
     * @return    Version const.
     */
    public function get_plugin_version() {
        return self::VERSION;
    }
    /**
     * Return an instance of this class.
     *
     * @since     1.0.0
     *
     * @return    object    A single instance of this class.
     */
    public static function get_instance() {

        // If the single instance hasn't been set, set it now.
        if ( null == self::$instance ) {
            self::$instance = new self;
        }

        return self::$instance;
    }

}
