<?php
/**
 * Vehicle Comparison Feature
 * Add this code to your functions.php or include it as a separate file
 */

/**
 * Register comparison session and functions
 */
function cardealership_init_comparison() {
    if (!isset($_SESSION)) {
        session_start();
    }
    
    // Initialize comparison array if it doesn't exist
    if (!isset($_SESSION['vehicle_comparison'])) {
        $_SESSION['vehicle_comparison'] = array();
    }
}
add_action('init', 'cardealership_init_comparison');

/**
 * Add AJAX handler for adding a vehicle to comparison
 */
function cardealership_add_to_comparison_ajax() {
    if (!isset($_POST['vehicle_id']) || !isset($_POST['nonce'])) {
        wp_send_json_error(__('Invalid request', 'cardealership-child'));
    }
    
    if (!wp_verify_nonce($_POST['nonce'], 'vehicle_comparison_nonce')) {
        wp_send_json_error(__('Security check failed', 'cardealership-child'));
    }
    
    $vehicle_id = intval($_POST['vehicle_id']);
    
    // Check if vehicle exists
    $vehicle = get_post($vehicle_id);
    if (!$vehicle || $vehicle->post_type !== 'vehicle') {
        wp_send_json_error(__('Vehicle not found', 'cardealership-child'));
    }
    
    // Initialize session if needed
    if (!isset($_SESSION)) {
        session_start();
    }
    
    // Initialize comparison array if it doesn't exist
    if (!isset($_SESSION['vehicle_comparison'])) {
        $_SESSION['vehicle_comparison'] = array();
    }
    
    // Check if vehicle is already in comparison
    if (in_array($vehicle_id, $_SESSION['vehicle_comparison'])) {
        wp_send_json_success(array(
            'message' => __('Vehicle is already in comparison', 'cardealership-child'),
            'count' => count($_SESSION['vehicle_comparison']),
            'already_added' => true,
        ));
        exit;
    }
    
    // Check if comparison is full (max 3 vehicles)
    if (count($_SESSION['vehicle_comparison']) >= 3) {
        wp_send_json_error(array(
            'message' => __('You can compare up to 3 vehicles. Please remove a vehicle before adding a new one.', 'cardealership-child'),
            'count' => count($_SESSION['vehicle_comparison']),
            'full' => true,
        ));
        exit;
    }
    
    // Add vehicle to comparison
    $_SESSION['vehicle_comparison'][] = $vehicle_id;
    
    // Return success
    wp_send_json_success(array(
        'message' => __('Vehicle added to comparison', 'cardealership-child'),
        'count' => count($_SESSION['vehicle_comparison']),
    ));
}
add_action('wp_ajax_add_to_comparison', 'cardealership_add_to_comparison_ajax');
add_action('wp_ajax_nopriv_add_to_comparison', 'cardealership_add_to_comparison_ajax');

/**
 * Add AJAX handler for removing a vehicle from comparison
 */
function cardealership_remove_from_comparison_ajax() {
    if (!isset($_POST['vehicle_id']) || !isset($_POST['nonce'])) {
        wp_send_json_error(__('Invalid request', 'cardealership-child'));
    }
    
    if (!wp_verify_nonce($_POST['nonce'], 'vehicle_comparison_nonce')) {
        wp_send_json_error(__('Security check failed', 'cardealership-child'));
    }
    
    $vehicle_id = intval($_POST['vehicle_id']);
    
    // Initialize session if needed
    if (!isset($_SESSION)) {
        session_start();
    }
    
    // Check if comparison array exists
    if (!isset($_SESSION['vehicle_comparison'])) {
        wp_send_json_error(__('No vehicles in comparison', 'cardealership-child'));
    }
    
    // Find and remove vehicle from comparison
    $key = array_search($vehicle_id, $_SESSION['vehicle_comparison']);
    if ($key !== false) {
        unset($_SESSION['vehicle_comparison'][$key]);
        // Reindex array
        $_SESSION['vehicle_comparison'] = array_values($_SESSION['vehicle_comparison']);
        
        wp_send_json_success(array(
            'message' => __('Vehicle removed from comparison', 'cardealership-child'),
            'count' => count($_SESSION['vehicle_comparison']),
        ));
    } else {
        wp_send_json_error(__('Vehicle not found in comparison', 'cardealership-child'));
    }
}
add_action('wp_ajax_remove_from_comparison', 'cardealership_remove_from_comparison_ajax');
add_action('wp_ajax_nopriv_remove_from_comparison', 'cardealership_remove_from_comparison_ajax');

/**
 * Clear all vehicles from comparison
 */
function cardealership_clear_comparison_ajax() {
    if (!isset($_POST['nonce'])) {
        wp_send_json_error(__('Invalid request', 'cardealership-child'));
    }
    
    if (!wp_verify_nonce($_POST['nonce'], 'vehicle_comparison_nonce')) {
        wp_send_json_error(__('Security check failed', 'cardealership-child'));
    }
    
    // Initialize session if needed
    if (!isset($_SESSION)) {
        session_start();
    }
    
    // Clear comparison array
    $_SESSION['vehicle_comparison'] = array();
    
    wp_send_json_success(array(
        'message' => __('Comparison cleared', 'cardealership-child'),
        'count' => 0,
    ));
}
add_action('wp_ajax_clear_comparison', 'cardealership_clear_comparison_ajax');
add_action('wp_ajax_nopriv_clear_comparison', 'cardealership_clear_comparison_ajax');

/**
 * Register custom comparison page template
 */
function cardealership_comparison_page_template($templates) {
    $templates['comparison-page.php'] = __('Vehicle Comparison', 'cardealership-child');
    return $templates;
}
add_filter('theme_page_templates', 'cardealership_comparison_page_template');

/**
 * Add "Add to Comparison" button to vehicle listings
 */
function cardealership_add_comparison_button() {
    global $post;
    
    // Only add to vehicle post type
    if ($post->post_type !== 'vehicle') {
        return;
    }
    
    // Generate nonce
    $nonce = wp_create_nonce('vehicle_comparison_nonce');
    
    // Check if vehicle is already in comparison
    $in_comparison = false;
    if (isset($_SESSION['vehicle_comparison']) && in_array($post->ID, $_SESSION['vehicle_comparison'])) {
        $in_comparison = true;
    }
    
    ?>
    <div class="comparison-button-container">
        <button type="button" class="button add-to-comparison <?php echo $in_comparison ? 'in-comparison' : ''; ?>" data-vehicle-id="<?php echo $post->ID; ?>" data-nonce="<?php echo $nonce; ?>">
            <span class="add-text"><?php _e('Add to Compare', 'cardealership-child'); ?></span>
            <span class="added-text"><?php _e('In Comparison', 'cardealership-child'); ?></span>
        </button>
    </div>
    <?php
}
add_action('cardealership_after_vehicle_title', 'cardealership_add_comparison_button');

/**
 * Add comparison notification bar
 */
function cardealership_comparison_notification_bar() {
    // Only show on vehicle archive and single pages
    if (!is_post_type_archive('vehicle') && !is_singular('vehicle') && !is_tax('vehicle_make') && !is_tax('vehicle_model') && !is_tax('vehicle_type')) {
        return;
    }
    
    // Check if we have vehicles in comparison
    $comparison_count = isset($_SESSION['vehicle_comparison']) ? count($_SESSION['vehicle_comparison']) : 0;
    
    // Only show if we have vehicles
    if ($comparison_count === 0) {
        return;
    }
    
    // Get comparison page URL
    $comparison_page_id = cardealership_get_comparison_page_id();
    $comparison_url = $comparison_page_id ? get_permalink($comparison_page_id) : '#';
    
    // Generate nonce
    $nonce = wp_create_nonce('vehicle_comparison_nonce');
    
    ?>
    <div class="comparison-notification-bar" style="<?php echo $comparison_count === 0 ? 'display: none;' : ''; ?>">
        <div class="container">
            <div class="notification-content">
                <span class="notification-text">
                    <?php 
                    if ($comparison_count === 1) {
                        _e('You have 1 vehicle in comparison', 'cardealership-child');
                    } else {
                        printf(__('You have %s vehicles in comparison', 'cardealership-child'), $comparison_count);
                    }
                    ?>
                </span>
                <div class="notification-actions">
                    <a href="<?php echo esc_url($comparison_url); ?>" class="button view-comparison"><?php _e('Compare Now', 'cardealership-child'); ?></a>
                    <button type="button" class="clear-comparison" data-nonce="<?php echo $nonce; ?>"><?php _e('Clear All', 'cardealership-child'); ?></button>
                </div>
            </div>
        </div>
    </div>
    <?php
}
add_action('wp_footer', 'cardealership_comparison_notification_bar');

/**
 * Add comparison styles and scripts
 */
function cardealership_comparison_scripts() {
    // Only load on vehicle pages
    if (!is_post_type_archive('vehicle') && !is_singular('vehicle') && !is_page_template('comparison-page.php') && !is_tax('vehicle_make') && !is_tax('vehicle_model') && !is_tax('vehicle_type')) {
        return;
    }
    
    // Add styles
    ?>
    <style>
        /* Comparison Button Styles */
        .comparison-button-container {
            margin-top: 10px;
            margin-bottom: 15px;
        }
        
        .add-to-comparison {
            background-color: #f8f9fa;
            color: #333;
            border: 1px solid #ddd;
            display: inline-flex;
            align-items: center;
            transition: all 0.3s ease;
            padding: 8px 15px;
        }
        
        .add-to-comparison:hover {
            background-color: #e9ecef;
        }
        
        .add-to-comparison.in-comparison {
            background-color: #d1e7dd;
            border-color: #badbcc;
            color: #0f5132;
        }
        
        .add-to-comparison .added-text {
            display: none;
        }
        
        .add-to-comparison.in-comparison .add-text {
            display: none;
        }
        
        .add-to-comparison.in-comparison .added-text {
            display: inline;
        }
        
        /* Notification Bar */
        .comparison-notification-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: #343a40;
            color: white;
            padding: 15px 0;
            z-index: 1000;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
        }
        
        .notification-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .notification-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .view-comparison {
            background-color: #d9534f;
            color: white;
            border: none;
        }
        
        .view-comparison:hover {
            background-color: #c9302c;
            color: white;
        }
        
        .clear-comparison {
            background: none;
            border: none;
            color: #adb5bd;
            cursor: pointer;
            text-decoration: underline;
            padding: 5px;
        }
        
        .clear-comparison:hover {
            color: white;
        }
        
        /* Comparison Page Styles */
        .comparison-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .comparison-title {
            margin: 0;
        }
        
        .comparison-actions .button {
            background-color: #6c757d;
            color: white;
        }
        
        .comparison-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .comparison-table th,
        .comparison-table td {
            padding: 15px;
            text-align: left;
            border: 1px solid #dee2e6;
        }
        
        .comparison-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            vertical-align: middle;
        }
        
        .comparison-table th:first-child {
            width: 20%;
        }
        
        .comparison-table th:not(:first-child) {
            width: 26.66%;
        }
        
        .comparison-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .comparison-table td.spec-highlight {
            font-weight: bold;
            color: #d9534f;
        }
        
        .comparison-table .vehicle-image {
            height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .comparison-table .vehicle-image img {
            max-width: 100%;
            max-height: 160px;
            object-fit: contain;
        }
        
        .comparison-table .vehicle-title {
            font-size: 18px;
            font-weight: 600;
            margin: 10px 0;
        }
        
        .comparison-table .vehicle-price {
            font-size: 20px;
            font-weight: bold;
            color: #d9534f;
            margin-bottom: 10px;
        }
        
        .comparison-table .vehicle-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        
        .empty-comparison {
            text-align: center;
            padding: 40px;
            background-color: #f8f9fa;
            border-radius: 6px;
            margin-bottom: 30px;
        }
        
        .empty-comparison h3 {
            margin-top: 0;
        }
        
        .empty-comparison .button {
            margin-top: 20px;
        }
        
        @media (max-width: 768px) {
            .comparison-table {
                display: block;
                overflow-x: auto;
            }
            
            .comparison-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .notification-content {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        // Add to comparison
        $('.add-to-comparison').click(function() {
            var button = $(this);
            var vehicleId = button.data('vehicle-id');
            var nonce = button.data('nonce');
            
            if (button.hasClass('in-comparison')) {
                // Remove from comparison
                $.ajax({
                    type: 'POST',
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    data: {
                        action: 'remove_from_comparison',
                        vehicle_id: vehicleId,
                        nonce: nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            button.removeClass('in-comparison');
                            
                            // Update notification bar
                            updateNotificationBar(response.data.count);
                        } else {
                            alert(response.data);
                        }
                    }
                });
            } else {
                // Add to comparison
                $.ajax({
                    type: 'POST',
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    data: {
                        action: 'add_to_comparison',
                        vehicle_id: vehicleId,
                        nonce: nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            button.addClass('in-comparison');
                            
                            // Update notification bar
                            updateNotificationBar(response.data.count);
                        } else {
                            if (response.data.full) {
                                alert(response.data.message);
                            } else {
                                alert(response.data);
                            }
                        }
                    }
                });
            }
        });
        
        // Clear comparison
        $('.clear-comparison').click(function() {
            var nonce = $(this).data('nonce');
            
            if (confirm('<?php _e("Are you sure you want to clear all vehicles from comparison?", "cardealership-child"); ?>')) {
                $.ajax({
                    type: 'POST',
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    data: {
                        action: 'clear_comparison',
                        nonce: nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update button states
                            $('.add-to-comparison').removeClass('in-comparison');
                            
                            // Update notification bar
                            updateNotificationBar(0);
                            
                            // Reload if on comparison page
                            if ($('.comparison-page').length > 0) {
                                location.reload();
                            }
                        } else {
                            alert(response.data);
                        }
                    }
                });
            }
        });
        
        // Remove from comparison on comparison page
        $('.remove-from-comparison').click(function() {
            var button = $(this);
            var vehicleId = button.data('vehicle-id');
            var nonce = button.data('nonce');
            
            $.ajax({
                type: 'POST',
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                data: {
                    action: 'remove_from_comparison',
                    vehicle_id: vehicleId,
                    nonce: nonce
                },
                success: function(response) {
                    if (response.success) {
                        // Reload the page to update comparison table
                        location.reload();
                    } else {
                        alert(response.data);
                    }
                }
            });
        });
        
        // Function to update notification bar
        function updateNotificationBar(count) {
            if (count > 0) {
                $('.comparison-notification-bar').show();
                
                // Update text
                if (count === 1) {
                    $('.notification-text').text('<?php _e("You have 1 vehicle in comparison", "cardealership-child"); ?>');
                } else {
                    $('.notification-text').text('<?php _e("You have", "cardealership-child"); ?> ' + count + ' <?php _e("vehicles in comparison", "cardealership-child"); ?>');
                }
            } else {
                $('.comparison-notification-bar').hide();
            }
        }
    });
    </script>
    <?php
}
add_action('wp_head', 'cardealership_comparison_scripts');

/**
 * Get the comparison page ID
 */
function cardealership_get_comparison_page_id() {
    // Find page with comparison template
    $pages = get_pages(array(
        'meta_key' => '_wp_page_template',
        'meta_value' => 'comparison-page.php'
    ));
    
    if (!empty($pages)) {
        return $pages[0]->ID;
    }
    
    return false;
}

/**
 * Load comparison page template
 */
function cardealership_load_comparison_template($template) {
    if (is_page_template('comparison-page.php')) {
        $template = get_stylesheet_directory() . '/templates/comparison-page.php';
        
        // Create template file if it doesn't exist
        if (!file_exists($template)) {
            // Create directory if needed
            $template_dir = get_stylesheet_directory() . '/templates';
            if (!file_exists($template_dir)) {
                mkdir($template_dir, 0755, true);
            }
            
            // Create template file
            $template_content = '<?php
/**
 * Template Name: Vehicle Comparison
 */

get_header();
?>

<div class="comparison-page container">
    <div class="comparison-header">
        <h1 class="comparison-title"><?php _e("Vehicle Comparison", "cardealership-child"); ?></h1>
        
        <div class="comparison-actions">
            <a href="<?php echo get_post_type_archive_link(\'vehicle\'); ?>" class="button return-to-inventory"><?php _e("Return to Inventory", "cardealership-child"); ?></a>
        </div>
    </div>
    
    <?php
    // Get vehicles in comparison
    $vehicle_ids = isset($_SESSION[\'vehicle_comparison\']) ? $_SESSION[\'vehicle_comparison\'] : array();
    
    if (empty($vehicle_ids)) {
        // No vehicles in comparison
        ?>
        <div class="empty-comparison">
            <h3><?php _e("No Vehicles in Comparison", "cardealership-child"); ?></h3>
            <p><?php _e("You haven\'t added any vehicles to compare yet. Browse our inventory and select vehicles to compare.", "cardealership-child"); ?></p>
            
            <a href="<?php echo get_post_type_archive_link(\'vehicle\'); ?>" class="button"><?php _e("Browse Inventory", "cardealership-child"); ?></a>
        </div>
        <?php
    } else {
        // Get vehicle data
        $vehicles = array();
        
        foreach ($vehicle_ids as $id) {
            $vehicle = get_post($id);
            
            if ($vehicle && $vehicle->post_type === \'vehicle\') {
                // Get vehicle details
                $make_terms = wp_get_post_terms($id, \'vehicle_make\');
                $model_terms = wp_get_post_terms($id, \'vehicle_model\');
                $type_terms = wp_get_post_terms($id, \'vehicle_type\');
                
                $make = !empty($make_terms) ? $make_terms[0]->name : \'\';
                $model = !empty($model_terms) ? $model_terms[0]->name : \'\';
                $type = !empty($type_terms) ? $type_terms[0]->name : \'\';
                
                $year = get_post_meta($id, \'_vehicle_year\', true);
                $price = get_post_meta($id, \'_vehicle_price\', true);
                $mileage = get_post_meta($id, \'_vehicle_mileage\', true);
                $engine = get_post_meta($id, \'_vehicle_engine\', true);
                $transmission = get_post_meta($id, \'_vehicle_transmission\', true);
                $color = get_post_meta($id, \'_vehicle_color\', true);
                $vin = get_post_meta($id, \'_vehicle_vin\', true);
                $financing_available = get_post_meta($id, \'_vehicle_financing_available\', true);
                
                // Format title
                $title = $year . \' \' . $make . \' \' . $model;
                
                // Get image
                $image = get_the_post_thumbnail_url($id, \'medium\');
                
                // Add to vehicles array
                $vehicles[] = array(
                    \'id\' => $id,
                    \'title\' => $title,
                    \'url\' => get_permalink($id),
                    \'image\' => $image,
                    \'price\' => $price,
                    \'year\' => $year,
                    \'make\' => $make,
                    \'model\' => $model,
                    \'type\' => $type,
                    \'mileage\' => $mileage,
                    \'engine\' => $engine,
                    \'transmission\' => $transmission,
                    \'color\' => $color,
                    \'vin\' => $vin,
                    \'financing_available\' => $financing_available,
                );
            }
        }
        
        // Create comparison table
        if (!empty($vehicles)) {
            // Generate nonce
            $nonce = wp_create_nonce(\'vehicle_comparison_nonce\');
            
            ?>
            <div class="comparison-table-container">
                <table class="comparison-table">
                    <thead>
                        <tr>
                            <th><?php _e("Feature", "cardealership-child"); ?></th>
                            <?php foreach ($vehicles as $vehicle) : ?>
                                <th>
                                    <div class="vehicle-image">
                                        <?php if ($vehicle[\'image\']) : ?>
                                            <img src="<?php echo esc_url($vehicle[\'image\']); ?>" alt="<?php echo esc_attr($vehicle[\'title\']); ?>">
                                        <?php else : ?>
                                            <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/no-image.jpg" alt="<?php echo esc_attr($vehicle[\'title\']); ?>">
                                        <?php endif; ?>
                                    </div>
                                    <div class="vehicle-title"><?php echo esc_html($vehicle[\'title\']); ?></div>
                                    <div class="vehicle-price"><?php echo wc_price($vehicle[\'price\']); ?></div>
                                    <div class="vehicle-actions">
                                        <a href="<?php echo esc_url($vehicle[\'url\']); ?>" class="button view-details"><?php _e("View Details", "cardealership-child"); ?></a>
                                        <button type="button" class="button remove-from-comparison" data-vehicle-id="<?php echo $vehicle[\'id\']; ?>" data-nonce="<?php echo $nonce; ?>"><?php _e("Remove", "cardealership-child"); ?></button>
                                    </div>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Basic Info -->
                        <tr>
                            <td><strong><?php _e("Year", "cardealership-child"); ?></strong></td>
                            <?php foreach ($vehicles as $vehicle) : ?>
                                <td><?php echo esc_html($vehicle[\'year\']); ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <td><strong><?php _e("Make", "cardealership-child"); ?></strong></td>
                            <?php foreach ($vehicles as $vehicle) : ?>
                                <td><?php echo esc_html($vehicle[\'make\']); ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <td><strong><?php _e("Model", "cardealership-child"); ?></strong></td>
                            <?php foreach ($vehicles as $vehicle) : ?>
                                <td><?php echo esc_html($vehicle[\'model\']); ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <td><strong><?php _e("Type", "cardealership-child"); ?></strong></td>
                            <?php foreach ($vehicles as $vehicle) : ?>
                                <td><?php echo esc_html($vehicle[\'type\']); ?></td>
                            <?php endforeach; ?>
                        </tr>
                        
                        <!-- Price -->
                        <tr>
                            <td><strong><?php _e("Price", "cardealership-child"); ?></strong></td>
                            <?php 
                            $min_price = min(array_column($vehicles, \'price\'));
                            foreach ($vehicles as $vehicle) : 
                                $highlight = ($vehicle[\'price\'] == $min_price) ? \' spec-highlight\' : \'\';
                            ?>
                                <td class="<?php echo $highlight; ?>"><?php echo wc_price($vehicle[\'price\']); ?></td>
                            <?php endforeach; ?>
                        </tr>
                        
                        <!-- Specs -->
                        <tr>
                            <td><strong><?php _e("Mileage", "cardealership-child"); ?></strong></td>
                            <?php 
                            $min_mileage = min(array_filter(array_column($vehicles, \'mileage\')));
                            foreach ($vehicles as $vehicle) : 
                                $highlight = ($vehicle[\'mileage\'] == $min_mileage) ? \' spec-highlight\' : \'\';
                            ?>
                                <td class="<?php echo $highlight; ?>"><?php echo $vehicle[\'mileage\'] ? number_format($vehicle[\'mileage\']) . \' km\' : \'-\'; ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <td><strong><?php _e("Engine", "cardealership-child"); ?></strong></td>
                            <?php foreach ($vehicles as $vehicle) : ?>
                                <td><?php echo esc_html($vehicle[\'engine\'] ?: \'-\'); ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <td><strong><?php _e("Transmission", "cardealership-child"); ?></strong></td>
                            <?php foreach ($vehicles as $vehicle) : ?>
                                <td><?php echo esc_html($vehicle[\'transmission\'] ? ucfirst($vehicle[\'transmission\']) : \'-\'); ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <td><strong><?php _e("Color", "cardealership-child"); ?></strong></td>
                            <?php foreach ($vehicles as $vehicle) : ?>
                                <td><?php echo esc_html($vehicle[\'color\'] ?: \'-\'); ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <td><strong><?php _e("VIN", "cardealership-child"); ?></strong></td>
                            <?php foreach ($vehicles as $vehicle) : ?>
                                <td><?php echo esc_html($vehicle[\'vin\'] ?: \'-\'); ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <td><strong><?php _e("Financing Available", "cardealership-child"); ?></strong></td>
                            <?php foreach ($vehicles as $vehicle) : ?>
                                <td><?php echo $vehicle[\'financing_available\'] ? __("Yes", "cardealership-child") : __("No", "cardealership-child"); ?></td>
                            <?php endforeach; ?>
                        </tr>
                        
                        <!-- Features -->
                        <?php
                        // Get all possible features
                        $all_features = array();
                        foreach ($vehicles as $vehicle) {
                            $features = get_post_meta($vehicle[\'id\'], \'_vehicle_features\', true);
                            if ($features) {
                                $feature_list = explode("\\n", $features);
                                foreach ($feature_list as $feature) {
                                    $feature = trim($feature);
                                    if (!empty($feature) && !in_array($feature, $all_features)) {
                                        $all_features[] = $feature;
                                    }
                                }
                            }
                        }
                        
                        // Sort features alphabetically
                        sort($all_features);
                        
                        // Display features if we have any
                        if (!empty($all_features)) {
                            ?>
                            <tr>
                                <td colspan="<?php echo count($vehicles) + 1; ?>" style="background-color: #e9ecef;"><strong><?php _e("Features", "cardealership-child"); ?></strong></td>
                            </tr>
                            <?php
                            foreach ($all_features as $feature) {
                                ?>
                                <tr>
                                    <td><?php echo esc_html($feature); ?></td>
                                    <?php foreach ($vehicles as $vehicle) : 
                                        $vehicle_features = get_post_meta($vehicle[\'id\'], \'_vehicle_features\', true);
                                        $vehicle_feature_list = $vehicle_features ? explode("\\n", $vehicle_features) : array();
                                        $vehicle_feature_list = array_map(\'trim\', $vehicle_feature_list);
                                        $has_feature = in_array($feature, $vehicle_feature_list);
                                    ?>
                                        <td><?php echo $has_feature ? \'<span class="dashicons dashicons-yes" style="color: #5cb85c;"></span>\' : \'<span class="dashicons dashicons-no" style="color: #d9534f;"></span>\'; ?></td>
                                    <?php endforeach; ?>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <?php
        }
    }
    ?>
</div>

<?php
get_footer();';
            
            file_put_contents($template, $template_content);
        }
    }
    
    return $template;
}
add_filter('template_include', 'cardealership_load_comparison_template');

/**
 * Create comparison page if it doesn't exist
 */
function cardealership_create_comparison_page() {
    // Check if page already exists
    $comparison_page_id = cardealership_get_comparison_page_id();
    
    if (!$comparison_page_id) {
        // Create page
        $page_data = array(
            'post_title' => __('Vehicle Comparison', 'cardealership-child'),
            'post_content' => __('Compare different vehicles side by side to help you make the best decision.', 'cardealership-child'),
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_author' => 1,
            'meta_input' => array(
                '_wp_page_template' => 'comparison-page.php'
            )
        );
        
        wp_insert_post($page_data);
    }
}
add_action('after_setup_theme', 'cardealership_create_comparison_page');

/**
 * Create menus and navigation links
 */
function cardealership_add_comparison_menu_item($items, $args) {
    // Add only to primary menu
    if ($args->theme_location !== 'primary') {
        return $items;
    }
    
    // Get comparison page URL
    $comparison_page_id = cardealership_get_comparison_page_id();
    if (!$comparison_page_id) {
        return $items;
    }
    
    $comparison_url = get_permalink($comparison_page_id);
    $comparison_title = __('Compare Vehicles', 'cardealership-child');
    
    // Create menu item
    $comparison_item = '<li class="menu-item menu-item-type-post_type menu-item-object-page">';
    $comparison_item .= '<a href="' . esc_url($comparison_url) . '">' . esc_html($comparison_title) . '</a>';
    $comparison_item .= '</li>';
    
    // Add after inventory link
    $inventory_pos = strpos($items, get_post_type_archive_link('vehicle'));
    
    if ($inventory_pos !== false) {
        // Find the end of the inventory list item
        $li_end_pos = strpos($items, '</li>', $inventory_pos);
        
        if ($li_end_pos !== false) {
            // Insert after the inventory list item
            $items = substr_replace($items, $comparison_item, $li_end_pos + 5, 0);
        } else {
            // Append to the end if we can't find the right position
            $items .= $comparison_item;
        }
    } else {
        // Append to the end if inventory link not found
        $items .= $comparison_item;
    }
    
    return $items;
}
add_filter('wp_nav_menu_items', 'cardealership_add_comparison_menu_item', 10, 2);
