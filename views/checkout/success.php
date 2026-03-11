<div class="cart-section" style="display: flex; justify-content: center; align-items: center; min-height: 60vh;">
    
    <div style="background: white; padding: 50px 40px; border-radius: 12px; box-shadow: 0 8px 20px rgba(0,0,0,0.08); text-align: center; max-width: 550px; width: 100%; border: 1px solid #e2e8f0;">
        
        <div style="width: 80px; height: 80px; background-color: #e6fffa; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px auto;">
            <svg style="width: 45px; height: 45px; color: #38a169;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>

        <h1 style="color: #2d3748; margin-bottom: 10px; font-size: 2.2rem;">Order Successful!</h1>
        <p style="color: #718096; font-size: 1.1rem; margin-bottom: 30px;">Thank you for supporting local sellers.</p>
        
        <div style="background-color: #f7fafc; border: 2px dashed #cbd5e0; padding: 20px; border-radius: 8px; margin-bottom: 35px;">
            <p style="margin: 0; color: #4a5568; font-size: 0.95rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">Order Reference</p>
            <p style="margin: 5px 0 0 0; font-size: 1.8rem; font-weight: bold; color: #3182ce;">
                #<?php echo htmlspecialchars($order_id); ?>
            </p>
        </div>

        <p style="color: #4a5568; margin-bottom: 35px; line-height: 1.6;">
            The seller has been notified and will begin preparing your items. 
        </p>

        <a href="/UnityExchange/product" class="btn-primary" style="display: inline-block; padding: 14px 35px; font-size: 1.1rem;">
            Continue Shopping
        </a>
    </div>

</div>