<?php
session_start();
include_once('../../../config/config_pdo.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'You must be logged in to post a review.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $user_id = $_SESSION['user_id'];
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT, ["options" => ["min_range" => 1, "max_range" => 5]]);
    $review_text = filter_input(INPUT_POST, 'review_text', FILTER_SANITIZE_SPECIAL_CHARS);

    if (!$product_id || !$rating || empty($review_text)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid input. Please check your data and try again.']);
        exit;
    }

    try {
        $conn->beginTransaction();

        // Check if the user has already reviewed this product
        $stmt_check = $conn->prepare("SELECT COUNT(*) FROM reviews WHERE user_id = :user_id AND product_id = :product_id");
        $stmt_check->execute([':user_id' => $user_id, ':product_id' => $product_id]);
        if ($stmt_check->fetchColumn() > 0) {
            http_response_code(409); // Conflict
            echo json_encode(['success' => false, 'message' => 'You have already submitted a review for this product.']);
            $conn->rollBack();
            exit;
        }

        // Insert the new review
        $stmt_insert = $conn->prepare("INSERT INTO reviews (product_id, user_id, rating, review_text) VALUES (:product_id, :user_id, :rating, :review_text)");
        $stmt_insert->execute([
            ':product_id' => $product_id,
            ':user_id' => $user_id,
            ':rating' => $rating,
            ':review_text' => $review_text
        ]);

        // Update the product's average rating
        $stmt_update_avg = $conn->prepare("
            UPDATE products p
            SET average_rating = (SELECT AVG(r.rating) FROM reviews r WHERE r.product_id = :product_id)
            WHERE p.product_id = :product_id
        ");
        $stmt_update_avg->execute([':product_id' => $product_id]);

        $conn->commit();

        echo json_encode(['success' => true, 'message' => 'Thank you! Your review has been submitted successfully.']);
    } catch (PDOException $e) {
        $conn->rollBack();
        http_response_code(500);
        // For debugging, you might log $e->getMessage(), but don't expose it to the user.
        echo json_encode(['success' => false, 'message' => 'A server error occurred. Please try again later.']);
    }
} else {
    // Method not allowed
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
}
