<?php
// Use the new cart logic functions
include './src/services/cart/cart_logic.php';

$cart_items = get_cart_contents($conn, $is_logged_in, $customer_id);
$totals = calculate_totals($cart_items);

// Prepare data for the checkout link
$checkout_query_params = ['customer' => $is_logged_in ? $customer_id : 'guest'];
$checkout_query = http_build_query($checkout_query_params);
?>

<!-- Breadcrumb -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex items-center space-x-2 text-sm">
        <a href="store" class="text-blue-500 hover:text-blue-600">Store</a>
        <span>›</span>
        <span class="text-gray-500">My Cart</span>
    </div>
</div>

<!-- Main Content -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-[100px]">
    <h1 class="observe-card text-3xl font-bold text-gray-600 mb-8">Cart (<span id="cart-item-count"><?= $totals['item_count'] ?></span>)</h1>

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Cart Items -->
        <div class="lg:w-2/3 observe-card">
            <div class="bg-white overflow-hidden">
                <div class="py-10" id="cart-table-container">
                    <div class="table-responsive scrollbar">
                        <table class="table fs-9 mb-0 w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="py-3 px-2 text-center" style="width: 60px;"></th>
                                    <th class="py-3 px-4 text-left" style="min-width:250px;">PRODUCT</th>
                                    <th class="py-3 px-4 text-center">QUANTITY</th>
                                    <th class="py-3 px-4 text-right">PRICE</th>
                                    <th class="py-3 px-2 text-center"></th>
                                </tr>
                            </thead>
                            <tbody class="list" id="cart-table-body">
                                <?php if (empty($cart_items)) : ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-16">
                                            <p class="text-lg text-gray-500">Your cart is empty.</p>
                                            <a href="store" class="mt-4 inline-block bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition-colors">Continue Shopping</a>
                                        </td>
                                    </tr>
                                <?php else : ?>
                                    <?php foreach ($cart_items as $item) :
                                        $price = isset($item['unit_price']) ? $item['unit_price'] : 
                                                ($item['discount_price'] > 0 ? $item['discount_price'] : $item['base_price']);
                                    ?>
                                        <tr class="cart-item-row" data-product-id="<?= $item['product_id'] ?>" data-variant-id="<?= $item['variant_id'] ?? '' ?>">
                                            <td class="py-4 px-2 text-center">
                                                <img class="border border-gray-200 rounded-md" src="<?= $item['image_urls'] ?>" alt="<?= $item['name'] ?>" width="53" />
                                            </td>
                                            <td class="py-4 px-4 products text-left">
                                                <a class="fw-semibold mb-0 line-clamp-1 hover:text-blue-600" href="product-view?product=<?= $item['product_id'] ?>"><?= $item['name'] ?></a>
                                                <?php if ($item['variant_id'] && ($item['storage_value'] || $item['color_value'])): ?>
                                                    <div class="text-sm text-gray-500 mt-1">
                                                        <?php 
                                                        $variant_parts = [];
                                                        if ($item['storage_value']) $variant_parts[] = $item['storage_value'];
                                                        if ($item['color_value']) $variant_parts[] = $item['color_value'];
                                                        echo implode(' - ', $variant_parts);
                                                        ?>
                                                    </div>
                                                <?php endif; ?>
                                        </td>
                                            <td class="py-4 px-4 size text-center">
                                                <input type="number" 
                                                    class="quantity-input w-20 border text-center py-2 px-2 border-gray-300 rounded-md outline-blue-400"
                                                    min="1"
                                                    max="<?= $item['stock_quantity'] ?>" 
                                                    value="<?= $item['cart_quantity'] ?>" 
                                                    data-product-id="<?= $item['product_id'] ?>"
                                                    data-variant-id="<?= $item['variant_id'] ?? '' ?>"
                                                    aria-label="Quantity for <?= $item['name'] ?>">
                                            </td>
                                            <td class="py-4 px-4 price text-right text-body fs-9 fw-semibold item-total" data-price="<?= $price ?>">
                                                <?= number_format($price * $item['cart_quantity'], 2) ?> Frw
                                        </td>
                                            <td class="py-4 px-2 text-center">
                                                <form method="POST" action="./src/services/cart/cart_handler.php" style="display:inline;">
                                                    <input type="hidden" name="action" value="remove_item">
                                                    <?php if ($is_logged_in): ?>
                                                        <input type="hidden" name="cart_item_id" value="<?= $item['cart_item_id'] ?? '' ?>">
                                                    <?php else: ?>
                                                        <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                                                        <input type="hidden" name="variant_id" value="<?= $item['variant_id'] ?? '' ?>">
                                                    <?php endif; ?>
                                                    <button type="submit" class="remove-item-btn cursor-pointer text-gray-400 hover:text-red-500" aria-label="Remove <?= $item['name'] ?>">
                                                        <span class="fas fa-trash-alt"></span>
                                                    </button>
                                                </form>
                                            </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="lg:w-1/3 observe-card">
            <div id="summary-section" class="bg-white p-6 border border-gray-200 rounded-sm sticky top-24">
                <h2 class="text-xl font-bold text-gray-600 mb-6">Summary</h2>
                <div class="space-y-3 text-sm mb-6 text-gray-500">
                    <div class="flex justify-between items-center">
                        <span>Subtotal</span>
                        <span class="font-medium" id="summary-subtotal"><?= number_format($totals['subtotal'], 2) ?> Frw</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span>Discount</span>
                        <span class="font-medium" id="summary-discount">- <?= number_format($totals['discount'], 2) ?> Frw</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span>Tax (18%)</span>
                        <span class="font-medium" id="summary-tax"><?= number_format($totals['tax'], 2) ?> Frw</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span>Shipping</span>
                        <span class="font-medium" id="summary-shipping"><?= number_format($totals['shipping'], 2) ?> Frw</span>
                    </div>
                </div>
                <div class="flex justify-between items-center text-lg font-bold border-t border-gray-200 pt-4 mt-4">
                    <span>Total</span>
                    <span id="summary-total"><?= number_format($totals['total'], 2) ?> Frw</span>
                </div>
                <form method="POST" action="./src/services/checkout/checkout_summary.php" class="w-full mt-6">
                    <?php foreach ($cart_items as $index => $item): ?>
                        <input type="hidden" name="products[<?= $index ?>][product_id]" value="<?= htmlspecialchars($item['product_id']) ?>">
                        <input type="hidden" name="products[<?= $index ?>][variant_id]" value="<?= htmlspecialchars($item['variant_id'] ?? '') ?>">
                        <input type="hidden" name="products[<?= $index ?>][name]" value="<?= htmlspecialchars($item['name']) ?>">
                        <input type="hidden" name="products[<?= $index ?>][color]" value="<?= htmlspecialchars($item['color_value'] ?? '') ?>">
                        <input type="hidden" name="products[<?= $index ?>][storage]" value="<?= htmlspecialchars($item['storage_value'] ?? '') ?>">
                        <input type="hidden" name="products[<?= $index ?>][unit_price]" value="<?= htmlspecialchars($item['unit_price'] ?? ($item['discount_price'] > 0 ? $item['discount_price'] : $item['base_price'])) ?>">
                        <input type="hidden" name="products[<?= $index ?>][quantity]" value="<?= (int)$item['cart_quantity'] ?>">
                    <?php endforeach; ?>
                    <input type="hidden" name="customer" value="<?= htmlspecialchars($customer_id) ?>">
                    <input type="hidden" name="subtotal" value="<?= htmlspecialchars($totals['subtotal']) ?>">
                    <input type="hidden" name="discount" value="<?= htmlspecialchars($totals['discount']) ?>">
                    <input type="hidden" name="tax" value="<?= htmlspecialchars($totals['tax']) ?>">
                    <input type="hidden" name="shipping_cost" value="<?= htmlspecialchars($totals['shipping']) ?>">
                    <input type="hidden" name="total" value="<?= htmlspecialchars($totals['total']) ?>">
                    <button type="submit" class="w-full text-center bg-blue-500 text-white py-3 block rounded-lg font-bold hover:bg-blue-600 transition-colors">
                        Proceed to Checkout
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cartApiUrl = './src/services/cart/cart_handler.php';
        const summarySection = document.getElementById('summary-section');
        const checkoutLink = document.getElementById('checkout-link');

        const formatCurrency = (amount) => `${parseFloat(amount).toFixed(2)} Frw`;

        const updateSummary = (data) => {
            if (!data) return;
            document.getElementById('summary-subtotal').textContent = formatCurrency(data.subtotal);
            document.getElementById('summary-discount').textContent = `- ${formatCurrency(data.discount)}`;
            document.getElementById('summary-tax').textContent = formatCurrency(data.tax);
            document.getElementById('summary-shipping').textContent = formatCurrency(data.shipping);
            document.getElementById('summary-total').textContent = formatCurrency(data.total);

            const cartItemCount = data.item_count || 0;
            document.getElementById('cart-item-count').textContent = cartItemCount;

            // Update both header and cart page counts
            const headerCartCount = document.getElementById('cart-count');
            if (headerCartCount) {
                headerCartCount.textContent = cartItemCount;
            }
            const cartQuantity = document.getElementById('cart-quantity');
            if (cartQuantity) {
                cartQuantity.textContent = cartItemCount;
            }

            if (cartItemCount === 0) {
                summarySection.classList.add('opacity-50', 'pointer-events-none');
                checkoutLink.href = '#';
            } else {
                summarySection.classList.remove('opacity-50', 'pointer-events-none');
            }
        };

        const handleCartAction = async (action, payload) => {
            summarySection.classList.add('opacity-50'); // Visual feedback for loading
            try {
                const response = await fetch(cartApiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ action, ...payload })
                });
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                const result = await response.json();

                if (result.success) {
                    updateSummary(result.data);
                    return result.data; // Return data for local updates
                } else {
                    console.error('Cart action failed:', result.message);
                    // TODO: Add a user-facing notification (toast)
                    return null;
                }
            } catch (error) {
                console.error('Error during cart action:', error);
                // TODO: Add a user-facing notification (toast)
                return null;
            } finally {
                summarySection.classList.remove('opacity-50');
            }
        };

        // --- Event Listeners ---
        const cartTableBody = document.getElementById('cart-table-body');

        cartTableBody.addEventListener('change', async (e) => {
            if (!e.target.classList.contains('quantity-input')) return;

            const input = e.target;
            const payload = {
                product_id: input.dataset.productId,
                variant_id: input.dataset.variantId,
                quantity: parseInt(input.value, 10)
            };
            const row = input.closest('.cart-item-row');

            if (payload.quantity < 1) {
                input.value = 1; // Or trigger remove action
                return;
            }

            const data = await handleCartAction('update_quantity', payload);

            if (data) {
                const price = parseFloat(row.querySelector('.item-total').dataset.price);
                const itemTotalEl = row.querySelector('.item-total');
                itemTotalEl.textContent = formatCurrency(price * payload.quantity);
            }
        });
    });
</script>