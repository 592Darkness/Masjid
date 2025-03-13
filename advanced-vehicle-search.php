<?php
/**
 * Advanced Vehicle Search and Filtering
 * Add this code to your functions.php or include it as a separate file
 */

/**
 * Register Vehicle Search Widget
 */
function cardealership_register_vehicle_search_widget() {
    register_widget('Cardealership_Vehicle_Search_Widget');
}
add_action('widgets_init', 'cardealership_register_vehicle_search_widget');

/**
 * Advanced Vehicle Search Widget
 */
class Cardealership_Vehicle_Search_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'cardealership_vehicle_search',
            __('Vehicle Search', 'cardealership-child'),
            array('description' => __('Advanced search form for vehicles', 'cardealership-child'))
        );
    }
    
    public function widget($args, $instance) {
        $title = apply_filters('widget_title', $instance['title']);
        $show_price_filter = isset($instance['show_price_filter']) ? $instance['show_price_filter'] : true;
        $show_year_filter = isset($instance['show_year_filter']) ? $instance['show_year_filter'] : true;
        $show_mileage_filter = isset($instance['show_mileage_filter']) ? $instance['show_mileage_filter'] : true;
        
        echo $args['before_widget'];
        
        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        
        // Get search parameters from URL
        $selected_make = isset($_GET['vehicle_make']) ? $_GET['vehicle_make'] : '';
        $selected_model = isset($_GET['vehicle_model']) ? $_GET['vehicle_model'] : '';
        $selected_type = isset($_GET['vehicle_type']) ? $_GET['vehicle_type'] : '';
        $min_price = isset($_GET['min_price']) ? $_GET['min_price'] : '';
        $max_price = isset($_GET['max_price']) ? $_GET['max_price'] : '';
        $min_year = isset($_GET['min_year']) ? $_GET['min_year'] : '';
        $max_year = isset($_GET['max_year']) ? $_GET['max_year'] : '';
        $max_mileage = isset($_GET['max_mileage']) ? $_GET['max_mileage'] : '';
        $transmission = isset($_GET['transmission']) ? $_GET['transmission'] : '';
        $financing = isset($_GET['financing']) ? $_GET['financing'] : '';
        
        // Get all vehicle makes
        $makes = get_terms(array(
            'taxonomy' => 'vehicle_make',
            'hide_empty' => true,
        ));
        
        // Get all vehicle types
        $types = get_terms(array(
            'taxonomy' => 'vehicle_type',
            'hide_empty' => true,
        ));
        
        ?>
        <div class="vehicle-search-form">
            <form method="get" action="<?php echo esc_url(get_post_type_archive_link('vehicle')); ?>">
                <div class="search-field">
                    <label for="vehicle_make"><?php _e('Make', 'cardealership-child'); ?></label>
                    <select name="vehicle_make" id="vehicle_make" class="make-select">
                        <option value=""><?php _e('Any Make', 'cardealership-child'); ?></option>
                        <?php foreach ($makes as $make) : ?>
                            <option value="<?php echo esc_attr($make->slug); ?>" <?php selected($selected_make, $make->slug); ?>><?php echo esc_html($make->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="search-field">
                    <label for="vehicle_model"><?php _e('Model', 'cardealership-child'); ?></label>
                    <select name="vehicle_model" id="vehicle_model" class="model-select">
                        <option value=""><?php _e('Any Model', 'cardealership-child'); ?></option>
                        <?php
                        // If make is selected, get models for that make
                        if (!empty($selected_make)) {
                            $make_term = get_term_by('slug', $selected_make, 'vehicle_make');
                            
                            if ($make_term) {
                                $models = get_terms(array(
                                    'taxonomy' => 'vehicle_model',
                                    'hide_empty' => true,
                                    'meta_query' => array(
                                        array(
                                            'key' => 'vehicle_make_id',
                                            'value' => $make_term->term_id,
                                            'compare' => '=',
                                        ),
                                    ),
                                ));
                                
                                if (!empty($models) && !is_wp_error($models)) {
                                    foreach ($models as $model) {
                                        echo '<option value="' . esc_attr($model->slug) . '" ' . selected($selected_model, $model->slug, false) . '>' . esc_html($model->name) . '</option>';
                                    }
                                }
                            }
                        }
                        ?>
                    </select>
                </div>
                
                <div class="search-field">
                    <label for="vehicle_type"><?php _e('Type', 'cardealership-child'); ?></label>
                    <select name="vehicle_type" id="vehicle_type">
                        <option value=""><?php _e('Any Type', 'cardealership-child'); ?></option>
                        <?php foreach ($types as $type) : ?>
                            <option value="<?php echo esc_attr($type->slug); ?>" <?php selected($selected_type, $type->slug); ?>><?php echo esc_html($type->name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <?php if ($show_price_filter) : ?>
                    <div class="search-field price-range">
                        <label><?php _e('Price Range', 'cardealership-child'); ?></label>
                        <div class="range-inputs">
                            <input type="number" name="min_price" placeholder="<?php _e('Min Price', 'cardealership-child'); ?>" value="<?php echo esc_attr($min_price); ?>" min="0" step="10000">
                            <span class="range-separator">-</span>
                            <input type="number" name="max_price" placeholder="<?php _e('Max Price', 'cardealership-child'); ?>" value="<?php echo esc_attr($max_price); ?>" min="0" step="10000">
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if ($show_year_filter) : ?>
                    <div class="search-field year-range">
                        <label><?php _e('Year Range', 'cardealership-child'); ?></label>
                        <div class="range-inputs">
                            <input type="number" name="min_year" placeholder="<?php _e('Min Year', 'cardealership-child'); ?>" value="<?php echo esc_attr($min_year); ?>" min="1900" max="<?php echo date('Y') + 1; ?>">
                            <span class="range-separator">-</span>
                            <input type="number" name="max_year" placeholder="<?php _e('Max Year', 'cardealership-child'); ?>" value="<?php echo esc_attr($max_year); ?>" min="1900" max="<?php echo date('Y') + 1; ?>">
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if ($show_mileage_filter) : ?>
                    <div class="search-field">
                        <label for="max_mileage"><?php _e('Max Mileage (km)', 'cardealership-child'); ?></label>
                        <input type="number" name="max_mileage" id="max_mileage" value="<?php echo esc_attr($max_mileage); ?>" min="0" step="1000">
                    </div>
                <?php endif; ?>
                
                <div class="search-field">
                    <label for="transmission"><?php _e('Transmission', 'cardealership-child'); ?></label>
                    <select name="transmission" id="transmission">
                        <option value=""><?php _e('Any Transmission', 'cardealership-child'); ?></option>
                        <option value="automatic" <?php selected($transmission, 'automatic'); ?>><?php _e('Automatic', 'cardealership-child'); ?></option>
                        <option value="manual" <?php selected($transmission, 'manual'); ?>><?php _e('Manual', 'cardealership-child'); ?></option>
                        <option value="cvt" <?php selected($transmission, 'cvt'); ?>><?php _e('CVT', 'cardealership-child'); ?></option>
                    </select>
                </div>
                
                <div class="search-field">
                    <label for="financing"><?php _e('Financing', 'cardealership-child'); ?></label>
                    <select name="financing" id="financing">
                        <option value=""><?php _e('Any', 'cardealership-child'); ?></option>
                        <option value="1" <?php selected($financing, '1'); ?>><?php _e('Available', 'cardealership-child'); ?></option>
                    </select>
                </div>
                
                <div class="search-actions">
                    <button type="submit" class="search-button"><?php _e('Search Vehicles', 'cardealership-child'); ?></button>
                    <?php if (isset($_GET['vehicle_make']) || isset($_GET['vehicle_model']) || isset($_GET['vehicle_type']) || isset($_GET['min_price'])) : ?>
                        <a href="<?php echo esc_url(get_post_type_archive_link('vehicle')); ?>" class="reset-button"><?php _e('Reset Filters', 'cardealership-child'); ?></a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            // Dynamic model dropdown based on selected make
            $('#vehicle_make').change(function() {
                var make = $(this).val();
                var modelSelect = $('#vehicle_model');
                
                // Clear current options
                modelSelect.html('<option value=""><?php echo esc_js(__('Any Model', 'cardealership-child')); ?></option>');
                
                if (make) {
                    // AJAX request to get models
                    $.ajax({
                        url: '<?php echo admin_url('admin-ajax.php'); ?>',
                        type: 'POST',
                        data: {
                            action: 'get_vehicle_models',
                            make: make
                        },
                        success: function(response) {
                            if (response.success) {
                                // Add new options
                                $.each(response.data, function(i, model) {
                                    modelSelect.append('<option value="' + model.slug + '">' + model.name + '</option>');
                                });
                            }
                        }
                    });
                }
            });
        });
        </script>
        <?php
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : __('Find Your Vehicle', 'cardealership-child');
        $show_price_filter = isset($instance['show_price_filter']) ? (bool) $instance['show_price_filter'] : true;
        $show_year_filter = isset($instance['show_year_filter']) ? (bool) $instance['show_year_filter'] : true;
        $show_mileage_filter = isset($instance['show_mileage_filter']) ? (bool) $instance['show_mileage_filter'] : true;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'cardealership-child'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <input type="checkbox" id="<?php echo $this->get_field_id('show_price_filter'); ?>" name="<?php echo $this->get_field_name('show_price_filter'); ?>" <?php checked($show_price_filter); ?>>
            <label for="<?php echo $this->get_field_id('show_price_filter'); ?>"><?php _e('Show Price Filter', 'cardealership-child'); ?></label>
        </p>
        <p>
            <input type="checkbox" id="<?php echo $this->get_field_id('show_year_filter'); ?>" name="<?php echo $this->get_field_name('show_year_filter'); ?>" <?php checked($show_year_filter); ?>>
            <label for="<?php echo $this->get_field_id('show_year_filter'); ?>"><?php _e('Show Year Filter', 'cardealership-child'); ?></label>
        </p>
        <p>
            <input type="checkbox" id="<?php echo $this->get_field_id('show_mileage_filter'); ?>" name="<?php echo $this->get_field_name('show_mileage_filter'); ?>" <?php checked($show_mileage_filter); ?>>
            <label for="<?php echo $this->get_field_id('show_mileage_filter'); ?>"><?php _e('Show Mileage Filter', 'cardealership-child'); ?></label>
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['show_price_filter'] = isset($new_instance['show_price_filter']) ? (bool) $new_instance['show_price_filter'] : false;
        $instance['show_year_filter'] = isset($new_instance['show_year_filter']) ? (bool) $new_instance['show_year_filter'] : false;
        $instance['show_mileage_filter'] = isset($new_instance['show_mileage_filter']) ? (bool) $new_instance['show_mileage_filter'] : false;
        
        return $instance;
    }
}

/**
 * AJAX handler for getting vehicle models
 */
function cardealership_get_vehicle_models_ajax() {
    $make = isset($_POST['make']) ? sanitize_text_field($_POST['make']) : '';
    
    if (empty($make)) {
        wp_send_json_error();
    }
    
    $make_term = get_term_by('slug', $make, 'vehicle_make');
    
    if (!$make_term) {
        wp_send_json_error();
    }
    
    $models = get_terms(array(
        'taxonomy' => 'vehicle_model',
        'hide_empty' => true,
        'meta_query' => array(
            array(
                'key' => 'vehicle_make_id',
                'value' => $make_term->term_id,
                'compare' => '=',
            ),
        ),
    ));
    
    if (empty($models) || is_wp_error($models)) {
        wp_send_json_error();
    }
    
    $model_data = array();
    
    foreach ($models as $model) {
        $model_data[] = array(
            'slug' => $model->slug,
            'name' => $model->name
        );
    }
    
    wp_send_json_success($model_data);
}
add_action('wp_ajax_get_vehicle_models', 'cardealership_get_vehicle_models_ajax');
add_action('wp_ajax_nopriv_get_vehicle_models', 'cardealership_get_vehicle_models_ajax');

/**
 * Vehicle Search Shortcode
 */
function cardealership_vehicle_search_shortcode($atts) {
    $atts = shortcode_atts(array(
        'title' => __('Find Your Vehicle', 'cardealership-child'),
        'show_price_filter' => true,
        'show_year_filter' => true,
        'show_mileage_filter' => true,
        'horizontal' => false,
    ), $atts);
    
    ob_start();
    
    // Get search parameters from URL
    $selected_make = isset($_GET['vehicle_make']) ? $_GET['vehicle_make'] : '';
    $selected_model = isset($_GET['vehicle_model']) ? $_GET['vehicle_model'] : '';
    $selected_type = isset($_GET['vehicle_type']) ? $_GET['vehicle_type'] : '';
    $min_price = isset($_GET['min_price']) ? $_GET['min_price'] : '';
    $max_price = isset($_GET['max_price']) ? $_GET['max_price'] : '';
    $min_year = isset($_GET['min_year']) ? $_GET['min_year'] : '';
    $max_year = isset($_GET['max_year']) ? $_GET['max_year'] : '';
    $max_mileage = isset($_GET['max_mileage']) ? $_GET['max_mileage'] : '';
    $transmission = isset($_GET['transmission']) ? $_GET['transmission'] : '';
    $financing = isset($_GET['financing']) ? $_GET['financing'] : '';
    
    // Get all vehicle makes
    $makes = get_terms(array(
        'taxonomy' => 'vehicle_make',
        'hide_empty' => true,
    ));
    
    // Get all vehicle types
    $types = get_terms(array(
        'taxonomy' => 'vehicle_type',
        'hide_empty' => true,
    ));
    
    $form_class = $atts['horizontal'] ? 'vehicle-search-form horizontal' : 'vehicle-search-form';
    ?>
    <div class="vehicle-search-shortcode">
        <?php if (!empty($atts['title'])) : ?>
            <h3 class="search-title"><?php echo esc_html($atts['title']); ?></h3>
        <?php endif; ?>
        
        <div class="<?php echo esc_attr($form_class); ?>">
            <form method="get" action="<?php echo esc_url(get_post_type_archive_link('vehicle')); ?>">
                <div class="search-fields">
                    <div class="search-field">
                        <label for="vehicle_make_sc"><?php _e('Make', 'cardealership-child'); ?></label>
                        <select name="vehicle_make" id="vehicle_make_sc" class="make-select">
                            <option value=""><?php _e('Any Make', 'cardealership-child'); ?></option>
                            <?php foreach ($makes as $make) : ?>
                                <option value="<?php echo esc_attr($make->slug); ?>" <?php selected($selected_make, $make->slug); ?>><?php echo esc_html($make->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="search-field">
                        <label for="vehicle_model_sc"><?php _e('Model', 'cardealership-child'); ?></label>
                        <select name="vehicle_model" id="vehicle_model_sc" class="model-select">
                            <option value=""><?php _e('Any Model', 'cardealership-child'); ?></option>
                            <?php
                            // If make is selected, get models for that make
                            if (!empty($selected_make)) {
                                $make_term = get_term_by('slug', $selected_make, 'vehicle_make');
                                
                                if ($make_term) {
                                    $models = get_terms(array(
                                        'taxonomy' => 'vehicle_model',
                                        'hide_empty' => true,
                                        'meta_query' => array(
                                            array(
                                                'key' => 'vehicle_make_id',
                                                'value' => $make_term->term_id,
                                                'compare' => '=',
                                            ),
                                        ),
                                    ));
                                    
                                    if (!empty($models) && !is_wp_error($models)) {
                                        foreach ($models as $model) {
                                            echo '<option value="' . esc_attr($model->slug) . '" ' . selected($selected_model, $model->slug, false) . '>' . esc_html($model->name) . '</option>';
                                        }
                                    }
                                }
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="search-field">
                        <label for="vehicle_type_sc"><?php _e('Type', 'cardealership-child'); ?></label>
                        <select name="vehicle_type" id="vehicle_type_sc">
                            <option value=""><?php _e('Any Type', 'cardealership-child'); ?></option>
                            <?php foreach ($types as $type) : ?>
                                <option value="<?php echo esc_attr($type->slug); ?>" <?php selected($selected_type, $type->slug); ?>><?php echo esc_html($type->name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <?php if ($atts['show_price_filter']) : ?>
                        <div class="search-field price-range">
                            <label><?php _e('Price Range', 'cardealership-child'); ?></label>
                            <div class="range-inputs">
                                <input type="number" name="min_price" placeholder="<?php _e('Min', 'cardealership-child'); ?>" value="<?php echo esc_attr($min_price); ?>" min="0" step="10000">
                                <span class="range-separator">-</span>
                                <input type="number" name="max_price" placeholder="<?php _e('Max', 'cardealership-child'); ?>" value="<?php echo esc_attr($max_price); ?>" min="0" step="10000">
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($atts['show_year_filter']) : ?>
                        <div class="search-field year-range">
                            <label><?php _e('Year', 'cardealership-child'); ?></label>
                            <div class="range-inputs">
                                <input type="number" name="min_year" placeholder="<?php _e('From', 'cardealership-child'); ?>" value="<?php echo esc_attr($min_year); ?>" min="1900" max="<?php echo date('Y') + 1; ?>">
                                <span class="range-separator">-</span>
                                <input type="number" name="max_year" placeholder="<?php _e('To', 'cardealership-child'); ?>" value="<?php echo esc_attr($max_year); ?>" min="1900" max="<?php echo date('Y') + 1; ?>">
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="search-field advanced-filters">
                        <button type="button" class="toggle-advanced-search"><?php _e('Advanced Search', 'cardealership-child'); ?> <span class="toggle-icon">+</span></button>
                        
                        <div class="advanced-search-fields" style="display: none;">
                            <?php if ($atts['show_mileage_filter']) : ?>
                                <div class="search-field">
                                    <label for="max_mileage_sc"><?php _e('Max Mileage (km)', 'cardealership-child'); ?></label>
                                    <input type="number" name="max_mileage" id="max_mileage_sc" value="<?php echo esc_attr($max_mileage); ?>" min="0" step="1000">
                                </div>
                            <?php endif; ?>
                            
                            <div class="search-field">
                                <label for="transmission_sc"><?php _e('Transmission', 'cardealership-child'); ?></label>
                                <select name="transmission" id="transmission_sc">
                                    <option value=""><?php _e('Any Transmission', 'cardealership-child'); ?></option>
                                    <option value="automatic" <?php selected($transmission, 'automatic'); ?>><?php _e('Automatic', 'cardealership-child'); ?></option>
                                    <option value="manual" <?php selected($transmission, 'manual'); ?>><?php _e('Manual', 'cardealership-child'); ?></option>
                                    <option value="cvt" <?php selected($transmission, 'cvt'); ?>><?php _e('CVT', 'cardealership-child'); ?></option>
                                </select>
                            </div>
                            
                            <div class="search-field">
                                <label for="financing_sc"><?php _e('Financing', 'cardealership-child'); ?></label>
                                <select name="financing" id="financing_sc">
                                    <option value=""><?php _e('Any', 'cardealership-child'); ?></option>
                                    <option value="1" <?php selected($financing, '1'); ?>><?php _e('Available', 'cardealership-child'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="search-actions">
                    <button type="submit" class="search-button"><?php _e('Search', 'cardealership-child'); ?></button>
                    <?php if (isset($_GET['vehicle_make']) || isset($_GET['vehicle_model']) || isset($_GET['vehicle_type']) || isset($_GET['min_price'])) : ?>
                        <a href="<?php echo esc_url(get_post_type_archive_link('vehicle')); ?>" class="reset-button"><?php _e('Reset', 'cardealership-child'); ?></a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // Toggle advanced search fields
        $('.toggle-advanced-search').click(function() {
            $('.advanced-search-fields').slideToggle(300);
            var icon = $(this).find('.toggle-icon');
            icon.text(icon.text() === '+' ? '-' : '+');
        });
        
        // Dynamic model dropdown based on selected make
        $('#vehicle_make_sc').change(function() {
            var make = $(this).val();
            var modelSelect = $('#vehicle_model_sc');
            
            // Clear current options
            modelSelect.html('<option value=""><?php echo esc_js(__('Any Model', 'cardealership-child')); ?></option>');
            
            if (make) {
                // AJAX request to get models
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'POST',
                    data: {
                        action: 'get_vehicle_models',
                        make: make
                    },
                    success: function(response) {
                        if (response.success) {
                            // Add new options
                            $.each(response.data, function(i, model) {
                                modelSelect.append('<option value="' + model.slug + '">' + model.name + '</option>');
                            });
                        }
                    }
                });
            }
        });
    });
    </script>
    <?php
    
    return ob_get_clean();
}
add_shortcode('vehicle_search', 'cardealership_vehicle_search_shortcode');

/**
 * Vehicle filter function to modify WP_Query
 */
function cardealership_vehicle_filter_query($query) {
    if (!is_admin() && $query->is_main_query() && (is_post_type_archive('vehicle') || is_tax('vehicle_make') || is_tax('vehicle_model') || is_tax('vehicle_type'))) {
        
        // Make filter
        if (isset($_GET['vehicle_make']) && !empty($_GET['vehicle_make'])) {
            $tax_query = $query->get('tax_query');
            if (!is_array($tax_query)) {
                $tax_query = array();
            }
            
            $tax_query[] = array(
                'taxonomy' => 'vehicle_make',
                'field' => 'slug',
                'terms' => sanitize_text_field($_GET['vehicle_make']),
            );
            
            $query->set('tax_query', $tax_query);
        }
        
        // Model filter
        if (isset($_GET['vehicle_model']) && !empty($_GET['vehicle_model'])) {
            $tax_query = $query->get('tax_query');
            if (!is_array($tax_query)) {
                $tax_query = array();
            }
            
            $tax_query[] = array(
                'taxonomy' => 'vehicle_model',
                'field' => 'slug',
                'terms' => sanitize_text_field($_GET['vehicle_model']),
            );
            
            $query->set('tax_query', $tax_query);
        }
        
        // Type filter
        if (isset($_GET['vehicle_type']) && !empty($_GET['vehicle_type'])) {
            $tax_query = $query->get('tax_query');
            if (!is_array($tax_query)) {
                $tax_query = array();
            }
            
            $tax_query[] = array(
                'taxonomy' => 'vehicle_type',
                'field' => 'slug',
                'terms' => sanitize_text_field($_GET['vehicle_type']),
            );
            
            $query->set('tax_query', $tax_query);
        }
        
        // Meta query for price range, year range, mileage, transmission, and financing
        $meta_query = array();
        
        // Price range
        if (isset($_GET['min_price']) && !empty($_GET['min_price'])) {
            $meta_query[] = array(
                'key' => '_vehicle_price',
                'value' => intval($_GET['min_price']),
                'compare' => '>=',
                'type' => 'NUMERIC',
            );
        }
        
        if (isset($_GET['max_price']) && !empty($_GET['max_price'])) {
            $meta_query[] = array(
                'key' => '_vehicle_price',
                'value' => intval($_GET['max_price']),
                'compare' => '<=',
                'type' => 'NUMERIC',
            );
        }
        
        // Year range
        if (isset($_GET['min_year']) && !empty($_GET['min_year'])) {
            $meta_query[] = array(
                'key' => '_vehicle_year',
                'value' => intval($_GET['min_year']),
                'compare' => '>=',
                'type' => 'NUMERIC',
            );
        }
        
        if (isset($_GET['max_year']) && !empty($_GET['max_year'])) {
            $meta_query[] = array(
                'key' => '_vehicle_year',
                'value' => intval($_GET['max_year']),
                'compare' => '<=',
                'type' => 'NUMERIC',
            );
        }
        
        // Max mileage
        if (isset($_GET['max_mileage']) && !empty($_GET['max_mileage'])) {
            $meta_query[] = array(
                'key' => '_vehicle_mileage',
                'value' => intval($_GET['max_mileage']),
                'compare' => '<=',
                'type' => 'NUMERIC',
            );
        }
        
        // Transmission
        if (isset($_GET['transmission']) && !empty($_GET['transmission'])) {
            $meta_query[] = array(
                'key' => '_vehicle_transmission',
                'value' => sanitize_text_field($_GET['transmission']),
                'compare' => '=',
            );
        }
        
        // Financing
        if (isset($_GET['financing']) && $_GET['financing'] == 1) {
            $meta_query[] = array(
                'key' => '_vehicle_financing_available',
                'value' => '1',
                'compare' => '=',
            );
        }
        
        // Apply meta query if we have conditions
        if (!empty($meta_query)) {
            $query->set('meta_query', $meta_query);
        }
        
        // Sorting options
        if (isset($_GET['orderby'])) {
            switch ($_GET['orderby']) {
                case 'price_low':
                    $query->set('meta_key', '_vehicle_price');
                    $query->set('orderby', 'meta_value_num');
                    $query->set('order', 'ASC');
                    break;
                    
                case 'price_high':
                    $query->set('meta_key', '_vehicle_price');
                    $query->set('orderby', 'meta_value_num');
                    $query->set('order', 'DESC');
                    break;
                    
                case 'year_new':
                    $query->set('meta_key', '_vehicle_year');
                    $query->set('orderby', 'meta_value_num');
                    $query->set('order', 'DESC');
                    break;
                    
                case 'year_old':
                    $query->set('meta_key', '_vehicle_year');
                    $query->set('orderby', 'meta_value_num');
                    $query->set('order', 'ASC');
                    break;
                    
                case 'mileage_low':
                    $query->set('meta_key', '_vehicle_mileage');
                    $query->set('orderby', 'meta_value_num');
                    $query->set('order', 'ASC');
                    break;
                    
                case 'mileage_high':
                    $query->set('meta_key', '_vehicle_mileage');
                    $query->set('orderby', 'meta_value_num');
                    $query->set('order', 'DESC');
                    break;
                    
                case 'date':
                default:
                    $query->set('orderby', 'date');
                    $query->set('order', 'DESC');
                    break;
            }
        }
    }
    
    return $query;
}
add_action('pre_get_posts', 'cardealership_vehicle_filter_query');

/**
 * Register search result template for vehicles
 */
function cardealership_search_template($template) {
    if (is_search() && get_query_var('post_type') === 'vehicle') {
        $new_template = locate_template(array('search-vehicle.php'));
        if (!empty($new_template)) {
            return $new_template;
        }
    }
    
    return $template;
}
add_filter('template_include', 'cardealership_search_template');

/**
 * Display active filters on archive page
 */
function cardealership_display_active_filters() {
    if (!is_post_type_archive('vehicle') && !is_tax('vehicle_make') && !is_tax('vehicle_model') && !is_tax('vehicle_type')) {
        return;
    }
    
    $active_filters = array();
    
    // Check for active filters
    if (isset($_GET['vehicle_make']) && !empty($_GET['vehicle_make'])) {
        $make_term = get_term_by('slug', $_GET['vehicle_make'], 'vehicle_make');
        if ($make_term) {
            $active_filters[] = array(
                'label' => __('Make', 'cardealership-child'),
                'value' => $make_term->name,
                'param' => 'vehicle_make',
            );
        }
    }
    
    if (isset($_GET['vehicle_model']) && !empty($_GET['vehicle_model'])) {
        $model_term = get_term_by('slug', $_GET['vehicle_model'], 'vehicle_model');
        if ($model_term) {
            $active_filters[] = array(
                'label' => __('Model', 'cardealership-child'),
                'value' => $model_term->name,
                'param' => 'vehicle_model',
            );
        }
    }
    
    if (isset($_GET['vehicle_type']) && !empty($_GET['vehicle_type'])) {
        $type_term = get_term_by('slug', $_GET['vehicle_type'], 'vehicle_type');
        if ($type_term) {
            $active_filters[] = array(
                'label' => __('Type', 'cardealership-child'),
                'value' => $type_term->name,
                'param' => 'vehicle_type',
            );
        }
    }
    
    if (isset($_GET['min_price']) && !empty($_GET['min_price'])) {
        $active_filters[] = array(
            'label' => __('Min Price', 'cardealership-child'),
            'value' => 'GYD ' . number_format($_GET['min_price']),
            'param' => 'min_price',
        );
    }
    
    if (isset($_GET['max_price']) && !empty($_GET['max_price'])) {
        $active_filters[] = array(
            'label' => __('Max Price', 'cardealership-child'),
            'value' => 'GYD ' . number_format($_GET['max_price']),
            'param' => 'max_price',
        );
    }
    
    if (isset($_GET['min_year']) && !empty($_GET['min_year'])) {
        $active_filters[] = array(
            'label' => __('Min Year', 'cardealership-child'),
            'value' => $_GET['min_year'],
            'param' => 'min_year',
        );
    }
    
    if (isset($_GET['max_year']) && !empty($_GET['max_year'])) {
        $active_filters[] = array(
            'label' => __('Max Year', 'cardealership-child'),
            'value' => $_GET['max_year'],
            'param' => 'max_year',
        );
    }
    
    if (isset($_GET['max_mileage']) && !empty($_GET['max_mileage'])) {
        $active_filters[] = array(
            'label' => __('Max Mileage', 'cardealership-child'),
            'value' => number_format($_GET['max_mileage']) . ' km',
            'param' => 'max_mileage',
        );
    }
    
    if (isset($_GET['transmission']) && !empty($_GET['transmission'])) {
        $transmission_labels = array(
            'automatic' => __('Automatic', 'cardealership-child'),
            'manual' => __('Manual', 'cardealership-child'),
            'cvt' => __('CVT', 'cardealership-child'),
        );
        
        $transmission = $_GET['transmission'];
        $transmission_label = isset($transmission_labels[$transmission]) ? $transmission_labels[$transmission] : $transmission;
        
        $active_filters[] = array(
            'label' => __('Transmission', 'cardealership-child'),
            'value' => $transmission_label,
            'param' => 'transmission',
        );
    }
    
    if (isset($_GET['financing']) && $_GET['financing'] == 1) {
        $active_filters[] = array(
            'label' => __('Financing', 'cardealership-child'),
            'value' => __('Available', 'cardealership-child'),
            'param' => 'financing',
        );
    }
    
    // Display active filters if we have any
    if (!empty($active_filters)) {
        echo '<div class="active-filters">';
        echo '<h4>' . __('Active Filters:', 'cardealership-child') . '</h4>';
        echo '<div class="filter-tags">';
        
        foreach ($active_filters as $filter) {
            echo '<span class="filter-tag">';
            echo esc_html($filter['label']) . ': ' . esc_html($filter['value']);
            
            // Create the remove URL
            $current_url = remove_query_arg($filter['param']);
            echo '<a href="' . esc_url($current_url) . '" class="remove-filter" title="' . __('Remove filter', 'cardealership-child') . '">×</a>';
            echo '</span>';
        }
        
        echo '<a href="' . esc_url(get_post_type_archive_link('vehicle')) . '" class="clear-all-filters">' . __('Clear All Filters', 'cardealership-child') . '</a>';
        echo '</div>';
        echo '</div>';
    }
}
add_action('cardealership_before_vehicle_archive', 'cardealership_display_active_filters');

/**
 * Add CSS for vehicle search form
 */
function cardealership_vehicle_search_css() {
    ?>
    <style>
        /* Vehicle Search Styles */
        .vehicle-search-form {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .vehicle-search-form.horizontal .search-fields {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .vehicle-search-form.horizontal .search-field {
            flex: 1;
            min-width: 180px;
        }
        
        .search-field {
            margin-bottom: 15px;
        }
        
        .search-field label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            font-size: 14px;
        }
        
        .search-field select,
        .search-field input {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .range-inputs {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .range-separator {
            font-weight: bold;
        }
        
        .search-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .search-button {
            background-color: #d9534f;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .search-button:hover {
            background-color: #c9302c;
        }
        
        .reset-button {
            color: #666;
            text-decoration: underline;
        }
        
        .toggle-advanced-search {
            background: none;
            border: none;
            color: #0073aa;
            padding: 0;
            font-size: 14px;
            cursor: pointer;
            text-decoration: underline;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .toggle-icon {
            font-weight: bold;
        }
        
        .advanced-search-fields {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        
        /* Active Filters */
        .active-filters {
            background-color: #f8f9fa;
            padding: 15px 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .active-filters h4 {
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 16px;
        }
        
        .filter-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }
        
        .filter-tag {
            background-color: #e9ecef;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .remove-filter {
            color: #666;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
        }
        
        .clear-all-filters {
            margin-left: auto;
            color: #d9534f;
            text-decoration: underline;
            font-size: 14px;
        }
        
        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .vehicle-search-form.horizontal .search-field {
                min-width: 100%;
            }
            
            .search-actions {
                flex-direction: column;
                align-items: stretch;
            }
            
            .filter-tags {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .clear-all-filters {
                margin-left: 0;
                margin-top: 10px;
            }
        }
    </style>
    <?php
}
add_action('wp_head', 'cardealership_vehicle_search_css');

/**
 * Add structured data for vehicle search results
 */
function cardealership_vehicle_schema_markup() {
    if (!is_singular('vehicle')) {
        return;
    }
    
    $post_id = get_the_ID();
    
    // Get vehicle data
    $year = get_post_meta($post_id, '_vehicle_year', true);
    $price = get_post_meta($post_id, '_vehicle_price', true);
    $mileage = get_post_meta($post_id, '_vehicle_mileage', true);
    $engine = get_post_meta($post_id, '_vehicle_engine', true);
    $transmission = get_post_meta($post_id, '_vehicle_transmission', true);
    $color = get_post_meta($post_id, '_vehicle_color', true);
    $vin = get_post_meta($post_id, '_vehicle_vin', true);
    $features = get_post_meta($post_id, '_vehicle_features', true);
    
    $make_terms = wp_get_post_terms($post_id, 'vehicle_make');
    $model_terms = wp_get_post_terms($post_id, 'vehicle_model');
    $type_terms = wp_get_post_terms($post_id, 'vehicle_type');
    
    $make = !empty($make_terms) ? $make_terms[0]->name : '';
    $model = !empty($model_terms) ? $model_terms[0]->name : '';
    $type = !empty($type_terms) ? $type_terms[0]->name : '';
    
    $title = get_the_title();
    $description = get_the_excerpt();
    $url = get_permalink();
    
    // Build features array
    $feature_array = array();
    if ($features) {
        $feature_list = explode("\n", $features);
        foreach ($feature_list as $feature) {
            if (trim($feature)) {
                $feature_array[] = trim($feature);
            }
        }
    }
    
    // Get image URL
    $image_url = '';
    if (has_post_thumbnail()) {
        $image_id = get_post_thumbnail_id();
        $image_data = wp_get_attachment_image_src($image_id, 'full');
        if ($image_data) {
            $image_url = $image_data[0];
        }
    }
    
    // Build structured data array
    $schema = array(
        '@context' => 'https://schema.org/',
        '@type' => 'Vehicle',
        'name' => $title,
        'description' => $description,
        'url' => $url,
        'vehicleIdentificationNumber' => $vin,
        'modelDate' => $year,
        'mileageFromOdometer' => array(
            '@type' => 'QuantitativeValue',
            'value' => $mileage,
            'unitCode' => 'KMT'
        ),
        'vehicleEngine' => $engine,
        'vehicleTransmission' => $transmission,
        'color' => $color,
        'brand' => array(
            '@type' => 'Brand',
            'name' => $make
        ),
        'model' => $model,
        'vehicleConfiguration' => $type,
        'offers' => array(
            '@type' => 'Offer',
            'price' => $price,
            'priceCurrency' => 'GYD',
            'availability' => 'https://schema.org/InStock'
        )
    );
    
    // Add images if available
    if ($image_url) {
        $schema['image'] = $image_url;
    }
    
    // Add features if available
    if (!empty($feature_array)) {
        $schema['additionalProperty'] = array();
        
        foreach ($feature_array as $feature) {
            $schema['additionalProperty'][] = array(
                '@type' => 'PropertyValue',
                'name' => 'feature',
                'value' => $feature
            );
        }
    }
    
    // Output schema markup
    echo '<script type="application/ld+json">' . json_encode($schema) . '</script>';
}
add_action('wp_head', 'cardealership_vehicle_schema_markup');
