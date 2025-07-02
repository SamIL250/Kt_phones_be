<?php
// Fetch recent orders
$orders_query = mysqli_prepare(
    $conn,
    "SELECT o.*, 
            COUNT(oi.order_item_id) as total_items,
            GROUP_CONCAT(p.name SEPARATOR ', ') as product_names
     FROM orders o
     LEFT JOIN order_items oi ON o.order_id = oi.order_id
     LEFT JOIN products p ON oi.product_id = p.product_id
     WHERE o.user_id = ?
     GROUP BY o.order_id
     ORDER BY o.order_date DESC
     LIMIT 5"
);
mysqli_stmt_bind_param($orders_query, "i", $customer_id);
mysqli_stmt_execute($orders_query);
$orders_result = mysqli_stmt_get_result($orders_query);
$orders = mysqli_fetch_all($orders_result, MYSQLI_ASSOC);
?>

<div id="recent-orders" class="bg-white border border-gray-200 p-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-600">Recent Orders</h2>
        <a href="/orders" class="text-blue-600 hover:text-blue-700 font-medium">
            View All Orders
        </a>
    </div>

    <?php if (empty($orders)): ?>
    <div class="text-center py-8">
        <i class="fas fa-shopping-bag text-gray-400 text-4xl mb-3"></i>
        <p class="text-gray-600">No orders yet</p>
        <a href="/store" class="mt-4 text-blue-600 hover:text-blue-700 font-medium inline-block">
            Start Shopping
        </a>
    </div>
    <?php else: ?>
    <div class="space-y-4">
        <?php foreach ($orders as $order): ?>
        <div class="border border-gray-300 p-4">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h3 class="font-medium text-gray-600">Order #<?php echo htmlspecialchars($order['order_id']); ?></h3>
                    <p class="text-sm text-gray-500">
                        <?php echo date('F d, Y', strtotime($order['order_date'])); ?>
                    </p>
                </div>
                <span class="px-3 py-1 rounded-full text-sm font-medium
                    <?php
                    switch($order['status']) {
                        case 'pending':
                            echo 'bg-yellow-100 text-yellow-800';
                            break;
                        case 'processing':
                            echo 'bg-blue-100 text-blue-800';
                            break;
                        case 'shipped':
                            echo 'bg-purple-100 text-purple-800';
                            break;
                        case 'delivered':
                            echo 'bg-green-100 text-green-800';
                            break;
                        case 'cancelled':
                            echo 'bg-red-100 text-red-800';
                            break;
                        default:
                            echo 'bg-gray-100 text-gray-800';
                    }
                    ?>">
                    <?php echo ucfirst($order['status']); ?>
                </span>
            </div>
            <div class="text-sm text-gray-600 mb-3">
                <p><?php echo htmlspecialchars($order['total_items']); ?> items</p>
                <p class="truncate"><?php echo htmlspecialchars($order['product_names']); ?></p>
            </div>
            <div class="flex items-center justify-between">
                <p class="font-medium text-gray-700">
                    Total: RWF <?php echo number_format($order['total_amount'], 2); ?>
                </p>
                <a href="checkout-success?order_id=<?php echo $order['order_id']; ?>" 
                   class="text-blue-600 hover:text-blue-700 font-medium">
                    View Details
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>