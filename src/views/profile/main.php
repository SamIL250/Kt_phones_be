<?php
    if(!$is_logged_in) {
        echo "<script>window.location.replace('401')</script>";
    }
?>

<div class="min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Profile Header -->
        <?php
        include 'utils/header.php';
        ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Sidebar - Navigation -->
            <?php
            include 'utils/aside.php';
            ?>

            <!-- Main Content Area -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Personal Information -->
                <?php
                include 'utils/personal_info.php';
                ?>

                <!-- Addresses -->
                <?php
                include 'utils/address.php';
                ?>

                <!-- Payment Methods -->
                <!-- <div id="payment-methods" class="bg-white border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-gray-600">Payment Methods</h2>
                        <button class="bg-blue-600 text-white px-4 py-2  hover:bg-blue-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            Add Card
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div class="border-2 border-b border-gray-200lue-200 bg-blue-50  p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-8 bg-blue-600 rounded flex items-center justify-center">
                                        <span class="text-white text-xs font-bold">VISA</span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-600">•••• •••• •••• 4532</p>
                                        <p class="text-sm text-gray-600">Expires 12/26</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="bg-blue-600 text-white text-xs px-2 py-1 rounded font-medium">DEFAULT</span>
                                    <button class="text-gray-500 hover:text-blue-600">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="text-gray-500 hover:text-red-600">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="border  p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-8 bg-red-500 rounded flex items-center justify-center">
                                        <span class="text-white text-xs font-bold">MC</span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-600">•••• •••• •••• 8765</p>
                                        <p class="text-sm text-gray-600">Expires 08/25</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <button class="text-gray-500 hover:text-blue-600">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="text-gray-500 hover:text-red-600">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->

                <!-- Recent Orders -->
                <?php
                include 'utils/recent_orders.php';
                ?>

                <!-- Security Settings -->
                <?php
                include 'utils/security.php';
                ?>
            </div>
        </div>
    </div>
</div>