document.addEventListener('DOMContentLoaded', () => {
    // 1. Target the Add to Cart button on the product details page
    const addToCartButton = document.getElementById('add-to-cart-button');

    if (addToCartButton) {
        addToCartButton.addEventListener('click', function() {
            // Grab the product ID from the button's data attribute
            const productId = this.getAttribute('data-id');

            // Grab the CSRF token from the meta tag we added to the header
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

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
});