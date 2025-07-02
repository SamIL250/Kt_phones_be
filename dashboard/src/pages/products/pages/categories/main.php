<nav class="mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="index">Dashboard</a></li>
        <li class="breadcrumb-item active">Categories</li>
    </ol>
</nav>
<div class="mb-9">
    <div class="row g-3 mb-4">
        <div class="col-auto">
            <h2 class="mb-0">Product Categories</h2>
        </div>
    </div>
    <ul class="nav nav-links mb-3 mb-lg-2 mx-n3">
        <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="#">
                <span>All </span>
                <span class="text-body-tertiary fw-semibold">
                    (<?php echo mysqli_fetch_array(
                            mysqli_query($conn, "SELECT COUNT(category_id) as cat_num FROM categories")
                        )['cat_num'] ?>)
                </span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">
                <span>Active </span>
                <span class="text-body-tertiary fw-semibold">
                    (<?php echo mysqli_fetch_array(
                            mysqli_query($conn, "SELECT COUNT(category_id) as cat_num FROM categories WHERE is_active = 1")
                        )['cat_num'] ?>)
                </span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">
                <span>Inactive </span>
                <span class="text-body-tertiary fw-semibold">
                    (<?php echo mysqli_fetch_array(
                            mysqli_query($conn, "SELECT COUNT(category_id) as cat_num FROM categories WHERE is_active = 0")
                        )['cat_num'] ?>)
                </span>
            </a>
        </li>
    </ul>
    <div id="categories" data-list='{"valueNames":["category","description","slug","parent","status"],"page":10,"pagination":true}'>
        <div class="mb-4">
            <div class="d-flex flex-wrap gap-3">
                <div class="search-box">
                    <form class="position-relative"><input class="form-control search-input search" type="search" placeholder="Search categories" aria-label="Search" />
                        <span class="fas fa-search search-box-icon"></span>
                    </form>
                </div>
                <div class="ms-xxl-auto">
                    <button class="btn btn-link text-body me-4 px-0"><span class="fa-solid fa-file-export fs-9 me-2"></span>Export</button>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal"><span class="fas fa-plus me-2"></span>Add category</button>
                </div>
            </div>
        </div>
        <div class="mx-n4 px-4 mx-lg-n6 px-lg-6 bg-body-emphasis border-top border-bottom border-translucent position-relative top-1">
            <div class="table-responsive scrollbar mx-n1 px-1">
                <table class="table fs-9 mb-0">
                    <thead>
                        <tr>
                            <th class="white-space-nowrap fs-9 align-middle ps-0" style="max-width:20px; width:18px;">
                                <div class="form-check mb-0 fs-8"><input class="form-check-input" id="checkbox-bulk-categories-select" type="checkbox" data-bulk-select='{"body":"categories-table-body"}' /></div>
                            </th>
                            <th class="sort white-space-nowrap align-middle ps-4" scope="col" style="width:250px;" data-sort="category">CATEGORY NAME</th>
                            <th class="sort align-middle ps-4" scope="col" data-sort="description" style="width:350px;">DESCRIPTION</th>
                            <th class="sort align-middle ps-4" scope="col" data-sort="slug" style="width:150px;">SLUG</th>
                            <th class="sort align-middle ps-4" scope="col" data-sort="parent" style="width:150px;">PARENT</th>
                            <th class="sort align-middle ps-4" scope="col" data-sort="status" style="width:100px;">STATUS</th>
                            <th class="sort text-end align-middle pe-0 ps-4" scope="col"></th>
                        </tr>
                    </thead>
                    <tbody class="list" id="categories-table-body">
                        <?php
                        $cat_query = mysqli_query($conn, "SELECT c.*, p.name as parent_name FROM categories c LEFT JOIN categories p ON c.parent_category_id = p.category_id ORDER BY c.category_id DESC");
                        $has_categories = false;
                        foreach ($cat_query as $cat) {
                            $has_categories = true;
                        ?>
                            <tr class="position-static" data-category-id="<?= $cat['category_id'] ?>">
                                <td class="fs-9 align-middle">
                                    <div class="form-check mb-0 fs-8">
                                        <input class="form-check-input" type="checkbox" data-bulk-select-row='{"category":"<?= htmlspecialchars($cat['name']) ?>"}' />
                                    </div>
                                </td>
                                <td class="category align-middle ps-4 fw-semibold">
                                    <?= htmlspecialchars($cat['name']) ?>
                                </td>
                                <td class="description align-middle ps-4 text-muted">
                                    <?= htmlspecialchars($cat['description']) ?>
                                </td>
                                <td class="slug align-middle ps-4 text-muted">
                                    <?= htmlspecialchars($cat['slug']) ?>
                                </td>
                                <td class="parent align-middle ps-4 text-muted">
                                    <?= $cat['parent_name'] ? htmlspecialchars($cat['parent_name']) : '-' ?>
                                </td>
                                <td class="status align-middle ps-4">
                                    <?php if ($cat['is_active']): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="align-middle white-space-nowrap text-end pe-0 ps-4 btn-reveal-trigger">
                                    <div class="btn-reveal-trigger position-static"><button class="btn btn-sm dropdown-toggle dropdown-caret-none transition-none btn-reveal fs-10" type="button" data-bs-toggle="dropdown" data-boundary="window" aria-haspopup="true" aria-expanded="false" data-bs-reference="parent"><span class="fas fa-ellipsis-h fs-10"></span></button>
                                        <div class="dropdown-menu dropdown-menu-end py-2">
                                            <a class="dropdown-item edit-category" href="#" data-category-id="<?= $cat['category_id'] ?>">Edit</a>
                                            <a class="dropdown-item text-danger delete-category" href="#" data-category-id="<?= $cat['category_id'] ?>">Delete</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                        <?php if (!$has_categories): ?>
                            <tr>
                                <td colspan="7">
                                    <div class='alert alert-info text-center p-2 rounded-2 mt-2 mb-2'>No categories found</div>
                                </td>
                            </tr>
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

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="./src/services/products/add_category.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Category Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" class="form-control" name="slug" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Parent Category</label>
                        <select class="form-select" name="parent_category_id">
                            <option value="">None</option>
                            <?php
                            $parent_query = mysqli_query($conn, "SELECT category_id, name FROM categories WHERE parent_category_id IS NULL");
                            while ($parent = mysqli_fetch_assoc($parent_query)) {
                                echo '<option value="' . $parent['category_id'] . '">' . htmlspecialchars($parent['name']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="isActive" checked>
                        <label class="form-check-label" for="isActive">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editCategoryForm" action="./src/services/products/edit_category.php" method="POST">
                <input type="hidden" name="category_id" id="editCategoryId">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Category Name</label>
                        <input type="text" class="form-control" name="name" id="editCategoryName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="editCategoryDescription" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" class="form-control" name="slug" id="editCategorySlug" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Parent Category</label>
                        <select class="form-select" name="parent_category_id" id="editCategoryParent">
                            <option value="">None</option>
                            <?php
                            $parent_query = mysqli_query($conn, "SELECT category_id, name FROM categories WHERE parent_category_id IS NULL");
                            while ($parent = mysqli_fetch_assoc($parent_query)) {
                                echo '<option value="' . $parent['category_id'] . '">' . htmlspecialchars($parent['name']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="editCategoryActive">
                        <label class="form-check-label" for="editCategoryActive">Active</label>
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

<!-- Add a single hidden form for delete -->
<form id="deleteCategoryForm" action="./src/services/products/delete_category.php" method="POST" style="display:none;">
    <input type="hidden" name="category_id" id="deleteCategoryId">
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle Edit Category
        document.querySelectorAll('.edit-category').forEach(function(item) {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                var categoryId = this.getAttribute('data-category-id');

                // Fetch category data via AJAX
                fetch('./src/services/products/get_category.php?category_id=' + categoryId)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            var category = data.category;
                            document.getElementById('editCategoryId').value = category.category_id;
                            document.getElementById('editCategoryName').value = category.name;
                            document.getElementById('editCategoryDescription').value = category.description || '';
                            document.getElementById('editCategorySlug').value = category.slug;

                            // Set parent category
                            var parentSelect = document.getElementById('editCategoryParent');
                            parentSelect.value = category.parent_category_id || '';

                            // Set active status
                            document.getElementById('editCategoryActive').checked = category.is_active == 1;

                            // Show modal
                            var modal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
                            modal.show();
                        } else {
                            alert('Error loading category data: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error loading category data');
                    });
            });
        });

        // Handle Delete Category
        document.querySelectorAll('.delete-category').forEach(function(item) {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                var categoryId = this.getAttribute('data-category-id');

                if (confirm('Are you sure you want to delete this category? This action cannot be undone.')) {
                    document.getElementById('deleteCategoryId').value = categoryId;
                    document.getElementById('deleteCategoryForm').submit();
                }
            });
        });
    });
</script>