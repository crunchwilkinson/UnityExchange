document.addEventListener('DOMContentLoaded', () => {
    // ======================================
    // HELPER: GET CSRF TOKEN FROM META TAG
    // ======================================
    const getCsrfToken = () => {
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        return csrfMeta ? csrfMeta.getAttribute('content') : '';
    };

    // ==================================================
    // ADD TO CART BUTTON HANDLER (Product Details Page)
    // ==================================================
    const addToCartBtn = document.getElementById('add-to-cart-button');

    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            const csrfToken = getCsrfToken();
           

            if (!csrfToken) {
                alert('Security token missing. Please refresh the page.');
                return;
            }

            // 2. Prepare the data to send (matching standard HTML form formatting)
            const formData = new URLSearchParams();
            formData.append('product_id', productId);
            formData.append('quantity', 1); // Default to adding 1 item to the cart
            formData.append('csrf_token', csrfToken);

            // 3. Disable the button so the user doesn't double-click it
            const originalText = this.textContent;
            this.innerText = 'Adding...';
            this.disabled = true;

            // 4. Send the background POST request to the server
            fetch('/UnityExchange/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Accept': 'application/json' // Tell the server we want JSON back
                },
                body: formData.toString()
            })
            .then(response => response.json()) // Parse the JSON response from the server
            .then(data => {
                this.innerText = originalText; // Reset button text
                this.disabled = false; // Re-enable the button

                // 5. Handle the PHP response (which should be JSON)
                if (data.status === 'success') {
                    alert(data.message);

                    // Update the cart count in the header (if you have a cart count element)
                    const cartCountElement = document.getElementById('cart-count');
                    if (cartCountElement) {
                        cartCountElement.innerText = data.cart_count; // Update with new cart count from server
                    }
                } else {
                    // The PHP Controller rejected the request
                    alert('Notice: ' + data.message);
                }
            })
            .catch(error => {
                // Handle any network errors or unexpected issues
                this.innerText = originalText;
                this.disabled = false;
                alert('An error occured while adding the product to the cart. Please try again.');
                console.error('Error adding to cart:', error);
            });
        });
    }

    // ===========================================
    // UPDATE CART QUANTITY HANDLER (Cart Page)
    // ===========================================
    const qtyInputs = document.querySelectorAll('.cart-qty-input');

    qtyInputs.forEach(input => {
        input.addEventListener('change', function() {
            const productId = this.getAttribute('data-id');
            const newQuantity = this.value;
            const csrfToken = getCsrfToken();

            if (!csrfToken) {
                alert('Security token missing. Please refresh the page.');
                return;
            }

            const formData = new URLSearchParams();
            formData.append('product_id', productId);
            formData.append('quantity', newQuantity);
            formData.append('csrf_token', csrfToken);
            
            fetch('/UnityExchange/cart/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Accept': 'application/json'
                },
                body: formData.toString()
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Reload the page so PHP calculates the new totals and updates the cart display
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                    window.location.reload(); // Reload to reset the quantity input to the correct value
                }
            });
        });
    });

    // ===========================================
    // REMOVE SINGLE ITEM (Cart Page)
    // ===========================================
    const removeBtns = document.querySelectorAll('.remove-item-btn');

    removeBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault(); // Prevent button from submitting a form or navigating

            const productId = this.getAttribute('data-id');
            const csrfToken = getCsrfToken();

            if (!csrfToken) {
                alert('Security token missing. Please refresh the page.');
                return;
            }

            const formData = new URLSearchParams();
            formData.append('product_id', productId);
            formData.append('csrf_token', csrfToken);

            fetch('/UnityExchange/cart/remove', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Accept': 'application/json'
                },
                body: formData.toString()
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Reload the page to reflect the updated cart
                    window.location.reload();
                } else {
                    alert('Error removing item: ' + data.message);
                    window.location.reload(); // Reload to ensure cart display is accurate
                }
            });
        });
    });

    // ==========================================
    // CLEAR ENTIRE CART (Cart Page)
    // ==========================================
    const clearBtn = document.getElementById('clear-cart-btn');

    if (clearBtn) {
        clearBtn.addEventListener('click', function(e) {
            e.preventDefault();

            if (!confirm("Are you sure you want to remove all items from your cart?")) {
                return;
            }

            const csrfToken = getCsrfToken();

            if (!csrfToken) {
                alert('Security token missing. Please refresh the page.');
                return;
            }

            const formData = new URLSearchParams();
            formData.append('csrf_token', csrfToken);

            fetch('/UnityExchange/cart/clear', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Accept': 'application/json'
                },
                body: formData.toString()
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    window.location.reload(); // Reload the page to reflect the cleared cart
                } else {
                    alert('Error clearing cart: ' + data.message);
                }
            });
        });
    }
});