<?php
/**
 * Stripe Payment Gateway Integration
 * Add this code to your functions.php or a separate file included by functions.php
 */

/**
 * Add custom Stripe settings
 */
function cardealership_stripe_settings($settings) {
    // Add Guyanese currency settings
    $gyd_settings = array(
        'title' => __('Guyanese Dollar Settings', 'cardealership-child'),
        'type' => 'title',
        'description' => __('Configure Stripe settings for Guyanese Dollar transactions.', 'cardealership-child'),
        'id' => 'stripe_gyd_settings',
    );
    
    $settings['gyd_title'] = $gyd_settings;
    
    // Add statement descriptor for Guyana
    $statement_descriptor = array(
        'title' => __('Guyana Statement Descriptor', 'cardealership-child'),
        'type' => 'text',
        'description' => __('Statement descriptors explain charges to customers on their statements. For Guyanese customers, specify a clear descriptor (max 22 characters).', 'cardealership-child'),
        'id' => 'stripe_gyd_statement_descriptor',
        'default' => get_bloginfo('name'),
        'desc_tip' => true,
    );
    
    $settings['gyd_statement_descriptor'] = $statement_descriptor;
    
    // Add exchange rate handling option
    $exchange_handling = array(
        'title' => __('Exchange Rate Handling', 'cardealership-child'),
        'type' => 'select',
        'description' => __('How to handle currency conversion for Stripe (if needed).', 'cardealership-child'),
        'id' => 'stripe_gyd_exchange_handling',
        'options' => array(
            'automatic' => __('Automatic (handled by Stripe)', 'cardealership-child'),
            'manual' => __('Manual (handled by website)', 'cardealership-child'),
        ),
        'default' => 'automatic',
        'desc_tip' => true,
    );
    
    $settings['gyd_exchange_handling'] = $exchange_handling;
    
    // Add manual exchange rate if selected
    $manual_exchange_rate = array(
        'title' => __('GYD to USD Exchange Rate', 'cardealership-child'),
        'type' => 'text',
        'description' => __('If using manual exchange rate handling, enter the current GYD to USD exchange rate. This will convert GYD prices to USD for Stripe processing.', 'cardealership-child'),
        'id' => 'stripe_gyd_exchange_rate',
        'default' => '0.0048', // Default exchange rate (example: 1 GYD = 0.0048 USD)
        'desc_tip' => true,
        'custom_attributes' => array(
            'step' => '0.00001',
            'min' => '0.00001',
        ),
    );
    
    $settings['gyd_exchange_rate'] = $manual_exchange_rate;
    
    // Add manual exchange rate update frequency
    $exchange_rate_frequency = array(
        'title' => __('Exchange Rate Update Frequency', 'cardealership-child'),
        'type' => 'select',
        'description' => __('How often to update the exchange rate.', 'cardealership-child'),
        'id' => 'stripe_gyd_exchange_update_frequency',
        'options' => array(
            'daily' => __('Daily', 'cardealership-child'),
            'weekly' => __('Weekly', 'cardealership-child'),
            'manual' => __('Manual Only', 'cardealership-child'),
        ),
        'default' => 'manual',
        'desc_tip' => true,
    );
    
    $settings['gyd_exchange_update_frequency'] = $exchange_rate_frequency;
    
    // End of Guyanese settings section
    $settings['gyd_end'] = array(
        'type' => 'sectionend',
        'id' => 'stripe_gyd_settings',
    );
    
    return $settings;
}
add_filter('woocommerce_get_settings_checkout', 'cardealership_stripe_settings', 10, 1);

/**
 * Modify Stripe amount for GYD currency
 */
function cardealership_modify_stripe_amount($amount, $order) {
    if (get_woocommerce_currency() === 'GYD') {
        $exchange_handling = get_option('stripe_gyd_exchange_handling', 'automatic');
        
        if ($exchange_handling === 'manual') {
            // Get the exchange rate from settings
            $exchange_rate = get_option('stripe_gyd_exchange_rate', 0.0048);
            
            // Convert GYD to USD
            $amount = round($amount * $exchange_rate, 2);
            
            // Store the conversion details in the order
            $order->update_meta_data('_gyd_to_usd_rate', $exchange_rate);
            $order->update_meta_data('_original_amount_gyd', $amount / $exchange_rate);
            $order->update_meta_data('_converted_amount_usd', $amount);
            $order->save();
            
            // Format as cents for Stripe
            $amount = round($amount * 100);
        }
    }
    
    return $amount;
}
add_filter('wc_stripe_order_amount', 'cardealership_modify_stripe_amount', 10, 2);

/**
 * Set Stripe currency for manual exchange handling
 */
function cardealership_set_stripe_currency($currency, $order) {
    if ($currency === 'GYD') {
        $exchange_handling = get_option('stripe_gyd_exchange_handling', 'automatic');
        
        if ($exchange_handling === 'manual') {
            // If we're manually converting, process in USD
            return 'USD';
        }
    }
    
    return $currency;
}
add_filter('wc_stripe_currency', 'cardealership_set_stripe_currency', 10, 2);

/**
 * Add exchange rate info to order receipt
 */
function cardealership_add_exchange_rate_info($order) {
    if (get_woocommerce_currency() === 'GYD') {
        $exchange_handling = get_option('stripe_gyd_exchange_handling', 'automatic');
        
        if ($exchange_handling === 'manual') {
            $exchange_rate = $order->get_meta('_gyd_to_usd_rate');
            $original_amount = $order->get_meta('_original_amount_gyd');
            $converted_amount = $order->get_meta('_converted_amount_usd');
            
            if ($exchange_rate && $original_amount && $converted_amount) {
                echo '<p class="exchange-rate-info">';
                printf(
                    __('Your order total of %1$s GYD was processed as %2$s USD (exchange rate: %3$s).', 'cardealership-child'),
                    wc_price($original_amount, array('currency' => 'GYD')),
                    wc_price($converted_amount, array('currency' => 'USD')),
                    $exchange_rate
                );
                echo '</p>';
            }
        }
    }
}
add_action('woocommerce_thankyou', 'cardealership_add_exchange_rate_info', 20);
add_action('woocommerce_email_order_details', 'cardealership_add_exchange_rate_info', 20);

/**
 * Update exchange rate automatically
 */
function cardealership_update_exchange_rate() {
    $exchange_frequency = get_option('stripe_gyd_exchange_update_frequency', 'manual');
    
    if ($exchange_frequency === 'manual') {
        return;
    }
    
    $last_update = get_option('stripe_gyd_last_exchange_update');
    $current_time = time();
    
    // Check if update is needed based on frequency
    if ($exchange_frequency === 'daily' && $last_update && $current_time - $last_update < DAY_IN_SECONDS) {
        return;
    }
    
    if ($exchange_frequency === 'weekly' && $last_update && $current_time - $last_update < WEEK_IN_SECONDS) {
        return;
    }
    
    // Fetch latest exchange rate
    // Note: You'll need to use an API or service to get real exchange rates
    // This is a placeholder; implement a real exchange rate API in production
    $api_url = 'https://api.exchangerate-api.com/v4/latest/GYD';
    
    $response = wp_remote_get($api_url);
    
    if (!is_wp_error($response) && 200 === wp_remote_retrieve_response_code($response)) {
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (isset($data['rates']['USD'])) {
            $new_rate = $data['rates']['USD'];
            
            // Update the option
            update_option('stripe_gyd_exchange_rate', $new_rate);
            update_option('stripe_gyd_last_exchange_update', $current_time);
            
            // Log the update
            error_log('GYD to USD exchange rate updated: ' . $new_rate);
        }
    }
}
add_action('wp_loaded', 'cardealership_update_exchange_rate');

/**
 * Additional Stripe checkout fields for Guyana
 */
function cardealership_stripe_checkout_fields($fields) {
    if (get_woocommerce_currency() === 'GYD') {
        // Add phone field (if not already present)
        if (!isset($fields['billing']['billing_phone'])) {
            $fields['billing']['billing_phone'] = array(
                'label'     => __('Phone', 'cardealership-child'),
                'placeholder'   => _x('Phone', 'placeholder', 'cardealership-child'),
                'required'  => true,
                'class'     => array('form-row-wide'),
                'clear'     => true,
                'priority'  => 100,
            );
        } else {
            // Make sure phone is required for Guyanese customers
            $fields['billing']['billing_phone']['required'] = true;
        }
    }
    
    return $fields;
}
add_filter('woocommerce_checkout_fields', 'cardealership_stripe_checkout_fields');

/**
 * Add additional customer metadata to Stripe
 */
function cardealership_add_stripe_customer_data($customer_data, $order) {
    // Add phone number to customer metadata
    if (!empty($order->get_billing_phone())) {
        $customer_data['metadata']['phone'] = $order->get_billing_phone();
    }
    
    // Add National ID number if available
    $national_id = $order->get_meta('_billing_national_id');
    if (!empty($national_id)) {
        $customer_data['metadata']['national_id'] = $national_id;
    }
    
    return $customer_data;
}
add_filter('wc_stripe_customer_data', 'cardealership_add_stripe_customer_data', 10, 2);

/**
 * Customize Stripe checkout message for Guyanese customers
 */
function cardealership_customize_stripe_description($description, $id) {
    if (get_woocommerce_currency() === 'GYD') {
        $exchange_handling = get_option('stripe_gyd_exchange_handling', 'automatic');
        
        if ($exchange_handling === 'manual') {
            $description .= ' ' . __('Your payment will be processed in USD at the current exchange rate.', 'cardealership-child');
        }
    }
    
    return $description;
}
add_filter('woocommerce_gateway_description', 'cardealership_customize_stripe_description', 10, 2);

/**
 * Add a notice about currency conversion at checkout
 */
function cardealership_add_currency_conversion_notice() {
    if (!is_checkout()) {
        return;
    }
    
    if (get_woocommerce_currency() === 'GYD') {
        $exchange_handling = get_option('stripe_gyd_exchange_handling', 'automatic');
        
        if ($exchange_handling === 'manual') {
            $exchange_rate = get_option('stripe_gyd_exchange_rate', 0.0048);
            
            wc_add_notice(
                sprintf(
                    __('Your credit/debit card will be charged in USD using an exchange rate of 1 GYD = %s USD.', 'cardealership-child'),
                    $exchange_rate
                ),
                'notice'
            );
        }
    }
}
add_action('woocommerce_before_checkout_form', 'cardealership_add_currency_conversion_notice', 10);

/**
 * Validate postcode for Guyana
 */
function cardealership_validate_guyana_postcode($valid, $postcode, $country) {
    if ($country === 'GY') {
        // Guyana doesn't use postcodes, so always valid
        return true;
    }
    
    return $valid;
}
add_filter('woocommerce_validate_postcode', 'cardealership_validate_guyana_postcode', 10, 3);

/**
 * Handle Stripe specific errors for Guyana
 */
function cardealership_handle_stripe_errors($error_message, $error) {
    if (get_woocommerce_currency() === 'GYD') {
        // Check for common Guyanese card errors
        if (strpos($error->getMessage(), 'card_declined') !== false) {
            $error_message = __('Your card was declined. Please try another card or contact your bank to authorize international transactions.', 'cardealership-child');
        } 
        
        // Check for currency conversion issues
        if (strpos($error->getMessage(), 'invalid_currency') !== false) {
            $error_message = __('There was an issue with currency conversion. Please contact us for assistance.', 'cardealership-child');
        }
    }
    
    return $error_message;
}
add_filter('wc_stripe_error_message', 'cardealership_handle_stripe_errors', 10, 2);

/**
 * Add Stripe custom receipt settings
 */
function cardealership_stripe_receipt_settings($settings) {
    $additional_settings = array(
        array(
            'title' => __('Receipt SMS Settings', 'cardealership-child'),
            'type' => 'title',
            'desc' => __('Customize receipt SMS settings for Guyanese customers.', 'cardealership-child'),
            'id' => 'stripe_receipt_sms_settings',
        ),
        array(
            'title' => __('Enable Receipt SMS', 'cardealership-child'),
            'desc' => __('Send payment receipts via SMS', 'cardealership-child'),
            'id' => 'stripe_receipt_sms_enabled',
            'default' => 'no',
            'type' => 'checkbox',
        ),
        array(
            'title' => __('Receipt SMS Message', 'cardealership-child'),
            'desc' => __('Customize the SMS receipt message. Use {amount}, {date}, and {order_id} as placeholders.', 'cardealership-child'),
            'id' => 'stripe_receipt_sms_message',
            'default' => __('Thank you for your payment of {amount} on {date}. Your order #{order_id} has been confirmed.', 'cardealership-child'),
            'type' => 'textarea',
            'desc_tip' => true,
        ),
        array(
            'type' => 'sectionend',
            'id' => 'stripe_receipt_sms_settings',
        )
    );
    
    return array_merge($settings, $additional_settings);
}
add_filter('woocommerce_get_settings_checkout', 'cardealership_stripe_receipt_settings', 11, 1);

/**
 * Send custom SMS receipt on successful payment
 */
function cardealership_send_stripe_sms_receipt($order_id) {
    $order = wc_get_order($order_id);
    
    if (!$order) {
        return;
    }
    
    // Check if order was paid with Stripe
    if ($order->get_payment_method() !== 'stripe') {
        return;
    }
    
    // Check if SMS receipts are enabled
    $sms_enabled = get_option('stripe_receipt_sms_enabled') === 'yes';
    
    if (!$sms_enabled) {
        return;
    }
    
    $phone = $order->get_billing_phone();
    
    if (empty($phone)) {
        return;
    }
    
    $message_template = get_option('stripe_receipt_sms_message', __('Thank you for your payment of {amount} on {date}. Your order #{order_id} has been confirmed.', 'cardealership-child'));
    
    $replacements = array(
        '{amount}' => strip_tags(wc_price($order->get_total(), array('currency' => $order->get_currency()))),
        '{date}' => date_i18n(get_option('date_format'), time()),
        '{order_id}' => $order->get_order_number(),
    );
    
    $message = str_replace(array_keys($replacements), array_values($replacements), $message_template);
    
    // Implement SMS sending functionality here
    // This is a placeholder; you need to implement actual SMS sending with your preferred provider
    
    // Example using a fictional SMS API
    /*
    $response = wp_remote_post('https://your-sms-provider.com/api/send', array(
        'body' => array(
            'api_key' => get_option('sms_api_key'),
            'to' => $phone,
            'message' => $message,
        ),
    ));
    
    if (is_wp_error($response)) {
        error_log('Failed to send SMS receipt: ' . $response->get_error_message());
    } else {
        $order->add_order_note(__('SMS receipt sent to customer.', 'cardealership-child'));
    }
    */
}
add_action('woocommerce_payment_complete', 'cardealership_send_stripe_sms_receipt', 10, 1);

/**
 * Add Stripe Elements custom styling
 */
function cardealership_stripe_elements_custom_styling($styles) {
    $custom_styles = array(
        'base' => array(
            'color' => '#333333',
            'fontFamily' => '"Open Sans", sans-serif',
            'fontSize' => '16px',
            'lineHeight' => '24px',
            '::placeholder' => array(
                'color' => '#999999',
            ),
        ),
        'invalid' => array(
            'color' => '#dc3232',
        ),
    );
    
    return array_merge_recursive($styles, $custom_styles);
}
add_filter('wc_stripe_elements_styling', 'cardealership_stripe_elements_custom_styling');

/**
 * Add warranty option to checkout when using Stripe
 */
function cardealership_add_warranty_checkout_field($fields) {
    // Only show for vehicle products
    $has_vehicle = false;
    
    if (WC()->cart) {
        foreach (WC()->cart->get_cart() as $cart_item) {
            $product_id = $cart_item['product_id'];
            $linked_vehicle_id = get_post_meta($product_id, '_linked_vehicle_id', true);
            
            if ($linked_vehicle_id) {
                $has_vehicle = true;
                break;
            }
        }
    }
    
    if ($has_vehicle) {
        $fields['order']['warranty_option'] = array(
            'type' => 'select',
            'label' => __('Extended Warranty', 'cardealership-child'),
            'options' => array(
                'none' => __('No Warranty', 'cardealership-child'),
                'basic' => __('Basic - 1 Year (GYD 50,000)', 'cardealership-child'),
                'standard' => __('Standard - 2 Years (GYD 100,000)', 'cardealership-child'),
                'premium' => __('Premium - 3 Years (GYD 150,000)', 'cardealership-child'),
            ),
            'default' => 'none',
            'required' => false,
            'class' => array('form-row-wide'),
            'priority' => 100,
        );
    }
    
    return $fields;
}
add_filter('woocommerce_checkout_fields', 'cardealership_add_warranty_checkout_field');

/**
 * Add warranty fee to order when selected
 */
function cardealership_add_warranty_fee() {
    if (isset($_POST['warranty_option']) && $_POST['warranty_option'] !== 'none') {
        $warranty_prices = array(
            'basic' => 50000,
            'standard' => 100000,
            'premium' => 150000,
        );
        
        $warranty_option = sanitize_text_field($_POST['warranty_option']);
        
        if (isset($warranty_prices[$warranty_option])) {
            $warranty_fee = $warranty_prices[$warranty_option];
            $warranty_label = sprintf(__('Extended Warranty - %s', 'cardealership-child'), ucfirst($warranty_option));
            
            WC()->cart->add_fee($warranty_label, $warranty_fee);
        }
    }
}
add_action('woocommerce_cart_calculate_fees', 'cardealership_add_warranty_fee');

/**
 * Save warranty option to order meta
 */
function cardealership_save_warranty_option($order_id) {
    if (isset($_POST['warranty_option'])) {
        update_post_meta($order_id, '_warranty_option', sanitize_text_field($_POST['warranty_option']));
    }
}
add_action('woocommerce_checkout_update_order_meta', 'cardealership_save_warranty_option');

/**
 * Display warranty details in order emails and admin
 */
function cardealership_display_warranty_details($order) {
    $warranty_option = get_post_meta($order->get_id(), '_warranty_option', true);
    
    if ($warranty_option && $warranty_option !== 'none') {
        $warranty_labels = array(
            'basic' => __('Basic - 1 Year', 'cardealership-child'),
            'standard' => __('Standard - 2 Years', 'cardealership-child'),
            'premium' => __('Premium - 3 Years', 'cardealership-child'),
        );
        
        $warranty_label = isset($warranty_labels[$warranty_option]) ? $warranty_labels[$warranty_option] : ucfirst($warranty_option);
        
        echo '<p><strong>' . __('Extended Warranty:', 'cardealership-child') . '</strong> ' . esc_html($warranty_label) . '</p>';
    }
}
add_action('woocommerce_admin_order_data_after_billing_address', 'cardealership_display_warranty_details');
add_action('woocommerce_email_customer_details', 'cardealership_display_warranty_details', 20);
add_action('woocommerce_order_details_after_order_table', 'cardealership_display_warranty_details', 10);

/**
 * Add payment plan selection for vehicle purchases
 */
function cardealership_add_payment_plan_field($fields) {
    // Only show for vehicle products with financing available
    $has_financeable_vehicle = false;
    
    if (WC()->cart) {
        foreach (WC()->cart->get_cart() as $cart_item) {
            $product_id = $cart_item['product_id'];
            $linked_vehicle_id = get_post_meta($product_id, '_linked_vehicle_id', true);
            
            if ($linked_vehicle_id) {
                $financing_available = get_post_meta($linked_vehicle_id, '_vehicle_financing_available', true);
                
                if ($financing_available) {
                    $has_financeable_vehicle = true;
                    break;
                }
            }
        }
    }
    
    if ($has_financeable_vehicle) {
        $fields['order']['payment_plan'] = array(
            'type' => 'select',
            'label' => __('Payment Plan', 'cardealership-child'),
            'options' => array(
                'full' => __('Full Payment', 'cardealership-child'),
                'financing' => __('Financing (Pay Deposit Only)', 'cardealership-child'),
            ),
            'default' => 'full',
            'required' => true,
            'class' => array('form-row-wide'),
            'priority' => 90,
        );
        
        $fields['order']['deposit_amount'] = array(
            'type' => 'number',
            'label' => __('Deposit Amount (GYD)', 'cardealership-child'),
            'placeholder' => __('Enter deposit amount', 'cardealership-child'),
            'required' => false,
            'class' => array('form-row-wide', 'deposit-field'),
            'priority' => 91,
            'custom_attributes' => array(
                'min' => '0',
                'step' => '1000',
            ),
        );
    }
    
    return $fields;
}
add_filter('woocommerce_checkout_fields', 'cardealership_add_payment_plan_field');

/**
 * Add JavaScript to toggle deposit field visibility
 */
function cardealership_payment_plan_script() {
    if (!is_checkout()) {
        return;
    }
    ?>
    <script type="text/javascript">
    jQuery(function($) {
        function toggleDepositField() {
            var plan = $('#payment_plan').val();
            if (plan === 'financing') {
                $('.deposit-field').show();
                $('#deposit_amount').prop('required', true);
            } else {
                $('.deposit-field').hide();
                $('#deposit_amount').prop('required', false);
            }
        }
        
        // Initially hide the deposit field
        $('.deposit-field').hide();
        
        // Toggle on change
        $('#payment_plan').change(toggleDepositField);
        
        // Check initial state
        toggleDepositField();
    });
    </script>
    <?php
}
add_action('wp_footer', 'cardealership_payment_plan_script');

/**
 * Adjust cart total for financing option
 */
function cardealership_adjust_cart_for_financing() {
    if (!is_checkout()) {
        return;
    }
    
    // Check if payment plan is set to financing
    if (isset($_POST['payment_plan']) && $_POST['payment_plan'] === 'financing' && isset($_POST['deposit_amount']) && is_numeric($_POST['deposit_amount'])) {
        $deposit_amount = floatval($_POST['deposit_amount']);
        
        // Loop through cart items to find vehicle
        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            $product_id = $cart_item['product_id'];
            $linked_vehicle_id = get_post_meta($product_id, '_linked_vehicle_id', true);
            
            if ($linked_vehicle_id) {
                $financing_available = get_post_meta($linked_vehicle_id, '_vehicle_financing_available', true);
                
                if ($financing_available) {
                    // Store original price in session
                    $product = wc_get_product($product_id);
                    $original_price = $product->get_price();
                    
                    WC()->session->set('original_vehicle_price_' . $product_id, $original_price);
                    
                    // Update the cart item price to the deposit amount
                    WC()->cart->cart_contents[$cart_item_key]['data']->set_price($deposit_amount);
                    
                    // Add a note to the order
                    WC()->session->set('financing_deposit_only', true);
                    WC()->session->set('financing_deposit_amount', $deposit_amount);
                    WC()->session->set('financing_full_amount', $original_price);
                    
                    break;
                }
            }
        }
    }
}
add_action('woocommerce_before_calculate_totals', 'cardealership_adjust_cart_for_financing', 20);

/**
 * Save financing details to order
 */
function cardealership_save_financing_details($order_id) {
    if (WC()->session->get('financing_deposit_only')) {
        update_post_meta($order_id, '_financing_order', 'yes');
        update_post_meta($order_id, '_deposit_amount', WC()->session->get('financing_deposit_amount'));
        update_post_meta($order_id, '_full_vehicle_amount', WC()->session->get('financing_full_amount'));
        
        // Add order note
        $order = wc_get_order($order_id);
        $deposit = wc_price(WC()->session->get('financing_deposit_amount'), array('currency' => $order->get_currency()));
        $full_amount = wc_price(WC()->session->get('financing_full_amount'), array('currency' => $order->get_currency()));
        
        $order->add_order_note(sprintf(
            __('Financing order: Customer paid deposit of %1$s. Full vehicle price: %2$s.', 'cardealership-child'),
            $deposit,
            $full_amount
        ));
        
        // Clear session data
        WC()->session->__unset('financing_deposit_only');
        WC()->session->__unset('financing_deposit_amount');
        WC()->session->__unset('financing_full_amount');
    }
    
    if (isset($_POST['payment_plan'])) {
        update_post_meta($order_id, '_payment_plan', sanitize_text_field($_POST['payment_plan']));
    }
    
    if (isset($_POST['deposit_amount'])) {
        update_post_meta($order_id, '_deposit_amount', sanitize_text_field($_POST['deposit_amount']));
    }
}
add_action('woocommerce_checkout_update_order_meta', 'cardealership_save_financing_details');

/**
 * Display financing details in order emails and admin
 */
function cardealership_display_financing_details($order) {
    $payment_plan = get_post_meta($order->get_id(), '_payment_plan', true);
    
    if ($payment_plan === 'financing') {
        $deposit_amount = get_post_meta($order->get_id(), '_deposit_amount', true);
        $full_amount = get_post_meta($order->get_id(), '_full_vehicle_amount', true);
        
        echo '<h3>' . __('Financing Details', 'cardealership-child') . '</h3>';
        echo '<p><strong>' . __('Payment Plan:', 'cardealership-child') . '</strong> ' . __('Financing', 'cardealership-child') . '</p>';
        echo '<p><strong>' . __('Deposit Paid:', 'cardealership-child') . '</strong> ' . wc_price($deposit_amount, array('currency' => $order->get_currency())) . '</p>';
        
        if ($full_amount) {
            $remaining = $full_amount - $deposit_amount;
            echo '<p><strong>' . __('Full Vehicle Price:', 'cardealership-child') . '</strong> ' . wc_price($full_amount, array('currency' => $order->get_currency())) . '</p>';
            echo '<p><strong>' . __('Remaining Balance:', 'cardealership-child') . '</strong> ' . wc_price($remaining, array('currency' => $order->get_currency())) . '</p>';
        }
        
        echo '<p>' . __('Our financing team will contact you to complete the financing process.', 'cardealership-child') . '</p>';
    }
}
add_action('woocommerce_admin_order_data_after_billing_address', 'cardealership_display_financing_details');
add_action('woocommerce_email_customer_details', 'cardealership_display_financing_details', 25);
add_action('woocommerce_order_details_after_order_table', 'cardealership_display_financing_details', 15);
