// Toggle payment method fields
document.addEventListener('DOMContentLoaded', function() {
    // For checkout page
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    const creditCardInfo = document.getElementById('credit_card_info');
    
    if(paymentMethods && creditCardInfo) {
        paymentMethods.forEach(method => {
            method.addEventListener('change', function() {
                if(this.value === 'credit_card' && this.checked) {
                    creditCardInfo.style.display = 'block';
                } else {
                    creditCardInfo.style.display = 'none';
                }
            });
        });
    }
    
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Cart quantity validation
    const quantityInputs = document.querySelectorAll('input[name="quantity"]');
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            if(this.value < 1) {
                this.value = 1;
            }
            if(this.max && this.value > this.max) {
                this.value = this.max;
            }
        });
    });
});