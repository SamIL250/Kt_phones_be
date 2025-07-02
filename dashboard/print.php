<?php
require_once './config/config.php';

if (!isset($_GET['order'])) {
    header('Location: orders.php');
    exit();
}

$order_id = $_GET['order'];

// Get order details with customer info
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
        p.sku
    FROM order_items oi
    JOIN products p ON oi.product_id = p.product_id
    WHERE oi.order_id = '$order_id'"
);

$order_items = array();
while ($item = mysqli_fetch_assoc($items_query)) {
    $order_items[] = $item;
}

$customer_name = !empty($order['user_id']) ? ($order['user_first_name'] . ' ' . $order['user_last_name']) : ($order['shipping_first_name'] . ' ' . $order['shipping_last_name']);
$customer_email = !empty($order['user_id']) ? $order['user_email'] : $order['shipping_email'];
$customer_phone = $order['shipping_phone'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Receipt - <?= $order_id ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: white !important;
            padding: 20px;
            margin: 0;
            line-height: 1.6;
        }

        .receipt {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .order-info,
        .customer-info {
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: white;
        }

        th,
        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f8f9fa;
        }

        .text-end {
            text-align: right;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin: 0 5px;
        }

        .btn-print {
            background-color: #28a745;
            color: white;
        }

        .btn-close {
            background-color: #6c757d;
            color: white;
        }

        @media print {
            body {
                background-color: white !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .receipt {
                box-shadow: none;
            }

            .d-print-none {
                display: none !important;
            }

            @page {
                margin: 0.5cm;
                background-color: white;
            }

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>

<body>
    <div class="receipt">
        <div class="header">
            <h1>KT Phones</h1>
            <p>Order Receipt</p>
        </div>

        <div class="order-info">
            <p><strong>Order ID:</strong> <?= $order_id ?></p>
            <p><strong>Date:</strong> <?= date('Y-m-d H:i:s', strtotime($order['order_date'])) ?></p>
            <p><strong>Status:</strong> <?= ucfirst($order['status']) ?></p>
        </div>

        <div class="customer-info">
            <h3>Customer Information</h3>
            <p><strong>Name:</strong> <?= $customer_name ?></p>
            <p><strong>Email:</strong> <?= $customer_email ?></p>
            <p><strong>Phone:</strong> <?= $customer_phone ?></p>
            <p><strong>Shipping Address:</strong> <?= $order['shipping_address'] ?>, <?= $order['shipping_district'] ?>, <?= $order['shipping_sector'] ?></p>
            <p><strong>Billing Address:</strong> <?= $order['billing_address'] ?>, <?= $order['billing_district'] ?>, <?= $order['billing_sector'] ?></p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order_items as $item): ?>
                    <tr>
                        <td><?= $item['product_name'] ?></td>
                        <td><?= $item['sku'] ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td><?= number_format($item['unit_price'], 2) ?> RWF</td>
                        <td><?= number_format($item['subtotal'], 2) ?> RWF</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
                    <td><?= number_format($order['total_amount'] - $order['shipping_cost'] - $order['tax_amount'], 2) ?> RWF</td>
                </tr>
                <tr>
                    <td colspan="4" class="text-end"><strong>Shipping:</strong></td>
                    <td><?= number_format($order['shipping_cost'], 2) ?> RWF</td>
                </tr>
                <tr>
                    <td colspan="4" class="text-end"><strong>Tax:</strong></td>
                    <td><?= number_format($order['tax_amount'], 2) ?> RWF</td>
                </tr>
                <tr>
                    <td colspan="4" class="text-end"><strong>Total:</strong></td>
                    <td><strong><?= number_format($order['total_amount'], 2) ?> RWF</strong></td>
                </tr>
            </tfoot>
        </table>

        <div class="text-center d-print-none" style="margin-top: 20px;">
            <button onclick="window.print()" class="btn btn-print">Print Receipt</button>
            <button onclick="window.close()" class="btn btn-close">Close</button>
        </div>
    </div>

    <script>
        // Auto-print when the page loads (after a short delay to ensure everything is loaded)
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 1000);
        };
    </script>
</body>

</html>