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

$customer_name = !empty($order['user_id']) ? ($order['user_first_name'] . ' ' . $order['user_last_name']) : ($order['shipping_first_name'] . ' ' . $order['shipping_last_name']);
$customer_email = !empty($order['user_id']) ? $order['user_email'] : $order['shipping_email'];
$customer_phone = $order['shipping_phone'];
?>

<div class="content">
    <div class="mb-9">
        <div class="row g-3 mb-4">
            <div class="col-auto">
                <h2 class="mb-0">Edit Order</h2>
            </div>
        </div>

        <form action="./src/services/orders/update_order.php" method="POST">
            <input type="hidden" name="order_id" value="<?= $order_id ?>">

            <div class="row g-4">
                <!-- Order Status and Payment -->
                <div class="col-12 col-xl-8">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="mb-4">Order Status</h4>
                            <div class="row g-3">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label" for="status">Order Status</label>
                                        <select class="form-select" id="status" name="status" required>
                                            <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="processing" <?= $order['status'] == 'processing' ? 'selected' : '' ?>>Processing</option>
                                            <option value="shipped" <?= $order['status'] == 'shipped' ? 'selected' : '' ?>>Shipped</option>
                                            <option value="delivered" <?= $order['status'] == 'delivered' ? 'selected' : '' ?>>Delivered</option>
                                            <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label" for="payment_status">Payment Status</label>
                                        <select class="form-select" id="payment_status" name="payment_status" required>
                                            <option value="pending" <?= $order['payment_status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="paid" <?= $order['payment_status'] == 'paid' ? 'selected' : '' ?>>Paid</option>
                                            <option value="failed" <?= $order['payment_status'] == 'failed' ? 'selected' : '' ?>>Failed</option>
                                            <option value="refunded" <?= $order['payment_status'] == 'refunded' ? 'selected' : '' ?>>Refunded</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label" for="tracking_number">Tracking Number</label>
                                        <input type="text" class="form-control" id="tracking_number" name="tracking_number" value="<?= $order['tracking_number'] ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h4 class="mb-4">Shipping Details</h4>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="form-label" for="shipping_method">Shipping Method</label>
                                        <input type="text" class="form-control" id="shipping_method" name="shipping_method" value="<?= $order['shipping_method'] ?>" required>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label" for="shipping_cost">Shipping Cost (RWF)</label>
                                        <input type="number" class="form-control" id="shipping_cost" name="shipping_cost" value="<?= $order['shipping_cost'] ?>" required>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label" for="tax_amount">Tax Amount (RWF)</label>
                                        <input type="number" class="form-control" id="tax_amount" name="tax_amount" value="<?= $order['tax_amount'] ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="col-12 col-xl-4">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h4 class="mb-4">Customer Information</h4>
                            <div class="mb-3">
                                <p class="mb-1"><strong>Name:</strong> <?= $customer_name ?></p>
                                <p class="mb-1"><strong>Email:</strong> <?= $customer_email ?></p>
                                <p class="mb-1"><strong>Phone:</strong> <?= $customer_phone ?></p>
                            </div>
                            <div class="mb-3">
                                <h5>Shipping Address</h5>
                                <p class="mb-1"><?= $order['shipping_address'] ?></p>
                                <p class="mb-1"><?= $order['shipping_district'] ?></p>
                                <p class="mb-0"><?= $order['shipping_sector'] ?></p>
                            </div>
                            <div class="mb-3">
                                <h5>Billing Address</h5>
                                <p class="mb-1"><?= $order['billing_address'] ?></p>
                                <p class="mb-1"><?= $order['billing_district'] ?></p>
                                <p class="mb-0"><?= $order['billing_sector'] ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h4 class="mb-4">Order Summary</h4>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span><?= number_format($order['total_amount'] - $order['shipping_cost'] - $order['tax_amount'], 2) ?> RWF</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Shipping:</span>
                                <span><?= number_format($order['shipping_cost'], 2) ?> RWF</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span>Tax:</span>
                                <span><?= number_format($order['tax_amount'], 2) ?> RWF</span>
                            </div>
                            <div class="d-flex justify-content-between fw-bold">
                                <span>Total:</span>
                                <span><?= number_format($order['total_amount'], 2) ?> RWF</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary me-2">Update Order</button>
                <a href="order-details?order=<?= $order_id ?>" class="btn btn-phoenix-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>