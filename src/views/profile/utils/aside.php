<?php
// Get user statistics
$stats = array();

// Get order count
$order_query = mysqli_prepare(
    $conn,
    "SELECT COUNT(*) as order_count FROM orders WHERE user_id = ?"
);
mysqli_stmt_bind_param($order_query, "i", $customer_id);
mysqli_stmt_execute($order_query);
$order_result = mysqli_stmt_get_result($order_query);
$stats['orders'] = mysqli_fetch_assoc($order_result)['order_count'] ?? 0;

// Get wishlist count
$wishlist_query = mysqli_prepare(
    $conn,
    "SELECT COUNT(*) as wishlist_count FROM wishlist_items 
    INNER JOIN wishlists ON wishlists.wishlist_id = wishlist_items.wishlist_id 
    WHERE wishlists.user_id = ?"
);
mysqli_stmt_bind_param($wishlist_query, "i", $customer_id);
mysqli_stmt_execute($wishlist_query);
$wishlist_result = mysqli_stmt_get_result($wishlist_query);
$stats['wishlist'] = mysqli_fetch_assoc($wishlist_result)['wishlist_count'] ?? 0;

// Get review count
$review_query = mysqli_prepare(
    $conn,
    "SELECT COUNT(*) as review_count FROM product_reviews WHERE user_id = ?"
);
mysqli_stmt_bind_param($review_query, "i", $customer_id);
mysqli_stmt_execute($review_query);
$review_result = mysqli_stmt_get_result($review_query);
$stats['reviews'] = mysqli_fetch_assoc($review_result)['review_count'] ?? 0;

// Get address count
$address_query = mysqli_prepare(
    $conn,
    "SELECT COUNT(*) as address_count FROM addresses WHERE user_id = ? AND is_guest = 0"
);
mysqli_stmt_bind_param($address_query, "i", $customer_id);
mysqli_stmt_execute($address_query);
$address_result = mysqli_stmt_get_result($address_query);
$stats['addresses'] = mysqli_fetch_assoc($address_result)['address_count'] ?? 0;
?>

<div class="lg:col-span-1">
    <div class="bg-white">
        <nav class="p-4">
            <ul class="space-y-2">
                <li>
                    <a href="#personal-info" class="flex items-center gap-3 px-3 py-2 text-blue-600 bg-blue-50 font-medium">
                        <i class="fas fa-user"></i>
                        Personal Information
                    </a>
                </li>
                <li>
                    <a href="#addresses" class="flex items-center gap-3 px-3 py-2 text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-map-marker-alt"></i>
                        Addresses
                        <span class="ml-auto bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-full">
                            <?php echo $stats['addresses']; ?>
                        </span>
                    </a>
                </li>
                <li>
                    <a href="#payment-methods" class="flex items-center gap-3 px-3 py-2 text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-credit-card"></i>
                        Payment Methods
                    </a>
                </li>
                <li>
                    <a href="#orders" class="flex items-center gap-3 px-3 py-2 text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-shopping-bag"></i>
                        Order History
                        <span class="ml-auto bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-full">
                            <?php echo $stats['orders']; ?>
                        </span>
                    </a>
                </li>
                <li>
                    <a href="#wishlist" class="flex items-center gap-3 px-3 py-2 text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-heart"></i>
                        Wishlist
                        <span class="ml-auto bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-full">
                            <?php echo $stats['wishlist']; ?>
                        </span>
                    </a>
                </li>
                <li>
                    <a href="#reviews" class="flex items-center gap-3 px-3 py-2 text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-star"></i>
                        Reviews & Ratings
                        <span class="ml-auto bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-full">
                            <?php echo $stats['reviews']; ?>
                        </span>
                    </a>
                </li>
                <li>
                    <a href="#notifications" class="flex items-center gap-3 px-3 py-2 text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-bell"></i>
                        Notifications
                    </a>
                </li>
                <li>
                    <a href="#security" class="flex items-center gap-3 px-3 py-2 text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-shield-alt"></i>
                        Security
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Quick Stats -->
    <div class="bg-white mt-6 p-4">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">Quick Stats</h3>
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-blue-50 p-3 rounded-lg">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-shopping-bag text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Orders</p>
                        <p class="text-xl font-semibold text-gray-800"><?php echo $stats['orders']; ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-purple-50 p-3 rounded-lg">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-heart text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Wishlist Items</p>
                        <p class="text-xl font-semibold text-gray-800"><?php echo $stats['wishlist']; ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-green-50 p-3 rounded-lg">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-star text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Reviews</p>
                        <p class="text-xl font-semibold text-gray-800"><?php echo $stats['reviews']; ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-orange-50 p-3 rounded-lg">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-map-marker-alt text-orange-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Addresses</p>
                        <p class="text-xl font-semibold text-gray-800"><?php echo $stats['addresses']; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>