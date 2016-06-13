<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.altertech.it/
 * @since    1.2.3
 *
 * @package    Woocommerce_Alter_Inventory
 * @subpackage Woocommerce_Alter_Inventory/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woocommerce_Alter_Inventory
 * @subpackage Woocommerce_Alter_Inventory/admin
 * @author     Bigbabert <bigbabert@gmail.com>
 */
class Woocommerce_Alter_Inventory_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.2.3
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.2.3
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

        /**
         * Instance of this class.
         *
         * @since    1.2.3
         *
         * @var      object
         */
        protected static $instance = null;
        
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.2.3
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

                $plugin = Woocommerce_Alter_Inventory_Public::get_instance();
                $this->plugin_slug = $plugin->get_plugin_slug();
		$this->plugin_name = $plugin->get_plugin_name();
		$this->version = $plugin->get_plugin_version();
		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'alter_add_action_links' ) );
        }
    /**
     * Bootstraps the class and hooks required actions & filters.
     *
     */
    public static function init() {
        add_filter('woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50);
        add_action('woocommerce_settings_tabs_alter_inventory_tab', __CLASS__ . '::settings_tab');
        add_action('woocommerce_update_options_alter_inventory_tab', __CLASS__ . '::update_settings');
        include_once(ABSPATH . 'wp-includes/pluggable.php');
        //User Role Based Options
        if (current_user_can('manage_options')) {
        $pay_gtateway = get_option('wc_alter_inventory_tab_payament_section');
        $alter_bill_ship = get_option('alter_inventory_bill_ship_section');
            if ($alter_bill_ship == "yes") {
            self::alter_inventory_bill_ship();
            }
            if ($pay_gtateway) {
            add_filter( 'woocommerce_available_payment_gateways', __CLASS__ . '::alter_unsets_payment_gateways' );  
            }
        }
        //Shortcodes Options        
        if (get_option('alter_inventory_shortcodes_section') == "yes") {
           add_shortcode( 'alter-inventory', __CLASS__ . '::get_alter_inventory_front' ); 
           add_shortcode( 'alter-report', __CLASS__ . '::shortcode_altereports_func' ); 
        }         
    }

    /**
     * Add a new settings tab to the WooCommerce settings tabs array.
     *
     * @param array $settings_tabs Array of WooCommerce setting tabs & their labels, excluding the Subscription tab.
     * @return array $settings_tabs Array of WooCommerce setting tabs & their labels, including the Subscription tab.
     */
    public static function add_settings_tab( $settings_tabs ) {
        $settings_tabs['alter_inventory_tab'] = __( 'Alter Inventory', 'woocommerce-alter-inventory' );
        return $settings_tabs;
    }
    /**
     * Uses the WooCommerce admin fields API to output settings via the @see woocommerce_admin_fields() function.
     *
     * @uses woocommerce_admin_fields()
     * @uses self::get_settings()
     */
    public static function settings_tab() {
        woocommerce_admin_fields( self::get_settings() );
    }
    /**
     * Get all the settings for this plugin for @see woocommerce_admin_fields() function.
     *
     * @return array Array of settings for @see woocommerce_admin_fields() function.
     */
    public static function get_settings() {
        $settings = array(
            'section_title' => array(
                'name' => __('Welcome in WooCommerce Alter Inventory Settings', 'woocommerce-alter-inventory'),
                'type' => 'title',
                'desc' => __('Here you can manage almost all thinks about your Alter Inventory pages and options', 'woocommerce-alter-inventory'),
                'id' => 'alter_inventory_section_title',
            ),
            'shortcodes_section' => array(
                'name' => __('Enable shortcodes', 'woocommerce-alter-inventory'),
                'type' => 'checkbox',
                'desc' => __('If checked allow you to use this shortcodes in yuor pages: ' . '</br>'.'copy and paste to use'.'<pre><b><p>[alter-inventory]</p><p>[alter-report]</p></b></pre>', 'woocommerce-alter-inventory'),
                'desc_tip' => true,
                'std' => 'yes', // WooCommerce < 2.0
                'default' => 'yes', // WooCommerce >= 2.0
                'id' => 'alter_inventory_shortcodes_section'
            ),
            'bill_ship_section' => array(
                'name' => __('Disable Billing/Shipping', 'woocommerce-alter-inventory'),
                'type' => 'checkbox',
                'desc' => __('If checked disable Billing and Shipping fields on WooCommerce Cart Page, for user with manage role', 'woocommerce-alter-inventory'),
                'desc_tip' => true,
                'std' => 'yes', // WooCommerce < 2.0
                'default' => 'yes', // WooCommerce >= 2.0
                'id' => 'alter_inventory_bill_ship_section'
            ),
            'payament_section' => array(
                'title' => __('Select Payament Checkout', 'woocommerce-alter-inventory'),
                'desc' => __('Select Payament method for Alter Inventory Manager user role', 'woocommerce-alter-inventory'),
                'desc_tip' => __('Select Payament method for Alter Inventory Manager user role', 'woocommerce-alter-inventory'),
                'desc_tip' => true,
                'type' => 'select',
                'default' => 'allow_all',
                'class' => 'select_pay',
                'options' => array(
                    'allow_all' => __('Select All', 'woocommerce'),
                    'paypal' => __('PayPal Standard', 'woocommerce'),
                    'cheque' => __('Cheque Payments', 'woocommerce'),
                    'cod' => __('Cash on Delivery', 'woocommerce'),
                    'bacs' => __('Bank Transfer (BACS)', 'woocommerce')
                ), // array of options for select/multiselects only
                'id' => 'wc_alter_inventory_tab_payament_section'
            ),
            'section_end' => array(
                'type' => 'sectionend',
                'id' => 'wc_alter_inventory_tab_section_end'
            ),
        );
        return apply_filters('wc_alter_inventory_tab_settings', $settings);
    }
    /**
     * Uses the WooCommerce options API to save settings via the @see woocommerce_update_options() function.
     *
     * @uses woocommerce_update_options()
     * @uses self::get_settings()
     */
    public static function update_settings() {
        woocommerce_update_options( self::get_settings() );
    }
    /**
    * Add settings action link to the plugins page.
    *
    * @since    1.0.0
    */
    public function alter_add_action_links( $links ) {
            return array_merge(
                            array(
                            'settings' => '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=alter_inventory_tab' ) . '">' . __( 'Settings' ) . '</a>',
                            'donate' => '<a href="https://www.eatscode.com/" target="_blank" >' . __( 'Donate', $this->plugin_slug ) . '</a>'
                            ), $links
            );
    }   
/**
 *  This function will remove all of the WooCommerce standard gateways from the 
 *  WooCommerce > Settings > Checkout dashboard.
 */
 public static function alter_unsets_payment_gateways( $available_gateways) {
	$remove_gateways = array( 
                    'paypal' => 'WC_Gateway_Paypal' ,
                    'cheque' => 'WC_Gateway_Cheque',
                    'cod' => 'WC_Gateway_COD',
                    'bacs' => 'WC_Gateway_BACS'
	);
        $pay_gtateway = get_option('wc_alter_inventory_tab_payament_section');
            foreach($remove_gateways as $key_set => $gateway_remove){
                    if ( $key_set != $pay_gtateway && $pay_gtateway !=  "allow_all") {
                    //print( $key_set );
                    unset( $available_gateways[$key_set]);
                    //print( $gateway_remove );
                    } 
		}                    
            /**
             * WooCommerce - Disable Payments
             */
            return $available_gateways;
	}               
        /*
        * disable entering of billing& shipping address on the checkout page
        */
        public static function alter_inventory_bill_ship() {   
                add_action('woocommerce_checkout_init', 'alter_disable_billing_shipping');

                function alter_disable_billing_shipping($checkout) {

                    $checkout->checkout_fields['billing'] = array();
                    $checkout->checkout_fields['shipping'] = array();
                    return $checkout;
                    /**
                     *
                     * Make "state" field not required on checkout
                     *
                     */
                    add_filter('woocommerce_billing_fields', 'woo_filter_state_billing', 10, 1);
                    add_filter('woocommerce_shipping_fields', 'woo_filter_state_shipping', 10, 1);

                    function woo_filter_state_billing($address_fields) {
                        $address_fields['billing_state']['required'] = false;
                    }

                    function woo_filter_state_shipping($address_fields) {
                        $address_fields['shipping_state']['required'] = false;
                    }
                }
                
        }

    /**
     * Add shortcodes WooCommerce Alter Inventory pages.
     *
     * @param [alter-inventory] / [alter-report].
     * @return Pages with inventory and reports.
     */        
    public function get_alter_inventory_front() {
            $out = '<div class="at_disllowed"><h1 style="color:#F00">'. __('This section is not allowed to users. Please contact the administrator to request the access!', 'woocommerce-alter-inventory' ).'</h1>';
            $user = wp_get_current_user();
            if (empty($user->ID)) {
                echo $out;
            }
            if (!is_user_logged_in()) {
                wp_login_form();
                echo "</div>";
            } else {
            global $woocommerce, $woo_options, $wp;
            $get_current_url = home_url(add_query_arg(array(),$wp->request));
            $parse_url = parse_url($get_current_url);
            $current_url = $parse_url['scheme'].'://'.$parse_url['host'].$parse_url['path'];
            
                //Change text in ceckout
                add_action('woocommerce_order_button_text', 'alter_custom_checkout_text');

                function alter_custom_checkout_text() {
                    $text = __('Conclude Sale', 'woocommerce-alter-inventory');
                    return $text;
                }
            include_once( 'partials/woocommerce-alter-inventory-shortcode-display.php' );

            }
        }
        //Alterreports shortcode
        public function shortcode_altereports_func() {

        include_once ABSPATH . 'wp-admin/includes/plugin.php';
        include_once (ABSPATH . WPINC . '/functions.php');

        global $wpdb, $woocommerce, $WC_Order, $woo_options, $WC_API_Reports, $WC_Admin_Dashboard, $WC_Admin_Reports, $WC_Admin_Report, $WC_Report_Customers, $WC_Report_Stock, $WC_alterinventory;


            $out = '<div class="at_disllowed"><h1 style="color:#F00">'. __('This section is not allowed to users. Please contact the administrator to request the access!', 'woocommerce-alter-inventory' ).'</h1>';
            $user = wp_get_current_user();
            if (empty($user->ID)) {
                echo $out;
            }
            if (!is_user_logged_in()) {
                wp_login_form();
                echo "</div>";
            } else {
            ?>
            <?php
            /**
             * Template for Direct Sells
             */
            if (!defined('ABSPATH')) {
                exit;
            }

            global $wpdb, $Product, $item, $item_meta, $product, $woocommerce, $woo_options, $order_count, $WC_API_Reports, $WC_Admin_Dashboard, $WC_Admin_Reports, $WC_Admin_Report, $WC_Report_Customers, $WC_Report_Stock, $WC_alterinventory;


            $customer_orders = get_posts(apply_filters('woocommerce_my_account_my_orders_query', array(
                'numberposts' => $order_count,
                'meta_key' => '_customer_user',
                'meta_value' => get_current_user_id(),
                'post_type' => 'shop_order',
                'post_status' => array('wc-processing', 'wc-completed'),
                'posts_per_page' => -1,
                'paged' => get_query_var('paged')
            )));

            if ($customer_orders) :
                ?>
             <div class="alter_reports_page">
             <div class="alter_inventory_header">
                <div style="margin-bottom: 20px;float: right;text-align: right;display: inline-block" >
                    <div style="margin-bottom: 20px;" >
                <?php
                $form = '<form role="search" method="get" id="searchform" action="' . esc_url(home_url('/')) . '">
                    <div>
                    <label class="screen-reader-text" for="s">' . __('Search Sales', 'woocommerce-alter-inventory') . ' :</label>
                    <input type="text" value="' . get_search_query() . '" name="s" id="s" placeholder="' . __('Sales..', 'woocommerce-alter-inventory') . '" />
                    <input class="button" type="submit" id="searchsubmit" value="' . esc_attr__('Search', 'woocommerce-alter-inventory') . '" />
                    <input type="hidden" name="post_type" value="product" />
                    </div>
                    </form>';
                echo $form;
                ?>
                    </div>
                <script type="text/javascript">
                    jQuery(document).ready(function() {  
                        jQuery("#myform").submit(function(e){
                            e.preventDefault();
                            jQuery.post(yes.ajaxurl,{action : 'doit'}, function( response) {
                            //jQuery("#searchtextbox").val(response);
                            jQuery("#result").append(response);
                            jQuery("#textarea").html(response);
                            return false;
                            });//end of function
                        }); // submit form function finishing here
                    }); //end of main loop
                    function printPage(){
                    var tableData = '<table border="1">'+document.getElementsByTagName('table')[0].innerHTML+'</table>';
                    var data = '<button onclick="window.print()"><?php echo __('Print', 'woocommerce-alter-inventory'); ?></button>'+tableData;
                    myWindow=window.open('','','width=1000,height=800px');
                    myWindow.innerWidth = screen.width;
                    myWindow.innerHeight = screen.height;
                    myWindow.screenX = 0;
                    myWindow.screenY = 0;
                    myWindow.document.write(data);
                    myWindow.focus();
                    };
                </script>
                <input type="submit" class="button wc-success"  id="printPage" onclick="printPage();" value="<?php echo __('Print', 'woocommerce-alter-inventory'); ?>" />
                </div>
            </div>
                                        <h2><?php __('Sales..', 'woocommerce-alter-inventory'); ?></h2>

                                <table class="shop_table my_account_orders" style="border: 1px solid #dedede;">
                                    <thead>
                                        <tr>
                                            <th class="order-number"><span class="nobr"><?php _e('#ID Sale', 'woocommerce-alter-inventory'); ?></span></th>
                                            <th class="order-date"><span class="nobr"><?php _e('Date', 'woocommerce-alter-inventory'); ?></span></th>
                                            <th class="order-total"><span class="nobr"><?php _e('Total', 'woocommerce-alter-inventory'); ?></span></th>
                                            <th class="order-total"><span class="nobr"><?php _e('Status', 'woocommerce-alter-inventory'); ?></span></th>
                                            <th class="order-actions"><span class="nobr"><?php _e('Products / Attributs', 'woocommerce-alter-inventory'); ?></span></th>
                                            <th class="order-actions"><span class="nobr"><?php _e('Details', 'woocommerce-alter-inventory'); ?></span></th>
                                            <th class="order-actions"><span class="nobr"><?php _e('Cancel', 'woocommerce-alter-inventory'); ?></span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                <?php
                                    foreach ($customer_orders as $customer_order) {
                                    $order = new WC_Order();

                                    $order->populate($customer_order);

                                    $item_count = $order->get_item_count();
                                ?>
                                <tr class="order">
                                    <td class="order-number" style="text-align:center;">
                                        <a href="<?php echo $order->get_view_order_url(); ?>">
                                        <?php echo $order->get_order_number(); ?>
                                        </a>
                                    </td>
                                    <td class="order-date"><time datetime="<?php echo date('Y-m-d', strtotime($order->order_date)); ?>" title="<?php echo esc_attr(strtotime($order->order_date)); ?>"><?php echo date_i18n(get_option('date_format'), strtotime($order->order_date)); ?></time></td>
                                    <td class="order-total" >
                                        <?php echo sprintf(_n('<strong>%s</strong> x <strong>%s Product</strong>', '<strong>%s</strong> x <strong>%s Products</strong>', $item_count, 'woocommerce'), $order->get_formatted_order_total(), $item_count); ?>
                                    </td>
                                    <td class="order-date"><?php echo $order->status; ?></td>
                                    <td class="order-actions" style="text-align: left; padding-left: 4%;border-left: 1px solid #dedede;border-right: 1px solid #dedede;">
                                        <table class="shop_table order_details">
                                            <thead>
                                                <tr>
                                                <th class="product-name" style="text-align:left;"><?php _e('Product', 'woocommerce-alter-inventory'); ?></th>
                                                <th class="product-total" style="text-align:center;"><?php _e('Total', 'woocommerce-alter-inventory'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                        <?php
                                        if (sizeof($order->get_items()) > 0) {

                                            foreach ($order->get_items() as $item) {
                                                $_product = apply_filters('woocommerce_order_item_product', $order->get_product_from_item($item), $item);
                                                $item_meta = new WC_Order_Item_Meta($item['item_meta'], $_product);
                                                ?>
                                                                                                <tr class="<?php echo esc_attr(apply_filters('woocommerce_order_item_class', 'order_item', $item, $order)); ?>">
                                                                                                <td  style="text-transform:uppercase;">
                                                <?php
                                                if ($_product && !$_product->is_visible())
                                                    echo apply_filters('woocommerce_order_item_name', $item['name'], $item);
                                                else
                                                    echo apply_filters('woocommerce_order_item_name', sprintf('<a href="%s">%s</a>', get_permalink($item['product_id']), $item['name']), $item);

                                                echo apply_filters('woocommerce_order_item_quantity_html', ' <strong class="product-quantity">' . sprintf('&times; %s', $item['qty']) . '</strong>', $item);

                                                $item_meta->display();

                                                if ($_product && $_product->exists() && $_product->is_downloadable() && $order->is_download_permitted()) {

                                                    $download_files = $order->get_item_downloads($item);
                                                    $i = 0;
                                                    $links = array();

                                                    foreach ($download_files as $download_id => $file) {
                                                        $i++;

                                                        $links[] = '<small><a href="' . esc_url($file['download_url']) . '">' . sprintf(__('Download file%s', 'woocommerce'), ( count($download_files) > 1 ? ' ' . $i . ': ' : ': ')) . esc_html($file['name']) . '</a></small>';
                                                    }

                                                     implode('<br/>', $links);
                                                }
                                                ?>
                                                                                                        </td>
                                                                                                        <td class="product-total" style="border-left: 1px solid #dedede;">
                                                <?php echo $order->get_formatted_line_subtotal($item); ?>
                                                                                                        </td>
                                                                                                </tr>

                                                <?php
                                            }
                                        }
                                        ?>
                                                </tbody>
                                            </table>
                                        </td>
                                        <td class="order-actions">
                                            <?php
                                            $actions = array();

                                            $actions['view'] = array(
                                                'url' => $order->get_view_order_url(),
                                                'name' => __('Details', 'woocommerce-alter-inventory')
                                            );

                                            $actions = apply_filters('woocommerce_my_account_my_orders_actions', $actions, $order);

                                            if ($actions) {
                                                foreach ($actions as $key => $action) {
                                                    echo '<a href="' . esc_url($action['url']) . '" class="button details_to_cart_button product_type_simple ' . sanitize_html_class($key) . '">' . esc_html($action['name']) . '</a>';
                                                }
                                            }
                                            ?>
                                        </td>
                                        <td class="order-actions">
                                            <?php
                                            $actions = array();
                                            $actions['cancel'] = array(
                                                'url' => $order->get_cancel_order_url(get_permalink(wc_get_page_id('alter-inventory'))),
                                                'name' => __('Cancel', 'woocommerce-alter-inventory')
                                            );
                                            if ($actions) {
                                                foreach ($actions as $key => $action) {
                                                    echo '<a href="' . esc_url($action['url']) . '" class="button delete_to_cart_button ' . sanitize_html_class($key) . '">' . esc_html($action['name']) . '</a>';
                                                }
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php
                            }
                            ?>
                                </tbody>
                            </table>
             </div>
                                                    <?php
                                                    endif;
                                                }
                                            }

                                            /**
	 * Register the stylesheets for the admin area.
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
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woocommerce-alter-inventory-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

            //wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woocommerce-alter-inventory-admin.js', array( 'jquery' ), $this->version, false );

	}

}
