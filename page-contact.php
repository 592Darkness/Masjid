<?php
/**
 * Template Name: Contact Page
 * 
 * Create this file as page-contact.php in your child theme
 */
get_header();
?>

<div class="contact-page-container">
    <div class="page-header">
        <h1 class="page-title"><?php the_title(); ?></h1>
    </div>
    
    <div class="contact-content">
        <div class="contact-info-section">
            <div class="contact-info-container">
                <div class="contact-info-card">
                    <div class="icon">
                        <i class="dashicons dashicons-location"></i>
                    </div>
                    <h3><?php _e('Visit Us', 'cardealership-child'); ?></h3>
                    <p><?php echo esc_html(get_option('dealership_address', 'Georgetown, Guyana')); ?></p>
                </div>
                
                <div class="contact-info-card">
                    <div class="icon">
                        <i class="dashicons dashicons-phone"></i>
                    </div>
                    <h3><?php _e('Call Us', 'cardealership-child'); ?></h3>
                    <p><a href="tel:<?php echo esc_attr(get_option('dealership_phone', '+592-000-0000')); ?>"><?php echo esc_html(get_option('dealership_phone', '+592-000-0000')); ?></a></p>
                </div>
                
                <div class="contact-info-card">
                    <div class="icon">
                        <i class="dashicons dashicons-email"></i>
                    </div>
                    <h3><?php _e('Email Us', 'cardealership-child'); ?></h3>
                    <p><a href="mailto:<?php echo esc_attr(get_option('dealership_email', 'info@example.com')); ?>"><?php echo esc_html(get_option('dealership_email', 'info@example.com')); ?></a></p>
                </div>
                
                <div class="contact-info-card">
                    <div class="icon">
                        <i class="dashicons dashicons-clock"></i>
                    </div>
                    <h3><?php _e('Business Hours', 'cardealership-child'); ?></h3>
                    <p><?php _e('Monday - Friday:', 'cardealership-child'); ?> 8:00 AM - 5:30 PM</p>
                    <p><?php _e('Saturday:', 'cardealership-child'); ?> 9:00 AM - 3:00 PM</p>
                    <p><?php _e('Sunday:', 'cardealership-child'); ?> <?php _e('Closed', 'cardealership-child'); ?></p>
                </div>
            </div>
        </div>
        
        <div class="contact-map-section">
            <div class="contact-map-container">
                <h2><?php _e('Find Us', 'cardealership-child'); ?></h2>
                
                <?php 
                // Get the Google Maps API key (add this to your theme options)
                $google_maps_api_key = get_option('dealership_google_maps_api_key');
                
                if ($google_maps_api_key) {
                    // If you have a Google Maps API key
                    $address = urlencode(get_option('dealership_address', 'Georgetown, Guyana'));
                    ?>
                    <div class="google-map">
                        <iframe 
                            width="100%" 
                            height="450" 
                            frameborder="0" 
                            style="border:0" 
                            src="https://www.google.com/maps/embed/v1/place?key=<?php echo esc_attr($google_maps_api_key); ?>&q=<?php echo $address; ?>" 
                            allowfullscreen>
                        </iframe>
                    </div>
                    <?php
                } else {
                    // If you don't have a Google Maps API key, use OpenStreetMap
                    $address = urlencode(get_option('dealership_address', 'Georgetown, Guyana'));
                    ?>
                    <div class="openstreetmap">
                        <iframe 
                            width="100%" 
                            height="450" 
                            frameborder="0" 
                            style="border:0" 
                            src="https://www.openstreetmap.org/export/embed.html?bbox=-58.2%2C6.7%2C-58.1%2C6.8&amp;layer=mapnik&amp;marker=6.75%2C-58.15" 
                            allowfullscreen>
                        </iframe>
                        <br/>
                        <small>
                            <a href="https://www.openstreetmap.org/?mlat=6.75&amp;mlon=-58.15#map=12/6.75/-58.15">
                                <?php _e('View Larger Map', 'cardealership-child'); ?>
                            </a>
                        </small>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
        
        <div class="contact-form-section">
            <div class="contact-form-container">
                <h2><?php _e('Send Us a Message', 'cardealership-child'); ?></h2>
                
                <?php
                // Check if Contact Form 7 is active
                if (function_exists('wpcf7_contact_form_tag_func')) {
                    // Replace '123' with your actual Contact Form 7 ID
                    echo do_shortcode('[contact-form-7 id="123" title="Contact Form"]');
                } else {
                    // Basic form if Contact Form 7 is not available
                    ?>
                    <form class="contact-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <input type="hidden" name="action" value="submit_contact_form">
                        <?php wp_nonce_field('contact_form_submission', 'contact_form_nonce'); ?>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="contact_name"><?php _e('Name', 'cardealership-child'); ?> <span class="required">*</span></label>
                                <input type="text" id="contact_name" name="contact_name" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="contact_email"><?php _e('Email', 'cardealership-child'); ?> <span class="required">*</span></label>
                                <input type="email" id="contact_email" name="contact_email" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="contact_phone"><?php _e('Phone', 'cardealership-child'); ?></label>
                                <input type="tel" id="contact_phone" name="contact_phone">
                            </div>
                            
                            <div class="form-group">
                                <label for="contact_subject"><?php _e('Subject', 'cardealership-child'); ?> <span class="required">*</span></label>
                                <select id="contact_subject" name="contact_subject" required>
                                    <option value=""><?php _e('Select a Subject', 'cardealership-child'); ?></option>
                                    <option value="vehicle_inquiry"><?php _e('Vehicle Inquiry', 'cardealership-child'); ?></option>
                                    <option value="parts_inquiry"><?php _e('Parts Inquiry', 'cardealership-child'); ?></option>
                                    <option value="financing"><?php _e('Financing', 'cardealership-child'); ?></option>
                                    <option value="service"><?php _e('Service', 'cardealership-child'); ?></option>
                                    <option value="other"><?php _e('Other', 'cardealership-child'); ?></option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group full-width">
                                <label for="contact_message"><?php _e('Message', 'cardealership-child'); ?> <span class="required">*</span></label>
                                <textarea id="contact_message" name="contact_message" rows="6" required></textarea>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group full-width check-group">
                                <label>
                                    <input type="checkbox" name="privacy_consent" value="1" required>
                                    <?php _e('I consent to having this website store my submitted information so they can respond to my inquiry.', 'cardealership-child'); ?> <span class="required">*</span>
                                </label>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="button submit-contact"><?php _e('Send Message', 'cardealership-child'); ?></button>
                        </div>
                    </form>
                    <?php
                }
                ?>
            </div>
        </div>
        
        <div class="contact-departments-section">
            <h2><?php _e('Department Contacts', 'cardealership-child'); ?></h2>
            
            <div class="departments-container">
                <div class="department-card">
                    <h3><?php _e('Sales Department', 'cardealership-child'); ?></h3>
                    <p><?php _e('For inquiries about new and used vehicles.', 'cardealership-child'); ?></p>
                    <p><strong><?php _e('Email:', 'cardealership-child'); ?></strong> <a href="mailto:sales@example.com">sales@example.com</a></p>
                    <p><strong><?php _e('Phone:', 'cardealership-child'); ?></strong> <a href="tel:+592-000-0001">+592-000-0001</a></p>
                </div>
                
                <div class="department-card">
                    <h3><?php _e('Parts Department', 'cardealership-child'); ?></h3>
                    <p><?php _e('For ordering parts and accessories.', 'cardealership-child'); ?></p>
                    <p><strong><?php _e('Email:', 'cardealership-child'); ?></strong> <a href="mailto:parts@example.com">parts@example.com</a></p>
                    <p><strong><?php _e('Phone:', 'cardealership-child'); ?></strong> <a href="tel:+592-000-0002">+592-000-0002</a></p>
                </div>
                
                <div class="department-card">
                    <h3><?php _e('Finance Department', 'cardealership-child'); ?></h3>
                    <p><?php _e('For financing and payment inquiries.', 'cardealership-child'); ?></p>
                    <p><strong><?php _e('Email:', 'cardealership-child'); ?></strong> <a href="mailto:<?php echo esc_attr(get_option('dealership_finance_email', 'finance@example.com')); ?>"><?php echo esc_html(get_option('dealership_finance_email', 'finance@example.com')); ?></a></p>
                    <p><strong><?php _e('Phone:', 'cardealership-child'); ?></strong> <a href="tel:+592-000-0003">+592-000-0003</a></p>
                </div>
                
                <div class="department-card">
                    <h3><?php _e('Customer Service', 'cardealership-child'); ?></h3>
                    <p><?php _e('For general inquiries and support.', 'cardealership-child'); ?></p>
                    <p><strong><?php _e('Email:', 'cardealership-child'); ?></strong> <a href="mailto:info@example.com">info@example.com</a></p>
                    <p><strong><?php _e('Phone:', 'cardealership-child'); ?></strong> <a href="tel:+592-000-0000">+592-000-0000</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Contact Page Styles */
.contact-page-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 15px;
}

.page-header {
    margin-bottom: 40px;
    text-align: center;
}

.contact-content {
    display: flex;
    flex-direction: column;
    gap: 60px;
}

.contact-info-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
}

.contact-info-card {
    background-color: #f8f9fa;
    padding: 30px 20px;
    border-radius: 8px;
    text-align: center;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.contact-info-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.contact-info-card .icon {
    font-size: 32px;
    color: #d9534f;
    margin-bottom: 15px;
}

.contact-info-card h3 {
    margin: 0 0 10px 0;
}

.contact-info-card p {
    margin: 0 0 5px 0;
}

.google-map, .openstreetmap {
    overflow: hidden;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    margin-top: 20px;
}

.contact-form-container {
    max-width: 800px;
    margin: 0 auto;
}

.form-row {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin-bottom: 20px;
}

.form-group {
    flex: 1;
    min-width: 250px;
}

.form-group.full-width {
    width: 100%;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.check-group {
    display: flex;
    align-items: flex-start;
}

.check-group input {
    width: auto;
    margin-right: 10px;
    margin-top: 3px;
}

.form-actions {
    margin-top: 30px;
}

.submit-contact {
    padding: 12px 25px;
    font-size: 16px;
}

.required {
    color: #d9534f;
}

.departments-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
    margin-top: 30px;
}

.department-card {
    background-color: #fff;
    padding: 25px;
    border-radius: 8px;
    border: 1px solid #eee;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

.department-card h3 {
    margin: 0 0 15px 0;
    color: #333;
}

.department-card p {
    margin: 0 0 10px 0;
}

@media (max-width: 768px) {
    .form-row {
        flex-direction: column;
        gap: 15px;
    }
    
    .form-group {
        min-width: 100%;
    }
}
</style>

<?php
get_footer();

/**
 * Process contact form submission
 */
function cardealership_process_contact_form() {
    // Check if our form was submitted
    if (!isset($_POST['action']) || $_POST['action'] !== 'submit_contact_form') {
        return;
    }
    
    // Verify nonce
    if (!isset($_POST['contact_form_nonce']) || !wp_verify_nonce($_POST['contact_form_nonce'], 'contact_form_submission')) {
        wp_die(__('Security check failed. Please try again.', 'cardealership-child'));
    }
    
    // Get form data
    $name = isset($_POST['contact_name']) ? sanitize_text_field($_POST['contact_name']) : '';
    $email = isset($_POST['contact_email']) ? sanitize_email($_POST['contact_email']) : '';
    $phone = isset($_POST['contact_phone']) ? sanitize_text_field($_POST['contact_phone']) : '';
    $subject = isset($_POST['contact_subject']) ? sanitize_text_field($_POST['contact_subject']) : '';
    $message = isset($_POST['contact_message']) ? sanitize_textarea_field($_POST['contact_message']) : '';
    $privacy_consent = isset($_POST['privacy_consent']) ? true : false;
    
    // Basic validation
    if (empty($name) || empty($email) || empty($subject) || empty($message) || !$privacy_consent) {
        wp_die(__('Please fill in all required fields.', 'cardealership-child'));
    }
    
    // Map subject values to human-readable subjects
    $subject_map = array(
        'vehicle_inquiry' => __('Vehicle Inquiry', 'cardealership-child'),
        'parts_inquiry' => __('Parts Inquiry', 'cardealership-child'),
        'financing' => __('Financing', 'cardealership-child'),
        'service' => __('Service', 'cardealership-child'),
        'other' => __('Other', 'cardealership-child'),
    );
    
    $subject_text = isset($subject_map[$subject]) ? $subject_map[$subject] : $subject;
    
    // Set up email to admin
    $admin_email = get_option('admin_email');
    $site_name = get_bloginfo('name');
    $email_subject = sprintf(__('[%s] New Contact Form: %s', 'cardealership-child'), $site_name, $subject_text);
    
    $email_body = sprintf(__('Name: %s', 'cardealership-child'), $name) . "\n\n";
    $email_body .= sprintf(__('Email: %s', 'cardealership-child'), $email) . "\n\n";
    
    if (!empty($phone)) {
        $email_body .= sprintf(__('Phone: %s', 'cardealership-child'), $phone) . "\n\n";
    }
    
    $email_body .= sprintf(__('Subject: %s', 'cardealership-child'), $subject_text) . "\n\n";
    $email_body .= sprintf(__('Message: %s', 'cardealership-child'), $message) . "\n\n";
    $email_body .= __('This message was sent from the contact form on your website.', 'cardealership-child');
    
    // Send email
    $sent = wp_mail($admin_email, $email_subject, $email_body);
    
    // Send to department email based on subject
    $department_email = '';
    
    switch ($subject) {
        case 'vehicle_inquiry':
            $department_email = 'sales@example.com';
            break;
        case 'parts_inquiry':
            $department_email = 'parts@example.com';
            break;
        case 'financing':
            $department_email = get_option('dealership_finance_email', 'finance@example.com');
            break;
        case 'service':
            $department_email = 'service@example.com';
            break;
        default:
            $department_email = get_option('dealership_email', 'info@example.com');
    }
    
    if (!empty($department_email) && $department_email !== $admin_email) {
        wp_mail($department_email, $email_subject, $email_body);
    }
    
    // Send confirmation to customer
    $confirmation_subject = sprintf(__('Thank you for contacting %s', 'cardealership-child'), $site_name);
    
    $confirmation_message = sprintf(__('Dear %s,', 'cardealership-child'), $name) . "\n\n";
    $confirmation_message .= __('Thank you for contacting us. We have received your message and will respond to you as soon as possible.', 'cardealership-child') . "\n\n";
    $confirmation_message .= __('Here is a copy of your message:', 'cardealership-child') . "\n\n";
    $confirmation_message .= sprintf(__('Subject: %s', 'cardealership-child'), $subject_text) . "\n\n";
    $confirmation_message .= sprintf(__('Message: %s', 'cardealership-child'), $message) . "\n\n";
    $confirmation_message .= __('Best regards,', 'cardealership-child') . "\n";
    $confirmation_message .= $site_name;
    
    wp_mail($email, $confirmation_subject, $confirmation_message);
    
    // Store contact in database (optional)
    $contact_data = array(
        'post_title' => sprintf(__('Contact from %s: %s', 'cardealership-child'), $name, $subject_text),
        'post_content' => $message,
        'post_status' => 'private',
        'post_type' => 'contact',
    );
    
    $contact_id = wp_insert_post($contact_data);
    
    if ($contact_id) {
        update_post_meta($contact_id, '_contact_name', $name);
        update_post_meta($contact_id, '_contact_email', $email);
        update_post_meta($contact_id, '_contact_phone', $phone);
        update_post_meta($contact_id, '_contact_subject', $subject);
    }
    
    // Redirect to thank you page
    wp_redirect(add_query_arg('contact_sent', '1', get_permalink()));
    exit;
}
add_action('admin_post_submit_contact_form', 'cardealership_process_contact_form');
add_action('admin_post_nopriv_submit_contact_form', 'cardealership_process_contact_form');

/**
 * Display thank you message
 */
function cardealership_contact_thank_you_message() {
    if (isset($_GET['contact_sent']) && $_GET['contact_sent'] == '1') {
        echo '<div class="contact-success-message">';
        echo '<h3>' . __('Thank You!', 'cardealership-child') . '</h3>';
        echo '<p>' . __('Your message has been sent successfully. We will contact you shortly.', 'cardealership-child') . '</p>';
        echo '</div>';
    }
}
add_action('wp_head', 'cardealership_contact_thank_you_message');

/**
 * Register Contact post type (for storing contact form submissions)
 */
function cardealership_register_contact_post_type() {
    $labels = array(
        'name'               => _x('Contacts', 'post type general name', 'cardealership-child'),
        'singular_name'      => _x('Contact', 'post type singular name', 'cardealership-child'),
        'menu_name'          => _x('Contacts', 'admin menu', 'cardealership-child'),
        'name_admin_bar'     => _x('Contact', 'add new on admin bar', 'cardealership-child'),
        'add_new'            => _x('Add New', 'contact', 'cardealership-child'),
        'add_new_item'       => __('Add New Contact', 'cardealership-child'),
        'new_item'           => __('New Contact', 'cardealership-child'),
        'edit_item'          => __('Edit Contact', 'cardealership-child'),
        'view_item'          => __('View Contact', 'cardealership-child'),
        'all_items'          => __('All Contacts', 'cardealership-child'),
        'search_items'       => __('Search Contacts', 'cardealership-child'),
        'parent_item_colon'  => __('Parent Contacts:', 'cardealership-child'),
        'not_found'          => __('No contacts found.', 'cardealership-child'),
        'not_found_in_trash' => __('No contacts found in Trash.', 'cardealership-child')
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __('Contact form submissions', 'cardealership-child'),
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'contact'),
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => 7,
        'menu_icon'          => 'dashicons-email-alt',
        'supports'           => array('title', 'editor'),
    );

    register_post_type('contact', $args);
}
add_action('init', 'cardealership_register_contact_post_type');
