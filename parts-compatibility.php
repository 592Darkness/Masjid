<?php
/**
 * Car Parts Compatibility System
 * Add this code to your functions.php or include it as a separate file
 */

/**
 * Register the compatibility check endpoint for AJAX
 */
function cardealership_register_compatibility_endpoint() {
    add_action('wp_ajax_check_part_compatibility', 'cardealership_check_part_compatibility');
    add_action('wp_ajax_nopriv_check_part_compatibility', 'cardealership_check_part_compatibility');
}
add_action('init', 'cardealership_register_compatibility_endpoint');

/**
 * Check part compatibility AJAX handler
 */
function cardealership_check_part_compatibility() {
    // Get parameters
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $make = isset($_POST['make']) ? sanitize_text_field($_POST['make']) : '';
    $model = isset($_POST['model']) ? sanitize_text_field($_POST['model']) : '';
    $year = isset($_POST['year']) ? intval($_POST['year']) : 0;
    
    if (!$product_id || !$make || !$model || !$year) {
        wp_send_json_error(__('Missing required parameters.', 'cardealership-child'));
    }
    
    // Get compatibility data for the product
    $compatibility = cardealership_get_product_compatibility($product_id);
    
    // Check against user vehicle
    $user_vehicle = $make . '_' . $model;
    $is_compatible = false;
    $compatible_years = array();
    
    foreach ($compatibility as $compatible_item) {
        if ($compatible_item['code'] === $user_vehicle) {
            $is_compatible = true;
            $compatible_years = $compatible_item['years'];
            break;
        }
    }
    
    // Check year range compatibility
    $year_compatible = false;
    if ($is_compatible && in_array($year, $compatible_years)) {
        $year_compatible = true;
    }
    
    // Prepare response
    $response = array(
        'is_compatible' => $is_compatible && $year_compatible,
        'make' => $make,
        'model' => $model,
        'year' => $year,
        'message' => '',
    );
    
    if ($is_compatible && $year_compatible) {
        $response['message'] = sprintf(
            __('This part is compatible with your %s %s %s.', 'cardealership-child'),
            $year,
            $make,
            $model
        );
    } elseif ($is_compatible && !$year_compatible) {
        // Compatible with model but not year
        $response['message'] = sprintf(
            __('This part is compatible with %s %s, but not with year %s. Compatible years: %s.', 'cardealership-child'),
            $make,
            $model,
            $year,
            implode(', ', $compatible_years)
        );
    } else {
        $response['message'] = sprintf(
            __('This part is not compatible with your %s %s %s.', 'cardealership-child'),
            $year,
            $make,
            $model
        );
    }
    
    wp_send_json_success($response);
}

/**
 * Get product compatibility data
 */
function cardealership_get_product_compatibility($product_id) {
    $compatibility = array();
    
    $compatible_vehicles = get_post_meta($product_id, '_compatible_vehicles', true);
    
    if (empty($compatible_vehicles) || !is_array($compatible_vehicles)) {
        return $compatibility;
    }
    
    // Group by make_model and collect years
    $grouped_compatibility = array();
    
    foreach ($compatible_vehicles as $vehicle_code) {
        $parts = explode('_', $vehicle_code);
        if (count($parts) == 2) {
            $make_model = $vehicle_code;
            $year = null;
        } elseif (count($parts) == 3) {
            $make_model = $parts[0] . '_' . $parts[1];
            $year = intval($parts[2]);
        } else {
            continue;
        }
        
        if (!isset($grouped_compatibility[$make_model])) {
            $make_slug = explode('_', $make_model)[0];
            $model_slug = explode('_', $make_model)[1];
            
            $make_term = get_term_by('slug', $make_slug, 'vehicle_make');
            $model_term = get_term_by('slug', $model_slug, 'vehicle_model');
            
            $make_name = $make_term ? $make_term->name : $make_slug;
            $model_name = $model_term ? $model_term->name : $model_slug;
            
            $grouped_compatibility[$make_model] = array(
                'code' => $make_model,
                'make' => $make_name,
                'model' => $model_name,
                'years' => array(),
            );
        }
        
        if ($year) {
            $grouped_compatibility[$make_model]['years'][] = $year;
        }
    }
    
    // Add year ranges from custom fields
    $year_ranges = get_post_meta($product_id, '_compatibility_year_ranges', true);
    
    if (!empty($year_ranges) && is_array($year_ranges)) {
        foreach ($year_ranges as $range) {
            if (isset($range['make_model'], $range['start_year'], $range['end_year'])) {
                $make_model = $range['make_model'];
                $start_year = intval($range['start_year']);
                $end_year = intval($range['end_year']);
                
                if (!isset($grouped_compatibility[$make_model])) {
                    $make_slug = explode('_', $make_model)[0];
                    $model_slug = explode('_', $make_model)[1];
                    
                    $make_term = get_term_by('slug', $make_slug, 'vehicle_make');
                    $model_term = get_term_by('slug', $model_slug, 'vehicle_model');
                    
                    $make_name = $make_term ? $make_term->name : $make_slug;
                    $model_name = $model_term ? $model_term->name : $model_slug;
                    
                    $grouped_compatibility[$make_model] = array(
                        'code' => $make_model,
                        'make' => $make_name,
                        'model' => $model_name,
                        'years' => array(),
                    );
                }
                
                // Add all years in the range
                for ($year = $start_year; $year <= $end_year; $year++) {
                    $grouped_compatibility[$make_model]['years'][] = $year;
                }
            }
        }
    }
    
    // Manual compatibility entries
    $manual_compatibility = get_post_meta($product_id, '_manual_compatibility', true);
    
    if (!empty($manual_compatibility)) {
        $entries = explode("\n", $manual_compatibility);
        
        foreach ($entries as $entry) {
            $entry = trim($entry);
            if (empty($entry)) {
                continue;
            }
            
            // Try to parse the entry to find make, model, and year
            // Format examples: "Toyota Corolla 2010-2015", "Honda Civic 2018"
            if (preg_match('/^([A-Za-z\s-]+)\s+([A-Za-z0-9\s-]+)\s+(\d{4})(?:-(\d{4}))?$/', $entry, $matches)) {
                $make_name = trim($matches[1]);
                $model_name = trim($matches[2]);
                $start_year = intval($matches[3]);
                $end_year = isset($matches[4]) ? intval($matches[4]) : $start_year;
                
                $make_slug = sanitize_title($make_name);
                $model_slug = sanitize_title($model_name);
                $make_model = $make_slug . '_' . $model_slug;
                
                if (!isset($grouped_compatibility[$make_model])) {
                    $grouped_compatibility[$make_model] = array(
                        'code' => $make_model,
                        'make' => $make_name,
                        'model' => $model_name,
                        'years' => array(),
                    );
                }
                
                // Add all years in the range
                for ($year = $start_year; $year <= $end_year; $year++) {
                    $grouped_compatibility[$make_model]['years'][] = $year;
                }
            }
        }
    }
    
    // Remove duplicates and sort years
    foreach ($grouped_compatibility as &$item) {
        $item['years'] = array_unique($item['years']);
        sort($item['years']);
    }
    
    return array_values($grouped_compatibility);
}

/**
 * Add compatibility check form to product pages
 */
function cardealership_add_compatibility_checker() {
    global $product;
    
    if (!$product) {
        return;
    }
    
    // Only show for products that have compatibility data
    $compatible_vehicles = get_post_meta($product->get_id(), '_compatible_vehicles', true);
    $manual_compatibility = get_post_meta($product->get_id(), '_manual_compatibility', true);
    
    if (empty($compatible_vehicles) && empty($manual_compatibility)) {
        return;
    }
    
    // Get all vehicle makes
    $makes = get_terms(array(
        'taxonomy' => 'vehicle_make',
        'hide_empty' => false,
    ));
    
    ?>
    <div class="compatibility-checker">
        <h3><?php _e('Check Compatibility with Your Vehicle', 'cardealership-child'); ?></h3>
        
        <form id="compatibility-check-form">
            <input type="hidden" name="product_id" value="<?php echo esc_attr($product->get_id()); ?>">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="vehicle_make_check"><?php _e('Make', 'cardealership-child'); ?> <span class="required">*</span></label>
                    <select name="make" id="vehicle_make_check" required>
                        <option value=""><?php _e('Select Make', 'cardealership-child'); ?></option>
                        <?php foreach ($makes as $make) : ?>
                            <option value="<?php echo esc_attr($make->slug); ?>"><?php echo esc_html($make->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="vehicle_model_check"><?php _e('Model', 'cardealership-child'); ?> <span class="required">*</span></label>
                    <select name="model" id="vehicle_model_check" required>
                        <option value=""><?php _e('Select Model', 'cardealership-child'); ?></option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="vehicle_year_check"><?php _e('Year', 'cardealership-child'); ?> <span class="required">*</span></label>
                    <select name="year" id="vehicle_year_check" required>
                        <option value=""><?php _e('Select Year', 'cardealership-child'); ?></option>
                        <?php
                        $current_year = date('Y');
                        for ($year = $current_year; $year >= 1990; $year--) {
                            echo '<option value="' . esc_attr($year) . '">' . esc_html($year) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                
                <div class="form-group check-button">
                    <button type="submit" class="button check-compatibility-button"><?php _e('Check Compatibility', 'cardealership-child'); ?></button>
                </div>
            </div>
        </form>
        
        <div id="compatibility-result" style="display:none;"></div>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // Get models when make is selected
        $('#vehicle_make_check').change(function() {
            var make = $(this).val();
            var modelSelect = $('#vehicle_model_check');
            
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
        
        // Handle compatibility check form submission
        $('#compatibility-check-form').submit(function(e) {
            e.preventDefault();
            
            var formData = $(this).serialize();
            
            // Add action
            formData += '&action=check_part_compatibility';
            
            // Clear previous results
            $('#compatibility-result').html('').hide();
            
            // Show loading indicator
            $('#compatibility-result').html('<p class="checking-compatibility"><?php _e('Checking compatibility...', 'cardealership-child'); ?></p>').show();
            
            // AJAX request
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        var result = response.data;
                        var resultHTML = '';
                        
                        if (result.is_compatible) {
                            resultHTML = '<div class="compatibility-match">' +
                                '<span class="compatibility-icon">✓</span>' +
                                '<p class="compatibility-message">' + result.message + '</p>' +
                                '</div>';
                        } else {
                            resultHTML = '<div class="compatibility-no-match">' +
                                '<span class="compatibility-icon">✗</span>' +
                                '<p class="compatibility-message">' + result.message + '</p>' +
                                '</div>';
                        }
                        
                        $('#compatibility-result').html(resultHTML).show();
                    } else {
                        $('#compatibility-result').html('<p class="compatibility-error">' + response.data + '</p>').show();
                    }
                },
                error: function() {
                    $('#compatibility-result').html('<p class="compatibility-error"><?php _e('Error checking compatibility. Please try again.', 'cardealership-child'); ?></p>').show();
                }
            });
        });
    });
    </script>
    
    <style>
        .compatibility-checker {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        
        .compatibility-checker h3 {
            margin-top: 0;
            margin-bottom: 15px;
        }
        
        .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .form-group {
            flex: 1;
            min-width: 150px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        .form-group select {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .form-group.check-button {
            display: flex;
            align-items: flex-end;
        }
        
        .check-compatibility-button {
            width: 100%;
            background-color: #5cb85c;
            color: white;
        }
        
        .check-compatibility-button:hover {
            background-color: #4cae4c;
            color: white;
        }
        
        #compatibility-result {
            margin-top: 20px;
            padding: 15px;
            border-radius: 4px;
        }
        
        .compatibility-match {
            background-color: #dff0d8;
            padding: 15px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .compatibility-no-match {
            background-color: #f2dede;
            padding: 15px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .compatibility-icon {
            font-size: 24px;
            font-weight: bold;
        }
        
        .compatibility-match .compatibility-icon {
            color: #5cb85c;
        }
        
        .compatibility-no-match .compatibility-icon {
            color: #d9534f;
        }
        
        .compatibility-message {
            margin: 0;
            flex: 1;
        }
        
        .compatibility-error {
            color: #d9534f;
        }
        
        .checking-compatibility {
            color: #31708f;
        }
        
        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
            }
            
            .form-group {
                width: 100%;
            }
        }
    </style>
    <?php
}
add_action('woocommerce_single_product_summary', 'cardealership_add_compatibility_checker', 25);

/**
 * Add compatibility tab to product tabs
 */
function cardealership_add_compatibility_tab($tabs) {
    global $product;
    
    if (!$product) {
        return $tabs;
    }
    
    // Only add tab for products that have compatibility data
    $compatible_vehicles = get_post_meta($product->get_id(), '_compatible_vehicles', true);
    $manual_compatibility = get_post_meta($product->get_id(), '_manual_compatibility', true);
    
    if (empty($compatible_vehicles) && empty($manual_compatibility)) {
        return $tabs;
    }
    
    $tabs['compatibility'] = array(
        'title' => __('Compatibility', 'cardealership-child'),
        'priority' => 25,
        'callback' => 'cardealership_compatibility_tab_content',
    );
    
    return $tabs;
}
add_filter('woocommerce_product_tabs', 'cardealership_add_compatibility_tab');

/**
 * Compatibility tab content
 */
function cardealership_compatibility_tab_content() {
    global $product;
    
    if (!$product) {
        return;
    }
    
    // Get compatibility data
    $compatibility = cardealership_get_product_compatibility($product->get_id());
    
    if (empty($compatibility)) {
        echo '<p>' . __('No compatibility information available for this product.', 'cardealership-child') . '</p>';
        return;
    }
    
    // Group by make
    $grouped_by_make = array();
    
    foreach ($compatibility as $item) {
        $make = $item['make'];
        
        if (!isset($grouped_by_make[$make])) {
            $grouped_by_make[$make] = array();
        }
        
        $grouped_by_make[$make][] = $item;
    }
    
    // Display compatibility table
    echo '<div class="compatibility-table-container">';
    
    foreach ($grouped_by_make as $make => $items) {
        echo '<h4>' . esc_html($make) . '</h4>';
        
        echo '<table class="compatibility-table">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>' . __('Model', 'cardealership-child') . '</th>';
        echo '<th>' . __('Compatible Years', 'cardealership-child') . '</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        foreach ($items as $item) {
            echo '<tr>';
            echo '<td>' . esc_html($item['model']) . '</td>';
            echo '<td>' . cardealership_format_year_ranges($item['years']) . '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
    }
    
    echo '</div>';
    
    // Manual compatibility notes
    $manual_compatibility = get_post_meta($product->get_id(), '_manual_compatibility', true);
    
    if (!empty($manual_compatibility)) {
        echo '<div class="manual-compatibility-notes">';
        echo '<h4>' . __('Additional Compatibility Notes', 'cardealership-child') . '</h4>';
        
        $notes = explode("\n", $manual_compatibility);
        echo '<ul>';
        
        foreach ($notes as $note) {
            if (trim($note)) {
                echo '<li>' . esc_html(trim($note)) . '</li>';
            }
        }
        
        echo '</ul>';
        echo '</div>';
    }
    
    // Add compatibility disclaimer
    echo '<div class="compatibility-disclaimer">';
    echo '<p><small>' . __('Note: Compatibility information is provided as a guide only. Please verify with your vehicle\'s documentation or contact us to confirm fitment.', 'cardealership-child') . '</small></p>';
    echo '</div>';
    
    // Style for compatibility table
    ?>
    <style>
        .compatibility-table-container {
            margin-bottom: 30px;
        }
        
        .compatibility-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .compatibility-table th,
        .compatibility-table td {
            padding: 8px 12px;
            text-align: left;
            border: 1px solid #ddd;
        }
        
        .compatibility-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        
        .compatibility-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .compatibility-table tr:hover {
            background-color: #e9ecef;
        }
        
        .manual-compatibility-notes {
            margin-bottom: 20px;
        }
        
        .compatibility-disclaimer {
            border-top: 1px solid #ddd;
            padding-top: 15px;
            color: #666;
        }
    </style>
    <?php
}

/**
 * Format years into ranges for display
 */
function cardealership_format_year_ranges($years) {
    if (empty($years)) {
        return __('All Years', 'cardealership-child');
    }
    
    // Sort years
    sort($years);
    
    $ranges = array();
    $range_start = $years[0];
    $range_end = $years[0];
    
    for ($i = 1; $i < count($years); $i++) {
        // Check if this is consecutive to previous year
        if ($years[$i] == $range_end + 1) {
            $range_end = $years[$i];
        } else {
            // Not consecutive, store range and start new one
            if ($range_start == $range_end) {
                $ranges[] = $range_start;
            } else {
                $ranges[] = $range_start . '-' . $range_end;
            }
            
            $range_start = $years[$i];
            $range_end = $years[$i];
        }
    }
    
    // Add last range
    if ($range_start == $range_end) {
        $ranges[] = $range_start;
    } else {
        $ranges[] = $range_start . '-' . $range_end;
    }
    
    return implode(', ', $ranges);
}

/**
 * Add compatibility metabox to WooCommerce products
 */
function cardealership_add_compatibility_metabox() {
    add_meta_box(
        'cardealership_compatibility_metabox',
        __('Vehicle Compatibility', 'cardealership-child'),
        'cardealership_compatibility_metabox_callback',
        'product',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'cardealership_add_compatibility_metabox');

/**
 * Compatibility metabox callback
 */
function cardealership_compatibility_metabox_callback($post) {
    wp_nonce_field('cardealership_save_compatibility', 'cardealership_compatibility_nonce');
    
    // Get existing compatibility data
    $compatible_vehicles = get_post_meta($post->ID, '_compatible_vehicles', true);
    
    if (!is_array($compatible_vehicles)) {
        $compatible_vehicles = array();
    }
    
    // Get year ranges
    $year_ranges = get_post_meta($post->ID, '_compatibility_year_ranges', true);
    
    if (!is_array($year_ranges)) {
        $year_ranges = array();
    }
    
    // Get manual compatibility
    $manual_compatibility = get_post_meta($post->ID, '_manual_compatibility', true);
    
    // Get all vehicle makes
    $makes = get_terms(array(
        'taxonomy' => 'vehicle_make',
        'hide_empty' => false,
    ));
    
    ?>
    <div class="part-compatibility-editor">
        <p><?php _e('Set which vehicles this part is compatible with:', 'cardealership-child'); ?></p>
        
        <div class="compatibility-tabs">
            <ul class="compatibility-tab-nav">
                <li class="active"><a href="#tab-vehicle-selector"><?php _e('Vehicle Selector', 'cardealership-child'); ?></a></li>
                <li><a href="#tab-year-ranges"><?php _e('Year Ranges', 'cardealership-child'); ?></a></li>
                <li><a href="#tab-manual-entry"><?php _e('Manual Entry', 'cardealership-child'); ?></a></li>
            </ul>
            
            <div class="compatibility-tab-content">
                <div id="tab-vehicle-selector" class="tab-pane active">
                    <div class="compatibility-selector">
                        <div class="selector-controls">
                            <div class="make-selector">
                                <label for="compatibility_make"><?php _e('Make:', 'cardealership-child'); ?></label>
                                <select id="compatibility_make">
                                    <option value=""><?php _e('Select Make', 'cardealership-child'); ?></option>
                                    <?php foreach ($makes as $make) : ?>
                                        <option value="<?php echo esc_attr($make->slug); ?>"><?php echo esc_html($make->name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="model-selector">
                                <label for="compatibility_model"><?php _e('Model:', 'cardealership-child'); ?></label>
                                <select id="compatibility_model">
                                    <option value=""><?php _e('Select Model', 'cardealership-child'); ?></option>
                                </select>
                            </div>
                            
                            <button type="button" class="button add-compatibility-btn"><?php _e('Add Vehicle', 'cardealership-child'); ?></button>
                        </div>
                        
                        <div class="selected-vehicles">
                            <h4><?php _e('Compatible Vehicles:', 'cardealership-child'); ?></h4>
                            
                            <div class="vehicles-list">
                                <?php if (!empty($compatible_vehicles)) : ?>
                                    <?php foreach ($compatible_vehicles as $vehicle) : 
                                        $parts = explode('_', $vehicle);
                                        if (count($parts) < 2) {
                                            continue;
                                        }
                                        
                                        $make_slug = $parts[0];
                                        $model_slug = $parts[1];
                                        
                                        $make_term = get_term_by('slug', $make_slug, 'vehicle_make');
                                        $model_term = get_term_by('slug', $model_slug, 'vehicle_model');
                                        
                                        $make_name = $make_term ? $make_term->name : $make_slug;
                                        $model_name = $model_term ? $model_term->name : $model_slug;
                                    ?>
                                        <div class="vehicle-item" data-code="<?php echo esc_attr($vehicle); ?>">
                                            <span class="vehicle-name"><?php echo esc_html($make_name . ' ' . $model_name); ?></span>
                                            <button type="button" class="remove-vehicle">×</button>
                                            <input type="hidden" name="compatible_vehicles[]" value="<?php echo esc_attr($vehicle); ?>">
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div id="tab-year-ranges" class="tab-pane">
                    <div class="year-ranges-editor">
                        <p><?php _e('Add year ranges for specific makes and models:', 'cardealership-child'); ?></p>
                        
                        <div class="year-range-controls">
                            <div class="make-model-selector">
                                <label for="year_range_make_model"><?php _e('Make & Model:', 'cardealership-child'); ?></label>
                                <select id="year_range_make_model">
                                    <option value=""><?php _e('Select Make/Model', 'cardealership-child'); ?></option>
                                    <?php foreach ($compatible_vehicles as $vehicle) : 
                                        $parts = explode('_', $vehicle);
                                        if (count($parts) < 2) {
                                            continue;
                                        }
                                        
                                        $make_slug = $parts[0];
                                        $model_slug = $parts[1];
                                        
                                        $make_term = get_term_by('slug', $make_slug, 'vehicle_make');
                                        $model_term = get_term_by('slug', $model_slug, 'vehicle_model');
                                        
                                        $make_name = $make_term ? $make_term->name : $make_slug;
                                        $model_name = $model_term ? $model_term->name : $model_slug;
                                        
                                        $make_model = $make_slug . '_' . $model_slug;
                                        $display_name = $make_name . ' ' . $model_name;
                                    ?>
                                        <option value="<?php echo esc_attr($make_model); ?>"><?php echo esc_html($display_name); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="year-range">
                                <div class="start-year">
                                    <label for="start_year"><?php _e('Start Year:', 'cardealership-child'); ?></label>
                                    <select id="start_year">
                                        <option value=""><?php _e('Select Year', 'cardealership-child'); ?></option>
                                        <?php
                                        $current_year = date('Y');
                                        for ($year = 1990; $year <= $current_year; $year++) {
                                            echo '<option value="' . esc_attr($year) . '">' . esc_html($year) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                
                                <div class="end-year">
                                    <label for="end_year"><?php _e('End Year:', 'cardealership-child'); ?></label>
                                    <select id="end_year">
                                        <option value=""><?php _e('Select Year', 'cardealership-child'); ?></option>
                                        <?php
                                        $current_year = date('Y');
                                        for ($year = 1990; $year <= $current_year; $year++) {
                                            echo '<option value="' . esc_attr($year) . '">' . esc_html($year) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            <button type="button" class="button add-year-range-btn"><?php _e('Add Year Range', 'cardealership-child'); ?></button>
                        </div>
                        
                        <div class="year-ranges-list">
                            <?php if (!empty($year_ranges)) : ?>
                                <?php foreach ($year_ranges as $index => $range) : 
                                    if (empty($range['make_model']) || empty($range['start_year']) || empty($range['end_year'])) {
                                        continue;
                                    }
                                    
                                    $make_model = $range['make_model'];
                                    $start_year = $range['start_year'];
                                    $end_year = $range['end_year'];
                                    
                                    $parts = explode('_', $make_model);
                                    if (count($parts) < 2) {
                                        continue;
                                    }
                                    
                                    $make_slug = $parts[0];
                                    $model_slug = $parts[1];
                                    
                                    $make_term = get_term_by('slug', $make_slug, 'vehicle_make');
                                    $model_term = get_term_by('slug', $model_slug, 'vehicle_model');
                                    
                                    $make_name = $make_term ? $make_term->name : $make_slug;
                                    $model_name = $model_term ? $model_term->name : $model_slug;
                                ?>
                                    <div class="year-range-item">
                                        <span class="range-display"><?php echo esc_html($make_name . ' ' . $model_name . ': ' . $start_year . '-' . $end_year); ?></span>
                                        <button type="button" class="remove-year-range">×</button>
                                        <input type="hidden" name="compatibility_year_ranges[<?php echo $index; ?>][make_model]" value="<?php echo esc_attr($make_model); ?>">
                                        <input type="hidden" name="compatibility_year_ranges[<?php echo $index; ?>][start_year]" value="<?php echo esc_attr($start_year); ?>">
                                        <input type="hidden" name="compatibility_year_ranges[<?php echo $index; ?>][end_year]" value="<?php echo esc_attr($end_year); ?>">
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div id="tab-manual-entry" class="tab-pane">
                    <div class="manual-compatibility">
                        <p><?php _e('Enter additional compatibility information (one per line):', 'cardealership-child'); ?></p>
                        <p class="description"><?php _e('Format examples: "Toyota Corolla 2010-2015", "Honda Civic 2018"', 'cardealership-child'); ?></p>
                        
                        <textarea name="manual_compatibility" id="manual_compatibility" rows="10" style="width: 100%;"><?php echo esc_textarea($manual_compatibility); ?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // Tab navigation
        $('.compatibility-tab-nav a').click(function(e) {
            e.preventDefault();
            
            // Set active tab nav
            $('.compatibility-tab-nav li').removeClass('active');
            $(this).parent().addClass('active');
            
            // Show active tab content
            var target = $(this).attr('href');
            $('.tab-pane').removeClass('active');
            $(target).addClass('active');
        });
        
        // Get models when make is selected
        $('#compatibility_make').change(function() {
            var make = $(this).val();
            var modelSelect = $('#compatibility_model');
            
            // Clear current options
            modelSelect.html('<option value=""><?php echo esc_js(__('Select Model', 'cardealership-child')); ?></option>');
            
            if (make) {
                // AJAX request to get models
                $.ajax({
                    url: ajaxurl,
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
        
        // Add vehicle to compatibility list
        $('.add-compatibility-btn').click(function() {
            var make = $('#compatibility_make').val();
            var model = $('#compatibility_model').val();
            var makeName = $('#compatibility_make option:selected').text();
            var modelName = $('#compatibility_model option:selected').text();
            
            if (!make || !model) {
                alert('<?php echo esc_js(__('Please select both make and model.', 'cardealership-child')); ?>');
                return;
            }
            
            var vehicleCode = make + '_' + model;
            
            // Check if already added
            if ($('.vehicle-item[data-code="' + vehicleCode + '"]').length > 0) {
                alert('<?php echo esc_js(__('This vehicle is already in the compatibility list.', 'cardealership-child')); ?>');
                return;
            }
            
            // Add to list
            var vehicleItem = '<div class="vehicle-item" data-code="' + vehicleCode + '">' +
                '<span class="vehicle-name">' + makeName + ' ' + modelName + '</span>' +
                '<button type="button" class="remove-vehicle">×</button>' +
                '<input type="hidden" name="compatible_vehicles[]" value="' + vehicleCode + '">' +
                '</div>';
            
            $('.vehicles-list').append(vehicleItem);
            
            // Clear selection
            $('#compatibility_make').val('');
            $('#compatibility_model').html('<option value=""><?php echo esc_js(__('Select Model', 'cardealership-child')); ?></option>');
            
            // Update year range make/model select
            updateYearRangeMakeModel();
        });
        
        // Remove vehicle from compatibility list
        $(document).on('click', '.remove-vehicle', function() {
            $(this).closest('.vehicle-item').remove();
            
            // Update year range make/model select
            updateYearRangeMakeModel();
        });
        
        // Update year range make/model select options
        function updateYearRangeMakeModel() {
            var yearRangeSelect = $('#year_range_make_model');
            
            // Clear current options
            yearRangeSelect.html('<option value=""><?php echo esc_js(__('Select Make/Model', 'cardealership-child')); ?></option>');
            
            // Add options for each vehicle in the list
            $('.vehicle-item').each(function() {
                var code = $(this).data('code');
                var name = $(this).find('.vehicle-name').text();
                
                yearRangeSelect.append('<option value="' + code + '">' + name + '</option>');
            });
        }
        
        // Add year range
        $('.add-year-range-btn').click(function() {
            var makeModel = $('#year_range_make_model').val();
            var makeModelText = $('#year_range_make_model option:selected').text();
            var startYear = $('#start_year').val();
            var endYear = $('#end_year').val();
            
            if (!makeModel || !startYear || !endYear) {
                alert('<?php echo esc_js(__('Please select make/model and year range.', 'cardealership-child')); ?>');
                return;
            }
            
            // Validate years
            if (parseInt(startYear) > parseInt(endYear)) {
                alert('<?php echo esc_js(__('Start year cannot be greater than end year.', 'cardealership-child')); ?>');
                return;
            }
            
            // Generate unique index
            var index = $('.year-range-item').length;
            
            // Add to list
            var rangeItem = '<div class="year-range-item">' +
                '<span class="range-display">' + makeModelText + ': ' + startYear + '-' + endYear + '</span>' +
                '<button type="button" class="remove-year-range">×</button>' +
                '<input type="hidden" name="compatibility_year_ranges[' + index + '][make_model]" value="' + makeModel + '">' +
                '<input type="hidden" name="compatibility_year_ranges[' + index + '][start_year]" value="' + startYear + '">' +
                '<input type="hidden" name="compatibility_year_ranges[' + index + '][end_year]" value="' + endYear + '">' +
                '</div>';
            
            $('.year-ranges-list').append(rangeItem);
            
            // Clear selection
            $('#year_range_make_model').val('');
            $('#start_year').val('');
            $('#end_year').val('');
        });
        
        // Remove year range
        $(document).on('click', '.remove-year-range', function() {
            $(this).closest('.year-range-item').remove();
        });
        
        // Initialize
        updateYearRangeMakeModel();
    });
    </script>
    
    <style>
        .part-compatibility-editor {
            margin-top: 10px;
        }
        
        .compatibility-tabs {
            margin-top: 15px;
        }
        
        .compatibility-tab-nav {
            display: flex;
            margin: 0;
            padding: 0;
            list-style: none;
            border-bottom: 1px solid #ddd;
        }
        
        .compatibility-tab-nav li {
            margin: 0 5px 0 0;
        }
        
        .compatibility-tab-nav a {
            display: block;
            padding: 8px 12px;
            text-decoration: none;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-bottom: none;
            border-radius: 3px 3px 0 0;
            color: #666;
        }
        
        .compatibility-tab-nav li.active a {
            background-color: #fff;
            border-bottom: 1px solid #fff;
            margin-bottom: -1px;
            color: #333;
        }
        
        .tab-pane {
            display: none;
            padding: 15px;
            border: 1px solid #ddd;
            border-top: none;
        }
        
        .tab-pane.active {
            display: block;
        }
        
        .selector-controls {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 15px;
            align-items: flex-end;
        }
        
        .make-selector, .model-selector, .make-model-selector, .start-year, .end-year {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .vehicles-list {
            margin-top: 10px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .vehicle-item {
            display: flex;
            align-items: center;
            background-color: #f8f9fa;
            padding: 5px 10px;
            border-radius: 3px;
            gap: 5px;
        }
        
        .remove-vehicle, .remove-year-range {
            background: none;
            border: none;
            color: #d9534f;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            padding: 0;
            line-height: 1;
        }
        
        .year-range-controls {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 15px;
            align-items: flex-end;
        }
        
        .year-range {
            display: flex;
            gap: 10px;
        }
        
        .year-ranges-list {
            margin-top: 10px;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .year-range-item {
            display: flex;
            align-items: center;
            background-color: #f8f9fa;
            padding: 5px 10px;
            border-radius: 3px;
            gap: 10px;
        }
        
        .range-display {
            flex: 1;
        }
    </style>
    <?php
}

/**
 * Save compatibility metabox data
 */
function cardealership_save_compatibility($post_id) {
    // Check if our nonce is set
    if (!isset($_POST['cardealership_compatibility_nonce'])) {
        return;
    }
    
    // Verify the nonce
    if (!wp_verify_nonce($_POST['cardealership_compatibility_nonce'], 'cardealership_save_compatibility')) {
        return;
    }
    
    // If this is an autosave, don't do anything
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Check user permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Save compatible vehicles
    if (isset($_POST['compatible_vehicles'])) {
        $compatible_vehicles = array_map('sanitize_text_field', $_POST['compatible_vehicles']);
        update_post_meta($post_id, '_compatible_vehicles', $compatible_vehicles);
    } else {
        delete_post_meta($post_id, '_compatible_vehicles');
    }
    
    // Save year ranges
    if (isset($_POST['compatibility_year_ranges'])) {
        $year_ranges = array();
        
        foreach ($_POST['compatibility_year_ranges'] as $range) {
            if (isset($range['make_model'], $range['start_year'], $range['end_year'])) {
                $year_ranges[] = array(
                    'make_model' => sanitize_text_field($range['make_model']),
                    'start_year' => intval($range['start_year']),
                    'end_year' => intval($range['end_year']),
                );
            }
        }
        
        update_post_meta($post_id, '_compatibility_year_ranges', $year_ranges);
    } else {
        delete_post_meta($post_id, '_compatibility_year_ranges');
    }
    
    // Save manual compatibility
    if (isset($_POST['manual_compatibility'])) {
        update_post_meta($post_id, '_manual_compatibility', sanitize_textarea_field($_POST['manual_compatibility']));
    } else {
        delete_post_meta($post_id, '_manual_compatibility');
    }
}
add_action('save_post', 'cardealership_save_compatibility');

/**
 * Add compatibility filter to shop sidebar
 */
function cardealership_add_compatibility_filter_widget() {
    register_widget('Cardealership_Compatibility_Filter_Widget');
}
add_action('widgets_init', 'cardealership_add_compatibility_filter_widget');

/**
 * Compatibility Filter Widget
 */
class Cardealership_Compatibility_Filter_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'cardealership_compatibility_filter',
            __('Parts Compatibility Filter', 'cardealership-child'),
            array('description' => __('Filter parts by vehicle compatibility', 'cardealership-child'))
        );
    }
    
    public function widget($args, $instance) {
        $title = apply_filters('widget_title', $instance['title']);
        
        // Only show on product archive pages
        if (!is_shop() && !is_product_category() && !is_product_tag()) {
            return;
        }
        
        echo $args['before_widget'];
        
        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        
        // Get all vehicle makes
        $makes = get_terms(array(
            'taxonomy' => 'vehicle_make',
            'hide_empty' => false,
        ));
        
        // Get current filters
        $current_make = isset($_GET['vehicle_make']) ? $_GET['vehicle_make'] : '';
        $current_model = isset($_GET['vehicle_model']) ? $_GET['vehicle_model'] : '';
        $current_year = isset($_GET['vehicle_year']) ? $_GET['vehicle_year'] : '';
        ?>
        
        <div class="compatibility-filter">
            <form method="get" action="<?php echo esc_url(wc_get_page_permalink('shop')); ?>">
                <?php
                // Keep existing query parameters
                foreach ($_GET as $key => $value) {
                    if (!in_array($key, array('vehicle_make', 'vehicle_model', 'vehicle_year'))) {
                        echo '<input type="hidden" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '">';
                    }
                }
                ?>
                
                <div class="filter-field">
                    <label for="filter_vehicle_make"><?php _e('Vehicle Make', 'cardealership-child'); ?></label>
                    <select name="vehicle_make" id="filter_vehicle_make">
                        <option value=""><?php _e('Any Make', 'cardealership-child'); ?></option>
                        <?php foreach ($makes as $make) : ?>
                            <option value="<?php echo esc_attr($make->slug); ?>" <?php selected($current_make, $make->slug); ?>><?php echo esc_html($make->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-field">
                    <label for="filter_vehicle_model"><?php _e('Vehicle Model', 'cardealership-child'); ?></label>
                    <select name="vehicle_model" id="filter_vehicle_model">
                        <option value=""><?php _e('Any Model', 'cardealership-child'); ?></option>
                        <?php
                        // If make is selected, get models for that make
                        if (!empty($current_make)) {
                            $make_term = get_term_by('slug', $current_make, 'vehicle_make');
                            
                            if ($make_term) {
                                $models = get_terms(array(
                                    'taxonomy' => 'vehicle_model',
                                    'hide_empty' => false,
                                    'meta_query' => array(
                                        array(
                                            'key' => 'vehicle_make_id',
                                            'value' => $make_term->term_id,
                                            'compare' => '=',
                                        ),
                                    ),
                                ));
                                
                                if (!empty($models) && !is_wp_error($models)) {
                                    foreach ($models as $model) {
                                        echo '<option value="' . esc_attr($model->slug) . '" ' . selected($current_model, $model->slug, false) . '>' . esc_html($model->name) . '</option>';
                                    }
                                }
                            }
                        }
                        ?>
                    </select>
                </div>
                
                <div class="filter-field">
                    <label for="filter_vehicle_year"><?php _e('Vehicle Year', 'cardealership-child'); ?></label>
                    <select name="vehicle_year" id="filter_vehicle_year">
                        <option value=""><?php _e('Any Year', 'cardealership-child'); ?></option>
                        <?php
                        $current_year = date('Y');
                        for ($year = $current_year; $year >= 1990; $year--) {
                            echo '<option value="' . esc_attr($year) . '" ' . selected($current_year, $year, false) . '>' . esc_html($year) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                
                <div class="filter-actions">
                    <button type="submit" class="button"><?php _e('Filter Parts', 'cardealership-child'); ?></button>
                    
                    <?php if (!empty($current_make) || !empty($current_model) || !empty($current_year)) : ?>
                        <a href="<?php echo esc_url(remove_query_arg(array('vehicle_make', 'vehicle_model', 'vehicle_year'))); ?>" class="reset-filter"><?php _e('Clear Filter', 'cardealership-child'); ?></a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            // Get models when make is selected
            $('#filter_vehicle_make').change(function() {
                var make = $(this).val();
                var modelSelect = $('#filter_vehicle_model');
                
                // Clear current options
                modelSelect.html('<option value=""><?php echo esc_js(__('Any Model', 'cardealership-child')); ?></option>');
                
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
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : __('Filter by Vehicle', 'cardealership-child');
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
 * Filter products by vehicle compatibility
 */
function cardealership_filter_products_by_compatibility($query) {
    if (!is_admin() && $query->is_main_query() && (is_shop() || is_product_category() || is_product_tag())) {
        $make = isset($_GET['vehicle_make']) ? sanitize_text_field($_GET['vehicle_make']) : '';
        $model = isset($_GET['vehicle_model']) ? sanitize_text_field($_GET['vehicle_model']) : '';
        $year = isset($_GET['vehicle_year']) ? intval($_GET['vehicle_year']) : 0;
        
        if (!empty($make) || !empty($model) || !empty($year)) {
            // Build compatibility codes to search for
            $codes_to_match = array();
            
            if (!empty($make) && !empty($model)) {
                $codes_to_match[] = $make . '_' . $model;
            }
            
            // Get products with matching compatibility
            $compatible_products = array();
            
            if (!empty($codes_to_match)) {
                // Get products with direct compatibility
                $products = get_posts(array(
                    'post_type' => 'product',
                    'posts_per_page' => -1,
                    'fields' => 'ids',
                    'meta_query' => array(
                        array(
                            'key' => '_compatible_vehicles',
                            'value' => serialize($codes_to_match[0]),
                            'compare' => 'LIKE',
                        ),
                    ),
                ));
                
                if (!empty($products)) {
                    $compatible_products = array_merge($compatible_products, $products);
                }
                
                // Also check year ranges for specific year filter
                if (!empty($year)) {
                    $year_compatible_products = array();
                    
                    foreach ($products as $product_id) {
                        $year_ranges = get_post_meta($product_id, '_compatibility_year_ranges', true);
                        
                        if (!empty($year_ranges) && is_array($year_ranges)) {
                            foreach ($year_ranges as $range) {
                                if (isset($range['make_model'], $range['start_year'], $range['end_year'])) {
                                    $make_model = $range['make_model'];
                                    $start_year = intval($range['start_year']);
                                    $end_year = intval($range['end_year']);
                                    
                                    if (in_array($make_model, $codes_to_match) && $year >= $start_year && $year <= $end_year) {
                                        $year_compatible_products[] = $product_id;
                                        break;
                                    }
                                }
                            }
                        }
                    }
                    
                    if (!empty($year_compatible_products)) {
                        $compatible_products = $year_compatible_products;
                    } else {
                        // No products match the year filter
                        $compatible_products = array(0); // Force no results
                    }
                }
                
                // Filter query to include only compatible products
                if (!empty($compatible_products)) {
                    $query->set('post__in', $compatible_products);
                } else {
                    // No compatible products found
                    $query->set('post__in', array(0)); // Force no results
                }
            }
        }
    }
    
    return $query;
}
add_action('pre_get_posts', 'cardealership_filter_products_by_compatibility');

/**
 * Add "Compatible with Your Vehicle" badge to product in shop
 */
function cardealership_compatible_product_badge() {
    global $product;
    
    if (!$product) {
        return;
    }
    
    $make = isset($_GET['vehicle_make']) ? sanitize_text_field($_GET['vehicle_make']) : '';
    $model = isset($_GET['vehicle_model']) ? sanitize_text_field($_GET['vehicle_model']) : '';
    $year = isset($_GET['vehicle_year']) ? intval($_GET['vehicle_year']) : 0;
    
    // Only show badge if vehicle filter is active
    if (empty($make) || empty($model)) {
        return;
    }
    
    $make_term = get_term_by('slug', $make, 'vehicle_make');
    $model_term = get_term_by('slug', $model, 'vehicle_model');
    
    if (!$make_term || !$model_term) {
        return;
    }
    
    $make_name = $make_term->name;
    $model_name = $model_term->name;
    
    if (!empty($year)) {
        $badge_text = sprintf(__('Compatible with %s %s %s', 'cardealership-child'), $year, $make_name, $model_name);
    } else {
        $badge_text = sprintf(__('Compatible with %s %s', 'cardealership-child'), $make_name, $model_name);
    }
    
    echo '<span class="compatible-badge">' . esc_html($badge_text) . '</span>';
}
add_action('woocommerce_before_shop_loop_item_title', 'cardealership_compatible_product_badge', 15);

/**
 * Add CSS for compatibility badges
 */
function cardealership_compatibility_badge_css() {
    if (!is_shop() && !is_product_category() && !is_product_tag()) {
        return;
    }
    
    ?>
    <style>
        .compatible-badge {
            position: absolute;
            top: 10px;
            left: 0;
            background-color: #5cb85c;
            color: white;
            padding: 5px 10px;
            font-size: 12px;
            font-weight: bold;
            z-index: 9;
        }
        
        .compatibility-filter {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .filter-field {
            margin-bottom: 15px;
        }
        
        .filter-field label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            font-size: 14px;
        }
        
        .filter-field select {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .filter-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }
        
        .reset-filter {
            color: #666;
            text-decoration: underline;
            font-size: 14px;
        }
    </style>
    <?php
}
add_action('wp_head', 'cardealership_compatibility_badge_css');
