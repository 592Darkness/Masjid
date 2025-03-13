<?php
/**
 * Header Template for Car Dealership Child Theme
 * Save this file as header.php in your child theme
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php
if (function_exists('wp_body_open')) {
    wp_body_open();
} else {
    do_action('wp_body_open');
}
?>

<div id="page" class="site">
    <a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e('Skip to content', 'cardealership-child'); ?></a>

    <!-- Custom Dealership Topbar -->
    <div class="dealership-topbar">
        <div class="container">
            <div class="contact-info">
                <span class="location">
                    <i class="dashicons dashicons-location"></i>
                    <?php echo esc_html(get_option('dealership_address', '123 Main Street, Georgetown, Guyana')); ?>
                </span>
                <span class="phone">
                    <i class="dashicons dashicons-phone"></i>
                    <a href="tel:<?php echo esc_attr(get_option('dealership_phone', '+592-000-0000')); ?>">
                        <?php echo esc_html(get_option('dealership_phone', '+592-000-0000')); ?>
                    </a>
                </span>
                <span class="hours">
                    <i class="dashicons dashicons-clock"></i>
                    <?php echo esc_html(get_option('dealership_hours', 'Mon-Fri: 8am-6pm, Sat: 9am-4pm')); ?>
                </span>
            </div>
            <div class="social-icons">
                <?php if (get_option('dealership_facebook')) : ?>
                    <a href="<?php echo esc_url(get_option('dealership_facebook')); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('Facebook', 'cardealership-child'); ?>">
                        <i class="dashicons dashicons-facebook-alt"></i>
                    </a>
                <?php endif; ?>
                
                <?php if (get_option('dealership_instagram')) : ?>
                    <a href="<?php echo esc_url(get_option('dealership_instagram')); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('Instagram', 'cardealership-child'); ?>">
                        <i class="dashicons dashicons-instagram"></i>
                    </a>
                <?php endif; ?>
                
                <?php if (get_option('dealership_whatsapp')) : ?>
                    <a href="https://wa.me/<?php echo esc_attr(get_option('dealership_whatsapp')); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('WhatsApp', 'cardealership-child'); ?>">
                        <i class="dashicons dashicons-whatsapp"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <header id="masthead" class="site-header" role="banner">
        <div class="container">
            <div class="site-header-wrapper">
                <div class="site-branding">
                    <?php if (has_custom_logo()) : ?>
                        <div class="site-logo"><?php the_custom_logo(); ?></div>
                    <?php else : ?>
                        <h1 class="site-title">
                            <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                                <?php bloginfo('name'); ?>
                            </a>
                        </h1>
                        <p class="site-description"><?php bloginfo('description'); ?></p>
                    <?php endif; ?>
                </div>

                <div class="header-right">
                    <div class="header-actions">
                        <!-- Currency switcher placeholder -->
                        <?php if (function_exists('woocommerce_demo_store') && class_exists('WooCommerce')) : ?>
                            <?php if (is_user_logged_in()) : ?>
                                <a href="<?php echo esc_url(wc_get_account_endpoint_url('dashboard')); ?>" class="my-account-link">
                                    <i class="dashicons dashicons-admin-users"></i>
                                    <?php esc_html_e('My Account', 'cardealership-child'); ?>
                                </a>
                            <?php else : ?>
                                <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" class="my-account-link">
                                    <i class="dashicons dashicons-admin-users"></i>
                                    <?php esc_html_e('Login / Register', 'cardealership-child'); ?>
                                </a>
                            <?php endif; ?>
                            
                            <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="cart-contents" title="<?php esc_attr_e('View your shopping cart', 'cardealership-child'); ?>">
                                <i class="dashicons dashicons-cart"></i>
                                <span class="cart-count">
                                    <?php echo esc_html(WC()->cart ? WC()->cart->get_cart_contents_count() : '0'); ?>
                                </span>
                            </a>
                        <?php endif; ?>
                        
                        <a href="<?php echo esc_url(site_url('/financing-application/')); ?>" class="financing-link">
                            <i class="dashicons dashicons-money-alt"></i>
                            <?php esc_html_e('Apply for Financing', 'cardealership-child'); ?>
                        </a>
                    </div>
                    
                    <nav id="site-navigation" class="main-navigation" role="navigation">
                        <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false">
                            <span class="dashicons dashicons-menu"></span>
                            <?php esc_html_e('Menu', 'cardealership-child'); ?>
                        </button>
                        
                        <?php
                        wp_nav_menu(array(
                            'theme_location' => 'primary',
                            'menu_id' => 'primary-menu',
                            'container_class' => 'primary-menu-container',
                            'fallback_cb' => false,
                        ));
                        ?>
                    </nav>
                </div>
            </div>
        </div>
    </header>

    <div id="content" class="site-content">
