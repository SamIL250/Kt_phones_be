<?php
// Fetch user addresses
$address_query = mysqli_prepare(
    $conn,
    "SELECT * FROM addresses WHERE user_id = ? AND is_guest = 0 ORDER BY is_default DESC"
);
mysqli_stmt_bind_param($address_query, "i", $customer_id);
mysqli_stmt_execute($address_query);
$address_result = mysqli_stmt_get_result($address_query);
$addresses = mysqli_fetch_all($address_result, MYSQLI_ASSOC);
?>

<div id="addresses" class="bg-white border border-gray-200 p-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-600">Saved Addresses</h2>
        <button onclick="openAddAddressModal()" class="bg-blue-600 text-white px-4 py-2 hover:bg-blue-700 transition-colors">
            <i class="fas fa-plus mr-2"></i>
            Add Address
        </button>
    </div>

    <?php if (empty($addresses)): ?>
    <div class="text-center py-8">
        <i class="fas fa-map-marker-alt text-gray-400 text-4xl mb-3"></i>
        <p class="text-gray-600">No addresses saved yet</p>
        <button onclick="openAddAddressModal()" class="mt-4 text-blue-600 hover:text-blue-700 font-medium">
            Add your first address
        </button>
    </div>
    <?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <?php foreach ($addresses as $address): ?>
        <div class="border-2 <?php echo $address['is_default'] ? 'border-blue-200 bg-blue-50' : 'border-gray-200'; ?> p-4">
            <div class="flex items-center justify-between mb-2">
                <span class="<?php echo $address['is_default'] ? 'bg-blue-600 text-white' : 'text-gray-500'; ?> text-xs px-2 py-1 rounded font-medium">
                    <?php echo strtoupper($address['address_type']); ?>
                    <?php if ($address['is_default']): ?>
                    (DEFAULT)
                    <?php endif; ?>
                </span>
                <div class="flex gap-2">
                    <button onclick="editAddress('<?php echo $address['address_id']; ?>', <?php echo $customer_id; ?>)" class="text-gray-500 hover:text-blue-600">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deleteAddress('<?php echo $address['address_id']; ?>', <?php echo $customer_id; ?>)" class="text-gray-500 hover:text-red-600">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <h3 class="font-medium text-gray-600">
                <?php echo htmlspecialchars($address['address_line1']); ?>
                <?php if ($address['address_line2']): ?>
                <br><?php echo htmlspecialchars($address['address_line2']); ?>
                <?php endif; ?>
            </h3>
            <p class="text-gray-700 text-sm mt-1">
                <?php 
                $address_parts = array_filter([
                    $address['district'],
                    $address['sector'],
                    $address['cell'],
                    $address['country']
                ]);
                echo htmlspecialchars(implode(', ', $address_parts));
                ?>
                <?php if ($address['phone_number']): ?>
                <br>Phone: <?php echo htmlspecialchars($address['phone_number']); ?>
                <?php endif; ?>
            </p>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<!-- Add/Edit Address Modal -->
<div id="addressModal" class="fixed inset-0 hidden items-center justify-center" style="z-index: 55555;">
    <div class="bg-white border border-gray-300  shadow-sm p-6 w-full max-w-3xl mx-4 transform transition-all">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-800" id="modalTitle">Add New Address</h3>
            <button onclick="closeAddressModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="addressForm" action="<?php echo $addressId ? './src/services/address/update.php' : './src/services/address/add.php'; ?>" method="POST">
            <input type="hidden" id="addressId" name="address_id">
            <input type="hidden" name="user_id" value="<?php echo $customer_id; ?>">
            <input type="hidden" name="is_guest" value="0">
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address Type</label>
                    <select name="address_type" class="w-full border border-gray-200  px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" required>
                        <option value="shipping">Shipping</option>
                        <option value="billing">Billing</option>
                        <option value="both">Both</option>
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 1</label>
                    <input type="text" name="address_line1" class="w-full border border-gray-200  px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" required>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 2</label>
                    <input type="text" name="address_line2" class="w-full border border-gray-200  px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                    <input type="text" name="country" class="w-full border border-gray-200  px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">District</label>
                    <input type="text" name="district" class="w-full border border-gray-200  px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sector</label>
                    <input type="text" name="sector" class="w-full border border-gray-200  px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cell</label>
                    <input type="text" name="cell" class="w-full border border-gray-200  px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input type="tel" name="phone_number" class="w-full border border-gray-200  px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                </div>
                <div class="flex items-center">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_default" id="isDefault" class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        <span class="ml-3 text-sm font-medium text-gray-700">Set as default address</span>
                    </label>
                </div>
            </div>
            <div class="mt-5 flex justify-end gap-3">
                <button type="button" onclick="closeAddressModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white  hover:bg-blue-700 transition-colors">
                    Save Address
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddAddressModal() {
    document.getElementById('modalTitle').textContent = 'Add New Address';
    document.getElementById('addressForm').reset();
    document.getElementById('addressId').value = '';
    document.getElementById('addressForm').action = './src/services/address/add.php';
    document.getElementById('addressModal').classList.remove('hidden');
    document.getElementById('addressModal').classList.add('flex');
}

function closeAddressModal() {
    document.getElementById('addressModal').classList.add('hidden');
    document.getElementById('addressModal').classList.remove('flex');
}

function editAddress(addressId, customerId) {
    document.getElementById('modalTitle').textContent = 'Edit Address';
    // Fetch address details and populate form
    fetch(`./src/services/address/get.php?id=${addressId}&user_id=${customerId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const form = document.getElementById('addressForm');
                form.address_id.value = data.address_id;
                form.user_id.value = customerId;
                form.address_type.value = data.address_type;
                form.address_line1.value = data.address_line1;
                form.address_line2.value = data.address_line2 || '';
                form.country.value = data.country;
                form.district.value = data.district;
                form.sector.value = data.sector || '';
                form.cell.value = data.cell || '';
                form.phone_number.value = data.phone_number || '';
                form.is_default.checked = data.is_default;
                form.action = './src/services/address/update.php';
                
                document.getElementById('addressModal').classList.remove('hidden');
                document.getElementById('addressModal').classList.add('flex');
            } else {
                alert(data.message || 'Failed to load address details');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load address details');
        });
}

function deleteAddress(addressId, customerId) {
    if (confirm('Are you sure you want to delete this address?')) {
        window.location.href = `./src/services/address/delete.php?address_id=${addressId}&user_id=${customerId}`;
    }
}
</script>