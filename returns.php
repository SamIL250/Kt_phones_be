<?php include './src/layout/layout.php'; ?>

<div class="bg-gray-50 font-sans">
    <div class="container max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <!-- Page Header -->
        <div class="text-center mb-12 observe-card">
            <h1 class="text-4xl md:text-5xl font-extrabold text-gray-800">Returns & Exchanges</h1>
            <p class="text-lg text-gray-500 mt-2">Hassle-free returns to ensure you're happy with your purchase.</p>
            <div class="mt-4 mx-auto w-24 h-1 bg-blue-600 rounded"></div>
        </div>

        <div class="bg-white p-8 sm:p-10 rounded-lg shadow-lg space-y-8 observe-card">

            <!-- Introduction -->
            <div class="prose max-w-none">
                <p class="text-lg text-gray-600">At KT-Phones, your satisfaction is our priority. If you're not completely satisfied with your purchase, we're here to help. Our return policy is designed to be simple and straightforward.</p>
            </div>

            <!-- Policy Section -->
            <div class="grid md:grid-cols-2 gap-8 items-start">
                <div class="space-y-6">
                    <!-- Eligibility -->
                    <div>
                        <h2 class="text-2xl font-bold text-gray-700 flex items-center mb-3"><i class="bi bi-check-circle-fill text-blue-500 mr-3"></i>Return Eligibility</h2>
                        <ul class="list-disc list-inside space-y-2 text-gray-600 pl-4">
                            <li>Items must be returned within <strong>30 days</strong> of the purchase date.</li>
                            <li>Products must be in their original condition: unused, with all original packaging, and accessories included.</li>
                            <li>A valid proof of purchase (receipt, order confirmation) is required.</li>
                            <li>Customized or special-order items are not eligible for return.</li>
                        </ul>
                    </div>

                    <!-- How to Initiate a Return -->
                    <div>
                        <h2 class="text-2xl font-bold text-gray-700 flex items-center mb-3"><i class="bi bi-box-arrow-in-right text-blue-500 mr-3"></i>How to Start a Return</h2>
                        <ol class="list-decimal list-inside space-y-2 text-gray-600 pl-4">
                            <li><strong>Contact Us:</strong> Email our support team at <a href="mailto:returns@ktphones.com" class="text-blue-600 hover:underline">returns@ktphones.com</a> with your order number.</li>
                            <li><strong>Receive Authorization:</strong> We will provide you with a Return Merchandise Authorization (RMA) number and shipping instructions.</li>
                            <li><strong>Ship Your Item:</strong> Pack the item securely and send it to the address provided.</li>
                        </ol>
                    </div>
                </div>
                <div class="bg-blue-50 p-6 rounded-lg border-l-4 border-blue-500">
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Refunds & Exchanges</h3>
                    <p class="text-gray-600 mb-4">Once we receive and inspect your item, we will notify you of the approval or rejection of your refund. If approved, your refund will be processed, and a credit will automatically be applied to your original method of payment within 7-10 business days.</p>
                    <p class="text-gray-600">For exchanges, please specify the new item you would like. We will process the exchange once the original item has been received and inspected.</p>
                    <div class="mt-5">
                        <a href="contact" class="bg-blue-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-700 transition-all shadow-md">Start a Return</a>
                    </div>
                </div>
            </div>

            <!-- FAQ Section -->
            <div class="border-t pt-8">
                <h2 class="text-2xl font-bold text-gray-700 text-center mb-6">Frequently Asked Questions</h2>
                <div class="space-y-4">
                    <details class="bg-gray-50 p-4 rounded-lg cursor-pointer">
                        <summary class="font-semibold text-gray-800">Who pays for return shipping?</summary>
                        <p class="text-gray-600 mt-2">For defective items or errors on our part, we will cover the return shipping costs. For all other returns, the customer is responsible for shipping fees.</p>
                    </details>
                    <details class="bg-gray-50 p-4 rounded-lg cursor-pointer">
                        <summary class="font-semibold text-gray-800">What if my item is damaged upon arrival?</summary>
                        <p class="text-gray-600 mt-2">Please contact us within 48 hours of receiving the item with photos of the damage. We will arrange for a replacement or a full refund.</p>
                    </details>
                    <details class="bg-gray-50 p-4 rounded-lg cursor-pointer">
                        <summary class="font-semibold text-gray-800">How long does an exchange take?</summary>
                        <p class="text-gray-600 mt-2">Exchange processing time is typically 3-5 business days after we receive the returned item, plus standard shipping time for the new item.</p>
                    </details>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include './src/components/footer.php'; ?>
</body>

</html>