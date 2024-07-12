<?php
/*
Plugin Name: Paid Memberships Pro - Add PayPal Express Add On
Plugin URI: https://www.paidmembershipspro.com/add-ons/pmpro-add-paypal-express-option-checkout/
Description: Add PayPal Express as a Second Option at Checkout
Version: .6
Author: Paid Memberships Pro
Author URI: https://www.paidmembershipspro.com
Text Domain: pmpro-add-paypal-express
Domain Path: /languages
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

function pmpro_add_paypal_express_i18n() {
	load_plugin_textdomain( 'pmpro-add-paypal-express', false, basename( dirname( __FILE__ ) ) . '/languages' ); 
}
add_action( 'plugins_loaded', 'pmpro_add_paypal_express_i18n', 10 );

function pmpro_add_paypal_express_register_styles() {
	wp_register_style( 'pmpro-add-paypal-express-styles', plugins_url( 'css/pmpro-add-paypal-express.css', __FILE__ ) );
	wp_enqueue_style( 'pmpro-add-paypal-express-styles' );
}
add_action( 'wp_enqueue_scripts', 'pmpro_add_paypal_express_register_styles' );

/**
 * Make PayPal Express a valid gateway.
 *
 * @param array $gateways Array of valid gateways.
 */
function pmproappe_pmpro_valid_gateways( $gateways ) {
	// Get the current gateway setting.
	$setting_gateway = get_option( 'pmpro_gateway' );

	// If PayPal is already the current gateway, ignore this.
	if ( pmproappe_using_paypal( $setting_gateway ) ) {
		return $gateways;
	}

	// Add PayPal Express to the list of valid gateways.
	$gateways[] = "paypalexpress";

	return $gateways;
}
add_filter( 'pmpro_valid_gateways', 'pmproappe_pmpro_valid_gateways' );

/*
	Check if a PayPal gateway is enabled for PMPro.
*/
function pmproappe_using_paypal( $check_gateway = null ) {

	if (is_null($check_gateway)) {

		global $gateway;
		$check_gateway = $gateway;
	}

	$paypal_gateways = apply_filters('pmpro_paypal_gateways', array('paypal', 'paypalstandard', 'paypalexpress' ) );

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
	if(pmproappe_using_paypal( $setting_gateway )) {
		return;
	}

	global $pmpro_requirebilling, $gateway, $pmpro_review;

	//only show this if we're not reviewing and the current gateway isn't a PayPal gateway
	if( empty($pmpro_review) ) {
	?>
	<fieldset id="pmpro_payment_method" class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_form_fieldset', 'pmpro_payment_method' ) ); ?>"<?php if(!$pmpro_requirebilling) { ?> style="display: none;"<?php } ?>>
		<div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_card' ) ); ?>">
			<div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_card_content' ) ); ?>">
				<legend class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_form_legend' ) ); ?>">
					<h2 class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_form_heading pmpro_font-large' ) ); ?>">
						<?php esc_html_e( 'Choose Your Payment Method', 'pmpro-add-paypal-express' ); ?>
					</h2>
				</legend>
				<div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_form_fields' ) ); ?>">
					<div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_form_field pmpro_form_field-radio' ) ); ?>">
						<div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_form_field-radio-items' ) ); ?>">
							<?php if ( $setting_gateway != 'check' ) { ?>
								<div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_form_field pmpro_form_field-radio-item' ) ); ?> gateway_<?php echo esc_attr($setting_gateway); ?>">
									<input type="radio" id="gateway_<?php echo esc_attr( $setting_gateway ); ?>" name="gateway" class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_form_input pmpro_form_input-radio' ) ); ?>" value="<?php echo esc_attr( $setting_gateway ); ?>" <?php if(!$gateway || $gateway == $setting_gateway) { ?>checked="checked"<?php } ?> />
									<label for="gateway_<?php echo esc_attr( $setting_gateway ); ?>" class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_form_label pmpro_form_label-inline pmpro_clickable' ) ); ?>">
										<?php esc_html_e( 'Check Out with a Credit Card Here', 'pmpro-add-paypal-express' ); ?>
									</label>
								</div> <!-- end pmpro_form_field pmpro_form_field-radio-item -->
							<?php } ?>

							<div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_form_field pmpro_form_field-radio-item' ) ); ?> gateway_paypalexpress">
								<input type="radio" id="gateway_paypalexpress" name="gateway" class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_form_input pmpro_form_input-radio' ) ); ?>" value="paypalexpress" <?php if($gateway == "paypalexpress") { ?>checked="checked"<?php } ?> />
								<label for="gateway_paypalexpress" class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_form_label pmpro_form_label-inline pmpro_clickable' ) ); ?>">
									<?php esc_html_e( 'Check Out with PayPal', 'pmpro-add-paypal-express' ); ?>
								</label>
							</div> <!-- end pmpro_form_field pmpro_form_field-radio-item -->

							<?php
								// Integrate with the PMPro Pay by Check Add On.
								if ( function_exists( 'pmpropbc_checkout_boxes' ) ) {
									global $pmpro_level;
									$options = pmpropbc_getOptions( $pmpro_level->id );
									$check_gateway_label = get_option( 'pmpro_check_gateway_label' ) ?: __( 'Check', 'pmpro-add-paypal-express' );

									// Only show if the main gateway is not check and setting value == 1 (value == 2 means only do check payments).
									if ( $setting_gateway != "check" && $options['setting'] == 1 ) {
										?>
										<div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_form_field pmpro_form_field-radio-item' ) ); ?> gateway_check">
											<input type="radio" id="gateway_check" name="gateway" class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_form_input pmpro_form_input-radio' ) ); ?>" value="check" <?php if($gateway == "check") { ?>checked="checked"<?php } ?> />
											<label for="gateway_check" class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_form_label pmpro_form_label-inline pmpro_clickable' ) ); ?>">
												<?php echo esc_html( sprintf( __( 'Pay by %s', 'pmpro-pay-by-check' ), $check_gateway_label ) ); ?>
											</label>
										</div> <!-- end pmpro_form_field pmpro_form_field-radio-item -->
										<?php
									}
								}
							?>
						</div> <!-- end pmpro_form_field-radio-items -->
					</div> <!-- end pmpro_form_field pmpro_form_field-radio -->
				</div> <!-- end pmpro_form_fields -->
			</div> <!-- end pmpro_card_content -->
		</div> <!-- end pmpro_card -->
	</fieldset> <!-- end pmpro_payment_method -->
	<?php
		// Here we draw the PayPal Express button, which gets moved in place by JavaScript.
		// But if the current gateway is PayPal Express, this span will already have been added.
		if ( $gateway != 'paypalexpress' ) {
			?>
			<span id="pmpro_paypalexpress_checkout" style="display: none;">
				<input type="hidden" name="submit-checkout" value="1" />
				<button type="submit" id="pmpro_btn-submit-paypalexpress" class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_btn pmpro_btn-submit-checkout pmpro_btn-submit-checkout-paypal' ) ); ?>">
					<?php
						printf(
							/* translators: %s is the PayPal logo */
							esc_html__( 'Check Out With %s', 'paid-memberships-pro' ),
							'<span class="pmpro_btn-submit-checkout-paypal-image"></span>'
						);
					?>
					<span class="screen-reader-text"><?php esc_html_e( 'PayPal', 'paid-memberships-pro' ); ?></span>
				</button>
			</span>
			<?php
		}
	?>
	<script>
		var pmpro_require_billing = <?php if($pmpro_requirebilling) echo "true"; else echo "false";?>;

		//hide/show functions
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

		function showCheckCheckout()
		{
			jQuery('#pmpro_billing_address_fields').show();
			jQuery('#pmpro_payment_information_fields').hide();
			jQuery('#pmpro_submit_span').show();
			jQuery('#pmpro_paypalexpress_checkout').hide();

			pmpro_require_billing = false;
		}

		//choosing payment method
		jQuery(document).ready(function() {

			// Move PayPal Express button into submit box.
			var pmpro_form_submit = jQuery('div.pmpro_form_submit');
			if (pmpro_form_submit.length) {
				// This means we are on v3.1+.
				jQuery('#pmpro_paypalexpress_checkout').prependTo(pmpro_form_submit);
			} else {
				// This means we are on v3.0.x. or earlier.
				jQuery('#pmpro_paypalexpress_checkout').appendTo('div.pmpro_submit');

				// Remove the screen-reader-text class from the span inside the button.
				// This version doesn't load the image in core PMPro styles so we need to show the word PayPal.
				jQuery('#pmpro_btn-submit-paypalexpress span.screen-reader-text').removeClass('screen-reader-text');
			}

			//detect gateway change
			jQuery('input[name=gateway]').click(function() {
				var chosen_gateway = jQuery(this).val();
				if(chosen_gateway == 'paypalexpress') {
					showPayPalExpressCheckout();
				} else if(chosen_gateway == 'check') {
					showCheckCheckout();
				} else {
					showCreditCardCheckout();
				}
			});

			//update radio on page load
			if(jQuery('input[name=gateway]:checked').val() == 'check') {
				showCheckCheckout();
			} else if(jQuery('input[name=gateway]:checked').val() != 'paypalexpress' && pmpro_require_billing == true) {
				showCreditCardCheckout();
			} else if(pmpro_require_billing == true) {
				showPayPalExpressCheckout();
			} else {
				showFreeCheckout();
			}
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
	Integration with the PMPro Pay by Check Addon.
	Unhook the PBC checkout boxes method.
	We add a check option above.
*/
function pmproappe_init_pbc_integrations() {
	remove_action("pmpro_checkout_boxes", "pmpropbc_checkout_boxes", 20);
}
add_action('init', 'pmproappe_init_pbc_integrations');

/*
	Hide/show billing fields and options if a free discount code is applied.
*/
function pmproappe_pmpro_applydiscountcode_return_js($discount_code, $discount_code_id, $level_id, $code_level)
{
	// skip this if the active gateway is a PayPal gateway
	$setting_gateway = get_option("pmpro_gateway");
	if (true === pmproappe_using_paypal($setting_gateway)) {
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

	<?php
	}
}
add_action('pmpro_applydiscountcode_return_js', 'pmproappe_pmpro_applydiscountcode_return_js', 10, 4);

/*
	Add notice to the PMPro payment settings page if this plugin is active and
	either PayPal or PayPal Express is the primary gateway.
*/
function pmproappe_admin_notices() {
	//make sure we're on the payment settings page
	if( !empty( $_REQUEST['page'] ) && $_REQUEST['page'] == 'pmpro-paymentsettings' ) {
		//check gateway
		$gateway = pmpro_getGateway();
		if( $gateway == 'paypal' || $gateway == 'paypalexpress' ) {
		?>
		<div class="notice notice-warning is-dismissible">
			<p><?php echo __( 'The Add PayPal Express Add On is not required with the chosen gateway. Change the gateway setting below or deactivate the Add On.', 'pmpro-add-paypal-express' ) ;?></p>
		</div>
		<?php
		}
	}
}
add_action('admin_notices', 'pmproappe_admin_notices');

/*
	In PMPro core, the PayPal Express class hooks into pmpro_include_billing_address_fields
	and pmpro_include_payment_information_fields to keep those checkout sections from being shown.
	Some gateways, like Authorize.net and the default testing gateway need those fields to be shown.
	
	We remove any filter on pmpro_include_billing_address_fields and pmpro_include_payment_information_fields
	that simply returns false (like the ones in the PayPal Express gateway class), but this won't affect filters
	that override the billing and payment info fields with their own (like Stripe).
	
	The JS in this Add On will then hide/show the fields based on which gateway option is chosen.
*/
function pmproappe_include_billing_and_payment_fields() {
	//if already using paypal, ignore this
	$setting_gateway = get_option("pmpro_gateway");
	if(pmproappe_using_paypal( $setting_gateway )) {
		return;
	}

	global $gateway;
		
	if ( $gateway == 'paypalexpress' ) {
		remove_filter( 'pmpro_include_billing_address_fields', '__return_false' );
		remove_filter( 'pmpro_include_payment_information_fields', '__return_false' );
	}	
}
add_action('pmpro_checkout_preheader', 'pmproappe_include_billing_and_payment_fields' );

/*
	Function to add links to the plugin row meta
*/
function pmproappe_plugin_row_meta($links, $file) {
	if(strpos($file, 'pmpro-add-paypal-express.php') !== false)
	{
		$new_links = array(
			'<a href="' . esc_url('https://www.paidmembershipspro.com/add-ons/pmpro-add-paypal-express-option-checkout/')  . '" title="' . esc_attr( __( 'View Documentation', 'pmpro-add-paypal-express' ) ) . '">' . __( 'Docs', 'pmpro-add-paypal-express' ) . '</a>',
			'<a href="' . esc_url('https://www.paidmembershipspro.com/support/') . '" title="' . esc_attr( __( 'Visit Customer Support Forum', 'pmpro-add-paypal-express' ) ) . '">' . __( 'Support', 'pmpro-add-paypal-express' ) . '</a>',
		);
		$links = array_merge($links, $new_links);
	}
	return $links;
}
add_filter('plugin_row_meta', 'pmproappe_plugin_row_meta', 10, 2);
