<?php
include './config/config.php';

// Get parameters
$type = $_GET['type'] ?? 'overview';
$start_date = $_GET['start'] ?? date('Y-01-01');
$end_date = $_GET['end'] ?? date('Y-m-d');

// Get report data based on type
function getReportData($conn, $type, $start_date, $end_date) {
    $data = [];
    
    if ($type == 'all' || $type == 'overview') {
        // Stock Summary
        $stock_in_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count, SUM(stock_quantity) as total FROM products WHERE stock_quantity > 0"));
        $data['stock_in_products'] = (int)($stock_in_row['count'] ?? 0);
        $data['total_stock_units'] = (int)($stock_in_row['total'] ?? 0);
        
        $out_of_stock_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM products WHERE stock_quantity = 0"));
        $data['out_of_stock_products'] = (int)($out_of_stock_row['count'] ?? 0);
        
        $stock_value_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(stock_quantity * base_price) as value FROM products WHERE stock_quantity > 0"));
        $data['total_stock_value'] = (float)($stock_value_row['value'] ?? 0);
        
        // Revenue
        $revenue_result = mysqli_query($conn, "SELECT SUM(total_amount) as revenue FROM orders WHERE order_date BETWEEN '$start_date' AND '$end_date' AND payment_status = 'paid' AND status != 'cancelled'");
        $revenue_row = mysqli_fetch_assoc($revenue_result);
        $data['total_revenue'] = (float)($revenue_row['revenue'] ?? 0);
        
        $orders_result = mysqli_query($conn, "SELECT COUNT(*) as count FROM orders WHERE order_date BETWEEN '$start_date' AND '$end_date' AND payment_status = 'paid' AND status != 'cancelled'");
        $orders_row = mysqli_fetch_assoc($orders_result);
        $data['total_orders'] = (int)($orders_row['count'] ?? 0);
        
        // Users
        $registered_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE created_at BETWEEN '$start_date' AND '$end_date'"))['count'];
        $guest_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM orders WHERE is_guest_order = 1 AND order_date BETWEEN '$start_date' AND '$end_date'"))['count'];
        $data['registered_users'] = (int)$registered_users;
        $data['guest_orders'] = (int)$guest_orders;
        
        // Reviews
        $avg_rating = mysqli_fetch_assoc(mysqli_query($conn, "SELECT AVG(rating) as avg FROM product_reviews WHERE created_at BETWEEN '$start_date' AND '$end_date'"))['avg'];
        $data['avg_rating'] = $avg_rating !== null ? number_format($avg_rating, 2) : '0.00';
    }
    
    if ($type == 'all' || $type == 'sales') {
        // Orders by status
        $order_statuses = ['pending','processing','shipped','delivered','cancelled'];
        $data['orders_by_status'] = [];
        foreach ($order_statuses as $status) {
            $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM orders WHERE status = '$status' AND order_date BETWEEN '$start_date' AND '$end_date'");
            $row = mysqli_fetch_assoc($result);
            $data['orders_by_status'][$status] = (int)($row['count'] ?? 0);
        }
        
        // Payment methods
        $pm_query = mysqli_query($conn, "SELECT payment_method, COUNT(*) as count FROM orders WHERE order_date BETWEEN '$start_date' AND '$end_date' GROUP BY payment_method");
        $data['payment_methods'] = [];
        while ($row = mysqli_fetch_assoc($pm_query)) {
            $data['payment_methods'][$row['payment_method']] = (int)$row['count'];
        }
    }
    
    if ($type == 'all' || $type == 'inventory') {
        // Low stock
        $low_stock = mysqli_query($conn, "SELECT name, stock_quantity FROM products WHERE stock_quantity > 0 AND stock_quantity <= 10 ORDER BY stock_quantity ASC LIMIT 10");
        $data['low_stock'] = [];
        while ($row = mysqli_fetch_assoc($low_stock)) {
            $data['low_stock'][] = $row;
        }
        
        // Out of stock
        $out_of_stock = mysqli_query($conn, "SELECT name FROM products WHERE stock_quantity = 0 LIMIT 10");
        $data['out_of_stock'] = [];
        while ($row = mysqli_fetch_assoc($out_of_stock)) {
            $data['out_of_stock'][] = $row;
        }
    }
    
    if ($type == 'all' || $type == 'customers') {
        // Customer growth
        $customer_data = [];
        for ($i = 1; $i <= 12; $i++) {
            $month_start = date('Y-m-01', strtotime("$start_date +".($i-1)." months"));
            $month_end = date('Y-m-t', strtotime($month_start));
            if ($month_start < $start_date) $month_start = $start_date;
            if ($month_end > $end_date) $month_end = $end_date;
            $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE created_at BETWEEN '$month_start' AND '$month_end'");
            $row = mysqli_fetch_assoc($result);
            $customer_data[date('M', mktime(0, 0, 0, $i, 10))] = (int)($row['count'] ?? 0);
        }
        $data['customer_growth'] = $customer_data;
    }
    
    if ($type == 'all' || $type == 'products') {
        // Top products
        $top_products = mysqli_query($conn, "SELECT p.name, SUM(oi.quantity) as sold, SUM(oi.subtotal) as revenue FROM order_items oi JOIN products p ON oi.product_id = p.product_id JOIN orders o ON oi.order_id = o.order_id WHERE o.order_date BETWEEN '$start_date' AND '$end_date' GROUP BY oi.product_id ORDER BY sold DESC LIMIT 10");
        $data['top_products'] = [];
        while ($row = mysqli_fetch_assoc($top_products)) {
            $data['top_products'][] = $row;
        }
        
        // Top categories
        $top_categories = mysqli_query($conn, "SELECT c.name, COUNT(p.product_id) as count FROM categories c LEFT JOIN products p ON c.category_id = p.category_id GROUP BY c.category_id ORDER BY count DESC LIMIT 10");
        $data['top_categories'] = [];
        while ($row = mysqli_fetch_assoc($top_categories)) {
            $data['top_categories'][] = $row;
        }
        
        // Top brands
        $top_brands = mysqli_query($conn, "SELECT b.name, COUNT(p.product_id) as count FROM brands b LEFT JOIN products p ON b.brand_id = p.brand_id GROUP BY b.brand_id ORDER BY count DESC LIMIT 10");
        $data['top_brands'] = [];
        while ($row = mysqli_fetch_assoc($top_brands)) {
            $data['top_brands'][] = $row;
        }
        
        // Most reviewed
        $most_reviewed = mysqli_query($conn, "SELECT p.name, COUNT(r.review_id) as reviews FROM product_reviews r JOIN products p ON r.product_id = p.product_id WHERE r.created_at BETWEEN '$start_date' AND '$end_date' GROUP BY r.product_id ORDER BY reviews DESC LIMIT 10");
        $data['most_reviewed'] = [];
        while ($row = mysqli_fetch_assoc($most_reviewed)) {
            $data['most_reviewed'][] = $row;
        }
    }
    
    return $data;
}

$reportData = getReportData($conn, $type, $start_date, $end_date);
$reportTitle = $type == 'all' ? 'Complete Business Report' : ucfirst($type) . ' Report';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KT-Phones <?= $reportTitle ?></title>
    <style>
        @media print {
            body { margin: 0; padding: 10px; }
            .no-print { display: none; }
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            background: white;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        
        .header h2 {
            margin: 5px 0;
            font-size: 14px;
        }
        
        .header p {
            margin: 5px 0;
            font-size: 11px;
        }
        
        .section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        
        .section h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            font-weight: bold;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }
        
        .data-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        
        .data-row .label {
            font-weight: bold;
        }
        
        .data-row .value {
            text-align: right;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        table th, table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
            font-size: 11px;
        }
        
        table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            border-top: 1px solid #000;
            padding-top: 10px;
        }
        
        .print-btn {
            position: fixed;
            top: 10px;
            right: 10px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">Print Report</button>
    
    <div class="header">
        <h1>KT-PHONES</h1>
        <h2><?= $reportTitle ?></h2>
        <p>Generated: <?= date('Y-m-d H:i:s') ?></p>
        <p>Date Range: <?= $start_date ?> to <?= $end_date ?></p>
    </div>

    <?php if ($type == 'all' || $type == 'overview'): ?>
    <div class="section">
        <h3>STOCK SUMMARY</h3>
        <div class="data-row">
            <span class="label">Products In Stock:</span>
            <span class="value"><?= number_format($reportData['stock_in_products']) ?></span>
        </div>
        <div class="data-row">
            <span class="label">Out of Stock Products:</span>
            <span class="value"><?= number_format($reportData['out_of_stock_products']) ?></span>
        </div>
        <div class="data-row">
            <span class="label">Total Stock Units:</span>
            <span class="value"><?= number_format($reportData['total_stock_units']) ?></span>
        </div>
        <div class="data-row">
            <span class="label">Stock Value:</span>
            <span class="value"><?= number_format($reportData['total_stock_value']) ?> RWF</span>
        </div>
    </div>

    <div class="section">
        <h3>REVENUE SUMMARY</h3>
        <div class="data-row">
            <span class="label">Total Revenue:</span>
            <span class="value"><?= number_format($reportData['total_revenue']) ?> RWF</span>
        </div>
        <div class="data-row">
            <span class="label">Total Orders:</span>
            <span class="value"><?= number_format($reportData['total_orders']) ?></span>
        </div>
        <div class="data-row">
            <span class="label">Registered Users:</span>
            <span class="value"><?= number_format($reportData['registered_users']) ?></span>
        </div>
        <div class="data-row">
            <span class="label">Guest Orders:</span>
            <span class="value"><?= number_format($reportData['guest_orders']) ?></span>
        </div>
        <div class="data-row">
            <span class="label">Average Rating:</span>
            <span class="value"><?= $reportData['avg_rating'] ?> / 5</span>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($type == 'all' || $type == 'sales'): ?>
    <div class="section">
        <h3>ORDERS BY STATUS</h3>
        <?php foreach ($reportData['orders_by_status'] as $status => $count): ?>
        <div class="data-row">
            <span class="label"><?= ucfirst($status) ?>:</span>
            <span class="value"><?= number_format($count) ?></span>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="section">
        <h3>PAYMENT METHODS</h3>
        <?php foreach ($reportData['payment_methods'] as $method => $count): ?>
        <div class="data-row">
            <span class="label"><?= ucfirst($method) ?>:</span>
            <span class="value"><?= number_format($count) ?></span>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if ($type == 'all' || $type == 'inventory'): ?>
    <div class="section">
        <h3>LOW STOCK PRODUCTS</h3>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Stock</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reportData['low_stock'] as $product): ?>
                <tr>
                    <td><?= htmlspecialchars($product['name']) ?></td>
                    <td><?= $product['stock_quantity'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h3>OUT OF STOCK PRODUCTS</h3>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reportData['out_of_stock'] as $product): ?>
                <tr>
                    <td><?= htmlspecialchars($product['name']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <?php if ($type == 'all' || $type == 'customers'): ?>
    <div class="section">
        <h3>CUSTOMER GROWTH (Monthly)</h3>
        <?php foreach ($reportData['customer_growth'] as $month => $count): ?>
        <div class="data-row">
            <span class="label"><?= $month ?>:</span>
            <span class="value"><?= number_format($count) ?></span>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if ($type == 'all' || $type == 'products'): ?>
    <div class="section">
        <h3>TOP PRODUCTS BY SALES</h3>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Sold</th>
                    <th>Revenue</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reportData['top_products'] as $product): ?>
                <tr>
                    <td><?= htmlspecialchars($product['name']) ?></td>
                    <td><?= $product['sold'] ?></td>
                    <td><?= number_format($product['revenue']) ?> RWF</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h3>TOP CATEGORIES</h3>
        <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Products</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reportData['top_categories'] as $category): ?>
                <tr>
                    <td><?= htmlspecialchars($category['name']) ?></td>
                    <td><?= $category['count'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h3>TOP BRANDS</h3>
        <table>
            <thead>
                <tr>
                    <th>Brand</th>
                    <th>Products</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reportData['top_brands'] as $brand): ?>
                <tr>
                    <td><?= htmlspecialchars($brand['name']) ?></td>
                    <td><?= $brand['count'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h3>MOST REVIEWED PRODUCTS</h3>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Reviews</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reportData['most_reviewed'] as $product): ?>
                <tr>
                    <td><?= htmlspecialchars($product['name']) ?></td>
                    <td><?= $product['reviews'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <div class="footer">
        <p>KT-Phones Business Report | Generated on <?= date('Y-m-d H:i:s') ?></p>
        <p>This is a computer-generated report</p>
    </div>
</body>
</html> 