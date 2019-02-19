=== Advanced Orders Export For WooCommerce ===
Contributors: algolplus
Donate link: http://algolplus.com/plugins/
Tags: woocommerce,export,order,xls,csv,xml,woo export lite,export orders,orders export,csv export,xml export,xls export
Requires at least: 4.2.4
Tested up to: 4.4
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily export Woocommerce orders to Excel/CSV/XML/Json file

== Description ==
This plugin helps you to easily export WooCommerce order data. 

You can export all details or select the fields you want to download using the various powerful filters. 

Mark your WooCommerce orders and run "Export as..." a bulk operation.

Export any custom field assigned to orders/products/coupons is easy and you can select from various formats to export the data in such as CSV, XLS, XML and JSON.

Export Includes:

* order data
* summary order details (# of items, discounts, taxes etc…)
* customer details (both shipping and billing)
* product attributes
* coupon details
* CSV, XLS, XML and JSON formats

Features

* export WooCommerce custom fields or terms for products/orders
* apply powerful filters
* select the fields to export
* rename labels
* reorder columns and much more

Use this plugin to export orders for

* sending order data to 3rd part drop shippers
* updating your accounting system
* analysing your order data

Have an idea or feature request?
Please create a topic in the "Support" section with any ideas or suggestions for new features.

= Pro Version =
Are you looking to have your Woocommerce products drop shipped from a third party? Our plugin can help you export your orders to CSV/XML/etc and send them to your drop shipper. You can even automate this process with [Pro version](http://algolplus.com/plugins/downloads/orders-export-pro-for-woocommerce/) .

== Installation ==
= Automatic Installation =
Go to Wordpress dashboard, click  Plugins / Add New  , type 'order export lite' and hit Enter.
Install and activate plugin, visit WooCommerce > Export Orders.
= Manual Installation =
[Please, visit the link and follow the instructions](http://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation)

== Frequently Asked Questions ==

= Progress bar does nothing after 100% for XLS format =

PHP must be compiled with option  --enable-zip

= I am exporting the Orders data with its individual Products/Items as separate rows. But the Product rows don't seem to have rest of order details included. =

You should mark checkbox "Populate other columns if products exported as rows" for selected format ( CSV or XLS)

= Could I request any new feature ? =

Yes, you could email a request to aprokaev@gmail.com. We intensively develop this plugin.

== Screenshots ==

1. Default view.  You could click 'Express Export' to get results.
2. It's possible to filter orders by many parameters, not only by order date or status.
3. You could select the fields to export, rename labels, reorder columns.
4. The preview works for all formats.
5. You could add custom field or taxonomy as new column to export.
6. You could select orders to export.

== Changelog ==

= 1.1.11 - 2016-04-27 =
* Added filter by custom fields (for order)
* Coded fallback if the plugin can't create files in folder "/tmp"
* Added new hooks/filters

= 1.1.10 - 2016-03-30 =
* "Filter by product" allows to export only filtered products
* Fixed bug for meta fields with spaces in title
* Fixed bug for XML/Json fields ( unable to rename )
* Added new hooks/filters
* Added extra UI alerts
* Added tab "Profiles" (Pro version)


= 1.1.9 - 2016-03-14 =
* Disable Object Cache during export
* Added fields : Line Subtotal, Order Subtotal, Order Total Tax

= 1.1.8 - 2016-03-07 =
* Added link to PRO version
* Fixed few minor bugs

= 1.1.7 - 2016-02-18 =
* Added options "prepend/append raw XML"
* Added column "Item#" for Products
* Fixed custom fields for Products

= 1.1.6 - 2016-02-04 =
* Added column "Total weight" (to support Royal Mails DMO)
* Display progressbar errors during export

= 1.1.5 - 2016-01-21 =
* Fixed another bug for product custom fields

= 1.1.4 - 2016-01-13 =
* Added custom css to our pages only

= 1.1.3 - 2015-12-18 =
* Ability to export selected orders only
* Fixed bug for product custom fields
* Fixed progressbar freeze

= 1.1.2 - 2015-11-11 =
* Fixed path for temporary files
* Export coupon description

= 1.1.1 - 2015-10-27 =
* Export products taxonomies

= 1.1.0 - 2015-10-06 =
* Order exported records by ID
* Corrected extension for xlsx files
* Fixed bug for "Fields Setup"

= 1.0.6 - 2015-09-28 =
* Attribute filter shows attribute values.
* Shipping filter shows values too.

= 1.0.5 - 2015-09-09  =
* Filter by product taxonomies

= 1.0.4 - 2015-09-04 =
* Export to XLS

= 1.0.3 =
* Partially support outdated Select2 (some plugins still use version 3.5.x)
* Fixed problem with empty file( preview was fine)

= 1.0.2 - 2015-08-25 =
* Added Progress bar
* Added new csv option "Populate other columns if products exported as rows"

= 1.0.1 - 2015-08-11 =
* Added Russian language


= 1.0.0 - 2015-08-10  =
* First release.