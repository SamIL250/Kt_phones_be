<?php
// --- GLOBAL DATE FILTER ---
$global_start = $_GET['start_date'] ?? date('Y-01-01');
$global_end = $_GET['end_date'] ?? date('Y-m-d');
// --- STOCK SUMMARY ---
$stock_in_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count, SUM(stock_quantity) as total FROM products WHERE stock_quantity > 0"));
$total_in_stock_products = (int)($stock_in_row['count'] ?? 0);
$total_stock_units = (int)($stock_in_row['total'] ?? 0);
$out_of_stock_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM products WHERE stock_quantity = 0"));
$total_out_of_stock_products = (int)($out_of_stock_row['count'] ?? 0);
$stock_value_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(stock_quantity * base_price) as value FROM products WHERE stock_quantity > 0"));
$total_stock_value = (float)($stock_value_row['value'] ?? 0);
?>
<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="index">Dashboard</a></li>
        <li class="breadcrumb-item active">Reports</li>
    </ol>
</nav>
<div class="mb-9">
    <style>
    .card-body canvas {
      min-height: 220px !important;
      height: 220px !important;
    }
    
    /* Print Styles */
    @media print {
        .no-print { display: none !important; }
        .print-only { display: block !important; }
        .card { border: 1px solid #ddd !important; margin-bottom: 20px !important; }
        .card-header { background-color: #f8f9fa !important; }
        .table { font-size: 12px !important; }
        .display-6 { font-size: 24px !important; }
        body { font-size: 14px !important; }
        .tab-content > .tab-pane { display: block !important; }
        .tab-content > .tab-pane:not(.active) { display: none !important; }
    }
    
    .print-only { display: none; }
    </style>
    <div class="row g-3 mb-4">
        <div class="col-auto">
            <h2 class="mb-0">Reports & Analytics</h2>
        </div>
        <div class="col-auto ms-auto">
            <button class="btn btn-outline-primary" onclick="printAllReports()">
                <i class="fas fa-print me-2"></i>Print All Reports
            </button>
        </div>
    </div>
    
    <!-- Stock Summary Cards (Always Visible) -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-header bg-light py-2"><h6 class="mb-0">Products In Stock</h6></div>
                <div class="card-body">
                    <div class="display-6 fw-bold text-success mb-2"><?php echo number_format($total_in_stock_products); ?></div>
                    <div class="text-muted">Products with stock &gt; 0</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-header bg-light py-2"><h6 class="mb-0">Out of Stock Products</h6></div>
                <div class="card-body">
                    <div class="display-6 fw-bold text-danger mb-2"><?php echo number_format($total_out_of_stock_products); ?></div>
                    <div class="text-muted">Products with stock = 0</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-header bg-light py-2"><h6 class="mb-0">Total Stock Units</h6></div>
                <div class="card-body">
                    <div class="display-6 fw-bold text-primary mb-2"><?php echo number_format($total_stock_units); ?></div>
                    <div class="text-muted">Sum of all stock units</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-header bg-light py-2"><h6 class="mb-0">Stock Value</h6></div>
                <div class="card-body">
                    <div class="display-6 fw-bold text-warning mb-2"><?php echo number_format($total_stock_value); ?> RWF</div>
                    <div class="text-muted">Total value of all stock</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Global Date Filter -->
    <form method="get" class="row g-2 align-items-end mb-4">
      <div class="col-auto">
        <label for="start_date" class="form-label mb-0">From</label>
        <input type="date" class="form-control" name="start_date" id="start_date" value="<?= htmlspecialchars($global_start) ?>">
      </div>
      <div class="col-auto">
        <label for="end_date" class="form-label mb-0">To</label>
        <input type="date" class="form-control" name="end_date" id="end_date" value="<?= htmlspecialchars($global_end) ?>">
      </div>
      <div class="col-auto">
        <button type="submit" class="btn btn-primary">Filter</button>
      </div>
    </form>

    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs mb-4 no-print" id="reportsTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">
                <i class="fas fa-chart-pie me-2"></i>Overview
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="sales-tab" data-bs-toggle="tab" data-bs-target="#sales" type="button" role="tab">
                <i class="fas fa-dollar-sign me-2"></i>Sales & Revenue
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="inventory-tab" data-bs-toggle="tab" data-bs-target="#inventory" type="button" role="tab">
                <i class="fas fa-boxes me-2"></i>Inventory
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="customers-tab" data-bs-toggle="tab" data-bs-target="#customers" type="button" role="tab">
                <i class="fas fa-users me-2"></i>Customers
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="products-tab" data-bs-toggle="tab" data-bs-target="#products" type="button" role="tab">
                <i class="fas fa-tags me-2"></i>Products
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="reportsTabContent">
        <!-- Overview Tab -->
        <div class="tab-pane fade show active" id="overview" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-3 no-print">
                <h5 class="mb-0">Overview Report</h5>
                <button class="btn btn-sm btn-outline-secondary" onclick="printReport('overview')">
                    <i class="fas fa-print me-1"></i>Print Overview
                </button>
            </div>
            <?php
            // --- SALES & REVENUE ---
            $sales_labels = [];
            $sales_data = [];
            $revenue_data = [];
            for ($i = 1; $i <= 12; $i++) {
                $month_start = date('Y-m-01', strtotime("$global_start +".($i-1)." months"));
                $month_end = date('Y-m-t', strtotime($month_start));
                if ($month_start < $global_start) $month_start = $global_start;
                if ($month_end > $global_end) $month_end = $global_end;
                $sales_labels[] = date('M', mktime(0, 0, 0, $i, 10));
                $result = mysqli_query($conn, "SELECT COUNT(*) as count, SUM(total_amount) as revenue FROM orders WHERE order_date BETWEEN '$month_start' AND '$month_end' AND payment_status = 'paid' AND status != 'cancelled'");
                $row = mysqli_fetch_assoc($result);
                $sales_data[] = (int)($row['count'] ?? 0);
                $revenue_data[] = (float)($row['revenue'] ?? 0);
            }
            $total_revenue = array_sum($revenue_data);
            $total_orders_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM orders WHERE order_date BETWEEN '$global_start' AND '$global_end' AND payment_status = 'paid' AND status != 'cancelled'"));
            $total_orders = (int)($total_orders_row['count'] ?? 0);
            // --- REGISTERED VS GUEST ---
            $registered_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE created_at BETWEEN '$global_start' AND '$global_end'"))['count'];
            $guest_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM orders WHERE is_guest_order = 1 AND order_date BETWEEN '$global_start' AND '$global_end'"))['count'];
            // --- REVIEWS ---
            $avg_rating = mysqli_fetch_assoc(mysqli_query($conn, "SELECT AVG(rating) as avg FROM product_reviews WHERE created_at BETWEEN '$global_start' AND '$global_end'"))['avg'];
            if ($avg_rating === null) $avg_rating = 0;
            ?>
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-header bg-light py-2"><h6 class="mb-0">Total Revenue</h6></div>
                        <div class="card-body">
                            <div class="display-6 fw-bold text-warning mb-2"><?php echo number_format($total_revenue); ?> RWF</div>
                            <div class="text-muted">Total Orders: <b><?php echo number_format($total_orders); ?></b></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-header bg-light py-2"><h6 class="mb-0">Average Product Rating</h6></div>
                        <div class="card-body">
                            <div class="display-6 fw-bold text-success mb-2"><?php echo number_format($avg_rating, 2); ?> / 5</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-header bg-light py-2"><h6 class="mb-0">Registered vs Guest Users</h6></div>
                        <div class="card-body">
                            <canvas id="usersPieChart" height="220"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-light py-2"><h6 class="mb-0">Monthly Sales & Revenue</h6></div>
                        <div class="card-body">
                            <canvas id="salesRevenueChart" height="220"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-light py-2"><h6 class="mb-0">Orders by Status</h6></div>
                        <div class="card-body">
                            <canvas id="ordersStatusChart" height="220"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales & Revenue Tab -->
        <div class="tab-pane fade" id="sales" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-3 no-print">
                <h5 class="mb-0">Sales & Revenue Report</h5>
                <button class="btn btn-sm btn-outline-secondary" onclick="printReport('sales')">
                    <i class="fas fa-print me-1"></i>Print Sales Report
                </button>
            </div>
            <?php
            // --- ORDERS BY STATUS ---
            $order_statuses = ['pending','processing','shipped','delivered','cancelled'];
            $orders_by_status = [];
            foreach ($order_statuses as $status) {
                $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM orders WHERE status = '$status' AND order_date BETWEEN '$global_start' AND '$global_end'");
                $row = mysqli_fetch_assoc($result);
                $orders_by_status[] = (int)($row['count'] ?? 0);
            }
            // --- ORDERS BY PAYMENT METHOD ---
            $payment_methods = [];
            $payment_counts = [];
            $pm_query = mysqli_query($conn, "SELECT payment_method, COUNT(*) as count FROM orders WHERE order_date BETWEEN '$global_start' AND '$global_end' GROUP BY payment_method");
            while ($row = mysqli_fetch_assoc($pm_query)) {
                $payment_methods[] = $row['payment_method'];
                $payment_counts[] = (int)$row['count'];
            }
            ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-light py-2"><h6 class="mb-0">Orders by Payment Method</h6></div>
                        <div class="card-body">
                            <canvas id="ordersPaymentChart" height="220"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-light py-2"><h6 class="mb-0">Orders by Status</h6></div>
                        <div class="card-body">
                            <canvas id="ordersStatusChart2" height="220"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory Tab -->
        <div class="tab-pane fade" id="inventory" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-3 no-print">
                <h5 class="mb-0">Inventory Report</h5>
                <button class="btn btn-sm btn-outline-secondary" onclick="printReport('inventory')">
                    <i class="fas fa-print me-1"></i>Print Inventory Report
                </button>
            </div>
            <?php
            // --- INVENTORY: LOW STOCK & OUT OF STOCK ---
            $low_stock = mysqli_query($conn, "SELECT name, stock_quantity FROM products WHERE stock_quantity > 0 AND stock_quantity <= 10 ORDER BY stock_quantity ASC LIMIT 5");
            $out_of_stock = mysqli_query($conn, "SELECT name FROM products WHERE stock_quantity = 0 LIMIT 5");
            ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-light py-2"><h6 class="mb-0">Low Stock Products</h6></div>
                        <div class="card-body">
                            <table class="table table-sm table-striped">
                                <thead><tr><th>Product</th><th>Stock</th></tr></thead>
                                <tbody>
                                <?php while($row = mysqli_fetch_assoc($low_stock)): ?>
                                    <tr><td><?= htmlspecialchars($row['name']) ?></td><td><?= $row['stock_quantity'] ?></td></tr>
                                <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-light py-2"><h6 class="mb-0">Out of Stock Products</h6></div>
                        <div class="card-body">
                            <table class="table table-sm table-striped">
                                <thead><tr><th>Product</th></tr></thead>
                                <tbody>
                                <?php while($row = mysqli_fetch_assoc($out_of_stock)): ?>
                                    <tr><td><?= htmlspecialchars($row['name']) ?></td></tr>
                                <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customers Tab -->
        <div class="tab-pane fade" id="customers" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-3 no-print">
                <h5 class="mb-0">Customer Report</h5>
                <button class="btn btn-sm btn-outline-secondary" onclick="printReport('customers')">
                    <i class="fas fa-print me-1"></i>Print Customer Report
                </button>
            </div>
            <?php
            // --- CUSTOMER GROWTH ---
            $cg_start = $_GET['cg_start'] ?? $global_start;
            $cg_end = $_GET['cg_end'] ?? $global_end;
            $customer_labels = [];
            $customer_data = [];
            for ($i = 1; $i <= 12; $i++) {
                $month_start = date('Y-m-01', strtotime("$cg_start +".($i-1)." months"));
                $month_end = date('Y-m-t', strtotime($month_start));
                if ($month_start < $cg_start) $month_start = $cg_start;
                if ($month_end > $cg_end) $month_end = $cg_end;
                $customer_labels[] = date('M', mktime(0, 0, 0, $i, 10));
                $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE created_at BETWEEN '$month_start' AND '$month_end'");
                $row = mysqli_fetch_assoc($result);
                $customer_data[] = (int)($row['count'] ?? 0);
            }
            ?>
            <div class="row g-3">
                <div class="col-md-12">
                    <div class="card h-100">
                        <div class="card-header bg-light py-2"><h6 class="mb-0">Customer Growth
                        <form method="get" class="d-inline-block ms-2">
                            <input type="date" name="cg_start" value="<?= htmlspecialchars($cg_start) ?>" class="form-control form-control-sm d-inline-block w-auto">
                            <input type="date" name="cg_end" value="<?= htmlspecialchars($cg_end) ?>" class="form-control form-control-sm d-inline-block w-auto">
                            <input type="hidden" name="start_date" value="<?= htmlspecialchars($global_start) ?>">
                            <input type="hidden" name="end_date" value="<?= htmlspecialchars($global_end) ?>">
                            <button type="submit" class="btn btn-sm btn-outline-primary">Filter</button>
                        </form>
                        </h6></div>
                        <div class="card-body">
                            <canvas id="customerGrowthChart" height="220"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Tab -->
        <div class="tab-pane fade" id="products" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-3 no-print">
                <h5 class="mb-0">Products Report</h5>
                <button class="btn btn-sm btn-outline-secondary" onclick="printReport('products')">
                    <i class="fas fa-print me-1"></i>Print Products Report
                </button>
            </div>
            <?php
            // --- PER-REPORT FILTERS ---
            $tp_start = $_GET['tp_start'] ?? $global_start;
            $tp_end = $_GET['tp_end'] ?? $global_end;
            $mr_start = $_GET['mr_start'] ?? $global_start;
            $mr_end = $_GET['mr_end'] ?? $global_end;
            // --- TOP PRODUCTS BY SALES ---
            $top_products = mysqli_query($conn, "SELECT p.name, SUM(oi.quantity) as sold, SUM(oi.subtotal) as revenue FROM order_items oi JOIN products p ON oi.product_id = p.product_id JOIN orders o ON oi.order_id = o.order_id WHERE o.order_date BETWEEN '$tp_start' AND '$tp_end' GROUP BY oi.product_id ORDER BY sold DESC LIMIT 5");
            // --- TOP CATEGORIES ---
            $top_categories = mysqli_query($conn, "SELECT c.name, COUNT(p.product_id) as count FROM categories c LEFT JOIN products p ON c.category_id = p.category_id GROUP BY c.category_id ORDER BY count DESC LIMIT 5");
            // --- TOP BRANDS ---
            $top_brands = mysqli_query($conn, "SELECT b.name, COUNT(p.product_id) as count FROM brands b LEFT JOIN products p ON b.brand_id = p.brand_id GROUP BY b.brand_id ORDER BY count DESC LIMIT 5");
            // --- REVIEWS ---
            $most_reviewed = mysqli_query($conn, "SELECT p.name, COUNT(r.review_id) as reviews FROM product_reviews r JOIN products p ON r.product_id = p.product_id WHERE r.created_at BETWEEN '$mr_start' AND '$mr_end' GROUP BY r.product_id ORDER BY reviews DESC LIMIT 5");
            ?>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-light py-2"><h6 class="mb-0">Top Products by Sales
                        <form method="get" class="d-inline-block ms-2">
                            <input type="date" name="tp_start" value="<?= htmlspecialchars($tp_start) ?>" class="form-control form-control-sm d-inline-block w-auto">
                            <input type="date" name="tp_end" value="<?= htmlspecialchars($tp_end) ?>" class="form-control form-control-sm d-inline-block w-auto">
                            <input type="hidden" name="start_date" value="<?= htmlspecialchars($global_start) ?>">
                            <input type="hidden" name="end_date" value="<?= htmlspecialchars($global_end) ?>">
                            <button type="submit" class="btn btn-sm btn-outline-primary">Filter</button>
                        </form>
                        </h6></div>
                        <div class="card-body">
                            <table class="table table-sm table-striped">
                                <thead><tr><th>Product</th><th>Sold</th><th>Revenue</th></tr></thead>
                                <tbody>
                                <?php while($row = mysqli_fetch_assoc($top_products)): ?>
                                    <tr><td><?= htmlspecialchars($row['name']) ?></td><td><?= $row['sold'] ?></td><td><?= number_format($row['revenue']) ?> RWF</td></tr>
                                <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-light py-2"><h6 class="mb-0">Most Reviewed Products
                        <form method="get" class="d-inline-block ms-2">
                            <input type="date" name="mr_start" value="<?= htmlspecialchars($mr_start) ?>" class="form-control form-control-sm d-inline-block w-auto">
                            <input type="date" name="mr_end" value="<?= htmlspecialchars($mr_end) ?>" class="form-control form-control-sm d-inline-block w-auto">
                            <input type="hidden" name="start_date" value="<?= htmlspecialchars($global_start) ?>">
                            <input type="hidden" name="end_date" value="<?= htmlspecialchars($global_end) ?>">
                            <button type="submit" class="btn btn-sm btn-outline-primary">Filter</button>
                        </form>
                        </h6></div>
                        <div class="card-body">
                            <table class="table table-sm table-striped">
                                <thead><tr><th>Product</th><th>Reviews</th></tr></thead>
                                <tbody>
                                <?php while($row = mysqli_fetch_assoc($most_reviewed)): ?>
                                    <tr><td><?= htmlspecialchars($row['name']) ?></td><td><?= $row['reviews'] ?></td></tr>
                                <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-light py-2"><h6 class="mb-0">Top Categories</h6></div>
                        <div class="card-body">
                            <table class="table table-sm table-striped">
                                <thead><tr><th>Category</th><th>Products</th></tr></thead>
                                <tbody>
                                <?php while($row = mysqli_fetch_assoc($top_categories)): ?>
                                    <tr><td><?= htmlspecialchars($row['name']) ?></td><td><?= $row['count'] ?></td></tr>
                                <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-light py-2"><h6 class="mb-0">Top Brands</h6></div>
                        <div class="card-body">
                            <table class="table table-sm table-striped">
                                <thead><tr><th>Brand</th><th>Products</th></tr></thead>
                                <tbody>
                                <?php while($row = mysqli_fetch_assoc($top_brands)): ?>
                                    <tr><td><?= htmlspecialchars($row['name']) ?></td><td><?= $row['count'] ?></td></tr>
                                <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Print Functions
function printReport(reportType) {
    console.log('Printing report:', reportType);
    
    // Get current date range
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    
    // Open print page in new window
    const printWindow = window.open(`print-report.php?type=${reportType}&start=${startDate}&end=${endDate}`, '_blank');
    
    if (printWindow) {
        printWindow.onload = function() {
            printWindow.print();
        };
    } else {
        alert('Please allow popups to print reports');
    }
}

function printAllReports() {
    console.log('Printing all reports');
    
    // Get current date range
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    
    // Open print page in new window
    const printWindow = window.open(`print-report.php?type=all&start=${startDate}&end=${endDate}`, '_blank');
    
    if (printWindow) {
        printWindow.onload = function() {
            printWindow.print();
        };
    } else {
        alert('Please allow popups to print reports');
    }
}

function getReportTitle(reportType) {
    const titles = {
        'overview': 'Overview Report',
        'sales': 'Sales & Revenue Report',
        'inventory': 'Inventory Report',
        'customers': 'Customer Report',
        'products': 'Products Report'
    };
    return titles[reportType] || 'Report';
}

// Alternative simple print function
function simplePrint() {
    console.log('Simple print triggered');
    window.print();
}

// Users Pie Chart
new Chart(document.getElementById('usersPieChart').getContext('2d'), {
    type: 'pie',
    data: {
        labels: ['Registered Users', 'Guest Orders'],
        datasets: [{
            data: [<?= $registered_users ?>, <?= $guest_orders ?>],
            backgroundColor: ['#4e73df', '#e74a3b']
        }]
    },
    options: {responsive: true, plugins: {legend: {position: 'bottom'}}}
});
// Sales & Revenue Chart
new Chart(document.getElementById('salesRevenueChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($sales_labels) ?>,
        datasets: [
            {label: 'Orders', data: <?= json_encode($sales_data) ?>, backgroundColor: 'rgba(54, 162, 235, 0.7)', borderRadius: 5},
            {label: 'Revenue (RWF)', data: <?= json_encode($revenue_data) ?>, backgroundColor: 'rgba(255, 206, 86, 0.7)', borderRadius: 5}
        ]
    },
    options: {responsive: true, plugins: {legend: {position: 'bottom'}}}
});
// Orders by Status (Overview)
new Chart(document.getElementById('ordersStatusChart').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: <?= json_encode($order_statuses) ?>,
        datasets: [{data: <?= json_encode($orders_by_status) ?>, backgroundColor: ['#f6c23e','#36b9cc','#858796','#1cc88a','#e74a3b']}]
    },
    options: {responsive: true, plugins: {legend: {position: 'bottom'}}}
});
// Orders by Status (Sales Tab)
new Chart(document.getElementById('ordersStatusChart2').getContext('2d'), {
    type: 'doughnut',
    data: {
        labels: <?= json_encode($order_statuses) ?>,
        datasets: [{data: <?= json_encode($orders_by_status) ?>, backgroundColor: ['#f6c23e','#36b9cc','#858796','#1cc88a','#e74a3b']}]
    },
    options: {responsive: true, plugins: {legend: {position: 'bottom'}}}
});
// Orders by Payment Method
new Chart(document.getElementById('ordersPaymentChart').getContext('2d'), {
    type: 'pie',
    data: {
        labels: <?= json_encode($payment_methods) ?>,
        datasets: [{data: <?= json_encode($payment_counts) ?>, backgroundColor: ['#4e73df','#1cc88a','#36b9cc','#f6c23e','#e74a3b','#858796']}]
    },
    options: {responsive: true, plugins: {legend: {position: 'bottom'}}}
});
// Customer Growth
new Chart(document.getElementById('customerGrowthChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: <?= json_encode($customer_labels) ?>,
        datasets: [{label: 'New Customers', data: <?= json_encode($customer_data) ?>, borderColor: '#36b9cc', backgroundColor: 'rgba(54, 185, 204, 0.2)', fill: true, tension: 0.4}]
    },
    options: {responsive: true, plugins: {legend: {position: 'bottom'}}}
});
</script>