

// Modern JavaScript enhancements for KT Phones

// Hide preloader when page is loaded
window.addEventListener('load', function () {
  const preloader = document.getElementById('preloader');
  if (preloader) {
    preloader.style.opacity = '0';
    setTimeout(() => {
      preloader.style.display = 'none';
    }, 300);
  }
});

// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function (e) {
    e.preventDefault();
    const target = document.querySelector(this.getAttribute('href'));
    if (target) {
      target.scrollIntoView({
        behavior: 'smooth',
        block: 'start'
      });
    }
  });
});

// Intersection Observer for animations
const observerOptions = {
  threshold: 0.1,
  rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add('animate-fade-in-up');
    }
  });
}, observerOptions);

// Observe elements with animation classes
document.addEventListener('DOMContentLoaded', function () {
  const animatedElements = document.querySelectorAll('.observe-card, .card-hover');
  animatedElements.forEach(el => {
    observer.observe(el);
  });
});

// Product card hover effects
document.querySelectorAll('.group').forEach(card => {
  card.addEventListener('mouseenter', function () {
    this.style.transform = 'translateY(-8px)';
    this.style.boxShadow = '0 20px 25px -5px rgba(0, 0, 0, 0.1)';
  });

  card.addEventListener('mouseleave', function () {
    this.style.transform = 'translateY(0)';
    this.style.boxShadow = '0 4px 6px -1px rgba(0, 0, 0, 0.1)';
  });
});

// Newsletter subscription
document.addEventListener('DOMContentLoaded', function () {
  const newsletterForm = document.querySelector('input[type="email"]');
  const subscribeBtn = document.querySelector('button:contains("Subscribe")');

  if (newsletterForm && subscribeBtn) {
    subscribeBtn.addEventListener('click', function (e) {
      e.preventDefault();
      const email = newsletterForm.value;

      if (email && isValidEmail(email)) {
        // Show success message
        showToast('Thank you for subscribing!', 'success');
        newsletterForm.value = '';
      } else {
        showToast('Please enter a valid email address.', 'error');
      }
    });
  }
});

// Email validation
function isValidEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
}

// Toast notification system
function showToast(message, type = 'info') {
  const toast = document.createElement('div');
  toast.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full`;

  const colors = {
    success: 'bg-green-500 text-white',
    error: 'bg-red-500 text-white',
    warning: 'bg-yellow-500 text-white',
    info: 'bg-blue-500 text-white'
  };

  toast.className += ` ${colors[type] || colors.info}`;
  toast.textContent = message;

  document.body.appendChild(toast);

  // Animate in
  setTimeout(() => {
    toast.classList.remove('translate-x-full');
  }, 100);

  // Remove after 3 seconds
  setTimeout(() => {
    toast.classList.add('translate-x-full');
    setTimeout(() => {
      document.body.removeChild(toast);
    }, 300);
  }, 3000);
}

// Lazy loading for images
document.addEventListener('DOMContentLoaded', function () {
  const images = document.querySelectorAll('img[data-src]');

  const imageObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const img = entry.target;
        img.src = img.dataset.src;
        img.classList.remove('lazy');
        imageObserver.unobserve(img);
      }
    });
  });

  images.forEach(img => imageObserver.observe(img));
});

// Search functionality enhancement
document.addEventListener('DOMContentLoaded', function () {
  const searchInput = document.querySelector('input[type="search"]');
  if (searchInput) {
    let searchTimeout;

    searchInput.addEventListener('input', function () {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
        // Implement search logic here
        console.log('Searching for:', this.value);
      }, 300);
    });
  }
});

// Add to cart animation
function addToCartAnimation(productId) {
  const cartIcon = document.querySelector('#cart-count');
  if (cartIcon) {
    cartIcon.classList.add('animate-pulse');
    setTimeout(() => {
      cartIcon.classList.remove('animate-pulse');
    }, 1000);
  }
}

// Wishlist animation
function addToWishlistAnimation(productId) {
  const wishlistIcon = document.querySelector('#wishlist-count');
  if (wishlistIcon) {
    wishlistIcon.classList.add('animate-pulse');
    setTimeout(() => {
      wishlistIcon.classList.remove('animate-pulse');
    }, 1000);
  }
}

// Mobile menu enhancement
document.addEventListener('DOMContentLoaded', function () {
  const mobileMenuToggle = document.getElementById('mobileMenuToggle');
  const mobileMenu = document.getElementById('mobileMenu');

  if (mobileMenuToggle && mobileMenu) {
    mobileMenuToggle.addEventListener('click', function () {
      mobileMenu.classList.toggle('hidden');

      // Animate menu items
      const menuItems = mobileMenu.querySelectorAll('a');
      menuItems.forEach((item, index) => {
        if (!mobileMenu.classList.contains('hidden')) {
          item.style.animationDelay = `${index * 0.1}s`;
          item.classList.add('animate-slide-in-left');
        } else {
          item.classList.remove('animate-slide-in-left');
        }
      });
    });
  }
});

// Back to top button
function createBackToTopButton() {
  const backToTop = document.createElement('button');
  backToTop.innerHTML = '<i class="bi bi-arrow-up"></i>';
  backToTop.className = 'fixed bottom-6 right-6 w-12 h-12 bg-blue-600 text-white rounded-full shadow-lg hover:bg-blue-700 transition-all duration-300 opacity-0 invisible z-40';
  backToTop.id = 'backToTop';

  backToTop.addEventListener('click', function () {
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  });

  document.body.appendChild(backToTop);

  // Show/hide based on scroll position
  window.addEventListener('scroll', function () {
    if (window.pageYOffset > 300) {
      backToTop.classList.remove('opacity-0', 'invisible');
    } else {
      backToTop.classList.add('opacity-0', 'invisible');
    }
  });
}

// Initialize back to top button
document.addEventListener('DOMContentLoaded', createBackToTopButton);

// Product image gallery (if needed)
function initProductGallery() {
  const productImages = document.querySelectorAll('.product-image');
  const mainImage = document.querySelector('.main-product-image');

  if (productImages.length && mainImage) {
    productImages.forEach(img => {
      img.addEventListener('click', function () {
        mainImage.src = this.src;
        productImages.forEach(i => i.classList.remove('border-blue-500'));
        this.classList.add('border-blue-500');
      });
    });
  }
}

// Initialize product gallery
document.addEventListener('DOMContentLoaded', initProductGallery);

// Performance optimization: Debounce function
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

// Optimize scroll events
const optimizedScrollHandler = debounce(function () {
  // Handle scroll events here
}, 16); // ~60fps

window.addEventListener('scroll', optimizedScrollHandler);

// Console welcome message
console.log('%c🚀 Welcome to KT Phones!', 'color: #2563eb; font-size: 20px; font-weight: bold;');
console.log('%cPremium smartphones, exceptional service.', 'color: #6b7280; font-size: 14px;');