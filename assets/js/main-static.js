/**
 * Main JavaScript File - Static Version
 * Professional Grocery Shop Management System
 * Client-side only (no backend required)
 */

// Global variables
let cart = [];
let wishlist = [];

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

// Initialize application
function initializeApp() {
    // Load cart and wishlist from localStorage
    loadCartFromStorage();
    loadWishlistFromStorage();
    
    // Update cart counter
    updateCartCounter();
    
    // Initialize tooltips
    initializeTooltips();
    
    // Initialize mobile menu
    initializeMobileMenu();
    
    // Initialize search functionality
    initializeSearch();
    
    // Initialize lazy loading for images
    initializeLazyLoading();
}

// Cart functions
function addToCart(productId, quantity = 1) {
    // Show loading
    showLoading();
    
    // Simulate API delay
    setTimeout(() => {
        hideLoading();
        
        const existingItem = cart.find(item => item.id === productId);
        
        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            cart.push({ id: productId, quantity: quantity });
        }
        
        saveCartToStorage();
        updateCartCounter();
        showToast('Product added to cart!', 'success');
    }, 300);
}

function removeFromCart(productId) {
    showLoading();
    
    setTimeout(() => {
        hideLoading();
        
        cart = cart.filter(item => item.id !== productId);
        saveCartToStorage();
        updateCartCounter();
        showToast('Product removed from cart', 'success');
    }, 300);
}

function updateCartQuantity(productId, quantity) {
    if (quantity < 1) {
        removeFromCart(productId);
        return;
    }
    
    const item = cart.find(item => item.id === productId);
    if (item) {
        item.quantity = quantity;
        saveCartToStorage();
        updateCartCounter();
    }
}

// Wishlist functions
function toggleWishlist(productId) {
    const isInWishlist = wishlist.includes(productId);
    
    if (isInWishlist) {
        wishlist = wishlist.filter(id => id !== productId);
        showToast('Removed from wishlist', 'success');
    } else {
        wishlist.push(productId);
        showToast('Added to wishlist!', 'success');
    }
    
    updateWishlistIcons();
    saveWishlistToStorage();
}

// Update cart counter
function updateCartCounter() {
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    const counters = document.querySelectorAll('.cart-counter, .cart-badge-mobile');
    counters.forEach(counter => {
        counter.textContent = totalItems;
        counter.style.display = totalItems > 0 ? 'flex' : 'none';
    });
}

// Update wishlist icons
function updateWishlistIcons() {
    const wishlistBtns = document.querySelectorAll('[data-wishlist]');
    wishlistBtns.forEach(btn => {
        const productId = parseInt(btn.dataset.wishlist);
        const icon = btn.querySelector('i');
        if (wishlist.includes(productId)) {
            icon.classList.remove('far');
            icon.classList.add('fas', 'text-danger');
        } else {
            icon.classList.remove('fas', 'text-danger');
            icon.classList.add('far');
        }
    });
}

// Search functionality
function initializeSearch() {
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');
    
    if (!searchInput) return;
    
    let searchTimeout;
    
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            if (searchResults) {
                searchResults.style.display = 'none';
            }
            return;
        }
        
        searchTimeout = setTimeout(() => {
            performSearch(query);
        }, 300);
    });
    
    // Hide search results when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && searchResults && !searchResults.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });
}

function performSearch(query) {
    const searchResults = document.getElementById('searchResults');
    if (!searchResults) return;
    
    // Mock search - filter products
    const filteredProducts = window.products ? window.products.filter(p => 
        p.name.toLowerCase().includes(query.toLowerCase()) ||
        p.description.toLowerCase().includes(query.toLowerCase())
    ) : [];
    
    if (filteredProducts.length > 0) {
        searchResults.innerHTML = filteredProducts.map(p => `
            <div class="search-result-item p-3 border-bottom">
                <strong>${p.name}</strong>
                <p class="text-muted small mb-0">${p.description.substring(0, 50)}...</p>
            </div>
        `).join('');
        searchResults.style.display = 'block';
    } else {
        searchResults.innerHTML = '<div class="p-3 text-muted">No products found</div>';
        searchResults.style.display = 'block';
    }
}

// Mobile menu
function initializeMobileMenu() {
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileSidebar = document.querySelector('.mobile-sidebar');
    const sidebarOverlay = document.querySelector('.mobile-sidebar-overlay');
    
    if (mobileMenuToggle && mobileSidebar) {
        mobileMenuToggle.addEventListener('click', function() {
            mobileSidebar.classList.toggle('show');
            sidebarOverlay.classList.toggle('show');
        });
    }
    
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            mobileSidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
        });
    }
}

// Toast notifications
function showToast(message, type = 'info') {
    const toastContainer = getOrCreateToastContainer();
    
    const toast = document.createElement('div');
    toast.className = `toast-notification toast-${type} fade-in`;
    
    const icon = getToastIcon(type);
    toast.innerHTML = `
        <div class="toast-content">
            <i class="${icon} me-2"></i>
            <span>${message}</span>
        </div>
        <button class="toast-close" onclick="closeToast(this)">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    toastContainer.appendChild(toast);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        closeToast(toast.querySelector('.toast-close'));
    }, 5000);
}

function getOrCreateToastContainer() {
    let container = document.getElementById('toastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
    return container;
}

function getToastIcon(type) {
    const icons = {
        success: 'fas fa-check-circle',
        error: 'fas fa-exclamation-circle',
        warning: 'fas fa-exclamation-triangle',
        info: 'fas fa-info-circle'
    };
    return icons[type] || icons.info;
}

function closeToast(button) {
    const toast = button.closest('.toast-notification');
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(100%)';
    setTimeout(() => {
        toast.remove();
    }, 300);
}

// Loading spinner
function showLoading() {
    let loader = document.getElementById('globalLoader');
    if (!loader) {
        loader = document.createElement('div');
        loader.id = 'globalLoader';
        loader.className = 'global-loader';
        loader.innerHTML = '<div class="spinner-glass"></div>';
        document.body.appendChild(loader);
    }
    loader.style.display = 'flex';
}

function hideLoading() {
    const loader = document.getElementById('globalLoader');
    if (loader) {
        loader.style.display = 'none';
    }
}

// Utility functions
function isLoggedIn() {
    return document.body.dataset.loggedIn === 'true';
}

function formatCurrency(amount) {
    return '৳ ' + parseFloat(amount).toLocaleString('en-BD', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

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

// Local storage functions
function loadCartFromStorage() {
    const stored = localStorage.getItem('cart');
    if (stored) {
        cart = JSON.parse(stored);
    }
}

function saveCartToStorage() {
    localStorage.setItem('cart', JSON.stringify(cart));
}

function loadWishlistFromStorage() {
    const stored = localStorage.getItem('wishlist');
    if (stored) {
        wishlist = JSON.parse(stored);
    }
}

function saveWishlistToStorage() {
    localStorage.setItem('wishlist', JSON.stringify(wishlist));
}

// Initialize tooltips
function initializeTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

// Initialize lazy loading
function initializeLazyLoading() {
    const images = document.querySelectorAll('img[data-src]');
    
    if ('IntersectionObserver' in window) {
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
    } else {
        // Fallback for older browsers
        images.forEach(img => {
            img.src = img.dataset.src;
            img.classList.remove('lazy');
        });
    }
}

// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;
    
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    return isValid;
}

// Smooth scrolling
function smoothScrollTo(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }
}

// Price filter
function filterByPrice(minPrice, maxPrice) {
    const products = document.querySelectorAll('.product-card');
    
    products.forEach(product => {
        const priceElement = product.querySelector('.product-price');
        if (priceElement) {
            const price = parseFloat(priceElement.dataset.price);
            if (price >= minPrice && price <= maxPrice) {
                product.style.display = 'block';
            } else {
                product.style.display = 'none';
            }
        }
    });
}

// Category filter
function filterByCategory(categoryId) {
    const products = document.querySelectorAll('.product-card');
    
    products.forEach(product => {
        const productCategory = product.dataset.category;
        if (categoryId === 'all' || productCategory === categoryId) {
            product.style.display = 'block';
        } else {
            product.style.display = 'none';
        }
    });
}

// Initialize on page load
window.addEventListener('load', function() {
    // Hide loading screen if exists
    const loadingScreen = document.getElementById('loadingScreen');
    if (loadingScreen) {
        loadingScreen.style.opacity = '0';
        setTimeout(() => {
            loadingScreen.style.display = 'none';
        }, 500);
    }
    
    // Initialize AOS (Animate On Scroll) if available
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true
        });
    }
});

// Handle back button
window.addEventListener('popstate', function(event) {
    // Handle browser back button if needed
    if (event.state && event.state.page) {
        // Load page content via AJAX if implementing SPA
    }
});

// Export functions for global use
window.addToCart = addToCart;
window.removeFromCart = removeFromCart;
window.updateCartQuantity = updateCartQuantity;
window.toggleWishlist = toggleWishlist;
window.showToast = showToast;
window.closeToast = closeToast;
window.showLoading = showLoading;
window.hideLoading = hideLoading;
window.validateForm = validateForm;
window.smoothScrollTo = smoothScrollTo;
window.filterByPrice = filterByPrice;
window.filterByCategory = filterByCategory;
