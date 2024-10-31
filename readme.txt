=== Quantity Calculator for Woocommerce ===
Contributors: enituretechnology
Tags: Quantity Calculator for Woocommerce,shipping estimate, woocommerce,eniture,eniture technology
Requires at least: 6.4
Tested up to: 6.6.1
Stable tag: 1.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Quantity Calculator for Woocommerce. Fifteen day free trial.

== Description ==

Calculates the amount of product needed based on customer input for area or cubic volume.

**Key Features**

* Calculates coverage area (for example Square feet) from product quantity.
* Calculates quantity from coverage area

**Requirements**

* WooCommerce 6.4 or newer.
* A license from Eniture Technology.

== Installation ==

**Installation Overview**

A more comprehensive and graphically illustrated set of instructions can be found on the *Documentation* tab at
[eniture.com](https://eniture.com/woocommerce-quantity-calculator/).

**1. Install and activate the plugin**
In your WordPress dashboard, go to Plugins => Add New. Search for "Quantity Calculator for Woocommerce", and click Install Now.
After the installation process completes, click the Activate Plugin link to activate the plugin.

**2. Get a license from Eniture Technology**
Go to [Eniture Technology](https://eniture.com/woocommerce-quantity-calculator/) and pick a
subscription package. When you complete the registration process you will receive an email containing your Eniture API Key and
your login to eniture.com. Save your login information in a safe place. You will need it to access your customer dashboard
where you can manage your licenses and subscriptions. A credit card is not required for the free trial. If you opt for the free trial you will need to login to your [Eniture Technology](http://eniture.com) dashboard before the trial period expires to purchase a subscription to the license. Without a paid subscription, the plugin will stop working once the trial period expires.

**3. Establish the connection**
Go to WooCommerce => Settings => Quantity Calculator. Use the *Settings* link to verify connection.

**5. Select the plugin settings**
Go to WooCommerce => Settings => Quantity Calculator. Use the *Settings* link to enter the required information and choose
the optional settings.

== External Service Usage ==

A Eniture API Key from eniture.com is required to use this plugin. Please review the following information regarding the usage of this service:

1. **Functionality**:
   - This plugin leverages a 3rd party service called "Eniture's Webservices" to validate the Eniture API Key. To ensure proper functionality, the plugin transmits a Eniture API Key provided by the user during the setup process. This Eniture API Key is used for user verification with Eniture's Webservices.

2. **Data Transmission**:
   - The data transmission occurs during the execution of a specific function within the plugin's code. Specifically, the request is sent on line 276 of the `en-quantity-calculator/includes/class-en-quantity-calculator-register-hooks.php` file.
   - Additionally, the same function mentioned above is used to test the API credentials.

4. **Service Provider**:
   - For more information about Eniture's Webservices, please visit their website: https://eniture.com/.

You can also access their terms of use and privacy policy here: https://eniture.com/eniture-llc-privacy-policy/ & https://eniture.com/eniture-technology-terms-of-use/.

== Changelog ==

= 1.0.2 =
* Update: Updated connection tab according to WordPress requirements

= 1.0.1 =
* Update: Enhanced UI on the product detail page and the connection settings page

= 1.0 =
* Initial release.

== Upgrade Notice ==
