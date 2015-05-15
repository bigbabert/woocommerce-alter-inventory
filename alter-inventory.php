<?php

/*
Plugin Name: WooCommerce - Alter Inventory
Plugin URI: http://www.altertech.it/woocommerce-alter-inventory/
Description: This plugin display all your Woocommerce inventory products and variable products as variation, in user friendly mode on front-end in a reserved page, you can create this page simply adding a shortcode [alterinventory] to a new page. You also can simply use your woocommerce to make CMR for direct sells and keep all report of yours sells in a page with shortcode [altereports] .
Tested on Wordpress 4.2 and Woocommerce 2.2.8 
Version: 1.2.2
Author: Bigbabert
Author URI: http://www.blog.altertech.it

	Copyright: © 2014 Alberto Cocchiara (email : bigbabertgmail.com)
	License: GNU General Public License v3.0
	License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

// Check if WooCommerce is active
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	
	// Localization
	load_plugin_textdomain( 'woocommerce-alter-inventory', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	
	// Class
	if ( ! class_exists( 'WC_alterinventory') ) {
		class WC_alterinventory {
			public function __construct() {
				
				$this->alterinventory_enabled = get_option( 'alterinventory_enable' ) == 'yes' ? true : false;
				$this->altereports_enabled = get_option( 'altereports_enable' ) == 'yes' ? true : false;
				$this->alter_wp_login_form_enabled = get_option( 'alter_wp_login_form_enable' ) == 'yes' ? true : false;
				
				// called only after woocommerce has finished loading
				add_action( 'init', array( $this, 'plugin_init' ) );
				
	

				//disable entering of billing& shipping address on the checkout page
add_action('woocommerce_checkout_init','alter_disable_billing_shipping');
 
function alter_disable_billing_shipping($checkout){
 
 $checkout->checkout_fields['billing']=array();
 $checkout->checkout_fields['shipping']=array();
 return $checkout;
 
 
	

/**
 *
 * Make "state" field not required on checkout
 *
 */
 
add_filter( 'woocommerce_billing_fields', 'woo_filter_state_billing', 10, 1 );
add_filter( 'woocommerce_shipping_fields', 'woo_filter_state_shipping', 10, 1 );

function woo_filter_state_billing( $address_fields ) { 
	$address_fields['billing_state']['required'] = false;
}

function woo_filter_state_shipping( $address_fields ) { 
	$address_fields['shipping_state']['required'] = false;
}

/**
 * Add payment method to admin new order email
 *
 */
add_action( 'woocommerce_email_after_order_table', 'woo_add_payment_method_to_admin_new_order', 15, 2 ); 

function woo_add_payment_method_to_admin_new_order( $order, $is_admin_email ) { 
	if ( $is_admin_email ) { 
	echo '<p><strong>'.__('Payment Method','woocommerce-alter-inventory').':</strong> ' . $order->cod . '</p>'; 
	
	
   } 
  }	
 }			
				// Init settings
				$this->settings = array(
					array(
						'name' => __( 'Alter Inventory Options', 'woocommerce-alter-inventory' ),
						'type' => 'title',
						'id' => 'alterinventory_options'
					),
					array(
						'name' => __( 'Alter Inventory', 'woocommerce-alter-inventory' ),
						'desc' => __( 'Enable Alter Inventory, use shortcode: [alterinventory] in the page you will use like Front End Inventory.', 'woocommerce-alter-inventory' ),
						'id' => 'alterinventory_enable',
						'type' => 'checkbox'
					),
					array(
						'name' => __( 'Alter Reports', 'woocommerce-alter-inventory' ),
						'desc' => __( 'Enable Alter Reports, use shortcode: [altereports] in the page you will use like Front End Reports.', 'woocommerce-alter-inventory' ),
						'id' => 'altereports_enable',
						'type' => 'checkbox'
					),
					array(
						'name' => __( 'Enable Login', 'woocommerce-alter-inventory' ),
						'desc' => __( 'Enable Login in the Alter-Inventory Page', 'woocommerce-alter-inventory' ),
						'id' => 'alter_wp_login_form_enable',
						'type' => 'checkbox'
					),
					array(
						'name' => __( 'Enable Sell', 'woocommerce-alter-inventory' ),
						'desc_tip' => __( 'Enable Cart and Checkout in the Alter-Inventory Page, you well sell directly.', 'woocommerce-alter-inventory' ),
						'id' => 'alter_wp_seller_form_enable',
						'type' => 'select',
						'options' => array(
						'enable'  			=> __( 'Enable', 'woocommerce' ),
						'disable'	=> __( 'Disable', 'woocommerce' ),
						
					),
						'css' 		=> 'min-width:50px;',
						'desc' =>  __( 'Enable direct sell in Alter-Inventory Page', 'woocommerce-alter-inventory' ),
						
						
					),
					array(
						'name'     => __( 'Error Message', 'woocommerce-alter-inventory' ),
						'desc_tip' => __( 'Insert you Error Message.', 'woocommerce-alter-inventory' ),
						'id'       => 'alterinventory_error_message',
						'type'     => 'textarea',
						'css'      => 'min-width:500px;',
						'desc'     => __( 'Insert you Error Message.', 'woocommerce-alter-inventory' ),
					),		'desc_tip'	=>  true,		array( 'type' => 'sectionend', 'id' => 'alter_inventory_options'),

				);
				
				// Default options
				add_option( 'alterinventory_enable', 'yes' );
				add_option( 'altereports_enable', 'yes' );
				add_option( 'alter_wp_login_form_enable', 'yes' );
				add_option( 'alter_wp_seller_form_enable', 'yes' );
				add_option( 'alterinventory_error_message', '<h1 style="color:#F00">This section is not allowed to users. Please contact the administrator to request the access!</h1>' );
				
				


                                 // Admin
				add_action( 'woocommerce_settings_inventory_options_after', array( $this, 'admin_settings' ), 40);
				add_action( 'woocommerce_update_alterinventory_options', array( $this, 'save_admin_settings' ));
				get_option('alterinventory_options');
				
				
				 // Trigger actions
	   
	    do_action( 'woocommerce_update_options' );
				
			}			function plugin_init() {
				if ( $this->alterinventory_enabled ) {
					function get_inventory() {
						
						$options = get_option('alterinventory_options');
						$out = (!isset($options['<h1 style="color:#F00">errormessage_template</h1>']) || $options['<h1 style="color:#F00">errormessage_template</h1>']=="") ? '<h1 style="color:#F00">This section is not allowed to users. Please contact the administrator to request the access!</h1>' : $options['errormessage_template'];
						
						$out = get_option('<h1 style="color:#F00">alterinventory_error_message</h1>', '<h1 style="color:#F00">This section is not allowed to users. Please contact the administrator to request the access!</h1>');
					
						

						$user = wp_get_current_user();
						if ( empty( $user->ID )) {
								echo $out;
								
								
								
						}
						
						if (!is_user_logged_in()) {
                       wp_login_form();
                        }
						
						
						else {
							global $woocommerce, $woo_options;
	
?>

      

<div>
<div align="right" style="margin-bottom:-64px" >
<script type="text/javascript">
 function printPage(){
        var tableData = '<table border="1">'+document.getElementsByTagName('table')[0].innerHTML+'</table>';
        var data = '<button onclick="window.print()"><?php echo __('Print','woocommerce-alter-inventory'); ?></button>'+tableData;
        myWindow=window.open('','','width=1000,height=800px');
        myWindow.innerWidth = screen.width;
        myWindow.innerHeight = screen.height;
        myWindow.screenX = 0;
        myWindow.screenY = 0;
        myWindow.document.write(data);
        myWindow.focus();
    };
 </script>
 <br />
    <a href="javascript:void(0);" class="button" type="submit"  id="printPage" onclick="printPage();"><?php echo __('Print','woocommerce-alter-inventory'); ?></a>
   |
    <a href="alter-inventory/" class="button" type="submit" ><?php echo __('Update','woocommerce-alter-inventory'); ?></a>
    </div>
    <div align="left" >
</div>
</div>


							<h2><?php echo __('VARIATIONS','woocommerce-alter-inventory'); ?></h2>
							<table width="100%" style="border: 1px solid #000; width: 100%; margin-bottom: 50px" cellspacing="0" cellpadding="2" >
								<thead >
									<tr>
                                      <th scope="col" style="text-align:left; border: 1px solid #000; padding: 6px;"><?php _e('IMAGE', 'woothemes'); ?></th>

										<th scope="col" style="text-align:left; border: 1px solid #000; padding: 6px;"><?php _e('VARIABLE', 'woocommerce-alter-inventory'); ?></th>
										<th scope="col" style="text-align:left; border: 1px solid #000; padding: 6px;"><?php _e('PRODUCT', 'woocommerce-alter-inventory'); ?></th>
										<th scope="col" style="text-align:left; border: 1px solid #000; padding: 6px;"><?php _e('SKU', 'woocommerce-alter-inventory'); ?></th>
                                        <th scope="col" style="text-align:left; border: 1px solid #000; padding: 6px;"><?php _e('PRICE', 'woocommerce-alter-inventory'); ?></th>
                                         <th scope="col" style="text-align:left; border: 1px solid #000; padding: 6px;"><?php _e('SELL PRICE', 'woocommerce-alter-inventory'); ?></th>
                                       <th scope="col" style="text-align:left; border: 1px solid #000; padding: 6px;"><?php _e('ATTRIBUTE', 'woocommerce-alter-inventory'); ?></th>
										<th scope="col" style="text-align:right; border: 1px solid #000; padding: 6px;"><?php _e('STOCK', 'woocommerce-alter-inventory'); ?></th>                       
                                        <th class="order-number" scope="col"><?php _e('BUY', 'woothemes'); ?></th>
                           
									</tr>
								</thead>
								<tbody>
							
	    <?php
								$args = array(
									'post_type'         => 'product_variation',
									'post_status'       => 'publish',
									'posts_per_page'    => -1,
									'orderby'           => 'title',
									'order'             => 'ASC'
									
								);
                  //	Loop Product Variation 
								
								
								$loop = new WP_Query( $args);
								while ( $loop->have_posts() ) : $loop->the_post();
									$product = new WC_Product_Variation( $loop->post->ID);
									$attrs = array();
			if ($product->variation_data != "") {
				$terms = wc_get_attribute_taxonomies();

				foreach ( $terms as $term) {
					$termMap['attribute_pa_'.$term->attribute_name] = $term->attribute_label;
				}

				foreach ($product->variation_data as $attributeKey=>$value) {
					if (isset($termMap[$attributeKey])) {
						$attrs[] = $termMap[$attributeKey]." : ".$value;
					} else {
						$attrs[] = $value;
					}
				}
					}	
				
                  /*
                     $colore = get_post_meta( get_the_ID(), 'attribute_pa_color', true );

                     $taglia = get_post_meta( get_the_ID(), 'attribute_pa_taglia', true );
                  */      
										?>
                 
									<tr class="order">
<td  class="thumb column-thumb" ><?php echo $product->get_image( $size ='shop_thumbnail' ); ?></td>
				<td class="order-number" style="text-transform:uppercase"><?php echo $product->get_title(); ?></td>
				<td class="order-number"><?php echo get_the_title( $loop->post->post_parent ); ?></td>
				<td class="order-number"><?php echo $product->sku; ?> </td>
                <td class="order-number"><?php echo $product->regular_price; ?> <strong>€</strong></td>
                <td class="order-number" style="color:#F00"><?php echo $product->sale_price; ?></td>				                
                <td class="order-number" style="text-transform:uppercase"><?php echo join("<strong> / </strong>",$attrs)?></td>
				<td class="order-number"><?php echo $product->stock;  ?> <strong>pezzi</strong></td>
                
                <td style="float:right;" class="order-number"><?php  echo apply_filters( 'woocommerce_loop_add_to_cart_link',
	sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" class="button %s product_type_%s">%s</a>',
		esc_url( $product->add_to_cart_url('/alter-inventory/') ),
		esc_attr( $product->id ),
		esc_attr( $product->get_sku() ),
		$product->is_purchasable() ? 'add_to_cart_button' : '',
		esc_attr( $product->product_type ),
		esc_html( $product->add_to_cart_text() )
	),
$product );



	   ?>
    
</td>
                        
									</tr>
								<?php
								endwhile;
								?>
								</tbody>
							</table>
                            <style>
								.shipping_calculator, .cart-collaterals, .woocommerce-info {display: none;}
								.hentry img { height: auto; max-width: 35%;}
								h2 { padding-top: 20px;}
								.woocommerce-message { display:none;}
								.customer_details, .col2-set { display:none;}
                                                                #reviews {display: none;}
						    </style>
                            
                            <h2><?php echo __('PRODUCTS','woocommerce-alter-inventory'); ?></h2>
                            
                            <table width="100%" style="border: 1px solid #000; width: 100%; margin-bottom: 50px" cellspacing="0" cellpadding="2" >
								<thead>
									<tr>
 <th scope="col" style="text-align:left; border: 1px solid #000; padding: 6px;"><?php _e('IMAGE', 'woocommerce-alter-inventory'); ?></th>

<th scope="col" style="text-align:left; border: 1px solid #000; padding: 6px;"><?php _e('PRODUCT', 'woocommerce-alter-inventory'); ?></th>

<th scope="col" style="text-align:left; border: 1px solid #000; padding: 6px;"><?php _e('SKU', 'woocommerce-alter-inventory'); ?></th>
                                       
<th scope="col" style="text-align:left; border: 1px solid #000; padding: 6px;"><?php _e('PRICE', 'woocommerce-alter-inventory'); ?></th>

 <th scope="col" style="text-align:left; border: 1px solid #000; padding: 6px;"><?php _e('SELL PRICE', 'woocommerce-alter-inventory'); ?></th>


<th scope="col" style="text-align:left; border: 1px solid #000; padding: 6px;"><?php _e('STOCK', 'woocommerce-alter-inventory'); ?></th>
<th scope="col" style="text-align:left; border: 1px solid #000; padding: 6px;"><?php _e('BUY', 'woocommerce-alter-inventory'); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php
									$args2 = array(
									'post_type'         => 'product',
									'post_status'       => 'publish',
									'posts_per_page'    => -1,
									'orderby'           => 'title',
									'order'             => 'ASC',
									
									'tax_query'         => array(
																array(
																	'taxonomy'  => 'product_type',
																	'field'     => 'slug',
																	'terms'     => array('simple'),
																	'operator'  => 'IN'
																)
															)
									);
									$loop = new WP_Query( $args2 );
									while ( $loop->have_posts() ) : $loop->the_post();
									global $product;
			
									?>
										<tr >
<td  class="thumb column-thumb" ><?php echo $product->get_image( $size ='shop_thumbnail' ); ?></td>
<td class="order-number" style="text-transform:uppercase"><?php echo $product->get_title(); ?></td>
<td class="order-number"><?php echo $product->sku; ?></td>											
<td class="order-number"><?php echo $product->price; ?></td>
<td class="order-number" style="color:#F00"><?php echo $product->sale_price; ?></td>	
<td class="order-number"><?php echo $product->stock; ?></td>
 
 <td class="order-number" meta http-equiv="refresh" content="0; url=/alter-inventory/" /><?php  echo apply_filters( 'woocommerce_loop_add_to_cart_link',
	sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" class="button %s product_type_%s" >%s</a>',
		esc_url( $product->add_to_cart_url('alter-inventory/') ),
		esc_attr( $product->id ),
		esc_attr( $product->get_sku() ),
		$product->is_purchasable() ? 'add_to_cart_button' : '', 
		esc_attr( $product->product_type ),
		esc_html( $product->add_to_cart_text() )
		
	),
$product );



 ?><div id="ajax-response"></div>
</td>
										</tr>
									<?php
									endwhile;
									?>
								</tbody>
							</table>
                            
                            <div >
                            
                            <h2><?php echo __('PRODUCT SELL','woocommerce-alter-inventory'); ?></h2>
                            </div>
<?php echo do_shortcode('[woocommerce_cart]','alterinventory');


//Change text in ceckout
add_action( 'woocommerce_order_button_text', 'alter_custom_checkout_text' );
 
function alter_custom_checkout_text() {
	$text= __('Conclude Sale','woocommerce-alter-inventory');
    return $text;
} 


	 ?>

<div>
<h1 align="center">
<a href="..//alter-inventory/">
<?php echo __('UPDATE PAGE','woocommerce-alter-inventory'); ?></a></a></h1>
</div>
<?php echo do_shortcode('[woocommerce_checkout]','alterinventory'); ?>

					<?php	
						   }
					}
					add_shortcode( 'alterinventory', 'get_inventory','manage_inventory' );
					
				}
			}
			
			// Load the settings
			function admin_settings() {
				woocommerce_admin_fields( $this->settings );
			}

			// Save the settings
			function save_admin_settings() {
				woocommerce_update_settings( $this->settings );
			}
			
			//Add support links to plugin page
			public function add_support_links( $links, $file ) {
				if ( !current_user_can( 'install_plugins' ) ) {
					return $links;
				}
				if ( $file == WC_alterinventory::$plugin_basefile ) {
					$links[] = '<a href="http://www.altertech.it/woocommerce-alter-inventory/" target="_blank" title="' . __( 'Homepage', 'woocommerce-alter-inventory' ) . '">' . __( 'Homepage', 'woocommerce-alter-inventory' ) . '</a>';
					
				}
				return $links;
			}
 
	 function add_action_links( $links ) {

		return array(
			array(
				'settings' => '<a href="' . admin_url( 'admin_settings' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);

	}
 
			//Add settings link to plugin page
			 function add_settings_link( $settings ) {
				$settings = sprintf( '<a href="/admin.php?page=wc-settings&tab=products&section=inventory"</a>' , admin_url( '/admin.php?page=wc-settings&tab=products&section=inventory' . $this->settings ) , __( 'Go to the settings page', 'woocommerce-alter-inventory' ) , __( 'Settings', 'woocommerce-alter-inventory' ) );
				array_unshift( $links, $settings );
				return $links;	
				
			}
			
		}
		
		// Instantiate our plugin class and add it to the set of globals
		$GLOBALS['wc_alterinventory'] = new WC_alterinventory();
	}
	
} else {
	function check_woo_notices() {
		if (!is_plugin_active('woocommerce/woocommerce.php')) {
			ob_start();
			?><div class="error">
			<h1 style="color:#F00"><p><strong><?php echo __('WARNING', 'woocommerce-alter-inventory'); ?></strong>: <?php echo __('WooCommerce is not active and Alter Inventory can not work!', 'woocommerce-alter-inventory'); ?></p></h1>
			</div><?php
			echo ob_get_clean();
		}
	}
	add_action('admin_notices', 'check_woo_notices');
}
/**
 * Add payment method to admin new order email
 *
 */
add_action( 'woocommerce_email_after_order_table', 'woo_add_payment_method_to_admin_new_order', 15, 2 ); 

function woo_add_payment_method_to_admin_new_order( $order, $is_admin_email ) { 
	if ( $is_admin_email ) { 
}
	echo '<p><strong>'.__('Payment Method', 'woocommerce-alter-inventory').':</strong> ' . $order->cod . '</p>'; 
	} 



/**
 * Auto Complete all WooCommerce orders.
 * http://docs.woothemes.com/document/automaticaaly-complete-orders/
 */
 
add_action( 'woocommerce_thankyou', 'alter_woocommerce_auto_complete_order' );
function alter_woocommerce_auto_complete_order( $order_id ) {
    global $woocommerce;
 
    if ( ! $order_id ) {
        return;
    }
    $order = new WC_Order( $order_id );
    $order->update_status( 'completed' );
	 wp_redirect( home_url('/alter-reports/') ); exit; // or whatever url you want
}

// rename the coupon field on the checkout page
function woocommerce_rename_coupon_field_on_checkout( $translated_text, $text, $text_domain ) {
 
	// bail if not modifying frontend woocommerce text
	if ( is_admin() || 'woocommerce' !== $text_domain ) {
		return $translated_text;
	}
 
if ( 'Apply Coupon' === $text ) {
		$translated_text = __('Please enter your code for Operator % discount.','woocommerce-alter-inventory');
	}
 
	return $translated_text;
}
add_filter( 'gettext', 'woocommerce_rename_coupon_field_on_checkout', 10, 3 );



 //////////////////////////////// DASHBOARD WIDGET
 
 
        add_action( 'wp_dashboard_setup', 'prefix_add_dashboard_widget' );

function prefix_add_dashboard_widget() {
    wp_add_dashboard_widget(
        'my_dashboard_widget', 
        'Alter Reports Page', 
        'prefix_dashboard_widget', 
        'prefix_dashboard_widget_handle'
    );
}

function prefix_dashboard_widget() {
    # get saved data
    if( !$widget_options = get_option( 'my_dashboard_widget_options' ) )
        $widget_options = array( );

    # default output
    $output = sprintf(
        '<h2 style="text-align:right">%s</h2>',
        __( 'Please, configure the widget ☝','woocommerce-alter-inventory' )
    );
    
    # check if saved data contains content
    $saved_feature_post = isset( $widget_options['feature_post'] ) 
        ? $widget_options['feature_post'] : false;

    # custom content saved by control callback, modify output
    if( $saved_feature_post ) {
        $post = get_post( $saved_feature_post );
        if( $post ) {
            $content = do_shortcode( html_entity_decode( $post->post_content ) );
            $output = "<h2>{$post->post_title}</h2><p>{$content}</p>";
        }
    }
    echo "<div class='feature_post_class_wrap'>
        <label style='background:#ccc;'>$output</label>
    </div>
    ";
}

function prefix_dashboard_widget_handle()
{
    # get saved data
    if( !$widget_options = get_option( 'my_dashboard_widget_options' ) )
        $widget_options = array( );

    # process update
    if( 'POST' == $_SERVER['REQUEST_METHOD'] && isset( $_POST['my_dashboard_widget_options'] ) ) {
        # minor validation
        $widget_options['feature_post'] = absint( $_POST['my_dashboard_widget_options']['feature_post'] );
        # save update
        update_option( 'my_dashboard_widget_options', $widget_options );
    }

    # set defaults  
    if( !isset( $widget_options['feature_post'] ) )
        $widget_options['feature_post'] = '';


    echo "<p><strong>Available Pages</strong></p>
    <div class='feature_post_class_wrap'>
        <label>Title</label>";
    wp_dropdown_pages( array(
        'post_type'        => 'page',
        'selected'         => $widget_options['feature_post'],
        'name'             => 'my_dashboard_widget_options[feature_post]',
        'id'               => 'feature_post',
        'show_option_none' => '- Select -'
    ) );
    echo "</div>";
}
/////////////////////

?>
<?php
function shortcode_altereports_func() {
	
	include_once ABSPATH . 'wp-admin/includes/plugin.php';
    include_once (ABSPATH . WPINC . '/functions.php');
	
	global $wpdb, $woocommerce, $WC_Order, $woo_options, $WC_API_Reports, $WC_Admin_Dashboard, $WC_Admin_Reports, $WC_Admin_Report, $WC_Report_Customers, $WC_Report_Stock, $WC_alterinventory;
	
	
	$out = get_option('<h1 style="color:#F00">alterinventory_error_message</h1>', '<h1 style="color:#F00">'.__('This section is not allowed to users. Please contact the administrator to request the access!','woocommerce-alter-inventory').'</h1>');
    $options = get_option('alterinventory_options');
	$user = wp_get_current_user();
						if ( empty( $user->ID )) {
								echo $out;
								
								
								
						}
						
						if (!is_user_logged_in()) {
                       wp_login_form();
                        }
						
						
						else {


	?>
    <?php
/**
 * Template for Direct Sells
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $wpdb, $Product, $item, $item_meta, $product, $woocommerce, $woo_options,  $order_count, $WC_API_Reports, $WC_Admin_Dashboard, $WC_Admin_Reports, $WC_Admin_Report, $WC_Report_Customers, $WC_Report_Stock, $WC_alterinventory;


$customer_orders = get_posts( apply_filters( 'woocommerce_my_account_my_orders_query', array(
	'numberposts' => $order_count,
	'meta_key'    => '_customer_user',
	'meta_value'  => get_current_user_id(),
	'post_type'   => 'shop_order',
	'post_status' => array( 'wc-processing', 'wc-completed' ),
	'posts_per_page' => -1,
	'paged' => get_query_var('paged')
) ) );

if ( $customer_orders ) : ?>
<div align="right" style="margin-bottom:-60px" >
<script type="text/javascript">
 function printPage(){
        var tableData = '<table border="1">'+document.getElementsByTagName('table')[0].innerHTML+'</table>';
        var data = '<button onclick="window.print()"> Stampa </button>'+tableData;
        myWindow=window.open('','','width=1000,height=800px');
        myWindow.innerWidth = screen.width;
        myWindow.innerHeight = screen.height;
        myWindow.screenX = 0;
        myWindow.screenY = 0;
        myWindow.document.write(data);
        myWindow.focus();
    };
 </script>

 <br />
    <a href="javascript:void(0);" class="button" type="submit"  id="printPage" onclick="printPage();">Stampa</a>
    |
    <a href="alter-inventory/" class="button" type="submit" >
Aggiorna</a>
    <br />
    
    </div>
   
	
<div style="margin-bottom: 40px;" >
<?php
 

 
$form = '<form role="search" method="get" id="searchform" action="' . esc_url( home_url( '/'  ) ) . '">
	<div>
		<label class="screen-reader-text" for="s">' . __( 'Search Sales', 'woocommerce-alter-inventory' ) . ' :</label>
		<input type="text" value="' . get_search_query() . '" name="s" id="s" placeholder="' . __( 'Sales..', 'woocommerce-alter-inventory' ) . '" />
		<input class="button" type="submit" id="searchsubmit" value="'. esc_attr__( 'Search', 'woocommerce-alter-inventory' ) .'" />
		<input type="hidden" name="post_type" value="product" />
	</div>
</form>';
 
echo $form;
?>
</div>
<h2><?php  __( 'Sales..', 'woocommerce-alter-inventory' ); ?></h2>
   
	<table class="shop_table my_account_orders">

		<thead>
        
			<tr>
   
				<th class="order-number"><span class="nobr"><?php _e( '#ID Sale', 'woocommerce-alter-inventory' ); ?></span></th>
				<th class="order-date"><span class="nobr"><?php _e( 'Date', 'woocommerce-alter-inventory' ); ?></span></th>
				<th class="order-total"><span class="nobr"><?php _e( 'Total', 'woocommerce-alter-inventory' ); ?></span></th>
                <th class="order-actions"><span class="nobr"><?php _e( 'Products / Attributi / Totale', 'woocommerce-alter-inventory' ); ?></span></th>
				<th class="order-actions"><span class="nobr"><?php _e( 'Details', 'woocommerce-alter-inventory' ); ?></span></th>
                <th class="order-actions"><span class="nobr"><?php _e( 'Cancel', 'woocommerce-alter-inventory' ); ?></span></th>
			</tr>
		</thead>

 <style>
								
								.hentry img { height: auto; max-width: 35%;}
								.woocommerce-message { display:none;}
						    </style>
		<tbody><?php
			foreach ( $customer_orders as $customer_order ) {
				$order = new WC_Order();

				$order->populate( $customer_order );


				$item_count = $order->get_item_count();
				

				?><tr class="order">
					<td class="order-number">
						<a href="<?php echo $order->get_view_order_url(); ?>">
							<?php echo $order->get_order_number(); ?>
						</a>
					</td>
					<td class="order-date">
						<time datetime="<?php echo date( 'Y-m-d', strtotime( $order->order_date ) ); ?>" title="<?php echo esc_attr( strtotime( $order->order_date ) ); ?>"><?php echo date_i18n( get_option( 'date_format' ), strtotime( $order->order_date ) ); ?></time>
					</td>

					<td class="order-total">
						<?php echo sprintf( _n( '<strong>%s</strong> x <strong>%s Product</strong>', '%s for %s items', $item_count, 'woocommerce' ), $order->get_formatted_order_total(), $item_count ); ?>
					</td>
                   
		
                 <td class="order-actions">
  <table class="shop_table order_details">
	<thead>
		<tr>
			<th class="product-name"><?php _e( 'Product', 'woocommerce-alter-inventory' ); ?></th>
			<th class="product-total"><?php _e( 'Total', 'woocommerce-alter-inventory' ); ?></th>
		</tr>
	</thead>
    
	<tfoot>
	
	</tfoot>
	<tbody>
		<?php
		if ( sizeof( $order->get_items() ) > 0 ) {

			foreach( $order->get_items() as $item ) {
				$_product     = apply_filters( 'woocommerce_order_item_product', $order->get_product_from_item( $item ), $item );
				$item_meta    = new WC_Order_Item_Meta( $item['item_meta'], $_product );

				?>
				<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'order_item', $item, $order ) ); ?>">
					<td  style="text-transform:uppercase;">
						<?php
							if ( $_product && ! $_product->is_visible() )
								echo apply_filters( 'woocommerce_order_item_name', $item['name'], $item );
							else
								echo apply_filters( 'woocommerce_order_item_name', sprintf( '<a href="%s">%s</a>', get_permalink( $item['product_id'] ), $item['name'] ), $item );

							echo apply_filters( 'woocommerce_order_item_quantity_html', ' <strong class="product-quantity">' . sprintf( '&times; %s', $item['qty'] ) . '</strong>', $item );

							$item_meta->display();

							if ( $_product && $_product->exists() && $_product->is_downloadable() && $order->is_download_permitted() ) {

								$download_files = $order->get_item_downloads( $item );
								$i              = 0;
								$links          = array();

								foreach ( $download_files as $download_id => $file ) {
									$i++;

									$links[] = '<small><a href="' . esc_url( $file['download_url'] ) . '">' . sprintf( __( 'Download file%s', 'woocommerce' ), ( count( $download_files ) > 1 ? ' ' . $i . ': ' : ': ' ) ) . esc_html( $file['name'] ) . '</a></small>';
								}

								echo '<br/>' . implode( '<br/>', $links );
							}
						?>
					</td>
					<td class="product-total" style="color:#F00">
						<?php echo $order->get_formatted_line_subtotal( $item ); ?>
					</td>
				</tr>
				
					<?php
				
			}
		}

		
		?>
	</tbody>
</table>
<div class="clear"></div>
</td>
<td class="order-actions">
                 
						<?php
							
							$actions = array();

							if ( in_array( $order->status, apply_filters( 'woocommerce_valid_order_statuses_for_payment', array( 'Incompleta', 'Fallita' ), $order ) ) ) {
								
							}
							
							

							
								

						$actions['view'] = array(
								'url'  => $order->get_view_order_url(),
								'name' => __( 'Details', 'woocommerce-alter-inventory' )
							);

							$actions = apply_filters( 'woocommerce_my_account_my_orders_actions', $actions, $order );

							if ($actions) {
								foreach ( $actions as $key => $action ) {
									echo '<a href="' . esc_url( $action['url'] ) . '" class="button ' . sanitize_html_class( $key ) . '">' . esc_html( $action['name'] ) . '</a>';
								}
							}
							
		
			
						?>
  				</td>
                    <td class="order-actions">
                    	<?php
							$actions = array();



							
								$actions['cancel'] = array(
									'url'  => $order->get_cancel_order_url( get_permalink( wc_get_page_id( 'alter-inventory' ) ) ),
									'name' => __( 'Cancel', 'woocommerce-alter-inventory' )
								);
							


							

							if ($actions) {
								foreach ( $actions as $key => $action ) {
									echo '<a href="' . esc_url( $action['url'] ) . '" class="button ' . sanitize_html_class( $key ) . '">' . esc_html( $action['name'] ) . '</a>';
								}
							}
						?>
					</td>
				</tr><?php
			}
		?></tbody>

	</table>
<?php endif; ?>

	
	<?php	
  }
   }


add_shortcode('altereports','shortcode_altereports_func');
?>
