<?php
/**
 * Admin Dashboard Customization
 * Add this code to your functions.php or include it as a separate file
 */

/**
 * Create custom dashboard widget for dealership overview
 */
function cardealership_dashboard_widget() {
    wp_add_dashboard_widget(
        'cardealership_dashboard_widget',
        __('Dealership Overview', 'cardealership-child'),
        'cardealership_dashboard_widget_content'
    );
}
add_action('wp_dashboard_setup', 'cardealership_dashboard_widget');

/**
 * Dashboard widget content
 */
function cardealership_dashboard_widget_content() {
    // Count vehicles
    $vehicle_count = wp_count_posts('vehicle');
    $total_vehicles = $vehicle_count->publish;
    
    // Count financing applications
    $financing_count = wp_count_posts('financing_app');
    $total_applications = $financing_count->private + $financing_count->publish;
    $new_applications = 0;
    
    $applications = get_posts(array(
        'post_type' => 'financing_app',
        'posts_per_page' => -1,
        'post_status' => 'private',
        'meta_query' => array(
            array(
                'key' => '_application_status',
                'value' => 'new',
                'compare' => '=',
            ),
        ),
    ));
    
    $new_applications = count($applications);
    
    // Count orders
    $processing_orders = wc_get_orders(array(
        'status' => 'processing',
        'limit' => -1,
        'return' => 'ids',
    ));
    
    $processing_count = count($processing_orders);
    
    // Count contacts
    $contact_count = wp_count_posts('contact');
    $new_contacts = $contact_count->private;
    
    // Display stats
    ?>
    <div class="dealership-dashboard-stats">
        <div class="dashboard-stat">
            <span class="stat-number"><?php echo $total_vehicles; ?></span>
            <span class="stat-label"><?php _e('Active Vehicles', 'cardealership-child'); ?></span>
        </div>
        
        <div class="dashboard-stat">
            <span class="stat-number"><?php echo $new_applications; ?></span>
            <span class="stat-label"><?php _e('New Financing Applications', 'cardealership-child'); ?></span>
        </div>
        
        <div class="dashboard-stat">
            <span class="stat-number"><?php echo $processing_count; ?></span>
            <span class="stat-label"><?php _e('Processing Orders', 'cardealership-child'); ?></span>
        </div>
        
        <div class="dashboard-stat">
            <span class="stat-number"><?php echo $new_contacts; ?></span>
            <span class="stat-label"><?php _e('New Messages', 'cardealership-child'); ?></span>
        </div>
    </div>
    
    <div class="dealership-dashboard-links">
        <a href="<?php echo admin_url('edit.php?post_type=vehicle'); ?>" class="button button-primary"><?php _e('Manage Vehicles', 'cardealership-child'); ?></a>
        <a href="<?php echo admin_url('edit.php?post_type=financing_app'); ?>" class="button"><?php _e('Financing Applications', 'cardealership-child'); ?></a>
        <a href="<?php echo admin_url('edit.php?post_type=shop_order'); ?>" class="button"><?php _e('Orders', 'cardealership-child'); ?></a>
        <a href="<?php echo admin_url('edit.php?post_type=contact'); ?>" class="button"><?php _e('Contact Messages', 'cardealership-child'); ?></a>
    </div>
    
    <style>
        .dealership-dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .dashboard-stat {
            background-color: #f8f9fa;
            padding: 15px;
            text-align: center;
            border-radius: 4px;
        }
        
        .stat-number {
            display: block;
            font-size: 24px;
            font-weight: bold;
            color: #0073aa;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 13px;
            color: #666;
        }
        
        .dealership-dashboard-links {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
    </style>
    <?php
}

/**
 * Create a custom admin menu structure
 */
function cardealership_admin_menu() {
    // Remove default menu items
    remove_menu_page('edit.php'); // Posts
    
    // Add custom top-level menu for dealership
    add_menu_page(
        __('Dealership', 'cardealership-child'),
        __('Dealership', 'cardealership-child'),
        'manage_options',
        'dealership-dashboard',
        'cardealership_dashboard_page',
        'dashicons-car',
        2
    );
    
    // Add dealership settings page
    add_submenu_page(
        'dealership-dashboard',
        __('Dealership Settings', 'cardealership-child'),
        __('Settings', 'cardealership-child'),
        'manage_options',
        'dealership-settings',
        'cardealership_settings_page'
    );
    
    // Add vehicle import/export page
    add_submenu_page(
        'dealership-dashboard',
        __('Import/Export Vehicles', 'cardealership-child'),
        __('Import/Export', 'cardealership-child'),
        'manage_options',
        'dealership-import-export',
        'cardealership_import_export_page'
    );
    
    // Add reporting page
    add_submenu_page(
        'dealership-dashboard',
        __('Dealership Reports', 'cardealership-child'),
        __('Reports', 'cardealership-child'),
        'manage_options',
        'dealership-reports',
        'cardealership_reports_page'
    );
}
add_action('admin_menu', 'cardealership_admin_menu', 999);

/**
 * Dealership dashboard page
 */
function cardealership_dashboard_page() {
    ?>
    <div class="wrap">
        <h1><?php _e('Dealership Dashboard', 'cardealership-child'); ?></h1>
        
        <div class="dealership-dashboard-wrapper">
            <div class="dashboard-section inventory-overview">
                <h2><?php _e('Inventory Overview', 'cardealership-child'); ?></h2>
                
                <?php
                // Count vehicles by type
                $types = get_terms(array(
                    'taxonomy' => 'vehicle_type',
                    'hide_empty' => false,
                ));
                
                $type_counts = array();
                
                if (!empty($types) && !is_wp_error($types)) {
                    foreach ($types as $type) {
                        $count = wc_get_products(array(
                            'status' => 'publish',
                            'limit' => -1,
                            'return' => 'ids',
                            'tax_query' => array(
                                array(
                                    'taxonomy' => 'vehicle_type',
                                    'field' => 'term_id',
                                    'terms' => $type->term_id,
                                ),
                            ),
                        ));
                        
                        $type_counts[$type->name] = count($count);
                    }
                }
                ?>
                
                <div class="inventory-stats">
                    <?php foreach ($type_counts as $type => $count) : ?>
                        <div class="inventory-stat">
                            <span class="stat-number"><?php echo $count; ?></span>
                            <span class="stat-label"><?php echo esc_html($type); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="inventory-actions">
                    <a href="<?php echo admin_url('post-new.php?post_type=vehicle'); ?>" class="button button-primary"><?php _e('Add New Vehicle', 'cardealership-child'); ?></a>
                    <a href="<?php echo admin_url('edit.php?post_type=vehicle'); ?>" class="button"><?php _e('Manage Inventory', 'cardealership-child'); ?></a>
                </div>
            </div>
            
            <div class="dashboard-section recent-orders">
                <h2><?php _e('Recent Orders', 'cardealership-child'); ?></h2>
                
                <?php
                $recent_orders = wc_get_orders(array(
                    'limit' => 5,
                    'orderby' => 'date',
                    'order' => 'DESC',
                ));
                
                if (!empty($recent_orders)) {
                    ?>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th><?php _e('Order', 'cardealership-child'); ?></th>
                                <th><?php _e('Date', 'cardealership-child'); ?></th>
                                <th><?php _e('Status', 'cardealership-child'); ?></th>
                                <th><?php _e('Total', 'cardealership-child'); ?></th>
                                <th><?php _e('Customer', 'cardealership-child'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_orders as $order) : ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo admin_url('post.php?post=' . $order->get_id() . '&action=edit'); ?>">
                                            #<?php echo $order->get_order_number(); ?>
                                        </a>
                                    </td>
                                    <td><?php echo $order->get_date_created()->date_i18n(get_option('date_format') . ' ' . get_option('time_format')); ?></td>
                                    <td><span class="order-status status-<?php echo $order->get_status(); ?>"><?php echo wc_get_order_status_name($order->get_status()); ?></span></td>
                                    <td><?php echo $order->get_formatted_order_total(); ?></td>
                                    <td><?php echo esc_html($order->get_billing_first_name() . ' ' . $order->get_billing_last_name()); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <div class="order-actions">
                        <a href="<?php echo admin_url('edit.php?post_type=shop_order'); ?>" class="button"><?php _e('View All Orders', 'cardealership-child'); ?></a>
                    </div>
                    <?php
                } else {
                    echo '<p>' . __('No recent orders found.', 'cardealership-child') . '</p>';
                }
                ?>
            </div>
            
            <div class="dashboard-section recent-applications">
                <h2><?php _e('Recent Financing Applications', 'cardealership-child'); ?></h2>
                
                <?php
                $recent_applications = get_posts(array(
                    'post_type' => 'financing_app',
                    'posts_per_page' => 5,
                    'post_status' => 'private',
                    'orderby' => 'date',
                    'order' => 'DESC',
                ));
                
                if (!empty($recent_applications)) {
                    ?>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th><?php _e('Applicant', 'cardealership-child'); ?></th>
                                <th><?php _e('Date', 'cardealership-child'); ?></th>
                                <th><?php _e('Status', 'cardealership-child'); ?></th>
                                <th><?php _e('Monthly Income', 'cardealership-child'); ?></th>
                                <th><?php _e('Actions', 'cardealership-child'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_applications as $application) : 
                                $first_name = get_post_meta($application->ID, 'first_name', true);
                                $last_name = get_post_meta($application->ID, 'last_name', true);
                                $status = get_post_meta($application->ID, '_application_status', true);
                                if (empty($status)) {
                                    $status = 'new';
                                }
                                $income = get_post_meta($application->ID, 'monthly_income', true);
                                
                                $status_labels = array(
                                    'new' => __('New Application', 'cardealership-child'),
                                    'reviewing' => __('Under Review', 'cardealership-child'),
                                    'documents_requested' => __('Documents Requested', 'cardealership-child'),
                                    'pending_approval' => __('Pending Approval', 'cardealership-child'),
                                    'approved' => __('Approved', 'cardealership-child'),
                                    'conditional_approval' => __('Conditional Approval', 'cardealership-child'),
                                    'declined' => __('Declined', 'cardealership-child'),
                                    'completed' => __('Completed', 'cardealership-child'),
                                );
                                
                                $status_label = isset($status_labels[$status]) ? $status_labels[$status] : $status;
                            ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo admin_url('post.php?post=' . $application->ID . '&action=edit'); ?>">
                                            <?php echo esc_html($first_name . ' ' . $last_name); ?>
                                        </a>
                                    </td>
                                    <td><?php echo get_the_date(get_option('date_format'), $application); ?></td>
                                    <td><span class="application-status status-<?php echo $status; ?>"><?php echo $status_label; ?></span></td>
                                    <td><?php echo 'GYD ' . number_format($income); ?></td>
                                    <td>
                                        <a href="<?php echo admin_url('post.php?post=' . $application->ID . '&action=edit'); ?>" class="button button-small"><?php _e('View', 'cardealership-child'); ?></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <div class="applications-actions">
                        <a href="<?php echo admin_url('edit.php?post_type=financing_app'); ?>" class="button"><?php _e('View All Applications', 'cardealership-child'); ?></a>
                    </div>
                    <?php
                } else {
                    echo '<p>' . __('No recent financing applications found.', 'cardealership-child') . '</p>';
                }
                ?>
            </div>
            
            <div class="dashboard-section recent-contacts">
                <h2><?php _e('Recent Contact Messages', 'cardealership-child'); ?></h2>
                
                <?php
                $recent_contacts = get_posts(array(
                    'post_type' => 'contact',
                    'posts_per_page' => 5,
                    'post_status' => 'private',
                    'orderby' => 'date',
                    'order' => 'DESC',
                ));
                
                if (!empty($recent_contacts)) {
                    ?>
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th><?php _e('Name', 'cardealership-child'); ?></th>
                                <th><?php _e('Subject', 'cardealership-child'); ?></th>
                                <th><?php _e('Date', 'cardealership-child'); ?></th>
                                <th><?php _e('Actions', 'cardealership-child'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_contacts as $contact) : 
                                $name = get_post_meta($contact->ID, '_contact_name', true);
                                $subject = get_post_meta($contact->ID, '_contact_subject', true);
                                
                                $subject_map = array(
                                    'vehicle_inquiry' => __('Vehicle Inquiry', 'cardealership-child'),
                                    'parts_inquiry' => __('Parts Inquiry', 'cardealership-child'),
                                    'financing' => __('Financing', 'cardealership-child'),
                                    'service' => __('Service', 'cardealership-child'),
                                    'other' => __('Other', 'cardealership-child'),
                                );
                                
                                $subject_text = isset($subject_map[$subject]) ? $subject_map[$subject] : $subject;
                            ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo admin_url('post.php?post=' . $contact->ID . '&action=edit'); ?>">
                                            <?php echo esc_html($name); ?>
                                        </a>
                                    </td>
                                    <td><?php echo esc_html($subject_text); ?></td>
                                    <td><?php echo get_the_date(get_option('date_format') . ' ' . get_option('time_format'), $contact); ?></td>
                                    <td>
                                        <a href="<?php echo admin_url('post.php?post=' . $contact->ID . '&action=edit'); ?>" class="button button-small"><?php _e('View', 'cardealership-child'); ?></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <div class="contacts-actions">
                        <a href="<?php echo admin_url('edit.php?post_type=contact'); ?>" class="button"><?php _e('View All Messages', 'cardealership-child'); ?></a>
                    </div>
                    <?php
                } else {
                    echo '<p>' . __('No recent contact messages found.', 'cardealership-child') . '</p>';
                }
                ?>
            </div>
        </div>
    </div>
    
    <style>
        .dealership-dashboard-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 20px;
        }
        
        .dashboard-section {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .dashboard-section h2 {
            margin-top: 0;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .inventory-stats {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin: 20px 0;
        }
        
        .inventory-stat {
            background-color: #f8f9fa;
            padding: 15px;
            text-align: center;
            border-radius: 4px;
            flex: 1;
            min-width: 100px;
        }
        
        .inventory-actions, .order-actions, .applications-actions, .contacts-actions {
            margin-top: 15px;
            display: flex;
            gap: 10px;
        }
        
        table.widefat {
            margin-top: 15px;
            border-collapse: collapse;
            width: 100%;
        }
        
        table.widefat th, table.widefat td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .order-status, .application-status {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 12px;
            color: white;
        }
        
        .order-status.status-completed, .application-status.status-approved {
            background-color: #5cb85c;
        }
        
        .order-status.status-processing, .application-status.status-reviewing {
            background-color: #5bc0de;
        }
        
        .order-status.status-on-hold, .application-status.status-pending_approval {
            background-color: #f0ad4e;
        }
        
        .order-status.status-failed, .application-status.status-declined {
            background-color: #d9534f;
        }
        
        .order-status.status-cancelled {
            background-color: #777;
        }
        
        .application-status.status-new {
            background-color: #0073aa;
        }
        
        .application-status.status-documents_requested {
            background-color: #72aee6;
        }
        
        .application-status.status-conditional_approval {
            background-color: #00a0d2;
        }
        
        .application-status.status-completed {
            background-color: #6c6c6c;
        }
        
        @media screen and (max-width: 782px) {
            .dealership-dashboard-wrapper {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <?php
}

/**
 * Dealership settings page
 */
function cardealership_settings_page() {
    // Check if form was submitted
    if (isset($_POST['dealership_settings_nonce']) && wp_verify_nonce($_POST['dealership_settings_nonce'], 'dealership_settings')) {
        // Save settings
        if (isset($_POST['dealership_phone'])) {
            update_option('dealership_phone', sanitize_text_field($_POST['dealership_phone']));
        }
        
        if (isset($_POST['dealership_email'])) {
            update_option('dealership_email', sanitize_email($_POST['dealership_email']));
        }
        
        if (isset($_POST['dealership_address'])) {
            update_option('dealership_address', sanitize_text_field($_POST['dealership_address']));
        }
        
        if (isset($_POST['dealership_facebook'])) {
            update_option('dealership_facebook', esc_url_raw($_POST['dealership_facebook']));
        }
        
        if (isset($_POST['dealership_instagram'])) {
            update_option('dealership_instagram', esc_url_raw($_POST['dealership_instagram']));
        }
        
        if (isset($_POST['dealership_twitter'])) {
            update_option('dealership_twitter', esc_url_raw($_POST['dealership_twitter']));
        }
        
        if (isset($_POST['dealership_finance_email'])) {
            update_option('dealership_finance_email', sanitize_email($_POST['dealership_finance_email']));
        }
        
        if (isset($_POST['dealership_google_maps_api_key'])) {
            update_option('dealership_google_maps_api_key', sanitize_text_field($_POST['dealership_google_maps_api_key']));
        }
        
        // Show success message
        echo '<div class="notice notice-success is-dismissible"><p>' . __('Settings saved successfully.', 'cardealership-child') . '</p></div>';
    }
    
    // Get current settings
    $phone = get_option('dealership_phone', '+592-000-0000');
    $email = get_option('dealership_email', 'info@example.com');
    $address = get_option('dealership_address', 'Georgetown, Guyana');
    $facebook = get_option('dealership_facebook', '');
    $instagram = get_option('dealership_instagram', '');
    $twitter = get_option('dealership_twitter', '');
    $finance_email = get_option('dealership_finance_email', 'finance@example.com');
    $google_maps_api_key = get_option('dealership_google_maps_api_key', '');
    
    // Display settings form
    ?>
    <div class="wrap">
        <h1><?php _e('Dealership Settings', 'cardealership-child'); ?></h1>
        
        <form method="post" action="">
            <?php wp_nonce_field('dealership_settings', 'dealership_settings_nonce'); ?>
            
            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row"><label for="dealership_phone"><?php _e('Phone Number', 'cardealership-child'); ?></label></th>
                        <td><input name="dealership_phone" type="text" id="dealership_phone" value="<?php echo esc_attr($phone); ?>" class="regular-text"></td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><label for="dealership_email"><?php _e('Email Address', 'cardealership-child'); ?></label></th>
                        <td><input name="dealership_email" type="email" id="dealership_email" value="<?php echo esc_attr($email); ?>" class="regular-text"></td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><label for="dealership_address"><?php _e('Address', 'cardealership-child'); ?></label></th>
                        <td><input name="dealership_address" type="text" id="dealership_address" value="<?php echo esc_attr($address); ?>" class="regular-text"></td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><label for="dealership_facebook"><?php _e('Facebook URL', 'cardealership-child'); ?></label></th>
                        <td><input name="dealership_facebook" type="url" id="dealership_facebook" value="<?php echo esc_url($facebook); ?>" class="regular-text"></td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><label for="dealership_instagram"><?php _e('Instagram URL', 'cardealership-child'); ?></label></th>
                        <td><input name="dealership_instagram" type="url" id="dealership_instagram" value="<?php echo esc_url($instagram); ?>" class="regular-text"></td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><label for="dealership_twitter"><?php _e('Twitter URL', 'cardealership-child'); ?></label></th>
                        <td><input name="dealership_twitter" type="url" id="dealership_twitter" value="<?php echo esc_url($twitter); ?>" class="regular-text"></td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><label for="dealership_finance_email"><?php _e('Finance Department Email', 'cardealership-child'); ?></label></th>
                        <td><input name="dealership_finance_email" type="email" id="dealership_finance_email" value="<?php echo esc_attr($finance_email); ?>" class="regular-text"></td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><label for="dealership_google_maps_api_key"><?php _e('Google Maps API Key', 'cardealership-child'); ?></label></th>
                        <td>
                            <input name="dealership_google_maps_api_key" type="text" id="dealership_google_maps_api_key" value="<?php echo esc_attr($google_maps_api_key); ?>" class="regular-text">
                            <p class="description"><?php _e('Used for the contact page map. Leave empty to use OpenStreetMap instead.', 'cardealership-child'); ?></p>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <p class="submit">
                <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Settings', 'cardealership-child'); ?>">
            </p>
        </form>
    </div>
    <?php
}

/**
 * Import/Export page for vehicles
 */
function cardealership_import_export_page() {
    ?>
    <div class="wrap">
        <h1><?php _e('Import/Export Vehicles', 'cardealership-child'); ?></h1>
        
        <div class="import-export-container">
            <div class="import-section">
                <h2><?php _e('Import Vehicles', 'cardealership-child'); ?></h2>
                
                <p><?php _e('Upload a CSV file to import vehicles into your inventory.', 'cardealership-child'); ?></p>
                
                <form method="post" enctype="multipart/form-data" action="">
                    <?php wp_nonce_field('vehicle_import', 'vehicle_import_nonce'); ?>
                    
                    <div class="form-field">
                        <label for="import_file"><?php _e('CSV File', 'cardealership-child'); ?></label>
                        <input type="file" name="import_file" id="import_file" accept=".csv">
                    </div>
                    
                    <div class="form-field">
                        <label>
                            <input type="checkbox" name="update_existing" value="1">
                            <?php _e('Update existing vehicles (match by VIN)', 'cardealership-child'); ?>
                        </label>
                    </div>
                    
                    <p class="submit">
                        <input type="submit" name="vehicle_import_submit" id="vehicle_import_submit" class="button button-primary" value="<?php _e('Import Vehicles', 'cardealership-child'); ?>">
                    </p>
                </form>
                
                <div class="csv-template">
                    <h3><?php _e('CSV Template', 'cardealership-child'); ?></h3>
                    <p><?php _e('Download the CSV template for vehicle imports.', 'cardealership-child'); ?></p>
                    <a href="#" class="button"><?php _e('Download Template', 'cardealership-child'); ?></a>
                </div>
            </div>
            
            <div class="export-section">
                <h2><?php _e('Export Vehicles', 'cardealership-child'); ?></h2>
                
                <p><?php _e('Export your vehicle inventory as a CSV file.', 'cardealership-child'); ?></p>
                
                <form method="post" action="">
                    <?php wp_nonce_field('vehicle_export', 'vehicle_export_nonce'); ?>
                    
                    <div class="form-field">
                        <label for="export_type"><?php _e('Export Type', 'cardealership-child'); ?></label>
                        <select name="export_type" id="export_type">
                            <option value="all"><?php _e('All Vehicles', 'cardealership-child'); ?></option>
                            <option value="new"><?php _e('New Vehicles', 'cardealership-child'); ?></option>
                            <option value="used"><?php _e('Used Vehicles', 'cardealership-child'); ?></option>
                        </select>
                    </div>
                    
                    <p class="submit">
                        <input type="submit" name="vehicle_export_submit" id="vehicle_export_submit" class="button button-primary" value="<?php _e('Export Vehicles', 'cardealership-child'); ?>">
                    </p>
                </form>
            </div>
        </div>
    </div>
    
    <style>
        .import-export-container {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            margin-top: 20px;
        }
        
        .import-section, .export-section {
            flex: 1;
            min-width: 300px;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .form-field {
            margin-bottom: 15px;
        }
        
        .form-field label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .csv-template {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
    </style>
    <?php
}

/**
 * Reports page
 */
function cardealership_reports_page() {
    ?>
    <div class="wrap">
        <h1><?php _e('Dealership Reports', 'cardealership-child'); ?></h1>
        
        <div class="reports-container">
            <div class="report-filters">
                <form method="get">
                    <input type="hidden" name="page" value="dealership-reports">
                    
                    <div class="filter-row">
                        <div class="filter-field">
                            <label for="report_type"><?php _e('Report Type', 'cardealership-child'); ?></label>
                            <select name="report_type" id="report_type">
                                <option value="sales"><?php _e('Sales Report', 'cardealership-child'); ?></option>
                                <option value="inventory"><?php _e('Inventory Report', 'cardealership-child'); ?></option>
                                <option value="financing"><?php _e('Financing Report', 'cardealership-child'); ?></option>
                            </select>
                        </div>
                        
                        <div class="filter-field">
                            <label for="date_range"><?php _e('Date Range', 'cardealership-child'); ?></label>
                            <select name="date_range" id="date_range">
                                <option value="this_month"><?php _e('This Month', 'cardealership-child'); ?></option>
                                <option value="last_month"><?php _e('Last Month', 'cardealership-child'); ?></option>
                                <option value="this_quarter"><?php _e('This Quarter', 'cardealership-child'); ?></option>
                                <option value="last_quarter"><?php _e('Last Quarter', 'cardealership-child'); ?></option>
                                <option value="this_year"><?php _e('This Year', 'cardealership-child'); ?></option>
                                <option value="last_year"><?php _e('Last Year', 'cardealership-child'); ?></option>
                                <option value="custom"><?php _e('Custom Range', 'cardealership-child'); ?></option>
                            </select>
                        </div>
                        
                        <div class="filter-field date-fields" style="display: none;">
                            <label for="start_date"><?php _e('Start Date', 'cardealership-child'); ?></label>
                            <input type="date" name="start_date" id="start_date">
                        </div>
                        
                        <div class="filter-field date-fields" style="display: none;">
                            <label for="end_date"><?php _e('End Date', 'cardealership-child'); ?></label>
                            <input type="date" name="end_date" id="end_date">
                        </div>
                        
                        <div class="filter-submit">
                            <button type="submit" class="button button-primary"><?php _e('Generate Report', 'cardealership-child'); ?></button>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="report-results">
                <div class="report-heading">
                    <h2><?php _e('Sales Report - This Month', 'cardealership-child'); ?></h2>
                    <div class="report-actions">
                        <button class="button"><?php _e('Export CSV', 'cardealership-child'); ?></button>
                        <button class="button"><?php _e('Print', 'cardealership-child'); ?></button>
                    </div>
                </div>
                
                <div class="report-summary">
                    <div class="summary-card">
                        <span class="summary-value">5</span>
                        <span class="summary-label"><?php _e('Vehicles Sold', 'cardealership-child'); ?></span>
                    </div>
                    
                    <div class="summary-card">
                        <span class="summary-value">$75,000</span>
                        <span class="summary-label"><?php _e('Total Revenue', 'cardealership-child'); ?></span>
                    </div>
                    
                    <div class="summary-card">
                        <span class="summary-value">$15,000</span>
                        <span class="summary-label"><?php _e('Average Sale Price', 'cardealership-child'); ?></span>
                    </div>
                    
                    <div class="summary-card">
                        <span class="summary-value">3</span>
                        <span class="summary-label"><?php _e('Financing Applications', 'cardealership-child'); ?></span>
                    </div>
                </div>
                
                <div class="report-chart">
                    <!-- Placeholder for chart - In a real implementation, use a charting library -->
                    <div class="placeholder-chart">
                        <p><?php _e('Chart Placeholder - Sales by Day', 'cardealership-child'); ?></p>
                    </div>
                </div>
                
                <div class="report-table">
                    <table class="widefat">
                        <thead>
                            <tr>
                                <th><?php _e('Date', 'cardealership-child'); ?></th>
                                <th><?php _e('Vehicle', 'cardealership-child'); ?></th>
                                <th><?php _e('Sale Price', 'cardealership-child'); ?></th>
                                <th><?php _e('Customer', 'cardealership-child'); ?></th>
                                <th><?php _e('Financing', 'cardealership-child'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Sample Data -->
                            <tr>
                                <td>2023-08-15</td>
                                <td>2022 Toyota Corolla</td>
                                <td>$18,500</td>
                                <td>John Smith</td>
                                <td>Yes</td>
                            </tr>
                            <tr>
                                <td>2023-08-12</td>
                                <td>2021 Honda Civic</td>
                                <td>$17,200</td>
                                <td>Sarah Johnson</td>
                                <td>No</td>
                            </tr>
                            <tr>
                                <td>2023-08-10</td>
                                <td>2019 Ford Escape</td>
                                <td>$14,800</td>
                                <td>Michael Brown</td>
                                <td>Yes</td>
                            </tr>
                            <tr>
                                <td>2023-08-05</td>
                                <td>2020 Nissan Rogue</td>
                                <td>$15,500</td>
                                <td>Emma Wilson</td>
                                <td>Yes</td>
                            </tr>
                            <tr>
                                <td>2023-08-02</td>
                                <td>2018 Chevrolet Malibu</td>
                                <td>$9,000</td>
                                <td>David Lee</td>
                                <td>No</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        .reports-container {
            margin-top: 20px;
        }
        
        .report-filters {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .filter-row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: flex-end;
        }
        
        .filter-field {
            flex: 1;
            min-width: 200px;
        }
        
        .filter-field label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .filter-field select, .filter-field input {
            width: 100%;
            padding: 6px;
        }
        
        .report-results {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .report-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .report-heading h2 {
            margin: 0;
        }
        
        .report-actions {
            display: flex;
            gap: 10px;
        }
        
        .report-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .summary-card {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-radius: 4px;
        }
        
        .summary-value {
            display: block;
            font-size: 24px;
            font-weight: bold;
            color: #0073aa;
            margin-bottom: 5px;
        }
        
        .summary-label {
            font-size: 14px;
            color: #666;
        }
        
        .report-chart {
            margin-bottom: 30px;
        }
        
        .placeholder-chart {
            height: 300px;
            background-color: #f8f9fa;
            border: 1px dashed #ccc;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
        }
        
        .report-table {
            overflow-x: auto;
        }
        
        @media screen and (max-width: 782px) {
            .filter-row {
                flex-direction: column;
            }
            
            .filter-field {
                width: 100%;
            }
            
            .report-heading {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        // Show/hide custom date fields
        $('#date_range').change(function() {
            if ($(this).val() === 'custom') {
                $('.date-fields').show();
            } else {
                $('.date-fields').hide();
            }
        });
    });
    </script>
    <?php
}

/**
 * Add admin footer text
 */
function cardealership_admin_footer_text($text) {
    $screen = get_current_screen();
    
    // Only modify on dealership pages
    if ($screen && ($screen->post_type === 'vehicle' || $screen->post_type === 'financing_app' || $screen->post_type === 'contact' || strpos($screen->id, 'dealership') !== false)) {
        $text = sprintf(__('Thank you for using %s Car Dealership WordPress Theme', 'cardealership-child'), get_bloginfo('name'));
    }
    
    return $text;
}
add_filter('admin_footer_text', 'cardealership_admin_footer_text');

/**
 * Add vehicle data metabox to orders
 */
function cardealership_add_vehicle_order_metabox() {
    add_meta_box(
        'vehicle_order_data',
        __('Vehicle Information', 'cardealership-child'),
        'cardealership_vehicle_order_metabox_callback',
        'shop_order',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'cardealership_add_vehicle_order_metabox');

/**
 * Vehicle order metabox callback
 */
function cardealership_vehicle_order_metabox_callback($post) {
    $order = wc_get_order($post->ID);
    $vehicle_purchased = false;
    
    foreach ($order->get_items() as $item) {
        $product_id = $item->get_product_id();
        $linked_vehicle_id = get_post_meta($product_id, '_linked_vehicle_id', true);
        
        if ($linked_vehicle_id) {
            $vehicle_purchased = true;
            
            $year = get_post_meta($linked_vehicle_id, '_vehicle_year', true);
            $make_terms = wp_get_post_terms($linked_vehicle_id, 'vehicle_make');
            $model_terms = wp_get_post_terms($linked_vehicle_id, 'vehicle_model');
            $mileage = get_post_meta($linked_vehicle_id, '_vehicle_mileage', true);
            $vin = get_post_meta($linked_vehicle_id, '_vehicle_vin', true);
            
            $make = !empty($make_terms) ? $make_terms[0]->name : '';
            $model = !empty($model_terms) ? $model_terms[0]->name : '';
            
            echo '<div class="vehicle-order-info">';
            echo '<h3>' . sprintf(__('Vehicle: %s %s %s', 'cardealership-child'), $year, $make, $model) . '</h3>';
            
            echo '<table class="widefat">';
            echo '<tr><th>' . __('Year:', 'cardealership-child') . '</th><td>' . esc_html($year) . '</td></tr>';
            echo '<tr><th>' . __('Make:', 'cardealership-child') . '</th><td>' . esc_html($make) . '</td></tr>';
            echo '<tr><th>' . __('Model:', 'cardealership-child') . '</th><td>' . esc_html($model) . '</td></tr>';
            
            if ($mileage) {
                echo '<tr><th>' . __('Mileage:', 'cardealership-child') . '</th><td>' . number_format($mileage) . ' km</td></tr>';
            }
            
            if ($vin) {
                echo '<tr><th>' . __('VIN:', 'cardealership-child') . '</th><td>' . esc_html($vin) . '</td></tr>';
            }
            
            // Add financing info if available
            $financing_order = get_post_meta($post->ID, '_financing_order', true);
            if ($financing_order === 'yes') {
                $deposit_amount = get_post_meta($post->ID, '_deposit_amount', true);
                $full_amount = get_post_meta($post->ID, '_full_vehicle_amount', true);
                
                echo '<tr><th>' . __('Financing:', 'cardealership-child') . '</th><td>' . __('Yes', 'cardealership-child') . '</td></tr>';
                echo '<tr><th>' . __('Deposit Paid:', 'cardealership-child') . '</th><td>' . wc_price($deposit_amount, array('currency' => $order->get_currency())) . '</td></tr>';
                echo '<tr><th>' . __('Full Vehicle Price:', 'cardealership-child') . '</th><td>' . wc_price($full_amount, array('currency' => $order->get_currency())) . '</td></tr>';
                
                $remaining = $full_amount - $deposit_amount;
                echo '<tr><th>' . __('Remaining Balance:', 'cardealership-child') . '</th><td>' . wc_price($remaining, array('currency' => $order->get_currency())) . '</td></tr>';
            }
            
            echo '<tr><th>' . __('Vehicle Link:', 'cardealership-child') . '</th><td><a href="' . get_edit_post_link($linked_vehicle_id) . '">' . __('Edit Vehicle', 'cardealership-child') . '</a></td></tr>';
            
            echo '</table>';
            echo '</div>';
            
            // Only show first vehicle for now
            break;
        }
    }
    
    if (!$vehicle_purchased) {
        echo '<p>' . __('No vehicle purchase in this order.', 'cardealership-child') . '</p>';
    }
}

/**
 * Add custom admin CSS
 */
function cardealership_admin_custom_css() {
    $screen = get_current_screen();
    
    // Only add on dealership pages
    if ($screen && ($screen->post_type === 'vehicle' || $screen->post_type === 'financing_app' || $screen->post_type === 'contact' || strpos($screen->id, 'dealership') !== false)) {
        ?>
        <style>
            /* General improvements */
            .wrap h1 {
                margin-bottom: 20px;
            }
            
            /* Vehicle edit screen */
            .vehicle-meta-box {
                margin-bottom: 15px;
            }
            
            /* Enhanced status badges */
            .application-status {
                display: inline-block;
                padding: 3px 8px;
                border-radius: 3px;
                color: white;
                font-weight: 500;
            }
            
            .status-new {
                background-color: #0073aa;
            }
            
            .status-reviewing {
                background-color: #5bc0de;
            }
            
            .status-documents_requested {
                background-color: #72aee6;
            }
            
            .status-pending_approval {
                background-color: #f0ad4e;
            }
            
            .status-approved {
                background-color: #5cb85c;
            }
            
            .status-conditional_approval {
                background-color: #00a0d2;
            }
            
            .status-declined {
                background-color: #d9534f;
            }
            
            .status-completed {
                background-color: #6c6c6c;
            }
            
            /* Dashboard stats */
            .dealership-dashboard-stats {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
                gap: 15px;
                margin-bottom: 20px;
            }
            
            .dashboard-stat {
                background-color: #f8f9fa;
                padding: 15px;
                text-align: center;
                border-radius: 4px;
            }
            
            .stat-number {
                display: block;
                font-size: 24px;
                font-weight: bold;
                color: #0073aa;
                margin-bottom: 5px;
            }
            
            .stat-label {
                font-size: 13px;
                color: #666;
            }
        </style>
        <?php
    }
}
add_action('admin_head', 'cardealership_admin_custom_css');

/**
 * Add quick links to admin bar
 */
function cardealership_admin_bar_links($admin_bar) {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    $admin_bar->add_menu(array(
        'id' => 'dealership-menu',
        'title' => __('Dealership', 'cardealership-child'),
        'href' => admin_url('admin.php?page=dealership-dashboard'),
    ));
    
    $admin_bar->add_menu(array(
        'id' => 'add-vehicle',
        'parent' => 'dealership-menu',
        'title' => __('Add New Vehicle', 'cardealership-child'),
        'href' => admin_url('post-new.php?post_type=vehicle'),
    ));
    
    $admin_bar->add_menu(array(
        'id' => 'manage-vehicles',
        'parent' => 'dealership-menu',
        'title' => __('Manage Vehicles', 'cardealership-child'),
        'href' => admin_url('edit.php?post_type=vehicle'),
    ));
    
    $admin_bar->add_menu(array(
        'id' => 'financing-applications',
        'parent' => 'dealership-menu',
        'title' => __('Financing Applications', 'cardealership-child'),
        'href' => admin_url('edit.php?post_type=financing_app'),
    ));
    
    $admin_bar->add_menu(array(
        'id' => 'orders',
        'parent' => 'dealership-menu',
        'title' => __('Orders', 'cardealership-child'),
        'href' => admin_url('edit.php?post_type=shop_order'),
    ));
    
    $admin_bar->add_menu(array(
        'id' => 'dealership-settings',
        'parent' => 'dealership-menu',
        'title' => __('Settings', 'cardealership-child'),
        'href' => admin_url('admin.php?page=dealership-settings'),
    ));
}
add_action('admin_bar_menu', 'cardealership_admin_bar_links', 80);
