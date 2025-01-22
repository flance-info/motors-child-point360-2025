<?php
/**
 * Automatic Payment Processor
 * Handles automatic payment processing for WooCommerce orders
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}



/**
 * Process automatic payment for an order
 *
 * @param int $order_id WooCommerce order ID
 * @return bool Whether payment processing was successful
 */
function process_automatic_payment($order_id) {
    $order = wc_get_order($order_id);
    
    if (!$order) {
        return false;
    }

    // Get available payment gateways
    $available_gateways = WC()->payment_gateways()->get_available_payment_gateways();
    
    // Set payment method to first available gateway (usually this would be your default payment method)
    $payment_method = isset($available_gateways['bacs']) ? 'bacs' : array_key_first($available_gateways);
    
    if ($payment_method) {
        $order->set_payment_method($payment_method);
        $order->set_status('completed');
        $order->save();
        
        // Clear cart
        WC()->cart->empty_cart();
        
        return true;  // Return true instead of redirecting
    }
    
    return false;
}


/**
 * Create and process automatic order
 *
 * @param int $product_id Product/Listing ID to process
 * @param float $price Price for the product
 * @return array Response array with status and messages
 */
function create_and_process_automatic_order($product_id, $price) {
    $response = array(
        'success' => false,
        'message' => '',
        'url' => ''
    );

    if (!class_exists('WooCommerce')) {
        $response['message'] = esc_html__('WooCommerce is not active', 'stm_vehicles_listing');
        return $response;
    }

    // Update product price and meta
    update_post_meta($product_id, '_price', $price);
    update_post_meta($product_id, 'pay_per_listing', 'pay');
    
    // Clear cart and add new product
    empty_woocommerce_cart_if_not_empty();
    WC()->cart->add_to_cart($product_id);
    
  
    stm_add_safe_product();
    // Create order
    $checkout = WC()->checkout();
    $order_id = $checkout->create_order(array());
    
    if ($order_id) {
        process_automatic_payment($order_id);
        $response['success'] = true;
        $response['url'] = get_author_posts_url(get_current_user_id());
        $response['message'] = esc_html__('Payment processed successfully, redirecting to your account', 'stm_vehicles_listing');
        
        // Send email to administrators
        $admin_emails = get_option('admin_email');
        $listing_url = get_permalink($product_id);
        $subject = sprintf(__('New Listing Created: %s', 'stm_vehicles_listing'), get_the_title($product_id));
        
        $message = sprintf(
            __('A new listing has been created and payment processed.

Listing Details:
- Title: %s
- Link: %s
- Created by: %s

You can view the listing by clicking the link above.', 'stm_vehicles_listing'),
            get_the_title($product_id),
            $listing_url,
            wp_get_current_user()->display_name
        );
        
        wp_mail($admin_emails, $subject, $message);
    } else {
        $response['message'] = esc_html__('Failed to create order', 'stm_vehicles_listing');
    }

    return $response;
} 