<?php
/**
 * Template Name: Vehicle Archive
 * 
 * Create this file as archive-vehicle.php in your child theme
 */
get_header();
?>

<div class="vehicle-archive-container">
    <div class="vehicle-filters">
        <h2><?php _e('Find Your Vehicle', 'cardealership-child'); ?></h2>
        
        <form method="get" class="vehicle-filter-form">
            <?php
            // Get all makes
            $makes = get_terms(array(
                'taxonomy' => 'vehicle_make',
                'hide_empty' => true,
            ));
            
            // Get all types
            $types = get_terms(array(
                'taxonomy' => 'vehicle_type',
                'hide_empty' => true,
            ));
            ?>
            
            <div class="filter-row">
                <div class="filter-col">
                    <label for="vehicle_make"><?php _e('Make', 'cardealership-child'); ?></label>
                    <select name="vehicle_make" id="vehicle_make">
                        <option value=""><?php _e('Any Make', 'cardealership-child'); ?></option>
                        <?php foreach ($makes as $make) : ?>
                            <option value="<?php echo esc_attr($make->slug); ?>" <?php selected(isset($_GET['vehicle_make']) ? $_GET['vehicle_make'] : '', $make->slug); ?>>
                                <?php echo esc_html($make->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-col">
                    <label for="vehicle_type"><?php _e('Type', 'cardealership-child'); ?></label>
                    <select name="vehicle_type" id="vehicle_type">
                        <option value=""><?php _e('Any Type', 'cardealership-child'); ?></option>
                        <?php foreach ($types as $type) : ?>
                            <option value="<?php echo esc_attr($type->slug); ?>" <?php selected(isset($_GET['vehicle_type']) ? $_GET['vehicle_type'] : '', $type->slug); ?>>
                                <?php echo esc_html($type->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-col">
                    <label for="min_price"><?php _e('Min Price', 'cardealership-child'); ?></label>
                    <input type="number" name="min_price" id="min_price" value="<?php echo isset($_GET['min_price']) ? esc_attr($_GET['min_price']) : ''; ?>" placeholder="<?php _e('Min GYD', 'cardealership-child'); ?>">
                </div>
                
                <div class="filter-col">
                    <label for="max_price"><?php _e('Max Price', 'cardealership-child'); ?></label>
                    <input type="number" name="max_price" id="max_price" value="<?php echo isset($_GET['max_price']) ? esc_attr($_GET['max_price']) : ''; ?>" placeholder="<?php _e('Max GYD', 'cardealership-child'); ?>">
                </div>
            </div>
            
            <div class="filter-row">
                <div class="filter-col">
                    <label for="min_year"><?php _e('Min Year', 'cardealership-child'); ?></label>
                    <input type="number" name="min_year" id="min_year" value="<?php echo isset($_GET['min_year']) ? esc_attr($_GET['min_year']) : ''; ?>" min="1900" max="<?php echo date('Y'); ?>">
                </div>
                
                <div class="filter-col">
                    <label for="max_year"><?php _e('Max Year', 'cardealership-child'); ?></label>
                    <input type="number" name="max_year" id="max_year" value="<?php echo isset($_GET['max_year']) ? esc_attr($_GET['max_year']) : ''; ?>" min="1900" max="<?php echo date('Y'); ?>">
                </div>
                
                <div class="filter-col">
                    <label for="transmission"><?php _e('Transmission', 'cardealership-child'); ?></label>
                    <select name="transmission" id="transmission">
                        <option value=""><?php _e('Any Transmission', 'cardealership-child'); ?></option>
                        <option value="automatic" <?php selected(isset($_GET['transmission']) ? $_GET['transmission'] : '', 'automatic'); ?>><?php _e('Automatic', 'cardealership-child'); ?></option>
                        <option value="manual" <?php selected(isset($_GET['transmission']) ? $_GET['transmission'] : '', 'manual'); ?>><?php _e('Manual', 'cardealership-child'); ?></option>
                        <option value="cvt" <?php selected(isset($_GET['transmission']) ? $_GET['transmission'] : '', 'cvt'); ?>><?php _e('CVT', 'cardealership-child'); ?></option>
                    </select>
                </div>
                
                <div class="filter-col">
                    <label for="financing"><?php _e('Financing', 'cardealership-child'); ?></label>
                    <select name="financing" id="financing">
                        <option value=""><?php _e('Any', 'cardealership-child'); ?></option>
                        <option value="1" <?php selected(isset($_GET['financing']) ? $_GET['financing'] : '', '1'); ?>><?php _e('Available', 'cardealership-child'); ?></option>
                    </select>
                </div>
            </div>
            
            <div class="filter-row">
                <div class="filter-col filter-submit">
                    <button type="submit" class="button"><?php _e('Search Vehicles', 'cardealership-child'); ?></button>
                    <?php if (isset($_GET['vehicle_make']) || isset($_GET['vehicle_type']) || isset($_GET['min_price']) || isset($_GET['max_price'])) : ?>
                        <a href="<?php echo get_post_type_archive_link('vehicle'); ?>" class="reset-button"><?php _e('Reset Filters', 'cardealership-child'); ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
    
    <div class="vehicle-results">
        <?php
        if (have_posts()) :
            ?>
            <div class="vehicle-sort">
                <form method="get" class="vehicle-sort-form">
                    <?php
                    // Preserve existing query parameters
                    foreach ($_GET as $key => $value) {
                        if ($key !== 'orderby') {
                            echo '<input type="hidden" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '">';
                        }
                    }
                    ?>
                    <label for="orderby"><?php _e('Sort by:', 'cardealership-child'); ?></label>
                    <select name="orderby" id="orderby" onchange="this.form.submit()">
                        <option value="date" <?php selected(isset($_GET['orderby']) ? $_GET['orderby'] : '', 'date'); ?>><?php _e('Newest First', 'cardealership-child'); ?></option>
                        <option value="price_low" <?php selected(isset($_GET['orderby']) ? $_GET['orderby'] : '', 'price_low'); ?>><?php _e('Price: Low to High', 'cardealership-child'); ?></option>
                        <option value="price_high" <?php selected(isset($_GET['orderby']) ? $_GET['orderby'] : '', 'price_high'); ?>><?php _e('Price: High to Low', 'cardealership-child'); ?></option>
                        <option value="year_new" <?php selected(isset($_GET['orderby']) ? $_GET['orderby'] : '', 'year_new'); ?>><?php _e('Year: Newest First', 'cardealership-child'); ?></option>
                        <option value="year_old" <?php selected(isset($_GET['orderby']) ? $_GET['orderby'] : '', 'year_old'); ?>><?php _e('Year: Oldest First', 'cardealership-child'); ?></option>
                    </select>
                </form>
            </div>
            
            <div class="vehicles-grid">
                <?php
                while (have_posts()) :
                    the_post();
                    
                    $post_id = get_the_ID();
                    $price = get_post_meta($post_id, '_vehicle_price', true);
                    $year = get_post_meta($post_id, '_vehicle_year', true);
                    $mileage = get_post_meta($post_id, '_vehicle_mileage', true);
                    $transmission = get_post_meta($post_id, '_vehicle_transmission', true);
                    $financing_available = get_post_meta($post_id, '_vehicle_financing_available', true);
                    
                    $make_terms = wp_get_post_terms($post_id, 'vehicle_make');
                    $model_terms = wp_get_post_terms($post_id, 'vehicle_model');
                    $type_terms = wp_get_post_terms($post_id, 'vehicle_type');
                    
                    $make = !empty($make_terms) ? $make_terms[0]->name : '';
                    $model = !empty($model_terms) ? $model_terms[0]->name : '';
                    $type = !empty($type_terms) ? $type_terms[0]->name : '';
                    ?>
                    
                    <div class="vehicle-card">
                        <div class="vehicle-image">
                            <?php if ($financing_available) : ?>
                                <span class="financing-badge"><?php _e('Financing Available', 'cardealership-child'); ?></span>
                            <?php endif; ?>
                            
                            <a href="<?php the_permalink(); ?>">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('medium'); ?>
                                <?php else : ?>
                                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/no-image.jpg" alt="<?php the_title_attribute(); ?>">
                                <?php endif; ?>
                            </a>
                        </div>
                        
                        <div class="vehicle-details">
                            <h3 class="vehicle-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            
                            <div class="vehicle-meta">
                                <?php if ($year || $make || $model) : ?>
                                    <p class="vehicle-name">
                                        <?php echo $year ? esc_html($year) . ' ' : ''; ?>
                                        <?php echo $make ? esc_html($make) . ' ' : ''; ?>
                                        <?php echo $model ? esc_html($model) : ''; ?>
                                    </p>
                                <?php endif; ?>
                                
                                <?php if ($type) : ?>
                                    <span class="vehicle-type"><?php echo esc_html($type); ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($price) : ?>
                                <div class="vehicle-price"><?php echo wc_price($price); ?></div>
                            <?php endif; ?>
                            
                            <div class="vehicle-specs">
                                <?php if ($mileage) : ?>
                                    <div class="spec-item">
                                        <span class="spec-icon"><i class="dashicons dashicons-dashboard"></i></span>
                                        <span class="spec-value"><?php echo number_format($mileage); ?> km</span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($transmission) : ?>
                                    <div class="spec-item">
                                        <span class="spec-icon"><i class="dashicons dashicons-admin-settings"></i></span>
                                        <span class="spec-value"><?php echo esc_html(ucfirst($transmission)); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="vehicle-actions">
                                <a href="<?php the_permalink(); ?>" class="button view-details"><?php _e('View Details', 'cardealership-child'); ?></a>
                                <?php if ($financing_available) : ?>
                                    <a href="<?php the_permalink(); ?>#financing" class="button calculate-financing"><?php _e('Calculate Financing', 'cardealership-child'); ?></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                <?php endwhile; ?>
            </div>
            
            <div class="pagination">
                <?php
                echo paginate_links(array(
                    'prev_text' => __('&laquo; Previous', 'cardealership-child'),
                    'next_text' => __('Next &raquo;', 'cardealership-child'),
                ));
                ?>
            </div>
            
        <?php else : ?>
            
            <div class="no-vehicles-found">
                <h3><?php _e('No vehicles found matching your criteria.', 'cardealership-child'); ?></h3>
                <p><?php _e('Please adjust your search filters and try again.', 'cardealership-child'); ?></p>
                <a href="<?php echo get_post_type_archive_link('vehicle'); ?>" class="button"><?php _e('Clear Filters', 'cardealership-child'); ?></a>
            </div>
            
        <?php endif; ?>
    </div>
</div>

<?php
get_footer();
?>

<?php
/**
 * Template Name: Single Vehicle
 * 
 * Create this file as single-vehicle.php in your child theme
 */
get_header();
?>

<div class="single-vehicle-container">
    <?php
    while (have_posts()) :
        the_post();
        
        $post_id = get_the_ID();
        $price = get_post_meta($post_id, '_vehicle_price', true);
        $msrp = get_post_meta($post_id, '_vehicle_msrp', true);
        $year = get_post_meta($post_id, '_vehicle_year', true);
        $mileage = get_post_meta($post_id, '_vehicle_mileage', true);
        $engine = get_post_meta($post_id, '_vehicle_engine', true);
        $transmission = get_post_meta($post_id, '_vehicle_transmission', true);
        $color = get_post_meta($post_id, '_vehicle_color', true);
        $vin = get_post_meta($post_id, '_vehicle_vin', true);
        $features = get_post_meta($post_id, '_vehicle_features', true);
        $financing_available = get_post_meta($post_id, '_vehicle_financing_available', true);
        
        $make_terms = wp_get_post_terms($post_id, 'vehicle_make');
        $model_terms = wp_get_post_terms($post_id, 'vehicle_model');
        $type_terms = wp_get_post_terms($post_id, 'vehicle_type');
        
        $make = !empty($make_terms) ? $make_terms[0]->name : '';
        $model = !empty($model_terms) ? $model_terms[0]->name : '';
        $type = !empty($type_terms) ? $type_terms[0]->name : '';
        
        // Get linked WooCommerce product
        $linked_product_id = get_post_meta($post_id, '_linked_product_id', true);
        ?>
        
        <div class="vehicle-header">
            <div class="vehicle-title-container">
                <h1 class="vehicle-title">
                    <?php if ($year || $make || $model) : ?>
                        <?php echo $year ? esc_html($year) . ' ' : ''; ?>
                        <?php echo $make ? esc_html($make) . ' ' : ''; ?>
                        <?php echo $model ? esc_html($model) : ''; ?>
                    <?php else : ?>
                        <?php the_title(); ?>
                    <?php endif; ?>
                </h1>
                
                <?php if ($type) : ?>
                    <span class="vehicle-type"><?php echo esc_html($type); ?></span>
                <?php endif; ?>
            </div>
            
            <div class="vehicle-price-container">
                <?php if ($price) : ?>
                    <div class="vehicle-price"><?php echo wc_price($price); ?></div>
                <?php endif; ?>
                
                <?php if ($msrp && $msrp > $price) : ?>
                    <div class="vehicle-msrp">
                        <span class="msrp-label"><?php _e('MSRP:', 'cardealership-child'); ?></span>
                        <span class="msrp-value"><?php echo wc_price($msrp); ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="vehicle-content">
            <div class="vehicle-gallery">
                <?php
                if (has_post_thumbnail()) {
                    echo '<div class="vehicle-main-image">';
                    the_post_thumbnail('large');
                    echo '</div>';
                    
                    // Get gallery images
                    $gallery_ids = get_post_meta($post_id, '_vehicle_gallery', true);
                    
                    if ($gallery_ids) {
                        echo '<div class="vehicle-thumbnails">';
                        echo '<div class="thumbnail active">' . get_the_post_thumbnail($post_id, 'thumbnail') . '</div>';
                        
                        $gallery_ids = explode(',', $gallery_ids);
                        foreach ($gallery_ids as $gallery_id) {
                            echo '<div class="thumbnail">' . wp_get_attachment_image($gallery_id, 'thumbnail') . '</div>';
                        }
                        
                        echo '</div>';
                    }
                }
                ?>
            </div>
            
            <div class="vehicle-details">
                <div class="vehicle-specs">
                    <h3><?php _e('Vehicle Specifications', 'cardealership-child'); ?></h3>
                    
                    <div class="specs-grid">
                        <?php if ($year) : ?>
                            <div class="spec-item">
                                <span class="spec-label"><?php _e('Year:', 'cardealership-child'); ?></span>
                                <span class="spec-value"><?php echo esc_html($year); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($make) : ?>
                            <div class="spec-item">
                                <span class="spec-label"><?php _e('Make:', 'cardealership-child'); ?></span>
                                <span class="spec-value"><?php echo esc_html($make); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($model) : ?>
                            <div class="spec-item">
                                <span class="spec-label"><?php _e('Model:', 'cardealership-child'); ?></span>
                                <span class="spec-value"><?php echo esc_html($model); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($mileage) : ?>
                            <div class="spec-item">
                                <span class="spec-label"><?php _e('Mileage:', 'cardealership-child'); ?></span>
                                <span class="spec-value"><?php echo number_format($mileage); ?> km</span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($engine) : ?>
                            <div class="spec-item">
                                <span class="spec-label"><?php _e('Engine:', 'cardealership-child'); ?></span>
                                <span class="spec-value"><?php echo esc_html($engine); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($transmission) : ?>
                            <div class="spec-item">
                                <span class="spec-label"><?php _e('Transmission:', 'cardealership-child'); ?></span>
                                <span class="spec-value"><?php echo esc_html(ucfirst($transmission)); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($color) : ?>
                            <div class="spec-item">
                                <span class="spec-label"><?php _e('Color:', 'cardealership-child'); ?></span>
                                <span class="spec-value"><?php echo esc_html($color); ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($vin) : ?>
                            <div class="spec-item">
                                <span class="spec-label"><?php _e('VIN:', 'cardealership-child'); ?></span>
                                <span class="spec-value"><?php echo esc_html($vin); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if ($features) : ?>
                    <div class="vehicle-features">
                        <h3><?php _e('Features', 'cardealership-child'); ?></h3>
                        
                        <ul class="features-list">
                            <?php
                            $features_array = explode("\n", $features);
                            foreach ($features_array as $feature) {
                                if (trim($feature)) {
                                    echo '<li>' . esc_html(trim($feature)) . '</li>';
                                }
                            }
                            ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <div class="vehicle-description">
                    <h3><?php _e('Description', 'cardealership-child'); ?></h3>
                    <?php the_content(); ?>
                </div>
                
                <div class="vehicle-actions">
                    <?php if ($linked_product_id) : ?>
                        <a href="<?php echo add_query_arg('add-to-cart', $linked_product_id, wc_get_cart_url()); ?>" class="button add-to-cart"><?php _e('Add to Cart', 'cardealership-child'); ?></a>
                    <?php endif; ?>
                    
                    <a href="#vehicle-inquiry" class="button inquiry"><?php _e('Inquire Now', 'cardealership-child'); ?></a>
                    
                    <a href="tel:<?php echo esc_attr(get_option('dealership_phone', '+592-000-0000')); ?>" class="button call-us"><?php _e('Call Us', 'cardealership-child'); ?></a>
                </div>
            </div>
        </div>
        
        <?php if ($financing_available) : ?>
            <div id="financing" class="vehicle-financing">
                <h3><?php _e('Financing Calculator', 'cardealership-child'); ?></h3>
                
                <?php
                $down_payment = get_post_meta($post_id, '_vehicle_down_payment', true);
                $financing_term = get_post_meta($post_id, '_vehicle_financing_term', true);
                $interest_rate = get_post_meta($post_id, '_vehicle_interest_rate', true);
                ?>
                
                <input type="hidden" id="vehicle_price" value="<?php echo esc_attr($price); ?>">
                <input type="hidden" id="default_down_payment" value="<?php echo esc_attr($down_payment); ?>">
                <input type="hidden" id="default_term" value="<?php echo esc_attr($financing_term); ?>">
                <input type="hidden" id="default_rate" value="<?php echo esc_attr($interest_rate); ?>">
                
                <div class="financing-calculator">
                    <div class="calculator-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="down_payment"><?php _e('Down Payment (GYD):', 'cardealership-child'); ?></label>
                                <input type="number" id="down_payment" class="form-control" min="0" step="1000">
                            </div>
                            
                            <div class="form-group">
                                <label for="loan_term"><?php _e('Loan Term (months):', 'cardealership-child'); ?></label>
                                <select id="loan_term" class="form-control">
                                    <option value="12">12 <?php _e('months', 'cardealership-child'); ?></option>
                                    <option value="24">24 <?php _e('months', 'cardealership-child'); ?></option>
                                    <option value="36">36 <?php _e('months', 'cardealership-child'); ?></option>
                                    <option value="48">48 <?php _e('months', 'cardealership-child'); ?></option>
                                    <option value="60">60 <?php _e('months', 'cardealership-child'); ?></option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="interest_rate"><?php _e('Interest Rate (%):', 'cardealership-child'); ?></label>
                                <input type="number" id="interest_rate" class="form-control" min="0" max="100" step="0.1">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <button type="button" id="calculate_payment" class="button alt"><?php _e('Calculate Payment', 'cardealership-child'); ?></button>
                        </div>
                    </div>
                    
                    <div class="calculator-results">
                        <div class="result-group">
                            <span class="result-label"><?php _e('Monthly Payment:', 'cardealership-child'); ?></span>
                            <span id="monthly_payment" class="result-value"></span>
                        </div>
                        
                        <div class="result-group">
                            <span class="result-label"><?php _e('Total Loan Amount:', 'cardealership-child'); ?></span>
                            <span id="loan_amount" class="result-value"></span>
                        </div>
                        
                        <div class="result-group">
                            <span class="result-label"><?php _e('Total Interest:', 'cardealership-child'); ?></span>
                            <span id="total_interest" class="result-value"></span>
                        </div>
                        
                        <div class="result-group">
                            <span class="result-label"><?php _e('Total Cost:', 'cardealership-child'); ?></span>
                            <span id="total_cost" class="result-value"></span>
                        </div>
                    </div>
                </div>
                
                <div class="financing-note">
                    <p><?php _e('This calculator provides an estimate. Contact us for actual financing options and rates.', 'cardealership-child'); ?></p>
                    
                    <a href="#vehicle-inquiry" class="button financing-inquiry"><?php _e('Apply for Financing', 'cardealership-child'); ?></a>
                </div>
            </div>
        <?php endif; ?>
        
        <div id="vehicle-inquiry" class="vehicle-inquiry">
            <h3><?php _e('Inquire About This Vehicle', 'cardealership-child'); ?></h3>
            
            <?php
            // Check if Contact Form 7 is active
            if (function_exists('wpcf7_contact_form_tag_func')) {
                // Replace '123' with your actual Contact Form 7 ID
                echo do_shortcode('[contact-form-7 id="123" title="Vehicle Inquiry"]');
            } else {
                // Basic form if Contact Form 7 is not available
                ?>
                <form class="inquiry-form" method="post">
                    <input type="hidden" name="vehicle_id" value="<?php echo esc_attr($post_id); ?>">
                    <input type="hidden" name="vehicle_title" value="<?php echo esc_attr(get_the_title($post_id)); ?>">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name"><?php _e('Your Name *', 'cardealership-child'); ?></label>
                            <input type="text" name="name" id="name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email"><?php _e('Your Email *', 'cardealership-child'); ?></label>
                            <input type="email" name="email" id="email" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone"><?php _e('Your Phone *', 'cardealership-child'); ?></label>
                            <input type="tel" name="phone" id="phone" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="preferred_contact"><?php _e('Preferred Contact Method', 'cardealership-child'); ?></label>
                            <select name="preferred_contact" id="preferred_contact">
                                <option value="email"><?php _e('Email', 'cardealership-child'); ?></option>
                                <option value="phone"><?php _e('Phone', 'cardealership-child'); ?></option>
                                <option value="whatsapp"><?php _e('WhatsApp', 'cardealership-child'); ?></option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="message"><?php _e('Your Message', 'cardealership-child'); ?></label>
                            <textarea name="message" id="message" rows="4"></textarea>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group check-group">
                            <label>
                                <input type="checkbox" name="financing" value="1">
                                <?php _e('I am interested in financing options', 'cardealership-child'); ?>
                            </label>
                        </div>
                        
                        <div class="form-group check-group">
                            <label>
                                <input type="checkbox" name="test_drive" value="1">
                                <?php _e('I would like to schedule a test drive', 'cardealership-child'); ?>
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <button type="submit" class="button alt"><?php _e('Send Inquiry', 'cardealership-child'); ?></button>
                    </div>
                </form>
                <?php
            }
            ?>
        </div>
        
        <?php
        // Related vehicles (same make or type)
        $related_args = array(
            'post_type' => 'vehicle',
            'posts_per_page' => 3,
            'post__not_in' => array($post_id),
            'orderby' => 'rand',
        );
        
        if (!empty($make_terms)) {
            $related_args['tax_query'][] = array(
                'taxonomy' => 'vehicle_make',
                'field' => 'term_id',
                'terms' => $make_terms[0]->term_id,
            );
        } elseif (!empty($type_terms)) {
            $related_args['tax_query'][] = array(
                'taxonomy' => 'vehicle_type',
                'field' => 'term_id',
                'terms' => $type_terms[0]->term_id,
            );
        }
        
        $related_vehicles = new WP_Query($related_args);
        
        if ($related_vehicles->have_posts()) :
            ?>
            <div class="related-vehicles">
                <h3><?php _e('Similar Vehicles', 'cardealership-child'); ?></h3>
                
                <div class="vehicles-grid related-grid">
                    <?php
                    while ($related_vehicles->have_posts()) :
                        $related_vehicles->the_post();
                        
                        $related_id = get_the_ID();
                        $related_price = get_post_meta($related_id, '_vehicle_price', true);
                        $related_year = get_post_meta($related_id, '_vehicle_year', true);
                        $related_make_terms = wp_get_post_terms($related_id, 'vehicle_make');
                        $related_model_terms = wp_get_post_terms($related_id, 'vehicle_model');
                        
                        $related_make = !empty($related_make_terms) ? $related_make_terms[0]->name : '';
                        $related_model = !empty($related_model_terms) ? $related_model_terms[0]->name : '';
                        ?>
                        
                        <div class="vehicle-card related-card">
                            <div class="vehicle-image">
                                <a href="<?php the_permalink(); ?>">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <?php the_post_thumbnail('medium'); ?>
                                    <?php else : ?>
                                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/no-image.jpg" alt="<?php the_title_attribute(); ?>">
                                    <?php endif; ?>
                                </a>
                            </div>
                            
                            <div class="vehicle-details">
                                <h4 class="vehicle-title"><a href="<?php the_permalink(); ?>">
                                    <?php if ($related_year || $related_make || $related_model) : ?>
                                        <?php echo $related_year ? esc_html($related_year) . ' ' : ''; ?>
                                        <?php echo $related_make ? esc_html($related_make) . ' ' : ''; ?>
                                        <?php echo $related_model ? esc_html($related_model) : ''; ?>
                                    <?php else : ?>
                                        <?php the_title(); ?>
                                    <?php endif; ?>
                                </a></h4>
                                
                                <?php if ($related_price) : ?>
                                    <div class="vehicle-price"><?php echo wc_price($related_price); ?></div>
                                <?php endif; ?>
                                
                                <a href="<?php the_permalink(); ?>" class="button view-details"><?php _e('View Details', 'cardealership-child'); ?></a>
                            </div>
                        </div>
                        
                    <?php endwhile; ?>
                </div>
            </div>
            
            <?php
            wp_reset_postdata();
        endif;
        ?>
        
    <?php endwhile; ?>
</div>

<script>
    jQuery(document).ready(function($) {
        // Image gallery functionality
        $('.vehicle-thumbnails .thumbnail').click(function() {
            var imgSrc = $(this).find('img').attr('src');
            var fullSrc = imgSrc.replace('-150x150', '');
            
            $('.vehicle-thumbnails .thumbnail').removeClass('active');
            $(this).addClass('active');
            
            $('.vehicle-main-image img').attr('src', fullSrc);
        });
        
        // Smooth scroll to sections
        $('a[href^="#"]').click(function(e) {
            e.preventDefault();
            
            var target = $(this.hash);
            if (target.length) {
                $('html, body').animate({
                    scrollTop: target.offset().top - 100
                }, 500);
            }
        });
    });
</script>

<?php
get_footer();
?>
