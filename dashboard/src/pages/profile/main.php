<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="index">Dashboard</a></li>
        <li class="breadcrumb-item active">Profile</li>
    </ol>
</nav>

<div class="mb-9">
    <div class="row g-3 mb-4">
        <div class="col-auto">
            <h2 class="mb-0">Admin Profile</h2>
        </div>
    </div>

    <?php if (isset($_SESSION['notification'])): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['notification']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['notification']); ?>
    <?php endif; ?>

    <?php
    // Get admin information from database
    $admin_id = $admin_id ?? $_SESSION['user_id'] ?? null;
    $admin_data = null;
    
    if ($admin_id) {
        $stmt = $conn->prepare("SELECT user_id, first_name, last_name, email, phone_number, date_of_birth, is_active, is_admin, created_at, updated_at FROM users WHERE user_id = ?");
        $stmt->bind_param('i', $admin_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin_data = $result->fetch_assoc();
        $stmt->close();
    }
    
    if (!$admin_data) {
        echo '<div class="alert alert-danger">Unable to load admin information.</div>';
        return;
    }
    ?>

    <div class="row g-4">
        <!-- Admin Information -->
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Personal Information</h5>
                </div>
                <div class="card-body">
                    <form action="./src/services/profile/update_profile.php" method="POST">
                        <div class="row g-3">
                            <div class="col-12 col-sm-6">
                                <label class="form-label" for="first_name">First Name</label>
                                <input class="form-control" type="text" id="first_name" name="first_name" 
                                       value="<?= htmlspecialchars($admin_data['first_name']) ?>" required>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="form-label" for="last_name">Last Name</label>
                                <input class="form-control" type="text" id="last_name" name="last_name" 
                                       value="<?= htmlspecialchars($admin_data['last_name']) ?>" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="email">Email Address</label>
                                <input class="form-control" type="email" id="email" name="email" 
                                       value="<?= htmlspecialchars($admin_data['email']) ?>" required>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="form-label" for="phone_number">Phone Number</label>
                                <input class="form-control" type="tel" id="phone_number" name="phone_number" 
                                       value="<?= htmlspecialchars($admin_data['phone_number'] ?? '') ?>">
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="form-label" for="date_of_birth">Date of Birth</label>
                                <input class="form-control" type="date" id="date_of_birth" name="date_of_birth" 
                                       value="<?= htmlspecialchars($admin_data['date_of_birth'] ?? '') ?>">
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary" type="submit">
                                    <span class="fas fa-save me-2"></span>Update Profile
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Admin Account & Business Summary -->
        <div class="col-12 col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Account Summary</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar avatar-4xl me-3">
                            <div class="avatar-name rounded-circle bg-primary">
                                <span class="fs-2 text-white">
                                    <?= strtoupper(substr($admin_data['first_name'] ?? 'A', 0, 1)) ?>
                                </span>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h6 class="mb-1"><?= htmlspecialchars($admin_data['first_name'] . ' ' . $admin_data['last_name']) ?></h6>
                            <p class="mb-0 text-body-tertiary"><?= htmlspecialchars($admin_data['email']) ?></p>
                            <small class="text-body-tertiary">
                                Member since <?= date('M Y', strtotime($admin_data['created_at'])) ?><br>
                                <span class="badge bg-success">Admin</span>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Business Overview</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="p-2 bg-body-tertiary rounded-2">
                                <h6 class="mb-1 fw-bold text-primary">
                                    <?php
                                    $customers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE is_admin = 0"));
                                    echo $customers['count'];
                                    ?>
                                </h6>
                                <small class="text-body-tertiary">Customers</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="p-2 bg-body-tertiary rounded-2">
                                <h6 class="mb-1 fw-bold text-success">
                                    <?php
                                    $orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM orders"));
                                    echo $orders['count'];
                                    ?>
                                </h6>
                                <small class="text-body-tertiary">Orders</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-body-tertiary rounded-2">
                                <h6 class="mb-1 fw-bold text-info">
                                    <?php
                                    $products = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM products"));
                                    echo $products['count'];
                                    ?>
                                </h6>
                                <small class="text-body-tertiary">Products</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-body-tertiary rounded-2">
                                <h6 class="mb-1 fw-bold text-warning">
                                    <?php
                                    $revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_amount) as total FROM orders WHERE payment_status = 'paid' AND status != 'cancelled'"));
                                    echo number_format($revenue['total'] ?? 0);
                                    ?> RWF
                                </h6>
                                <small class="text-body-tertiary">Total Revenue</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Password -->
    <div class="row g-4 mt-2">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Change Password</h5>
                </div>
                <div class="card-body">
                    <form action="./src/services/profile/change_password.php" method="POST">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label" for="current_password">Current Password</label>
                                <input class="form-control" type="password" id="current_password" name="current_password" required>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="form-label" for="new_password">New Password</label>
                                <input class="form-control" type="password" id="new_password" name="new_password" 
                                       minlength="6" required>
                                <div class="form-text">Minimum 6 characters</div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="form-label" for="confirm_password">Confirm New Password</label>
                                <input class="form-control" type="password" id="confirm_password" name="confirm_password" required>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-warning" type="submit">
                                    <span class="fas fa-key me-2"></span>Change Password
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Account Actions -->
        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Account Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#exportDataModal">
                            <span class="fas fa-download me-2"></span>Export My Data
                        </button>
                        <button class="btn btn-outline-warning" type="button" data-bs-toggle="modal" data-bs-target="#deactivateAccountModal">
                            <span class="fas fa-pause me-2"></span>Deactivate Account
                        </button>
                        <button class="btn btn-outline-danger" type="button" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                            <span class="fas fa-trash me-2"></span>Delete Account
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="./src/services/profile/delete_account.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title text-danger">Delete Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <h6 class="alert-heading">Warning!</h6>
                        <p class="mb-0">This action cannot be undone. All your admin data will be permanently deleted.</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Enter your password to confirm</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Account</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Deactivate Account Modal -->
<div class="modal fade" id="deactivateAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-warning">Deactivate Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <h6 class="alert-heading">Account Deactivation</h6>
                    <p class="mb-0">Your account will be temporarily disabled. You can reactivate it anytime by logging in.</p>
                </div>
                <p>This feature is not yet implemented.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Export Data Modal -->
<div class="modal fade" id="exportDataModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Export My Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>This feature allows you to download all your personal data including:</p>
                <ul>
                    <li>Personal information</li>
                    <li>Account activity</li>
                </ul>
                <p class="text-muted">This feature is not yet implemented.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password confirmation validation
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');
    
    function validatePassword() {
        if (newPassword.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity('Passwords do not match');
        } else {
            confirmPassword.setCustomValidity('');
        }
    }
    
    newPassword.addEventListener('change', validatePassword);
    confirmPassword.addEventListener('keyup', validatePassword);
});
</script>
