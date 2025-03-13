<?php
/**
 * Footer Template for Car Dealership Child Theme
 * Save this file as footer.php in your child theme
 */
?>

    </div><!-- #content -->

    <footer id="colophon" class="site-footer" role="contentinfo">
        <div class="footer-top">
            <div class="container">
                <div class="footer-widgets">
                    <div class="footer-widget footer-widget-1">
                        <div class="footer-logo">
                            <?php
                            if (function_exists('the_custom_logo') && has_custom_logo()) {
                                the_custom_logo();
                            } else {
                                echo '<h3 class="footer-site-title">' . esc_html(get_bloginfo('name')) . '</h3>';
                            }
                            ?>
                        </div>
                        <div class="footer-description">
                            <?php echo esc_html(get_option('dealership_footer_description', 'Your trusted car dealership in Guyana offering a wide selection of new and used vehicles, genuine parts, and flexible financing options.')); ?>
                        </div>
                        <div class="footer-social">
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
                            
                            <?php if (get_option('dealership_youtube')) : ?>
                                <a href="<?php echo esc_url(get_option('dealership_youtube')); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('YouTube', 'cardealership-child'); ?>">
                                    <i class="dashicons dashicons-youtube"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="footer-widget footer-widget-2">
                        <h3 class="footer-widget-title"><?php esc_html_e('Quick Links', 'cardealership-child'); ?></h3>
                        <?php
                        wp_nav_menu(array(
                            'theme_location' => 'footer-menu',
                            'menu_id' => 'footer-menu',
                            'depth' => 1,
                            'container' => false,
                            'menu_class' => 'footer-menu',
                            'fallback_cb' => false,
                        ));
                        ?>
                    </div>
                    
                    <div class="footer-widget footer-widget-3">
                        <h3 class="footer-widget-title"><?php esc_html_e('Contact Us', 'cardealership-child'); ?></h3>
                        <div class="footer-contact-info">
                            <p>
                                <i class="dashicons dashicons-location"></i>
                                <?php echo esc_html(get_option('dealership_address', '123 Main Street, Georgetown, Guyana')); ?>
                            </p>
                            <p>
                                <i class="dashicons dashicons-phone"></i>
                                <a href="tel:<?php echo esc_attr(get_option('dealership_phone', '+592-000-0000')); ?>">
                                    <?php echo esc_html(get_option('dealership_phone', '+592-000-0000')); ?>
                                </a>
                            </p>
                            <p>
                                <i class="dashicons dashicons-email-alt"></i>
                                <a href="mailto:<?php echo esc_attr(get_option('dealership_email', 'info@example.com')); ?>">
                                    <?php echo esc_html(get_option('dealership_email', 'info@example.com')); ?>
                                </a>
                            </p>
                            <p>
                                <i class="dashicons dashicons-clock"></i>
                                <?php echo esc_html(get_option('dealership_hours', 'Mon-Fri: 8am-6pm, Sat: 9am-4pm')); ?>
                            </p>
                        </div>
                    </div>
                    
                    <div class="footer-widget footer-widget-4">
                        <h3 class="footer-widget-title"><?php esc_html_e('Newsletter', 'cardealership-child'); ?></h3>
                        <div class="footer-newsletter">
                            <p><?php esc_html_e('Subscribe to our newsletter for updates on new vehicles, exclusive offers, and automotive news.', 'cardealership-child'); ?></p>
                            <?php
                            // Check if a newsletter form shortcode is set
                            $newsletter_shortcode = get_option('dealership_newsletter_shortcode');
                            if ($newsletter_shortcode) {
                                echo do_shortcode($newsletter_shortcode);
                            } else {
                                // Basic newsletter form
                                ?>
                                <form class="newsletter-form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
                                    <input type="hidden" name="action" value="dealership_newsletter_signup">
                                    <?php wp_nonce_field('dealership_newsletter_nonce', 'newsletter_nonce'); ?>
                                    <div class="form-group">
                                        <input type="email" name="subscriber_email" placeholder="<?php esc_attr_e('Your Email Address', 'cardealership-child'); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="newsletter-submit">
                                            <?php esc_html_e('Subscribe', 'cardealership-child'); ?>
                                        </button>
                                    </div>
                                </form>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <div class="container">
                <div class="footer-bottom-content">
                    <div class="copyright">
                        &copy; <?php echo date('Y'); ?> <?php echo esc_html(get_bloginfo('name')); ?>. 
                        <?php esc_html_e('All Rights Reserved.', 'cardealership-child'); ?>
                    </div>
                    <div class="footer-bottom-links">
                        <a href="<?php echo esc_url(get_privacy_policy_url()); ?>"><?php esc_html_e('Privacy Policy', 'cardealership-child'); ?></a>
                        <a href="<?php echo esc_url(site_url('/terms-and-conditions/')); ?>"><?php esc_html_e('Terms & Conditions', 'cardealership-child'); ?></a>
                        <a href="<?php echo esc_url(site_url('/sitemap/')); ?>"><?php esc_html_e('Sitemap', 'cardealership-child'); ?></a>
                    </div>
                    <div class="payment-methods">
                        <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/images/payment-methods.png'); ?>" alt="<?php esc_attr_e('Payment Methods', 'cardealership-child'); ?>">
                    </div>
                </div>
            </div>
        </div>
    </footer><!-- #colophon -->
</div><!-- #page -->

<?php
// Back to top button
if (get_theme_mod('dealership_back_to_top', true)) :
?>
<a href="#" id="back-to-top" class="back-to-top">
    <i class="dashicons dashicons-arrow-up-alt2"></i>
</a>
<script>
    // Back to top functionality
    jQuery(document).ready(function($) {
        var offset = 300;
        var duration = 300;
        
        $(window).scroll(function() {
            if ($(this).scrollTop() > offset) {
                $('#back-to-top').addClass('visible');
            } else {
                $('#back-to-top').removeClass('visible');
            }
        });
        
        $('#back-to-top').click(function(event) {
            event.preventDefault();
            $('html, body').animate({scrollTop: 0}, duration);
            return false;
        });
    });
</script>
<?php endif; ?>

<?php wp_footer(); ?>

</body>
</html>
