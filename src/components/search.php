<div class="relative w-[100%] lg:w-[60%]">
    <form id="search-form" class="flex-1 mx-0 md:mx-8 max-w-full md:max-w-2xl flex items-center bg-gray-100 rounded-full border border-gray-200 px-3 sm:px-4 py-2 focus-within:ring-2 focus-within:ring-blue-400 transition mt-2 md:mt-0 order-3 md:order-none w-full md:w-auto" action="/search" method="get" autocomplete="off">
        <i class="bi bi-search text-gray-400 text-lg sm:text-xl mr-2"></i>
        <input style="outline: none;" id="search-input" type="text" name="q" placeholder="Search for products, brands, categories..." class="flex-1 border-none outline-none bg-transparent text-gray-800 placeholder-gray-400 text-sm sm:text-base min-w-0" autocomplete="off" />
        <span class="ml-2 bg-blue-500 hover:bg-blue-600 text-white px-3 sm:px-5 py-2 rounded-full font-semibold shadow transition text-sm sm:text-base">Search</button>
    </form>
    <div id="search-results-dropdown" class="absolute left-0 right-0 mt-2 bg-white border border-gray-200 rounded-xl shadow-lg z-50 hidden">
        <div id="search-loading" class="flex items-center justify-center py-6 text-blue-500 hidden">
            <span class="spinner-border animate-spin inline-block w-6 h-6 border-4 rounded-full border-blue-400 border-t-transparent"></span>
            <span class="ml-2">Searching...</span>
        </div>
        <ul id="search-results-list" class="divide-y divide-gray-100"></ul>
        <div id="search-more-less" class="flex justify-center py-2"></div>
    </div>
</div>
<script>
(function() {
    const input = document.getElementById('search-input');
    const dropdown = document.getElementById('search-results-dropdown');
    const resultsList = document.getElementById('search-results-list');
    const loading = document.getElementById('search-loading');
    const moreLess = document.getElementById('search-more-less');
    let results = [];
    let showingAll = false;
    let lastQuery = '';
    let totalResults = 0;
    let debounceTimeout;

    function renderResults(items, total) {
        resultsList.innerHTML = '';
        if (!items.length) {
            resultsList.innerHTML = '<li class="py-6 text-center text-gray-400">No results found</li>';
            moreLess.innerHTML = '';
            return;
        }
        items.forEach(item => {
            resultsList.innerHTML += `
                <li class="flex items-center gap-4 px-4 py-3 hover:bg-blue-50 cursor-pointer transition" onclick="window.location='product-view?product=${item.product_id}'">
                    <img src="${item.primary_image}" alt="${item.product_name}" class="w-12 h-12 object-cover rounded-md border border-gray-200 flex-shrink-0" />
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-gray-800 truncate">${item.product_name}</div>
                        <div class="text-xs text-gray-500 truncate">${item.brand_name || ''} ${item.category_name ? '· ' + item.category_name : ''}</div>
                        <div class="text-xs text-blue-500 font-bold mt-1">${item.discount_price > 0 ? Number(item.discount_price).toLocaleString() : Number(item.base_price).toLocaleString()} Frw</div>
                    </div>
                    <div class="flex flex-col items-end ml-2">
                        <span class="text-yellow-400 text-sm flex items-center">${'★'.repeat(Math.round(item.avg_rating))}${'☆'.repeat(5-Math.round(item.avg_rating))}</span>
                        <span class="text-xs text-gray-400">${item.num_reviews} reviews</span>
                    </div>
                </li>
            `;
        });
        // More/Less button
        if (total > items.length) {
            moreLess.innerHTML = `<button type="button" class="text-blue-500 hover:underline font-semibold" id="show-more-btn">Show more</button>`;
        } else if (items.length > 5) {
            moreLess.innerHTML = `<button type="button" class="text-blue-500 hover:underline font-semibold" id="show-less-btn">Show less</button>`;
        } else {
            moreLess.innerHTML = '';
        }
    }

    function showDropdown() {
        dropdown.classList.remove('hidden');
    }
    function hideDropdown() {
        dropdown.classList.add('hidden');
    }
    function setLoading(isLoading) {
        loading.classList.toggle('hidden', !isLoading);
        resultsList.classList.toggle('hidden', isLoading);
        moreLess.classList.toggle('hidden', isLoading);
    }

    async function fetchResults(query, limit = 5) {
        setLoading(true);
        try {
            const res = await fetch(`./src/services/products/filter.php?search=${encodeURIComponent(query)}&page=1&limit=${limit}`);
            const data = await res.json();
            results = data.results || [];
            totalResults = typeof data.total === 'number' ? data.total : results.length;
            renderResults(results, totalResults);
            showDropdown();
        } catch (e) {
            console.log(e);
            resultsList.innerHTML = '<li class="py-6 text-center text-gray-400">Error searching</li>';
            moreLess.innerHTML = '';
            showDropdown();
        } finally {
            setLoading(false);
        }
    }

    input.addEventListener('input', function(e) {
        const query = e.target.value.trim();
        lastQuery = query;
        showingAll = false;
        if (debounceTimeout) clearTimeout(debounceTimeout);
        if (!query) {
            hideDropdown();
            return;
        }
        debounceTimeout = setTimeout(() => fetchResults(query, 5), 250);
    });

    // Show more/less logic
    moreLess.addEventListener('click', function(e) {
        if (e.target.id === 'show-more-btn') {
            showingAll = true;
            fetchResults(lastQuery, 20); // Show up to 20
        } else if (e.target.id === 'show-less-btn') {
            showingAll = false;
            fetchResults(lastQuery, 5);
        }
    });

    // Hide dropdown on outside click
    document.addEventListener('click', function(e) {
        if (!dropdown.contains(e.target) && e.target !== input) {
            hideDropdown();
        }
    });
    // Show dropdown on focus if there are results
    input.addEventListener('focus', function() {
        if (results.length) showDropdown();
    });
})();
</script>
<style>
.spinner-border { border-top-color: transparent !important; border-right-color: #3b82f6 !important; border-bottom-color: #3b82f6 !important; border-left-color: #3b82f6 !important; }
</style>