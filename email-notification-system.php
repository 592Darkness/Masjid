<?php
/**
 * Email Notification System
 * Add this code to your functions.php or include it as a separate file
 */

/**
 * Register custom email templates for WooCommerce
 */
function cardealership_register_email_notifications($email_classes) {
    // Include email template classes
    require_once get_stylesheet_directory() . '/inc/emails/class-vehicle-inquiry-email.php';
    require_once get_stylesheet_directory() . '/inc/emails/class-financing-application-email.php';
    require_once get_stylesheet_directory() . '/inc/emails/class-vehicle-purchased-email.php';
    
    // Add email classes
    $email_classes['WC_Email_Vehicle_Inquiry'] = new WC_Email_Vehicle_Inquiry();
    $email_classes['WC_Email_Financing_Application'] = new WC_Email_Financing_Application();
    $email_classes['WC_Email_Vehicle_Purchased'] = new WC_Email_Vehicle_Purchased();
    
    return $email_classes;
}
add_filter('woocommerce_email_classes', 'cardealership_register_email_notifications');

/**
 * Create the directory for email templates if it doesn't exist
 */
function cardealership_create_email_template_directory() {
    $email_dir = get_stylesheet_directory() . '/inc/emails';
    
    if (!file_exists($email_dir)) {
        mkdir($email_dir, 0755, true);
    }
}
add_action('after_setup_theme', 'cardealership_create_email_template_directory');

/**
 * Vehicle Inquiry Email template class
 * Save as: /inc/emails/class-vehicle-inquiry-email.php
 */
class WC_Email_Vehicle_Inquiry extends WC_Email {
    /**
     * Constructor
     */
    public function __construct() {
        $this->id = 'vehicle_inquiry';
        $this->title = __('Vehicle Inquiry', 'cardealership-child');
        $this->description = __('Email notification sent to admin when a customer submits a vehicle inquiry', 'cardealership-child');
        
        $this->template_html = 'emails/vehicle-inquiry.php';
        $this->template_plain = 'emails/plain/vehicle-inquiry.php';
        $this->template_base = get_stylesheet_directory() . '/woocommerce/';
        
        // Call parent constructor
        parent::__construct();
        
        // Default recipient to admin email
        $this->recipient = $this->get_option('recipient', get_option('admin_email'));
        
        // Admin and Customer email
        $this->customer_email = true;
    }
    
    /**
     * Get email subject
     */
    public function get_subject() {
        return apply_filters('woocommerce_email_subject_vehicle_inquiry', $this->format_string($this->get_option('subject', __('New Vehicle Inquiry: {vehicle_title}', 'cardealership-child'))), $this->object);
    }
    
    /**
     * Get email heading
     */
    public function get_heading() {
        return apply_filters('woocommerce_email_heading_vehicle_inquiry', $this->format_string($this->get_option('heading', __('New Vehicle Inquiry', 'cardealership-child'))), $this->object);
    }
    
    /**
     * Trigger the email
     */
    public function trigger($inquiry_data = array()) {
        $this->setup_locale();
        
        $this->object = $inquiry_data;
        
        if (is_array($inquiry_data) && !empty($inquiry_data)) {
            // Replace placeholders in subject/heading
            $this->find['vehicle_title'] = '{vehicle_title}';
            $this->replace['vehicle_title'] = isset($inquiry_data['vehicle_title']) ? $inquiry_data['vehicle_title'] : '';
            
            if (!$this->is_enabled() || !$this->get_recipient()) {
                return;
            }
            
            // Send email to admin
            $this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());
            
            // Send confirmation to customer if customer email is provided
            if (isset($inquiry_data['email']) && !empty($inquiry_data['email'])) {
                $this->customer_email = true;
                $this->send($inquiry_data['email'], $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());
            }
        }
        
        $this->restore_locale();
    }
    
    /**
     * Get content html
     */
    public function get_content_html() {
        return wc_get_template_html(
            $this->template_html,
            array(
                'inquiry_data' => $this->object,
                'email_heading' => $this->get_heading(),
                'sent_to_admin' => true,
                'plain_text' => false,
                'email' => $this,
            ),
            'woocommerce/',
            $this->template_base
        );
    }
    
    /**
     * Get content plain
     */
    public function get_content_plain() {
        return wc_get_template_html(
            $this->template_plain,
            array(
                'inquiry_data' => $this->object,
                'email_heading' => $this->get_heading(),
                'sent_to_admin' => true,
                'plain_text' => true,
                'email' => $this,
            ),
            'woocommerce/',
            $this->template_base
        );
    }
    
    /**
     * Initialize settings form fields
     */
    public function init_form_fields() {
        $this->form_fields = array(
            'enabled' => array(
                'title' => __('Enable/Disable', 'cardealership-child'),
                'type' => 'checkbox',
                'label' => __('Enable this email notification', 'cardealership-child'),
                'default' => 'yes',
            ),
            'recipient' => array(
                'title' => __('Recipient(s)', 'cardealership-child'),
                'type' => 'text',
                'description' => __('Enter recipients (comma separated) for this email. Defaults to admin email.', 'cardealership-child'),
                'placeholder' => get_option('admin_email'),
                'default' => get_option('admin_email'),
                'css' => 'width: 400px;',
            ),
            'subject' => array(
                'title' => __('Subject', 'cardealership-child'),
                'type' => 'text',
                'description' => __('This controls the email subject line. Leave blank to use the default subject: <code>New Vehicle Inquiry: {vehicle_title}</code>.', 'cardealership-child'),
                'placeholder' => __('New Vehicle Inquiry: {vehicle_title}', 'cardealership-child'),
                'default' => '',
                'css' => 'width: 400px;',
            ),
            'heading' => array(
                'title' => __('Email Heading', 'cardealership-child'),
                'type' => 'text',
                'description' => __('This controls the main heading in the email notification. Leave blank to use the default heading: <code>New Vehicle Inquiry</code>.', 'cardealership-child'),
                'placeholder' => __('New Vehicle Inquiry', 'cardealership-child'),
                'default' => '',
                'css' => 'width: 400px;',
            ),
            'email_type' => array(
                'title' => __('Email type', 'cardealership-child'),
                'type' => 'select',
                'description' => __('Choose which format of email to send.', 'cardealership-child'),
                'default' => 'html',
                'class' => 'email_type wc-enhanced-select',
                'options' => $this->get_email_type_options(),
            ),
        );
    }
}

/**
 * Financing Application Email template class
 * Save as: /inc/emails/class-financing-application-email.php
 */
class WC_Email_Financing_Application extends WC_Email {
    /**
     * Constructor
     */
    public function __construct() {
        $this->id = 'financing_application';
        $this->title = __('Financing Application', 'cardealership-child');
        $this->description = __('Email notification sent when a customer submits a financing application', 'cardealership-child');
        
        $this->template_html = 'emails/financing-application.php';
        $this->template_plain = 'emails/plain/financing-application.php';
        $this->template_base = get_stylesheet_directory() . '/woocommerce/';
        
        // Call parent constructor
        parent::__construct();
        
        // Default recipient to admin email
        $this->recipient = $this->get_option('recipient', get_option('admin_email'));
        
        // Admin and Customer email
        $this->customer_email = true;
    }
    
    /**
     * Get email subject
     */
    public function get_subject() {
        return apply_filters('woocommerce_email_subject_financing_application', $this->format_string($this->get_option('subject', __('New Financing Application: {applicant_name}', 'cardealership-child'))), $this->object);
    }
    
    /**
     * Get email heading
     */
    public function get_heading() {
        return apply_filters('woocommerce_email_heading_financing_application', $this->format_string($this->get_option('heading', __('New Financing Application', 'cardealership-child'))), $this->object);
    }
    
    /**
     * Trigger the email
     */
    public function trigger($application_id) {
        $this->setup_locale();
        
        if ($application_id) {
            $this->object = get_post($application_id);
            
            if ($this->object) {
                // Get application data
                $first_name = get_post_meta($application_id, 'first_name', true);
                $last_name = get_post_meta($application_id, 'last_name', true);
                $applicant_name = $first_name . ' ' . $last_name;
                $email = get_post_meta($application_id, 'email', true);
                
                // Replace placeholders in subject/heading
                $this->find['applicant_name'] = '{applicant_name}';
                $this->replace['applicant_name'] = $applicant_name;
                
                if (!$this->is_enabled() || !$this->get_recipient()) {
                    return;
                }
                
                // Send email to admin
                $this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());
                
                // Send confirmation to customer
                if (!empty($email)) {
                    $this->customer_email = true;
                    $this->send($email, $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());
                }
            }
        }
        
        $this->restore_locale();
    }
    
    /**
     * Get content html
     */
    public function get_content_html() {
        return wc_get_template_html(
            $this->template_html,
            array(
                'application' => $this->object,
                'application_id' => $this->object->ID,
                'email_heading' => $this->get_heading(),
                'sent_to_admin' => true,
                'plain_text' => false,
                'email' => $this,
            ),
            'woocommerce/',
            $this->template_base
        );
    }
    
    /**
     * Get content plain
     */
    public function get_content_plain() {
        return wc_get_template_html(
            $this->template_plain,
            array(
                'application' => $this->object,
                'application_id' => $this->object->ID,
                'email_heading' => $this->get_heading(),
                'sent_to_admin' => true,
                'plain_text' => true,
                'email' => $this,
            ),
            'woocommerce/',
            $this->template_base
        );
    }
    
    /**
     * Initialize settings form fields
     */
    public function init_form_fields() {
        $this->form_fields = array(
            'enabled' => array(
                'title' => __('Enable/Disable', 'cardealership-child'),
                'type' => 'checkbox',
                'label' => __('Enable this email notification', 'cardealership-child'),
                'default' => 'yes',
            ),
            'recipient' => array(
                'title' => __('Recipient(s)', 'cardealership-child'),
                'type' => 'text',
                'description' => __('Enter recipients (comma separated) for this email. Defaults to admin email.', 'cardealership-child'),
                'placeholder' => get_option('admin_email'),
                'default' => get_option('admin_email'),
                'css' => 'width: 400px;',
            ),
            'subject' => array(
                'title' => __('Subject', 'cardealership-child'),
                'type' => 'text',
                'description' => __('This controls the email subject line. Leave blank to use the default subject: <code>New Financing Application: {applicant_name}</code>.', 'cardealership-child'),
                'placeholder' => __('New Financing Application: {applicant_name}', 'cardealership-child'),
                'default' => '',
                'css' => 'width: 400px;',
            ),
            'heading' => array(
                'title' => __('Email Heading', 'cardealership-child'),
                'type' => 'text',
                'description' => __('This controls the main heading in the email notification. Leave blank to use the default heading: <code>New Financing Application</code>.', 'cardealership-child'),
                'placeholder' => __('New Financing Application', 'cardealership-child'),
                'default' => '',
                'css' => 'width: 400px;',
            ),
            'email_type' => array(
                'title' => __('Email type', 'cardealership-child'),
                'type' => 'select',
                'description' => __('Choose which format of email to send.', 'cardealership-child'),
                'default' => 'html',
                'class' => 'email_type wc-enhanced-select',
                'options' => $this->get_email_type_options(),
            ),
        );
    }
}

/**
 * Vehicle Purchased Email template class
 * Save as: /inc/emails/class-vehicle-purchased-email.php
 */
class WC_Email_Vehicle_Purchased extends WC_Email {
    /**
     * Constructor
     */
    public function __construct() {
        $this->id = 'vehicle_purchased';
        $this->title = __('Vehicle Purchased', 'cardealership-child');
        $this->description = __('Email notification sent when a vehicle is purchased', 'cardealership-child');
        
        $this->template_html = 'emails/vehicle-purchased.php';
        $this->template_plain = 'emails/plain/vehicle-purchased.php';
        $this->template_base = get_stylesheet_directory() . '/woocommerce/';
        
        // Call parent constructor
        parent::__construct();
        
        // Default recipient to admin email
        $this->recipient = $this->get_option('recipient', get_option('admin_email'));
        
        // Triggers
        add_action('woocommerce_order_status_completed', array($this, 'trigger'), 10, 2);
    }
    
    /**
     * Get email subject
     */
    public function get_subject() {
        return apply_filters('woocommerce_email_subject_vehicle_purchased', $this->format_string($this->get_option('subject', __('Vehicle Purchased: {vehicle_title}', 'cardealership-child'))), $this->object);
    }
    
    /**
     * Get email heading
     */
    public function get_heading() {
        return apply_filters('woocommerce_email_heading_vehicle_purchased', $this->format_string($this->get_option('heading', __('Thank You for Your Vehicle Purchase', 'cardealership-child'))), $this->object);
    }
    
    /**
     * Trigger the email
     */
    public function trigger($order_id, $order = false) {
        $this->setup_locale();
        
        if ($order_id && !$order) {
            $order = wc_get_order($order_id);
        }
        
        if (is_a($order, 'WC_Order')) {
            $this->object = $order;
            
            // Check if this order contains a vehicle
            $vehicle_purchased = false;
            $vehicle_title = '';
            
            foreach ($order->get_items() as $item) {
                $product_id = $item->get_product_id();
                $linked_vehicle_id = get_post_meta($product_id, '_linked_vehicle_id', true);
                
                if ($linked_vehicle_id) {
                    $vehicle_purchased = true;
                    $vehicle_title = get_the_title($linked_vehicle_id);
                    break;
                }
            }
            
            // Only send if a vehicle was purchased
            if ($vehicle_purchased) {
                // Replace placeholders in subject/heading
                $this->find['vehicle_title'] = '{vehicle_title}';
                $this->replace['vehicle_title'] = $vehicle_title;
                
                $this->find['order-date'] = '{order_date}';
                $this->replace['order-date'] = wc_format_datetime($this->object->get_date_created());
                
                $this->find['order-number'] = '{order_number}';
                $this->replace['order-number'] = $this->object->get_order_number();
                
                if (!$this->is_enabled() || !$this->get_recipient()) {
                    return;
                }
                
                // Send email to admin
                $this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());
                
                // Send to customer
                $this->customer_email = true;
                $this->send($this->object->get_billing_email(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());
            }
        }
        
        $this->restore_locale();
    }
    
    /**
     * Get content html
     */
    public function get_content_html() {
        return wc_get_template_html(
            $this->template_html,
            array(
                'order' => $this->object,
                'email_heading' => $this->get_heading(),
                'sent_to_admin' => false,
                'plain_text' => false,
                'email' => $this,
            ),
            'woocommerce/',
            $this->template_base
        );
    }
    
    /**
     * Get content plain
     */
    public function get_content_plain() {
        return wc_get_template_html(
            $this->template_plain,
            array(
                'order' => $this->object,
                'email_heading' => $this->get_heading(),
                'sent_to_admin' => false,
                'plain_text' => true,
                'email' => $this,
            ),
            'woocommerce/',
            $this->template_base
        );
    }
    
    /**
     * Initialize settings form fields
     */
    public function init_form_fields() {
        $this->form_fields = array(
            'enabled' => array(
                'title' => __('Enable/Disable', 'cardealership-child'),
                'type' => 'checkbox',
                'label' => __('Enable this email notification', 'cardealership-child'),
                'default' => 'yes',
            ),
            'recipient' => array(
                'title' => __('Admin Recipient(s)', 'cardealership-child'),
                'type' => 'text',
                'description' => __('Enter recipients (comma separated) for this email. Defaults to admin email.', 'cardealership-child'),
                'placeholder' => get_option('admin_email'),
                'default' => get_option('admin_email'),
                'css' => 'width: 400px;',
            ),
            'subject' => array(
                'title' => __('Subject', 'cardealership-child'),
                'type' => 'text',
                'description' => __('This controls the email subject line. Leave blank to use the default subject: <code>Vehicle Purchased: {vehicle_title}</code>.', 'cardealership-child'),
                'placeholder' => __('Vehicle Purchased: {vehicle_title}', 'cardealership-child'),
                'default' => '',
                'css' => 'width: 400px;',
            ),
            'heading' => array(
                'title' => __('Email Heading', 'cardealership-child'),
                'type' => 'text',
                'description' => __('This controls the main heading in the email notification. Leave blank to use the default heading: <code>Thank You for Your Vehicle Purchase</code>.', 'cardealership-child'),
                'placeholder' => __('Thank You for Your Vehicle Purchase', 'cardealership-child'),
                'default' => '',
                'css' => 'width: 400px;',
            ),
            'email_type' => array(
                'title' => __('Email type', 'cardealership-child'),
                'type' => 'select',
                'description' => __('Choose which format of email to send.', 'cardealership-child'),
                'default' => 'html',
                'class' => 'email_type wc-enhanced-select',
                'options' => $this->get_email_type_options(),
            ),
        );
    }
}

/**
 * Create email template files
 */
function cardealership_create_email_templates() {
    // Create directories if they don't exist
    $directories = array(
        get_stylesheet_directory() . '/woocommerce/emails',
        get_stylesheet_directory() . '/woocommerce/emails/plain',
    );
    
    foreach ($directories as $dir) {
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }
    }
    
    // Vehicle Inquiry Email Template
    $vehicle_inquiry_template = get_stylesheet_directory() . '/woocommerce/emails/vehicle-inquiry.php';
    
    if (!file_exists($vehicle_inquiry_template)) {
        $vehicle_inquiry_content = '<?php
/**
 * Vehicle Inquiry Email
 */

if (!defined(\'ABSPATH\')) {
    exit;
}

/**
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action(\'woocommerce_email_header\', $email_heading, $email);
?>

<p><?php printf(__(\'You have received a vehicle inquiry from %s.\', \'cardealership-child\'), $inquiry_data[\'name\']); ?></p>

<h2><?php _e(\'Inquiry Details\', \'cardealership-child\'); ?></h2>

<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; margin-bottom: 20px; border: 1px solid #e5e5e5;">
    <tbody>
        <tr>
            <th class="td" scope="row" style="text-align: left; width: 30%;"><?php _e(\'Vehicle:\', \'cardealership-child\'); ?></th>
            <td class="td" style="text-align: left;"><?php echo esc_html($inquiry_data[\'vehicle_title\']); ?></td>
        </tr>
        <tr>
            <th class="td" scope="row" style="text-align: left;"><?php _e(\'Name:\', \'cardealership-child\'); ?></th>
            <td class="td" style="text-align: left;"><?php echo esc_html($inquiry_data[\'name\']); ?></td>
        </tr>
        <tr>
            <th class="td" scope="row" style="text-align: left;"><?php _e(\'Email:\', \'cardealership-child\'); ?></th>
            <td class="td" style="text-align: left;"><?php echo esc_html($inquiry_data[\'email\']); ?></td>
        </tr>
        <?php if (!empty($inquiry_data[\'phone\'])) : ?>
        <tr>
            <th class="td" scope="row" style="text-align: left;"><?php _e(\'Phone:\', \'cardealership-child\'); ?></th>
            <td class="td" style="text-align: left;"><?php echo esc_html($inquiry_data[\'phone\']); ?></td>
        </tr>
        <?php endif; ?>
        <?php if (!empty($inquiry_data[\'message\'])) : ?>
        <tr>
            <th class="td" scope="row" style="text-align: left; vertical-align: top;"><?php _e(\'Message:\', \'cardealership-child\'); ?></th>
            <td class="td" style="text-align: left;"><?php echo wpautop(wptexturize(esc_html($inquiry_data[\'message\']))); ?></td>
        </tr>
        <?php endif; ?>
        <?php if (!empty($inquiry_data[\'financing\']) && $inquiry_data[\'financing\']) : ?>
        <tr>
            <th class="td" scope="row" style="text-align: left;"><?php _e(\'Financing:\', \'cardealership-child\'); ?></th>
            <td class="td" style="text-align: left;"><?php _e(\'Customer is interested in financing options\', \'cardealership-child\'); ?></td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php if (!$sent_to_admin) : ?>
<p><?php _e(\'Thank you for your inquiry. Our team will contact you shortly.\', \'cardealership-child\'); ?></p>
<?php endif; ?>

<?php
/**
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action(\'woocommerce_email_footer\', $email);';
        
        file_put_contents($vehicle_inquiry_template, $vehicle_inquiry_content);
    }
    
    // Vehicle Inquiry Plain Email Template
    $vehicle_inquiry_plain_template = get_stylesheet_directory() . '/woocommerce/emails/plain/vehicle-inquiry.php';
    
    if (!file_exists($vehicle_inquiry_plain_template)) {
        $vehicle_inquiry_plain_content = '<?php
/**
 * Vehicle Inquiry Email (plain text)
 */

if (!defined(\'ABSPATH\')) {
    exit;
}

echo "= " . $email_heading . " =\\n\\n";

echo sprintf(__(\'You have received a vehicle inquiry from %s.\', \'cardealership-child\'), $inquiry_data[\'name\']) . "\\n\\n";

echo "= " . __(\'Inquiry Details\', \'cardealership-child\') . " =\\n\\n";

echo __(\'Vehicle:\', \'cardealership-child\') . " " . $inquiry_data[\'vehicle_title\'] . "\\n";
echo __(\'Name:\', \'cardealership-child\') . " " . $inquiry_data[\'name\'] . "\\n";
echo __(\'Email:\', \'cardealership-child\') . " " . $inquiry_data[\'email\'] . "\\n";

if (!empty($inquiry_data[\'phone\'])) {
    echo __(\'Phone:\', \'cardealership-child\') . " " . $inquiry_data[\'phone\'] . "\\n";
}

if (!empty($inquiry_data[\'message\'])) {
    echo __(\'Message:\', \'cardealership-child\') . "\\n" . $inquiry_data[\'message\'] . "\\n\\n";
}

if (!empty($inquiry_data[\'financing\']) && $inquiry_data[\'financing\']) {
    echo __(\'Financing:\', \'cardealership-child\') . " " . __(\'Customer is interested in financing options\', \'cardealership-child\') . "\\n";
}

if (!$sent_to_admin) {
    echo __(\'Thank you for your inquiry. Our team will contact you shortly.\', \'cardealership-child\') . "\\n\\n";
}

echo apply_filters(\'woocommerce_email_footer_text\', get_option(\'woocommerce_email_footer_text\', \'\'));';
        
        file_put_contents($vehicle_inquiry_plain_template, $vehicle_inquiry_plain_content);
    }
    
    // Financing Application Email Template
    $financing_template = get_stylesheet_directory() . '/woocommerce/emails/financing-application.php';
    
    if (!file_exists($financing_template)) {
        $financing_content = '<?php
/**
 * Financing Application Email
 */

if (!defined(\'ABSPATH\')) {
    exit;
}

/**
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action(\'woocommerce_email_header\', $email_heading, $email);

$first_name = get_post_meta($application_id, \'first_name\', true);
$last_name = get_post_meta($application_id, \'last_name\', true);
$email_address = get_post_meta($application_id, \'email\', true);
$phone = get_post_meta($application_id, \'phone\', true);
$monthly_income = get_post_meta($application_id, \'monthly_income\', true);
$down_payment = get_post_meta($application_id, \'down_payment\', true);
$loan_term = get_post_meta($application_id, \'loan_term\', true);
?>

<?php if ($sent_to_admin) : ?>
<p><?php printf(__(\'A new financing application has been submitted by %s %s.\', \'cardealership-child\'), $first_name, $last_name); ?></p>
<?php else : ?>
<p><?php printf(__(\'Dear %s,\', \'cardealership-child\'), $first_name); ?></p>
<p><?php _e(\'Thank you for submitting your financing application. Our team will review your information and contact you shortly.\', \'cardealership-child\'); ?></p>
<?php endif; ?>

<h2><?php _e(\'Application Details\', \'cardealership-child\'); ?></h2>

<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; margin-bottom: 20px; border: 1px solid #e5e5e5;">
    <tbody>
        <tr>
            <th class="td" scope="row" style="text-align: left; width: 30%;"><?php _e(\'Application ID:\', \'cardealership-child\'); ?></th>
            <td class="td" style="text-align: left;"><?php echo esc_html(\'FIN-\' . $application_id); ?></td>
        </tr>
        <tr>
            <th class="td" scope="row" style="text-align: left;"><?php _e(\'Name:\', \'cardealership-child\'); ?></th>
            <td class="td" style="text-align: left;"><?php echo esc_html($first_name . \' \' . $last_name); ?></td>
        </tr>
        <tr>
            <th class="td" scope="row" style="text-align: left;"><?php _e(\'Email:\', \'cardealership-child\'); ?></th>
            <td class="td" style="text-align: left;"><?php echo esc_html($email_address); ?></td>
        </tr>
        <tr>
            <th class="td" scope="row" style="text-align: left;"><?php _e(\'Phone:\', \'cardealership-child\'); ?></th>
            <td class="td" style="text-align: left;"><?php echo esc_html($phone); ?></td>
        </tr>
        <tr>
            <th class="td" scope="row" style="text-align: left;"><?php _e(\'Monthly Income:\', \'cardealership-child\'); ?></th>
            <td class="td" style="text-align: left;"><?php echo \'GYD \' . number_format($monthly_income); ?></td>
        </tr>
        <?php if (!empty($down_payment)) : ?>
        <tr>
            <th class="td" scope="row" style="text-align: left;"><?php _e(\'Down Payment:\', \'cardealership-child\'); ?></th>
            <td class="td" style="text-align: left;"><?php echo \'GYD \' . number_format($down_payment); ?></td>
        </tr>
        <?php endif; ?>
        <?php if (!empty($loan_term)) : ?>
        <tr>
            <th class="td" scope="row" style="text-align: left;"><?php _e(\'Requested Loan Term:\', \'cardealership-child\'); ?></th>
            <td class="td" style="text-align: left;"><?php echo esc_html($loan_term) . \' \' . __("months", "cardealership-child"); ?></td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php if (!$sent_to_admin) : ?>
<p><?php _e(\'If you have any questions, please contact our financing department.\', \'cardealership-child\'); ?></p>
<?php endif; ?>

<?php
/**
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action(\'woocommerce_email_footer\', $email);';
        
        file_put_contents($financing_template, $financing_content);
    }
    
    // Financing Application Plain Email Template
    $financing_plain_template = get_stylesheet_directory() . '/woocommerce/emails/plain/financing-application.php';
    
    if (!file_exists($financing_plain_template)) {
        $financing_plain_content = '<?php
/**
 * Financing Application Email (plain text)
 */

if (!defined(\'ABSPATH\')) {
    exit;
}

echo "= " . $email_heading . " =\\n\\n";

$first_name = get_post_meta($application_id, \'first_name\', true);
$last_name = get_post_meta($application_id, \'last_name\', true);
$email_address = get_post_meta($application_id, \'email\', true);
$phone = get_post_meta($application_id, \'phone\', true);
$monthly_income = get_post_meta($application_id, \'monthly_income\', true);
$down_payment = get_post_meta($application_id, \'down_payment\', true);
$loan_term = get_post_meta($application_id, \'loan_term\', true);

if ($sent_to_admin) {
    echo sprintf(__(\'A new financing application has been submitted by %s %s.\', \'cardealership-child\'), $first_name, $last_name) . "\\n\\n";
} else {
    echo sprintf(__(\'Dear %s,\', \'cardealership-child\'), $first_name) . "\\n\\n";
    echo __(\'Thank you for submitting your financing application. Our team will review your information and contact you shortly.\', \'cardealership-child\') . "\\n\\n";
}

echo "= " . __(\'Application Details\', \'cardealership-child\') . " =\\n\\n";

echo __(\'Application ID:\', \'cardealership-child\') . " FIN-" . $application_id . "\\n";
echo __(\'Name:\', \'cardealership-child\') . " " . $first_name . " " . $last_name . "\\n";
echo __(\'Email:\', \'cardealership-child\') . " " . $email_address . "\\n";
echo __(\'Phone:\', \'cardealership-child\') . " " . $phone . "\\n";
echo __(\'Monthly Income:\', \'cardealership-child\') . " GYD " . number_format($monthly_income) . "\\n";

if (!empty($down_payment)) {
    echo __(\'Down Payment:\', \'cardealership-child\') . " GYD " . number_format($down_payment) . "\\n";
}

if (!empty($loan_term)) {
    echo __(\'Requested Loan Term:\', \'cardealership-child\') . " " . $loan_term . " " . __("months", "cardealership-child") . "\\n";
}

if (!$sent_to_admin) {
    echo "\\n" . __(\'If you have any questions, please contact our financing department.\', \'cardealership-child\') . "\\n\\n";
}

echo apply_filters(\'woocommerce_email_footer_text\', get_option(\'woocommerce_email_footer_text\', \'\'));';
        
        file_put_contents($financing_plain_template, $financing_plain_content);
    }
    
    // Vehicle Purchased Email Template
    $vehicle_purchased_template = get_stylesheet_directory() . '/woocommerce/emails/vehicle-purchased.php';
    
    if (!file_exists($vehicle_purchased_template)) {
        $vehicle_purchased_content = '<?php
/**
 * Vehicle Purchased Email
 */

if (!defined(\'ABSPATH\')) {
    exit;
}

/**
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action(\'woocommerce_email_header\', $email_heading, $email);

// Find vehicle in order items
$vehicle_title = \'\';
$vehicle_id = 0;
$linked_vehicle_id = 0;

foreach ($order->get_items() as $item) {
    $product_id = $item->get_product_id();
    $linked_vehicle_id = get_post_meta($product_id, \'_linked_vehicle_id\', true);
    
    if ($linked_vehicle_id) {
        $vehicle_id = $product_id;
        $vehicle_title = get_the_title($linked_vehicle_id);
        break;
    }
}

// Get vehicle details
$year = get_post_meta($linked_vehicle_id, \'_vehicle_year\', true);
$make_terms = wp_get_post_terms($linked_vehicle_id, \'vehicle_make\');
$model_terms = wp_get_post_terms($linked_vehicle_id, \'vehicle_model\');
$vin = get_post_meta($linked_vehicle_id, \'_vehicle_vin\', true);

$make = !empty($make_terms) ? $make_terms[0]->name : \'\';
$model = !empty($model_terms) ? $model_terms[0]->name : \'\';

// Get financing info
$financing_order = get_post_meta($order->get_id(), \'_financing_order\', true);
?>

<?php if ($sent_to_admin) : ?>
<p><?php printf(__(\'%s has purchased a vehicle: %s\', \'cardealership-child\'), $order->get_formatted_billing_full_name(), $vehicle_title); ?></p>
<?php else : ?>
<p><?php printf(__(\'Dear %s,\', \'cardealership-child\'), $order->get_formatted_billing_full_name()); ?></p>
<p><?php _e(\'Thank you for your vehicle purchase! We\'re delighted to confirm your order.\', \'cardealership-child\'); ?></p>
<?php endif; ?>

<h2><?php _e(\'Order Details\', \'cardealership-child\'); ?></h2>

<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; margin-bottom: 20px; border: 1px solid #e5e5e5;">
    <tbody>
        <tr>
            <th class="td" scope="row" style="text-align: left; width: 30%;"><?php _e(\'Order Number:\', \'cardealership-child\'); ?></th>
            <td class="td" style="text-align: left;"><?php echo $order->get_order_number(); ?></td>
        </tr>
        <tr>
            <th class="td" scope="row" style="text-align: left;"><?php _e(\'Order Date:\', \'cardealership-child\'); ?></th>
            <td class="td" style="text-align: left;"><?php echo wc_format_datetime($order->get_date_created()); ?></td>
        </tr>
        <tr>
            <th class="td" scope="row" style="text-align: left;"><?php _e(\'Vehicle:\', \'cardealership-child\'); ?></th>
            <td class="td" style="text-align: left;"><?php echo esc_html($vehicle_title); ?></td>
        </tr>
        <tr>
            <th class="td" scope="row" style="text-align: left;"><?php _e(\'Total:\', \'cardealership-child\'); ?></th>
            <td class="td" style="text-align: left;"><?php echo $order->get_formatted_order_total(); ?></td>
        </tr>
        <?php if ($financing_order === \'yes\') : ?>
        <tr>
            <th class="td" scope="row" style="text-align: left;"><?php _e(\'Financing:\', \'cardealership-child\'); ?></th>
            <td class="td" style="text-align: left;"><?php _e(\'Yes (Deposit Payment)\', \'cardealership-child\'); ?></td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>

<h2><?php _e(\'Vehicle Information\', \'cardealership-child\'); ?></h2>

<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; margin-bottom: 20px; border: 1px solid #e5e5e5;">
    <tbody>
        <?php if ($year) : ?>
        <tr>
            <th class="td" scope="row" style="text-align: left; width: 30%;"><?php _e(\'Year:\', \'cardealership-child\'); ?></th>
            <td class="td" style="text-align: left;"><?php echo esc_html($year); ?></td>
        </tr>
        <?php endif; ?>
        <?php if ($make) : ?>
        <tr>
            <th class="td" scope="row" style="text-align: left;"><?php _e(\'Make:\', \'cardealership-child\'); ?></th>
            <td class="td" style="text-align: left;"><?php echo esc_html($make); ?></td>
        </tr>
        <?php endif; ?>
        <?php if ($model) : ?>
        <tr>
            <th class="td" scope="row" style="text-align: left;"><?php _e(\'Model:\', \'cardealership-child\'); ?></th>
            <td class="td" style="text-align: left;"><?php echo esc_html($model); ?></td>
        </tr>
        <?php endif; ?>
        <?php if ($vin) : ?>
        <tr>
            <th class="td" scope="row" style="text-align: left;"><?php _e(\'VIN:\', \'cardealership-child\'); ?></th>
            <td class="td" style="text-align: left;"><?php echo esc_html($vin); ?></td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php if (!$sent_to_admin) : ?>
    <?php if ($financing_order === \'yes\') : ?>
        <h2><?php _e(\'Next Steps\', \'cardealership-child\'); ?></h2>
        <p><?php _e(\'Our financing team will contact you shortly to complete the financing process and schedule vehicle delivery.\', \'cardealership-child\'); ?></p>
    <?php else : ?>
        <h2><?php _e(\'Next Steps\', \'cardealership-child\'); ?></h2>
        <p><?php _e(\'Our sales team will contact you shortly to arrange the vehicle delivery or pickup.\', \'cardealership-child\'); ?></p>
    <?php endif; ?>
    
    <p><?php _e(\'If you have any questions about your order, please contact us.\', \'cardealership-child\'); ?></p>
<?php endif; ?>

<?php
/**
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action(\'woocommerce_email_footer\', $email);';
        
        file_put_contents($vehicle_purchased_template, $vehicle_purchased_content);
    }
    
    // Vehicle Purchased Plain Email Template
    $vehicle_purchased_plain_template = get_stylesheet_directory() . '/woocommerce/emails/plain/vehicle-purchased.php';
    
    if (!file_exists($vehicle_purchased_plain_template)) {
        $vehicle_purchased_plain_content = '<?php
/**
 * Vehicle Purchased Email (plain text)
 */

if (!defined(\'ABSPATH\')) {
    exit;
}

echo "= " . $email_heading . " =\\n\\n";

// Find vehicle in order items
$vehicle_title = \'\';
$vehicle_id = 0;
$linked_vehicle_id = 0;

foreach ($order->get_items() as $item) {
    $product_id = $item->get_product_id();
    $linked_vehicle_id = get_post_meta($product_id, \'_linked_vehicle_id\', true);
    
    if ($linked_vehicle_id) {
        $vehicle_id = $product_id;
        $vehicle_title = get_the_title($linked_vehicle_id);
        break;
    }
}

// Get vehicle details
$year = get_post_meta($linked_vehicle_id, \'_vehicle_year\', true);
$make_terms = wp_get_post_terms($linked_vehicle_id, \'vehicle_make\');
$model_terms = wp_get_post_terms($linked_vehicle_id, \'vehicle_model\');
$vin = get_post_meta($linked_vehicle_id, \'_vehicle_vin\', true);

$make = !empty($make_terms) ? $make_terms[0]->name : \'\';
$model = !empty($model_terms) ? $model_terms[0]->name : \'\';

// Get financing info
$financing_order = get_post_meta($order->get_id(), \'_financing_order\', true);

if ($sent_to_admin) {
    echo sprintf(__(\'%s has purchased a vehicle: %s\', \'cardealership-child\'), $order->get_formatted_billing_full_name(), $vehicle_title) . "\\n\\n";
} else {
    echo sprintf(__(\'Dear %s,\', \'cardealership-child\'), $order->get_formatted_billing_full_name()) . "\\n\\n";
    echo __(\'Thank you for your vehicle purchase! We\'re delighted to confirm your order.\', \'cardealership-child\') . "\\n\\n";
}

echo "= " . __(\'Order Details\', \'cardealership-child\') . " =\\n\\n";

echo __(\'Order Number:\', \'cardealership-child\') . " " . $order->get_order_number() . "\\n";
echo __(\'Order Date:\', \'cardealership-child\') . " " . wc_format_datetime($order->get_date_created()) . "\\n";
echo __(\'Vehicle:\', \'cardealership-child\') . " " . $vehicle_title . "\\n";
echo __(\'Total:\', \'cardealership-child\') . " " . $order->get_formatted_order_total() . "\\n";

if ($financing_order === \'yes\') {
    echo __(\'Financing:\', \'cardealership-child\') . " " . __("Yes (Deposit Payment)", "cardealership-child") . "\\n";
}

echo "\\n= " . __(\'Vehicle Information\', \'cardealership-child\') . " =\\n\\n";

if ($year) {
    echo __(\'Year:\', \'cardealership-child\') . " " . $year . "\\n";
}

if ($make) {
    echo __(\'Make:\', \'cardealership-child\') . " " . $make . "\\n";
}

if ($model) {
    echo __(\'Model:\', \'cardealership-child\') . " " . $model . "\\n";
}

if ($vin) {
    echo __(\'VIN:\', \'cardealership-child\') . " " . $vin . "\\n";
}

if (!$sent_to_admin) {
    echo "\\n= " . __(\'Next Steps\', \'cardealership-child\') . " =\\n\\n";
    
    if ($financing_order === \'yes\') {
        echo __("Our financing team will contact you shortly to complete the financing process and schedule vehicle delivery.", "cardealership-child") . "\\n\\n";
    } else {
        echo __("Our sales team will contact you shortly to arrange the vehicle delivery or pickup.", "cardealership-child") . "\\n\\n";
    }
    
    echo __("If you have any questions about your order, please contact us.", "cardealership-child") . "\\n\\n";
}

echo apply_filters(\'woocommerce_email_footer_text\', get_option(\'woocommerce_email_footer_text\', \'\'));';
        
        file_put_contents($vehicle_purchased_plain_template, $vehicle_purchased_plain_content);
    }
}
add_action('after_setup_theme', 'cardealership_create_email_templates');

/**
 * Trigger vehicle inquiry email notification
 */
function cardealership_trigger_vehicle_inquiry_email($inquiry_data) {
    if (empty($inquiry_data)) {
        return;
    }
    
    // Get WooCommerce mailer
    $mailer = WC()->mailer();
    
    // Get vehicle inquiry email instance
    $emails = $mailer->get_emails();
    $vehicle_inquiry_email = isset($emails['WC_Email_Vehicle_Inquiry']) ? $emails['WC_Email_Vehicle_Inquiry'] : null;
    
    if ($vehicle_inquiry_email) {
        $vehicle_inquiry_email->trigger($inquiry_data);
    }
}

/**
 * Trigger financing application email
 */
function cardealership_trigger_financing_application_email($application_id) {
    if (!$application_id) {
        return;
    }
    
    // Get WooCommerce mailer
    $mailer = WC()->mailer();
    
    // Get financing application email instance
    $emails = $mailer->get_emails();
    $financing_email = isset($emails['WC_Email_Financing_Application']) ? $emails['WC_Email_Financing_Application'] : null;
    
    if ($financing_email) {
        $financing_email->trigger($application_id);
    }
}

/**
 * Modify the original form submission handler to use the email notification system
 */
function cardealership_submit_vehicle_inquiry() {
    if (isset($_POST['action']) && $_POST['action'] === 'submit_vehicle_inquiry') {
        // Verify nonce
        if (!isset($_POST['vehicle_inquiry_nonce']) || !wp_verify_nonce($_POST['vehicle_inquiry_nonce'], 'vehicle_inquiry_nonce')) {
            wp_die(__('Security check failed. Please try again.', 'cardealership-child'));
        }
        
        // Get form data
        $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : '';
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
        $message = isset($_POST['message']) ? sanitize_textarea_field($_POST['message']) : '';
        $vehicle_id = isset($_POST['vehicle_id']) ? intval($_POST['vehicle_id']) : 0;
        $vehicle_title = isset($_POST['vehicle_title']) ? sanitize_text_field($_POST['vehicle_title']) : '';
        $financing = isset($_POST['financing']) ? true : false;
        
        // Basic validation
        if (empty($name) || empty($email) || empty($vehicle_id)) {
            wp_die(__('Please fill in all required fields.', 'cardealership-child'));
        }
        
        // Prepare inquiry data
        $inquiry_data = array(
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'message' => $message,
            'vehicle_id' => $vehicle_id,
            'vehicle_title' => $vehicle_title,
            'financing' => $financing,
            'date' => current_time('mysql'),
        );
        
        // Store inquiry in the database (optional)
        $inquiry_post = array(
            'post_title' => sprintf(__('Inquiry from %s: %s', 'cardealership-child'), $name, $vehicle_title),
            'post_content' => $message,
            'post_status' => 'private',
            'post_type' => 'vehicle_inquiry',
        );
        
        $inquiry_id = wp_insert_post($inquiry_post);
        
        if ($inquiry_id) {
            update_post_meta($inquiry_id, '_inquiry_name', $name);
            update_post_meta($inquiry_id, '_inquiry_email', $email);
            update_post_meta($inquiry_id, '_inquiry_phone', $phone);
            update_post_meta($inquiry_id, '_inquiry_vehicle_id', $vehicle_id);
            update_post_meta($inquiry_id, '_inquiry_vehicle_title', $vehicle_title);
            update_post_meta($inquiry_id, '_inquiry_financing', $financing);
        }
        
        // Trigger email notification
        cardealership_trigger_vehicle_inquiry_email($inquiry_data);
        
        // Redirect back with success message
        wp_safe_redirect(add_query_arg('inquiry_sent', '1', get_permalink($vehicle_id)));
        exit;
    }
}
add_action('init', 'cardealership_submit_vehicle_inquiry');

/**
 * Modify the original financing application submission handler to use the email notification system
 */
function cardealership_modify_financing_application_handler() {
    // This function will be called after the original handler
    // We just need to trigger the email notification for newly created applications
    
    if (isset($_POST['submit_financing_application']) && isset($_POST['financing_application_nonce']) && wp_verify_nonce($_POST['financing_application_nonce'], 'submit_financing_application')) {
        // Check if we have a pending application ID in the session
        $application_id = WC()->session->get('new_financing_application_id');
        
        if ($application_id) {
            // Trigger the email notification
            cardealership_trigger_financing_application_email($application_id);
            
            // Clear the session variable
            WC()->session->__unset('new_financing_application_id');
        }
    }
}
add_action('template_redirect', 'cardealership_modify_financing_application_handler', 20);

/**
 * Store application ID in session for email notification
 */
function cardealership_store_application_id($post_id) {
    // Check if this is a financing application post
    if (get_post_type($post_id) === 'financing_app') {
        // Store the ID in the session for later use
        WC()->session->set('new_financing_application_id', $post_id);
    }
}
add_action('wp_insert_post', 'cardealership_store_application_id');

/**
 * Register Vehicle Inquiry post type
 */
function cardealership_register_vehicle_inquiry_post_type() {
    $labels = array(
        'name'               => _x('Vehicle Inquiries', 'post type general name', 'cardealership-child'),
        'singular_name'      => _x('Vehicle Inquiry', 'post type singular name', 'cardealership-child'),
        'menu_name'          => _x('Inquiries', 'admin menu', 'cardealership-child'),
        'name_admin_bar'     => _x('Vehicle Inquiry', 'add new on admin bar', 'cardealership-child'),
        'add_new'            => _x('Add New', 'inquiry', 'cardealership-child'),
        'add_new_item'       => __('Add New Inquiry', 'cardealership-child'),
        'new_item'           => __('New Inquiry', 'cardealership-child'),
        'edit_item'          => __('Edit Inquiry', 'cardealership-child'),
        'view_item'          => __('View Inquiry', 'cardealership-child'),
        'all_items'          => __('All Inquiries', 'cardealership-child'),
        'search_items'       => __('Search Inquiries', 'cardealership-child'),
        'parent_item_colon'  => __('Parent Inquiries:', 'cardealership-child'),
        'not_found'          => __('No inquiries found.', 'cardealership-child'),
        'not_found_in_trash' => __('No inquiries found in Trash.', 'cardealership-child')
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __('Vehicle inquiries from customers', 'cardealership-child'),
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'vehicle-inquiry'),
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => 6,
        'menu_icon'          => 'dashicons-email-alt',
        'supports'           => array('title', 'editor'),
    );

    register_post_type('vehicle_inquiry', $args);
}
add_action('init', 'cardealership_register_vehicle_inquiry_post_type');

/**
 * Add Vehicle Inquiry Meta Box
 */
function cardealership_add_inquiry_meta_box() {
    add_meta_box(
        'vehicle_inquiry_details',
        __('Inquiry Details', 'cardealership-child'),
        'cardealership_inquiry_meta_box_callback',
        'vehicle_inquiry',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'cardealership_add_inquiry_meta_box');

/**
 * Vehicle Inquiry Meta Box Callback
 */
function cardealership_inquiry_meta_box_callback($post) {
    $name = get_post_meta($post->ID, '_inquiry_name', true);
    $email = get_post_meta($post->ID, '_inquiry_email', true);
    $phone = get_post_meta($post->ID, '_inquiry_phone', true);
    $vehicle_id = get_post_meta($post->ID, '_inquiry_vehicle_id', true);
    $vehicle_title = get_post_meta($post->ID, '_inquiry_vehicle_title', true);
    $financing = get_post_meta($post->ID, '_inquiry_financing', true);
    
    ?>
    <table class="form-table">
        <tr>
            <th><label for="inquiry_name"><?php _e('Name:', 'cardealership-child'); ?></label></th>
            <td><?php echo esc_html($name); ?></td>
        </tr>
        <tr>
            <th><label for="inquiry_email"><?php _e('Email:', 'cardealership-child'); ?></label></th>
            <td><?php echo esc_html($email); ?></td>
        </tr>
        <tr>
            <th><label for="inquiry_phone"><?php _e('Phone:', 'cardealership-child'); ?></label></th>
            <td><?php echo esc_html($phone); ?></td>
        </tr>
        <tr>
            <th><label for="inquiry_vehicle"><?php _e('Vehicle:', 'cardealership-child'); ?></label></th>
            <td>
                <?php echo esc_html($vehicle_title); ?>
                <?php if ($vehicle_id) : ?>
                    (<a href="<?php echo get_edit_post_link($vehicle_id); ?>"><?php _e('Edit Vehicle', 'cardealership-child'); ?></a>)
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th><label for="inquiry_financing"><?php _e('Financing:', 'cardealership-child'); ?></label></th>
            <td><?php echo $financing ? __('Yes', 'cardealership-child') : __('No', 'cardealership-child'); ?></td>
        </tr>
    </table>
    <?php
}
