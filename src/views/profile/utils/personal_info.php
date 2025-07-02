<?php
// Fetch user data
$user_query = mysqli_prepare(
    $conn,
    "SELECT * FROM users WHERE user_id = ? AND is_admin != 1"
);
mysqli_stmt_bind_param($user_query, "i", $customer_id);
mysqli_stmt_execute($user_query);
$user_result = mysqli_stmt_get_result($user_query);
$user_data = mysqli_fetch_assoc($user_result);

// Format date of birth if it exists
$formatted_dob = $user_data['date_of_birth'] ? date('Y-m-d', strtotime($user_data['date_of_birth'])) : '';
?>

<div id="personal-info" class="bg-white border border-gray-200 p-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-600">Personal Information</h2>
        <button onclick="openEditPersonalInfoModal()" class="text-blue-600 hover:text-blue-700 font-medium">
            <i class="fas fa-edit mr-1"></i>
            Edit
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
            <p class="text-gray-600"><?php echo htmlspecialchars($user_data['first_name'] ?? 'Not set'); ?></p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
            <p class="text-gray-600"><?php echo htmlspecialchars($user_data['last_name'] ?? 'Not set'); ?></p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
            <p class="text-gray-600"><?php echo htmlspecialchars($user_data['email'] ?? 'Not set'); ?></p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
            <p class="text-gray-600"><?php echo htmlspecialchars($user_data['phone_number'] ?? 'Not set'); ?></p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
            <p class="text-gray-600"><?php echo $formatted_dob ? date('F d, Y', strtotime($formatted_dob)) : 'Not set'; ?></p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Account Status</label>
            <p class="text-gray-600"><?php echo $user_data['is_active'] ? 'Active' : 'Inactive'; ?></p>
        </div>
    </div>
</div>

<!-- Edit Personal Info Modal -->
<div id="personalInfoModal" class="fixed inset-0 hidden items-center justify-center" style="z-index: 55555;">
    <div class="bg-white border border-gray-300  shadow-sm p-6 w-full max-w-2xl mx-4 transform transition-all">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-800">Edit Personal Information</h3>
            <button onclick="closePersonalInfoModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="personalInfoForm" action="./src/services/profile/update.php" method="POST">
            <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                    <input type="text" name="first_name" value="<?php echo htmlspecialchars($user_data['first_name']); ?>" class="w-full border border-gray-200  px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                    <input type="text" name="last_name" value="<?php echo htmlspecialchars($user_data['last_name']); ?>" class="w-full border border-gray-200  px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input type="tel" name="phone_number" value="<?php echo htmlspecialchars($user_data['phone_number']); ?>" class="w-full border border-gray-200  px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                    <input type="date" name="date_of_birth" value="<?php echo $formatted_dob; ?>" class="w-full border border-gray-200  px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                </div>
            </div>
            <div class="mt-5 flex justify-end gap-3">
                <button type="button" onclick="closePersonalInfoModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white  hover:bg-blue-700 transition-colors">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditPersonalInfoModal() {
    document.getElementById('personalInfoModal').classList.remove('hidden');
    document.getElementById('personalInfoModal').classList.add('flex');
}

function closePersonalInfoModal() {
    document.getElementById('personalInfoModal').classList.add('hidden');
    document.getElementById('personalInfoModal').classList.remove('flex');
}
</script>