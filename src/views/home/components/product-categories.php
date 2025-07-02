<?php
// Fetch all active categories and a featured product image for each
$categories = [];
$sql = "SELECT c.category_id, c.name, c.slug, c.description,
        (
            SELECT pi.image_url FROM products p
            JOIN product_images pi ON p.product_id = pi.product_id AND pi.is_primary = 1
            WHERE p.category_id = c.category_id AND p.is_active = 1 AND p.published = 'true'
            ORDER BY p.is_featured DESC, p.created_at DESC LIMIT 1
        ) AS image_url
        FROM categories c
        WHERE c.is_active = 1
        ORDER BY c.name ASC";
$result = mysqli_query($conn, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }
}
?>
<style>
.arc-slider-section {
    background: whitesmoke;
    min-height: 80vh;
    width: 100vw;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
    padding-top: 3rem;
    padding-bottom: 3rem;
}
.arc-slider-container {
    position: relative;
    width: 80vw;
    height: 540px;
    margin: 0 auto;
    display: flex;
    align-items: flex-end;
    justify-content: center;
}
.arc-category {
    position: absolute;
    left: 50%;
    bottom: 0;
    transform: translate(-50%, 0);
    transition: all 0.6s cubic-bezier(.4,2,.6,1);
    display: flex;
    flex-direction: column;
    align-items: center;
    cursor: pointer;
    opacity: 0.7;
    z-index: 1;
}
.arc-category.active {
    transform: translate(-50%, 0) scale(1.25) translateY(-32px);
    z-index: 10;
    opacity: 1;
    box-shadow: 0 8px 32px 0 #3a8dde22;
    border-radius: 0.7rem;
    background: #fff;
    padding: 1.5rem 2rem 2rem 2rem;
    border: 1px gray;
}
.arc-category img {
    width: 220px;
    height: 140px;
    object-fit: contain;
    background: #fff;
    box-shadow: 0 2px 16px 0 #3a8dde11;
    margin-bottom: 1rem;
}
.arc-category .cat-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #23272f;
    margin-bottom: 0.25rem;
    text-align: center;
}
.arc-category .cat-desc {
    font-size: 0.85rem;
    color: #555a64;
    text-align: center;
    max-width: 180px;
}
.arc-nav-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 20;
    width: 48px;
    height: 48px;
    background: #fff;
    color: #3a8dde;
    border-radius: 50%;
    box-shadow: 0 2px 12px 0 #3a8dde11;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    border: none;
    cursor: pointer;
    transition: background 0.2s, color 0.2s, transform 0.2s;
}
.arc-nav-btn:hover {
    background: #3a8dde;
    color: #fff;
    transform: scale(1.08) translateY(-50%);
}
.arc-nav-btn.left { left: 2vw; }
.arc-nav-btn.right { right: 2vw; }
</style>
<div class="arc-slider-section my-32">
    <button class="arc-nav-btn left" id="arc-prev">&#8592;</button>
    <div class="arc-slider-container py-5" id="arcSlider">
        <?php foreach ($categories as $i => $cat): ?>
            <div class="arc-category<?= $i === 0 ? ' active' : '' ?>"
                data-idx="<?= $i ?>"
                onclick="window.location.href='category?category=<?= urlencode($cat['slug']) ?>'">
                <?php if ($cat['image_url']): ?>
                    <img src="<?= htmlspecialchars($cat['image_url']) ?>" alt="<?= htmlspecialchars($cat['name']) ?>" class="rounded-lg" />
                <?php else: ?>
                    <div class="w-[220px] h-[140px] flex items-center justify-center bg-white rounded-lg mb-3">
                        <i class="bi bi-box text-5xl text-gray-400"></i>
                    </div>
                <?php endif; ?>
                <span class="cat-title"><?= htmlspecialchars($cat['name']) ?></span>
                <span class="cat-desc"><?= htmlspecialchars($cat['description']) ?></span>
            </div>
        <?php endforeach; ?>
    </div>
    <button class="arc-nav-btn right" id="arc-next">&#8594;</button>
</div>
<script>
const arcSlides = document.querySelectorAll('.arc-category');
const arcCount = arcSlides.length;
let arcCurrent = 0;
function updateArcSlider(idx) {
    const radiusX = 700; // much wider horizontally
    const radiusY = 120; // much less vertical for a flatter, lower arc
    const centerX = 0;
    const centerY = 0;
    const visible = 7; // how many categories to show on the arc (odd number, center is active)
    const angleStep = Math.PI / (visible - 1); // spread over 180deg
    let start = idx - Math.floor(visible / 2);
    arcSlides.forEach((slide, i) => {
        slide.classList.remove('active');
        let rel = i - idx;
        if (Math.abs(rel) > Math.floor(visible / 2)) {
            slide.style.opacity = 0;
            slide.style.pointerEvents = 'none';
            slide.style.transform = 'scale(0.7) translate(-50%, 32px)';
        } else {
            const angle = Math.PI - (rel + Math.floor(visible / 2)) * angleStep;
            const x = Math.cos(angle) * radiusX;
            const y = Math.sin(angle) * radiusY;
            slide.style.left = `calc(50% + ${x}px)`;
            slide.style.bottom = `${y}px`;
            slide.style.opacity = rel === 0 ? 1 : 0.8;
            slide.style.pointerEvents = 'auto';
            slide.style.zIndex = rel === 0 ? 10 : 5 - Math.abs(rel);
            slide.classList.toggle('active', rel === 0);
            slide.style.transform = rel === 0
                ? 'translate(-50%, 0) scale(1.25) translateY(-32px)'
                : 'translate(-50%, 0) scale(0.85) translateY(16px)';
        }
    });
    arcCurrent = idx;
}
document.getElementById('arc-prev').onclick = () => {
    let idx = (arcCurrent - 1 + arcCount) % arcCount;
    updateArcSlider(idx);
};
document.getElementById('arc-next').onclick = () => {
    let idx = (arcCurrent + 1) % arcCount;
    updateArcSlider(idx);
};
// Initialize
updateArcSlider(0);

// --- Animation on scroll into view ---
let arcSpinning = false;
let arcSpinTimeout = null;
let arcSpinInterval = null;
function startArcSpin(steps = 6, speed = 320) {
    if (arcSpinning) return;
    arcSpinning = true;
    let remaining = steps;
    arcSpinInterval = setInterval(() => {
        let idx = (arcCurrent + 1) % arcCount;
        updateArcSlider(idx);
        remaining--;
        if (remaining <= 0) {
            clearInterval(arcSpinInterval);
            arcSpinning = false;
        }
    }, speed);
}
// Intersection Observer to trigger spin
const arcSection = document.querySelector('.arc-slider-section');
let arcAnimated = false;
if (window.IntersectionObserver && arcSection) {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !arcAnimated) {
                arcAnimated = true;
                startArcSpin(6, 320); // 6 steps, smooth and slow
            }
        });
    }, { threshold: 0.3 });
    observer.observe(arcSection);
}
</script>
