/**
 * Financing Calculator JavaScript
 * Save this file as 'financing-calculator.js' in the 'js' folder of your child theme
 */

jQuery(document).ready(function($) {
    // Initialize financing calculator
    function initFinancingCalculator() {
        if ($('.financing-calculator').length === 0) {
            return;
        }
        
        // Get default values
        const vehiclePrice = parseFloat($('#vehicle_price').val()) || 0;
        const defaultDownPayment = parseFloat($('#default_down_payment').val()) || 0;
        const defaultTerm = $('#default_term').val() || '36';
        const defaultRate = parseFloat($('#default_rate').val()) || 5.0;
        
        // Set default values
        $('#down_payment').val(defaultDownPayment);
        $('#loan_term').val(defaultTerm);
        $('#interest_rate').val(defaultRate);
        
        // Calculate default payment
        calculatePayment();
        
        // Attach event handler to calculate button
        $('#calculate_payment').on('click', function() {
            calculatePayment();
        });
        
        // Function to calculate payment
        function calculatePayment() {
            const downPayment = parseFloat($('#down_payment').val()) || 0;
            const loanTerm = parseInt($('#loan_term').val()) || 36;
            const interestRate = parseFloat($('#interest_rate').val()) || 5.0;
            
            // Calculate loan amount
            const loanAmount = vehiclePrice - downPayment;
            
            // Calculate monthly payment
            // Formula: M = P * (r * (1 + r)^n) / ((1 + r)^n - 1)
            // Where:
            // M = monthly payment
            // P = loan amount
            // r = monthly interest rate (annual rate / 12 / 100)
            // n = loan term in months
            
            let monthlyPayment = 0;
            if (loanAmount > 0) {
                const monthlyRate = interestRate / 12 / 100;
                const termPower = Math.pow(1 + monthlyRate, loanTerm);
                monthlyPayment = loanAmount * (monthlyRate * termPower) / (termPower - 1);
            }
            
            // Calculate total interest
            const totalInterest = (monthlyPayment * loanTerm) - loanAmount;
            
            // Calculate total cost
            const totalCost = vehiclePrice + totalInterest;
            
            // Format numbers as currency
            const formatCurrency = function(num) {
                return financing_vars.currency_symbol + ' ' + num.toFixed(financing_vars.decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            };
            
            // Update results
            $('#monthly_payment').text(formatCurrency(monthlyPayment));
            $('#loan_amount').text(formatCurrency(loanAmount));
            $('#total_interest').text(formatCurrency(totalInterest));
            $('#total_cost').text(formatCurrency(totalCost));
        }
    }
    
    // Initialize calculator on page load
    initFinancingCalculator();
});
