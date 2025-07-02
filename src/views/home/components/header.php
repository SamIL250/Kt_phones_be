<?php
// Standalone query to fetch featured products (is_featured=1)
$featured_products = [];
$sql = "SELECT p.product_id, p.name, p.description, pi.image_url, b.name AS brand_name
        FROM products p
        JOIN brands b ON p.brand_id = b.brand_id
        JOIN product_images pi ON p.product_id = pi.product_id AND pi.is_primary = 1
        WHERE p.is_active = 1 AND p.published = 'true' AND p.is_featured = 1
        ORDER BY p.created_at DESC
        LIMIT 5";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $featured_products[] = $row;
    }
}
// Fetch categories for navigation
$categories = [];
$cat_sql = "SELECT name, slug FROM categories WHERE is_active = 1 ORDER BY name ASC";
$cat_result = mysqli_query($conn, $cat_sql);
if ($cat_result) {
    while ($row = mysqli_fetch_assoc($cat_result)) {
        $categories[] = $row;
    }
}
?>
<style>
/* Only keep custom CSS for 3D carousel effect */
.hero-carousel-3d {
    perspective: 1200px;
    width: 100vw;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    position: absolute;
    top: 0; left: 0;
    z-index: 2;
    pointer-events: none;
}
.hero-carousel-3d-inner {
    width: 600px;
    height: 400px;
    position: relative;
    transform-style: preserve-3d;
    transition: transform 1s cubic-bezier(.4,2,.6,1);
}
.hero-carousel-3d-slide {
    position: absolute;
    top: 0; left: 0;
    width: 600px;
    height: 400px;
    opacity: 0.5;
    filter: blur(14px) grayscale(0.5);
    transition: transform 1s, opacity 0.5s, filter 0.5s;
    will-change: transform, opacity, filter;
    pointer-events: auto;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1;
}
.hero-carousel-3d-slide.active {
    opacity: 1;
    filter: none;
    z-index: 3;
}
</style>

<div class="w-screen min-h-screen bg-white flex flex-col relative overflow-hidden">

    <!-- Swiper CSS CDN -->
   
    <div class="w-full mx-auto mb-6">
        <div class="swiper mySwiper overflow-hidden">
            <div class="swiper-wrapper">
                <div class="swiper-slide"><img src="./src/assets/images/552406The-future-is-here-ezgif.com-jpg-to-avif-converter.avif" alt="Slide 1" class="w-full h-48 sm:h-64 object-cover" /></div>
                <div class="swiper-slide"><img src="./src/assets/images/Get-more-wad-ezgif.com-jpg-to-avif-converter.avif" alt="Slide 2" class="w-full h-48 sm:h-64 object-cover" /></div>
                <div class="swiper-slide"><img src="./src/assets/images/Visit-Our-Store-ezgif.com-jpg-to-avif-converter.avif" alt="Slide 3" class="w-full h-48 sm:h-64 object-cover" /></div>
            </div>
            <div class="swiper-pagination"></div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </div>
    <!-- Swiper JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script>
      var swiper = new Swiper('.mySwiper', {
        loop: true,
        pagination: {
          el: '.swiper-pagination',
          clickable: true,
        },
        navigation: {
          nextEl: '.swiper-button-next',
          prevEl: '.swiper-button-prev',
        },
        autoplay: {
          delay: 3500,
          disableOnInteraction: false,
        },
        slidesPerView: 1,
        spaceBetween: 0,
      });
    </script>

    <!-- 3D Carousel Container -->
    <div class="relative min-h-[100vh]">
    <div class="hero-carousel-3d mt-10">
        <div class="hero-carousel-3d-inner" id="heroCarousel3D">
            <?php foreach ($featured_products as $i => $product): ?>
                <div class="hero-carousel-3d-slide<?= $i === 0 ? ' active' : '' ?>" data-slide="<?= $i ?>">
                    <img class="max-w-[340px] max-h-[340px] w-full h-auto object-contain bg-transparent" src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" />
                    <div class="relative z-10 ml-12 mb-12 max-w-xl text-left">
                        <!-- <h2 class="text-2xl font-light tracking-wide text-blue-500 mb-2 opacity-80"><?= htmlspecialchars($product['brand_name']) ?></h2> -->
                        <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4 leading-tight tracking-tight"><?= htmlspecialchars($product['name']) ?></h1>
                        <a href="product-view?product=<?= urlencode($product['product_id']) ?>" class="inline-block mt-2 bg-blue-500 text-white font-semibold rounded-full px-5 py-0 text-base shadow-md hover:bg-white hover:text-blue-500 border-2 border-transparent hover:border-blue-500 transition">Buy Now</a>
                        <!-- <p class="text-base md:text-lg text-gray-500 mb-6 max-w-md"><?= htmlspecialchars($product['description']) ?></p> -->
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <!-- Carousel Controls -->
    <div class="absolute left-0 bottom-12 w-full flex items-center justify-between z-20 px-12">
        <button class="w-14 h-14 bg-gray-100 text-blue-500 rounded-full flex items-center justify-center text-2xl shadow hover:bg-blue-500 hover:text-white transition border-none" id="hero-prev"><span>&#8592;</span></button>
        <div class="flex items-center gap-3">
            <?php foreach ($featured_products as $i => $product): ?>
                <div class="transition-all duration-300 rounded bg-blue-100" style="width:<?= $i === 0 ? '32px' : '16px' ?>;height:6px;background:<?= $i === 0 ? '#3a8dde' : '#3a8dde22' ?>;margin-right:6px;"></div>
            <?php endforeach; ?>
        </div>
        <button class="w-14 h-14 bg-gray-100 text-blue-500 rounded-full flex items-center justify-center text-2xl shadow hover:bg-blue-500 hover:text-white transition border-none" id="hero-next"><span>&#8594;</span></button>
    </div>
    </div>
</div>
<script>
// 3D Carousel Effect (basic, can be replaced with Three.js or a 3D library for more advanced effects)
const slides = document.querySelectorAll('.hero-carousel-3d-slide');
const dots = document.querySelectorAll('.absolute.left-0.bottom-12 .flex.items-center.gap-3 > div');
const inner = document.getElementById('heroCarousel3D');
let currentSlide = 0;
const total = slides.length;
function update3DCarousel(idx) {
    const angle = 360 / total;
    slides.forEach((slide, i) => {
        const offset = i - idx;
        const theta = offset * angle;
        slide.style.transform = `rotateY(${theta}deg) translateZ(600px)`;
        slide.classList.toggle('active', i === idx);
    });
    dots.forEach((dot, i) => {
        dot.style.background = i === idx ? '#3a8dde' : '#3a8dde22';
        dot.style.width = i === idx ? '32px' : '16px';
    });
    currentSlide = idx;
}
document.getElementById('hero-prev').onclick = () => {
    let idx = (currentSlide - 1 + total) % total;
    update3DCarousel(idx);
};
document.getElementById('hero-next').onclick = () => {
    let idx = (currentSlide + 1) % total;
    update3DCarousel(idx);
};
dots.forEach((dot, i) => {
    dot.onclick = () => update3DCarousel(i);
});
// Optional: auto-advance
setInterval(() => {
    let idx = (currentSlide + 1) % total;
    update3DCarousel(idx);
}, 6000);
// Initialize
update3DCarousel(0);
</script>

