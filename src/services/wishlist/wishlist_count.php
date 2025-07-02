<?php
header('Content-Type: application/json');
session_start();
include '../../../config/config.php';

$customer_id = isset($_GET['customer_id']) ? $_GET['customer_id'] : null;
$is_logged_in = isset($_GET['is_logged_in']) ? filter_var($_GET['is_logged_in'], FILTER_VALIDATE_BOOLEAN) : false;

$count = 0;
if ($is_logged_in && $customer_id) {
    $wishlist_query_str = "SELECT COUNT(DISTINCT wi.product_id) as total FROM wishlists w JOIN wishlist_items wi ON w.wishlist_id = wi.wishlist_id WHERE w.user_id = ?";
    $wishlist_query = mysqli_prepare($conn, $wishlist_query_str);
    if ($wishlist_query) {
        mysqli_stmt_bind_param($wishlist_query, "s", $customer_id);
        mysqli_stmt_execute($wishlist_query);
        $result = mysqli_stmt_get_result($wishlist_query);
        if ($row = mysqli_fetch_assoc($result)) {
            $count = (int)($row['total'] ?? 0);
        }
        mysqli_stmt_close($wishlist_query);
    }
}
echo json_encode(['count' => $count]);
