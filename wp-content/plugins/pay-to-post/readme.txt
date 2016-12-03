=== Plugin Name ===
Contributors: PluginCentral
Donate link: www.paytopost-plugin.com/donate/
Tags: pay to post, charge posting, charge publish, take payment, charge for posting, paypal, pay for posting, pay for publishing
Requires at least: 4.0.0
Tested up to: 4.3
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A plugin to allow administrators to set a price for publishing a post on a site.

== Description ==

This plugin allows a site administrator to set a price which users of the site will have to pay to publish a post on the site. The price is set on a per post basis. Different prices can be set for different user roles. Any post type can be charged for, including custom post types. A shortcode has been included so that this functionality can be used on front end forms.

Payment for each post published will be deducted from a local account on the site. Credit can be added to this local account using PayPal.

= Main Features =

*	Charge for the publication of any post type.
*	Different charges can be applied to different user roles.
*	Users add credit to their local account using PayPal.
*	Charges can be applied to both builtin and custom post types.
*	A shortcode can be used to provide this functionality on front end forms and pages.

== Installation ==

= Automatic Installation =

1. In your WordPress dashboard go to Plugins > Add New > search for pay to post.
2. Click Install Now on the Pay To Post plugin. Make sure you install the correct plugin, Pay To Post by PluginCentral

= Manual Installation =

1.	Click the Download Version button above, save the file to a handy location.
2.	Extract the zip file to path_to_your_site/wp-content/plugins/, this should create a pay-to-post folder.

= Enable the plugin =

In your WordPress dashboard go to Plugins > Installed Plugins > click Activate just under Pay To Post.

= Using this plugin =

In your WordPress dashboard go to Settings > Pay To Post. Here you'll be able to set payment amounts.

NB When you add, edit or delete a new charging rule, you must click Save Changes. If you don't click Save Changes, your changes will be lost.

Each of the following shortcodes must be set up on it's own page:

*	[ptp_payment-form]
*	[ptp_confirmatin_form]
*	[ptp_transaction_display]
*	[ptp_cancellation_display]

If you do not set up a page for each of these shortcodes then the plugin will not function correctly.

You must enter your PayPal account details for the plugin to function correctly. The PayPal details which you enter will be the account into which users payments will be made.

It is strongly recommended that you test your site using the Sandbox mode - this will ensure that no 'real' money is transferred, until you are sure your site is functioning correctly.

NB This plugin is published with no warrenty - it's up to you to ensure that it works correctly with your site. No liability will be accepted for any losses encurred by the use of this plugin.

== Frequently Asked Questions ==

= I've found a bug, what do I do? =

Click on the View support forum button on the right.

= How does a user know they will be charged? =

A message will be displayed, informing the user that they will be charged for publishing a post. If the user has sufficient credit in their local account, the cost of posting will be displayed along with their current balance. If the user does not have sufficient credit a link will be displayed to the payment page.

= How can I use the shortcode? = 

To use the Pay To Post functionality on any front end forms (IE anything outside of the admin pages) you'll need to add a shortcode to your page:

`[pay_to_post type="post_type"] your form code [/pay_to_post]`

The following parameters can be used with the shortcode:

`type - this is the post type that the form is dealing with.
	Values for this can be any post type or custom post type, that you currently have.
	Default is post.`

	
Example:

`[pay_to_post type="post"] your form code [/pay_to_post]`




== Screenshots ==

1.	Settings > Pay To Post admin panel, showing charging rules.
2. 	Adding or editing a new charging rule.
3.	Publication charging message.

== Changelog ==

= 1.0.0 = 
Initial release

== Upgrade Notice ==

= 1.0 = 
Initial release