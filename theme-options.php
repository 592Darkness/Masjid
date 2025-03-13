<?php
/**
 * Theme Options and Settings for Car Dealership
 * Add this code to your functions.php file
 */

/**
 * Register the theme options page in the WordPress admin
 */
function cardealership_register_options_page() {
    add_menu_page(
        __('Dealership Options', 'cardealership-child'),
        __('Dealership', 'cardealership-child'),
        'manage_options',
        'dealership-options',
        'cardealership_options_page',
        'dashicons-car',
        59
    );
    
    add_submenu_page(
        'dealership-options',
        __('General Settings', 'cardealership-child'),
        __('General Settings', 'cardealership-child'),
        'manage_options',
        'dealership-options',
        'cardealership_options_page'
    );
    
    add_submenu_page(
        'dealership-options',
        __('Social Media', 'cardealership-child'),
        __('Social Media', 'cardealership-child'),
        'manage_options',
        'dealership-social',
        'cardealership_social_page'
    );
    
    add_submenu_page(
        'dealership-options',
        __('Import Demo Data', 'cardealership-child'),
        __('Import Demo Data', 'cardealership-child'),
        'manage_options',
        'dealership-import',
        'cardealership_import_page'
    );
}
add_action('admin_menu', 'cardealership_register_options_page');

/**
 * Register settings for the options page
 */
function cardealership_register_settings() {
    // General Settings
    register_setting('dealership_options_group', 'dealership_address');
    register_setting('dealership_options_group', 'dealership_phone');
    register_setting('dealership_options_group', 'dealership_email');
    register_setting('dealership_options_group', 'dealership_hours');
    register_setting('dealership_options_group', 'dealership_finance_email');
    register_setting('dealership_options_group', 'dealership_footer_description');
    register_setting('dealership_options_group', 'dealership_newsletter_shortcode');
    
    // Social Media Settings
    register_setting('dealership_social_group', 'dealership_facebook');
    register_setting('dealership_social_group', 'dealership_instagram');
    register_setting('dealership_social_group', 'dealership_whatsapp');
    register_setting('dealership_social_group', 'dealership_youtube');
}
add_action('admin_init', 'cardealership_register_settings');

/**
 * Render the general options page
 */
function cardealership_options_page() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        
        <form method="post" action="options.php">
            <?php settings_fields('dealership_options_group'); ?>
            <?php do_settings_sections('dealership_options_group'); ?>
            
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('Address', 'cardealership-child'); ?></th>
                    <td>
                        <input type="text" name="dealership_address" value="<?php echo esc_attr(get_option('dealership_address', '123 Main Street, Georgetown, Guyana')); ?>" class="regular-text" />
                        <p class="description"><?php esc_html_e('Enter your dealership address', 'cardealership-child'); ?></p>
                    </td>
                </tr>
                
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('Phone Number', 'cardealership-child'); ?></th>
                    <td>
                        <input type="text" name="dealership_phone" value="<?php echo esc_attr(get_option('dealership_phone', '+592-000-0000')); ?>" class="regular-text" />
                        <p class="description"><?php esc_html_e('Enter your dealership phone number', 'cardealership-child'); ?></p>
                    </td>
                </tr>
                
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('Email Address', 'cardealership-child'); ?></th>
                    <td>
                        <input type="email" name="dealership_email" value="<?php echo esc_attr(get_option('dealership_email', 'info@example.com')); ?>" class="regular-text" />
                        <p class="description"><?php esc_html_e('Enter your dealership email address', 'cardealership-child'); ?></p>
                    </td>
                </tr>
                
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('Financing Email Address', 'cardealership-child'); ?></th>
                    <td>
                        <input type="email" name="dealership_finance_email" value="<?php echo esc_attr(get_option('dealership_finance_email', 'finance@example.com')); ?>" class="regular-text" />
                        <p class="description"><?php esc_html_e('Enter your financing department email address', 'cardealership-child'); ?></p>
                    </td>
                </tr>
                
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('Business Hours', 'cardealership-child'); ?></th>
                    <td>
                        <input type="text" name="dealership_hours" value="<?php echo esc_attr(get_option('dealership_hours', 'Mon-Fri: 8am-6pm, Sat: 9am-4pm')); ?>" class="regular-text" />
                        <p class="description"><?php esc_html_e('Enter your business hours', 'cardealership-child'); ?></p>
                    </td>
                </tr>
                
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('Footer Description', 'cardealership-child'); ?></th>
                    <td>
                        <textarea name="dealership_footer_description" rows="4" class="large-text"><?php echo esc_textarea(get_option('dealership_footer_description', 'Your trusted car dealership in Guyana offering a wide selection of new and used vehicles, genuine parts, and flexible financing options.')); ?></textarea>
                        <p class="description"><?php esc_html_e('Enter a short description for the footer', 'cardealership-child'); ?></p>
                    </td>
                </tr>
                
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('Newsletter Shortcode', 'cardealership-child'); ?></th>
                    <td>
                        <input type="text" name="dealership_newsletter_shortcode" value="<?php echo esc_attr(get_option('dealership_newsletter_shortcode')); ?>" class="regular-text" />
                        <p class="description"><?php esc_html_e('Enter a shortcode for newsletter signup form (e.g., MailChimp, MailPoet)', 'cardealership-child'); ?></p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

/**
 * Render the social media page
 */
function cardealership_social_page() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        
        <form method="post" action="options.php">
            <?php settings_fields('dealership_social_group'); ?>
            <?php do_settings_sections('dealership_social_group'); ?>
            
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('Facebook URL', 'cardealership-child'); ?></th>
                    <td>
                        <input type="url" name="dealership_facebook" value="<?php echo esc_attr(get_option('dealership_facebook')); ?>" class="regular-text" />
                        <p class="description"><?php esc_html_e('Enter your Facebook page URL', 'cardealership-child'); ?></p>
                    </td>
                </tr>
                
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('Instagram URL', 'cardealership-child'); ?></th>
                    <td>
                        <input type="url" name="dealership_instagram" value="<?php echo esc_attr(get_option('dealership_instagram')); ?>" class="regular-text" />
                        <p class="description"><?php esc_html_e('Enter your Instagram profile URL', 'cardealership-child'); ?></p>
                    </td>
                </tr>
                
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('WhatsApp Number', 'cardealership-child'); ?></th>
                    <td>
                        <input type="text" name="dealership_whatsapp" value="<?php echo esc_attr(get_option('dealership_whatsapp')); ?>" class="regular-text" />
                        <p class="description"><?php esc_html_e('Enter your WhatsApp number (without +)', 'cardealership-child'); ?></p>
                    </td>
                </tr>
                
                <tr valign="top">
                    <th scope="row"><?php esc_html_e('YouTube Channel URL', 'cardealership-child'); ?></th>
                    <td>
                        <input type="url" name="dealership_youtube" value="<?php echo esc_attr(get_option('dealership_youtube')); ?>" class="regular-text" />
                        <p class="description"><?php esc_html_e('Enter your YouTube channel URL', 'cardealership-child'); ?></p>
                    </td>
                </tr>
            </table>
            
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

/**
 * Render the import demo data page
 */
function cardealership_import_page() {
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        
        <div class="notice notice-warning">
            <p><strong><?php esc_html_e('Warning:', 'cardealership-child'); ?></strong> <?php esc_html_e('Importing demo data will replace your current content. It is recommended to backup your database before proceeding.', 'cardealership-child'); ?></p>
        </div>
        
        <div class="card">
            <h2><?php esc_html_e('Import Demo Content', 'cardealership-child'); ?></h2>
            <p><?php esc_html_e('This will import demo content including sample vehicles, parts, pages, and menus to help you get started.', 'cardealership-child'); ?></p>
            
            <form method="post" action="">
                <?php wp_nonce_field('dealership_import_demo', 'dealership_import_nonce'); ?>
                <input type="hidden" name="action" value="import_demo_data">
                <p>
                    <button type="submit" class="button button-primary"><?php esc_html_e('Import Demo Data', 'cardealership-child'); ?></button>
                </p>
            </form>
        </div>
        
        <div class="card">
            <h2><?php esc_html_e('Reset Demo Content', 'cardealership-child'); ?></h2>
            <p><?php esc_html_e('This will remove all imported demo content. Your custom content will remain unchanged.', 'cardealership-child'); ?></p>
            
            <form method="post" action="">
                <?php wp_nonce_field('dealership_reset_demo', 'dealership_reset_nonce'); ?>
                <input type="hidden" name="action" value="reset_demo_data">
                <p>
                    <button type="submit" class="button button-secondary"><?php esc_html_e('Reset Demo Data', 'cardealership-child'); ?></button>
                </p>
            </form>
        </div>
    </div>
    <?php
}

/**
 * Process import and reset actions
 */
function cardealership_process_import_actions() {
    // Import demo data
    if (isset($_POST['action']) && $_POST['action'] === 'import_demo_data' && isset($_POST['dealership_import_nonce'])) {
        if (!wp_verify_nonce($_POST['dealership_import_nonce'], 'dealership_import_demo')) {
            wp_die(__('Security check failed. Please try again.', 'cardealership-child'));
        }
        
        // Process demo data import
        // This is a placeholder; implement actual import functionality
        update_option('dealership_demo_imported', true);
        
        // Redirect back to the import page with success message
        wp_redirect(admin_url('admin.php?page=dealership-import&imported=1'));
        exit;
    }
    
    // Reset demo data
    if (isset($_POST['action']) && $_POST['action'] === 'reset_demo_data' && isset($_POST['dealership_reset_nonce'])) {
        if (!wp_verify_nonce($_POST['dealership_reset_nonce'], 'dealership_reset_demo')) {
            wp_die(__('Security check failed. Please try again.', 'cardealership-child'));
        }
        
        // Process demo data reset
        // This is a placeholder; implement actual reset functionality
        update_option('dealership_demo_imported', false);
        
        // Redirect back to the import page with success message
        wp_redirect(admin_url('admin.php?page=dealership-import&reset=1'));
        exit;
    }
}
add_action('admin_init', 'cardealership_process_import_actions');

/**
 * Display admin notices for import actions
 */
function cardealership_import_admin_notices() {
    $screen = get_current_screen();
    
    if ($screen->id !== 'dealership_page_dealership-import') {
        return;
    }
    
    if (isset($_GET['imported']) && $_GET['imported'] === '1') {
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php esc_html_e('Demo data imported successfully!', 'cardealership-child'); ?></p>
        </div>
        <?php
    }
    
    if (isset($_GET['reset']) && $_GET['reset'] === '1') {
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php esc_html_e('Demo data reset successfully!', 'cardealership-child'); ?></p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'cardealership_import_admin_notices');

/**
 * Add theme customizer options
 */
function cardealership_customize_register($wp_customize) {
    // Add Dealership Options Section
    $wp_customize->add_section('dealership_options', array(
        'title' => __('Dealership Options', 'cardealership-child'),
        'priority' => 30,
    ));
    
    // Add Back to Top Option
    $wp_customize->add_setting('dealership_back_to_top', array(
        'default' => true,
        'sanitize_callback' => 'cardealership_sanitize_checkbox',
    ));
    
    $wp_customize->add_control('dealership_back_to_top', array(
        'label' => __('Show Back to Top Button', 'cardealership-child'),
        'section' => 'dealership_options',
        'type' => 'checkbox',
    ));
    
    // Add Primary Color Option
    $wp_customize->add_setting('dealership_primary_color', array(
        'default' => '#d9534f',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'dealership_primary_color', array(
        'label' => __('Primary Color', 'cardealership-child'),
        'section' => 'dealership_options',
    )));
    
    // Add Secondary Color Option
    $wp_customize->add_setting('dealership_secondary_color', array(
        'default' => '#5bc0de',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'dealership_secondary_color', array(
        'label' => __('Secondary Color', 'cardealership-child'),
        'section' => 'dealership_options',
    )));
    
    // Add Footer Background Color Option
    $wp_customize->add_setting('dealership_footer_bg_color', array(
        'default' => '#333333',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'dealership_footer_bg_color', array(
        'label' => __('Footer Background Color', 'cardealership-child'),
        'section' => 'dealership_options',
    )));
    
    // Add Footer Text Color Option
    $wp_customize->add_setting('dealership_footer_text_color', array(
        'default' => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'dealership_footer_text_color', array(
        'label' => __('Footer Text Color', 'cardealership-child'),
        'section' => 'dealership_options',
    )));
}
add_action('customize_register', 'cardealership_customize_register');

/**
 * Sanitize checkbox values
 */
function cardealership_sanitize_checkbox($input) {
    return (isset($input) && true == $input) ? true : false;
}

/**
 * Output custom CSS from theme customizer
 */
function cardealership_customizer_css() {
    $primary_color = get_theme_mod('dealership_primary_color', '#d9534f');
    $secondary_color = get_theme_mod('dealership_secondary_color', '#5bc0de');
    $footer_bg_color = get_theme_mod('dealership_footer_bg_color', '#333333');
    $footer_text_color = get_theme_mod('dealership_footer_text_color', '#ffffff');
    
    ?>
    <style type="text/css">
        :root {
            --primary-color: <?php echo esc_attr($primary_color); ?>;
            --primary-color-hover: <?php echo esc_attr(cardealership_adjust_brightness($primary_color, -20)); ?>;
            --secondary-color: <?php echo esc_attr($secondary_color); ?>;
            --secondary-color-hover: <?php echo esc_attr(cardealership_adjust_brightness($secondary_color, -20)); ?>;
            --footer-bg-color: <?php echo esc_attr($footer_bg_color); ?>;
            --footer-text-color: <?php echo esc_attr($footer_text_color); ?>;
        }
        
        /* Apply primary color */
        .button, 
        button.alt, 
        input[type="button"].alt, 
        input[type="reset"].alt, 
        input[type="submit"].alt,
        .vehicle-price,
        .step-number,
        .section-title:after,
        .testimonial-dot.active,
        .pagination .page-numbers.current {
            background-color: var(--primary-color);
        }
        
        .button:hover, 
        button.alt:hover, 
        input[type="button"].alt:hover, 
        input[type="reset"].alt:hover, 
        input[type="submit"].alt:hover {
            background-color: var(--primary-color-hover);
        }
        
        .service-icon,
        a:hover,
        .vehicle-title a:hover {
            color: var(--primary-color);
        }
        
        .thumbnail.active {
            box-shadow: 0 0 0 2px var(--primary-color);
        }
        
        /* Apply secondary color */
        .button.secondary,
        .calculate-financing {
            background-color: var(--secondary-color);
        }
        
        .button.secondary:hover,
        .calculate-financing:hover {
            background-color: var(--secondary-color-hover);
        }
        
        /* Apply footer colors */
        .site-footer {
            background-color: var(--footer-bg-color);
            color: var(--footer-text-color);
        }
        
        .site-footer a,
        .site-footer h3 {
            color: var(--footer-text-color);
        }
        
        /* Back to top button */
        .back-to-top {
            background-color: var(--primary-color);
            color: #fff;
        }
        
        .back-to-top:hover {
            background-color: var(--primary-color-hover);
        }
    </style>
    <?php
}
add_action('wp_head', 'cardealership_customizer_css');

/**
 * Helper function to adjust color brightness
 */
function cardealership_adjust_brightness($hex, $steps) {
    $hex = ltrim($hex, '#');
    
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    $r = max(0, min(255, $r + $steps));
    $g = max(0, min(255, $g + $steps));
    $b = max(0, min(255, $b + $steps));
    
    return '#' . sprintf('%02x', $r) . sprintf('%02x', $g) . sprintf('%02x', $b);
}

/**
 * Register theme menus
 */
function cardealership_register_menus() {
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'cardealership-child'),
        'footer-menu' => __('Footer Menu', 'cardealership-child'),
    ));
}
add_action('after_setup_theme', 'cardealership_register_menus');

/**
 * Register footer widget areas
 */
function cardealership_widgets_init() {
    register_sidebar(array(
        'name' => __('Footer Widget 1', 'cardealership-child'),
        'id' => 'footer-1',
        'description' => __('First footer widget area', 'cardealership-child'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));
    
    register_sidebar(array(
        'name' => __('Footer Widget 2', 'cardealership-child'),
        'id' => 'footer-2',
        'description' => __('Second footer widget area', 'cardealership-child'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));
    
    register_sidebar(array(
        'name' => __('Footer Widget 3', 'cardealership-child'),
        'id' => 'footer-3',
        'description' => __('Third footer widget area', 'cardealership-child'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));
    
    register_sidebar(array(
        'name' => __('Shop Sidebar', 'cardealership-child'),
        'id' => 'shop-sidebar',
        'description' => __('Sidebar for the shop pages', 'cardealership-child'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));
}
add_action('widgets_init', 'cardealership_widgets_init');

/**
 * Process newsletter signup form
 */
function cardealership_process_newsletter_signup() {
    if (!isset($_POST['newsletter_nonce']) || !wp_verify_nonce($_POST['newsletter_nonce'], 'dealership_newsletter_nonce')) {
        wp_die(__('Security check failed. Please try again.', 'cardealership-child'));
    }
    
    $email = sanitize_email($_POST['subscriber_email']);
    
    if (!is_email($email)) {
        wp_redirect(add_query_arg('newsletter', 'invalid-email', wp_get_referer()));
        exit;
    }
    
    // This is a placeholder. In a real scenario, you would:
    // 1. Save to database or
    // 2. Connect to a newsletter service API (MailChimp, MailPoet, etc.)
    
    // For now, just log the email to demonstrate
    error_log('Newsletter signup: ' . $email);
    
    // Redirect back with success message
    wp_redirect(add_query_arg('newsletter', 'success', wp_get_referer()));
    exit;
}
add_action('admin_post_dealership_newsletter_signup', 'cardealership_process_newsletter_signup');
add_action('admin_post_nopriv_dealership_newsletter_signup', 'cardealership_process_newsletter_signup');

/**
 * Display newsletter signup notices
 */
function cardealership_newsletter_notices() {
    if (isset($_GET['newsletter'])) {
        if ($_GET['newsletter'] === 'success') {
            ?>
            <div class="newsletter-message newsletter-success">
                <p><?php esc_html_e('Thank you for subscribing to our newsletter!', 'cardealership-child'); ?></p>
            </div>
            <?php
        } elseif ($_GET['newsletter'] === 'invalid-email') {
            ?>
            <div class="newsletter-message newsletter-error">
                <p><?php esc_html_e('Please enter a valid email address.', 'cardealership-child'); ?></p>
            </div>
            <?php
        }
    }
}
add_action('wp_footer', 'cardealership_newsletter_notices');

/**
 * Enqueue dashicons for frontend use
 */
function cardealership_enqueue_dashicons() {
    wp_enqueue_style('dashicons');
}
add_action('wp_enqueue_scripts', 'cardealership_enqueue_dashicons');
