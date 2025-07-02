<?php
include '../../../config/config.php';

$attribute_type_id = intval($_GET['attribute_type_id'] ?? 0);
if ($attribute_type_id === 0) {
    echo '<div class="alert alert-danger">Invalid attribute type.</div>';
    exit;
}

// Get attribute type name
$type_query = mysqli_query($conn, "SELECT name FROM attribute_type WHERE attribute_type_id = $attribute_type_id");
$type = mysqli_fetch_assoc($type_query);
if (!$type) {
    echo '<div class="alert alert-danger">Attribute type not found.</div>';
    exit;
}

// Get all values for this attribute type
$values_query = mysqli_query($conn, "SELECT * FROM attribute_value WHERE attribute_type_id = $attribute_type_id ORDER BY value ASC");
$values = [];
while ($row = mysqli_fetch_assoc($values_query)) {
    $values[] = $row;
}
$value_count = count($values);
?>
<div class="card  mb-3">
    <div class="card-header bg-light d-flex align-items-center justify-content-between">
        <span class="fw-semibold">Possible Values <span class="badge bg-primary ms-2"><?= $value_count ?></span></span>
        <span class="text-muted small">Attribute: <b><?= htmlspecialchars($type['name']) ?></b></span>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover table-sm mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width:60%">Value</th>
                    <th style="width:40%" class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($values as $val): ?>
                <tr data-attribute-value-id="<?= (int)$val['attribute_value_id'] ?>">
                    <td class="ps-4 fw-medium" style="color: #333;"><?= htmlspecialchars($val['value']) ?></td>
                    <td class="text-end pe-3">
                        <button class="btn btn-sm btn-outline-warning edit-value-btn me-1" data-bs-toggle="tooltip" title="Edit" data-attribute-value-id="<?= (int)$val['attribute_value_id'] ?>" data-value="<?= htmlspecialchars($val['value']) ?>"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-outline-danger delete-value-btn" data-bs-toggle="tooltip" title="Delete" data-attribute-value-id="<?= (int)$val['attribute_value_id'] ?>"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (!$value_count): ?>
                <tr><td colspan="2" class="text-center text-muted py-3">No values found for this attribute type.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="card ">
    <div class="card-header bg-light fw-semibold">Add New Value</div>
    <div class="card-body py-3">
        <form id="addValueForm" action="./src/services/products/add_attribute_value.php" method="POST" class="row g-2 align-items-center justify-content-center">
            <input type="hidden" name="attribute_type_id" value="<?= $attribute_type_id ?>">
            <div class="col-8 col-md-6">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-plus"></i></span>
                    <input type="text" class="form-control" name="value" placeholder="New value" required>
                </div>
            </div>
            <div class="col-4 col-md-2">
                <button type="submit" class="btn btn-primary w-100">Add Value</button>
            </div>
        </form>
    </div>
</div>
<script>
// Bootstrap tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
tooltipTriggerList.forEach(function (tooltipTriggerEl) {
    new bootstrap.Tooltip(tooltipTriggerEl);
});
// Edit value
Array.from(document.querySelectorAll('.edit-value-btn')).forEach(function(btn) {
    btn.addEventListener('click', function() {
        var valueId = btn.getAttribute('data-attribute-value-id');
        var value = btn.getAttribute('data-value');
        var newValue = prompt('Edit value:', value);
        if (newValue && newValue.trim() !== '' && newValue !== value) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = './src/services/products/edit_attribute_value.php';
            form.innerHTML = '<input type="hidden" name="attribute_value_id" value="'+valueId+'">' +
                             '<input type="hidden" name="value" value="'+encodeURIComponent(newValue)+'">';
            document.body.appendChild(form);
            form.submit();
        }
    });
});
// Delete value
Array.from(document.querySelectorAll('.delete-value-btn')).forEach(function(btn) {
    btn.addEventListener('click', function() {
        var valueId = btn.getAttribute('data-attribute-value-id');
        if (confirm('Are you sure you want to delete this value?')) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = './src/services/products/delete_attribute_value.php';
            form.innerHTML = '<input type="hidden" name="attribute_value_id" value="'+valueId+'">';
            document.body.appendChild(form);
            form.submit();
        }
    });
});
</script> 