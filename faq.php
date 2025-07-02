<?php include './src/layout/layout.php'; ?>

<div class="bg-gray-50 font-sans">
    <div class="container max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <!-- Page Header -->
        <div class="text-center mb-12 observe-card">
            <h1 class="text-4xl md:text-5xl font-extrabold text-gray-800">Frequently Asked Questions</h1>
            <p class="text-lg text-gray-500 mt-2">Have questions? We've got answers. Find what you're looking for below.</p>
            <div class="mt-4 mx-auto w-24 h-1 bg-blue-600 rounded"></div>
        </div>

        <div class="bg-white p-8 sm:p-10 rounded-lg shadow-lg observe-card">
            <div class="space-y-8">

                <!-- General Questions -->
                <div>
                    <h2 class="text-2xl font-bold text-gray-700 border-b-2 border-blue-200 pb-2 mb-6">General</h2>
                    <div class="space-y-4">
                        <details class="bg-gray-50 p-4 rounded-lg cursor-pointer group">
                            <summary class="font-semibold text-gray-800 flex justify-between items-center">
                                What makes KT-Phones different from other phone retailers?
                                <i class="bi bi-chevron-down group-open:rotate-180 transition-transform"></i>
                            </summary>
                            <p class="text-gray-600 mt-3">At KT-Phones, we pride ourselves on a curated selection of high-quality smartphones, competitive pricing, and exceptional customer service. We don't just sell phones; we provide a complete, satisfying shopping experience from start to finish.</p>
                        </details>
                        <details class="bg-gray-50 p-4 rounded-lg cursor-pointer group">
                            <summary class="font-semibold text-gray-800 flex justify-between items-center">
                                Are all your products new and authentic?
                                <i class="bi bi-chevron-down group-open:rotate-180 transition-transform"></i>
                            </summary>
                            <p class="text-gray-600 mt-3">Absolutely. We are an authorized retailer for all the brands we carry. Every product is brand new, in its original sealed packaging, and comes with a full manufacturer's warranty.</p>
                        </details>
                    </div>
                </div>

                <!-- Orders & Payment -->
                <div>
                    <h2 class="text-2xl font-bold text-gray-700 border-b-2 border-blue-200 pb-2 mb-6">Orders & Payment</h2>
                    <div class="space-y-4">
                        <details class="bg-gray-50 p-4 rounded-lg cursor-pointer group">
                            <summary class="font-semibold text-gray-800 flex justify-between items-center">
                                What payment methods do you accept?
                                <i class="bi bi-chevron-down group-open:rotate-180 transition-transform"></i>
                            </summary>
                            <p class="text-gray-600 mt-3">We accept all major credit cards (Visa, MasterCard, American Express), as well as secure payment options like PayPal and Mobile Money.</p>
                        </details>
                        <details class="bg-gray-50 p-4 rounded-lg cursor-pointer group">
                            <summary class="font-semibold text-gray-800 flex justify-between items-center">
                                Can I cancel or modify my order?
                                <i class="bi bi-chevron-down group-open:rotate-180 transition-transform"></i>
                            </summary>
                            <p class="text-gray-600 mt-3">If you need to change or cancel your order, please contact us as soon as possible. We process orders quickly, but we'll do our best to accommodate your request if the order hasn't been shipped yet.</p>
                        </details>
                    </div>
                </div>

                <!-- Shipping & Delivery -->
                <div>
                    <h2 class="text-2xl font-bold text-gray-700 border-b-2 border-blue-200 pb-2 mb-6">Shipping & Delivery</h2>
                    <div class="space-y-4">
                        <details class="bg-gray-50 p-4 rounded-lg cursor-pointer group">
                            <summary class="font-semibold text-gray-800 flex justify-between items-center">
                                How long will it take to receive my order?
                                <i class="bi bi-chevron-down group-open:rotate-180 transition-transform"></i>
                            </summary>
                            <p class="text-gray-600 mt-3">Standard shipping typically takes 3-5 business days. We also offer expedited shipping options at checkout for faster delivery. You will receive a tracking number as soon as your order ships.</p>
                        </details>
                        <details class="bg-gray-50 p-4 rounded-lg cursor-pointer group">
                            <summary class="font-semibold text-gray-800 flex justify-between items-center">
                                Do you ship internationally?
                                <i class="bi bi-chevron-down group-open:rotate-180 transition-transform"></i>
                            </summary>
                            <p class="text-gray-600 mt-3">Currently, we only ship within the country. We are working on expanding our shipping options to include international destinations in the near future.</p>
                        </details>
                    </div>
                </div>

                <!-- Returns & Exchanges -->
                <div>
                    <h2 class="text-2xl font-bold text-gray-700 border-b-2 border-blue-200 pb-2 mb-6">Returns & Exchanges</h2>
                    <div class="space-y-4">
                        <details class="bg-gray-50 p-4 rounded-lg cursor-pointer group">
                            <summary class="font-semibold text-gray-800 flex justify-between items-center">
                                What is your return policy?
                                <i class="bi bi-chevron-down group-open:rotate-180 transition-transform"></i>
                            </summary>
                            <p class="text-gray-600 mt-3">We offer a 30-day hassle-free return policy for items in their original, unused condition. For more details, please visit our <a href="returns" class="text-blue-600 hover:underline">Returns Policy</a> page.</p>
                        </details>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    summary::-webkit-details-marker {
        display: none;
    }
</style>

<?php include './src/components/footer.php'; ?>
</body>

</html>