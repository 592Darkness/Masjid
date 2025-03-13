<?php
/**
 * Template Name: Financing Application
 * 
 * Create this file as page-financing-application.php in your child theme
 */
get_header();
?>

<div class="financing-application-container">
    <div class="page-header">
        <h1 class="page-title"><?php the_title(); ?></h1>
    </div>
    
    <div class="page-content">
        <?php
        // Display page content first
        while (have_posts()) :
            the_post();
            the_content();
        endwhile;
        ?>
        
        <div class="financing-process">
            <h2><?php _e('Financing Process', 'cardealership-child'); ?></h2>
            
            <div class="process-steps">
                <div class="process-step">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <h3><?php _e('Complete Application', 'cardealership-child'); ?></h3>
                        <p><?php _e('Fill out our secure financing application form.', 'cardealership-child'); ?></p>
                    </div>
                </div>
                
                <div class="process-step">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <h3><?php _e('Document Submission', 'cardealership-child'); ?></h3>
                        <p><?php _e('Submit required documents to verify your information.', 'cardealership-child'); ?></p>
                    </div>
                </div>
                
                <div class="process-step">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <h3><?php _e('Approval Process', 'cardealership-child'); ?></h3>
                        <p><?php _e('Our financing team reviews your application.', 'cardealership-child'); ?></p>
                    </div>
                </div>
                
                <div class="process-step">
                    <div class="step-number">4</div>
                    <div class="step-content">
                        <h3><?php _e('Vehicle Selection', 'cardealership-child'); ?></h3>
                        <p><?php _e('Choose your vehicle from our inventory.', 'cardealership-child'); ?></p>
                    </div>
                </div>
                
                <div class="process-step">
                    <div class="step-number">5</div>
                    <div class="step-content">
                        <h3><?php _e('Drive Away', 'cardealership-child'); ?></h3>
                        <p><?php _e('Complete the paperwork and drive away in your new vehicle.', 'cardealership-child'); ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="required-documents">
            <h2><?php _e('Required Documents', 'cardealership-child'); ?></h2>
            
            <ul class="documents-list">
                <li><?php _e('Valid National ID Card', 'cardealership-child'); ?></li>
                <li><?php _e('Valid Driver\'s License', 'cardealership-child'); ?></li>
                <li><?php _e('Proof of Address (utility bill dated within last 3 months)', 'cardealership-child'); ?></li>
                <li><?php _e('Proof of Income (last 3 pay stubs or 6 months bank statements)', 'cardealership-child'); ?></li>
                <li><?php _e('TIN Certificate', 'cardealership-child'); ?></li>
                <li><?php _e('Employment Letter (if applicable)', 'cardealership-child'); ?></li>
                <li><?php _e('Business Registration (if self-employed)', 'cardealership-child'); ?></li>
            </ul>
        </div>
        
        <div id="financing-application-form" class="financing-application-form">
            <h2><?php _e('Financing Application Form', 'cardealership-child'); ?></h2>
            
            <?php
            // Success message if form was submitted
            if (isset($_GET['application_submitted']) && $_GET['application_submitted'] == '1') {
                echo '<div class="form-success-message">';
                echo '<h3>' . __('Application Submitted Successfully!', 'cardealership-child') . '</h3>';
                echo '<p>' . __('Thank you for submitting your financing application. Our team will review your information and contact you within 1-2 business days.', 'cardealership-child') . '</p>';
                echo '</div>';
            } else {
                // Display the form
                ?>
                <form method="post" class="application-form" enctype="multipart/form-data">
                    <?php wp_nonce_field('submit_financing_application', 'financing_application_nonce'); ?>
                    
                    <div class="form-section">
                        <h3><?php _e('Personal Information', 'cardealership-child'); ?></h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="first_name"><?php _e('First Name', 'cardealership-child'); ?> <span class="required">*</span></label>
                                <input type="text" id="first_name" name="first_name" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="last_name"><?php _e('Last Name', 'cardealership-child'); ?> <span class="required">*</span></label>
                                <input type="text" id="last_name" name="last_name" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="date_of_birth"><?php _e('Date of Birth', 'cardealership-child'); ?> <span class="required">*</span></label>
                                <input type="date" id="date_of_birth" name="date_of_birth" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="national_id"><?php _e('National ID Number', 'cardealership-child'); ?> <span class="required">*</span></label>
                                <input type="text" id="national_id" name="national_id" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="email"><?php _e('Email Address', 'cardealership-child'); ?> <span class="required">*</span></label>
                                <input type="email" id="email" name="email" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="phone"><?php _e('Phone Number', 'cardealership-child'); ?> <span class="required">*</span></label>
                                <input type="tel" id="phone" name="phone" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3><?php _e('Current Address', 'cardealership-child'); ?></h3>
                        
                        <div class="form-row">
                            <div class="form-group full-width">
                                <label for="street_address"><?php _e('Street Address', 'cardealership-child'); ?> <span class="required">*</span></label>
                                <input type="text" id="street_address" name="street_address" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="city"><?php _e('City/Town', 'cardealership-child'); ?> <span class="required">*</span></label>
                                <input type="text" id="city" name="city" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="region"><?php _e('Region', 'cardealership-child'); ?> <span class="required">*</span></label>
                                <select id="region" name="region" required>
                                    <option value=""><?php _e('Select Region', 'cardealership-child'); ?></option>
                                    <option value="Barima-Waini"><?php _e('Barima-Waini (Region 1)', 'cardealership-child'); ?></option>
                                    <option value="Pomeroon-Supenaam"><?php _e('Pomeroon-Supenaam (Region 2)', 'cardealership-child'); ?></option>
                                    <option value="Essequibo Islands-West Demerara"><?php _e('Essequibo Islands-West Demerara (Region 3)', 'cardealership-child'); ?></option>
                                    <option value="Demerara-Mahaica"><?php _e('Demerara-Mahaica (Region 4)', 'cardealership-child'); ?></option>
                                    <option value="Mahaica-Berbice"><?php _e('Mahaica-Berbice (Region 5)', 'cardealership-child'); ?></option>
                                    <option value="East Berbice-Corentyne"><?php _e('East Berbice-Corentyne (Region 6)', 'cardealership-child'); ?></option>
                                    <option value="Cuyuni-Mazaruni"><?php _e('Cuyuni-Mazaruni (Region 7)', 'cardealership-child'); ?></option>
                                    <option value="Potaro-Siparuni"><?php _e('Potaro-Siparuni (Region 8)', 'cardealership-child'); ?></option>
                                    <option value="Upper Takutu-Upper Essequibo"><?php _e('Upper Takutu-Upper Essequibo (Region 9)', 'cardealership-child'); ?></option>
                                    <option value="Upper Demerara-Berbice"><?php _e('Upper Demerara-Berbice (Region 10)', 'cardealership-child'); ?></option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="residence_years"><?php _e('Years at Current Address', 'cardealership-child'); ?> <span class="required">*</span></label>
                                <input type="number" id="residence_years" name="residence_years" min="0" step="0.1" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="residence_status"><?php _e('Residence Status', 'cardealership-child'); ?> <span class="required">*</span></label>
                                <select id="residence_status" name="residence_status" required>
                                    <option value=""><?php _e('Select Status', 'cardealership-child'); ?></option>
                                    <option value="own"><?php _e('Own', 'cardealership-child'); ?></option>
                                    <option value="rent"><?php _e('Rent', 'cardealership-child'); ?></option>
                                    <option value="living_with_family"><?php _e('Living with Family', 'cardealership-child'); ?></option>
                                    <option value="other"><?php _e('Other', 'cardealership-child'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3><?php _e('Employment Information', 'cardealership-child'); ?></h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="employment_status"><?php _e('Employment Status', 'cardealership-child'); ?> <span class="required">*</span></label>
                                <select id="employment_status" name="employment_status" required>
                                    <option value=""><?php _e('Select Status', 'cardealership-child'); ?></option>
                                    <option value="employed_full_time"><?php _e('Employed Full-Time', 'cardealership-child'); ?></option>
                                    <option value="employed_part_time"><?php _e('Employed Part-Time', 'cardealership-child'); ?></option>
                                    <option value="self_employed"><?php _e('Self-Employed', 'cardealership-child'); ?></option>
                                    <option value="retired"><?php _e('Retired', 'cardealership-child'); ?></option>
                                    <option value="student"><?php _e('Student', 'cardealership-child'); ?></option>
                                    <option value="unemployed"><?php _e('Unemployed', 'cardealership-child'); ?></option>
                                </select>
                            </div>
                            
                            <div class="form-group employer-field">
                                <label for="employer_name"><?php _e('Employer Name', 'cardealership-child'); ?> <span class="required">*</span></label>
                                <input type="text" id="employer_name" name="employer_name">
                            </div>
                        </div>
                        
                        <div class="form-row employer-fields">
                            <div class="form-group">
                                <label for="job_title"><?php _e('Job Title/Position', 'cardealership-child'); ?> <span class="required">*</span></label>
                                <input type="text" id="job_title" name="job_title">
                            </div>
                            
                            <div class="form-group">
                                <label for="employment_years"><?php _e('Years with Employer', 'cardealership-child'); ?> <span class="required">*</span></label>
                                <input type="number" id="employment_years" name="employment_years" min="0" step="0.1">
                            </div>
                        </div>
                        
                        <div class="form-row employer-fields">
                            <div class="form-group">
                                <label for="employer_phone"><?php _e('Employer Phone Number', 'cardealership-child'); ?></label>
                                <input type="tel" id="employer_phone" name="employer_phone">
                            </div>
                            
                            <div class="form-group">
                                <label for="employer_address"><?php _e('Employer Address', 'cardealership-child'); ?></label>
                                <input type="text" id="employer_address" name="employer_address">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3><?php _e('Income Information', 'cardealership-child'); ?></h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="monthly_income"><?php _e('Monthly Income (GYD)', 'cardealership-child'); ?> <span class="required">*</span></label>
                                <input type="number" id="monthly_income" name="monthly_income" min="0" step="1000" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="additional_income"><?php _e('Additional Monthly Income (GYD)', 'cardealership-child'); ?></label>
                                <input type="number" id="additional_income" name="additional_income" min="0" step="1000">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group full-width">
                                <label for="additional_income_source"><?php _e('Source of Additional Income (if applicable)', 'cardealership-child'); ?></label>
                                <input type="text" id="additional_income_source" name="additional_income_source">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3><?php _e('Vehicle Information', 'cardealership-child'); ?></h3>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="vehicle_interest"><?php _e('Are you interested in a specific vehicle?', 'cardealership-child'); ?></label>
                                <select id="vehicle_interest" name="vehicle_interest">
                                    <option value="no"><?php _e('Not yet, exploring options', 'cardealership-child'); ?></option>
                                    <option value="yes"><?php _e('Yes, I have a specific vehicle in mind', 'cardealership-child'); ?></option>
                                </select>
                            </div>
                            
                            <div class="form-group vehicle-details-field" style="display: none;">
                                <label for="vehicle_details"><?php _e('Vehicle Details (if known)', 'cardealership-child'); ?></label>
                                <input type="text" id="vehicle_details" name="vehicle_details" placeholder="<?php _e('Year, Make, Model', 'cardealership-child'); ?>">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="down_payment"><?php _e('Down Payment Amount (GYD)', 'cardealership-child'); ?> <span class="required">*</span></label>
                                <input type="number" id="down_payment" name="down_payment" min="0" step="10000" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="loan_term"><?php _e('Preferred Loan Term', 'cardealership-child'); ?> <span class="required">*</span></label>
                                <select id="loan_term" name="loan_term" required>
                                    <option value=""><?php _e('Select Term', 'cardealership-child'); ?></option>
                                    <option value="12"><?php _e('12 months (1 year)', 'cardealership-child'); ?></option>
                                    <option value="24"><?php _e('24 months (2 years)', 'cardealership-child'); ?></option>
                                    <option value="36"><?php _e('36 months (3 years)', 'cardealership-child'); ?></option>
                                    <option value="48"><?php _e('48 months (4 years)', 'cardealership-child'); ?></option>
                                    <option value="60"><?php _e('60 months (5 years)', 'cardealership-child'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3><?php _e('Document Upload', 'cardealership-child'); ?></h3>
                        <p class="section-note"><?php _e('Please upload at least 2 of the required documents. You can submit additional documents later if needed.', 'cardealership-child'); ?></p>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="id_document"><?php _e('National ID / Passport', 'cardealership-child'); ?></label>
                                <input type="file" id="id_document" name="id_document" accept=".jpg,.jpeg,.png,.pdf">
                            </div>
                            
                            <div class="form-group">
                                <label for="address_document"><?php _e('Proof of Address', 'cardealership-child'); ?></label>
                                <input type="file" id="address_document" name="address_document" accept=".jpg,.jpeg,.png,.pdf">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="income_document"><?php _e('Proof of Income', 'cardealership-child'); ?></label>
                                <input type="file" id="income_document" name="income_document" accept=".jpg,.jpeg,.png,.pdf">
                            </div>
                            
                            <div class="form-group">
                                <label for="license_document"><?php _e('Driver\'s License', 'cardealership-child'); ?></label>
                                <input type="file" id="license_document" name="license_document" accept=".jpg,.jpeg,.png,.pdf">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3><?php _e('Additional Information', 'cardealership-child'); ?></h3>
                        
                        <div class="form-row">
                            <div class="form-group full-width">
                                <label for="additional_notes"><?php _e('Any additional information you would like to provide:', 'cardealership-child'); ?></label>
                                <textarea id="additional_notes" name="additional_notes" rows="4"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3><?php _e('Terms and Consent', 'cardealership-child'); ?></h3>
                        
                        <div class="form-row">
                            <div class="form-group full-width check-group">
                                <label>
                                    <input type="checkbox" name="credit_check_consent" value="1" required>
                                    <?php _e('I authorize the dealership to obtain credit reports and verify the information provided in this application.', 'cardealership-child'); ?> <span class="required">*</span>
                                </label>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group full-width check-group">
                                <label>
                                    <input type="checkbox" name="terms_consent" value="1" required>
                                    <?php _e('I agree to the financing terms and conditions.', 'cardealership-child'); ?> <span class="required">*</span>
                                </label>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group full-width check-group">
                                <label>
                                    <input type="checkbox" name="privacy_consent" value="1" required>
                                    <?php _e('I have read and agree to the Privacy Policy.', 'cardealership-child'); ?> <span class="required">*</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" name="submit_financing_application" class="button submit-application"><?php _e('Submit Application', 'cardealership-child'); ?></button>
                    </div>
                </form>
                
                <script>
                    jQuery(document).ready(function($) {
                        // Toggle employer fields based on employment status
                        $('#employment_status').change(function() {
                            var status = $(this).val();
                            
                            if (status === 'employed_full_time' || status === 'employed_part_time') {
                                $('.employer-fields').show();
                                $('.employer-field').show();
                                $('#employer_name, #job_title, #employment_years').prop('required', true);
                            } else if (status === 'self_employed') {
                                $('.employer-fields').show();
                                $('.employer-field').show();
                                $('#employer_name').prop('required', true);
                                $('#job_title, #employment_years').prop('required', false);
                            } else {
                                $('.employer-fields').hide();
                                $('.employer-field').hide();
                                $('#employer_name, #job_title, #employment_years').prop('required', false);
                            }
                        });
                        
                        // Toggle vehicle details field
                        $('#vehicle_interest').change(function() {
                            if ($(this).val() === 'yes') {
                                $('.vehicle-details-field').show();
                            } else {
                                $('.vehicle-details-field').hide();
                            }
                        });
                        
                        // Trigger on page load to respect initial values
                        $('#employment_status').trigger('change');
                        $('#vehicle_interest').trigger('change');
                    });
                </script>
                <?php
            }
            ?>
        </div>
    </div>
</div>

<?php
get_footer();

/**
 * Process the financing application form submission
 */
function cardealership_process_financing_application() {
    if (isset($_POST['submit_financing_application']) && isset($_POST['financing_application_nonce'])) {
        // Verify nonce
        if (!wp_verify_nonce($_POST['financing_application_nonce'], 'submit_financing_application')) {
            return;
        }
        
        // Basic validation
        $first_name = isset($_POST['first_name']) ? sanitize_text_field($_POST['first_name']) : '';
        $last_name = isset($_POST['last_name']) ? sanitize_text_field($_POST['last_name']) : '';
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
        
        if (empty($first_name) || empty($last_name) || empty($email) || empty($phone)) {
            return;
        }
        
        // Create a new post to store the application
        $application_data = array(
            'post_title'    => $first_name . ' ' . $last_name . ' - Financing Application',
            'post_content'  => 'Financing Application from ' . $first_name . ' ' . $last_name,
            'post_status'   => 'private',
            'post_type'     => 'financing_app',
            'post_author'   => 1, // Default admin user
        );
        
        $application_id = wp_insert_post($application_data);
        
        if (!is_wp_error($application_id)) {
            // Store all form fields as post meta
            foreach ($_POST as $key => $value) {
                if ($key !== 'submit_financing_application' && $key !== 'financing_application_nonce') {
                    if (is_array($value)) {
                        update_post_meta($application_id, $key, sanitize_text_field(implode(', ', $value)));
                    } else {
                        update_post_meta($application_id, $key, sanitize_text_field($value));
                    }
                }
            }
            
            // Handle file uploads
            $upload_fields = array('id_document', 'address_document', 'income_document', 'license_document');
            
            foreach ($upload_fields as $field) {
                if (isset($_FILES[$field]) && $_FILES[$field]['size'] > 0) {
                    // Set up the upload directory
                    $upload_dir = wp_upload_dir();
                    $upload_path = $upload_dir['path'] . '/financing-documents/';
                    
                    // Create directory if it doesn't exist
                    if (!file_exists($upload_path)) {
                        wp_mkdir_p($upload_path);
                    }
                    
                    // Sanitize the file name
                    $original_file_name = sanitize_file_name($_FILES[$field]['name']);
                    $file_name = $application_id . '-' . $field . '-' . $original_file_name;
                    
                    // Full path to the file
                    $file_path = $upload_path . $file_name;
                    
                    // Move the uploaded file
                    if (move_uploaded_file($_FILES[$field]['tmp_name'], $file_path)) {
                        // Store the file path in meta
                        update_post_meta($application_id, $field . '_path', $file_path);
                        update_post_meta($application_id, $field . '_url', $upload_dir['url'] . '/financing-documents/' . $file_name);
                    }
                }
            }
            
            // Send notifications
            $admin_email = get_option('admin_email');
            $subject = 'New Financing Application: ' . $first_name . ' ' . $last_name;
            
            $message = "A new financing application has been submitted:\n\n";
            $message .= "Name: $first_name $last_name\n";
            $message .= "Email: $email\n";
            $message .= "Phone: $phone\n";
            $message .= "Date of Birth: " . sanitize_text_field($_POST['date_of_birth']) . "\n";
            $message .= "Monthly Income: GYD " . number_format(sanitize_text_field($_POST['monthly_income'])) . "\n\n";
            $message .= "View the full application in the admin dashboard.\n";
            
            wp_mail($admin_email, $subject, $message);
            
            // Send confirmation to applicant
            $customer_subject = 'Your Financing Application Has Been Received';
            
            $customer_message = "Dear $first_name,\n\n";
            $customer_message .= "Thank you for submitting your financing application. We have received your information and our financing team will review it shortly.\n\n";
            $customer_message .= "Application Reference #: FIN-$application_id\n\n";
            $customer_message .= "We will contact you within 1-2 business days to discuss your application and the next steps.\n\n";
            $customer_message .= "If you have any questions, please contact our financing department at " . get_option('dealership_finance_email', 'finance@example.com') . " or " . get_option('dealership_phone', '+592-000-0000') . ".\n\n";
            $customer_message .= "Regards,\n";
            $customer_message .= get_bloginfo('name') . " Financing Team";
            
            wp_mail($email, $customer_subject, $customer_message);
            
            // Redirect to success page
            wp_redirect(add_query_arg('application_submitted', '1', get_permalink()));
            exit;
        }
    }
}
add_action('template_redirect', 'cardealership_process_financing_application');

/**
 * Register Financing Application post type
 */
function cardealership_register_financing_app_post_type() {
    $labels = array(
        'name'               => _x('Financing Applications', 'post type general name', 'cardealership-child'),
        'singular_name'      => _x('Financing Application', 'post type singular name', 'cardealership-child'),
        'menu_name'          => _x('Financing Apps', 'admin menu', 'cardealership-child'),
        'name_admin_bar'     => _x('Financing App', 'add new on admin bar', 'cardealership-child'),
        'add_new'            => _x('Add New', 'financing application', 'cardealership-child'),
        'add_new_item'       => __('Add New Financing Application', 'cardealership-child'),
        'new_item'           => __('New Financing Application', 'cardealership-child'),
        'edit_item'          => __('Edit Financing Application', 'cardealership-child'),
        'view_item'          => __('View Financing Application', 'cardealership-child'),
        'all_items'          => __('All Financing Applications', 'cardealership-child'),
        'search_items'       => __('Search Financing Applications', 'cardealership-child'),
        'parent_item_colon'  => __('Parent Financing Applications:', 'cardealership-child'),
        'not_found'          => __('No financing applications found.', 'cardealership-child'),
        'not_found_in_trash' => __('No financing applications found in Trash.', 'cardealership-child')
    );

    $args = array(
        'labels'             => $labels,
        'description'        => __('Financing applications from customers', 'cardealership-child'),
        'public'             => false,
        'publicly_queryable' => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => array('slug' => 'financing-app'),
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => 6,
        'menu_icon'          => 'dashicons-money-alt',
        'supports'           => array('title'),
    );

    register_post_type('financing_app', $args);
}
add_action('init', 'cardealership_register_financing_app_post_type');

/**
 * Add meta boxes for financing applications
 */
function cardealership_financing_app_meta_boxes() {
    add_meta_box(
        'financing_app_details',
        __('Application Details', 'cardealership-child'),
        'cardealership_financing_app_details_callback',
        'financing_app',
        'normal',
        'high'
    );
    
    add_meta_box(
        'financing_app_status',
        __('Application Status', 'cardealership-child'),
        'cardealership_financing_app_status_callback',
        'financing_app',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'cardealership_financing_app_meta_boxes');

/**
 * Application Details Meta Box Callback
 */
function cardealership_financing_app_details_callback($post) {
    // Get all post meta
    $post_meta = get_post_meta($post->ID);
    
    // Organize into sections
    $sections = array(
        'personal' => array(
            'title' => __('Personal Information', 'cardealership-child'),
            'fields' => array(
                'first_name' => __('First Name', 'cardealership-child'),
                'last_name' => __('Last Name', 'cardealership-child'),
                'date_of_birth' => __('Date of Birth', 'cardealership-child'),
                'national_id' => __('National ID', 'cardealership-child'),
                'email' => __('Email', 'cardealership-child'),
                'phone' => __('Phone', 'cardealership-child'),
            ),
        ),
        'address' => array(
            'title' => __('Address Information', 'cardealership-child'),
            'fields' => array(
                'street_address' => __('Street Address', 'cardealership-child'),
                'city' => __('City/Town', 'cardealership-child'),
                'region' => __('Region', 'cardealership-child'),
                'residence_years' => __('Years at Address', 'cardealership-child'),
                'residence_status' => __('Residence Status', 'cardealership-child'),
            ),
        ),
        'employment' => array(
            'title' => __('Employment Information', 'cardealership-child'),
            'fields' => array(
                'employment_status' => __('Employment Status', 'cardealership-child'),
                'employer_name' => __('Employer Name', 'cardealership-child'),
                'job_title' => __('Job Title', 'cardealership-child'),
                'employment_years' => __('Years with Employer', 'cardealership-child'),
                'employer_phone' => __('Employer Phone', 'cardealership-child'),
                'employer_address' => __('Employer Address', 'cardealership-child'),
            ),
        ),
        'income' => array(
            'title' => __('Income Information', 'cardealership-child'),
            'fields' => array(
                'monthly_income' => __('Monthly Income (GYD)', 'cardealership-child'),
                'additional_income' => __('Additional Income (GYD)', 'cardealership-child'),
                'additional_income_source' => __('Additional Income Source', 'cardealership-child'),
            ),
        ),
        'vehicle' => array(
            'title' => __('Vehicle Information', 'cardealership-child'),
            'fields' => array(
                'vehicle_interest' => __('Interested in Specific Vehicle', 'cardealership-child'),
                'vehicle_details' => __('Vehicle Details', 'cardealership-child'),
                'down_payment' => __('Down Payment (GYD)', 'cardealership-child'),
                'loan_term' => __('Loan Term (months)', 'cardealership-child'),
            ),
        ),
        'documents' => array(
            'title' => __('Uploaded Documents', 'cardealership-child'),
            'fields' => array(
                'id_document_url' => __('ID Document', 'cardealership-child'),
                'address_document_url' => __('Address Document', 'cardealership-child'),
                'income_document_url' => __('Income Document', 'cardealership-child'),
                'license_document_url' => __('License Document', 'cardealership-child'),
            ),
        ),
        'notes' => array(
            'title' => __('Additional Notes', 'cardealership-child'),
            'fields' => array(
                'additional_notes' => __('Additional Notes', 'cardealership-child'),
            ),
        ),
    );
    
    // Display form sections and fields
    foreach ($sections as $section_id => $section) {
        echo '<div class="financing-app-section">';
        echo '<h3>' . esc_html($section['title']) . '</h3>';
        echo '<table class="form-table">';
        
        foreach ($section['fields'] as $field_id => $field_label) {
            $value = isset($post_meta[$field_id]) ? $post_meta[$field_id][0] : '';
            
            // Format special fields
            if ($field_id === 'monthly_income' || $field_id === 'additional_income' || $field_id === 'down_payment') {
                $value = 'GYD ' . number_format($value);
            } elseif ($field_id === 'residence_status') {
                $statuses = array(
                    'own' => __('Own', 'cardealership-child'),
                    'rent' => __('Rent', 'cardealership-child'),
                    'living_with_family' => __('Living with Family', 'cardealership-child'),
                    'other' => __('Other', 'cardealership-child'),
                );
                
                $value = isset($statuses[$value]) ? $statuses[$value] : $value;
            } elseif ($field_id === 'employment_status') {
                $statuses = array(
                    'employed_full_time' => __('Employed Full-Time', 'cardealership-child'),
                    'employed_part_time' => __('Employed Part-Time', 'cardealership-child'),
                    'self_employed' => __('Self-Employed', 'cardealership-child'),
                    'retired' => __('Retired', 'cardealership-child'),
                    'student' => __('Student', 'cardealership-child'),
                    'unemployed' => __('Unemployed', 'cardealership-child'),
                );
                
                $value = isset($statuses[$value]) ? $statuses[$value] : $value;
            } elseif ($field_id === 'vehicle_interest') {
                $value = $value === 'yes' ? __('Yes', 'cardealership-child') : __('No, exploring options', 'cardealership-child');
            } elseif (strpos($field_id, '_document_url') !== false && !empty($value)) {
                $value = '<a href="' . esc_url($value) . '" target="_blank">' . __('View Document', 'cardealership-child') . '</a>';
            }
            
            echo '<tr>';
            echo '<th><label>' . esc_html($field_label) . '</label></th>';
            echo '<td>' . (strpos($field_id, '_document_url') !== false ? $value : esc_html($value)) . '</td>';
            echo '</tr>';
        }
        
        echo '</table>';
        echo '</div>';
    }
    
    // Application consent fields
    echo '<div class="financing-app-section">';
    echo '<h3>' . __('Consent Information', 'cardealership-child') . '</h3>';
    echo '<ul>';
    
    $credit_check_consent = isset($post_meta['credit_check_consent']) ? $post_meta['credit_check_consent'][0] : '0';
    $terms_consent = isset($post_meta['terms_consent']) ? $post_meta['terms_consent'][0] : '0';
    $privacy_consent = isset($post_meta['privacy_consent']) ? $post_meta['privacy_consent'][0] : '0';
    
    echo '<li>' . __('Credit Check Authorization:', 'cardealership-child') . ' ' . ($credit_check_consent === '1' ? __('Yes', 'cardealership-child') : __('No', 'cardealership-child')) . '</li>';
    echo '<li>' . __('Terms & Conditions Agreement:', 'cardealership-child') . ' ' . ($terms_consent === '1' ? __('Yes', 'cardealership-child') : __('No', 'cardealership-child')) . '</li>';
    echo '<li>' . __('Privacy Policy Agreement:', 'cardealership-child') . ' ' . ($privacy_consent === '1' ? __('Yes', 'cardealership-child') : __('No', 'cardealership-child')) . '</li>';
    
    echo '</ul>';
    echo '</div>';
}

/**
 * Application Status Meta Box Callback
 */
function cardealership_financing_app_status_callback($post) {
    // Add nonce for security
    wp_nonce_field('cardealership_save_financing_app_status', 'cardealership_financing_app_status_nonce');
    
    // Get current status
    $status = get_post_meta($post->ID, '_application_status', true);
    if (empty($status)) {
        $status = 'new';
    }
    
    // Status options
    $status_options = array(
        'new' => __('New Application', 'cardealership-child'),
        'reviewing' => __('Under Review', 'cardealership-child'),
        'documents_requested' => __('Documents Requested', 'cardealership-child'),
        'pending_approval' => __('Pending Approval', 'cardealership-child'),
        'approved' => __('Approved', 'cardealership-child'),
        'conditional_approval' => __('Conditional Approval', 'cardealership-child'),
        'declined' => __('Declined', 'cardealership-child'),
        'completed' => __('Completed', 'cardealership-child'),
    );
    
    echo '<div class="financing-app-status">';
    echo '<p><label for="application_status">' . __('Application Status:', 'cardealership-child') . '</label></p>';
    echo '<select id="application_status" name="application_status" style="width: 100%;">';
    
    foreach ($status_options as $option_value => $option_label) {
        echo '<option value="' . esc_attr($option_value) . '" ' . selected($status, $option_value, false) . '>' . esc_html($option_label) . '</option>';
    }
    
    echo '</select>';
    echo '</div>';
    
    // Status notes
    $status_notes = get_post_meta($post->ID, '_status_notes', true);
    
    echo '<div class="financing-app-status-notes">';
    echo '<p><label for="status_notes">' . __('Status Notes:', 'cardealership-child') . '</label></p>';
    echo '<textarea id="status_notes" name="status_notes" rows="5" style="width: 100%;">' . esc_textarea($status_notes) . '</textarea>';
    echo '</div>';
    
    // Application date
    $application_date = get_the_date('', $post->ID);
    echo '<div class="financing-app-date">';
    echo '<p>' . __('Application Date:', 'cardealership-child') . ' <strong>' . esc_html($application_date) . '</strong></p>';
    echo '</div>';
    
    // Notify applicant
    echo '<div class="financing-app-notify">';
    echo '<p><label><input type="checkbox" name="notify_applicant" value="1"> ' . __('Notify applicant of status change', 'cardealership-child') . '</label></p>';
    echo '</div>';
}

/**
 * Save Application Status
 */
function cardealership_save_financing_app_status($post_id) {
    // Check if nonce is set
    if (!isset($_POST['cardealership_financing_app_status_nonce'])) {
        return;
    }
    
    // Verify nonce
    if (!wp_verify_nonce($_POST['cardealership_financing_app_status_nonce'], 'cardealership_save_financing_app_status')) {
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
    
    // Save status
    if (isset($_POST['application_status'])) {
        $old_status = get_post_meta($post_id, '_application_status', true);
        $new_status = sanitize_text_field($_POST['application_status']);
        
        update_post_meta($post_id, '_application_status', $new_status);
        
        // Save status change time
        update_post_meta($post_id, '_status_updated_time', current_time('mysql'));
    }
    
    // Save status notes
    if (isset($_POST['status_notes'])) {
        update_post_meta($post_id, '_status_notes', sanitize_textarea_field($_POST['status_notes']));
    }
    
    // Notify applicant if requested
    if (isset($_POST['notify_applicant']) && $_POST['notify_applicant'] === '1') {
        $status = sanitize_text_field($_POST['application_status']);
        $notes = isset($_POST['status_notes']) ? sanitize_textarea_field($_POST['status_notes']) : '';
        
        $first_name = get_post_meta($post_id, 'first_name', true);
        $email = get_post_meta($post_id, 'email', true);
        
        if (!empty($email)) {
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
            
            $subject = __('Financing Application Update - ', 'cardealership-child') . get_bloginfo('name');
            
            $message = "Dear $first_name,\n\n";
            $message .= sprintf(__("We are writing to inform you that your financing application (Reference #: FIN-%s) status has been updated to: %s\n\n", 'cardealership-child'), $post_id, $status_label);
            
            if (!empty($notes)) {
                $message .= __("Additional notes from our financing team:\n", 'cardealership-child');
                $message .= "$notes\n\n";
            }
            
            if ($status === 'documents_requested') {
                $message .= __("Please provide the requested documents as soon as possible to continue with your application process.\n\n", 'cardealership-child');
            } elseif ($status === 'approved') {
                $message .= __("Congratulations! Your financing application has been approved. Our team will contact you shortly to discuss next steps.\n\n", 'cardealership-child');
            } elseif ($status === 'conditional_approval') {
                $message .= __("Your application has been conditionally approved. We will need some additional information before finalizing your financing.\n\n", 'cardealership-child');
            }
            
            $message .= sprintf(__("If you have any questions, please contact our financing department at %s or %s.\n\n", 'cardealership-child'), get_option('dealership_finance_email', 'finance@example.com'), get_option('dealership_phone', '+592-000-0000'));
            $message .= __("Regards,\n", 'cardealership-child');
            $message .= get_bloginfo('name') . " " . __("Financing Team", 'cardealership-child');
            
            wp_mail($email, $subject, $message);
            
            // Add note that notification was sent
            $current_user = wp_get_current_user();
            $admin_name = $current_user->display_name;
            
            $notification_log = get_post_meta($post_id, '_notification_log', true);
            if (empty($notification_log)) {
                $notification_log = array();
            }
            
            $notification_log[] = array(
                'time' => current_time('mysql'),
                'status' => $status,
                'admin' => $admin_name,
            );
            
            update_post_meta($post_id, '_notification_log', $notification_log);
        }
    }
}
add_action('save_post_financing_app', 'cardealership_save_financing_app_status');

/**
 * Add custom admin columns for financing applications
 */
function cardealership_financing_app_columns($columns) {
    $new_columns = array();
    
    foreach ($columns as $key => $value) {
        if ($key === 'title') {
            $new_columns[$key] = $value;
            $new_columns['applicant'] = __('Applicant', 'cardealership-child');
            $new_columns['contact'] = __('Contact', 'cardealership-child');
            $new_columns['income'] = __('Monthly Income', 'cardealership-child');
            $new_columns['status'] = __('Status', 'cardealership-child');
            $new_columns['application_date'] = __('Date', 'cardealership-child');
        } else if ($key !== 'date') {
            $new_columns[$key] = $value;
        }
    }
    
    return $new_columns;
}
add_filter('manage_financing_app_posts_columns', 'cardealership_financing_app_columns');

/**
 * Display custom column content for financing applications
 */
function cardealership_financing_app_custom_column($column, $post_id) {
    switch ($column) {
        case 'applicant':
            $first_name = get_post_meta($post_id, 'first_name', true);
            $last_name = get_post_meta($post_id, 'last_name', true);
            echo esc_html($first_name . ' ' . $last_name);
            break;
            
        case 'contact':
            $email = get_post_meta($post_id, 'email', true);
            $phone = get_post_meta($post_id, 'phone', true);
            echo esc_html($email) . '<br>' . esc_html($phone);
            break;
            
        case 'income':
            $monthly_income = get_post_meta($post_id, 'monthly_income', true);
            echo 'GYD ' . number_format($monthly_income);
            break;
            
        case 'status':
            $status = get_post_meta($post_id, '_application_status', true);
            if (empty($status)) {
                $status = 'new';
            }
            
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
            
            $status_colors = array(
                'new' => '#0073aa',
                'reviewing' => '#ffba00',
                'documents_requested' => '#72aee6',
                'pending_approval' => '#ff9800',
                'approved' => '#46b450',
                'conditional_approval' => '#00a0d2',
                'declined' => '#dc3232',
                'completed' => '#6c6c6c',
            );
            
            $status_label = isset($status_labels[$status]) ? $status_labels[$status] : $status;
            $status_color = isset($status_colors[$status]) ? $status_colors[$status] : '#000000';
            
            echo '<span style="display: inline-block; padding: 3px 6px; background-color: ' . esc_attr($status_color) . '; color: white; border-radius: 3px;">' . esc_html($status_label) . '</span>';
            break;
            
        case 'application_date':
            echo get_the_date('Y-m-d', $post_id) . '<br>' . get_the_date('H:i', $post_id);
            break;
    }
}
add_action('manage_financing_app_posts_custom_column', 'cardealership_financing_app_custom_column', 10, 2);

/**
 * Make custom columns sortable
 */
function cardealership_financing_app_sortable_columns($columns) {
    $columns['status'] = '_application_status';
    $columns['application_date'] = 'date';
    $columns['income'] = 'monthly_income';
    
    return $columns;
}
add_filter('manage_edit-financing_app_sortable_columns', 'cardealership_financing_app_sortable_columns');

/**
 * Handle custom column sorting
 */
function cardealership_financing_app_column_orderby($query) {
    if (!is_admin() || !$query->is_main_query() || $query->get('post_type') !== 'financing_app') {
        return;
    }
    
    $orderby = $query->get('orderby');
    
    if ($orderby === '_application_status') {
        $query->set('meta_key', '_application_status');
        $query->set('orderby', 'meta_value');
    } elseif ($orderby === 'monthly_income') {
        $query->set('meta_key', 'monthly_income');
        $query->set('orderby', 'meta_value_num');
    }
}
add_action('pre_get_posts', 'cardealership_financing_app_column_orderby');
