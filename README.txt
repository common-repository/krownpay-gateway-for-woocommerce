=== KrownPay Gateway for WooCommerce ===
Contributors: krownpay
Tags: woocommerce, gateway, payments
Requires at least: 5.0
Tested up to: 6.6.2
Stable tag: 1.0
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

KrownPay offers a secure payment gateway solution for seamless transactions on your WooCommerce store.

== Installation ==

=== Download the Plugin ===
Obtain the KrownPay WooCommerce Plugin from the official source or marketplace where it is available.

=== Upload the Plugin to WordPress ===
1. Navigate to the WordPress admin dashboard.
2. Go to `Plugins > Add New`.
3. Click on the `Upload Plugin` button.
4. Choose the downloaded KrownPay WooCommerce Plugin ZIP file and click `Install Now`.

=== Install the Plugin ===
Once uploaded, click `Activate Plugin` to activate the KrownPay WooCommerce Plugin.

== Activation ==

=== Activate the Plugin ===
1. After installing, navigate to `Plugins > Installed Plugins`.
2. Locate the KrownPay WooCommerce Plugin and click `Activate`.

== Configuration ==

=== Adding WooCommerce Credentials ===
1. Access WooCommerce Settings: Navigate to `WooCommerce > Settings` in the WordPress admin dashboard.
2. Generate your WooCommerce consumer key and secret key.
3. Locate the KrownPay Section: In the WooCommerce settings, find the `Payments` tab.
4. Click on `KrownPay` to access the settings for the KrownPay Gateway for WooCommerce Plugin.
5. Enter WooCommerce Credentials: Enter your WooCommerce store credentials, including API keys and any required authentication details.
6. Save the changes.

== Creating a Store on KrownPay ==
1. Visit [KrownPay](https://stg.app.krownpay.com/): Go to the link and sign up for an account if you don't have one or use this test credential:
email: test123@mail.com
password: krown123456.
2. Create a New Store: After logging in, navigate to the `Stores` section.
3. Click on `Create Store` and fill in the required information about your store (e.g., name, URL).
4. Take note of the Merchant ID, Secret Key and Gateway URL. as they are all be auto generated.

== Generating a Gateway URL ==
1. Access Your KrownPay Store: In the KrownPay dashboard, go to the `Stores` section.
2. Select the store you created.
3. Generate Gateway URL: Navigate to the `Gateway Settings`.
4. Generate a new Gateway URL for your store. This URL will be used to process payments.
5. Copy the Gateway URL for use in the plugin settings.

== Adding the Gateway URL to KrownPay Settings ==
1. Access KrownPay Plugin Settings: In the WordPress admin dashboard, go to `WooCommerce > Settings > Payments`.
2. Click on `KrownPay` to open the settings.
3. Enter the Gateway URL: Paste the copied Gateway URL into the `Gateway URL` field.
4. Save the changes.

== Using KrownPay for Checkout ==
1. Enable KrownPay Payment Method: Ensure that the KrownPay payment method is enabled in the WooCommerce payment settings.
2. Add Items to Cart: Customers can browse your WooCommerce store and add items to their cart as usual.
3. Proceed to Checkout: When ready, customers can proceed to checkout. On the checkout page, they will see the option to pay with KrownPay.
4. Select KrownPay: Customers select KrownPay as their payment method and click `Place Order`.

== Payment Flow ==
1. Generate Payment Link: Upon selecting KrownPay and placing the order, a payment link is generated.
2. Redirect to Payment Page: The customer is redirected to the KrownPay payment page via the generated link.
3. Complete Payment: The customer completes the payment using their chosen cryptocurrency on the KrownPay payment page.
4. Redirect to Success Page: After successful payment, the customer is redirected back to the WooCommerce site’s success page.

== Troubleshooting ==
- **Payment Issues**: If customers encounter issues during payment, ensure the Gateway URL is correctly configured and your KrownPay store settings are accurate.
- **Plugin Conflicts**: Deactivate other plugins to check for conflicts that may interfere with KrownPay functionality.
- **Logs**: Enable logging in the KrownPay settings for detailed troubleshooting information.

== FAQs ==
- **What cryptocurrencies does KrownPay support?**
  KrownPay supports a wide range of cryptocurrencies. Please refer to the KrownPay website for the most up-to-date list of supported currencies.

- **How do I update the plugin?**
  Update the plugin via the WordPress admin dashboard under `Plugins > Installed Plugins`. Click on `Update Now` if a new version is available.

- **Can I issue refunds via KrownPay?**
  Refunds must be processed manually through KrownPay and WooCommerce. Refer to both platforms’ documentation for detailed instructions on issuing refunds.

== Support ==
For additional support, contact KrownPay support through their support portal or refer to the documentation provided on their website. For WooCommerce-related issues, consult the WooCommerce support forums or documentation.

== Changelog ==

= 1.0 =
* Initial release.

== License ==

This plugin is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.


== Technical Flow  ==

The KrownPay Gateway for WooCommerce plugin integrates securely with KrownPay's payment platform, enabling merchants to generate custom payment links for each order placed in their store. During configuration, each merchant is provided with a unique gateway URL, which plays a key role in establishing a secure connection between their store and the KrownPay payment gateway.

In the krownpay.php file, the merchant's gateway URL ($gateway) is carefully sanitized using sanitize_text_field() to prevent injection attacks and ensure data integrity. This sanitized URL is then used to build the endpoint (/gtw/v1/woocommerce/orders), where the plugin initiates a secure POST request via wp_remote_post(). The request, which includes properly structured headers and a validated request body, communicates with the KrownPay API to create an order and generate a unique payment link for the customer.

All data exchanged between the store and KrownPay is transmitted over HTTPS, ensuring that sensitive information, including order details, is encrypted during transit. This encrypted communication prevents unauthorized interception and guarantees that the transaction data remains confidential.

Once the payment link is generated, the customer is redirected to a custom checkout page where they can complete their transaction using any of their connected wallets. Upon successful payment, the customer is automatically redirected back to the  store, and the order status is updated in real-time within the merchant's backend, ensuring accurate tracking of all transactions.

KrownPay prioritizes security and privacy. We do not store or have access to any private keys—neither from the customer nor the merchant. At no point during the integration process will we request these keys, maintaining the highest standard of data protection throughout the transaction lifecycle.



== Privacy Policy ==
https://www.krownpay.com/privacy


== Terms and Conditions ==
https://stg.app.krownpay.com/terms






