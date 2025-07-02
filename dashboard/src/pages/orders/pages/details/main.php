<?php
if (!isset($_GET['order'])) {
    header('Location: orders.php');
    exit();
}

$order_id = $_GET['order'];

// Get order details
$order_query = mysqli_query(
    $conn,
    "SELECT 
        o.*,
        u.first_name AS user_first_name,
        u.last_name AS user_last_name,
        u.email AS user_email,
        sa.first_name AS shipping_first_name,
        sa.last_name AS shipping_last_name,
        sa.email AS shipping_email,
        sa.phone_number AS shipping_phone,
        sa.address_line1 AS shipping_address,
        sa.district AS shipping_district,
        sa.sector AS shipping_sector,
        ba.first_name AS billing_first_name,
        ba.last_name AS billing_last_name,
        ba.email AS billing_email,
        ba.phone_number AS billing_phone,
        ba.address_line1 AS billing_address,
        ba.district AS billing_district,
        ba.sector AS billing_sector
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.user_id
    LEFT JOIN addresses sa ON o.shipping_address_id = sa.address_id
    LEFT JOIN addresses ba ON o.billing_address_id = ba.address_id
    WHERE o.order_id = '$order_id'"
);

$order = mysqli_fetch_assoc($order_query);

if (!$order) {
    header('Location: orders.php');
    exit();
}

// Get order items
$items_query = mysqli_query(
    $conn,
    "SELECT 
        oi.*,
        p.name as product_name,
        p.sku,
        pi.image_url
    FROM order_items oi
    JOIN products p ON oi.product_id = p.product_id
    LEFT JOIN product_images pi ON p.product_id = pi.product_id AND pi.is_primary = 1
    WHERE oi.order_id = '$order_id'"
);

$order_items = array();
while ($item = mysqli_fetch_assoc($items_query)) {
    $order_items[] = $item;
}

$customer_name = !empty($order['user_id']) ? ($order['user_first_name'] . ' ' . $order['user_last_name']) : ($order['shipping_first_name'] . ' ' . $order['shipping_last_name']);
$customer_email = !empty($order['user_id']) ? $order['user_email'] : $order['shipping_email'];
$customer_phone = !empty($order['user_id']) ? $order['shipping_phone'] : $order['shipping_phone'];

// Get status badge class
$status_class = '';
switch ($order['status']) {
    case 'pending':
        $status_class = 'warning';
        break;
    case 'processing':
        $status_class = 'info';
        break;
    case 'shipped':
        $status_class = 'primary';
        break;
    case 'delivered':
        $status_class = 'success';
        break;
    case 'cancelled':
        $status_class = 'danger';
        break;
}
?>

<div class="content">
    <div class="mb-9">
        <div class="row g-3 mb-4">
            <div class="col-auto">
                <h2 class="mb-0">Order Details</h2>
            </div>
        </div>

        <!-- Order Summary Card -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row justify-content-between align-items-center">
                    <div class="col-md-4">
                        <h5 class="mb-2">Order #<?= $order['order_id'] ?></h5>
                        <p class="mb-0">Placed on <?= date('M d, Y', strtotime($order['order_date'])) ?></p>
                    </div>
                    <div class="col-md-4 text-md-center">
                        <span class="badge badge-phoenix badge-phoenix-<?= $status_class ?> fs-7">
                            <?= ucfirst($order['status']) ?>
                        </span>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <a href="print.php?order=<?= $order['order_id'] ?>" target="_blank" class="btn btn-phoenix-secondary me-1">Print Receipt</a>
                        <?php if ($order['status'] !== 'delivered' && $order['status'] !== 'cancelled'): ?>
                            <a href="./src/services/orders/cancel_order.php?order=<?= $order['order_id'] ?>"
                                class="btn btn-phoenix-danger me-1"
                                onclick="return confirm('Are you sure you want to cancel this order? This action cannot be undone.')">
                                Cancel Order
                            </a>
                        <?php endif; ?>
                        <a href="order-edit?order=<?= $order['order_id'] ?>" class="btn btn-phoenix-primary">Edit Order</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Customer Information -->
            <div class="col-12 col-xl-4 order-xl-1">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-4">
                            <h4>Customer</h4>
                            <p class="mb-0 fw-semibold"><?= $customer_name ?></p>
                            <p class="mb-0"><?= $customer_email ?></p>
                            <p class="mb-0"><?= $customer_phone ?></p>
                        </div>
                        <div class="mb-4">
                            <h4>Shipping Address</h4>
                            <p class="mb-0"><?= $order['shipping_address'] ?></p>
                            <p class="mb-0"><?= $order['shipping_district'] ?></p>
                            <p class="mb-0"><?= $order['shipping_sector'] ?></p>
                        </div>
                        <div class="mb-4">
                            <h4>Billing Address</h4>
                            <p class="mb-0"><?= $order['billing_address'] ?></p>
                            <p class="mb-0"><?= $order['billing_district'] ?></p>
                            <p class="mb-0"><?= $order['billing_sector'] ?></p>
                        </div>
                        <div>
                            <h4>Payment Details</h4>
                            <p class="mb-0">Method: <?= ucfirst($order['payment_method']) ?></p>
                            <p class="mb-0">Status:
                                <span class="badge badge-phoenix badge-phoenix-<?= $order['payment_status'] == 'paid' ? 'success' : 'warning' ?>">
                                    <?= ucfirst($order['payment_status']) ?>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="col-12 col-xl-8">
                <div class="card">
                    <div class="card-body">
                        <h4>Order Items</h4>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">Product</th>
                                        <th scope="col">SKU</th>
                                        <th scope="col">Price</th>
                                        <th scope="col">Quantity</th>
                                        <th scope="col" class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($order_items as $item): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?= $item['image_url'] ?>" alt="<?= $item['product_name'] ?>" width="50" class="me-2">
                                                    <div>
                                                        <h6 class="mb-0"><?= $item['product_name'] ?></h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?= $item['sku'] ?></td>
                                            <td><?= number_format($item['unit_price'], 2) ?> RWF</td>
                                            <td><?= $item['quantity'] ?></td>
                                            <td class="text-end"><?= number_format($item['subtotal'], 2) ?> RWF</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="border-top">
                                    <tr>
                                        <td colspan="4" class="text-end fw-semibold">Subtotal:</td>
                                        <td class="text-end"><?= number_format($order['total_amount'] - $order['shipping_cost'] - $order['tax_amount'], 2) ?> RWF</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-end fw-semibold">Shipping:</td>
                                        <td class="text-end"><?= number_format($order['shipping_cost'], 2) ?> RWF</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-end fw-semibold">Tax:</td>
                                        <td class="text-end"><?= number_format($order['tax_amount'], 2) ?> RWF</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-end fw-bold">Total:</td>
                                        <td class="text-end fw-bold"><?= number_format($order['total_amount'], 2) ?> RWF</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function printOrder(orderId) {
        // Get the base URL dynamically
        const baseUrl = window.location.protocol + '//' + window.location.host + window.location.pathname;
        const basePath = baseUrl.substring(0, baseUrl.lastIndexOf('/'));

        // Construct the print URL using absolute path
        const printUrl = basePath + '/src/pages/orders/pages/details/print_receipt.php?order=' + orderId;

        // Open print receipt in a new window with specific size
        const printWindow = window.open(printUrl, 'PrintWindow', 'width=800,height=900,scrollbars=yes');

        if (printWindow) {
            printWindow.focus();
        } else {
            // If popup was blocked, try opening in same window
            window.location.href = printUrl;
        }
    }
</script>