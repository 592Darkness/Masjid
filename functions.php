<?php
/**
 * Car Dealership Child Theme Functions
 * Place this file in your child theme folder
 */

// Enqueue parent and child theme stylesheets
function cardealership_enqueue_styles() {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/style.css', array('parent-style'));
    
    // Enqueue custom JS
    wp_enqueue_script('financing-calculator', get_stylesheet_directory_uri() . '/js/financing-calculator.js', array('jquery'), '1.0.0', true);
    
    // Localize script for currency format
    wp_localize_script('financing-calculator', 'financing_vars', array(
        'currency_symbol' => 'GYD $',
        'currency_position' => 'left',
        'thousand_separator' => ',',
        'decimal_separator' => '.',
        'decimals' => 2
    ));
}
add_action('wp_enqueue_scripts', 'cardealership_enqueue_styles');

/**
 * Register Custom Post Type for Vehicles
 */
function cardealership_register_vehicle_cpt() {
    $labels = array(
        'name'               => _x('Vehicles', 'post type general name', 'cardealership-child'),
        'singular_name'      => _x('Vehicle', 'post type singular name', 'cardealership-child'),
        'menu_name'          => _x('Vehicles', 'admin menu', 'cardealership-child'),
        'name_admin_bar'     => _x('Vehicle', 'add new on admin bar', 'cardealership-child'),
        'add_new'            => _x('Add New', 'vehicle', 'cardealership-child'),
        'add_new_item'       => __('Add New Vehicle', 'cardealership-child'),
        'new_item'           => __('New Vehicle', 'cardealership-child'),
        'edit_item'          => __('Edit Vehicle', 'cardealership-child'),
        'view_item'          => __('View Vehicle', 'cardealership-child'),
        'all_items'          => __('All Vehicles', 'cardealership-child'),
        'search_items'       => __('Search Vehicles', 'cardealership-child'),
        'parent_item_colon'  => __('Parent Vehicles:', 'cardealership-child'),
        'not_found'          => __('No vehicles found.', 'cardealership-child'),
        'not_found_in_trash' => __('No vehicles found in Trash.', 'cardealership-child')
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __('Vehicle listings for car dealership', 'cardealership-child'),
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'vehicles'),
        'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-car',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
        'show_in_rest'       => true
    );

    register_post_type('vehicle', $args);
}
add_action('init', 'cardealership_register_vehicle_cpt');

/**
 * Register Vehicle Taxonomies
 */
function cardealership_register_vehicle_taxonomies() {
    // Vehicle Make Taxonomy
    $make_labels = array(
        'name'              => _x('Makes', 'taxonomy general name', 'cardealership-child'),
        'singular_name'     => _x('Make', 'taxonomy singular name', 'cardealership-child'),
        'search_items'      => __('Search Makes', 'cardealership-child'),
        'all_items'         => __('All Makes', 'cardealership-child'),
        'parent_item'       => __('Parent Make', 'cardealership-child'),
        'parent_item_colon' => __('Parent Make:', 'cardealership-child'),
        'edit_item'         => __('Edit Make', 'cardealership-child'),
        'update_item'       => __('Update Make', 'cardealership-child'),
        'add_new_item'      => __('Add New Make', 'cardealership-child'),
        'new_item_name'     => __('New Make Name', 'cardealership-child'),
        'menu_name'         => __('Makes', 'cardealership-child'),
    );

    $make_args = array(
        'hierarchical'      => true,
        'labels'            => $make_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'vehicle-make'),
        'show_in_rest'      => true,
    );

    register_taxonomy('vehicle_make', array('vehicle'), $make_args);

    // Vehicle Model Taxonomy
    $model_labels = array(
        'name'              => _x('Models', 'taxonomy general name', 'cardealership-child'),
        'singular_name'     => _x('Model', 'taxonomy singular name', 'cardealership-child'),
        'search_items'      => __('Search Models', 'cardealership-child'),
        'all_items'         => __('All Models', 'cardealership-child'),
        'parent_item'       => __('Parent Model', 'cardealership-child'),
        'parent_item_colon' => __('Parent Model:', 'cardealership-child'),
        'edit_item'         => __('Edit Model', 'cardealership-child'),
        'update_item'       => __('Update Model', 'cardealership-child'),
        'add_new_item'      => __('Add New Model', 'cardealership-child'),
        'new_item_name'     => __('New Model Name', 'cardealership-child'),
        'menu_name'         => __('Models', 'cardealership-child'),
    );

    $model_args = array(
        'hierarchical'      => true,
        'labels'            => $model_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'vehicle-model'),
        'show_in_rest'      => true,
    );

    register_taxonomy('vehicle_model', array('vehicle'), $model_args);
    
    // Vehicle Type Taxonomy (New, Used, Certified)
    $type_labels = array(
        'name'              => _x('Vehicle Types', 'taxonomy general name', 'cardealership-child'),
        'singular_name'     => _x('Vehicle Type', 'taxonomy singular name', 'cardealership-child'),
        'search_items'      => __('Search Vehicle Types', 'cardealership-child'),
        'all_items'         => __('All Vehicle Types', 'cardealership-child'),
        'parent_item'       => __('Parent Vehicle Type', 'cardealership-child'),
        'parent_item_colon' => __('Parent Vehicle Type:', 'cardealership-child'),
        'edit_item'         => __('Edit Vehicle Type', 'cardealership-child'),
        'update_item'       => __('Update Vehicle Type', 'cardealership-child'),
        'add_new_item'      => __('Add New Vehicle Type', 'cardealership-child'),
        'new_item_name'     => __('New Vehicle Type Name', 'cardealership-child'),
        'menu_name'         => __('Vehicle Types', 'cardealership-child'),
    );

    $type_args = array(
        'hierarchical'      => true,
        'labels'            => $type_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'vehicle-type'),
        'show_in_rest'      => true,
    );

    register_taxonomy('vehicle_type', array('vehicle'), $type_args);
}
add_action('init', 'cardealership_register_vehicle_taxonomies');

/**
 * Register Custom Meta Boxes for Vehicles
 */
function cardealership_register_vehicle_meta_boxes() {
    add_meta_box(
        'vehicle_details',
        __('Vehicle Details', 'cardealership-child'),
        'cardealership_vehicle_details_callback',
        'vehicle',
        'normal',
        'high'
    );
    
    add_meta_box(
        'vehicle_pricing',
        __('Pricing & Financing', 'cardealership-child'),
        'cardealership_vehicle_pricing_callback',
        'vehicle',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'cardealership_register_vehicle_meta_boxes');

/**
 * Vehicle Details Meta Box Callback
 */
function cardealership_vehicle_details_callback($post) {
    wp_nonce_field('cardealership_save_vehicle_details', 'cardealership_vehicle_details_nonce');
    
    $year = get_post_meta($post->ID, '_vehicle_year', true);
    $mileage = get_post_meta($post->ID, '_vehicle_mileage', true);
    $engine = get_post_meta($post->ID, '_vehicle_engine', true);
    $transmission = get_post_meta($post->ID, '_vehicle_transmission', true);
    $color = get_post_meta($post->ID, '_vehicle_color', true);
    $vin = get_post_meta($post->ID, '_vehicle_vin', true);
    $features = get_post_meta($post->ID, '_vehicle_features', true);
    
    ?>
    <div class="vehicle-meta-box">
        <p>
            <label for="vehicle_year"><?php _e('Year:', 'cardealership-child'); ?></label>
            <input type="number" id="vehicle_year" name="vehicle_year" value="<?php echo esc_attr($year); ?>" min="1900" max="<?php echo date('Y') + 1; ?>" />
        </p>
        <p>
            <label for="vehicle_mileage"><?php _e('Mileage (km):', 'cardealership-child'); ?></label>
            <input type="number" id="vehicle_mileage" name="vehicle_mileage" value="<?php echo esc_attr($mileage); ?>" min="0" />
        </p>
        <p>
            <label for="vehicle_engine"><?php _e('Engine:', 'cardealership-child'); ?></label>
            <input type="text" id="vehicle_engine" name="vehicle_engine" value="<?php echo esc_attr($engine); ?>" />
        </p>
        <p>
            <label for="vehicle_transmission"><?php _e('Transmission:', 'cardealership-child'); ?></label>
            <select id="vehicle_transmission" name="vehicle_transmission">
                <option value=""><?php _e('Select Transmission', 'cardealership-child'); ?></option>
                <option value="automatic" <?php selected($transmission, 'automatic'); ?>><?php _e('Automatic', 'cardealership-child'); ?></option>
                <option value="manual" <?php selected($transmission, 'manual'); ?>><?php _e('Manual', 'cardealership-child'); ?></option>
                <option value="cvt" <?php selected($transmission, 'cvt'); ?>><?php _e('CVT', 'cardealership-child'); ?></option>
            </select>
        </p>
        <p>
            <label for="vehicle_color"><?php _e('Color:', 'cardealership-child'); ?></label>
            <input type="text" id="vehicle_color" name="vehicle_color" value="<?php echo esc_attr($color); ?>" />
        </p>
        <p>
            <label for="vehicle_vin"><?php _e('VIN:', 'cardealership-child'); ?></label>
            <input type="text" id="vehicle_vin" name="vehicle_vin" value="<?php echo esc_attr($vin); ?>" />
        </p>
        <p>
            <label for="vehicle_features"><?php _e('Features (one per line):', 'cardealership-child'); ?></label>
            <textarea id="vehicle_features" name="vehicle_features" rows="6"><?php echo esc_textarea($features); ?></textarea>
        </p>
    </div>
    <?php
}

/**
 * Vehicle Pricing Meta Box Callback
 */
function cardealership_vehicle_pricing_callback($post) {
    wp_nonce_field('cardealership_save_vehicle_pricing', 'cardealership_vehicle_pricing_nonce');
    
    $price = get_post_meta($post->ID, '_vehicle_price', true);
    $msrp = get_post_meta($post->ID, '_vehicle_msrp', true);
    $financing_available = get_post_meta($post->ID, '_vehicle_financing_available', true);
    $down_payment = get_post_meta($post->ID, '_vehicle_down_payment', true);
    $monthly_payment = get_post_meta($post->ID, '_vehicle_monthly_payment', true);
    $financing_term = get_post_meta($post->ID, '_vehicle_financing_term', true);
    $interest_rate = get_post_meta($post->ID, '_vehicle_interest_rate', true);
    
    ?>
    <div class="vehicle-meta-box">
        <p>
            <label for="vehicle_price"><?php _e('Price (GYD):', 'cardealership-child'); ?></label>
            <input type="number" id="vehicle_price" name="vehicle_price" value="<?php echo esc_attr($price); ?>" min="0" step="1000" />
        </p>
        <p>
            <label for="vehicle_msrp"><?php _e('MSRP (GYD):', 'cardealership-child'); ?></label>
            <input type="number" id="vehicle_msrp" name="vehicle_msrp" value="<?php echo esc_attr($msrp); ?>" min="0" step="1000" />
        </p>
        <p>
            <label for="vehicle_financing_available"><?php _e('Financing Available:', 'cardealership-child'); ?></label>
            <input type="checkbox" id="vehicle_financing_available" name="vehicle_financing_available" value="1" <?php checked($financing_available, '1'); ?> />
        </p>
        
        <div class="financing-options" <?php echo empty($financing_available) ? 'style="display: none;"' : ''; ?>>
            <h4><?php _e('Financing Details', 'cardealership-child'); ?></h4>
            <p>
                <label for="vehicle_down_payment"><?php _e('Minimum Down Payment (GYD):', 'cardealership-child'); ?></label>
                <input type="number" id="vehicle_down_payment" name="vehicle_down_payment" value="<?php echo esc_attr($down_payment); ?>" min="0" step="1000" />
            </p>
            <p>
                <label for="vehicle_monthly_payment"><?php _e('Estimated Monthly Payment (GYD):', 'cardealership-child'); ?></label>
                <input type="number" id="vehicle_monthly_payment" name="vehicle_monthly_payment" value="<?php echo esc_attr($monthly_payment); ?>" min="0" step="100" />
            </p>
            <p>
                <label for="vehicle_financing_term"><?php _e('Financing Term (months):', 'cardealership-child'); ?></label>
                <select id="vehicle_financing_term" name="vehicle_financing_term">
                    <option value=""><?php _e('Select Term', 'cardealership-child'); ?></option>
                    <option value="12" <?php selected($financing_term, '12'); ?>>12 <?php _e('months', 'cardealership-child'); ?></option>
                    <option value="24" <?php selected($financing_term, '24'); ?>>24 <?php _e('months', 'cardealership-child'); ?></option>
                    <option value="36" <?php selected($financing_term, '36'); ?>>36 <?php _e('months', 'cardealership-child'); ?></option>
                    <option value="48" <?php selected($financing_term, '48'); ?>>48 <?php _e('months', 'cardealership-child'); ?></option>
                    <option value="60" <?php selected($financing_term, '60'); ?>>60 <?php _e('months', 'cardealership-child'); ?></option>
                </select>
            </p>
            <p>
                <label for="vehicle_interest_rate"><?php _e('Interest Rate (%):', 'cardealership-child'); ?></label>
                <input type="number" id="vehicle_interest_rate" name="vehicle_interest_rate" value="<?php echo esc_attr($interest_rate); ?>" min="0" max="100" step="0.1" />
            </p>
        </div>
    </div>
    
    <script>
        jQuery(document).ready(function($) {
            $('#vehicle_financing_available').change(function() {
                if($(this).is(':checked')) {
                    $('.financing-options').show();
                } else {
                    $('.financing-options').hide();
                }
            });
        });
    </script>
    <?php
}

/**
 * Save Vehicle Meta Box Data
 */
function cardealership_save_vehicle_meta($post_id) {
    // Check if nonce is set
    if (!isset($_POST['cardealership_vehicle_details_nonce']) || !isset($_POST['cardealership_vehicle_pricing_nonce'])) {
        return;
    }

    // Verify nonces
    if (!wp_verify_nonce($_POST['cardealership_vehicle_details_nonce'], 'cardealership_save_vehicle_details') || 
        !wp_verify_nonce($_POST['cardealership_vehicle_pricing_nonce'], 'cardealership_save_vehicle_pricing')) {
        return;
    }

    // If this is an autosave, don't do anything
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check the user's permissions
    if (isset($_POST['post_type']) && 'vehicle' == $_POST['post_type']) {
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
    }

    // Save vehicle details
    if (isset($_POST['vehicle_year'])) {
        update_post_meta($post_id, '_vehicle_year', sanitize_text_field($_POST['vehicle_year']));
    }
    
    if (isset($_POST['vehicle_mileage'])) {
        update_post_meta($post_id, '_vehicle_mileage', sanitize_text_field($_POST['vehicle_mileage']));
    }
    
    if (isset($_POST['vehicle_engine'])) {
        update_post_meta($post_id, '_vehicle_engine', sanitize_text_field($_POST['vehicle_engine']));
    }
    
    if (isset($_POST['vehicle_transmission'])) {
        update_post_meta($post_id, '_vehicle_transmission', sanitize_text_field($_POST['vehicle_transmission']));
    }
    
    if (isset($_POST['vehicle_color'])) {
        update_post_meta($post_id, '_vehicle_color', sanitize_text_field($_POST['vehicle_color']));
    }
    
    if (isset($_POST['vehicle_vin'])) {
        update_post_meta($post_id, '_vehicle_vin', sanitize_text_field($_POST['vehicle_vin']));
    }
    
    if (isset($_POST['vehicle_features'])) {
        update_post_meta($post_id, '_vehicle_features', sanitize_textarea_field($_POST['vehicle_features']));
    }
    
    // Save vehicle pricing
    if (isset($_POST['vehicle_price'])) {
        update_post_meta($post_id, '_vehicle_price', sanitize_text_field($_POST['vehicle_price']));
    }
    
    if (isset($_POST['vehicle_msrp'])) {
        update_post_meta($post_id, '_vehicle_msrp', sanitize_text_field($_POST['vehicle_msrp']));
    }
    
    $financing_available = isset($_POST['vehicle_financing_available']) ? '1' : '0';
    update_post_meta($post_id, '_vehicle_financing_available', $financing_available);
    
    if (isset($_POST['vehicle_down_payment'])) {
        update_post_meta($post_id, '_vehicle_down_payment', sanitize_text_field($_POST['vehicle_down_payment']));
    }
    
    if (isset($_POST['vehicle_monthly_payment'])) {
        update_post_meta($post_id, '_vehicle_monthly_payment', sanitize_text_field($_POST['vehicle_monthly_payment']));
    }
    
    if (isset($_POST['vehicle_financing_term'])) {
        update_post_meta($post_id, '_vehicle_financing_term', sanitize_text_field($_POST['vehicle_financing_term']));
    }
    
    if (isset($_POST['vehicle_interest_rate'])) {
        update_post_meta($post_id, '_vehicle_interest_rate', sanitize_text_field($_POST['vehicle_interest_rate']));
    }
}
add_action('save_post', 'cardealership_save_vehicle_meta');

/**
 * Add Vehicle Data to WooCommerce
 */
function cardealership_vehicle_to_woocommerce($post_id) {
    // Check if this is a vehicle post
    if ('vehicle' !== get_post_type($post_id)) {
        return;
    }
    
    // Get vehicle data
    $title = get_the_title($post_id);
    $description = get_the_content(null, false, $post_id);
    $price = get_post_meta($post_id, '_vehicle_price', true);
    $year = get_post_meta($post_id, '_vehicle_year', true);
    $make_terms = wp_get_post_terms($post_id, 'vehicle_make');
    $model_terms = wp_get_post_terms($post_id, 'vehicle_model');
    
    $make = !empty($make_terms) ? $make_terms[0]->name : '';
    $model = !empty($model_terms) ? $model_terms[0]->name : '';
    
    // Create a product if one doesn't exist for this vehicle
    $linked_product_id = get_post_meta($post_id, '_linked_product_id', true);
    
    if (!$linked_product_id) {
        // Create new product
        $product = array(
            'post_title'    => $title,
            'post_content'  => $description,
            'post_status'   => 'publish',
            'post_type'     => 'product',
            'post_author'   => get_current_user_id(),
        );
        
        $product_id = wp_insert_post($product);
        
        if (!is_wp_error($product_id)) {
            // Set product data
            wp_set_object_terms($product_id, 'simple', 'product_type');
            update_post_meta($product_id, '_regular_price', $price);
            update_post_meta($product_id, '_price', $price);
            update_post_meta($product_id, '_sold_individually', 'yes');
            update_post_meta($product_id, '_stock_status', 'instock');
            update_post_meta($product_id, '_virtual', 'yes');
            
            // Set the featured image if exists
            if (has_post_thumbnail($post_id)) {
                $thumbnail_id = get_post_thumbnail_id($post_id);
                set_post_thumbnail($product_id, $thumbnail_id);
            }
            
            // Link the product to the vehicle
            update_post_meta($post_id, '_linked_product_id', $product_id);
            update_post_meta($product_id, '_linked_vehicle_id', $post_id);
            
            // Add custom attributes
            $attributes = array();
            
            if ($year) {
                $attributes['year'] = array(
                    'name' => 'Year',
                    'value' => $year,
                    'position' => 0,
                    'is_visible' => 1,
                    'is_variation' => 0,
                    'is_taxonomy' => 0
                );
            }
            
            if ($make) {
                $attributes['make'] = array(
                    'name' => 'Make',
                    'value' => $make,
                    'position' => 1,
                    'is_visible' => 1,
                    'is_variation' => 0,
                    'is_taxonomy' => 0
                );
            }
            
            if ($model) {
                $attributes['model'] = array(
                    'name' => 'Model',
                    'value' => $model,
                    'position' => 2,
                    'is_visible' => 1,
                    'is_variation' => 0,
                    'is_taxonomy' => 0
                );
            }
            
            update_post_meta($product_id, '_product_attributes', $attributes);
        }
    } else {
        // Update existing product
        $product = array(
            'ID'            => $linked_product_id,
            'post_title'    => $title,
            'post_content'  => $description,
        );
        
        wp_update_post($product);
        
        // Update product data
        update_post_meta($linked_product_id, '_regular_price', $price);
        update_post_meta($linked_product_id, '_price', $price);
    }
}
add_action('save_post', 'cardealership_vehicle_to_woocommerce', 20);

/**
 * Add Vehicle Custom Fields to Product Pages
 */
function cardealership_display_vehicle_fields() {
    global $product;
    
    if (!$product) {
        return;
    }
    
    // Get linked vehicle
    $vehicle_id = get_post_meta($product->get_id(), '_linked_vehicle_id', true);
    
    if (!$vehicle_id) {
        return;
    }
    
    // Get vehicle details
    $year = get_post_meta($vehicle_id, '_vehicle_year', true);
    $mileage = get_post_meta($vehicle_id, '_vehicle_mileage', true);
    $engine = get_post_meta($vehicle_id, '_vehicle_engine', true);
    $transmission = get_post_meta($vehicle_id, '_vehicle_transmission', true);
    $color = get_post_meta($vehicle_id, '_vehicle_color', true);
    $vin = get_post_meta($vehicle_id, '_vehicle_vin', true);
    $features = get_post_meta($vehicle_id, '_vehicle_features', true);
    $financing_available = get_post_meta($vehicle_id, '_vehicle_financing_available', true);
    
    // Display vehicle details
    echo '<div class="vehicle-details">';
    echo '<h3>' . __('Vehicle Details', 'cardealership-child') . '</h3>';
    echo '<div class="vehicle-specs">';
    
    if ($year) {
        echo '<p><strong>' . __('Year:', 'cardealership-child') . '</strong> ' . esc_html($year) . '</p>';
    }
    
    if ($mileage) {
        echo '<p><strong>' . __('Mileage:', 'cardealership-child') . '</strong> ' . esc_html(number_format($mileage)) . ' km</p>';
    }
    
    if ($engine) {
        echo '<p><strong>' . __('Engine:', 'cardealership-child') . '</strong> ' . esc_html($engine) . '</p>';
    }
    
    if ($transmission) {
        echo '<p><strong>' . __('Transmission:', 'cardealership-child') . '</strong> ' . esc_html(ucfirst($transmission)) . '</p>';
    }
    
    if ($color) {
        echo '<p><strong>' . __('Color:', 'cardealership-child') . '</strong> ' . esc_html($color) . '</p>';
    }
    
    if ($vin) {
        echo '<p><strong>' . __('VIN:', 'cardealership-child') . '</strong> ' . esc_html($vin) . '</p>';
    }
    
    echo '</div>';
    
    // Display features
    if ($features) {
        echo '<div class="vehicle-features">';
        echo '<h4>' . __('Features', 'cardealership-child') . '</h4>';
        echo '<ul>';
        
        $features_array = explode("\n", $features);
        foreach ($features_array as $feature) {
            if (trim($feature)) {
                echo '<li>' . esc_html(trim($feature)) . '</li>';
            }
        }
        
        echo '</ul>';
        echo '</div>';
    }
    
    // Display financing calculator if available
    if ($financing_available) {
        $down_payment = get_post_meta($vehicle_id, '_vehicle_down_payment', true);
        $financing_term = get_post_meta($vehicle_id, '_vehicle_financing_term', true);
        $interest_rate = get_post_meta($vehicle_id, '_vehicle_interest_rate', true);
        $price = get_post_meta($vehicle_id, '_vehicle_price', true);
        
        echo '<div class="financing-calculator">';
        echo '<h4>' . __('Financing Calculator', 'cardealership-child') . '</h4>';
        echo '<input type="hidden" id="vehicle_price" value="' . esc_attr($price) . '">';
        echo '<input type="hidden" id="default_down_payment" value="' . esc_attr($down_payment) . '">';
        echo '<input type="hidden" id="default_term" value="' . esc_attr($financing_term) . '">';
        echo '<input type="hidden" id="default_rate" value="' . esc_attr($interest_rate) . '">';
        
        echo '<div class="calculator-form">';
        echo '<div class="form-group">';
        echo '<label for="down_payment">' . __('Down Payment (GYD):', 'cardealership-child') . '</label>';
        echo '<input type="number" id="down_payment" class="form-control" min="0" step="1000">';
        echo '</div>';
        
        echo '<div class="form-group">';
        echo '<label for="loan_term">' . __('Loan Term (months):', 'cardealership-child') . '</label>';
        echo '<select id="loan_term" class="form-control">';
        echo '<option value="12">12 ' . __('months', 'cardealership-child') . '</option>';
        echo '<option value="24">24 ' . __('months', 'cardealership-child') . '</option>';
        echo '<option value="36">36 ' . __('months', 'cardealership-child') . '</option>';
        echo '<option value="48">48 ' . __('months', 'cardealership-child') . '</option>';
        echo '<option value="60">60 ' . __('months', 'cardealership-child') . '</option>';
        echo '</select>';
        echo '</div>';
        
        echo '<div class="form-group">';
        echo '<label for="interest_rate">' . __('Interest Rate (%):', 'cardealership-child') . '</label>';
        echo '<input type="number" id="interest_rate" class="form-control" min="0" max="100" step="0.1">';
        echo '</div>';
        
        echo '<button type="button" id="calculate_payment" class="button alt">' . __('Calculate Payment', 'cardealership-child') . '</button>';
        echo '</div>';
        
        echo '<div class="calculator-results">';
        echo '<div class="result-group">';
        echo '<label>' . __('Monthly Payment:', 'cardealership-child') . '</label>';
        echo '<span id="monthly_payment"></span>';
        echo '</div>';
        
        echo '<div class="result-group">';
        echo '<label>' . __('Total Loan Amount:', 'cardealership-child') . '</label>';
        echo '<span id="loan_amount"></span>';
        echo '</div>';
        
        echo '<div class="result-group">';
        echo '<label>' . __('Total Interest:', 'cardealership-child') . '</label>';
        echo '<span id="total_interest"></span>';
        echo '</div>';
        
        echo '<div class="result-group">';
        echo '<label>' . __('Total Cost:', 'cardealership-child') . '</label>';
        echo '<span id="total_cost"></span>';
        echo '</div>';
        echo '</div>';
        
        echo '</div>'; // financing-calculator
    }
    
    echo '</div>'; // vehicle-details
}
add_action('woocommerce_after_single_product_summary', 'cardealership_display_vehicle_fields', 15);

/**
 * Add Contact Form to Product Pages
 */
function cardealership_vehicle_contact_form() {
    global $product;
    
    if (!$product) {
        return;
    }
    
    // Get linked vehicle
    $vehicle_id = get_post_meta($product->get_id(), '_linked_vehicle_id', true);
    
    if (!$vehicle_id) {
        return;
    }
    
    echo '<div class="vehicle-contact-form">';
    echo '<h3>' . __('Interested in this vehicle?', 'cardealership-child') . '</h3>';
    echo '<p>' . __('Fill out the form below and our team will get back to you as soon as possible.', 'cardealership-child') . '</p>';
    
    // Check if Contact Form 7 is active
    if (function_exists('wpcf7_contact_form_tag_func')) {
        // Replace '123' with your actual Contact Form 7 ID
        echo do_shortcode('[contact-form-7 id="123" title="Vehicle Inquiry"]');
    } else {
        // Basic form if Contact Form 7 is not available
        echo '<form class="inquiry-form" method="post">';
        echo '<input type="hidden" name="vehicle_id" value="' . esc_attr($vehicle_id) . '">';
        echo '<input type="hidden" name="vehicle_title" value="' . esc_attr(get_the_title($vehicle_id)) . '">';
        
        echo '<p>';
        echo '<label for="name">' . __('Your Name *', 'cardealership-child') . '</label>';
        echo '<input type="text" name="name" id="name" required>';
        echo '</p>';
        
        echo '<p>';
        echo '<label for="email">' . __('Your Email *', 'cardealership-child') . '</label>';
        echo '<input type="email" name="email" id="email" required>';
        echo '</p>';
        
        echo '<p>';
        echo '<label for="phone">' . __('Your Phone *', 'cardealership-child') . '</label>';
        echo '<input type="tel" name="phone" id="phone" required>';
        echo '</p>';
        
        echo '<p>';
        echo '<label for="message">' . __('Your Message', 'cardealership-child') . '</label>';
        echo '<textarea name="message" id="message" rows="4"></textarea>';
        echo '</p>';
        
        echo '<p>';
        echo '<label>';
        echo '<input type="checkbox" name="financing" value="1"> ' . __('I am interested in financing options', 'cardealership-child');
        echo '</label>';
        echo '</p>';
        
        echo '<p>';
        echo '<button type="submit" class="button alt">' . __('Send Inquiry', 'cardealership-child') . '</button>';
        echo '</p>';
        
        echo '</form>';
    }
    
    echo '</div>';
}
add_action('woocommerce_after_single_product', 'cardealership_vehicle_contact_form', 10);

/**
 * Process the vehicle inquiry form
 */
function cardealership_process_inquiry_form() {
    if (isset($_POST['vehicle_id']) && isset($_POST['name']) && isset($_POST['email']) && isset($_POST['phone'])) {
        $vehicle_id = sanitize_text_field($_POST['vehicle_id']);
        $vehicle_title = sanitize_text_field($_POST['vehicle_title']);
        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $phone = sanitize_text_field($_POST['phone']);
        $message = isset($_POST['message']) ? sanitize_textarea_field($_POST['message']) : '';
        $financing = isset($_POST['financing']) ? true : false;
        
        // Email to admin
        $admin_email = get_option('admin_email');
        $subject = sprintf(__('New Vehicle Inquiry: %s', 'cardealership-child'), $vehicle_title);
        
        $body = sprintf(__('Name: %s', 'cardealership-child'), $name) . "\n\n";
        $body .= sprintf(__('Email: %s', 'cardealership-child'), $email) . "\n\n";
        $body .= sprintf(__('Phone: %s', 'cardealership-child'), $phone) . "\n\n";
        
        if (!empty($message)) {
            $body .= sprintf(__('Message: %s', 'cardealership-child'), $message) . "\n\n";
        }
        
        $body .= sprintf(__('Vehicle: %s (ID: %s)', 'cardealership-child'), $vehicle_title, $vehicle_id) . "\n\n";
        $body .= $financing ? __('Customer is interested in financing options.', 'cardealership-child') . "\n\n" : '';
        
        wp_mail($admin_email, $subject, $body);
        
        // Redirect back with success message
        wp_safe_redirect(add_query_arg('inquiry_sent', '1', get_permalink($vehicle_id)));
        exit;
    }
}
add_action('template_redirect', 'cardealership_process_inquiry_form');

/**
 * Display inquiry success message
 */
function cardealership_display_inquiry_message() {
    if (isset($_GET['inquiry_sent']) && $_GET['inquiry_sent'] == '1') {
        wc_add_notice(__('Thank you! Your inquiry has been sent. Our team will contact you shortly.', 'cardealership-child'), 'success');
    }
}
add_action('woocommerce_before_single_product', 'cardealership_display_inquiry_message');

/**
 * Import common vehicle makes and models
 */
function cardealership_import_vehicle_data() {
    // Only run this once
    if (get_option('cardealership_data_imported')) {
        return;
    }
    
    // Common car makes
    $makes = array(
        'Toyota', 'Honda', 'Ford', 'Chevrolet', 'Nissan',
        'Hyundai', 'Kia', 'Suzuki', 'Mazda', 'Mercedes-Benz',
        'BMW', 'Audi', 'Volkswagen', 'Mitsubishi', 'Land Rover',
        'Jeep', 'Subaru', 'Lexus', 'Isuzu'
    );
    
    // Import makes
    foreach ($makes as $make) {
        if (!term_exists($make, 'vehicle_make')) {
            wp_insert_term($make, 'vehicle_make');
        }
    }
    
    // Common vehicle types
    $types = array(
        'New', 'Used', 'Certified Pre-Owned'
    );
    
    // Import types
    foreach ($types as $type) {
        if (!term_exists($type, 'vehicle_type')) {
            wp_insert_term($type, 'vehicle_type');
        }
    }
    
    // Mark as imported
    update_option('cardealership_data_imported', true);
}
add_action('init', 'cardealership_import_vehicle_data');

/**
 * Register Vehicle Shortcodes
 */

// Featured Vehicles Shortcode
function cardealership_featured_vehicles_shortcode($atts) {
    $atts = shortcode_atts(array(
        'count' => 4,
        'type' => '',
        'make' => '',
    ), $atts);
    
    $args = array(
        'post_type' => 'vehicle',
        'posts_per_page' => intval($atts['count']),
        'meta_key' => '_vehicle_price',
        'orderby' => 'meta_value_num',
        'order' => 'DESC',
    );
    
    // Filter by type if specified
    if (!empty($atts['type'])) {
        $args['tax_query'][] = array(
            'taxonomy' => 'vehicle_type',
            'field' => 'slug',
            'terms' => $atts['type'],
        );
    }
    
    // Filter by make if specified
    if (!empty($atts['make'])) {
        $args['tax_query'][] = array(
            'taxonomy' => 'vehicle_make',
            'field' => 'slug',
            'terms' => $atts['make'],
        );
    }
    
    $query = new WP_Query($args);
    
    ob_start();
    
    if ($query->have_posts()) {
        echo '<div class="featured-vehicles">';
        
        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
            
            $price = get_post_meta($post_id, '_vehicle_price', true);
            $year = get_post_meta($post_id, '_vehicle_year', true);
            $mileage = get_post_meta($post_id, '_vehicle_mileage', true);
            $make_terms = wp_get_post_terms($post_id, 'vehicle_make');
            $model_terms = wp_get_post_terms($post_id, 'vehicle_model');
            
            $make = !empty($make_terms) ? $make_terms[0]->name : '';
            $model = !empty($model_terms) ? $model_terms[0]->name : '';
            
            echo '<div class="vehicle-featured">';
            
            if (has_post_thumbnail()) {
                echo '<div class="vehicle-thumbnail">';
                echo '<a href="' . get_permalink() . '">';
                the_post_thumbnail('medium');
                echo '</a>';
                echo '</div>';
            }
            
            echo '<div class="vehicle-info">';
            echo '<h3><a href="' . get_permalink() . '">' . get_the_title() . '</a></h3>';
            
            if ($year || $make || $model) {
                echo '<p class="vehicle-meta">';
                echo $year ? $year . ' ' : '';
                echo $make ? $make . ' ' : '';
                echo $model ? $model : '';
                echo '</p>';
            }
            
            if ($price) {
                echo '<p class="vehicle-price">' . wc_price($price) . '</p>';
            }
            
            if ($mileage) {
                echo '<p class="vehicle-mileage"><strong>' . __('Mileage:', 'cardealership-child') . '</strong> ' . number_format($mileage) . ' km</p>';
            }
            
            echo '<a href="' . get_permalink() . '" class="button">' . __('View Details', 'cardealership-child') . '</a>';
            echo '</div>'; // vehicle-info
            
            echo '</div>'; // vehicle-featured
        }
        
        echo '</div>'; // featured-vehicles
    } else {
        echo '<p>' . __('No vehicles found.', 'cardealership-child') . '</p>';
    }
    
    wp_reset_postdata();
    
    return ob_get_clean();
}
add_shortcode('featured_vehicles', 'cardealership_featured_vehicles_shortcode');

// Vehicle Search Shortcode
function cardealership_vehicle_search_shortcode($atts) {
    $atts = shortcode_atts(array(
        'title' => __('Find Your Perfect Vehicle', 'cardealership-child'),
    ), $atts);
    
    // Get all makes
    $makes = get_terms(array(
        'taxonomy' => 'vehicle_make',
        'hide_empty' => true,
    ));
    
    // Get all types
    $types = get_terms(array(
        'taxonomy' => 'vehicle_type',
        'hide_empty' => true,
    ));
    
    ob_start();
    
    echo '<div class="vehicle-search-form">';
    
    if (!empty($atts['title'])) {
        echo '<h3>' . esc_html($atts['title']) . '</h3>';
    }
    
    echo '<form method="get" action="' . esc_url(home_url('/')) . '">';
    echo '<input type="hidden" name="post_type" value="vehicle">';
    
    echo '<div class="search-fields">';
    
    // Make dropdown
    echo '<div class="search-field">';
    echo '<label for="vehicle_make">' . __('Make', 'cardealership-child') . '</label>';
    echo '<select name="vehicle_make" id="vehicle_make">';
    echo '<option value="">' . __('Any Make', 'cardealership-child') . '</option>';
    
    if (!empty($makes) && !is_wp_error($makes)) {
        foreach ($makes as $make) {
            echo '<option value="' . esc_attr($make->slug) . '">' . esc_html($make->name) . '</option>';
        }
    }
    
    echo '</select>';
    echo '</div>';
    
    // Type dropdown
    echo '<div class="search-field">';
    echo '<label for="vehicle_type">' . __('Type', 'cardealership-child') . '</label>';
    echo '<select name="vehicle_type" id="vehicle_type">';
    echo '<option value="">' . __('Any Type', 'cardealership-child') . '</option>';
    
    if (!empty($types) && !is_wp_error($types)) {
        foreach ($types as $type) {
            echo '<option value="' . esc_attr($type->slug) . '">' . esc_html($type->name) . '</option>';
        }
    }
    
    echo '</select>';
    echo '</div>';
    
    // Price range
    echo '<div class="search-field">';
    echo '<label for="min_price">' . __('Min Price (GYD)', 'cardealership-child') . '</label>';
    echo '<input type="number" name="min_price" id="min_price" min="0" step="100000">';
    echo '</div>';
    
    echo '<div class="search-field">';
    echo '<label for="max_price">' . __('Max Price (GYD)', 'cardealership-child') . '</label>';
    echo '<input type="number" name="max_price" id="max_price" min="0" step="100000">';
    echo '</div>';
    
    // Search button
    echo '<div class="search-field search-submit">';
    echo '<button type="submit" class="button">' . __('Search Vehicles', 'cardealership-child') . '</button>';
    echo '</div>';
    
    echo '</div>'; // search-fields
    
    echo '</form>';
    echo '</div>'; // vehicle-search-form
    
    return ob_get_clean();
}
add_shortcode('vehicle_search', 'cardealership_vehicle_search_shortcode');

/**
 * Vehicle Search Filter
 */
function cardealership_vehicle_search_filter($query) {
    if (!is_admin() && $query->is_main_query() && $query->is_post_type_archive('vehicle')) {
        // Make filter
        if (isset($_GET['vehicle_make']) && !empty($_GET['vehicle_make'])) {
            $tax_query = $query->get('tax_query');
            if (!is_array($tax_query)) {
                $tax_query = array();
            }
            
            $tax_query[] = array(
                'taxonomy' => 'vehicle_make',
                'field' => 'slug',
                'terms' => sanitize_text_field($_GET['vehicle_make']),
            );
            
            $query->set('tax_query', $tax_query);
        }
        
        // Type filter
        if (isset($_GET['vehicle_type']) && !empty($_GET['vehicle_type'])) {
            $tax_query = $query->get('tax_query');
            if (!is_array($tax_query)) {
                $tax_query = array();
            }
            
            $tax_query[] = array(
                'taxonomy' => 'vehicle_type',
                'field' => 'slug',
                'terms' => sanitize_text_field($_GET['vehicle_type']),
            );
            
            $query->set('tax_query', $tax_query);
        }
        
        // Price range filter
        $meta_query = $query->get('meta_query');
        if (!is_array($meta_query)) {
            $meta_query = array();
        }
        
        if (isset($_GET['min_price']) && !empty($_GET['min_price'])) {
            $meta_query[] = array(
                'key' => '_vehicle_price',
                'value' => intval($_GET['min_price']),
                'compare' => '>=',
                'type' => 'NUMERIC',
            );
        }
        
        if (isset($_GET['max_price']) && !empty($_GET['max_price'])) {
            $meta_query[] = array(
                'key' => '_vehicle_price',
                'value' => intval($_GET['max_price']),
                'compare' => '<=',
                'type' => 'NUMERIC',
            );
        }
        
        if (!empty($meta_query)) {
            $query->set('meta_query', $meta_query);
        }
    }
    
    return $query;
}
add_action('pre_get_posts', 'cardealership_vehicle_search_filter');
