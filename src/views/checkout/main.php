<?php

// Get user information if logged in
$user_data = null;
$address_data = null;

if (isset($_COOKIE['customer_token'])) {
   
    // Get user information
    $user_query = mysqli_prepare(
        $conn,
        "SELECT * FROM users WHERE user_id = ? AND is_admin != 1"
    );
    mysqli_stmt_bind_param($user_query, "i", $customer_id);
    mysqli_stmt_execute($user_query);
    $user_result = mysqli_stmt_get_result($user_query);
    $user_data = mysqli_fetch_assoc($user_result);

    if ($customer_id) {
        // Get user's default address
        $address_query = mysqli_prepare(
            $conn,
            'SELECT * FROM addresses WHERE user_id = ? AND is_default = 1 AND is_guest = 0 LIMIT 1'
        );
        mysqli_stmt_bind_param($address_query, "i", $customer_id);
        mysqli_stmt_execute($address_query);
        $address_result = mysqli_stmt_get_result($address_query);
        $address_data = mysqli_fetch_assoc($address_result);
    }
}

// Get cart ID for current user
$cart_id = null;
if (isset($_COOKIE['customer_token'])) {
        // $customer_id = $payload['user_id'];
        // Get active cart for user
        $cart_query = mysqli_prepare(
            $conn,
            "SELECT cart_id FROM cart WHERE user_id = ? AND status = 'active' ORDER BY created_at DESC LIMIT 1"
        );
        if (!$cart_query) {
            die('Failed to prepare cart query: ' . mysqli_error($conn));
        }
        mysqli_stmt_bind_param($cart_query, "i", $customer_id);
        if (!mysqli_stmt_execute($cart_query)) {
            die('Failed to execute cart query: ' . mysqli_stmt_error($cart_query));
        }
        $cart_result = mysqli_stmt_get_result($cart_query);
        if ($cart_row = mysqli_fetch_assoc($cart_result)) {
            $cart_id = $cart_row['cart_id'];
        }
        mysqli_stmt_close($cart_query);
    
} else {
    // Get guest cart from cookie
    if (isset($_COOKIE['guest_cart_id'])) {
        $cart_id = $_COOKIE['guest_cart_id'];
    }
}

// If no active cart found, redirect to cart page
// if (!$cart_id) {
//     header('location:../cart');
//     exit();
// }

// Get cart summary
$summary = $_SESSION['checkout_summary'] ?? null;
$products_summary = [];

if ($summary && !empty($summary['products'])) {
    $products_summary = $summary['products'];

    // Collect all product_ids
    $product_ids = array_column($products_summary, 'product_id');
    if (!empty($product_ids)) {
        $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
        $query = "SELECT product_id, (SELECT image_url FROM product_images WHERE product_id = products.product_id AND is_primary = 1 LIMIT 1) AS image FROM products WHERE product_id IN ($placeholders)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, str_repeat('s', count($product_ids)), ...$product_ids);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // Build a map of product_id => image
        $images = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $images[$row['product_id']] = $row['image'];
        }

        // Merge images into products_summary
        foreach ($products_summary as &$product) {
            $product['image'] = $images[$product['product_id']] ?? null;
        }
        unset($product);
    }
}

// If no products in cart, redirect to cart page
if (empty($products_summary)) {
    ?>
        <script>window.location.replace('cart')</script>
    <?php
    exit();
}
?>

<!-- Breadcrumb -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex items-center space-x-2 text-sm">
        <a href="../store" class="text-blue-500">Store</a>
        <span>›</span>
        <span class="text-gray-500">Check Out</span>
    </div>
</div>

<!-- Main Content -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="min-h-screen">
        <div class="">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column - Forms -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Delivery Details -->
                    <form action="<?= $is_logged_in ? './src/services/order/place.php' : './src/services/order/guest_order.php' ?>" method="POST" class="bg-white p-6 grid gap-4">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-semibold text-gray-600">Check out</h2>
                        </div>

                        <?php if ($user_data): ?>
                        <!-- Logged in user details -->
                        <div class="mb-6">
                            <div class="bg-gray-50 p-6 border border-gray-200">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between gap-3 lg:max-w-[60%]">
                                    <div class="flex items-center gap-3">
                                        <i class="fas fa-user text-gray-500"></i>
                                        <span class="text-sm text-gray-500">Name</span>
                                    </div>
                                    <div class="">
                                        <div>
                                                <p class="text-gray-700 font-medium">
                                                    <?php echo htmlspecialchars($user_data['first_name'] . ' ' . $user_data['last_name']); ?>
                                                </p>
                                        </div>
                                    </div>
                                </div>

                                    <?php if ($address_data): ?>
                                <div class="flex items-center justify-between gap-3 lg:max-w-[60%]">
                                    <div class="flex items-center gap-3">
                                        <i class="fas fa-map-marker-alt text-gray-500"></i>
                                        <span class="text-sm text-gray-500">Address</span>
                                    </div>
                                    <div class="">
                                        <div>
                                                <p class="text-gray-700 font-medium">
                                                    <?php 
                                                    $address_parts = array_filter([
                                                        $address_data['address_line1'],
                                                        $address_data['address_line2'],
                                                        $address_data['district'],
                                                        $address_data['sector'],
                                                        $address_data['cell'],
                                                        $address_data['country']
                                                    ]);
                                                    echo htmlspecialchars(implode(', ', $address_parts));
                                                    ?>
                                                </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between gap-3 lg:max-w-[60%]">
                                    <div class="flex items-center gap-3">
                                        <i class="fas fa-phone text-gray-500"></i>
                                        <span class="text-sm text-gray-500">Phone</span>
                                    </div>
                                    <div class="">
                                            <div>
                                                <p class="text-gray-700 font-medium">
                                                    <?php echo htmlspecialchars($address_data['phone_number'] ?? $user_data['phone_number'] ?? 'Not provided'); ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <?php else: ?>
                                    <div class="bg-yellow-50 border border-yellow-200 p-4">
                                        <div class="flex items-center gap-3">
                                            <i class="fas fa-exclamation-circle text-yellow-500"></i>
                                            <p class="text-yellow-700">No default address found. Please add an address in your profile.</p>
                                        </div>
                                        <a href="profile#addresses" class="mt-3 inline-block text-blue-600 hover:text-blue-700 font-medium">
                                            Add Address
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php else: ?>
                        <!-- Guest user form -->
                        <div class="mb-6">
                            <div class="bg-gray-50 p-6 border border-gray-200 rounded-lg shadow-sm">
                                <h3 class="text-xl font-semibold text-gray-700 mb-4 flex items-center gap-2">
                                    <i class="fas fa-user-plus text-blue-500"></i> Guest Checkout
                                </h3>
                                <div class="space-y-6">
                                    <!-- Contact Information -->
                                    <div>
                                        <h4 class="text-lg font-medium text-gray-600 mb-2 flex items-center gap-2">
                                            <i class="fas fa-address-card text-gray-400"></i> Contact Information
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1" for="first_name">First Name</label>
                                                <div class="relative">
                                                    <input type="text" name="first_name" id="first_name" required placeholder="e.g. John" class="border-2 border-gray-300 py-3 px-4 w-full focus:outline-blue-400 text-sm rounded-md focus:ring-2 focus:ring-blue-100 transition" autocomplete="given-name">
                                                    <span class="absolute right-3 top-3 text-gray-300"><i class="fas fa-user"></i></span>
                                                </div>
                                                <span class="text-xs text-red-500 hidden" id="first_name_error">First name is required</span>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1" for="last_name">Last Name</label>
                                                <div class="relative">
                                                    <input type="text" name="last_name" id="last_name" required placeholder="e.g. Doe" class="border-2 border-gray-300 py-3 px-4 w-full focus:outline-blue-400 text-sm rounded-md focus:ring-2 focus:ring-blue-100 transition" autocomplete="family-name">
                                                    <span class="absolute right-3 top-3 text-gray-300"><i class="fas fa-user"></i></span>
                                                </div>
                                                <span class="text-xs text-red-500 hidden" id="last_name_error">Last name is required</span>
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <label class="block text-sm font-medium text-gray-700 mb-1" for="email">Email</label>
                                            <div class="relative">
                                                <input type="email" name="email" id="email" required placeholder="e.g. john@example.com" class="border-2 border-gray-300 py-3 px-4 w-full focus:outline-blue-400 text-sm rounded-md focus:ring-2 focus:ring-blue-100 transition" autocomplete="email">
                                                <span class="absolute right-3 top-3 text-gray-300"><i class="fas fa-envelope"></i></span>
                                            </div>
                                            <span class="text-xs text-red-500 hidden" id="email_error">Valid email is required</span>
                                        </div>
                                        <div class="mt-4">
                                            <label class="block text-sm font-medium text-gray-700 mb-1" for="phone">Phone Number</label>
                                            <div class="relative">
                                                <input type="tel" name="phone" id="phone" required placeholder="e.g. 0781234567" class="border-2 border-gray-300 py-3 px-4 w-full focus:outline-blue-400 text-sm rounded-md focus:ring-2 focus:ring-blue-100 transition" autocomplete="tel">
                                                <span class="absolute right-3 top-3 text-gray-300"><i class="fas fa-phone"></i></span>
                                            </div>
                                            <span class="text-xs text-red-500 hidden" id="phone_error">Phone number is required</span>
                                        </div>
                                    </div>
                                    <!-- Shipping Address -->
                                    <div>
                                        <h4 class="text-lg font-medium text-gray-600 mb-2 flex items-center gap-2">
                                            <i class="fas fa-map-marker-alt text-gray-400"></i> Shipping Address
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1" for="address_line1">Address Line 1</label>
                                                <div class="relative">
                                                    <input type="text" name="address_line1" id="address_line1" required placeholder="e.g. 123 Main St" class="border-2 border-gray-300 py-3 px-4 w-full focus:outline-blue-400 text-sm rounded-md focus:ring-2 focus:ring-blue-100 transition" autocomplete="address-line1">
                                                    <span class="absolute right-3 top-3 text-gray-300"><i class="fas fa-home"></i></span>
                                                </div>
                                                <span class="text-xs text-red-500 hidden" id="address_line1_error">Address is required</span>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1" for="address_line2">Address Line 2 (Optional)</label>
                                                <div class="relative">
                                                    <input type="text" name="address_line2" id="address_line2" placeholder="Apartment, suite, etc." class="border-2 border-gray-300 py-3 px-4 w-full focus:outline-blue-400 text-sm rounded-md focus:ring-2 focus:ring-blue-100 transition" autocomplete="address-line2">
                                                    <span class="absolute right-3 top-3 text-gray-300"><i class="fas fa-building"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1" for="country">Country</label>
                                                <div class="relative">
                                                    <input type="text" name="country" id="country" required placeholder="e.g. Rwanda" class="border-2 border-gray-300 py-3 px-4 w-full focus:outline-blue-400 text-sm rounded-md focus:ring-2 focus:ring-blue-100 transition" autocomplete="country">
                                                    <span class="absolute right-3 top-3 text-gray-300"><i class="fas fa-flag"></i></span>
                                                </div>
                                                <span class="text-xs text-red-500 hidden" id="country_error">Country is required</span>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1" for="district">District</label>
                                                <div class="relative">
                                                    <input type="text" name="district" id="district" placeholder="e.g. Nyarugenge" class="border-2 border-gray-300 py-3 px-4 w-full focus:outline-blue-400 text-sm rounded-md focus:ring-2 focus:ring-blue-100 transition" autocomplete="address-level2">
                                                    <span class="absolute right-3 top-3 text-gray-300"><i class="fas fa-map"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1" for="sector">Sector</label>
                                                <div class="relative">
                                                    <input type="text" name="sector" id="sector" placeholder="e.g. Nyakabanda" class="border-2 border-gray-300 py-3 px-4 w-full focus:outline-blue-400 text-sm rounded-md focus:ring-2 focus:ring-blue-100 transition" autocomplete="address-level3">
                                                    <span class="absolute right-3 top-3 text-gray-300"><i class="fas fa-map-pin"></i></span>
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1" for="cell">Cell</label>
                                                <div class="relative">
                                                    <input type="text" name="cell" id="cell" placeholder="e.g. Nyakabanda I" class="border-2 border-gray-300 py-3 px-4 w-full focus:outline-blue-400 text-sm rounded-md focus:ring-2 focus:ring-blue-100 transition" autocomplete="address-level4">
                                                    <span class="absolute right-3 top-3 text-gray-300"><i class="fas fa-location-arrow"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Delivery Type -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Delivery Type</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="border border-gray-300 p-4">
                                    <label class="flex items-start gap-3 cursor-pointer">
                                        <input type="radio" name="delivery_type" value="free" class="mt-1">
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between">
                                                <span class="text-gray-500 font-bold">Free Delivery</span>
                                                <span class="font-bold text-gray-600">0.00 Frw</span>
                                            </div>
                                            <?php
                                            $start_date = date('M d', strtotime('+5 days'));
                                            $end_date = date('M d', strtotime('+7 days'));
                                            ?>
                                            <p class="text-sm text-gray-500 mt-1">Est. delivery: <?= $start_date ?> - <?= $end_date ?></p>
                                            <p class="text-sm text-gray-500">Get Free Shipped products in 7 days!</p>
                                        </div>
                                    </label>
                                </div>

                                <div class="border border-gray-300 p-4">
                                    <label class="flex items-start gap-3 cursor-pointer">
                                        <input type="radio" name="delivery_type" value="two-day" class="mt-1">
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between">
                                                <span class="font-bold text-gray-500">Two days Delivery</span>
                                                <span class="font-bold text-gray-600">5000.00 Frw</span>
                                            </div>
                                            <?php
                                            $start_date = date('M d', strtotime('+1 days'));
                                            $end_date = date('M d', strtotime('+2 days'));
                                            ?>
                                            <p class="text-sm text-gray-500 mt-1">Est. delivery: <?= $start_date ?> - <?= $end_date ?></p>
                                            <p class="text-sm text-gray-500">Everything Arrive with minimum Delivery fee</p>
                                        </div>
                                    </label>
                                </div>

                                <div class="border border-gray-300 p-4">
                                    <label class="flex items-start gap-3 cursor-pointer">
                                        <input type="radio" name="delivery_type" value="standard" class="mt-1">
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between">
                                                <span class="font-bold text-gray-500">Standard Delivery</span>
                                                <span class="font-bold text-gray-600">3000.00 Frw</span>
                                            </div>
                                            <?php
                                            $start_date = date('M d', strtotime('+3 days'));
                                            $end_date = date('M d', strtotime('+5 days'));
                                            ?>
                                            <p class="text-sm text-gray-500 mt-1">Est. delivery: <?= $start_date ?> - <?= $end_date ?></p>
                                            <p class="text-sm text-gray-500">Get cheap delivery with economy Delivery</p>
                                        </div>
                                    </label>
                                </div>

                                <div class="border-2 border-blue-500 p-4 bg-blue-50">
                                    <label class="flex items-start gap-3 cursor-pointer">
                                        <input type="radio" name="delivery_type" value="one-day" class="mt-1" checked>
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between">
                                                <span class="font-bold text-gray-500">One-day Delivery</span>
                                                <span class="font-bold text-gray-600">10,000.00 Frw</span>
                                            </div>
                                            <div class="flex items-center gap-2 mt-1">
                                                <?php
                                                $start_date = date('M d', strtotime('+1 days'));
                                                $end_date = date('M d', strtotime('+1 days'));
                                                ?>
                                                <p class="text-sm text-gray-500 mt-1">Est. delivery: <?= $start_date ?></p>
                                                <span class="bg-orange-100 text-orange-600 text-xs px-2 py-1 rounded font-medium">POPULAR</span>
                                            </div>
                                            <p class="text-sm text-gray-500">Highest priority Delivery in the lowest cost.</p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden fields for shipping cost -->
                        <input type="hidden" name="shipping_cost" id="shipping-cost-input" value="10000">
                        <?php if (!$is_logged_in): ?>
                            <input type="hidden" name="is_guest" value="1">
                        <?php else: ?>
                            <input type="hidden" name="cart_id" value="<?= $cart_id ?>">
                            <input type="hidden" name="customer_id" value="<?= $customer_id ?>">
                        <?php endif; ?>

                        <!-- Submit buttons -->
                        <div class="mt-6 space-y-3">
                            <button type="submit" muted class="w-full bg-blue-600 text-white py-3 font-medium hover:bg-blue-700 transition-colors">
                                Pay <span id="total-amount-2"><?= number_format($summary['total'], 2) ?> Frw</span>
                            </button>
                            <button type="button" onclick="saveOrder()" class="w-full border border-gray-300 text-gray-700 py-3 font-medium hover:bg-gray-50 transition-colors">
                                Save Order and Exit
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Right Column - Summary -->
                <div class="lg:col-span-1 text-gray-500">
                    <div class="bg-white border border-gray-300 p-6 sticky top-8">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Summary</h3>
                            <button onclick="window.location.replace('../cart')" class="cursor-pointer text-blue-600 hover:text-blue-700 text-sm font-medium">
                                Edit cart
                            </button>
                        </div>

                        <div class="space-y-4 mb-6">
                            <?php if (!empty($products_summary)): ?>
                                <div class="space-y-4 mb-6">
                                    <?php foreach ($products_summary as $product): ?>
                                        <div class="flex items-center gap-3">
                                            <div class="w-12 h-12 bg-gray-200 rounded overflow-hidden">
                                                <?php if (!empty($product['image'])): ?>
                                                    <img src="<?= htmlspecialchars($product['image']) ?>" class="w-full h-full object-cover" alt="<?= htmlspecialchars($product['name']) ?>" />
                                                <?php else: ?>
                                                    <i class="fas fa-box-open text-gray-500 w-full h-full flex items-center justify-center"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-sm font-medium mb-1"><?= htmlspecialchars($product['name']) ?></p>
                                                <?php if (!empty($product['color']) || !empty($product['storage'])): ?>
                                                    <div class="text-xs text-gray-500">
                                                        <?php if (!empty($product['color'])): ?>Color: <?= htmlspecialchars($product['color']) ?><br><?php endif; ?>
                                                        <?php if (!empty($product['storage'])): ?>Storage: <?= htmlspecialchars($product['storage']) ?><br><?php endif; ?>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if (!empty($product['variant_id'])): ?>
                                                    <div class="text-xs text-gray-400">Variant ID: <?= htmlspecialchars($product['variant_id']) ?></div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm font-medium">×<?= (int)($product['quantity'] ?? 1) ?></p>
                                                <p class="text-sm font-bold">
                                                    <?= number_format((float)($product['unit_price'] ?? 0) * (int)($product['quantity'] ?? 1), 2) ?> Frw
                                                </p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="border-t pt-4 border-gray-200 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span>Items subtotal:</span>
                                <span><?= number_format($summary['subtotal'], 2) ?> Frw</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span>Discount:</span>
                                <span class="text-red-500">-<?= number_format($summary['discount'], 2) ?> Frw</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span>Tax:</span>
                                <span><?= number_format($summary['tax'], 2) ?> Frw</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span>Delivery Cost:</span>
                                <span id="delivery-cost"><?= number_format($summary['shipping_cost'], 2) ?> Frw</span>
                            </div>

                            <div class="border-t border-gray-200 pt-2">
                                <div class="flex justify-between font-semibold">
                                    <span>Total:</span>
                                    <span id="total-amount"><?= number_format($summary['total'], 2) ?> Frw</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Update delivery cost and total when delivery type changes
document.querySelectorAll('input[name="delivery_type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const deliveryCosts = {
            'free': 0,
            'two-day': 5000,
            'standard': 3000,
            'one-day': 10000
        };
        
        const cost = deliveryCosts[this.value];
        const subtotal = <?= $summary['subtotal'] ?>;
        const discount = <?= $summary['discount'] ?>;
        const tax = <?= $summary['tax'] ?>;
        
        const total = subtotal - discount + tax + cost;
        
        document.getElementById('delivery-cost').textContent = cost.toFixed(2) + ' Frw';
        document.getElementById('total-amount').textContent = total.toFixed(2) + ' Frw';
        document.getElementById('total-amount-2').textContent = total.toFixed(2) + ' Frw';
        document.getElementById('shipping-cost-input').value = cost;
    });
});

function saveOrder() {
    // Implement save order functionality
    alert('Save order functionality coming soon!');
}

// Simple inline validation for guest checkout form
// Only runs on the guest form
(function() {
    const form = document.querySelector('form[action*="guest_order.php"]');
    if (!form) return;
    form.addEventListener('submit', function(e) {
        let valid = true;
        const fields = [
            {id: 'first_name', error: 'first_name_error'},
            {id: 'last_name', error: 'last_name_error'},
            {id: 'email', error: 'email_error'},
            {id: 'phone', error: 'phone_error'},
            {id: 'address_line1', error: 'address_line1_error'},
            {id: 'country', error: 'country_error'}
        ];
        fields.forEach(f => {
            const input = document.getElementById(f.id);
            const error = document.getElementById(f.error);
            if (input && error) {
                if (!input.value.trim()) {
                    error.classList.remove('hidden');
                    input.classList.add('border-red-400');
                    valid = false;
                } else {
                    error.classList.add('hidden');
                    input.classList.remove('border-red-400');
                }
            }
        });
        if (!valid) {
            e.preventDefault();
        }
    });
})();
</script>
