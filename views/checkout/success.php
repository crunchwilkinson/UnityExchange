<div class="cart-section success-container-wrapper">
    
    <div class="success-card">
        
        <div class="success-icon-wrapper">
            <svg class="success-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>

        <h1 class="success-title">Order Successful!</h1>
        <p class="success-subtitle">Thank you for supporting local sellers.</p>
        
        <div class="reference-box">
            <p class="reference-label">Order Reference</p>
            <p class="reference-value">
                #<?php echo htmlspecialchars($order_id); ?>
            </p>
        </div>

        <p class="success-footer-text">
            The seller has been notified and will begin preparing your items. 
        </p>

        <a href="/UnityExchange/product" class="btn-primary btn-lg">
            Continue Shopping
        </a>
    </div>

</div>