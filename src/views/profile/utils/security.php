<?php
// Fetch user data for last password change
$user_query = mysqli_prepare(
    $conn,
    "SELECT updated_at FROM users WHERE user_id = ? AND is_admin != 1"
);
mysqli_stmt_bind_param($user_query, "i", $customer_id);
mysqli_stmt_execute($user_query);
$user_result = mysqli_stmt_get_result($user_query);
$user_data = mysqli_fetch_assoc($user_result);

// Format last password change date
$last_password_change = $user_data['updated_at'] ? date('F d, Y', strtotime($user_data['updated_at'])) : 'Never';
?>

<div id="security" class="bg-white shadow-sm p-6">
    <h2 class="text-xl font-semibold text-gray-600 mb-6">Security Settings</h2>

    <div class="space-y-6">
        <div class="flex items-center justify-between py-4 border-b border-gray-200">
            <div>
                <h3 class="font-medium text-gray-600">Password</h3>
                <p class="text-sm text-gray-600">Last changed <?php echo $last_password_change; ?></p>
            </div>
            <button onclick="openChangePasswordModal()" class="text-blue-600 hover:text-blue-700 font-medium">Change Password</button>
        </div>

        <!-- <div class="flex items-center justify-between py-4 border-b border-gray-200">
            <div>
                <h3 class="font-medium text-gray-600">Login Activity</h3>
                <p class="text-sm text-gray-600">Monitor recent login attempts</p>
            </div>
            <button class="text-blue-600 hover:text-blue-700 font-medium">View Activity</button>
        </div> -->

        <div class="flex items-center justify-between py-4">
            <div>
                <h3 class="font-medium text-gray-600">Delete Account</h3>
                <p class="text-sm text-gray-600">Permanently delete your account and data</p>
            </div>
            <button onclick="openDeleteAccountModal()" class="text-red-600 hover:text-red-700 font-medium">Delete Account</button>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div id="changePasswordModal" class="fixed inset-0 hidden items-center justify-center" style="z-index: 55555;">
    <div class="bg-white border border-gray-300  shadow-sm p-6 w-full max-w-md mx-4 transform transition-all">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-800">Change Password</h3>
            <button onclick="closeChangePasswordModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="changePasswordForm" action="./src/services/profile/change_password.php" method="POST">
            <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                    <input type="password" name="current_password" class="w-full border border-gray-200  px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                    <input type="password" name="new_password" class="w-full border border-gray-200  px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="w-full border border-gray-200  px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" required>
                </div>
            </div>
            <div class="mt-5 flex justify-end gap-3">
                <button type="button" onclick="closeChangePasswordModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white  hover:bg-blue-700 transition-colors">
                    Change Password
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Account Modal -->
<div id="deleteAccountModal" class="fixed inset-0 hidden items-center justify-center" style="z-index: 55555;">
    <div class="bg-white border border-gray-300  shadow-sm p-6 w-full max-w-md mx-4 transform transition-all">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-800">Delete Account</h3>
            <button onclick="closeDeleteAccountModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="mb-4">
            <p class="text-gray-600">Are you sure you want to delete your account? This action cannot be undone.</p>
        </div>
        <form id="deleteAccountForm" action="./src/services/profile/delete_account.php" method="POST">
            <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Enter your password to confirm</label>
                    <input type="password" name="password" class="w-full border border-gray-200  px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" required>
                </div>
            </div>
            <div class="mt-5 flex justify-end gap-3">
                <button type="button" onclick="closeDeleteAccountModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white  hover:bg-red-700 transition-colors">
                    Delete Account
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openChangePasswordModal() {
    document.getElementById('changePasswordModal').classList.remove('hidden');
    document.getElementById('changePasswordModal').classList.add('flex');
}

function closeChangePasswordModal() {
    document.getElementById('changePasswordModal').classList.add('hidden');
    document.getElementById('changePasswordModal').classList.remove('flex');
}

function openDeleteAccountModal() {
    document.getElementById('deleteAccountModal').classList.remove('hidden');
    document.getElementById('deleteAccountModal').classList.add('flex');
}

function closeDeleteAccountModal() {
    document.getElementById('deleteAccountModal').classList.add('hidden');
    document.getElementById('deleteAccountModal').classList.remove('flex');
}
</script>