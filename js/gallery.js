document.addEventListener("DOMContentLoaded", function() {
    console.log("[Gallery] Enhanced script loaded");
    
    // Main Gallery Filter (Videos/Images toggle)
    const mainFilter = document.getElementById("gallery-main-filter");
    const videosSection = document.getElementById("gallery-videos");
    const imagesSection = document.getElementById("gallery-images");
    
    // Debug: Log what we found
    console.log("Main filter found:", !!mainFilter);
    console.log("Videos section found:", !!videosSection);
    console.log("Images section found:", !!imagesSection);
    
    if (mainFilter && videosSection && imagesSection) {
        // Set default view to show videos only on page load
        videosSection.style.display = "block";
        imagesSection.style.display = "none";
        
        mainFilter.addEventListener("change", function() {
            const selectedValue = this.value;
            
            requestAnimationFrame(() => {
                if (selectedValue === "videos") {
                    videosSection.style.display = "block";
                    imagesSection.style.display = "none";
                } else if (selectedValue === "images") {
                    videosSection.style.display = "none";
                    imagesSection.style.display = "block";
                }
            });
        }, { passive: true });
    }
    
    // Video Category Filters
    const videoFilterButtons = document.querySelectorAll('.gallery-videos .filter-button');
    const videoCards = document.querySelectorAll('.video-card');
    
    videoFilterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            videoFilterButtons.forEach(btn => btn.classList.remove('active'));
            // Add active class to current button
            this.classList.add('active');
            
            const filter = this.getAttribute('data-filter');
            
            // Filter the video cards
            videoCards.forEach(card => {
                if (filter === 'all' || card.getAttribute('data-category') === filter) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }, { passive: true });
    });
    
    // Image Category Filters
    const imageFilterButtons = document.querySelectorAll('.gallery-images .filter-button');
    const galleryImages = document.querySelectorAll('.gallery-grid img');
    
    imageFilterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            imageFilterButtons.forEach(btn => btn.classList.remove('active'));
            // Add active class to current button
            this.classList.add('active');
            
            const filter = this.getAttribute('data-filter');
            console.log("Image filter selected:", filter);
            
            // Filter the images
            galleryImages.forEach(image => {
                const imageCategory = image.getAttribute('data-category');
                console.log("Checking image:", image.alt, "Category:", imageCategory);
                
                if (filter === 'all' || imageCategory === filter) {
                    image.style.display = 'block';
                    // Also ensure parent element is visible if needed
                    let parent = image.parentElement;
                    if (parent && parent.tagName.toLowerCase() === 'div') {
                        parent.style.display = 'block';
                    }
                } else {
                    image.style.display = 'none';
                    // Also hide parent element if needed
                    let parent = image.parentElement;
                    if (parent && parent.tagName.toLowerCase() === 'div') {
                        parent.style.display = 'none';
                    }
                }
            });
        });
    });
    
    // Video Modal Functionality
    const videoModal = document.getElementById('video-modal');
    const modalVideo = document.getElementById('modal-video');
    const modalClose = document.querySelector('.modal-close');
    const videoThumbnails = document.querySelectorAll('.video-thumbnail');
    
    videoThumbnails.forEach(thumbnail => {
        thumbnail.addEventListener('click', function() {
            const videoSrc = this.getAttribute('data-video');
            const videoSource = modalVideo.querySelector('source');
            videoSource.src = videoSrc;
            modalVideo.load();
            videoModal.style.display = 'flex';
            modalVideo.play().catch(err => {
                console.log("Autoplay prevented by browser. Click to play.");
            });
        });
    });
    
    if (modalClose) {
        modalClose.addEventListener('click', function() {
            modalVideo.pause();
            videoModal.style.display = 'none';
        });
    }
    
    if (videoModal) {
        videoModal.addEventListener('click', function(event) {
            if (event.target === videoModal) {
                modalVideo.pause();
                videoModal.style.display = 'none';
            }
        });
    }
    
    // Image Preview Modal Functionality
    // First, create or find the image preview modal
    let imageModal = document.getElementById('image-preview-modal');
    if (!imageModal) {
        // Create the modal if it doesn't exist
        imageModal = document.createElement('div');
        imageModal.id = 'image-preview-modal';
        imageModal.style.position = 'fixed';
        imageModal.style.top = '0';
        imageModal.style.left = '0';
        imageModal.style.width = '100%';
        imageModal.style.height = '100%';
        imageModal.style.backgroundColor = 'rgba(0,0,0,0.9)';
        imageModal.style.zIndex = '10000';
        imageModal.style.display = 'none';
        imageModal.style.alignItems = 'center';
        imageModal.style.justifyContent = 'center';
        
        // Create a container to hold the image
        const contentContainer = document.createElement('div');
        contentContainer.style.position = 'relative';
        contentContainer.style.maxWidth = '95%';
        contentContainer.style.maxHeight = '95%';
        contentContainer.style.display = 'flex';
        contentContainer.style.justifyContent = 'center';
        contentContainer.style.alignItems = 'center';
        
        // Create the modal image
        const modalImg = document.createElement('img');
        modalImg.id = 'preview-image';
        modalImg.style.maxWidth = '100%';
        modalImg.style.maxHeight = '85vh';
        modalImg.style.border = '2px solid white';
        modalImg.style.boxShadow = '0 5px 25px rgba(0,0,0,0.5)';
        modalImg.style.transition = 'opacity 0.3s ease';
        
        // Create a close button
        const closeBtn = document.createElement('span');
        closeBtn.innerHTML = '&times;';
        closeBtn.style.position = 'absolute';
        closeBtn.style.top = '-40px';
        closeBtn.style.right = '0';
        closeBtn.style.color = 'white';
        closeBtn.style.fontSize = '36px';
        closeBtn.style.fontWeight = 'bold';
        closeBtn.style.cursor = 'pointer';
        closeBtn.style.width = '40px';
        closeBtn.style.height = '40px';
        closeBtn.style.lineHeight = '36px';
        closeBtn.style.textAlign = 'center';
        closeBtn.style.backgroundColor = 'rgba(0,0,0,0.5)';
        closeBtn.style.borderRadius = '50%';
        closeBtn.style.transition = 'all 0.3s ease';
        
        // Add event listeners to the close button
        closeBtn.onmouseover = function() {
            this.style.color = '#007BFF';
            this.style.transform = 'scale(1.1)';
        };
        closeBtn.onmouseout = function() {
            this.style.color = 'white';
            this.style.transform = 'scale(1)';
        };
        
        // Add elements to the DOM
        contentContainer.appendChild(modalImg);
        contentContainer.appendChild(closeBtn);
        imageModal.appendChild(contentContainer);
        document.body.appendChild(imageModal);
        
        // Add event listeners to the modal
        closeBtn.addEventListener('click', function(event) {
            event.stopPropagation();
            imageModal.style.display = 'none';
        });
        
        imageModal.addEventListener('click', function(event) {
            if (event.target === imageModal) {
                imageModal.style.display = 'none';
            }
        });
    }
    
    // Add click event listeners to all gallery images
    const galleryImagesElements = document.querySelectorAll('.gallery-grid img');
    galleryImagesElements.forEach(image => {
        image.style.cursor = 'pointer';
        image.addEventListener('click', function(event) {
            event.preventDefault();
            const previewSrc = this.getAttribute('data-preview') || this.src;
            const previewImg = document.getElementById('preview-image');
            previewImg.src = previewSrc;
            previewImg.alt = this.alt || 'Gallery image';
            imageModal.style.display = 'flex';
        });
    });
    
    // Close modal with ESC key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            if (imageModal) {
                imageModal.style.display = 'none';
            }
            
            if (videoModal) {
                modalVideo.pause();
                videoModal.style.display = 'none';
            }
        }
    });
    
    // Handle thumbnail image errors
    const thumbnailImages = document.querySelectorAll('.video-thumbnail img');
    thumbnailImages.forEach(img => {
        img.addEventListener('error', function() {
            this.src = 'data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22288%22%20height%3D%22162%22%20viewBox%3D%220%200%20288%20162%22%3E%3Crect%20fill%3D%22%23465362%22%20width%3D%22288%22%20height%3D%22162%22%2F%3E%3Ctext%20fill%3D%22%23FFFFFF%22%20font-family%3D%22sans-serif%22%20font-size%3D%2214%22%20x%3D%22144%22%20y%3D%2281%22%20text-anchor%3D%22middle%22%3EVideo%20Thumbnail%3C%2Ftext%3E%3C%2Fsvg%3E';
            this.alt = 'Video Thumbnail';
        });
    });
    
    console.log("[Gallery] Enhanced setup complete with image preview");
});