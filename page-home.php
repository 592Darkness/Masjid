<?php
/**
 * Template Name: Dealership Homepage
 * 
 * Create this file as page-home.php in your child theme
 */
get_header();
?>

<div class="home-slider">
    <?php
    // Slider content
    $slides = array(
        array(
            'image' => get_stylesheet_directory_uri() . '/images/slider/slide1.jpg',
            'title' => 'Find Your Perfect Vehicle',
            'description' => 'Explore our extensive inventory of new and used vehicles with flexible financing options.',
            'button_text' => 'View Inventory',
            'button_url' => site_url('/vehicles/'),
        ),
        array(
            'image' => get_stylesheet_directory_uri() . '/images/slider/slide2.jpg',
            'title' => 'Quality Car Parts',
            'description' => 'Get genuine parts for all makes and models with nationwide delivery across Guyana.',
            'button_text' => 'Shop Parts',
            'button_url' => site_url('/shop/'),
        ),
        array(
            'image' => get_stylesheet_directory_uri() . '/images/slider/slide3.jpg',
            'title' => 'Flexible Financing Options',
            'description' => 'Drive away today with our easy financing solutions and competitive rates.',
            'button_text' => 'Apply Now',
            'button_url' => site_url('/financing-application/'),
        )
    );
    
    foreach ($slides as $index => $slide) {
        $active_class = ($index === 0) ? 'active' : '';
        ?>
        <div class="slide <?php echo $active_class; ?>" style="background-image: url('<?php echo esc_url($slide['image']); ?>');">
            <div class="slide-content">
                <h2 class="slide-title"><?php echo esc_html($slide['title']); ?></h2>
                <p class="slide-description"><?php echo esc_html($slide['description']); ?></p>
                <a href="<?php echo esc_url($slide['button_url']); ?>" class="button"><?php echo esc_html($slide['button_text']); ?></a>
            </div>
        </div>
        <?php
    }
    ?>
</div>

<div class="container">
    <!-- Featured Vehicles Section -->
    <div class="home-section">
        <h2 class="section-title">Featured Vehicles</h2>
        
        <?php
        // Get featured vehicles
        $args = array(
            'post_type' => 'vehicle',
            'posts_per_page' => 4,
            'meta_query' => array(
                array(
                    'key' => '_featured',
                    'value' => '1',
                    'compare' => '='
                )
            )
        );
        
        $featured_vehicles = new WP_Query($args);
        
        if ($featured_vehicles->have_posts()) :
            echo '<div class="featured-vehicles">';
            
            while ($featured_vehicles->have_posts()) : $featured_vehicles->the_post();
                $post_id = get_the_ID();
                $price = get_post_meta($post_id, '_vehicle_price', true);
                $year = get_post_meta($post_id, '_vehicle_year', true);
                $mileage = get_post_meta($post_id, '_vehicle_mileage', true);
                $financing_available = get_post_meta($post_id, '_vehicle_financing_available', true);
                
                $make_terms = wp_get_post_terms($post_id, 'vehicle_make');
                $model_terms = wp_get_post_terms($post_id, 'vehicle_model');
                
                $make = !empty($make_terms) ? $make_terms[0]->name : '';
                $model = !empty($model_terms) ? $model_terms[0]->name : '';
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
                        <h3 class="vehicle-title"><a href="<?php the_permalink(); ?>">
                            <?php if ($year || $make || $model) : ?>
                                <?php echo $year ? esc_html($year) . ' ' : ''; ?>
                                <?php echo $make ? esc_html($make) . ' ' : ''; ?>
                                <?php echo $model ? esc_html($model) : ''; ?>
                            <?php else : ?>
                                <?php the_title(); ?>
                            <?php endif; ?>
                        </a></h3>
                        
                        <?php if ($price) : ?>
                            <div class="vehicle-price"><?php echo wc_price($price); ?></div>
                        <?php endif; ?>
                        
                        <?php if ($mileage) : ?>
                            <div class="vehicle-mileage">
                                <span class="mileage-label"><?php _e('Mileage:', 'cardealership-child'); ?></span>
                                <span class="mileage-value"><?php echo number_format($mileage); ?> km</span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="vehicle-actions">
                            <a href="<?php the_permalink(); ?>" class="button view-details"><?php _e('View Details', 'cardealership-child'); ?></a>
                        </div>
                    </div>
                </div>
                
            <?php
            endwhile;
            
            echo '</div>';
            
            wp_reset_postdata();
        else :
            echo '<p>' . __('No featured vehicles found.', 'cardealership-child') . '</p>';
        endif;
        ?>
        
        <div class="section-action" style="text-align: center; margin-top: 30px;">
            <a href="<?php echo site_url('/vehicles/'); ?>" class="button"><?php _e('View All Vehicles', 'cardealership-child'); ?></a>
        </div>
    </div>
    
    <!-- Services Section -->
    <div class="home-section">
        <h2 class="section-title">Our Services</h2>
        
        <div class="services-grid">
            <div class="service-card">
                <div class="service-icon">
                    <i class="dashicons dashicons-car"></i>
                </div>
                <h3 class="service-title"><?php _e('Vehicle Sales', 'cardealership-child'); ?></h3>
                <p class="service-description"><?php _e('New and used vehicles from top manufacturers with competitive pricing.', 'cardealership-child'); ?></p>
            </div>
            
            <div class="service-card">
                <div class="service-icon">
                    <i class="dashicons dashicons-money-alt"></i>
                </div>
                <h3 class="service-title"><?php _e('Financing', 'cardealership-child'); ?></h3>
                <p class="service-description"><?php _e('Flexible financing options with competitive interest rates and quick approval.', 'cardealership-child'); ?></p>
            </div>
            
            <div class="service-card">
                <div class="service-icon">
                    <i class="dashicons dashicons-admin-tools"></i>
                </div>
                <h3 class="service-title"><?php _e('Parts & Accessories', 'cardealership-child'); ?></h3>
                <p class="service-description"><?php _e('Genuine parts and accessories for all vehicle makes and models.', 'cardealership-child'); ?></p>
            </div>
            
            <div class="service-card">
                <div class="service-icon">
                    <i class="dashicons dashicons-welcome-write-blog"></i>
                </div>
                <h3 class="service-title"><?php _e('Documentation', 'cardealership-child'); ?></h3>
                <p class="service-description"><?php _e('Assistance with vehicle registration, insurance, and other documentation.', 'cardealership-child'); ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Testimonials Section -->
<div class="testimonials">
    <div class="container">
        <h2 class="section-title"><?php _e('Customer Testimonials', 'cardealership-child'); ?></h2>
        
        <div class="testimonial-carousel">
            <div class="testimonial-slide active">
                <div class="testimonial-content">
                    <?php _e('"The financing process was incredibly smooth. I was able to drive home in my new Toyota within days of applying. Their team was helpful throughout the entire process."', 'cardealership-child'); ?>
                </div>
                <div class="testimonial-author"><?php _e('Michael R., Georgetown', 'cardealership-child'); ?></div>
            </div>
            
            <div class="testimonial-slide">
                <div class="testimonial-content">
                    <?php _e('"I needed specific parts for my Honda and they had everything in stock. Delivery was prompt and the prices were very reasonable compared to other places."', 'cardealership-child'); ?>
                </div>
                <div class="testimonial-author"><?php _e('Sarah T., Linden', 'cardealership-child'); ?></div>
            </div>
            
            <div class="testimonial-slide">
                <div class="testimonial-content">
                    <?php _e('"Best car buying experience I\'ve had. No pressure sales tactics and they helped me find the perfect vehicle for my family within my budget."', 'cardealership-child'); ?>
                </div>
                <div class="testimonial-author"><?php _e('David K., New Amsterdam', 'cardealership-child'); ?></div>
            </div>
            
            <div class="testimonial-controls">
                <span class="testimonial-dot active" data-slide="0"></span>
                <span class="testimonial-dot" data-slide="1"></span>
                <span class="testimonial-dot" data-slide="2"></span>
            </div>
        </div>
    </div>
</div>

<!-- Featured Parts Section -->
<div class="container">
    <div class="home-section">
        <h2 class="section-title"><?php _e('Popular Parts & Accessories', 'cardealership-child'); ?></h2>
        
        <?php
        // Get featured products
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => 4,
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field' => 'slug',
                    'terms' => 'car-parts',
                    'include_children' => true
                )
            ),
            'meta_query' => array(
                array(
                    'key' => '_featured',
                    'value' => 'yes',
                    'compare' => '='
                )
            )
        );
        
        $featured_products = new WP_Query($args);
        
        if ($featured_products->have_posts()) :
            echo '<ul class="products columns-4">';
            
            while ($featured_products->have_posts()) : $featured_products->the_post();
                wc_get_template_part('content', 'product');
            endwhile;
            
            echo '</ul>';
            
            wp_reset_postdata();
        else :
            echo '<p>' . __('No featured parts found.', 'cardealership-child') . '</p>';
        endif;
        ?>
        
        <div class="section-action" style="text-align: center; margin-top: 30px;">
            <a href="<?php echo site_url('/shop/car-parts/'); ?>" class="button"><?php _e('Shop All Parts', 'cardealership-child'); ?></a>
        </div>
    </div>
</div>

<!-- Call to Action Section -->
<div class="cta-section">
    <div class="container">
        <h2 class="cta-title"><?php _e('Ready to Find Your Perfect Vehicle?', 'cardealership-child'); ?></h2>
        <p class="cta-description"><?php _e('Browse our inventory, apply for financing, or contact our team to get started today.', 'cardealership-child'); ?></p>
        
        <div class="cta-buttons">
            <a href="<?php echo site_url('/vehicles/'); ?>" class="cta-button"><?php _e('Browse Inventory', 'cardealership-child'); ?></a>
            <a href="<?php echo site_url('/financing-application/'); ?>" class="cta-button"><?php _e('Apply for Financing', 'cardealership-child'); ?></a>
            <a href="<?php echo site_url('/contact/'); ?>" class="cta-button"><?php _e('Contact Us', 'cardealership-child'); ?></a>
        </div>
    </div>
</div>

<!-- Brands Section -->
<div class="brands-section">
    <div class="container">
        <h2 class="section-title"><?php _e('Brands We Carry', 'cardealership-child'); ?></h2>
        
        <div class="brands-grid">
            <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/brands/toyota.png" alt="Toyota" class="brand-logo">
            <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/brands/honda.png" alt="Honda" class="brand-logo">
            <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/brands/ford.png" alt="Ford" class="brand-logo">
            <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/brands/nissan.png" alt="Nissan" class="brand-logo">
            <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/brands/chevrolet.png" alt="Chevrolet" class="brand-logo">
            <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/brands/hyundai.png" alt="Hyundai" class="brand-logo">
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function($) {
        // Home slider functionality
        var currentSlide = 0;
        var totalSlides = $('.slide').length;
        
        function showSlide(index) {
            $('.slide').removeClass('active');
            $('.slide').eq(index).addClass('active');
            currentSlide = index;
        }
        
        function nextSlide() {
            var nextIndex = (currentSlide + 1) % totalSlides;
            showSlide(nextIndex);
        }
        
        // Auto slide
        var slideInterval = setInterval(nextSlide, 5000);
        
        // Reset interval on manual navigation
        $('.slide-nav').click(function() {
            clearInterval(slideInterval);
            slideInterval = setInterval(nextSlide, 5000);
        });
        
        // Testimonial carousel
        $('.testimonial-dot').click(function() {
            var slideIndex = $(this).data('slide');
            
            $('.testimonial-slide').removeClass('active');
            $('.testimonial-slide').eq(slideIndex).addClass('active');
            
            $('.testimonial-dot').removeClass('active');
            $(this).addClass('active');
        });
        
        // Auto rotate testimonials
        var testimonialIndex = 0;
        var totalTestimonials = $('.testimonial-slide').length;
        
        function nextTestimonial() {
            testimonialIndex = (testimonialIndex + 1) % totalTestimonials;
            
            $('.testimonial-slide').removeClass('active');
            $('.testimonial-slide').eq(testimonialIndex).addClass('active');
            
            $('.testimonial-dot').removeClass('active');
            $('.testimonial-dot').eq(testimonialIndex).addClass('active');
        }
        
        var testimonialInterval = setInterval(nextTestimonial, 7000);
        
        // Reset interval on manual navigation
        $('.testimonial-dot').click(function() {
            clearInterval(testimonialInterval);
            testimonialIndex = $(this).data('slide');
            testimonialInterval = setInterval(nextTestimonial, 7000);
        });
    });
</script>

<?php
get_footer();
?>
