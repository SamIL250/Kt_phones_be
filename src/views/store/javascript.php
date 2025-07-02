<script>
// Global modal functions
function showAuthModal() {
    const modal = document.getElementById('loginModal');
    const modalContent = document.getElementById('modalContent');
    if (!modal || !modalContent) {
        alert('Login modal not found in DOM!');
        return;
    }
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    setTimeout(() => {
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');
    }, 10);
}
function closeLoginModal() {
    const modal = document.getElementById('loginModal');
    const modalContent = document.getElementById('modalContent');
    if (!modal || !modalContent) return;
    modalContent.classList.remove('scale-100', 'opacity-100');
    modalContent.classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }, 300);
}

// Change main image
function changeMainImage(imageUrl, thumbnail) {
    document.getElementById('mainImage').src = imageUrl;
    document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
    thumbnail.classList.add('active');
}

// Quantity controls
function changeQuantity(delta) {
    const input = document.getElementById('quantity');
    const newValue = Math.max(1, Math.min(<?= $product['stock_quantity'] ?>, parseInt(input.value) + delta));
    input.value = newValue;
}

// Add to cart
async function addToCart(productId) {
    const quantity = document.getElementById('quantity').value;
    
    try {
        const response = await fetch('./src/services/cart/cart_handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'add',
                product_id: productId,
                quantity: parseInt(quantity)
            })
        });

        const data = await response.json();
        
        if (data.success) {
            // Show success message
            showToast(data.message, 'success');
            // Update cart count
            updateCartCount();
        } else {
            showToast(data.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showToast('An error occurred while adding to cart', 'error');
    }
}

// Add to wishlist
async function addToWishlist(productId, userId) {
    <?php if (isset($is_logged_in) && $is_logged_in): ?>
        try {
            const response = await fetch(`./src/services/wishlist/new_wishlist.php?product=${productId}&user=${userId}`);
            const data = await response.json();
            
            if (data.success) {
                showToast(data.message, 'success');
                // Update wishlist button
                const wishlistBtn = document.querySelector('.wishlist-btn');
                const icon = wishlistBtn.querySelector('i');
                wishlistBtn.classList.add('active');
                icon.classList.remove('bi-heart');
                icon.classList.add('bi-heart-fill');
            } else {
                showToast(data.message, 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('An error occurred while adding to wishlist', 'error');
        }
    <?php else: ?>
        showToast('Please sign in to add items to your wishlist', 'warning');
    <?php endif; ?>
}

// Toast notification
function showToast(message, type = 'info') {
    const colors = {
        success: 'linear-gradient(to right, #10b981, #059669)',
        error: 'linear-gradient(to right, #ef4444, #dc2626)',
        warning: 'linear-gradient(to right, #f59e0b, #d97706)',
        info: 'linear-gradient(to right, #3b82f6, #2563eb)'
    };

            Toastify({
        text: message,
        duration: 3000,
        gravity: "bottom",
        position: "right",
        backgroundColor: colors[type],
                stopOnFocus: true,
                style: {
                    borderRadius: "8px",
                    padding: "12px 16px",
                    color: "#fff",
                    fontSize: "14px",
                    fontWeight: "500",
                    boxShadow: "0px 4px 10px rgba(0, 0, 0, 0.15)",
                }
            }).showToast();
}

// Update cart count
async function updateCartCount() {
    try {
        const response = await fetch('./src/services/cart/cart_count.php');
        const data = await response.json();
        
        const cartCountElements = document.querySelectorAll('#cart-count, #mobile-cart-count');
        cartCountElements.forEach(element => {
            if (element) element.textContent = data.count || 0;
        });
    } catch (error) {
        console.error('Error updating cart count:', error);
    }
}


document.addEventListener('DOMContentLoaded', function() {
        const isUserLoggedIn = <?= json_encode($is_logged_in ?? false) ?>;
        const customerId = <?= json_encode($customer_id ?? null) ?>;

        // Price Range Slider
        const priceSlider = document.getElementById('price-slider');
        if (priceSlider) {
            noUiSlider.create(priceSlider, {
                start: [
                    document.getElementById('price-min-hidden').value,
                    document.getElementById('price-max-hidden').value
                ],
                connect: true,
                range: {
                    'min': <?= $global_min_price ?>,
                    'max': <?= $global_max_price ?>
                },
                format: wNumb({
                    decimals: 0
                }),
                step: 1000,
            });

            const priceMinLabel = document.getElementById('price-min');
            const priceMaxLabel = document.getElementById('price-max');
            const priceMinHidden = document.getElementById('price-min-hidden');
            const priceMaxHidden = document.getElementById('price-max-hidden');

            priceSlider.noUiSlider.on('update', function(values, handle) {
                if (handle === 0) {
                    priceMinLabel.innerHTML = 'Frw ' + values[0];
                    priceMinHidden.value = values[0];
                } else {
                    priceMaxLabel.innerHTML = 'Frw ' + values[1];
                    priceMaxHidden.value = values[1];
                }
            });
        }

        function updateURLAndReload() {
            const baseUrl = window.location.pathname;
            const params = new URLSearchParams(window.location.search);

            const search = document.getElementById('search-input').value;
            if (search) {
                params.set('search', search);
            } else {
                params.delete('search');
            }

            const category = document.querySelector('.filter-category.active')?.dataset.slug;
            if (category) {
                params.set('category', category);
            } else {
                params.delete('category');
            }


            const sortBy = document.getElementById('sort-by').value;
            if (sortBy !== 'newest') {
                params.set('sort', sortBy);
            } else {
                params.delete('sort');
            }


            const priceMin = document.getElementById('price-min-hidden').value;
            const priceMax = document.getElementById('price-max-hidden').value;

            if (priceMin != <?= $global_min_price ?>) {
                params.set('price_min', priceMin);
            } else {
                params.delete('price_min');
            }

            if (priceMax != <?= $global_max_price ?>) {
                params.set('price_max', priceMax);
            } else {
                params.delete('price_max');
            }


            params.delete('page');

            window.location.href = baseUrl + '?' + params.toString();
        }

        // Event Listeners for filters
        document.getElementById('apply-filters').addEventListener('click', updateURLAndReload);
        document.getElementById('search-input').addEventListener('keyup', function(event) {
            if (event.key === 'Enter') {
                updateURLAndReload();
            }
        });


        // Category selection
        const currentCategory = '<?= $category_filter ?>';
        document.querySelectorAll('.filter-category').forEach(cat => {
            if (cat.dataset.slug === currentCategory) {
                cat.classList.add('active', 'bg-blue-50', 'text-blue-600', 'font-bold');
            }
            cat.addEventListener('click', function(e) {
                e.preventDefault();
                const currentActive = document.querySelector('.filter-category.active');
                if (currentActive) {
                    currentActive.classList.remove('active', 'bg-blue-50', 'text-blue-600', 'font-bold');
                }
                this.classList.add('active', 'bg-blue-50', 'text-blue-600', 'font-bold');
                // No immediate reload, wait for 'Apply Filters' button
            });
        });

        // Pagination
        document.querySelectorAll('.page-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const page = this.dataset.page;
                const url = new URL(window.location);
                url.searchParams.set('page', page);
                window.location.href = url.toString();
            });
        });

        // Card Clicks
        document.querySelectorAll('.product-card .add-to-cart-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const card = btn.closest('.product-card');
                const productId = card.dataset.productId;
                addToCart(productId, 1, customerId);
            });
        });

        document.querySelectorAll('.product-card .add-to-wishlist-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                if (!isUserLoggedIn) {
                    showAuthModal();
                    return;
                }
                const card = btn.closest('.product-card');
                const productId = card.dataset.productId;
                addToWishList(productId, customerId);
            });
        });

        // Handle "You might also like" cart buttons
        document.querySelectorAll('.mt-16 .add-to-cart-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const card = btn.closest('.product-card');
                const productId = card.dataset.productId;
                addToCart(productId, 1, customerId);
            });
        });
    });
</script>