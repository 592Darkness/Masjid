/**
 * Main JavaScript file for Car Dealership
 * Save as main.js in your child theme's js folder
 */

jQuery(document).ready(function($) {
    'use strict';
    
    /**
     * Mobile Menu Toggle
     */
    $('.menu-toggle').on('click', function() {
        var $navigation = $('.primary-menu-container');
        var isExpanded = $(this).attr('aria-expanded') === 'true';
        
        // Toggle aria-expanded attribute
        $(this).attr('aria-expanded', !isExpanded);
        
        // Toggle menu visibility
        if (!isExpanded) {
            $navigation.slideDown();
        } else {
            $navigation.slideUp();
        }
    });
    
    /**
     * Home Page Slider
     */
    function initHomeSlider() {
        if ($('.home-slider').length === 0) {
            return;
        }
        
        var $slider = $('.home-slider');
        var $slides = $slider.find('.slide');
        var currentSlide = 0;
        var slideCount = $slides.length;
        var slideInterval;
        
        // Initialize slider
        function initSlider() {
            // Show first slide
            $slides.removeClass('active');
            $slides.eq(0).addClass('active');
            
            // Create navigation dots
            if (slideCount > 1) {
                var $navDots = $('<div class="slider-nav"></div>');
                
                for (var i = 0; i < slideCount; i++) {
                    var $dot = $('<span class="slider-dot"></span>');
                    if (i === 0) {
                        $dot.addClass('active');
                    }
                    $dot.data('slide', i);
                    $navDots.append($dot);
                }
                
                $slider.append($navDots);
                
                // Add navigation dot click handlers
                $('.slider-dot').on('click', function() {
                    var slideIndex = $(this).data('slide');
                    showSlide(slideIndex);
                    resetInterval();
                });
                
                // Add prev/next navigation
                var $prevNext = $('<div class="slider-arrows"><span class="slider-arrow prev">&lsaquo;</span><span class="slider-arrow next">&rsaquo;</span></div>');
                $slider.append($prevNext);
                
                $('.slider-arrow.prev').on('click', function() {
                    prevSlide();
                    resetInterval();
                });
                
                $('.slider-arrow.next').on('click', function() {
                    nextSlide();
                    resetInterval();
                });
                
                // Start autoplay
                startAutoplay();
            }
        }
        
        // Show slide by index
        function showSlide(index) {
            $slides.removeClass('active');
            $('.slider-dot').removeClass('active');
            
            $slides.eq(index).addClass('active');
            $('.slider-dot').eq(index).addClass('active');
            
            currentSlide = index;
        }
        
        // Go to next slide
        function nextSlide() {
            var nextIndex = (currentSlide + 1) % slideCount;
            showSlide(nextIndex);
        }
        
        // Go to previous slide
        function prevSlide() {
            var prevIndex = (currentSlide - 1 + slideCount) % slideCount;
            showSlide(prevIndex);
        }
        
        // Start autoplay
        function startAutoplay() {
            slideInterval = setInterval(function() {
                nextSlide();
            }, 5000);
        }
        
        // Reset interval
        function resetInterval() {
            clearInterval(slideInterval);
            startAutoplay();
        }
        
        // Initialize the slider
        initSlider();
        
        // Pause autoplay on hover
        $slider.hover(
            function() {
                clearInterval(slideInterval);
            },
            function() {
                startAutoplay();
            }
        );
    }
    
    /**
     * Testimonial Carousel
     */
    function initTestimonialCarousel() {
        if ($('.testimonial-carousel').length === 0) {
            return;
        }
        
        var $carousel = $('.testimonial-carousel');
        var $slides = $carousel.find('.testimonial-slide');
        var $dots = $carousel.find('.testimonial-dot');
        var currentSlide = 0;
        var slideCount = $slides.length;
        var slideInterval;
        
        function showSlide(index) {
            $slides.removeClass('active');
            $dots.removeClass('active');
            
            $slides.eq(index).addClass('active');
            $dots.eq(index).addClass('active');
            
            currentSlide = index;
        }
        
        function nextSlide() {
            var nextIndex = (currentSlide + 1) % slideCount;
            showSlide(nextIndex);
        }
        
        // Initialize
        showSlide(0);
        
        // Click handler for dots
        $dots.on('click', function() {
            var dotIndex = $(this).index();
            showSlide(dotIndex);
            
            // Reset interval
            clearInterval(slideInterval);
            startAutoplay();
        });
        
        // Start autoplay
        function startAutoplay() {
            slideInterval = setInterval(function() {
                nextSlide();
            }, 6000);
        }
        
        startAutoplay();
        
        // Pause on hover
        $carousel.hover(
            function() {
                clearInterval(slideInterval);
            },
            function() {
                startAutoplay();
            }
        );
    }
    
    /**
     * Vehicle Image Gallery
     */
    function initVehicleGallery() {
        if ($('.vehicle-gallery').length === 0) {
            return;
        }
        
        $('.vehicle-thumbnails .thumbnail').on('click', function() {
            var imgSrc = $(this).find('img').attr('src');
            var fullSrc = imgSrc.replace('-150x150', '');
            
            $('.vehicle-thumbnails .thumbnail').removeClass('active');
            $(this).addClass('active');
            
            $('.vehicle-main-image img').attr('src', fullSrc);
        });
    }
    
    /**
     * Financing Calculator
     */
    function initFinancingCalculator() {
        if ($('.financing-calculator').length === 0) {
            return;
        }
        
        // Get default values
        var vehiclePrice = parseFloat($('#vehicle_price').val()) || 0;
        var defaultDownPayment = parseFloat($('#default_down_payment').val()) || 0;
        var defaultTerm = $('#default_term').val() || '36';
        var defaultRate = parseFloat($('#default_rate').val()) || 5.0;
        
        // Set default values
        $('#down_payment').val(defaultDownPayment);
        $('#loan_term').val(defaultTerm);
        $('#interest_rate').val(defaultRate);
        
        // Calculate default payment
        calculatePayment();
        
        // Attach event handler to calculate button
        $('#calculate_payment').on('click', function(e) {
            e.preventDefault();
            calculatePayment();
        });
        
        // Function to calculate payment
        function calculatePayment() {
            var downPayment = parseFloat($('#down_payment').val()) || 0;
            var loanTerm = parseInt($('#loan_term').val()) || 36;
            var interestRate = parseFloat($('#interest_rate').val()) || 5.0;
            
            // Calculate loan amount
            var loanAmount = vehiclePrice - downPayment;
            
            // Calculate monthly payment
            // Formula: M = P * (r * (1 + r)^n) / ((1 + r)^n - 1)
            var monthlyPayment = 0;
            if (loanAmount > 0) {
                var monthlyRate = interestRate / 12 / 100;
                var termPower = Math.pow(1 + monthlyRate, loanTerm);
                monthlyPayment = loanAmount * (monthlyRate * termPower) / (termPower - 1);
            }
            
            // Calculate total interest
            var totalInterest = (monthlyPayment * loanTerm) - loanAmount;
            
            // Calculate total cost
            var totalCost = vehiclePrice + totalInterest;
            
            // Format as currency
            var formatCurrency = function(num) {
                return 'GYD ' + num.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            };
            
            // Update results
            $('#monthly_payment').text(formatCurrency(monthlyPayment));
            $('#loan_amount').text(formatCurrency(loanAmount));
            $('#total_interest').text(formatCurrency(totalInterest));
            $('#total_cost').text(formatCurrency(totalCost));
        }
    }
    
    /**
     * Vehicle Search Form
     */
    function initVehicleSearch() {
        if ($('.vehicle-filter-form').length === 0) {
            return;
        }
        
        // Reset filter form
        $('.reset-button').on('click', function(e) {
            e.preventDefault();
            var $form = $(this).closest('form');
            $form.find('input:not([type=hidden])').val('');
            $form.find('select').prop('selectedIndex', 0);
            $form.submit();
        });
        
        // Make dropdown (dynamic loading of models based on make)
        $('#vehicle_make').on('change', function() {
            var make = $(this).val();
            var $modelSelect = $('#vehicle_model');
            
            // Clear current options
            $modelSelect.html('<option value="">' + localized_strings.select_model + '</option>');
            
            if (make) {
                // Show loading
                $modelSelect.prop('disabled', true).after('<span class="loading-indicator">...</span>');
                
                // AJAX request to get models
                $.ajax({
                    url: ajax_object.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'get_vehicle_models',
                        make: make,
                        security: ajax_object.security
                    },
                    success: function(response) {
                        if (response.success && response.data) {
                            // Add new options
                            $.each(response.data, function(i, model) {
                                $modelSelect.append('<option value="' + model.slug + '">' + model.name + '</option>');
                            });
                        }
                        
                        // Remove loading
                        $modelSelect.prop('disabled', false);
                        $('.loading-indicator').remove();
                    },
                    error: function() {
                        // Remove loading on error
                        $modelSelect.prop('disabled', false);
                        $('.loading-indicator').remove();
                    }
                });
            }
        });
    }
    
    /**
     * Smooth Scroll
     */
    function initSmoothScroll() {
        $('a[href^="#"]').not('[href="#"]').on('click', function(e) {
            var target = $(this.hash);
            
            if (target.length) {
                e.preventDefault();
                
                $('html, body').animate({
                    scrollTop: target.offset().top - 100
                }, 500);
            }
        });
    }
    
    /**
     * Back to Top Button
     */
    function initBackToTop() {
        var $backToTop = $('#back-to-top');
        
        if ($backToTop.length === 0) {
            return;
        }
        
        $(window).scroll(function() {
            if ($(this).scrollTop() > 300) {
                $backToTop.addClass('visible');
            } else {
                $backToTop.removeClass('visible');
            }
        });
        
        $backToTop.on('click', function(e) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: 0
            }, 500);
        });
    }
    
    /**
     * WooCommerce Cart Update
     */
    function initCartUpdates() {
        $(document.body).on('added_to_cart', function() {
            // Update cart count in header
            $.ajax({
                url: ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_cart_count',
                    security: ajax_object.security
                },
                success: function(response) {
                    if (response.success && response.data) {
                        $('.cart-count').text(response.data);
                    }
                }
            });
        });
    }
    
    /**
     * Add sticky header on scroll
     */
    function initStickyHeader() {
        var $header = $('.site-header');
        var headerOffset = $header.offset().top;
        var headerHeight = $header.outerHeight();
        
        $(window).scroll(function() {
            if ($(this).scrollTop() > headerOffset + headerHeight) {
                $header.addClass('sticky');
                $('body').css('padding-top', headerHeight + 'px');
            } else {
                $header.removeClass('sticky');
                $('body').css('padding-top', '0');
            }
        });
    }
    
    /**
     * Form Validation
     */
    function initFormValidation() {
        // Financing application form
        if ($('.application-form').length > 0) {
            $('.application-form').on('submit', function(e) {
                var valid = true;
                
                // Check required fields
                $(this).find('[required]').each(function() {
                    if ($(this).val() === '') {
                        $(this).addClass('error');
                        valid = false;
                    } else {
                        $(this).removeClass('error');
                    }
                });
                
                if (!valid) {
                    e.preventDefault();
                    $('<div class="form-error-message">' + localized_strings.required_fields + '</div>').insertBefore($(this).find('.form-actions'));
                    
                    // Scroll to first error
                    $('html, body').animate({
                        scrollTop: $(this).find('.error').first().offset().top - 150
                    }, 500);
                }
            });
            
            // Remove error class on input
            $('.application-form').on('input', '[required]', function() {
                if ($(this).val() !== '') {
                    $(this).removeClass('error');
                }
            });
        }
    }
    
    /**
     * Newsletter modal
     */
    function initNewsletterModal() {
        var $modal = $('.newsletter-modal');
        
        if ($modal.length === 0) {
            return;
        }
        
        // Check if user has already seen the modal
        if (getCookie('newsletter_modal_shown') !== 'true') {
            // Show modal after 10 seconds
            setTimeout(function() {
                $modal.fadeIn();
            }, 10000);
            
            // Set cookie
            setCookie('newsletter_modal_shown', 'true', 7);
        }
        
        // Close modal
        $('.modal-close, .modal-overlay').on('click', function() {
            $modal.fadeOut();
        });
        
        // Prevent closing when clicking inside modal content
        $('.modal-content').on('click', function(e) {
            e.stopPropagation();
        });
    }
    
    /**
     * Cookie helpers
     */
    function setCookie(name, value, days) {
        var expires = '';
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = '; expires=' + date.toUTCString();
        }
        document.cookie = name + '=' + value + expires + '; path=/';
    }
    
    function getCookie(name) {
        var nameEQ = name + '=';
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) === ' ') {
                c = c.substring(1, c.length);
            }
            if (c.indexOf(nameEQ) === 0) {
                return c.substring(nameEQ.length, c.length);
            }
        }
        return null;
    }
    
    // Initialize all components
    initHomeSlider();
    initTestimonialCarousel();
    initVehicleGallery();
    initFinancingCalculator();
    initVehicleSearch();
    initSmoothScroll();
    initBackToTop();
    initCartUpdates();
    initStickyHeader();
    initFormValidation();
    initNewsletterModal();
});