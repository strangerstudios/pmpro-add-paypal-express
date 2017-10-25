== Paid Memberships Pro - Add PayPal Express Add On ==
Contributors: strangerstudios
Tags: pmpro, paid memberships pro, gateway, paypal, checkout, registration
Requires at least: 3.6
Tested up to: 4.8.2
Stable tag: .5.2

Appeal to the highest number of potential customers by offering PayPal as a payment option in addition to your onsite credit card payment gateway.

== Description ==

This plugin enables a radio select on the membership checkout page, allowing a user to select their preferred payment method for purchase. PayPal offers peace of mind to buyers that have concerns paying you directly by giving them a layer of protection when making purchases online.

== Installation ==

1. Upload the 'pmpro-add-paypal-express' directory to the '/wp-content/plugins/' directory of your site.
1. Activate the plugin through the 'Plugins' menu in WordPress.

== Setup ==
1. Navigate to the Memberships > Payment Settings admin page.
1. Select the 'PayPal Express' gateway from the 'Payment Gateway' dropdown list.
1. Enter your PayPal Express API key, username, and password set in the appropriate fields [docs].
1. Click 'Save Settings'.
1. Now (after inputting your PayPal Express settings and clicking save), switch back to your primary gateway by selecting your gateway from the 'Payment Gateway' dropdown list.
1. Click 'Save Settings'.
1. The PayPal Express settings will be remembered 'in the background'.

Navigate to your Memberships Checkout page and preview the new 'Choose Your Payment Method' box. We also advise testing checkout via PayPal to ensure that the connection is working.

== Important Notes ==

You do not need to activate this plugin with PayPal Website Payments Pro. PayPal Express is automatically an option at checkout with that gateway.
	
This plugin will only work when the primary gateway is an onsite gateway. At this time, this includes:
* Stripe
* Braintree
* Authorize.net
* PayPal Payflow Pro
* Cybersource

== Frequently Asked Questions ==

= I found a bug in the plugin. =

Please post it in the issues section of GitHub and we'll fix it as soon as we can. Thanks for helping. https://github.com/strangerstudios/pmpro-add-paypal-express/issues

== Changelog ==
= .5.2 =
* ENHANCEMENT: Improved fields display on membership checkout page to use no tables for compatibility with Paid Memberships Pro v1.9.4.

= .5.1 =
* ENHANCEMENT: Added stylesheet to improve the layout of the frontend checkout form.
* ENHANCEMENT: Improved setup instructions.

= .5 =
* BUG: Fixed bug where the Choose Payment Method box would disappear after submitting.
* BUG/ENHANCEMENT: Updated to better support using this addon along with the Pay by Check addon. Make sure both are up to date.

= .4.1 =
* BUG: Updated the pmproappe_using_paypal function to not consider Payflow Pro a PayPal gateway. We want to show PayPal Express as a second option if the main gateway is Payflow.

= .4 =
* BUG: Added pmpro_btn-submit-checkout class to the PayPal checkout button. When paired with PMPro core updates, this fixes issues where the PayPal button remained disabled after trying to checkout via the default gateway first.
* ENHANCEMENT: Changed "payment method" section to use div formatting instead of tables.
* ENHANCEMENT: Fixed cases where the plugin was active and the primary gateway was also set to PayPal or PayPal Express. Now shows a message to change the gateway setting or deasctivate the plugin.

= .3 =
* Fixed bug where payment fields could show up on the review page when checking out with PayPal Express.

= .2.2.1 =
* Fixed bug where PayPal button was sometimes showing up for free levels.

= .2.2 =
* Now checking if a discount code makes a level free when applied and adjusting the billing fields/etc.

= .2.1 =
* Now checking the pmpro_require_billing JS var to make sure we don't show the credit card fields when using other addons like pmpro-address-free-levels.

= .2 =
* Using different hook to add gateway option to checkout page.

= .1 =
* This is the initial version of the plugin.
