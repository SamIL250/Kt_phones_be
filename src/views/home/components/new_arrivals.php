<!-- New Arrivals UI inspired by Figma, implemented with the help of ChatGPT/Cursor AI assistant, 2025. -->
<?php
// Fetch the 4 latest products with their primary image
$sql = "SELECT p.*, (
    SELECT pi.image_url FROM product_images pi WHERE pi.product_id = p.product_id AND pi.is_primary = 1 ORDER BY pi.display_order ASC LIMIT 1
) as primary_image
FROM products p
WHERE p.is_active = 1 AND p.published = 'true'
ORDER BY p.created_at DESC
LIMIT 4";
$result = mysqli_query($conn, $sql);
$new_arrivals = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $new_arrivals[] = $row;
    }
}
?>
<div class="max-w-7xl mx-auto px-4 py-16">
    <div class="flex flex-col gap-8">
        <div class="flex items-center justify-between mb-6 fade-in-up">
            <div>
                <span class="text-blue-500 font-semibold text-sm">Featured</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mt-1">New Arrival</h2>
            </div>
            <a href="store" class="bg-blue-400 hover:bg-blue-500 text-white font-semibold px-6 py-2 rounded transition-all duration-300 transform hover:scale-105 fade-in-up delay-100">View All Products</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Left: Large Featured Product -->
            <div class="md:col-span-2 bg-black rounded-2xl overflow-hidden relative flex items-end min-h-[340px] group card-anim fade-in-up delay-200">
                <?php if (!empty($new_arrivals[0])): $p = $new_arrivals[0]; ?>
                    <img src="<?= htmlspecialchars($p['primary_image'] ?: './src/assets/images/placeholder.png') ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="absolute inset-0 w-full h-full object-cover opacity-80 transition-all duration-500 group-hover:scale-105 group-hover:brightness-110" />
                    <div class="relative z-10 p-8 text-white">
                        <h3 class="text-2xl font-bold mb-2"><?= htmlspecialchars($p['name']) ?></h3>
                        <p class="mb-4"><?= htmlspecialchars(mb_strimwidth($p['description'], 0, 60, '...')) ?></p>
                        <a href="product-view?product=<?= urlencode($p['product_id']) ?>" class="bg-white text-black font-semibold px-5 py-2 rounded hover:bg-gray-200 transition-all duration-300 transform hover:scale-105">Shop Now</a>
                    </div>
                <?php else: ?>
                    <div class="relative z-10 p-8 text-white">No featured product found.</div>
                <?php endif; ?>
            </div>
            <!-- Right: 3 Small Product Cards -->
            <div class="flex flex-col gap-6">
                <?php if (!empty($new_arrivals[1])): $p = $new_arrivals[1]; ?>
                    <div class="bg-gray-900 rounded-2xl overflow-hidden relative flex-1 min-h-[100px] flex items-end group card-anim fade-in-up delay-300">
                        <img src="<?= htmlspecialchars($p['primary_image'] ?: './src/assets/images/placeholder.png') ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="absolute inset-0 w-full h-full object-cover opacity-70 transition-all duration-500 group-hover:scale-105 group-hover:brightness-110" />
                        <div class="relative z-10 p-6 text-white">
                            <h4 class="text-lg font-bold mb-1"><?= htmlspecialchars($p['name']) ?></h4>
                            <p class="text-sm mb-3"><?= htmlspecialchars(mb_strimwidth($p['description'], 0, 50, '...')) ?></p>
                            <a href="product-view?product=<?= urlencode($p['product_id']) ?>" class="bg-white text-black font-semibold px-4 py-1 rounded hover:bg-gray-200 transition-all duration-300 transform hover:scale-105 text-sm">Shop Now</a>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="grid grid-cols-2 gap-6">
                    <?php for ($i = 2; $i <= 3; $i++): ?>
                        <?php if (!empty($new_arrivals[$i])): $p = $new_arrivals[$i]; ?>
                            <div class="bg-gray-900 rounded-2xl overflow-hidden relative flex flex-col justify-end min-h-[100px] group card-anim fade-in-up delay-<?= 400 + 100 * ($i-2) ?>">
                                <img src="<?= htmlspecialchars($p['primary_image'] ?: './src/assets/images/placeholder.png') ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="absolute inset-0 w-full h-full object-cover opacity-70 transition-all duration-500 group-hover:scale-105 group-hover:brightness-110" />
                                <div class="relative z-10 p-4 text-white">
                                    <h4 class="text-base font-bold mb-1"><?= htmlspecialchars($p['name']) ?></h4>
                                    <p class="text-xs mb-2"><?= htmlspecialchars(mb_strimwidth($p['description'], 0, 40, '...')) ?></p>
                                    <a href="product-view?product=<?= urlencode($p['product_id']) ?>" class="bg-white text-black font-semibold px-3 py-1 rounded hover:bg-gray-200 transition-all duration-300 transform hover:scale-105 text-xs">Shop Now</a>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </div>
</div>


<div class=" w-[100%]">
    <img src="./src/assets/images/The-future-is-here.gif" alt="" class="w-[100%]" srcset="">
</div>

<?php
    include 'explore_products.php';
?>




<!-- End New Arrivals UI -->
<style>
.fade-in-up {
    opacity: 0;
    transform: translateY(40px);
    transition: opacity 0.8s cubic-bezier(.4,2,.6,1), transform 0.8s cubic-bezier(.4,2,.6,1);
}
.fade-in-up.visible {
    opacity: 1 !important;
    transform: translateY(0) !important;
}
.fade-in-up.delay-100 { transition-delay: 0.1s; }
.fade-in-up.delay-200 { transition-delay: 0.2s; }
.fade-in-up.delay-300 { transition-delay: 0.3s; }
.fade-in-up.delay-400 { transition-delay: 0.4s; }
.fade-in-up.delay-500 { transition-delay: 0.5s; }
</style>
<script>
// Animate in on scroll
const fadeEls = document.querySelectorAll('.fade-in-up');
if (window.IntersectionObserver && fadeEls.length) {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.15 });
    fadeEls.forEach(el => observer.observe(el));
}
</script>
