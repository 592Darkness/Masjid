<?php
/**
 * Payment Gateway Integration for Guyana
 * Add this code to your functions.php
 */

/**
 * Filter WooCommerce available payment gateways
 */
function cardealership_payment_gateways($gateways) {
    // Make sure Credit Card/Debit Card payment is prioritized
    if (isset($gateways['stripe'])) {
        // Customize Stripe settings for Guyana
        add_filter('woocommerce_stripe_request_body', 'cardealership_customize_stripe_for_guyana', 10, 2);
    }
    
    // Add support for Guyana-specific payment methods if needed
    
    return $gateways;
}
add_filter('woocommerce_available_payment_gateways', 'cardealership_payment_gateways');

/**
 * Customize Stripe for Guyana
 */
function cardealership_customize_stripe_for_guyana($request_body, $order) {
    // Set the currency to GYD (Guyanese Dollar)
    if (isset($request_body['currency'])) {
        $request_body['currency'] = 'gyd';
    }
    
    // Add Guyana-specific Stripe settings if needed
    
    return $request_body;
}

/**
 * Add custom WooCommerce Checkout Fields for Guyana
 */
function cardealership_checkout_fields($fields) {
    // Modify country field to have Guyana as default
    $fields['billing']['billing_country']['default'] = 'GY';
    
    // Add National ID field
    $fields['billing']['billing_national_id'] = array(
        'label'       => __('National ID Number', 'cardealership-child'),
        'placeholder' => __('Enter your ID number', 'cardealership-child'),
        'required'    => false,
        'class'       => array('form-row-wide'),
        'clear'       => true,
        'priority'    => 35,
    );
    
    return $fields;
}
add_filter('woocommerce_checkout_fields', 'cardealership_checkout_fields');

/**
 * Save custom checkout fields
 */
function cardealership_checkout_field_save($order_id) {
    if (isset($_POST['billing_national_id'])) {
        update_post_meta($order_id, '_billing_national_id', sanitize_text_field($_POST['billing_national_id']));
    }
}
add_action('woocommerce_checkout_update_order_meta', 'cardealership_checkout_field_save');

/**
 * Display custom checkout fields in admin order page
 */
function cardealership_admin_order_data($order) {
    $order_id = $order->get_id();
    $national_id = get_post_meta($order_id, '_billing_national_id', true);
    
    if ($national_id) {
        echo '<p><strong>' . __('National ID:', 'cardealership-child') . '</strong> ' . esc_html($national_id) . '</p>';
    }
}
add_action('woocommerce_admin_order_data_after_billing_address', 'cardealership_admin_order_data');

/**
 * Add Custom Credit/Debit Card Fields (when not using Stripe)
 * Note: Only use this if NOT using Stripe or other standard payment gateways
 */
function cardealership_custom_credit_card_fields() {
    // Only add these fields if using a custom payment solution (not Stripe, PayPal, etc.)
    // WARNING: Do not store credit card information directly in your database unless using a PCI-compliant solution
    
    // Example of custom credit card fields - use only with a proper payment processor!
    /*
    echo '<div class="credit-card-fields">';
    
    echo '<h3>' . __('Credit/Debit Card Information', 'cardealership-child') . '</h3>';
    
    echo '<p class="form-row form-row-wide">';
    echo '<label for="card_name">' . __('Name on Card', 'cardealership-child') . ' <span class="required">*</span></label>';
    echo '<input type="text" class="input-text" id="card_name" name="card_name" required>';
    echo '</p>';
    
    echo '<p class="form-row form-row-wide">';
    echo '<label for="card_number">' . __('Card Number', 'cardealership-child') . ' <span class="required">*</span></label>';
    echo '<input type="text" class="input-text" id="card_number" name="card_number" required>';
    echo '</p>';
    
    echo '<p class="form-row form-row-first">';
    echo '<label for="card_expiry">' . __('Expiry (MM/YY)', 'cardealership-child') . ' <span class="required">*</span></label>';
    echo '<input type="text" class="input-text" id="card_expiry" name="card_expiry" placeholder="MM/YY" required>';
    echo '</p>';
    
    echo '<p class="form-row form-row-last">';
    echo '<label for="card_cvc">' . __('CVC', 'cardealership-child') . ' <span class="required">*</span></label>';
    echo '<input type="text" class="input-text" id="card_cvc" name="card_cvc" required>';
    echo '</p>';
    
    echo '</div>';
    */
}
// Uncomment this line ONLY if implementing custom payment solution:
// add_action('woocommerce_review_order_before_payment', 'cardealership_custom_credit_card_fields');

/**
 * Add extra security for payments
 */
function cardealership_payment_security() {
    // Ensure HTTPS is used for checkout
    if (is_checkout() && !is_ssl()) {
        // Redirect to HTTPS version
        $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        wp_redirect($redirect, 301);
        exit;
    }
}
add_action('template_redirect', 'cardealership_payment_security');

/**
 * WooCommerce SSL Verify Fix for Some Hosts
 */
function cardealership_ssl_verify_fix($args, $url) {
    // Only apply to specific payment endpoints if needed
    if (strpos($url, 'https://api.stripe.com') === 0) {
        $args['sslverify'] = false;
    }
    
    return $args;
}
// Only use this if experiencing SSL verification issues:
// add_filter('http_request_args', 'cardealership_ssl_verify_fix', 10, 2);

/**
 * Add currency for Guyana
 */
function cardealership_add_gyd_currency($currencies) {
    $currencies['GYD'] = __('Guyanese Dollar', 'cardealership-child');
    return $currencies;
}
add_filter('woocommerce_currencies', 'cardealership_add_gyd_currency');

/**
 * Add currency symbol for Guyanese Dollar
 */
function cardealership_add_gyd_currency_symbol($currency_symbol, $currency) {
    if ($currency === 'GYD') {
        $currency_symbol = 'GYD $';
    }
    return $currency_symbol;
}
add_filter('woocommerce_currency_symbol', 'cardealership_add_gyd_currency_symbol', 10, 2);

/**
 * Set Guyanese Dollar as default currency
 */
function cardealership_set_gyd_currency() {
    return 'GYD';
}
add_filter('woocommerce_currency', 'cardealership_set_gyd_currency');

/**
 * Modify order emails with payment instructions
 */
function cardealership_email_instructions($order, $sent_to_admin, $plain_text = false) {
    if (!$sent_to_admin && $order->get_payment_method() === 'cod') {
        echo '<p>' . __('Please note that your order will be processed once payment is received.', 'cardealership-child') . '</p>';
        echo '<p>' . __('For car purchases, our sales team will contact you to arrange the payment and delivery.', 'cardealership-child') . '</p>';
        echo '<p>' . __('For parts, you can make payment upon pickup or delivery.', 'cardealership-child') . '</p>';
    }
}
add_action('woocommerce_email_before_order_table', 'cardealership_email_instructions', 10, 3);
