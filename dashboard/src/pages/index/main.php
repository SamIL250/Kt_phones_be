<?php
require_once 'config/config.php';

// Function to handle database errors
function handleDBError($query)
{
    global $conn;
    error_log("Query failed: " . $query . " - Error: " . mysqli_error($conn));
    return false;
}

// 1. Monthly Sales Data
$sales_labels = [];
$sales_data = [];
for ($i = 1; $i <= 12; $i++) {
    $sales_labels[] = date('M', mktime(0, 0, 0, $i, 10));
    $result = mysqli_query($conn, "SELECT SUM(total_amount) as total FROM orders WHERE MONTH(order_date) = $i AND YEAR(order_date) = YEAR(CURDATE())") or handleDBError("Monthly sales query failed");
    $row = mysqli_fetch_assoc($result);
    $sales_data[] = (float)($row['total'] ?? 0);
}

// 2. Monthly Orders Data
$orders_labels = $sales_labels;
$orders_data = [];
for ($i = 1; $i <= 12; $i++) {
    $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM orders WHERE MONTH(order_date) = $i AND YEAR(order_date) = YEAR(CURDATE())");
    $row = mysqli_fetch_assoc($result);
    $orders_data[] = (int)($row['count'] ?? 0);
}

// 3. User Types (Registered vs Guest)
$user_count = mysqli_query($conn, "SELECT COUNT(*) as count FROM users");
$user_row = mysqli_fetch_assoc($user_count);
$registered_users = (int)($user_row['count'] ?? 0);
$guest_orders = mysqli_query($conn, "SELECT COUNT(*) as count FROM orders WHERE is_guest_order = 1");
$guest_row = mysqli_fetch_assoc($guest_orders);
$guest_users = (int)($guest_row['count'] ?? 0);
$users_labels = ['Registered Users', 'Guest Users'];
$users_data = [$registered_users, $guest_users];

// 4. Products by Category
$category_labels = [];
$category_data = [];
$cat_query = mysqli_query($conn, "SELECT c.name, COUNT(p.product_id) as count FROM categories c LEFT JOIN products p ON c.category_id = p.category_id GROUP BY c.category_id");
while ($row = mysqli_fetch_assoc($cat_query)) {
    $category_labels[] = $row['name'];
    $category_data[] = (int)$row['count'];
}

// 5. Monthly Revenue (Paid Orders Only)
$revenue_labels = $sales_labels;
$revenue_data = [];
for ($i = 1; $i <= 12; $i++) {
    $result = mysqli_query($conn, "SELECT SUM(total_amount) as total FROM orders WHERE payment_status = 'paid' AND MONTH(order_date) = $i AND YEAR(order_date) = YEAR(CURDATE())");
    $row = mysqli_fetch_assoc($result);
    $revenue_data[] = (float)($row['total'] ?? 0);
}

// Calculate total revenue
$total_revenue = array_sum($revenue_data);
$total_orders = array_sum($orders_data);
$total_products = array_sum($category_data);

// Recent Orders Query
$recent_orders_query = "SELECT o.order_id, o.total_amount, o.order_date, o.status, 
    COALESCE(CONCAT(u.first_name, ' ', u.last_name), 'Guest') as customer_name,
    COUNT(oi.order_item_id) as items_count
    FROM orders o 
    LEFT JOIN users u ON o.user_id = u.user_id 
    LEFT JOIN order_items oi ON o.order_id = oi.order_id
    GROUP BY o.order_id
    ORDER BY o.order_date DESC 
    LIMIT 5";
$recent_orders = mysqli_query($conn, $recent_orders_query);

// Recent Products Query
$recent_products_query = "SELECT p.product_id, p.name, p.base_price, p.stock_quantity, 
    b.name as brand_name, c.name as category_name,
    (SELECT image_url FROM product_images WHERE product_id = p.product_id AND is_primary = 1 LIMIT 1) as image_url
    FROM products p
    LEFT JOIN brands b ON p.brand_id = b.brand_id
    LEFT JOIN categories c ON p.category_id = c.category_id
    ORDER BY p.created_at DESC
    LIMIT 5";
$recent_products = mysqli_query($conn, $recent_products_query);
?>

<div class="">
    <!-- Dashboard Title Section -->
    <div class="mb-3">
        <div class="row g-3 align-items-center">
            <div class="col">
                <h2 class="mb-0 text-1100">Dashboard</h2>
                <p class="mb-0 text-700">Here's what's going on at your business right now</p>
            </div>
            <div class="col-auto">
                <div class="row g-2 align-items-center">
                    <div class="col-auto">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" id="today" type="radio" name="period" checked>
                            <label class="form-check-label mb-0" for="today">Today</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" id="week" type="radio" name="period">
                            <label class="form-check-label mb-0" for="week">Week</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" id="month" type="radio" name="period">
                            <label class="form-check-label mb-0" for="month">Month</label>
                        </div>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-phoenix-secondary px-3" type="button">
                            <span class="fa-solid fa-download me-2"></span>Download
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards Row -->
    <div class="row g-3 mb-3">
        <div class="col-sm-6 col-md-4">
            <div class="card overflow-hidden" style="min-width: 12rem">
                <div class="bg-holder bg-card" style="background-image:url(src/assets/img/icons/spot-illustrations/corner-1.png);"></div>
                <div class="card-body position-relative">
                    <h6>Total Revenue</h6>
                    <div class="display-4 fs-4 mb-2 fw-normal font-sans-serif text-warning" data-countup='{"endValue":58386,"decimalPlaces":0,"prefix":"RWF "}'><?php echo number_format($total_revenue); ?> RWF</div>
                    <a class="fw-semi-bold fs--1 text-nowrap" href="#!">See all<span class="fas fa-angle-right ms-1" data-fa-transform="down-1"></span></a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="card overflow-hidden" style="min-width: 12rem">
                <div class="bg-holder bg-card" style="background-image:url(src/assets/img/icons/spot-illustrations/corner-2.png);"></div>
                <div class="card-body position-relative">
                    <h6>Total Orders</h6>
                    <div class="display-4 fs-4 mb-2 fw-normal font-sans-serif text-info" data-countup='{"endValue":23497,"decimalPlaces":0,"prefix":""}'><?php echo number_format($total_orders); ?></div>
                    <a class="fw-semi-bold fs--1 text-nowrap" href="#!">All orders<span class="fas fa-angle-right ms-1" data-fa-transform="down-1"></span></a>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-4">
            <div class="card overflow-hidden" style="min-width: 12rem">
                <div class="bg-holder bg-card" style="background-image:url(src/assets/img/icons/spot-illustrations/corner-3.png);"></div>
                <div class="card-body position-relative">
                    <h6>Total Products</h6>
                    <div class="display-4 fs-4 mb-2 fw-normal font-sans-serif text-success" data-countup='{"endValue":120,"decimalPlaces":0,"prefix":""}'><?php echo number_format($total_products); ?></div>
                    <a class="fw-semi-bold fs--1 text-nowrap" href="#!">See products<span class="fas fa-angle-right ms-1" data-fa-transform="down-1"></span></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Grid -->
    <div class="row g-3 mb-3">
        <!-- Sales and Revenue -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0">Monthly Sales & Revenue</h6>
                </div>
                <div class="card-body">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Orders Trend -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0">Orders Trend</h6>
                </div>
                <div class="card-body">
                    <canvas id="ordersChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <!-- User Distribution -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0">User Distribution</h6>
                </div>
                <div class="card-body">
                    <canvas id="usersChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Products by Category -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0">Products by Category</h6>
                </div>
                <div class="card-body">
                    <canvas id="productsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Monthly Revenue -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0">Monthly Revenue</h6>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="row g-3 mb-3 mt-10">
        <!-- Recent Orders -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header border-bottom">
                    <div class="row flex-between-center">
                        <div class="col-auto">
                            <h6 class="mb-0">Recent Orders</h6>
                        </div>
                        <div class="col-auto d-flex">
                            <a class="btn btn-link btn-sm px-0 fw-medium" href="orders">View All<span class="fas fa-chevron-right ms-1 fs--2"></span></a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive scrollbar">
                        <table class="table table-sm table-striped fs--1 mb-0">
                            <thead class="bg-200">
                                <tr>
                                    <th class="text-900 white-space-nowrap">Order ID</th>
                                    <th class="text-900 white-space-nowrap">Customer</th>
                                    <th class="text-900 white-space-nowrap">Items</th>
                                    <th class="text-900 white-space-nowrap">Total</th>
                                    <th class="text-900 white-space-nowrap">Status</th>
                                    <th class="text-900 white-space-nowrap">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($order = mysqli_fetch_assoc($recent_orders)): ?>
                                    <tr>
                                        <td class="white-space-nowrap">
                                            <a class="fw-semi-bold" href="order-details?order=<?php echo $order['order_id']; ?>"><?php echo $order['order_id']; ?></a>
                                        </td>
                                        <td class="white-space-nowrap"><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                        <td class="white-space-nowrap"><?php echo $order['items_count']; ?></td>
                                        <td class="white-space-nowrap"><?php echo number_format($order['total_amount']); ?> RWF</td>
                                        <td>
                                            <span class="badge badge-phoenix badge-phoenix-<?php
                                                                                            switch ($order['status']) {
                                                                                                case 'pending':
                                                                                                    echo 'warning';
                                                                                                    break;
                                                                                                case 'processing':
                                                                                                    echo 'info';
                                                                                                    break;
                                                                                                case 'shipped':
                                                                                                    echo 'secondary';
                                                                                                    break;
                                                                                                case 'delivered':
                                                                                                    echo 'success';
                                                                                                    break;
                                                                                                case 'cancelled':
                                                                                                    echo 'danger';
                                                                                                    break;
                                                                                                default:
                                                                                                    echo 'primary';
                                                                                                    break;
                                                                                            }
                                                                                            ?>">
                                                <?php echo ucfirst($order['status']); ?>
                                            </span>
                                        </td>
                                        <td class="white-space-nowrap"><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Products -->
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header border-bottom">
                    <div class="row flex-between-center">
                        <div class="col-auto">
                            <h6 class="mb-0">Recently Added Products</h6>
                        </div>
                        <div class="col-auto d-flex">
                            <a class="btn btn-link btn-sm px-0 fw-medium" href="products">View All<span class="fas fa-chevron-right ms-1 fs--2"></span></a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive scrollbar">
                        <table class="table table-sm table-striped fs--1 mb-0">
                            <thead class="bg-200">
                                <tr>
                                    <th class="text-900">Product</th>
                                    <th class="text-900">Category</th>
                                    <th class="text-900">Brand</th>
                                    <th class="text-900">Price</th>
                                    <th class="text-900">Stock</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($product = mysqli_fetch_assoc($recent_products)): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center position-relative">
                                                <?php if ($product['image_url']): ?>
                                                    <img class="rounded-1 border border-200" src="<?php echo htmlspecialchars($product['image_url']); ?>" width="40" alt="" />
                                                <?php endif; ?>
                                                <div class="ms-2">
                                                    <a class="stretched-link" href="product?product=<?php echo $product['product_id']; ?>">
                                                        <h6 class="mb-0 fw-semi-bold text-900"><?php echo htmlspecialchars($product['name']); ?></h6>
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                        <td><?php echo htmlspecialchars($product['brand_name']); ?></td>
                                        <td><?php echo number_format($product['base_price']); ?> RWF</td>
                                        <td>
                                            <span class="badge badge-phoenix badge-phoenix-<?php echo $product['stock_quantity'] > 0 ? 'success' : 'danger'; ?>">
                                                <?php echo $product['stock_quantity']; ?> in stock
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Sales Chart
    const salesChartData = {
        labels: <?php echo json_encode($sales_labels); ?>,
        datasets: [{
            label: 'Sales (RWF)',
            data: <?php echo json_encode($sales_data); ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.7)',
            borderRadius: 5
        }]
    };
    new Chart(document.getElementById('salesChart').getContext('2d'), {
        type: 'bar',
        data: salesChartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true
                },
                title: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Orders Chart
    const ordersChartData = {
        labels: <?php echo json_encode($orders_labels); ?>,
        datasets: [{
            label: 'Orders',
            data: <?php echo json_encode($orders_data); ?>,
            borderColor: 'rgba(255, 99, 132, 0.7)',
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            fill: true,
            tension: 0.4
        }]
    };
    new Chart(document.getElementById('ordersChart').getContext('2d'), {
        type: 'line',
        data: ordersChartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true
                },
                title: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Users Chart
    const usersChartData = {
        labels: <?php echo json_encode($users_labels); ?>,
        datasets: [{
            label: 'User Types',
            data: <?php echo json_encode($users_data); ?>,
            backgroundColor: [
                'rgba(75, 192, 192, 0.7)',
                'rgba(255, 205, 86, 0.7)'
            ],
            borderWidth: 0
        }]
    };
    new Chart(document.getElementById('usersChart').getContext('2d'), {
        type: 'doughnut',
        data: usersChartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                },
                title: {
                    display: false
                }
            }
        }
    });

    // Products Chart
    const productsChartData = {
        labels: <?php echo json_encode($category_labels); ?>,
        datasets: [{
            label: 'Products by Category',
            data: <?php echo json_encode($category_data); ?>,
            backgroundColor: [
                'rgba(255, 159, 64, 0.7)',
                'rgba(153, 102, 255, 0.7)',
                'rgba(255, 205, 86, 0.7)',
                'rgba(54, 162, 235, 0.7)',
                'rgba(201, 203, 207, 0.7)',
                'rgba(100, 181, 246, 0.7)',
                'rgba(255, 138, 101, 0.7)'
            ],
            borderWidth: 0
        }]
    };
    new Chart(document.getElementById('productsChart').getContext('2d'), {
        type: 'pie',
        data: productsChartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                },
                title: {
                    display: false
                }
            }
        }
    });

    // Revenue Chart
    const revenueChartData = {
        labels: <?php echo json_encode($revenue_labels); ?>,
        datasets: [{
            label: 'Revenue (RWF)',
            data: <?php echo json_encode($revenue_data); ?>,
            backgroundColor: 'rgba(255, 206, 86, 0.7)',
            borderRadius: 5
        }]
    };
    new Chart(document.getElementById('revenueChart').getContext('2d'), {
        type: 'bar',
        data: revenueChartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true
                },
                title: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
</script>