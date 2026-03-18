document.addEventListener('DOMContentLoaded', () => {
    // ======================================
    // HELPER: GET CSRF TOKEN FROM META TAG
    // ======================================
    const getCsrfToken = () => {
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        return csrfMeta ? csrfMeta.getAttribute('content') : '';
    };

    // ======================================
    // HELPER: DISPLAY TOAST NOTIFICATION
    // ======================================
    const showToast = (message, type = 'success') => {
        const toast = document.createElement('div');
        toast.className = 'toast-notification';
        
        // Determine colors and icon based on type
        const config = {
            success: {
                background: 'linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%)',
                icon: '✓',
                shadow: 'rgba(59, 130, 246, 0.3)'
            },
            error: {
                background: 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)',
                icon: '✕',
                shadow: 'rgba(239, 68, 68, 0.3)'
            },
            warning: {
                background: 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)',
                icon: '!',
                shadow: 'rgba(245, 158, 11, 0.3)'
            }
        };

        const current = config[type] || config.success;
        
        // Create icon
        const icon = document.createElement('span');
        icon.style.cssText = `
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            flex-shrink: 0;
            font-weight: bold;
            font-size: 14px;
        `;
        icon.innerText = current.icon;
        
        // Create text
        const textContent = document.createElement('span');
        textContent.innerText = message;
        
        toast.appendChild(icon);
        toast.appendChild(textContent);

        Object.assign(toast.style, {
            position: 'fixed',
            top: '20px',
            right: '-350px',
            background: current.background,
            color: 'white',
            padding: '16px 24px',
            borderRadius: '8px',
            boxShadow: `0 10px 25px ${current.shadow}`,
            fontWeight: '500',
            fontSize: '1rem',
            fontFamily: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif",
            zIndex: '9999',
            opacity: '1',
            transition: 'right 0.4s ease-in-out, opacity 0.4s ease-in-out',
            border: '1px solid rgba(255, 255, 255, 0.2)',
            minWidth: '300px',
            maxWidth: '400px',
            wordWrap: 'break-word',
            display: 'flex',
            alignItems: 'center',
            gap: '12px'
        });

        document.body.appendChild(toast);

        // Slide in
        setTimeout(() => {
            toast.style.right = '20px';
        }, 50);

        // Fade out and remove
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => {
                toast.remove();
            }, 400);
        }, 4000);
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
                showToast('Security token missing. Please refresh the page.', 'error');
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
                    showToast(data.message, 'success');

                    // Update the cart count in the header (if you have a cart count element)
                    const cartCountElement = document.getElementById('cart-count');
                    if (cartCountElement) {
                        cartCountElement.innerText = data.cart_count; // Update with new cart count from server
                    }
                } else {
                    // The PHP Controller rejected the request
                    showToast(data.message, 'warning');
                }
            })
            .catch(error => {
                // Handle any network errors or unexpected issues
                this.innerText = originalText;
                this.disabled = false;
                showToast('An error occurred while adding the product to the cart. Please try again.', 'error');
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
                showToast('Security token missing. Please refresh the page.', 'error');
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
                    showToast(data.message, 'error');
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
                showToast('Security token missing. Please refresh the page.', 'error');
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
                    showToast(data.message, 'success');
                    setTimeout(() => window.location.reload(), 500);
                } else {
                    showToast(data.message, 'error');
                    setTimeout(() => window.location.reload(), 500); // Reload to ensure cart display is accurate
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
                showToast('Security token missing. Please refresh the page.', 'error');
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
                    showToast(data.message, 'success');
                    setTimeout(() => window.location.reload(), 500); // Reload the page to reflect the cleared cart
                } else {
                    showToast(data.message, 'error');
                }
            });
        });
    }

    // ==========================================
    // TOAST NOTIFICATIONS (Success Messages)
    // ==========================================
    const flashData = document.getElementById('js-flash-message');

    if (flashData) {
        const message = flashData.getAttribute('data-message');

        const type = flashData.getAttribute('data-type') || 'success'; // Default to 'success' if no type is provided

        if (message && message.trim() !== '') {
            showToast(message, type);
        }
    }

    // ==========================================
    // GLOBAL CATEGORY FILTERING
    // ==========================================
    const categoryFilter = document.getElementById('categoryFilter');

    // ONLY run this script if a filter actually exists on the current page
    if (categoryFilter) {
        
        const productCards = document.querySelectorAll('.product-card');
        const productRows = document.querySelectorAll('.product-row');
        const emptyState = document.getElementById('js-empty-state');
        const clearFilterBtn = document.getElementById('clearFilterBtn');

        // Listen for dropdown changes
        categoryFilter.addEventListener('change', (event) => {
            const filterValue = event.target.value;
            let visibleCount = 0;

            // 1. Are we on the Catalog Page? (Filter Cards)
            if (productCards.length > 0) {
                productCards.forEach(card => {
                    if (filterValue === 'all' || card.getAttribute('data-category') === filterValue) {
                        card.style.display = 'flex';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                if (emptyState) {
                    emptyState.style.display = (visibleCount === 0) ? 'block' : 'none';
                }
            }

            // 2. Are we on the Admin Page? (Filter Table Rows)
            if (productRows.length > 0) {
                productRows.forEach(row => {
                    if (filterValue === 'all' || row.getAttribute('data-category') === filterValue) {
                        row.style.display = ''; // Reverts to default table-row
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                if (emptyState) {
                    emptyState.style.display = (visibleCount === 0) ? '' : 'none';
                }
            }
        });

        // 3. Let the "View All Items" button reset the dropdown
        if (clearFilterBtn) {
            clearFilterBtn.addEventListener('click', () => {
                categoryFilter.value = 'all'; // Change the dropdown back to "All"
                categoryFilter.dispatchEvent(new Event('change')); // Force the script to run the filter again
            });
        }
    }

    // ==========================================
    // USER ROLE FILTERING (Admin)
    // ==========================================
    const userRoleFilter = document.getElementById('userRoleFilter');
    
    // ONLY run this script if the User Filter actually exists on the page
    if (userRoleFilter) {
        const userRows = document.querySelectorAll('.user-row');
        const userEmptyState = document.getElementById('js-user-empty-state');

        userRoleFilter.addEventListener('change', (event) => {
            // Convert to lowercase to ensure it matches the data-roles attribute perfectly
            const filterValue = event.target.value.toLowerCase();
            let visibleCount = 0;

            userRows.forEach(row => {
                const userRoles = row.getAttribute('data-roles') || '';
                
                // If "all" is selected OR if the row's roles string INCLUDES the selected role
                if (filterValue === 'all' || userRoles.includes(filterValue)) {
                    row.style.display = ''; // Reverts to default table-row
                    visibleCount++;
                } else {
                    row.style.display = 'none'; // Hides the row
                }
            });

            // Toggle the empty state row based on the visible count
            if (userEmptyState) {
                userEmptyState.style.display = (visibleCount === 0 && userRows.length > 0) ? '' : 'none';
            }
        });
    }

    // ==========================================
    // USER ROLE FILTERING (Admin)
    // ==========================================
    const orderStatusFilter = document.getElementById('statusFilter');
    
    // ONLY run this script if the User Filter actually exists on the page
    if (orderStatusFilter) {
        const orderRows = document.querySelectorAll('.order-row');
        const orderEmptyState = document.getElementById('js-order-empty-state');

        orderStatusFilter.addEventListener('change', (event) => {
            // Convert to lowercase to ensure it matches the data-status attribute perfectly
            const filterValue = event.target.value.toLowerCase();
            let visibleCount = 0;

            orderRows.forEach(row => {
                const orderStatus = row.getAttribute('data-status') || '';
                
                // If "all" is selected OR if the row's status string INCLUDES the selected status
                if (filterValue === 'all' || orderStatus.includes(filterValue)) {
                    row.style.display = ''; // Reverts to default table-row
                    visibleCount++;
                } else {
                    row.style.display = 'none'; // Hides the row
                }
            });

            // Toggle the empty state row based on the visible count
            if (orderEmptyState) {
                orderEmptyState.style.display = (visibleCount === 0 && orderRows.length > 0) ? '' : 'none';
            }
        });
    }

    // ==========================================
    // ADMIN LIVE SEARCH (Users & Products Tables)
    // ==========================================
    const adminSearch = document.getElementById('adminLiveSearch');

    if (adminSearch) {
        adminSearch.addEventListener('input', (event) => {
            const searchTerm = event.target.value.toLowerCase();
            
            // Grab both types of rows and empty states
            const productRows = document.querySelectorAll('.product-row');
            const userRows = document.querySelectorAll('.user-row');
            const orderRows = document.querySelectorAll('.order-row');
            const productEmptyState = document.getElementById('js-empty-state');
            const userEmptyState = document.getElementById('js-user-empty-state');
            const orderEmptyState = document.getElementById('js-order-empty-state');

            // 1. Filter Products Table (if it exists on the page)
            if (productRows.length > 0) {
                let visibleCount = 0;
                productRows.forEach(row => {
                    // .textContent grabs ALL the text in the row (Name, ID, Description, Price)
                    const rowText = row.textContent.toLowerCase();
                    if (rowText.includes(searchTerm)) {
                        row.style.display = ''; // Show row
                        visibleCount++;
                    } else {
                        row.style.display = 'none'; // Hide row
                    }
                });
                
                // Show empty state if nothing matches
                if (productEmptyState) {
                    productEmptyState.style.display = (visibleCount === 0) ? '' : 'none';
                }
            }

            // 2. Filter Users Table (if it exists on the page)
            if (userRows.length > 0) {
                let visibleCount = 0;
                userRows.forEach(row => {
                    // .textContent grabs ALL the text (Username, Email, ID, Roles)
                    const rowText = row.textContent.toLowerCase();
                    if (rowText.includes(searchTerm)) {
                        row.style.display = ''; // Show row
                        visibleCount++;
                    } else {
                        row.style.display = 'none'; // Hide row
                    }
                });
                
                // Show empty state if nothing matches
                if (userEmptyState) {
                    userEmptyState.style.display = (visibleCount === 0) ? '' : 'none';
                }
            }

            // 3. Filter Orders Table (if it exists on the page)
            if (orderRows.length > 0) {
                let visibleCount = 0;
                orderRows.forEach(row => {
                    // .textContent grabs ALL the text (Order ID, Buyer, Total Amount, Date Placed, Current Status)
                    const rowText = row.textContent.toLowerCase();
                    if (rowText.includes(searchTerm)) {
                        row.style.display = ''; // Show row
                        visibleCount++;
                    } else {
                        row.style.display = 'none'; // Hide row
                    }
                });

                // Show empty state if nothing matches
                if (orderEmptyState) {
                    orderEmptyState.style.display = (visibleCount === 0) ? '' : 'none';
                }
            }
        });
    }
});