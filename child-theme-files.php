/* 
 * style.css - Child Theme Definition
 * Place this in a folder named 'cardealership-child' in wp-content/themes/
 */

/*
Theme Name: Car Dealership Child Theme
Theme URI: https://yourwebsite.com/
Description: A child theme for creating a car dealership website
Author: Your Name
Author URI: https://yourwebsite.com/
Template: astra (or your parent theme name)
Version: 1.0.0
Text Domain: cardealership-child
*/

/* Import parent theme styles */
@import url("../astra/style.css");

/* Custom CSS for dealership */
.vehicle-featured {
    border: 1px solid #e0e0e0;
    border-radius: 5px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 30px;
    transition: all 0.3s ease;
}

.vehicle-featured:hover {
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    transform: translateY(-5px);
}

.vehicle-price {
    font-size: 24px;
    font-weight: bold;
    color: #d9534f;
}

.financing-calculator {
    background: #f8f9fa;
    padding: 25px;
    border-radius: 8px;
    margin-bottom: 30px;
}

.part-compatibility {
    background: #e8f4f8;
    padding: 15px;
    border-radius: 5px;
    margin-top: 15px;
}

/* Mobile Responsive Adjustments */
@media (max-width: 768px) {
    .vehicle-gallery {
        height: 200px;
    }
    
    .vehicle-specs {
        font-size: 14px;
    }
}
