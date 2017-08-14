=== Plugin Name ===
Contributors: nuapay-wordpress
Tags: nuapay, mandates, emandates, sentenial
Requires at least: 3.0.1
Tested up to: 4.4
Stable tag: 1.0.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Sign SEPA and BACS mandates online and paper-free.  Collect your Direct Debits using Nuapay and Sentenial. Smart payment management with Nuapay.

== Description ==

The Nuapay eMandates plugin; Sign SEPA and BACS mandates online via your website and collect your Direct Debits using Nuapay.
Collect your Direct Debits using Nuapay and Sentenial.

== Installation ==

This section describes how to install the plugin and get it working onto your wordPress web site.

= Minimum Requirements =
1. Nuapay Account access
1. A Nuapay Account permitting you to collect direct debits

= Automatic installation =
Automatic installation is the easiest option as WordPress handles the file transfers itself and you don't need to leave your web browser. To do an automatic install of Nuapay, log in to your WordPress dashboard, navigate to the Plugins menu and click Add New.
In the search field type 'Nuapay' and click Search Plugins. Once you've found our eMandate plugin you can view details about it such as the version, rating and description and you can install it by simply clicking 'Install Now'.

= Manual installation =
The manual installation method involves downloading our Nuapay plugin and uploading it to your webserver via your FTP application.
1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Use the Settings->Plugin Name screen to configure the plugin

= Updating =
Automatic updates should work like a charm; as always though, ensure you backup your site just in case.
If on the off-chance you do encounter issues with the mandate signing pages after an update you simply need to flush the permalinks by going to WordPress > Settings > Permalinks and hitting 'save'. That should return things to normal.


== Frequently Asked Questions ==

= Where can I find out more about Nuapay =

Here's a link to [Nuapay.com](http://www.nuapay.com/ "Nuapay")

= What does this plugin do? =

It allows your customers to sign mandates online, emandates, allowing you to collect funds from their bank accounts by Direct Debits for your services.

= Where can I report bugs or contribute to the project? =
Bugs can be reported to our Nuapay Customer Support team via Nuapay.com 



= What does this plugin do? =

It allows your customers to sign emandates, allowing you to collect Direct Debits for your services.

== Screenshots ==

1. This plugins configuration screen. You will be given your urls and API key when you sign up for the service via nuapay.com
2. This shows the shortcode that allows you to configure the sign button that will be rendered in your WordPress site. Details of each parameter are included in the shortcode section.
3. This Button is what a button looks like when rendered, it will take your templates style for buttons into account
4. This is the sample mandate signup screen; Your customer details are entered into this screen.

== Changelog ==

= 1.0.2, 1.0.3 =
* Following WordPress recommended release and tagging process.

= 1.0.1 =
* Using REST behind the scenes now.
* Ability to include mandate id as a query parameter to support email and sms campaings with assignable mandate ids.

= 1.0.0 =
* First Version of the Plugin.

== Upgrade Notice ==

= 1.0.3 =
Publishing to WordPress Repo.

= 1.0.2 =
Publishing to WordPress Repo.

= 1.0.1 =
Collect Direct Debits Easily With Nuapay. New Features.

= 1.0 =
Collect Direct Debits Easily With Nuapay.

== shortcode ==

A sample shortcode is included below

`[nuapay api_key="4d9x1x0798882x0b02c0a81017b3497cxxxxxxxxx95ffbeb0fcbb0" scheme_type="CORE" payment_type="RCUR" scheme_id="IE26ZZZ123456799" creditor_iban="IE73AIBK93310475555570" integration_type="REDIRECT"]`

Parameter Details:

* api_key identifies you the creditor this will be provided to you when you signup via nuapay.com
* scheme_type describes the Scheme type you wish to sign mandates for examples are CORE,B2B and BACS
* payment_type indicates this mandate can collect recurring payments
* scheme_id the creditor scheme identifier 
* creditor_iban the collecting account you will be collecting the direct debits into
* integration type for now this plugin only supports redirect.

== query params ==

Sample supported query param(s) are included below.

id=XYZX

Query Param Details:

* id The mandate id to use for the mandate, this is useful if you want to use a custom mandate reference in an email campaign for example.