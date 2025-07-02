<?php
$orders = array();

// Build the WHERE clause based on filters
$where_clause = "WHERE 1=1";
if ($status_filter != 'all') {
    $where_clause .= " AND o.status = '$status_filter'";
}
if ($search_query) {
    $search_term = mysqli_real_escape_string($conn, $search_query);
    $where_clause .= " AND (
        o.order_id LIKE '%$search_term%'
        OR u.first_name LIKE '%$search_term%'
        OR u.last_name LIKE '%$search_term%'
        OR u.email LIKE '%$search_term%'
        OR ga.first_name LIKE '%$search_term%'
        OR ga.last_name LIKE '%$search_term%'
        OR ga.email LIKE '%$search_term%'
    )";
}

// Get total records for pagination
$count_query = mysqli_query(
    $conn,
    "SELECT COUNT(*) as total
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.user_id
    LEFT JOIN addresses sa ON o.shipping_address_id = sa.address_id
    LEFT JOIN addresses ba ON o.billing_address_id = ba.address_id
    $where_clause"
);
$total_records = mysqli_fetch_assoc($count_query)['total'];
$total_pages = ceil($total_records / $items_per_page);

// Get orders with pagination
$get_orders = mysqli_query(
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
    $where_clause
    ORDER BY o.order_date DESC" .
        ($items_per_page < PHP_INT_MAX ? " LIMIT $offset, $items_per_page" : "")
);

foreach ($get_orders as $order) {
    $orders[] = $order;
}
?>

<table class="table fs-9 mb-0" data-list='{"valueNames":["order_id","customer","total","payment","status","date"]}'>
    <thead>
        <tr>
            <th class="white-space-nowrap fs-9 align-middle ps-0" style="max-width:20px; width:18px;">
                <div class="form-check mb-0 fs-8">
                    <input class="form-check-input" id="checkbox-bulk-orders-select" type="checkbox" data-bulk-select='{"body":"orders-table-body"}' />
                </div>
            </th>
            <th class="sort white-space-nowrap align-middle ps-4" scope="col" style="width:350px;" data-sort="customer">CUSTOMER</th>
            <th class="sort align-middle text-end ps-4" scope="col" style="width:150px;" data-sort="total">TOTAL</th>
            <th class="sort align-middle ps-4" scope="col" style="width:150px;" data-sort="payment">PAYMENT</th>
            <th class="sort align-middle ps-4" scope="col" style="width:150px;" data-sort="status">STATUS</th>
            <th class="sort align-middle ps-4" scope="col" style="width:150px;" data-sort="date">DATE</th>
            <th class="sort text-end align-middle pe-0 ps-4" scope="col"></th>
        </tr>
    </thead>
    <tbody class="list" id="orders-table-body">
        <?php
        foreach ($orders as $order) {
            if (!empty($order['user_id'])) {
                $customer_name = $order['user_first_name'] . ' ' . $order['user_last_name'];
                $customer_email = $order['user_email'];
            } else {
                $customer_name = $order['shipping_first_name'] . ' ' . $order['shipping_last_name'];
                $customer_email = $order['shipping_email'];
            }

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
            <tr class="hover-actions-trigger btn-reveal-trigger position-static">
                <td class="fs-9 align-middle">
                    <div class="form-check mb-0 fs-8">
                        <input class="form-check-input" type="checkbox" />
                    </div>
                </td>
                <td class="customer align-middle white-space-nowrap ps-4">
                    <div class="d-flex align-items-center">
                        <div class="d-flex flex-column">
                            <h6 class="mb-0 fw-semibold text-1000"><?= $customer_name ?></h6>
                            <p class="fs-9 mb-0"><?= $customer_email ?></p>
                        </div>
                    </div>
                </td>
                <td class="total align-middle text-end fw-semibold ps-4">
                    <?= number_format($order['total_amount'], 2) ?> RWF
                </td>
                <td class="payment align-middle ps-4">
                    <div class="d-flex align-items-center">
                        <span class="badge badge-phoenix fs-9 badge-phoenix-<?= $order['payment_status'] == 'paid' ? 'success' : 'warning' ?>">
                            <?= ucfirst($order['payment_status']) ?>
                        </span>
                    </div>
                </td>
                <td class="status align-middle ps-4">
                    <span class="badge badge-phoenix fs-9 badge-phoenix-<?= $status_class ?>">
                        <?= ucfirst($order['status']) ?>
                    </span>
                </td>
                <td class="date align-middle ps-4">
                    <?= date('Y-m-d H:i:s', strtotime($order['order_date'])) ?>
                </td>
                <td class="align-middle white-space-nowrap text-end pe-0 ps-4">
                    <div class="btn-reveal-trigger position-static">
                        <button class="btn btn-sm dropdown-toggle dropdown-caret-none transition-none btn-reveal fs-10" type="button" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent">
                            <span class="fas fa-ellipsis-h fs-10"></span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end py-2">
                            <a class="dropdown-item" href="order-details?order=<?= $order['order_id'] ?>">View</a>
                            <a class="dropdown-item" href="order-edit?order=<?= $order['order_id'] ?>">Update</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="./src/services/orders/cancel_order.php?order=<?= $order['order_id'] ?>" onclick="return confirm('Are you sure you want to cancel this order?')">Cancel Order</a>
                        </div>
                    </div>
                </td>
            </tr>
        <?php
        }
        ?>
    </tbody>
</table>

<?php
if (empty($orders)) {
    echo "<div class='alert alert-info text-center p-2 rounded-2 mt-2 mb-2'>No orders found</div>";
}
?>