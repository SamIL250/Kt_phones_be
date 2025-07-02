<?php
// Get filter parameters
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// Get counts for each status
$count_query = mysqli_query(
    $conn,
    "SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as processing,
        SUM(CASE WHEN status = 'shipped' THEN 1 ELSE 0 END) as shipped,
        SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered,
        SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
    FROM orders"
);
$counts = mysqli_fetch_assoc($count_query);

// Items per page
$items_per_page = isset($_GET['show']) ? (int)$_GET['show'] : 10;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $items_per_page;

// View all option
if (isset($_GET['show']) && $_GET['show'] === 'all') {
    $items_per_page = PHP_INT_MAX;
    $current_page = 1;
    $offset = 0;
}
?>

<div class="">
    <nav class="mb-2" aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="/">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="/orders.php">Orders</a></li>
            <li class="breadcrumb-item active">Orders</li>
        </ol>
    </nav>
    <div class="mb-9">
        <div class="row g-3 mb-4">
            <div class="col-auto">
                <h2 class="mb-0">Orders</h2>
            </div>
        </div>
        <ul class="nav nav-links mb-3 mb-lg-2 mx-n3">
            <li class="nav-item">
                <a class="nav-link <?= $status_filter === 'all' ? 'active' : '' ?>" href="?status=all">
                    <span>All</span><span class="text-body-quaternary fw-bold">(<?= $counts['total'] ?>)</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $status_filter === 'pending' ? 'active' : '' ?>" href="?status=pending">
                    <span>Pending</span><span class="text-body-quaternary fw-bold">(<?= $counts['pending'] ?>)</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $status_filter === 'processing' ? 'active' : '' ?>" href="?status=processing">
                    <span>Processing</span><span class="text-body-quaternary fw-bold">(<?= $counts['processing'] ?>)</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $status_filter === 'shipped' ? 'active' : '' ?>" href="?status=shipped">
                    <span>Shipped</span><span class="text-body-quaternary fw-bold">(<?= $counts['shipped'] ?>)</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $status_filter === 'delivered' ? 'active' : '' ?>" href="?status=delivered">
                    <span>Delivered</span><span class="text-body-quaternary fw-bold">(<?= $counts['delivered'] ?>)</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $status_filter === 'cancelled' ? 'active' : '' ?>" href="?status=cancelled">
                    <span>Cancelled</span><span class="text-body-quaternary fw-bold">(<?= $counts['cancelled'] ?>)</span>
                </a>
            </li>
        </ul>
        <div id="products" data-list='{"valueNames":["product","price","category","tags","vendor","time"],"page":10,"pagination":true}'>
            <div class="mb-4">
                <div class="d-flex flex-wrap gap-3">
                    <div class="search-box">
                        <form class="position-relative"><input class="form-control search-input search" type="search" placeholder="Search order" aria-label="Search" />
                            <span class="fas fa-search search-box-icon"></span>
                        </form>
                    </div>
                    <div class="scrollbar overflow-hidden-y">
                        <div class="btn-group position-static" role="group">
                        </div>
                    </div>
                    <div class="ms-xxl-auto"><button class="btn btn-link text-body me-4 px-0"><span class="fa-solid fa-file-export fs-9 me-2"></span>Export</button><button onclick="window.location.replace('products-add')" class="btn btn-primary" id="addBtn"><span class="fas fa-plus me-2"></span>Add product</button></div>
                </div>
            </div>
            <div class="mx-n4 px-4 mx-lg-n6 px-lg-6 bg-body-emphasis border-top border-bottom border-translucent position-relative top-1">
                <div class="table-responsive scrollbar mx-n1 px-1">
                    <?php
                        include './src/pages/orders/utils/table.php';
                    ?>
                </div>
                <div class="row align-items-center justify-content-between py-2 pe-0 fs-9">
                    <div class="col-auto d-flex">
                        <p class="mb-0 d-none d-sm-block me-3 fw-semibold text-body" data-list-info="data-list-info"></p><a class="fw-semibold" href="#!" data-list-view="*">View all<span class="fas fa-angle-right ms-1" data-fa-transform="down-1"></span></a><a class="fw-semibold d-none" href="#!" data-list-view="less">View Less<span class="fas fa-angle-right ms-1" data-fa-transform="down-1"></span></a>
                    </div>
                    <div class="col-auto d-flex"><button class="page-link" data-list-pagination="prev"><span class="fas fa-chevron-left"></span></button>
                        <ul class="mb-0 pagination"></ul><button class="page-link pe-0" data-list-pagination="next"><span class="fas fa-chevron-right"></span></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>