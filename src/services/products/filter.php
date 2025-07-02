<?php
header('Content-Type: application/json');
include '../../../config/config.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$limit = isset($_GET['limit']) && is_numeric($_GET['limit']) && $_GET['limit'] > 0 ? min((int)$_GET['limit'], 20) : 10;

if ($search === '') {
    echo json_encode([
        'success' => true,
        'results' => [],
        'total' => 0
    ]);
    exit;
}

try {
    $search_term = '%' . $search . '%';
    $sql = "SELECT 
        p.product_id,
        p.name AS product_name,
        p.description,
        p.sku,
        p.base_price,
        p.discount_price,
        c.name AS category_name,
        b.name AS brand_name,
        (
            SELECT pi.image_url 
            FROM product_images pi 
            WHERE pi.product_id = p.product_id AND pi.is_primary = 1 
            ORDER BY pi.display_order ASC LIMIT 1
        ) AS primary_image,
        (
            SELECT ROUND(AVG(pr.rating),1) FROM product_reviews pr WHERE pr.product_id = p.product_id
        ) AS avg_rating,
        (
            SELECT COUNT(*) FROM product_reviews pr WHERE pr.product_id = p.product_id
        ) AS num_reviews
    FROM products p
    LEFT JOIN brands b ON p.brand_id = b.brand_id
    LEFT JOIN categories c ON p.category_id = c.category_id
    WHERE (
        p.name LIKE ? OR
        p.description LIKE ? OR
        p.sku LIKE ? OR
        b.name LIKE ? OR
        c.name LIKE ?
    )
    AND p.is_active = 1 AND p.published = 'true'
    ORDER BY p.created_at DESC
    LIMIT ?";

    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        throw new Exception('SQL error: ' . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt, 'sssssi', $search_term, $search_term, $search_term, $search_term, $search_term, $limit);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $results = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $results[] = [
            'product_id' => $row['product_id'],
            'product_name' => $row['product_name'],
            'description' => $row['description'],
            'sku' => $row['sku'],
            'base_price' => $row['base_price'],
            'discount_price' => $row['discount_price'],
            'category_name' => $row['category_name'],
            'brand_name' => $row['brand_name'],
            'primary_image' => $row['primary_image'] ?: 'src/assets/images/placeholder.jpg',
            'avg_rating' => $row['avg_rating'] !== null ? (float)$row['avg_rating'] : null,
            'num_reviews' => (int)$row['num_reviews']
        ];
    }

    // Get total count
    $count_sql = "SELECT COUNT(*) as total
        FROM products p
        LEFT JOIN brands b ON p.brand_id = b.brand_id
        LEFT JOIN categories c ON p.category_id = c.category_id
        WHERE (
            p.name LIKE ? OR
            p.description LIKE ? OR
            p.sku LIKE ? OR
            b.name LIKE ? OR
            c.name LIKE ?
        )
        AND p.is_active = 1 AND p.published = 'true'";
    $count_stmt = mysqli_prepare($conn, $count_sql);
    if (!$count_stmt) {
        throw new Exception('SQL error: ' . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($count_stmt, 'sssss', $search_term, $search_term, $search_term, $search_term, $search_term);
    mysqli_stmt_execute($count_stmt);
    $count_result = mysqli_stmt_get_result($count_stmt);
    $total = 0;
    if ($row = mysqli_fetch_assoc($count_result)) {
        $total = (int)$row['total'];
    }

    echo json_encode([
        'success' => true,
        'results' => $results,
        'total' => $total
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
