<?php
/**
 * Custom Header and Footer Functions
 * Add this code to your functions.php or include it as a separate file
 */

/**
 * Register Custom Header and Footer Areas
 */
function cardealership_widgets_init() {
    // Top Bar Widget Area
    register_sidebar(array(
        'name'          => __('Dealership Top Bar', 'cardealership-child'),
        'id'            => 'dealership-topbar',
        'description'   => __('Add widgets for top bar area (contact info, social icons, etc.)', 'cardealership-child'),
        'before_widget' => '<div id="%1$s" class="topbar-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ));
    
    // Footer Widget Areas
    register_sidebar(array(
        'name'          => __('Footer Column 1', 'cardealership-child'),
        'id'            => 'footer-1',
        'description'   => __('Add widgets for the first footer column', 'cardealership-child'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ));
    
    register_sidebar(array(
        'name'          => __('Footer Column 2', 'cardealership-child'),
        'id'            => 'footer-2',
        'description'   => __('Add widgets for the second footer column', 'cardealership-child'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ));
    
    register_sidebar(array(
        'name'          => __('Footer Column 3', 'cardealership-child'),
        'id'            => 'footer-3',
        'description'   => __('Add widgets for the third footer column', 'cardealership-child'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ));
    
    register_sidebar(array(
        'name'          => __('Footer Column 4', 'cardealership-child'),
        'id'            => 'footer-4',
        'description'   => __('Add widgets for the fourth footer column', 'cardealership-child'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="widget-title">',
        'after_title'   => '</h4>',
    ));
}
add_action('widgets_init', 'cardealership_widgets_init');

/**
 * Add Custom Header
 * Create a file named header.php in your child theme with this content
 */
function cardealership_custom_header() {
    // Remove parent theme's header actions if needed
    // remove_action('your_parent_theme_header_action', 'your_parent_theme_header_function');
    
    // Add our custom header
    add_action('wp_head', 'cardealership_header_styles');
}
add_action('after_setup_theme', 'cardealership_custom_header');

/**
 * Add Custom Footer
 * Create a file named footer.php in your child theme with this content
 */
function cardealership_custom_footer() {
    // Remove parent theme's footer actions if needed
    // remove_action('your_parent_theme_footer_action', 'your_parent_theme_footer_function');
    
    // Add our custom footer
    add_action('wp_footer', 'cardealership_footer_scripts');
}
add_action('after_setup_theme', 'cardealership_custom_footer');

/**
 * Add Inline Header Styles
 */
function cardealership_header_styles() {
    // Add any critical inline CSS here
}

/**
 * Add Footer Scripts
 */
function cardealership_footer_scripts() {
    // Add any custom footer scripts here
}

/**
 * Add Top Bar Above Header
 */
function cardealership_add_topbar() {
    ?>
    <div class="dealership-topbar">
        <div class="container">
            <div class="contact-info">
                <?php 
                $phone = get_option('dealership_phone', '+592-000-0000');
                $email = get_option('dealership_email', 'info@example.com');
                $address = get_option('dealership_address', 'Georgetown, Guyana');
                ?>
                <span class="topbar-phone">
                    <i class="dashicons dashicons-phone"></i>
                    <a href="tel:<?php echo esc_attr($phone); ?>"><?php echo esc_html($phone); ?></a>
                </span>
                <span class="topbar-email">
                    <i class="dashicons dashicons-email"></i>
                    <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a>
                </span>
                <span class="topbar-address">
                    <i class="dashicons dashicons-location"></i>
                    <?php echo esc_html($address); ?>
                </span>
            </div>
            
            <div class="social-icons">
                <?php 
                $facebook = get_option('dealership_facebook');
                $instagram = get_option('dealership_instagram');
                $twitter = get_option('dealership_twitter');
                ?>
                <?php if ($facebook) : ?>
                    <a href="<?php echo esc_url($facebook); ?>" target="_blank" rel="noopener" class="facebook-icon">
                        <i class="dashicons dashicons-facebook-alt"></i>
                    </a>
                <?php endif; ?>
                
                <?php if ($instagram) : ?>
                    <a href="<?php echo esc_url($instagram); ?>" target="_blank" rel="noopener" class="instagram-icon">
                        <i class="dashicons dashicons-instagram"></i>
                    </a>
                <?php endif; ?>
                
                <?php if ($twitter) : ?>
                    <a href="<?php echo esc_url($twitter); ?>" target="_blank" rel="noopener" class="twitter-icon">
                        <i class="dashicons dashicons-twitter"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
}
add_action('your_parent_theme_header_hook', 'cardealership_add_topbar', 5);
// Replace 'your_parent_theme_header_hook' with the appropriate hook from your parent theme

/**
 * Custom Footer Content
 */
function cardealership_custom_footer_content() {
    ?>
    <footer id="dealership-footer" class="site-footer">
        <div class="footer-widgets-container">
            <div class="container">
                <div class="footer-widgets-row">
                    <div class="footer-column">
                        <?php if (is_active_sidebar('footer-1')) : ?>
                            <?php dynamic_sidebar('footer-1'); ?>
                        <?php else : ?>
                            <div class="footer-widget">
                                <h4 class="widget-title"><?php _e('About Us', 'cardealership-child'); ?></h4>
                                <p><?php _e('We are a premier car dealership in Guyana offering a wide selection of new and used vehicles along with financing options and genuine parts.', 'cardealership-child'); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="footer-column">
                        <?php if (is_active_sidebar('footer-2')) : ?>
                            <?php dynamic_sidebar('footer-2'); ?>
                        <?php else : ?>
                            <div class="footer-widget">
                                <h4 class="widget-title"><?php _e('Quick Links', 'cardealership-child'); ?></h4>
                                <ul class="footer-links">
                                    <li><a href="<?php echo site_url('/vehicles/'); ?>"><?php _e('Vehicles', 'cardealership-child'); ?></a></li>
                                    <li><a href="<?php echo site_url('/shop/car-parts/'); ?>"><?php _e('Parts', 'cardealership-child'); ?></a></li>
                                    <li><a href="<?php echo site_url('/financing-application/'); ?>"><?php _e('Financing', 'cardealership-child'); ?></a></li>
                                    <li><a href="<?php echo site_url('/contact/'); ?>"><?php _e('Contact', 'cardealership-child'); ?></a></li>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="footer-column">
                        <?php if (is_active_sidebar('footer-3')) : ?>
                            <?php dynamic_sidebar('footer-3'); ?>
                        <?php else : ?>
                            <div class="footer-widget">
                                <h4 class="widget-title"><?php _e('Business Hours', 'cardealership-child'); ?></h4>
                                <ul class="business-hours">
                                    <li><span class="day"><?php _e('Monday - Friday:', 'cardealership-child'); ?></span> <span class="hours">8:00 AM - 5:30 PM</span></li>
                                    <li><span class="day"><?php _e('Saturday:', 'cardealership-child'); ?></span> <span class="hours">9:00 AM - 3:00 PM</span></li>
                                    <li><span class="day"><?php _e('Sunday:', 'cardealership-child'); ?></span> <span class="hours"><?php _e('Closed', 'cardealership-child'); ?></span></li>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="footer-column">
                        <?php if (is_active_sidebar('footer-4')) : ?>
                            <?php dynamic_sidebar('footer-4'); ?>
                        <?php else : ?>
                            <div class="footer-widget">
                                <h4 class="widget-title"><?php _e('Contact Us', 'cardealership-child'); ?></h4>
                                <ul class="contact-info">
                                    <li><i class="dashicons dashicons-location"></i> <?php echo esc_html(get_option('dealership_address', 'Georgetown, Guyana')); ?></li>
                                    <li><i class="dashicons dashicons-phone"></i> <a href="tel:<?php echo esc_attr(get_option('dealership_phone', '+592-000-0000')); ?>"><?php echo esc_html(get_option('dealership_phone', '+592-000-0000')); ?></a></li>
                                    <li><i class="dashicons dashicons-email"></i> <a href="mailto:<?php echo esc_attr(get_option('dealership_email', 'info@example.com')); ?>"><?php echo esc_html(get_option('dealership_email', 'info@example.com')); ?></a></li>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <div class="container">
                <div class="copyright">
                    <p>&copy; <?php echo date('Y'); ?> <?php echo esc_html(get_bloginfo('name')); ?>. <?php _e('All Rights Reserved.', 'cardealership-child'); ?></p>
                </div>
                
                <div class="payment-methods">
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/payment-methods.png" alt="<?php _e('Payment Methods', 'cardealership-child'); ?>">
                </div>
            </div>
        </div>
    </footer>
    <?php
}
// Hook into your parent theme's footer or replace it completely

/**
 * Add vehicle search form to header
 */
function cardealership_header_search_form() {
    ?>
    <div class="header-search-form">
        <form action="<?php echo esc_url(home_url('/')); ?>" method="get" class="quick-search-form">
            <input type="hidden" name="post_type" value="vehicle">
            <input type="text" name="s" placeholder="<?php _e('Search vehicles...', 'cardealership-child'); ?>" value="<?php echo get_search_query(); ?>">
            <button type="submit"><i class="dashicons dashicons-search"></i></button>
        </form>
    </div>
    <?php
}
// Hook into your parent theme's header or add it where needed

/**
 * Add custom header navigation for vehicles
 */
function cardealership_header_vehicle_nav() {
    // Get vehicle types for header menu
    $vehicle_types = get_terms(array(
        'taxonomy' => 'vehicle_type',
        'hide_empty' => true,
    ));
    
    if (!empty($vehicle_types) && !is_wp_error($vehicle_types)) {
        echo '<div class="vehicle-type-nav">';
        echo '<ul class="vehicle-types">';
        
        foreach ($vehicle_types as $type) {
            echo '<li><a href="' . esc_url(get_term_link($type)) . '">' . esc_html($type->name) . '</a></li>';
        }
        
        echo '</ul>';
        echo '</div>';
    }
}
// Hook into your parent theme's header or add it where needed

/**
 * Add mobile menu for dealership
 */
function cardealership_mobile_menu() {
    ?>
    <div class="mobile-menu-container">
        <button class="mobile-menu-toggle">
            <span class="menu-bar"></span>
            <span class="menu-bar"></span>
            <span class="menu-bar"></span>
        </button>
        
        <div class="mobile-menu">
            <div class="mobile-menu-header">
                <span class="close-mobile-menu">&times;</span>
            </div>
            
            <div class="mobile-menu-content">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'container' => false,
                    'menu_class' => 'mobile-nav',
                ));
                ?>
                
                <div class="mobile-contact-info">
                    <p><strong><?php _e('Call Us:', 'cardealership-child'); ?></strong> <a href="tel:<?php echo esc_attr(get_option('dealership_phone', '+592-000-0000')); ?>"><?php echo esc_html(get_option('dealership_phone', '+592-000-0000')); ?></a></p>
                    <p><strong><?php _e('Email:', 'cardealership-child'); ?></strong> <a href="mailto:<?php echo esc_attr(get_option('dealership_email', 'info@example.com')); ?>"><?php echo esc_html(get_option('dealership_email', 'info@example.com')); ?></a></p>
                </div>
                
                <div class="mobile-social-icons">
                    <?php 
                    $facebook = get_option('dealership_facebook');
                    $instagram = get_option('dealership_instagram');
                    $twitter = get_option('dealership_twitter');
                    ?>
                    <?php if ($facebook) : ?>
                        <a href="<?php echo esc_url($facebook); ?>" target="_blank" rel="noopener" class="facebook-icon">
                            <i class="dashicons dashicons-facebook-alt"></i>
                        </a>
                    <?php endif; ?>
                    
                    <?php if ($instagram) : ?>
                        <a href="<?php echo esc_url($instagram); ?>" target="_blank" rel="noopener" class="instagram-icon">
                            <i class="dashicons dashicons-instagram"></i>
                        </a>
                    <?php endif; ?>
                    
                    <?php if ($twitter) : ?>
                        <a href="<?php echo esc_url($twitter); ?>" target="_blank" rel="noopener" class="twitter-icon">
                            <i class="dashicons dashicons-twitter"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // Mobile menu toggle
        $('.mobile-menu-toggle').click(function() {
            $('.mobile-menu').addClass('active');
            $('body').addClass('mobile-menu-open');
        });
        
        $('.close-mobile-menu').click(function() {
            $('.mobile-menu').removeClass('active');
            $('body').removeClass('mobile-menu-open');
        });
        
        // Close menu when clicking outside
        $(document).click(function(event) {
            if (!$(event.target).closest('.mobile-menu, .mobile-menu-toggle').length) {
                $('.mobile-menu').removeClass('active');
                $('body').removeClass('mobile-menu-open');
            }
        });
        
        // Stop propagation on menu click
        $('.mobile-menu').click(function(event) {
            event.stopPropagation();
        });
    });
    </script>
    <?php
}
add_action('wp_footer', 'cardealership_mobile_menu');

/**
 * Add sticky header functionality
 */
function cardealership_sticky_header_script() {
    ?>
    <script>
    jQuery(document).ready(function($) {
        // Sticky header
        var header = $('.site-header');
        var headerHeight = header.outerHeight();
        var headerOffset = header.offset().top;
        
        $(window).scroll(function() {
            var scrollPosition = $(window).scrollTop();
            
            if (scrollPosition > headerOffset) {
                header.addClass('sticky-header');
                $('body').css('padding-top', headerHeight + 'px');
            } else {
                header.removeClass('sticky-header');
                $('body').css('padding-top', '0');
            }
        });
    });
    </script>
    <?php
}
add_action('wp_footer', 'cardealership_sticky_header_script');

/**
 * Add dealership options to Customizer
 */
function cardealership_customizer_options($wp_customize) {
    // Add Dealership Info Section
    $wp_customize->add_section('dealership_info', array(
        'title' => __('Dealership Information', 'cardealership-child'),
        'priority' => 30,
    ));
    
    // Add Phone Number Setting
    $wp_customize->add_setting('dealership_phone', array(
        'default' => '+592-000-0000',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('dealership_phone', array(
        'label' => __('Phone Number', 'cardealership-child'),
        'section' => 'dealership_info',
        'type' => 'text',
    ));
    
    // Add Email Setting
    $wp_customize->add_setting('dealership_email', array(
        'default' => 'info@example.com',
        'sanitize_callback' => 'sanitize_email',
    ));
    
    $wp_customize->add_control('dealership_email', array(
        'label' => __('Email Address', 'cardealership-child'),
        'section' => 'dealership_info',
        'type' => 'email',
    ));
    
    // Add Address Setting
    $wp_customize->add_setting('dealership_address', array(
        'default' => 'Georgetown, Guyana',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('dealership_address', array(
        'label' => __('Address', 'cardealership-child'),
        'section' => 'dealership_info',
        'type' => 'text',
    ));
    
    // Add Social Media Settings
    $wp_customize->add_setting('dealership_facebook', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control('dealership_facebook', array(
        'label' => __('Facebook URL', 'cardealership-child'),
        'section' => 'dealership_info',
        'type' => 'url',
    ));
    
    $wp_customize->add_setting('dealership_instagram', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control('dealership_instagram', array(
        'label' => __('Instagram URL', 'cardealership-child'),
        'section' => 'dealership_info',
        'type' => 'url',
    ));
    
    $wp_customize->add_setting('dealership_twitter', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control('dealership_twitter', array(
        'label' => __('Twitter URL', 'cardealership-child'),
        'section' => 'dealership_info',
        'type' => 'url',
    ));
    
    // Finance Department Contact
    $wp_customize->add_setting('dealership_finance_email', array(
        'default' => 'finance@example.com',
        'sanitize_callback' => 'sanitize_email',
    ));
    
    $wp_customize->add_control('dealership_finance_email', array(
        'label' => __('Finance Department Email', 'cardealership-child'),
        'section' => 'dealership_info',
        'type' => 'email',
    ));
    
    // Header Logo Setting
    $wp_customize->add_setting('dealership_logo', array(
        'default' => '',
        'sanitize_callback' => 'absint',
    ));
    
    $wp_customize->add_control(new WP_Customize_Media_Control($wp_customize, 'dealership_logo', array(
        'label' => __('Dealership Logo', 'cardealership-child'),
        'section' => 'title_tagline',
        'mime_type' => 'image',
        'priority' => 8,
    )));
}
add_action('customize_register', 'cardealership_customizer_options');
