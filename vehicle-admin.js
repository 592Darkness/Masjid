/**
 * Vehicle Entry Form JavaScript
 * Save as vehicle-admin.js in your child theme's js folder
 */

jQuery(document).ready(function($) {
    'use strict';
    
    /**
     * Toggle financing options
     */
    function toggleFinancingOptions() {
        var $checkbox = $('#vehicle_financing_available');
        var $financingOptions = $('.financing-options');
        
        if ($checkbox.length === 0) {
            return;
        }
        
        function updateVisibility() {
            if ($checkbox.is(':checked')) {
                $financingOptions.show();
            } else {
                $financingOptions.hide();
            }
        }
        
        // Initial state
        updateVisibility();
        
        // Change event
        $checkbox.on('change', updateVisibility);
    }
    
    /**
     * Vehicle image gallery
     */
    function initVehicleGallery() {
        var $galleryContainer = $('.vehicle-gallery-container');
        var $gallery = $('#vehicle_gallery');
        var $images = $('#vehicle_gallery_container');
        var frame;
        
        if ($galleryContainer.length === 0) {
            return;
        }
        
        // Add images button
        $('#add_vehicle_images').on('click', function(e) {
            e.preventDefault();
            
            // If the frame already exists, open it
            if (frame) {
                frame.open();
                return;
            }
            
            // Create the media frame
            frame = wp.media({
                title: vehicle_admin_strings.select_images,
                button: {
                    text: vehicle_admin_strings.add_to_gallery
                },
                multiple: true
            });
            
            // When images are selected
            frame.on('select', function() {
                var attachments = frame.state().get('selection').toJSON();
                var galleryIds = $gallery.val() ? $gallery.val().split(',') : [];
                
                // Loop through attachments
                $.each(attachments, function(i, attachment) {
                    // Add to gallery IDs
                    if ($.inArray(attachment.id.toString(), galleryIds) === -1) {
                        galleryIds.push(attachment.id.toString());
                    }
                    
                    // Add image to preview
                    if ($('#vehicle_gallery_image_' + attachment.id).length === 0) {
                        var image = $('<div class="gallery-image" id="vehicle_gallery_image_' + attachment.id + '"></div>')
                            .append('<img src="' + attachment.sizes.thumbnail.url + '" />')
                            .append('<a href="#" class="remove-image" data-id="' + attachment.id + '">&times;</a>');
                        
                        $images.append(image);
                    }
                });
                
                // Update gallery field
                $gallery.val(galleryIds.join(','));
            });
            
            // Open the frame
            frame.open();
        });
        
        // Remove image
        $images.on('click', '.remove-image', function(e) {
            e.preventDefault();
            
            var imageId = $(this).data('id').toString();
            var galleryIds = $gallery.val() ? $gallery.val().split(',') : [];
            
            // Remove from gallery IDs
            galleryIds = $.grep(galleryIds, function(val) {
                return val !== imageId;
            });
            
            // Update gallery field
            $gallery.val(galleryIds.join(','));
            
            // Remove image from preview
            $('#vehicle_gallery_image_' + imageId).remove();
        });
        
        // Sort images
        $images.sortable({
            items: '.gallery-image',
            cursor: 'move',
            scrollSensitivity: 40,
            forcePlaceholderSize: true,
            placeholder: 'gallery-image-placeholder',
            update: function() {
                var galleryIds = [];
                
                $images.find('.gallery-image').each(function() {
                    var id = $(this).attr('id').replace('vehicle_gallery_image_', '');
                    galleryIds.push(id);
                });
                
                // Update gallery field
                $gallery.val(galleryIds.join(','));
            }
        });
    }
    
    /**
     * Vehicle price calculator
     */
    function initPriceCalculator() {
        var $calculator = $('.price-calculator');
        
        if ($calculator.length === 0) {
            return;
        }
        
        function calculatePrice() {
            var basePrice = parseFloat($('#base_price').val()) || 0;
            var markup = parseFloat($('#markup_percentage').val()) || 0;
            var shipping = parseFloat($('#shipping_cost').val()) || 0;
            var duty = parseFloat($('#duty_percentage').val()) || 0;
            var vat = parseFloat($('#vat_percentage').val()) || 0;
            
            // Calculate components
            var markupAmount = basePrice * (markup / 100);
            var dutyAmount = basePrice * (duty / 100);
            var vatAmount = (basePrice + markupAmount + dutyAmount) * (vat / 100);
            
            // Calculate final price
            var finalPrice = basePrice + markupAmount + shipping + dutyAmount + vatAmount;
            
            // Update fields
            $('#markup_amount').val(markupAmount.toFixed(2));
            $('#duty_amount').val(dutyAmount.toFixed(2));
            $('#vat_amount').val(vatAmount.toFixed(2));
            $('#final_price').val(finalPrice.toFixed(2));
            $('#vehicle_price').val(finalPrice.toFixed(0));
            
            // Update MSRP suggestion (10% above final price)
            var msrpSuggestion = finalPrice * 1.1;
            $('#msrp_suggestion').text(msrpSuggestion.toFixed(0));
        }
        
        // Calculate on input change
        $calculator.find('input').on('input', calculatePrice);
        
        // Initial calculation
        calculatePrice();
    }
    
    /**
     * Vehicle features input
     */
    function initFeaturesInput() {
        var $container = $('.features-container');
        var $input = $('#vehicle_features');
        var $featuresList = $('#features_list');
        var $addFeature = $('#add_feature');
        var $newFeature = $('#new_feature');
        
        if ($container.length === 0) {
            return;
        }
        
        // Load features
        function loadFeatures() {
            var features = $input.val().split('\n');
            
            $featuresList.empty();
            
            $.each(features, function(i, feature) {
                feature = feature.trim();
                
                if (feature !== '') {
                    addFeatureToList(feature);
                }
            });
        }
        
        // Add feature to list
        function addFeatureToList(feature) {
            var $feature = $('<div class="feature-item"></div>')
                .append('<span class="feature-text">' + feature + '</span>')
                .append('<a href="#" class="remove-feature">&times;</a>');
            
            $featuresList.append($feature);
        }
        
        // Update input
        function updateInput() {
            var features = [];
            
            $featuresList.find('.feature-text').each(function() {
                features.push($(this).text());
            });
            
            $input.val(features.join('\n'));
        }
        
        // Load initial features
        loadFeatures();
        
        // Add feature
        $addFeature.on('click', function(e) {
            e.preventDefault();
            
            var feature = $newFeature.val().trim();
            
            if (feature !== '') {
                addFeatureToList(feature);
                updateInput();
                $newFeature.val('').focus();
            }
        });
        
        // Remove feature
        $featuresList.on('click', '.remove-feature', function(e) {
            e.preventDefault();
            
            $(this).parent().remove();
            updateInput();
        });
        
        // Sort features
        $featuresList.sortable({
            items: '.feature-item',
            cursor: 'move',
            update: updateInput
        });
        
        // Add feature on enter
        $newFeature.on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                $addFeature.click();
            }
        });
    }
    
    // Initialize components
    toggleFinancingOptions();
    initVehicleGallery();
    initPriceCalculator();
    initFeaturesInput();
});
