<?php
/*
Plugin Name: Paid Memberships Pro - Add PayPal Express Add On
Plugin URI: http://www.paidmembershipspro.com/wp/pmpro-add-paypal-express/
Description: Add PayPal Express as a Second Option at Checkout
Version: .4
Author: Stranger Studios
Author URI: http://www.strangerstudios.com
*/
/*
	You must have your PayPal Express API key, username, and password set in the PMPro Payment Settings for this plugin to work.
	After setting those settings and clicking save, you can switch to your primary gateway and set those settings. 
	The PayPal Express settings will be remembered "in the background".
	You do not need to activate this plugin with PayPal Website Payments Pro. PayPal Express is automatically an option at checkout with that gateway.
	
	This plugin will only work when the primary gateway is an onsite gateway. At this time, this includes:
	* Stripe
	* Braintree
	* Authorize.net
	* PayPal Payflow Pro
	* Cybersource
*/

/*
	Make PayPal Express a valid gateway.
*/
function pmproappe_pmpro_valid_gateways($gateways)
{
    //if already using paypal, ignore this
	$setting_gateway = get_option("pmpro_gateway");

	if(pmproappe_using_paypal( $setting_gateway )) {

		return $gateways;
	}
	
	$gateways[] = "paypalexpress";
    return $gateways;
}
add_filter("pmpro_valid_gateways", "pmproappe_pmpro_valid_gateways");

/*
	Check if a PayPal gateway is enabled for PMPro.
*/
function pmproappe_using_paypal( $check_gateway = null ) {

	if (is_null($check_gateway)) {

		global $gateway;
		$check_gateway = $gateway;
	}

	$paypal_gateways = apply_filters('pmpro_paypal_gateways', array('paypal', 'paypalstandard', 'paypalexpress', 'payflowpro' ) );

	if ( in_array($check_gateway, $paypal_gateways)) {
		return true;
	}

	return false;
}

/*
	Add toggle to checkout page.
*/
function pmproappe_pmpro_checkout_boxes()
{
	//if already using paypal, ignore this	
	$setting_gateway = get_option("pmpro_gateway");
	if($setting_gateway == "paypal")
		return;
		
	global $pmpro_requirebilling, $gateway, $pmpro_review;

	//only show this if we're not reviewing and the current gateway isn't a PayPal gateway
	if(empty($pmpro_review) && false === pmproappe_using_paypal())
	{
	?>
	<div id="pmpro_payment_method" class="pmpro_checkout" <?php if(!$pmpro_requirebilling) { ?>style="display: none;"<?php } ?>>
		<h2><?php _e('Choose your Payment Method', 'pmpro');?></h2>
		<div class="pmpro_checkout-fields">
			<span class="gateway_<?php echo esc_attr($setting_gateway); ?>">
				<input type="radio" name="gateway" value="<?php echo esc_attr($setting_gateway);?>" <?php if(!$gateway || $gateway == $setting_gateway) { ?>checked="checked"<?php } ?> />
				<a href="javascript:void(0);" class="pmpro_radio"><?php _e('Check Out with a Credit Card Here', 'pmpro');?></a>
			</span>
			<span class="gateway_paypalexpress">
				<input type="radio" name="gateway" value="paypalexpress" <?php if($gateway == "paypalexpress") { ?>checked="checked"<?php } ?> />
				<a href="javascript:void(0);" class="pmpro_radio"><?php _e('Check Out with PayPal', 'pmpro');?></a>
			</span>	
		</div>
	</div> <!--end pmpro_payment_method -->
	<?php //here we draw the PayPal Express button, which gets moved in place by JavaScript ?>
	<span id="pmpro_paypalexpress_checkout" style="display: none;">
		<input type="hidden" name="submit-checkout" value="1" />		
		<input type="image" class="pmpro_btn-submit-checkout" value="<?php _e('Check Out with PayPal', 'pmpro');?> &raquo;" src="<?php echo apply_filters("pmpro_paypal_button_image", "https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif");?>" />
	</span>
	<script>	
		var pmpro_require_billing = <?php if($pmpro_requirebilling) echo "true"; else echo "false";?>;
		
		//choosing payment method
		jQuery(document).ready(function() {		
			//move paypal express button into submit box
			jQuery('#pmpro_paypalexpress_checkout').appendTo('div.pmpro_submit');
			
			function showPayPalExpressCheckout()
			{
				jQuery('#pmpro_billing_address_fields').hide();
				jQuery('#pmpro_payment_information_fields').hide();			
				jQuery('#pmpro_submit_span').hide();
				jQuery('#pmpro_paypalexpress_checkout').show();
				
				pmpro_require_billing = false;		
			}
			
			function showCreditCardCheckout()
			{
				jQuery('#pmpro_paypalexpress_checkout').hide();
				jQuery('#pmpro_billing_address_fields').show();
				jQuery('#pmpro_payment_information_fields').show();			
				jQuery('#pmpro_submit_span').show();
				
				pmpro_require_billing = true;
			}
			
			function showFreeCheckout()
			{
				jQuery('#pmpro_billing_address_fields').hide();
				jQuery('#pmpro_payment_information_fields').hide();			
				jQuery('#pmpro_submit_span').show();
				jQuery('#pmpro_paypalexpress_checkout').hide();				
				
				pmpro_require_billing = false;	
			}
			
			//detect gateway change
			jQuery('input[name=gateway]').click(function() {		
				if(jQuery(this).val() != 'paypalexpress')
				{
					showCreditCardCheckout();
				}
				else
				{			
					showPayPalExpressCheckout();
				}
			});
			
			//update radio on page load
			if(jQuery('input[name=gateway]:checked').val() != 'paypalexpress' && pmpro_require_billing == true)
			{
				showCreditCardCheckout();
			}
			else if(pmpro_require_billing == true)
			{			
				showPayPalExpressCheckout();
			}
			else
			{
				showFreeCheckout();
			}
			
			//select the radio button if the label is clicked on
			jQuery('a.pmpro_radio').click(function() {
				jQuery(this).prev().click();
			});
		});
	</script>
	<?php
	}
	else
	{
	?>
	<script>
		//choosing payment method
		jQuery(document).ready(function() {		
			jQuery('#pmpro_billing_address_fields').hide();
			jQuery('#pmpro_payment_information_fields').hide();			
		});		
	</script>
	<?php
	}	
}
add_action("pmpro_checkout_boxes", "pmproappe_pmpro_checkout_boxes", 20);

/*
	Hide/show billing fields and options if a free discount code is applied.
*/
function pmproappe_pmpro_applydiscountcode_return_js($discount_code, $discount_code_id, $level_id, $code_level)
{
	// skip this if the active gateway is a PayPal gateway
	if (true === pmproappe_using_paypal()) {
		return;
	}

	if(pmpro_isLevelFree($code_level))
	{
		//free level, hide options and billing fields
	?>
		jQuery('#pmpro_payment_method').hide();
		jQuery('#pmpro_billing_address_fields').hide();
		jQuery('#pmpro_payment_information_fields').hide();			
		jQuery('#pmpro_submit_span').show();
		jQuery('#pmpro_paypalexpress_checkout').hide();
		
		pmpro_require_billing = false;
	<?php
	}
	else
	{
	?>
		jQuery('#pmpro_payment_method').show();
		if(jQuery('input[name=gateway]:checked').val() != 'paypalexpress' && pmpro_require_billing == true)
		{
			jQuery('#pmpro_paypalexpress_checkout').hide();
			jQuery('#pmpro_billing_address_fields').show();
			jQuery('#pmpro_payment_information_fields').show();			
			jQuery('#pmpro_submit_span').show();
			
			pmpro_require_billing = true;	
		}
		else if(pmpro_require_billing == true)
		{			
			jQuery('#pmpro_billing_address_fields').hide();
			jQuery('#pmpro_payment_information_fields').hide();			
			jQuery('#pmpro_submit_span').hide();
			jQuery('#pmpro_paypalexpress_checkout').show();
			
			pmpro_require_billing = false;
		}
		else
		{
			jQuery('#pmpro_billing_address_fields').hide();
			jQuery('#pmpro_payment_information_fields').hide();			
			jQuery('#pmpro_submit_span').show();
			jQuery('#pmpro_paypalexpress_checkout').hide();				
			
			pmpro_require_billing = false;	
		}
		
		pmpro_require_billing = true;
	<?php
	}
}
add_action('pmpro_applydiscountcode_return_js', 'pmproappe_pmpro_applydiscountcode_return_js', 10, 4);

/*
	Function to add links to the plugin row meta
*/
function pmproappe_plugin_row_meta($links, $file) {
	if(strpos($file, 'pmpro-add-paypal-express.php') !== false)
	{
		$new_links = array(
			'<a href="' . esc_url('http://www.paidmembershipspro.com/add-ons/plugins-on-github/pmpro-add-paypal-express-option-checkout/')  . '" title="' . esc_attr( __( 'View Documentation', 'pmpro' ) ) . '">' . __( 'Docs', 'pmpro' ) . '</a>',
			'<a href="' . esc_url('http://paidmembershipspro.com/support/') . '" title="' . esc_attr( __( 'Visit Customer Support Forum', 'pmpro' ) ) . '">' . __( 'Support', 'pmpro' ) . '</a>',
		);
		$links = array_merge($links, $new_links);
	}
	return $links;
}
add_filter('plugin_row_meta', 'pmproappe_plugin_row_meta', 10, 2);
