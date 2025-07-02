<!-- src/views/home/components/member_cta.php -->
<?php
if (!$is_logged_in) {
?>
    <div class="max-w-7xl mx-auto px-4 py-16 bg-white flex flex-col md:flex-row items-center justify-center md:justify-between gap-8 my-10">
        <!-- 3D CTA Card -->
        <div class="w-full md:w-1/2 flex justify-center md:justify-start">
            <div class="member-cta-3d relative group transition-transform duration-300 will-change-transform" style="perspective: 1200px;">
                <img src="./src/assets/images/light_30.png" alt="observe-card Become a Member Illustration" class="max-w-xs md:max-w-sm lg:max-w-md h-auto object-contain member-cta-anim opacity-0 translate-y-8 transition-all duration-700" />
            </div>
        </div>
        <!-- Text content and button on the right -->
        <div class="w-full md:w-1/2 text-center md:text-left flex flex-col items-center md:items-start">
            <h2 class="text-3xl lg:text-4xl font-bold text-gray-800 mb-4 leading-tight member-cta-anim opacity-0 translate-y-8 transition-all duration-700">
                Want to have the <span class="observe-card text-blue-600">ultimate customer experience?</span>
                <br>Become a <span class="observe-card text-blue-600">member</span> today!
            </h2>
            <a href="signup" class="observe-card inline-flex items-center bg-blue-500 text-white px-8 py-3 text-lg font-semibold hover:bg-blue-600 transition-colors duration-300 shadow-md member-cta-btn member-cta-anim opacity-0 translate-y-8 transition-all duration-700 relative overflow-hidden">
                Sign up <i class="bi bi-chevron-right ml-2"></i>
                <span class="member-cta-pulse"></span>
            </a>
        </div>
    </div>
<?php
}
?>

<!-- Modern Newsletter Signup and Promotional Sections -->
<?php if (!$is_logged_in): ?>
    <!-- Newsletter Signup Section -->
    <div class="bg-[whitesmoke] py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center text-gray-700">
                <h2 class="text-4xl font-bold mb-4 text-gray-800">Stay Updated with KT Phones</h2>
                <p class="text-xl mb-8 opacity-90 max-w-2xl mx-auto text-gray-600">
                    Get exclusive offers, new product alerts, and tech tips delivered to your inbox.
                    Join thousands of satisfied customers!
                </p>
                <div class="flex flex-col sm:flex-row gap-4 max-w-md mx-auto">
                    <input type="email" placeholder="Enter your email address"
                        class="flex-1 px-6 py-4 rounded-lg text-gray-800 font-medium focus:outline-none focus:ring-2 focus:ring-yellow-400 bg-white border border-gray-300">
                    <button class="bg-gray-800 hover:bg-gray-900 text-white font-bold px-8 py-4 rounded-lg transition-colors duration-300 transform hover:scale-105">
                        Subscribe
                    </button>
                </div>
                <p class="text-sm opacity-75 mt-4 text-gray-500">No spam, unsubscribe at any time</p>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class=" w-[100%]">
    <img src="./src/assets/images/495608Smart-Saless-Recovered.gif" alt="" class="w-[100%]" srcset="">
</div>

<!-- Promotional Banners Section -->
<div class="bg-gray-50 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Left Banner - Special Offer -->
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-green-500 to-emerald-600 p-8 h-80">
                <div class="absolute inset-0 bg-black/10"></div>
                <div class="relative z-10 h-full flex flex-col justify-between">
                    <div class="text-white">
                        <span class="inline-block bg-white/20 backdrop-blur-sm text-white px-4 py-2 rounded-full text-sm font-medium mb-4">
                            Special Offer
                        </span>
                        <h3 class="text-3xl font-bold mb-2">Get <span class="text-yellow-300">20% Off</span></h3>
                        <p class="text-lg opacity-90 mb-6">On all premium smartphones</p>
                        <div class="flex items-center gap-4 mb-6">
                            <div class="bg-white/20 backdrop-blur-sm rounded-lg p-3">
                                <i class="bi bi-truck text-2xl"></i>
                            </div>
                            <div>
                                <p class="font-semibold">Free Shipping</p>
                                <p class="text-sm opacity-80">On orders over 500,000 Frw</p>
                            </div>
                        </div>
                    </div>
                    <button onclick="window.location.replace('store')"
                        class="bg-white text-green-600 font-bold px-6 py-3 rounded-lg hover:bg-gray-100 transition-colors duration-300 w-fit">
                        Shop Now
                    </button>
                </div>
                <!-- Background Image -->
                <div class="absolute top-0 right-0 w-1/2 h-full opacity-20">
                    <img src="./src/assets/images/iPhone-15-pro-max.png" alt="iPhone" class="w-full h-full object-contain">
                </div>
            </div>
            <!-- Right Banner - New Arrivals -->
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 p-8 h-80">
                <div class="absolute inset-0 bg-black/10"></div>
                <div class="relative z-10 h-full flex flex-col justify-between">
                    <div class="text-white">
                        <span class="inline-block bg-white/20 backdrop-blur-sm text-white px-4 py-2 rounded-full text-sm font-medium mb-4">
                            New Arrivals
                        </span>
                        <h3 class="text-3xl font-bold mb-2">Latest <span class="text-yellow-300">Smartphones</span></h3>
                        <p class="text-lg opacity-90 mb-6">Discover cutting-edge technology</p>
                        <div class="flex items-center gap-4 mb-6">
                            <div class="bg-white/20 backdrop-blur-sm rounded-lg p-3">
                                <i class="bi bi-shield-check text-2xl"></i>
                            </div>
                            <div>
                                <p class="font-semibold">1 Year Warranty</p>
                                <p class="text-sm opacity-80">Full manufacturer warranty</p>
                            </div>
                        </div>
                    </div>
                    <button onclick="window.location.replace('store')"
                        class="bg-white text-blue-600 font-bold px-6 py-3 rounded-lg hover:bg-gray-100 transition-colors duration-300 w-fit">
                        Explore More
                    </button>
                </div>
                <!-- Background Image -->
                <div class="absolute top-0 right-0 w-1/2 h-full opacity-20">
                    <img src="./src/assets/images/iphone16.png" alt="Latest Smartphone" class="w-full h-full object-contain">
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.member-cta-3d {
    background: linear-gradient(120deg, #f8fafc 60%, #e0e7ef 100%);
    border-radius: 2rem;
    box-shadow: 0 8px 32px 0 #3a8dde22, 0 1.5px 8px 0 #23272f11;
    transition: box-shadow 0.3s, transform 0.3s;
    will-change: transform;
    padding: 2.5rem 2rem 2rem 2rem;
    min-width: 320px;
    min-height: 320px;
    display: flex;
    align-items: center;
    justify-content: center;
    perspective: 1200px;
}
.member-cta-3d:hover {
    box-shadow: 0 16px 48px 0 #3a8dde33, 0 2.5px 16px 0 #23272f22;
    transform: scale(1.04);
}
.member-cta-btn {
    position: relative;
    overflow: hidden;
    z-index: 1;
}
.member-cta-pulse {
    position: absolute;
    left: 50%;
    top: 50%;
    width: 120%;
    height: 120%;
    background: radial-gradient(circle, #3a8dde33 0%, transparent 70%);
    opacity: 0;
    transform: translate(-50%, -50%) scale(0.7);
    pointer-events: none;
    transition: opacity 0.4s, transform 0.4s;
    z-index: 0;
}
.member-cta-btn:hover .member-cta-pulse {
    opacity: 1;
    transform: translate(-50%, -50%) scale(1.1);
}
.member-cta-anim {
    opacity: 0;
    transform: translateY(32px);
    transition: opacity 0.7s cubic-bezier(.4,2,.6,1), transform 0.7s cubic-bezier(.4,2,.6,1);
}
.member-cta-anim.visible {
    opacity: 1 !important;
    transform: translateY(0) !important;
}
</style>
<script>
// 3D tilt effect for the card
const cta3d = document.querySelector('.member-cta-3d');
if (cta3d) {
    cta3d.addEventListener('mousemove', (e) => {
        const rect = cta3d.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        const centerX = rect.width / 2;
        const centerY = rect.height / 2;
        const rotateX = ((y - centerY) / centerY) * 10;
        const rotateY = ((x - centerX) / centerX) * 16;
        cta3d.style.transform = `rotateX(${-rotateX}deg) rotateY(${rotateY}deg) scale(1.04)`;
    });
    cta3d.addEventListener('mouseleave', () => {
        cta3d.style.transform = 'rotateX(0deg) rotateY(0deg) scale(1)';
    });
}
// Animate in on scroll
const ctaAnimEls = document.querySelectorAll('.member-cta-anim');
const ctaSection = cta3d?.closest('.max-w-7xl');
if (window.IntersectionObserver && ctaSection) {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                ctaAnimEls.forEach((el, i) => {
                    setTimeout(() => el.classList.add('visible'), i * 180);
                });
                observer.disconnect();
            }
        });
    }, { threshold: 0.2 });
    observer.observe(ctaSection);
}
</script>