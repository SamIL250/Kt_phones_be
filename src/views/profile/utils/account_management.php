<?php
// Fetch user data for verification
$user_query = mysqli_prepare(
    $conn,
    "SELECT * FROM users WHERE user_id = ? AND is_admin != 1"
);
mysqli_stmt_bind_param($user_query, "i", $customer_id);
mysqli_stmt_execute($user_query);
$user_result = mysqli_stmt_get_result($user_query);
$user_data = mysqli_fetch_assoc($user_result);
?>

<div id="account-management" class="bg-white border border-gray-200 p-6">
    <h2 class="text-xl font-semibold text-gray-600 mb-6">Account Management</h2>

    <!-- Change Password Section -->
    <div class="mb-8">
        <h3 class="text-lg font-medium text-gray-700 mb-4">Change Password</h3>
        <form id="changePasswordForm" onsubmit="handlePasswordChange(event)" class="space-y-4 max-w-md">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                <input type="password" name="current_password" required
                    class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                <input type="password" name="new_password" required minlength="8"
                    class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                <input type="password" name="confirm_password" required minlength="8"
                    class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit" 
                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                Update Password
            </button>
        </form>
    </div>

    <!-- Delete Account Section -->
    <div class="border-t pt-6">
        <h3 class="text-lg font-medium text-red-600 mb-4">Delete Account</h3>
        <p class="text-gray-600 mb-4">
            Once you delete your account, there is no going back. Please be certain.
        </p>
        <button onclick="openDeleteAccountModal()" 
            class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors">
            Delete Account
        </button>
    </div>
</div>

<!-- Delete Account Confirmation Modal -->
<div id="deleteAccountModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-red-600">Delete Account</h3>
            <button onclick="closeDeleteAccountModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <p class="text-gray-600 mb-6">
            Are you sure you want to delete your account? This action cannot be undone.
        </p>
        <form id="deleteAccountForm" onsubmit="handleAccountDeletion(event)" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Enter your password to confirm</label>
                <input type="password" name="password" required
                    class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeDeleteAccountModal()" 
                    class="px-4 py-2 text-gray-600 hover:text-gray-800">
                    Cancel
                </button>
                <button type="submit" 
                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                    Delete Account
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function handlePasswordChange(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);

    if (formData.get('new_password') !== formData.get('confirm_password')) {
        alert('New passwords do not match');
        return;
    }

    fetch('/services/account/change-password.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Password updated successfully');
            form.reset();
        } else {
            alert(data.message || 'Failed to update password');
        }
    });
}

function openDeleteAccountModal() {
    document.getElementById('deleteAccountModal').classList.remove('hidden');
    document.getElementById('deleteAccountModal').classList.add('flex');
}

function closeDeleteAccountModal() {
    document.getElementById('deleteAccountModal').classList.add('hidden');
    document.getElementById('deleteAccountModal').classList.remove('flex');
    document.getElementById('deleteAccountForm').reset();
}

function handleAccountDeletion(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);

    if (confirm('Are you absolutely sure you want to delete your account? This action cannot be undone.')) {
        fetch('/services/account/delete.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '/';
            } else {
                alert(data.message || 'Failed to delete account');
            }
        });
    }
}
</script> 