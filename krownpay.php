<?php
/*
Plugin Name: KrownPay Gateway for WooCommerce
Plugin URI: https://krownpay.com/
Description: Extends WooCommerce with the KrownPay gateway.
Version: 1.0
Author: Krownpay
Copyright: ©
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
add_action('plugins_loaded', 'krownpay_gateway_for_woocommerce_init', 0);

function krownpay_gateway_for_woocommerce_init() {
    if (!class_exists('WC_Payment_Gateway')) return;

    class WC_KrownPay_Gateway extends WC_Payment_Gateway {

        public function __construct() {
            $this->id = 'krownpay';
            $this->method_title = __('KrownPay', 'krownpay-gateway-for-woocommerce');
            $this->method_description = __('Pay with KrownPay', 'krownpay-gateway-for-woocommerce');
            $this->has_fields = false;

            $this->init_form_fields();
            $this->init_settings();

            $this->title = $this->get_option('title');
            $this->description = $this->get_option('description');

            add_action('update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        }

        public function init_form_fields() {
            $this->form_fields = array(
                'enabled' => array(
                    'title' => __('Enable/Disable', 'krownpay-gateway-for-woocommerce'),
                    'type' => 'checkbox',
                    'label' => __('Enable KrownPay Gateway', 'krownpay-gateway-for-woocommerce'),
                    'default' => 'no',
                ),
                'title' => array(
                    'title' => __('Title', 'krownpay-gateway-for-woocommerce'),
                    'type' => 'text',
                    'description' => __('This controls the title which the user sees during checkout.', 'krownpay-gateway-for-woocommerce'),
                    'default' => '',
                    'desc_tip' => true,
                ),
                'description' => array(
                    'title' => __('Description', 'krownpay-gateway-for-woocommerce'),
                    'type' => 'textarea',
                    'description' => __('This controls the description which the user sees during checkout.', 'krownpay-gateway-for-woocommerce'),
                    'default' => __('Pay with KrownPay', 'krownpay-gateway-for-woocommerce'),
                ),
                'consumer_key' => array(
                    'title' => __('Woocommerce Consumer Key', 'krownpay-gateway-for-woocommerce'),
                    'type' => 'text',
                    'description' => __('Enter your KrownPay Consumer Key.', 'krownpay-gateway-for-woocommerce'),
                    'default' => '',
                    'desc_tip' => true,
                ),
                'consumer_secret' => array(
                    'title' => __('Woocommerce Consumer Secret', 'krownpay-gateway-for-woocommerce'),
                    'type' => 'text',
                    'description' => __('Enter your KrownPay Consumer Secret.', 'krownpay-gateway-for-woocommerce'),
                    'default' => '',
                    'desc_tip' => true,
                ),
                'krownpay_merchant_id' => array(
                    'title' => __('KrownPay Merchant ID', 'krownpay-gateway-for-woocommerce'),
                    'type' => 'text',
                    'description' => __('Enter your KrownPay Merchant ID.', 'krownpay-gateway-for-woocommerce'),
                    'default' => '',
                    'desc_tip' => true,
                ),
                'krownpay_consumer_secret' => array(
                    'title' => __('Krownpay Consumer Secret', 'krownpay-gateway-for-woocommerce'),
                    'type' => 'text',
                    'description' => __('Enter your KrownPay Consumer Secret.', 'krownpay-gateway-for-woocommerce'),
                    'default' => '',
                    'desc_tip' => true,
                ),
                'gateway_url' => array(
                    'title' => __('Gateway Url', 'krownpay-gateway-for-woocommerce'),
                    'type' => 'text',
                    'description' => __('Enter your Gateway Url.', 'krownpay-gateway-for-woocommerce'),
                    'default' => '',
                    'desc_tip' => true,
                )
            );
        }

public function process_payment($order_id) {
    // Add nonce life filter
    add_filter('nonce_life', function() { return 3600; });

    // Retrieve credentials from the plugin options
    $consumer_key = $this->get_option('consumer_key');
    $consumer_secret = $this->get_option('consumer_secret');
    $krownpay_merchant_id = $this->get_option('krownpay_merchant_id');
    $krownpay_consumer_secret = $this->get_option('krownpay_consumer_secret');
    $gateway = $this->get_option('gateway_url');

    // Get the order details
    $order = wc_get_order($order_id);
    $order_total = $order->get_total();
    $order_currency = $order->get_currency();
    $order_success_url = $order->get_checkout_order_received_url();
    $order_cancel_url = $order->get_cancel_order_url();
    
    // Order Items
    $items = $order->get_items();
    $order_items = array();

    foreach ($items as $item_id => $item) {
        $product = $item->get_product(); // Get the WC_Product object
        $product_name = $product->get_name();
        $product_quantity = $item->get_quantity();
        $product_price = $item->get_total() / $item->get_quantity(); // Calculate individual price
        
        $order_items[] = (object) array('name' => $product_name, 'quantity' => $product_quantity, 'price' => $product_price);
    }

    // Mark the order as processing or on-hold (depending on your business flow)
    $order->update_status('processing', __('Payment redirected to KrownPay.', 'krownpay-gateway-for-woocommerce'));

    // Request data
    $body_data = array(
        'merchantOrderId' => $order_id,
        'total' => $order_total,
        'currency' => $order_currency,
        'orderSuccessUrl' => $order_success_url,
        'orderCancelUrl' => $order_cancel_url,
        'items' => $order_items
    );

    // Construct the request body
    $request_body = wp_json_encode($body_data);

    // Generate custom headers for authentication
    $nonce = wp_create_nonce('new_order-' . $order_id);
    $timestamp = time();
    $signature_payload = $krownpay_merchant_id . ':' . $gateway . ':' . $nonce . ':' . $timestamp;
    $signature = hash_hmac('sha256', $signature_payload, $krownpay_consumer_secret);
    
    $headers = array(
        'Content-Type' => 'application/json',
        'X-Merchant-Id' => sanitize_text_field($krownpay_merchant_id),
        'X-Nonce' => sanitize_text_field($nonce),
        'X-Timestamp' => sanitize_text_field($timestamp),
        'X-Signature' => sanitize_text_field($signature),
        'X-Woocommerce-Consumer-Key' => sanitize_text_field($consumer_key),
        'X-Woocommerce-Secret-Key' => sanitize_text_field($consumer_secret),
        'X-Gateway-Url' => sanitize_text_field($gateway),
        'X-Woocommerce-Url' => home_url()
    );

    // Call the endpoint to get the redirect URL - https://<gateway>/gtw/v1/
    $endpoint_url = 'https://' . sanitize_text_field($gateway) . '/gtw/v1/woocommerce/orders'; // Replace with the actual endpoint URL
    $response = wp_remote_post($endpoint_url, array(
        'method' => 'POST',
        'headers' => $headers,
        'body' => $request_body
    ));

    // Remove nonce life filter
    remove_filter('nonce_life', function() { return 3600; });

    if (is_wp_error($response)) {
        // Handle error
        error_log('Error occurred: ' . $response->get_error_message());
    } else {
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (isset($data['data'])) {
            $redirect_url = esc_url_raw($data['data']);
        } else {
            // Handle invalid response from the endpoint
            error_log('Invalid response from the endpoint');
        }

        // Log the response content to the console
        error_log('Response: ' . $redirect_url);
    }

    return array(
        'result' => 'success',
        'redirect' => $redirect_url
    );
}

// ... existing code ...
    }
}

add_action('rest_api_init', 'kgfw_callback_endpoint');


function kgfw_callback($request) {
    // Handle callback functionality here
    $body_data = $request->get_body_params();
    $order_id = sanitize_text_field($body_data['orderId']);
    $order_status = sanitize_text_field($body_data['status']);

    $order = wc_get_order($order_id);

    if ($order_status == 'completed') {
        $order->payment_complete();
        return new WP_REST_Response(array('message' => 'Callback processed successfully'), 200);
    }
}

function kgfw_callback_permission($request) {
    // Get nonce from headers
    $body_data = $request->get_body_params();

    // Check if the 'nonce' exists
    if (!isset($body_data['nonce'])) {
        return new WP_REST_Response(array('error' => 'Missing nonce'), 403); // Forbidden
    }

    // Get nonce and orderId from request body
    $nonce = sanitize_text_field($body_data['nonce']);
    $order_id = sanitize_text_field($body_data['orderId']);

    // Verify nonce
    if (!wp_verify_nonce($nonce, 'new_order-' . $order_id)) {
        return new WP_REST_Response(array('error' => 'Invalid nonce'), 403);
    }
}

function kgfw_callback_endpoint() {
    register_rest_route('krownpay/v1', '/callback', array(
        'methods' => 'POST',
        'callback' => 'kgfw_callback',
        'permission_callback' => 'kgfw_callback_permission', // Handle permission
    ));
}

function kgfw_krownpay_gateway($methods) {
    $methods[] = 'WC_KrownPay_Gateway';
    return $methods;
}

add_filter('woocommerce_payment_gateways', 'kgfw_krownpay_gateway');

?>
