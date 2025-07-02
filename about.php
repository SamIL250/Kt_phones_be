<?php include './src/layout/layout.php'; ?>

<div class="bg-gray-50 font-sans">
    <!-- Hero Section -->
    <div class="relative bg-cover bg-center text-white py-24 px-8" style="background-image: url('src/assets/images/header1.jpg');">
        <div class="absolute inset-0 bg-black opacity-60"></div>
        <div class="relative max-w-4xl mx-auto text-center">
            <h1 class="text-4xl md:text-5xl font-extrabold mb-3 observe-card">About KT-Phones</h1>
            <p class="text-lg text-gray-200 max-w-2xl mx-auto observe-card">Your trusted partner in mobile technology, connecting you to the world with the latest and greatest smartphones.</p>
        </div>
    </div>

    <div class="container max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">

        <!-- Our Mission Section -->
        <div class="bg-white p-10 rounded-lg shadow-lg mb-16 observe-card">
            <div class="grid md:grid-cols-2 gap-10 items-center">
                <div>
                    <h2 class="text-3xl font-bold text-gray-800 mb-4">Our Mission</h2>
                    <p class="text-gray-600 mb-4">Our mission is to make the latest mobile technology accessible to everyone. We believe in the power of connection and strive to provide high-quality devices at fair prices, backed by exceptional customer service. We're not just selling phones; we're building a community of tech enthusiasts who value quality, innovation, and reliability.</p>
                    <p class="text-gray-600">From flagship models to budget-friendly options, every product in our catalog is carefully selected to meet our high standards.</p>
                </div>
                <div class="rounded-lg overflow-hidden shadow-xl">
                    <img src="src/assets/images/google_pixel_8_.jpg" alt="Our Mission" class="w-full h-full object-cover">
                </div>
            </div>
        </div>

        <!-- Why Choose Us Section -->
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-gray-800 mb-10 observe-card">Why Choose Us?</h2>
            <div class="grid md:grid-cols-3 gap-10">
                <div class="bg-white p-8 rounded-lg shadow-md hover:shadow-xl transition-shadow observe-card">
                    <i class="bi bi-patch-check-fill text-4xl text-blue-600 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Quality Guaranteed</h3>
                    <p class="text-gray-600">We only source from trusted brands and suppliers to ensure every product is authentic and top-quality.</p>
                </div>
                <div class="bg-white p-8 rounded-lg shadow-md hover:shadow-xl transition-shadow observe-card">
                    <i class="bi bi-truck text-4xl text-blue-600 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Fast & Reliable Shipping</h3>
                    <p class="text-gray-600">With our efficient logistics, your new device will be in your hands before you know it.</p>
                </div>
                <div class="bg-white p-8 rounded-lg shadow-md hover:shadow-xl transition-shadow observe-card">
                    <i class="bi bi-headset text-4xl text-blue-600 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">Expert Support</h3>
                    <p class="text-gray-600">Our knowledgeable team is always here to help you with any questions or concerns.</p>
                </div>
            </div>
        </div>

        <!-- Meet the Team Section -->
        <div class="bg-white p-10 rounded-lg shadow-lg observe-card">
            <div class="text-center mb-10">
                <h2 class="text-3xl font-bold text-gray-800">Meet the Team</h2>
                <p class="text-gray-500 mt-2">The passionate individuals behind KT-Phones.</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-10 text-center">
                <!-- Team Member 1 -->
                <div class="team-member">
                    <div class="relative inline-block">
                        <img src="https://i.pravatar.cc/150?img=1" alt="Team Member 1" class="w-32 h-32 rounded-full mx-auto mb-4 shadow-md">
                        <div class="absolute inset-0 rounded-full border-4 border-transparent hover:border-blue-500 transition-all"></div>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800">John Doe</h3>
                    <p class="text-gray-500">Founder & CEO</p>
                </div>
                <!-- Team Member 2 -->
                <div class="team-member">
                    <div class="relative inline-block">
                        <img src="https://i.pravatar.cc/150?img=2" alt="Team Member 2" class="w-32 h-32 rounded-full mx-auto mb-4 shadow-md">
                        <div class="absolute inset-0 rounded-full border-4 border-transparent hover:border-blue-500 transition-all"></div>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800">Jane Smith</h3>
                    <p class="text-gray-500">Head of Operations</p>
                </div>
                <!-- Team Member 3 -->
                <div class="team-member">
                    <div class="relative inline-block">
                        <img src="https://i.pravatar.cc/150?img=3" alt="Team Member 3" class="w-32 h-32 rounded-full mx-auto mb-4 shadow-md">
                        <div class="absolute inset-0 rounded-full border-4 border-transparent hover:border-blue-500 transition-all"></div>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800">Peter Jones</h3>
                    <p class="text-gray-500">Lead Tech Expert</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include './src/components/footer.php'; ?>
</body>

</html>