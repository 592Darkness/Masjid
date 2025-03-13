<?php
/**
 * WooCommerce Configuration for Car Dealership
 * Add this code to your functions.php
 */

/**
 * Setup WooCommerce Support
 */
function cardealership_woocommerce_support() {
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}
add_action('after_setup_theme', 'cardealership_woocommerce_support');

/**
 * Customize WooCommerce Product Types for Cars and Parts
 */
function cardealership_product_types($types) {
    // Add vehicle as a product type
    $types['vehicle'] = __('Vehicle', 'cardealership-child');
    
    return $types;
}
add_filter('product_type_selector', 'cardealership_product_types');

/**
 * Add Custom Car Part Product Type
 */
function cardealership_register_car_part_product_type() {
    // Create Car Part Product Type
    class WC_Product_Car_Part extends WC_Product {
        public function __construct($product = 0) {
            $this->product_type = 'car_part';
            parent::__construct($product);
        }
        
        public function get_type() {
            return 'car_part';
        }
    }
}
add_action('init', 'cardealership_register_car_part_product_type');

/**
 * Add Car Part compatibility to cart and checkout
 */
function cardealership_car_part_type_to_cart_item($cart_item_data, $product_id, $variation_id) {
    $product = wc_get_product($product_id);
    
    if ($product && $product->get_type() === 'car_part') {
        $cart_item_data['car_part_data'] = array(
            'compatible_vehicles' => get_post_meta($product_id, '_compatible_vehicles', true),
        );
    }
    
    return $cart_item_data;
}
add_filter('woocommerce_add_cart_item_data', 'cardealership_car_part_type_to_cart_item', 10, 3);

/**
 * Add Custom Vehicle Compatibility Meta Box for Car Parts
 */
function cardealership_car_part_compatibility_meta_box() {
    add_meta_box(
        'vehicle_compatibility',
        __('Vehicle Compatibility', 'cardealership-child'),
        'cardealership_vehicle_compatibility_callback',
        'product',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'cardealership_car_part_compatibility_meta_box');

/**
 * Vehicle Compatibility Meta Box Callback
 */
function cardealership_vehicle_compatibility_callback($post) {
    // Add nonce for security
    wp_nonce_field('cardealership_save_vehicle_compatibility', 'cardealership_vehicle_compatibility_nonce');
    
    // Get saved compatibility data
    $compatibility = get_post_meta($post->ID, '_compatible_vehicles', true);
    
    if (!is_array($compatibility)) {
        $compatibility = array();
    }
    
    // Get vehicle makes and models
    $makes = get_terms(array(
        'taxonomy' => 'vehicle_make',
        'hide_empty' => false,
    ));
    
    echo '<div class="vehicle-compatibility-container">';
    echo '<p>' . __('Select which vehicles this part is compatible with:', 'cardealership-child') . '</p>';
    
    if (!empty($makes) && !is_wp_error($makes)) {
        echo '<div class="vehicle-makes-list">';
        
        foreach ($makes as $make) {
            echo '<div class="vehicle-make-item">';
            echo '<h4>' . esc_html($make->name) . '</h4>';
            
            // Get models for this make
            $models = get_terms(array(
                'taxonomy' => 'vehicle_model',
                'hide_empty' => false,
                'meta_query' => array(
                    array(
                        'key' => 'vehicle_make_id',
                        'value' => $make->term_id,
                        'compare' => '=',
                    ),
                ),
            ));
            
            if (!empty($models) && !is_wp_error($models)) {
                echo '<div class="vehicle-models-list">';
                
                foreach ($models as $model) {
                    $model_key = $make->slug . '_' . $model->slug;
                    $checked = in_array($model_key, $compatibility) ? 'checked="checked"' : '';
                    
                    echo '<div class="vehicle-model-item">';
                    echo '<label>';
                    echo '<input type="checkbox" name="compatible_vehicles[]" value="' . esc_attr($model_key) . '" ' . $checked . '>';
                    echo esc_html($model->name);
                    echo '</label>';
                    echo '</div>';
                }
                
                echo '</div>';
            } else {
                echo '<p>' . __('No models available for this make.', 'cardealership-child') . '</p>';
            }
            
            echo '</div>';
        }
        
        echo '</div>';
    } else {
        echo '<p>' . __('No vehicle makes available. Please add vehicle makes first.', 'cardealership-child') . '</p>';
    }
    
    echo '<div class="compatibility-manual-entry">';
    echo '<h4>' . __('Manual Compatibility Entry', 'cardealership-child') . '</h4>';
    echo '<p>' . __('Enter additional compatibility information (one per line):', 'cardealership-child') . '</p>';
    
    $manual_compatibility = get_post_meta($post->ID, '_manual_compatibility', true);
    echo '<textarea name="manual_compatibility" rows="5" style="width: 100%;">' . esc_textarea($manual_compatibility) . '</textarea>';
    echo '</div>';
    
    echo '</div>';
}

/**
 * Save Vehicle Compatibility Meta Box Data
 */
function cardealership_save_vehicle_compatibility($post_id) {
    // Check if nonce is set
    if (!isset($_POST['cardealership_vehicle_compatibility_nonce'])) {
        return;
    }
    
    // Verify nonce
    if (!wp_verify_nonce($_POST['cardealership_vehicle_compatibility_nonce'], 'cardealership_save_vehicle_compatibility')) {
        return;
    }
    
    // If this is an autosave, don't do anything
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Check the user's permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Save compatibility data
    $compatible_vehicles = isset($_POST['compatible_vehicles']) ? $_POST['compatible_vehicles'] : array();
    update_post_meta($post_id, '_compatible_vehicles', $compatible_vehicles);
    
    // Save manual compatibility data
    if (isset($_POST['manual_compatibility'])) {
        update_post_meta($post_id, '_manual_compatibility', sanitize_textarea_field($_POST['manual_compatibility']));
    }
}
add_action('save_post', 'cardealership_save_vehicle_compatibility');

/**
 * Display compatibility information on product page
 */
function cardealership_display_compatibility_info() {
    global $product;
    
    if (!$product) {
        return;
    }
    
    $compatibility = get_post_meta($product->get_id(), '_compatible_vehicles', true);
    $manual_compatibility = get_post_meta($product->get_id(), '_manual_compatibility', true);
    
    if (empty($compatibility) && empty($manual_compatibility)) {
        return;
    }
    
    echo '<div class="part-compatibility">';
    echo '<h3>' . __('Compatible Vehicles', 'cardealership-child') . '</h3>';
    
    if (!empty($compatibility) && is_array($compatibility)) {
        echo '<ul>';
        
        foreach ($compatibility as $model_key) {
            list($make_slug, $model_slug) = explode('_', $model_key);
            
            $make_term = get_term_by('slug', $make_slug, 'vehicle_make');
            $model_term = get_term_by('slug', $model_slug, 'vehicle_model');
            
            if ($make_term && $model_term) {
                echo '<li>' . esc_html($make_term->name) . ' ' . esc_html($model_term->name) . '</li>';
            }
        }
        
        echo '</ul>';
    }
    
    if (!empty($manual_compatibility)) {
        echo '<div class="manual-compatibility">';
        echo '<h4>' . __('Additional Compatibility:', 'cardealership-child') . '</h4>';
        echo '<ul>';
        
        $compatibility_items = explode("\n", $manual_compatibility);
        foreach ($compatibility_items as $item) {
            if (trim($item)) {
                echo '<li>' . esc_html(trim($item)) . '</li>';
            }
        }
        
        echo '</ul>';
        echo '</div>';
    }
    
    echo '</div>';
}
add_action('woocommerce_single_product_summary', 'cardealership_display_compatibility_info', 25);

/**
 * Create Car Parts category
 */
function cardealership_create_car_parts_category() {
    $parent_category_id = term_exists('Car Parts', 'product_cat');
    
    if (!$parent_category_id) {
        $parent_category_id = wp_insert_term(
            'Car Parts',
            'product_cat',
            array(
                'description' => __('All car parts and accessories', 'cardealership-child'),
                'slug' => 'car-parts'
            )
        );
    }
    
    if (!is_wp_error($parent_category_id)) {
        // Add subcategories for common car parts
        $subcategories = array(
            'Engine Parts' => 'Components for the engine system',
            'Transmission Parts' => 'Components for the transmission system',
            'Brake System' => 'Brake pads, rotors, and components',
            'Suspension & Steering' => 'Suspension and steering components',
            'Electrical System' => 'Electrical components and accessories',
            'Body Parts' => 'External body components',
            'Interior Accessories' => 'Interior components and accessories',
            'Filters & Fluids' => 'Replacement filters and fluids'
        );
        
        foreach ($subcategories as $name => $description) {
            if (!term_exists($name, 'product_cat')) {
                wp_insert_term(
                    $name,
                    'product_cat',
                    array(
                        'description' => __($description, 'cardealership-child'),
                        'slug' => sanitize_title($name),
                        'parent' => is_array($parent_category_id) ? $parent_category_id['term_id'] : $parent_category_id
                    )
                );
            }
        }
    }
}
add_action('init', 'cardealership_create_car_parts_category');

/**
 * Add Part Number field to products
 */
function cardealership_part_number_field() {
    woocommerce_wp_text_input(
        array(
            'id' => '_part_number',
            'label' => __('Part Number', 'cardealership-child'),
            'placeholder' => '',
            'desc_tip' => 'true',
            'description' => __('Enter the part number for this product.', 'cardealership-child')
        )
    );
}
add_action('woocommerce_product_options_inventory_product_data', 'cardealership_part_number_field');

/**
 * Save Part Number field
 */
function cardealership_save_part_number_field($post_id) {
    $part_number = isset($_POST['_part_number']) ? sanitize_text_field($_POST['_part_number']) : '';
    update_post_meta($post_id, '_part_number', $part_number);
}
add_action('woocommerce_process_product_meta', 'cardealership_save_part_number_field');

/**
 * Display Part Number on product page
 */
function cardealership_display_part_number() {
    global $product;
    
    if (!$product) {
        return;
    }
    
    $part_number = get_post_meta($product->get_id(), '_part_number', true);
    
    if (!empty($part_number)) {
        echo '<div class="part-number">';
        echo '<span class="label">' . __('Part Number:', 'cardealership-child') . '</span> ';
        echo '<span class="value">' . esc_html($part_number) . '</span>';
        echo '</div>';
    }
}
add_action('woocommerce_single_product_summary', 'cardealership_display_part_number', 25);

/**
 * Modify WooCommerce checkout fields for car parts
 */
function cardealership_modify_checkout_fields_for_parts($fields) {
    // Check if cart has only car parts
    $has_only_parts = true;
    
    foreach (WC()->cart->get_cart() as $cart_item) {
        $product_id = $cart_item['product_id'];
        $product = wc_get_product($product_id);
        
        if ($product && $product->get_type() !== 'car_part') {
            $has_only_parts = false;
            break;
        }
    }
    
    // Modify fields if only car parts in cart
    if ($has_only_parts) {
        // Add vehicle info field
        $fields['order']['order_vehicle_info'] = array(
            'type' => 'textarea',
            'label' => __('Vehicle Information', 'cardealership-child'),
            'placeholder' => __('Please provide your vehicle make, model, and year for compatibility verification.', 'cardealership-child'),
            'required' => false,
            'class' => array('notes'),
            'clear' => true
        );
    }
    
    return $fields;
}
add_filter('woocommerce_checkout_fields', 'cardealership_modify_checkout_fields_for_parts');

/**
 * Save vehicle info to order
 */
function cardealership_save_vehicle_info_to_order($order_id) {
    if (isset($_POST['order_vehicle_info'])) {
        update_post_meta($order_id, '_vehicle_info', sanitize_textarea_field($_POST['order_vehicle_info']));
    }
}
add_action('woocommerce_checkout_update_order_meta', 'cardealership_save_vehicle_info_to_order');

/**
 * Display vehicle info in admin order page
 */
function cardealership_display_vehicle_info_in_admin($order) {
    $order_id = $order->get_id();
    $vehicle_info = get_post_meta($order_id, '_vehicle_info', true);
    
    if (!empty($vehicle_info)) {
        echo '<p><strong>' . __('Vehicle Information:', 'cardealership-child') . '</strong> ' . esc_html($vehicle_info) . '</p>';
    }
}
add_action('woocommerce_admin_order_data_after_shipping_address', 'cardealership_display_vehicle_info_in_admin');

/**
 * Add shipping classes for car parts
 */
function cardealership_add_shipping_classes() {
    $shipping_classes = array(
        'small-parts' => array(
            'name' => 'Small Parts',
            'description' => 'Small automotive parts (filters, spark plugs, etc.)'
        ),
        'medium-parts' => array(
            'name' => 'Medium Parts',
            'description' => 'Medium-sized parts (alternators, starters, etc.)'
        ),
        'large-parts' => array(
            'name' => 'Large Parts',
            'description' => 'Large parts (bumpers, doors, etc.)'
        ),
        'pickup-only' => array(
            'name' => 'Pickup Only',
            'description' => 'Parts available for in-store pickup only'
        )
    );
    
    foreach ($shipping_classes as $slug => $data) {
        if (!term_exists($data['name'], 'product_shipping_class')) {
            wp_insert_term(
                $data['name'],
                'product_shipping_class',
                array(
                    'description' => $data['description'],
                    'slug' => $slug
                )
            );
        }
    }
}
add_action('init', 'cardealership_add_shipping_classes');

/**
 * Add Car Parts filter widget to shop sidebar
 */
function cardealership_parts_filter_widget() {
    register_widget('Cardealership_Parts_Filter_Widget');
}
add_action('widgets_init', 'cardealership_parts_filter_widget');

/**
 * Car Parts Filter Widget Class
 */
class Cardealership_Parts_Filter_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'cardealership_parts_filter',
            __('Car Parts Filter', 'cardealership-child'),
            array('description' => __('Filters car parts by vehicle compatibility', 'cardealership-child'))
        );
    }
    
    public function widget($args, $instance) {
        $title = apply_filters('widget_title', $instance['title']);
        
        echo $args['before_widget'];
        
        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        
        // Get vehicle makes
        $makes = get_terms(array(
            'taxonomy' => 'vehicle_make',
            'hide_empty' => true,
        ));
        
        if (!empty($makes) && !is_wp_error($makes)) {
            echo '<form class="parts-filter-form" method="get" action="' . esc_url(home_url('/shop/')) . '">';
            
            // Make dropdown
            echo '<div class="form-group">';
            echo '<label for="vehicle_make">' . __('Make:', 'cardealership-child') . '</label>';
            echo '<select name="vehicle_make" id="vehicle_make">';
            echo '<option value="">' . __('Select Make', 'cardealership-child') . '</option>';
            
            foreach ($makes as $make) {
                $selected = isset($_GET['vehicle_make']) && $_GET['vehicle_make'] === $make->slug ? 'selected="selected"' : '';
                echo '<option value="' . esc_attr($make->slug) . '" ' . $selected . '>' . esc_html($make->name) . '</option>';
            }
            
            echo '</select>';
            echo '</div>';
            
            // Model dropdown (populated via AJAX when make is selected)
            echo '<div class="form-group">';
            echo '<label for="vehicle_model">' . __('Model:', 'cardealership-child') . '</label>';
            echo '<select name="vehicle_model" id="vehicle_model">';
            echo '<option value="">' . __('Select Model', 'cardealership-child') . '</option>';
            
            // If make is selected, populate models
            if (isset($_GET['vehicle_make']) && !empty($_GET['vehicle_make'])) {
                $models = get_terms(array(
                    'taxonomy' => 'vehicle_model',
                    'hide_empty' => true,
                    'meta_query' => array(
                        array(
                            'key' => 'vehicle_make_id',
                            'value' => term_exists($_GET['vehicle_make'], 'vehicle_make'),
                            'compare' => '=',
                        ),
                    ),
                ));
                
                if (!empty($models) && !is_wp_error($models)) {
                    foreach ($models as $model) {
                        $selected = isset($_GET['vehicle_model']) && $_GET['vehicle_model'] === $model->slug ? 'selected="selected"' : '';
                        echo '<option value="' . esc_attr($model->slug) . '" ' . $selected . '>' . esc_html($model->name) . '</option>';
                    }
                }
            }
            
            echo '</select>';
            echo '</div>';
            
            // Year field
            echo '<div class="form-group">';
            echo '<label for="vehicle_year">' . __('Year:', 'cardealership-child') . '</label>';
            echo '<input type="number" name="vehicle_year" id="vehicle_year" min="1900" max="' . date('Y') . '" value="' . (isset($_GET['vehicle_year']) ? esc_attr($_GET['vehicle_year']) : '') . '">';
            echo '</div>';
            
            // Part type/category dropdown
            $part_categories = get_terms(array(
                'taxonomy' => 'product_cat',
                'hide_empty' => true,
                'parent' => term_exists('Car Parts', 'product_cat')['term_id'],
            ));
            
            if (!empty($part_categories) && !is_wp_error($part_categories)) {
                echo '<div class="form-group">';
                echo '<label for="part_category">' . __('Part Category:', 'cardealership-child') . '</label>';
                echo '<select name="part_category" id="part_category">';
                echo '<option value="">' . __('All Categories', 'cardealership-child') . '</option>';
                
                foreach ($part_categories as $category) {
                    $selected = isset($_GET['part_category']) && $_GET['part_category'] === $category->slug ? 'selected="selected"' : '';
                    echo '<option value="' . esc_attr($category->slug) . '" ' . $selected . '>' . esc_html($category->name) . '</option>';
                }
                
                echo '</select>';
                echo '</div>';
            }
            
            // Submit button
            echo '<div class="form-group">';
            echo '<button type="submit" class="button">' . __('Find Parts', 'cardealership-child') . '</button>';
            echo '</div>';
            
            echo '</form>';
            
            // Add JavaScript for dynamic model dropdown
            ?>
            <script>
                jQuery(document).ready(function($) {
                    $('#vehicle_make').change(function() {
                        var make = $(this).val();
                        var modelSelect = $('#vehicle_model');
                        
                        // Clear current options
                        modelSelect.html('<option value=""><?php echo esc_js(__('Select Model', 'cardealership-child')); ?></option>');
                        
                        if (make) {
                            // AJAX request to get models
                            $.ajax({
                                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                                type: 'POST',
                                data: {
                                    action: 'get_vehicle_models',
                                    make: make
                                },
                                success: function(response) {
                                    if (response.success) {
                                        // Add new options
                                        $.each(response.data, function(i, model) {
                                            modelSelect.append('<option value="' + model.slug + '">' + model.name + '</option>');
                                        });
                                    }
                                }
                            });
                        }
                    });
                });
            </script>
            <?php
        }
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : __('Find Parts by Vehicle', 'cardealership-child');
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'cardealership-child'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        
        return $instance;
    }
}

/**
 * AJAX handler for getting vehicle models
 */
function cardealership_get_vehicle_models() {
    $make = isset($_POST['make']) ? sanitize_text_field($_POST['make']) : '';
    
    if (empty($make)) {
        wp_send_json_error();
    }
    
    $make_term = get_term_by('slug', $make, 'vehicle_make');
    
    if (!$make_term) {
        wp_send_json_error();
    }
    
    $models = get_terms(array(
        'taxonomy' => 'vehicle_model',
        'hide_empty' => true,
        'meta_query' => array(
            array(
                'key' => 'vehicle_make_id',
                'value' => $make_term->term_id,
                'compare' => '=',
            ),
        ),
    ));
    
    if (empty($models) || is_wp_error($models)) {
        wp_send_json_error();
    }
    
    $model_data = array();
    
    foreach ($models as $model) {
        $model_data[] = array(
            'slug' => $model->slug,
            'name' => $model->name
        );
    }
    
    wp_send_json_success($model_data);
}
add_action('wp_ajax_get_vehicle_models', 'cardealership_get_vehicle_models');
add_action('wp_ajax_nopriv_get_vehicle_models', 'cardealership_get_vehicle_models');

/**
 * Filter products by vehicle compatibility
 */
function cardealership_filter_products_by_vehicle($query) {
    if (!is_admin() && $query->is_main_query() && (is_shop() || is_product_category())) {
        $make = isset($_GET['vehicle_make']) ? sanitize_text_field($_GET['vehicle_make']) : '';
        $model = isset($_GET['vehicle_model']) ? sanitize_text_field($_GET['vehicle_model']) : '';
        $year = isset($_GET['vehicle_year']) ? intval($_GET['vehicle_year']) : 0;
        $category = isset($_GET['part_category']) ? sanitize_text_field($_GET['part_category']) : '';
        
        // Apply category filter
        if (!empty($category)) {
            $tax_query = $query->get('tax_query');
            if (!is_array($tax_query)) {
                $tax_query = array();
            }
            
            $tax_query[] = array(
                'taxonomy' => 'product_cat',
                'field' => 'slug',
                'terms' => $category,
            );
            
            $query->set('tax_query', $tax_query);
        }
        
        // Apply vehicle compatibility filter
        if (!empty($make) && !empty($model)) {
            $model_key = $make . '_' . $model;
            
            // Get products with compatibility for this make/model
            $compatible_products = get_posts(array(
                'post_type' => 'product',
                'posts_per_page' => -1,
                'fields' => 'ids',
                'meta_query' => array(
                    array(
                        'key' => '_compatible_vehicles',
                        'value' => $model_key,
                        'compare' => 'LIKE',
                    ),
                ),
            ));
            
            if (!empty($compatible_products)) {
                $query->set('post__in', $compatible_products);
            } else {
                // No compatible products found
                $query->set('post__in', array(0)); // Force no results
            }
        }
    }
    
    return $query;
}
add_action('pre_get_posts', 'cardealership_filter_products_by_vehicle');
