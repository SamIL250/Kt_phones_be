<?php
    $user_info = array();

    // Get user information
    $get_user_info = mysqli_prepare(
        $conn,
        "SELECT * FROM users 
        LEFT JOIN addresses ON addresses.user_id = users.user_id AND addresses.is_guest = 0
        WHERE users.user_id = ? AND users.is_admin != 1"
    );
    mysqli_stmt_bind_param($get_user_info, "i", $customer_id);
    mysqli_stmt_execute($get_user_info);
    $result = mysqli_stmt_get_result($get_user_info);
    $user_data = mysqli_fetch_assoc($result);

    // Get user's first and last name initials
    $first_name = $user_data['first_name'] ?? '';
    $last_name = $user_data['last_name'] ?? '';
    $first_initial = !empty($first_name) ? strtoupper(substr($first_name, 0, 1)) : '';
    $last_initial = !empty($last_name) ? strtoupper(substr($last_name, 0, 1)) : '';
    $initials = $first_initial . $last_initial;

    // Get account creation date
    $created_at = $user_data['created_at'] ?? '';
    $account_date = !empty($created_at) ? date('F Y', strtotime($created_at)) : '';

    // Get user's email
    $user_email = $user_data['email'] ?? $customer_email;
?>

<div class="bg-white p-6 mb-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-6">
            <div class="relative">
                <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                    <?php echo htmlspecialchars($initials); ?>
                </div>
                <button class="absolute -bottom-2 -right-2 w-8 h-8 bg-blue-600 rounded-full text-white hover:bg-blue-700 transition-colors">
                    <i class="fas fa-camera text-xs"></i>
                </button>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-600"><?php echo htmlspecialchars($user_email); ?></h1>
                <p class="text-gray-600">Customer since <?php echo htmlspecialchars($account_date); ?></p>
                <div class="flex items-center gap-4 mt-2">
                    <span class="inline-flex items-center gap-1 text-sm text-yellow-600">
                        <i class="fas fa-star"></i>
                        <span class="font-medium">Gold Member</span>
                    </span>
                    <span class="text-sm text-gray-500">•</span>
                    <span class="text-sm text-green-600 font-medium">Verified Account</span>
                </div>
            </div>
        </div>
        <button onclick="openEditPersonalInfoModal()" class="bg-blue-600 text-white px-4 py-2 hover:bg-blue-700 transition-colors">
            <i class="fas fa-edit mr-2"></i>
            Edit Profile
        </button>
    </div>
</div>