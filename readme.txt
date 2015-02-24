== Paid Memberships Pro - Add PayPal Express Add On ==
Contributors: strangerstudios
Tags: pmpro, paid memberships pro, gateway, paypal, checkout, registration
Requires at least: 3.6
Tested up to: 3.9.1
Stable tag: .2.2.1

Add PayPal Express as an Alternate Payment Option at Checkout

== Description ==

This plugin enables a radio select on the membership checkout page, allowing a user to pay onsite with your credit card payment gateway or to pay via PayPal.

== Installation ==

1. Upload the 'pmpro-add-paypal-express' directory to the '/wp-content/plugins/' directory of your site.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Set your PayPal Express API key, username, and password set in the PMPro Payment Settings.
1. After inputting your PayPal Express settings and clicking save, switch back to your primary gateway and save those settings. (The PayPal Express settings will be remembered "in the background".)

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
