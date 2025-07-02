<!-- Modern Cookie Acceptance Banner -->
<div id="cookie-banner" class="fixed inset-x-0 bottom-0 z-[100000] flex justify-center pointer-events-none">
    <div class="pointer-events-auto bg-white/95 border border-gray-200 shadow-xl rounded-2xl m-4 px-6 py-5 flex flex-col sm:flex-row items-center gap-4 max-w-2xl w-full fade-in-up transition-all duration-500" style="backdrop-filter: blur(8px);">
        <div class="flex-1 text-gray-800 text-center sm:text-left">
            <span class="font-bold text-lg mr-2"><i class="bi bi-shield-lock text-blue-500 mr-1"></i>Cookies & Privacy</span>
            <p class="text-sm mt-1 opacity-80">We use cookies to enhance your browsing experience, serve personalized ads or content, and analyze our traffic. By clicking "Accept", you consent to our use of cookies. <a href="/privacy" class="text-blue-500 underline hover:text-blue-700">Learn more</a>.</p>
        </div>
        <button id="accept-cookies" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-full shadow transition-all duration-300">Accept</button>
    </div>
</div>

<style>
.fade-in-up { opacity: 0; transform: translateY(40px); }
.fade-in-up.visible { opacity: 1 !important; transform: translateY(0) !important; }
</style>
<script>
(function() {
    const banner = document.getElementById('cookie-banner');
    const acceptBtn = document.getElementById('accept-cookies');
    // Show only if not accepted
    if (localStorage.getItem('kt_cookies_accepted') === 'true') {
        if (banner) banner.style.display = 'none';
    } else {
        if (banner) setTimeout(() => banner.querySelector('.fade-in-up').classList.add('visible'), 100);
    }
    if (acceptBtn) {
        acceptBtn.addEventListener('click', function() {
            localStorage.setItem('kt_cookies_accepted', 'true');
            if (banner) {
                banner.querySelector('.fade-in-up').classList.remove('visible');
                setTimeout(() => { banner.style.display = 'none'; }, 500);
            }
        });
    }
})();
</script>
