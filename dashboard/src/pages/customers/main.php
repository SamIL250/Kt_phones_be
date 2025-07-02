<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="index">Dashboard</a></li>
        <li class="breadcrumb-item active">Customers</li>
    </ol>
</nav>
<div class="mb-9">
    <div class="row g-3 mb-4">
        <div class="col-auto">
            <h2 class="mb-0">Customers</h2>
        </div>
    </div>
    <ul class="nav nav-links mb-3 mb-lg-2 mx-n3" id="customer-filters">
        <li class="nav-item">
            <a class="nav-link active" data-filter="all" aria-current="page" href="#">
                <span>All </span>
                <span class="text-body-tertiary fw-semibold">
                    (<?php
                    // Count registered users
                    $user_count = mysqli_fetch_array(mysqli_query($conn, "SELECT COUNT(*) as cnt FROM users"))['cnt'];
                    // Count unique guest emails from addresses
                    $guest_count = mysqli_fetch_array(mysqli_query($conn, "SELECT COUNT(DISTINCT email) as cnt FROM addresses WHERE is_guest = 1"))['cnt'];
                    echo $user_count + $guest_count;
                    ?>)
                </span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-filter="registered" href="#">
                <span>Registered </span>
                <span class="text-body-tertiary fw-semibold">
                    (<?php echo $user_count; ?>)
                </span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-filter="guest" href="#">
                <span>Guest </span>
                <span class="text-body-tertiary fw-semibold">
                    (<?php echo $guest_count; ?>)
                </span>
            </a>
        </li>
    </ul>
    <div id="customers" data-list='{"valueNames":["customer","email","type"],"page":10,"pagination":true}'>
        <div class="mb-4">
            <div class="d-flex flex-wrap gap-3">
                <div class="search-box">
                    <form class="position-relative"><input class="form-control search-input search" type="search" placeholder="Search customers" aria-label="Search" />
                        <span class="fas fa-search search-box-icon"></span>
                    </form>
                </div>
                <div class="ms-xxl-auto">
                    <button class="btn btn-link text-body me-4 px-0"><span class="fa-solid fa-file-export fs-9 me-2"></span>Export</button>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCustomerModal"><span class="fas fa-plus me-2"></span>Add customer</button>
                </div>
            </div>
        </div>
        <div class="mx-n4 px-4 mx-lg-n6 px-lg-6 bg-body-emphasis border-top border-bottom border-translucent position-relative top-1">
            <div class="table-responsive scrollbar mx-n1 px-1">
                <table class="table fs-9 mb-0">
                    <thead>
                        <tr>
                            <th class="white-space-nowrap fs-9 align-middle ps-0" style="max-width:20px; width:18px;">
                                <div class="form-check mb-0 fs-8"><input class="form-check-input" id="checkbox-bulk-customers-select" type="checkbox" data-bulk-select='{"body":"customers-table-body"}' /></div>
                            </th>
                            <th class="sort white-space-nowrap align-middle ps-4" scope="col" style="width:200px;" data-sort="customer">CUSTOMER NAME</th>
                            <th class="sort align-middle ps-4" scope="col" data-sort="email" style="width:250px;">EMAIL</th>
                            <th class="sort align-middle ps-4" scope="col" data-sort="type" style="width:120px;">TYPE</th>
                            <th class="sort text-end align-middle pe-0 ps-4" scope="col"></th>
                        </tr>
                    </thead>
                    <tbody class="list" id="customers-table-body">
                        <?php
                        // Registered users
                        $user_query = mysqli_query($conn, "SELECT user_id, first_name, last_name, email FROM users");
                        $has_customers = false;
                        foreach ($user_query as $user) {
                            $has_customers = true;
                        ?>
                        <tr class="position-static" data-customer-id="user-<?= (int)$user['user_id'] ?>" data-type="registered">
                            <td class="fs-9 align-middle">
                                <div class="form-check mb-0 fs-8">
                                    <input class="form-check-input" type="checkbox" data-bulk-select-row='{"customer":"<?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>"}' />
                                </div>
                            </td>
                            <td class="customer align-middle ps-4 fw-semibold">
                                <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
                            </td>
                            <td class="email align-middle ps-4 text-muted">
                                <?= htmlspecialchars($user['email']) ?>
                            </td>
                            <td class="type align-middle ps-4">
                                <span class="badge bg-success-subtle text-success">Registered</span>
                            </td>
                            <td class="align-middle white-space-nowrap text-end pe-0 ps-4 btn-reveal-trigger">
                                <div class="btn-reveal-trigger position-static"><button class="btn btn-sm dropdown-toggle dropdown-caret-none transition-none btn-reveal fs-10" type="button" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent"><span class="fas fa-ellipsis-h fs-10"></span></button>
                                    <div class="dropdown-menu dropdown-menu-end py-2">
                                        <a class="dropdown-item" href="#">Edit</a>
                                        <a class="dropdown-item text-danger" href="#">Delete</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php }
                        // Guest users (unique emails)
                        $guest_query = mysqli_query($conn, "SELECT MIN(address_id) as min_id, email, first_name, last_name FROM addresses WHERE is_guest = 1 GROUP BY email");
                        foreach ($guest_query as $guest) {
                            $has_customers = true;
                        ?>
                        <tr class="position-static" data-customer-id="guest-<?= htmlspecialchars($guest['email']) ?>" data-type="guest">
                            <td class="fs-9 align-middle">
                                <div class="form-check mb-0 fs-8">
                                    <input class="form-check-input" type="checkbox" data-bulk-select-row='{"customer":"<?= htmlspecialchars($guest['first_name'] . ' ' . $guest['last_name']) ?>"}' />
                                </div>
                            </td>
                            <td class="customer align-middle ps-4 fw-semibold">
                                <?= htmlspecialchars($guest['first_name'] . ' ' . $guest['last_name']) ?>
                            </td>
                            <td class="email align-middle ps-4 text-muted">
                                <?= htmlspecialchars($guest['email']) ?>
                            </td>
                            <td class="type align-middle ps-4">
                                <span class="badge bg-secondary-subtle text-secondary">Guest</span>
                            </td>
                            <td class="align-middle white-space-nowrap text-end pe-0 ps-4 btn-reveal-trigger">
                                <div class="btn-reveal-trigger position-static"><button class="btn btn-sm dropdown-toggle dropdown-caret-none transition-none btn-reveal fs-10" type="button" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent"><span class="fas fa-ellipsis-h fs-10"></span></button>
                                    <div class="dropdown-menu dropdown-menu-end py-2">
                                        <a class="dropdown-item disabled" href="#">Edit</a>
                                        <a class="dropdown-item text-danger disabled" href="#">Delete</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php }
                        if (!$has_customers): ?>
                        <tr><td colspan="5"><div class='alert alert-info text-center p-2 rounded-2 mt-2 mb-2'>No customers found</div></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="row align-items-center justify-content-between py-2 pe-0 fs-9">
                <div class="col-auto d-flex">
                    <p class="mb-0 d-none d-sm-block me-3 fw-semibold text-body" data-list-info="data-list-info"></p>
                </div>
                <div class="col-auto d-flex"><button class="page-link" data-list-pagination="prev"><span class="fas fa-chevron-left"></span></button>
                    <ul class="mb-0 pagination"></ul><button class="page-link pe-0" data-list-pagination="next"><span class="fas fa-chevron-right"></span></button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Customer Modal (for registered users only) -->
<div class="modal fade" id="addCustomerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="./src/services/customers/add_customer.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Add Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">First Name</label>
                        <input type="text" class="form-control" name="first_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Last Name</label>
                        <input type="text" class="form-control" name="last_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Customer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Customer Modal (for registered users only) -->
<div class="modal fade" id="editCustomerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editCustomerForm" action="./src/services/customers/edit_customer.php" method="POST">
                <input type="hidden" name="user_id" id="editCustomerId">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">First Name</label>
                        <input type="text" class="form-control" name="first_name" id="editCustomerFirstName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Last Name</label>
                        <input type="text" class="form-control" name="last_name" id="editCustomerLastName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" id="editCustomerEmail" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password <span class="text-muted">(leave blank to keep current)</span></label>
                        <input type="password" class="form-control" name="password" id="editCustomerPassword">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add a single hidden form for delete (registered users only) -->
<form id="deleteCustomerForm" action="./src/services/customers/delete_customer.php" method="POST" style="display:none;">
    <input type="hidden" name="user_id" id="deleteCustomerId">
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter logic
    document.querySelectorAll('#customer-filters .nav-link').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('#customer-filters .nav-link').forEach(function(l) { l.classList.remove('active'); });
            link.classList.add('active');
            var filter = link.getAttribute('data-filter');
            document.querySelectorAll('#customers-table-body tr').forEach(function(row) {
                if (filter === 'all') {
                    row.style.display = '';
                } else if (row.getAttribute('data-type') === filter) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
    document.querySelectorAll('tr[data-customer-id^="user-"] .dropdown-menu .dropdown-item').forEach(function(item) {
        if(item.textContent.trim() === 'Edit') {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                var row = item.closest('tr');
                var userId = row.getAttribute('data-customer-id').replace('user-', '');
                var name = row.querySelector('.customer').textContent.trim().split(' ');
                var firstName = name[0];
                var lastName = name.slice(1).join(' ');
                var email = row.querySelector('.email').textContent.trim();
                document.getElementById('editCustomerId').value = userId;
                document.getElementById('editCustomerFirstName').value = firstName;
                document.getElementById('editCustomerLastName').value = lastName;
                document.getElementById('editCustomerEmail').value = email;
                document.getElementById('editCustomerPassword').value = '';
                var modal = new bootstrap.Modal(document.getElementById('editCustomerModal'));
                modal.show();
            });
        }
        if(item.textContent.trim() === 'Delete') {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                var row = item.closest('tr');
                var userId = row.getAttribute('data-customer-id').replace('user-', '');
                if (confirm('Are you sure you want to delete this customer? This action cannot be undone.')) {
                    document.getElementById('deleteCustomerId').value = userId;
                    document.getElementById('deleteCustomerForm').submit();
                }
            });
        }
    });
});
</script>
