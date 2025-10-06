/**
 * Wines & Spirits POS System
 * Main JavaScript File
 */

// Global variables
let cart = [];
let currentUser = null;
let settings = {};

// DOM Ready
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

// Initialize Application
function initializeApp() {
    // Load user data
    loadUserData();
    
    // Load settings
    loadSettings();
    
    // Initialize components
    initSidebar();
    initMobileNav();
    initDropdowns();
    initModals();
    initTooltips();
    initCharts();
    
    // Check session
    checkSession();
    
    // Initialize page-specific features
    const currentPage = window.location.pathname.split('/').pop();
    initPageFeatures(currentPage);
}

// Initialize page-specific features
function initPageFeatures(page) {
    switch(page) {
        case 'dashboard.php':
            initDashboard();
            break;
        case 'pos.php':
            initPOS();
            break;
        case 'products.php':
            initProducts();
            break;
        case 'reports.php':
            initReports();
            break;
        case 'settings.php':
            initSettings();
            break;
    }
}

// Sidebar Management
function initSidebar() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            if (window.innerWidth < 1024 && 
                !sidebar.contains(event.target) && 
                !sidebarToggle.contains(event.target) &&
                sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
            }
        });
    }
}

// Mobile Navigation
function initMobileNav() {
    const mobileNav = document.querySelector('.mobile-nav');
    if (mobileNav) {
        // Add active class to current page
        const currentPath = window.location.pathname;
        mobileNav.querySelectorAll('a').forEach(link => {
            if (link.getAttribute('href') === currentPath) {
                link.classList.add('active');
            }
        });
    }
}

// Dropdown Menus
function initDropdowns() {
    const dropdowns = document.querySelectorAll('[data-dropdown]');
    
    dropdowns.forEach(dropdown => {
        const trigger = dropdown.querySelector('[data-dropdown-trigger]');
        const menu = dropdown.querySelector('[data-dropdown-menu]');
        
        if (trigger && menu) {
            trigger.addEventListener('click', function(e) {
                e.stopPropagation();
                menu.classList.toggle('hidden');
            });
        }
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function() {
        document.querySelectorAll('[data-dropdown-menu]').forEach(menu => {
            menu.classList.add('hidden');
        });
    });
}

// Modal Management
function initModals() {
    // Open modal
    document.querySelectorAll('[data-modal-open]').forEach(button => {
        button.addEventListener('click', function() {
            const modalId = this.getAttribute('data-modal-open');
            openModal(modalId);
        });
    });
    
    // Close modal
    document.querySelectorAll('[data-modal-close]').forEach(button => {
        button.addEventListener('click', function() {
            const modalId = this.getAttribute('data-modal-close');
            closeModal(modalId);
        });
    });
    
    // Close on backdrop click
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal(this.id);
            }
        });
    });
}

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }
}

// POS Functions
function initPOS() {
    loadProducts();
    initBarcodeScan();
    initPaymentMethods();
    updateCartDisplay();
}

function addToCart(productId, name, price, quantity = 1) {
    const existingItem = cart.find(item => item.id === productId);
    
    if (existingItem) {
        existingItem.quantity += quantity;
    } else {
        cart.push({
            id: productId,
            name: name,
            price: parseFloat(price),
            quantity: quantity
        });
    }
    
    updateCartDisplay();
    showNotification('Product added to cart', 'success');
}

function removeFromCart(productId) {
    cart = cart.filter(item => item.id !== productId);
    updateCartDisplay();
}

function updateQuantity(productId, change) {
    const item = cart.find(item => item.id === productId);
    if (item) {
        item.quantity += change;
        if (item.quantity <= 0) {
            removeFromCart(productId);
        } else {
            updateCartDisplay();
        }
    }
}

function clearCart() {
    if (confirm('Are you sure you want to clear the cart?')) {
        cart = [];
        updateCartDisplay();
    }
}

function updateCartDisplay() {
    const cartItemsDiv = document.getElementById('cartItems');
    const cartCount = document.getElementById('cartCount');
    const cartCountMobile = document.getElementById('cartCountMobile');
    
    if (!cartItemsDiv) return;
    
    if (cart.length === 0) {
        cartItemsDiv.innerHTML = `
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-shopping-cart text-4xl mb-3"></i>
                <p>Your cart is empty</p>
            </div>
        `;
        if (cartCount) cartCount.textContent = '0';
        if (cartCountMobile) cartCountMobile.textContent = '0';
    } else {
        let html = '';
        let totalItems = 0;
        
        cart.forEach(item => {
            totalItems += item.quantity;
            const itemTotal = item.price * item.quantity;
            
            html += `
                <div class="bg-gray-50 rounded-lg p-3 mb-3">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-medium text-gray-800">${escapeHtml(item.name)}</h4>
                        <button onclick="removeFromCart(${item.id})" class="text-red-500 hover:text-red-700">
                            <i class="fas fa-trash text-sm"></i>
                        </button>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <button onclick="updateQuantity(${item.id}, -1)" 
                                class="w-7 h-7 bg-white border border-gray-300 rounded flex items-center justify-center hover:bg-gray-100">
                                <i class="fas fa-minus text-xs"></i>
                            </button>
                            <span class="w-8 text-center">${item.quantity}</span>
                            <button onclick="updateQuantity(${item.id}, 1)" 
                                class="w-7 h-7 bg-white border border-gray-300 rounded flex items-center justify-center hover:bg-gray-100">
                                <i class="fas fa-plus text-xs"></i>
                            </button>
                        </div>
                        <span class="font-semibold">${formatCurrency(itemTotal)}</span>
                    </div>
                </div>
            `;
        });
        
        cartItemsDiv.innerHTML = html;
        if (cartCount) cartCount.textContent = totalItems;
        if (cartCountMobile) cartCountMobile.textContent = totalItems;
    }
    
    updateTotals();
}

function updateTotals() {
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const taxRate = parseFloat(settings.tax_rate || 16) / 100;
    const tax = subtotal * taxRate;
    const discount = parseFloat(document.getElementById('discount')?.value || 0);
    const total = subtotal + tax - discount;
    
    const subtotalEl = document.getElementById('subtotal');
    const taxEl = document.getElementById('tax');
    const totalEl = document.getElementById('total');
    
    if (subtotalEl) subtotalEl.textContent = formatCurrency(subtotal);
    if (taxEl) taxEl.textContent = formatCurrency(tax);
    if (totalEl) totalEl.textContent = formatCurrency(total);
}

function processSale() {
    if (cart.length === 0) {
        showNotification('Cart is empty!', 'error');
        return;
    }
    
    openModal('paymentModal');
    const total = calculateTotal();
    document.getElementById('modalTotal').value = formatCurrency(total);
}

function calculateTotal() {
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const taxRate = parseFloat(settings.tax_rate || 16) / 100;
    const tax = subtotal * taxRate;
    const discount = parseFloat(document.getElementById('discount')?.value || 0);
    return subtotal + tax - discount;
}

function completeSale() {
    // Prepare sale data
    const saleData = {
        items: cart.map(item => ({
            product_id: item.id,
            quantity: item.quantity,
            price: item.price
        })),
        customer_name: document.getElementById('customerName')?.value || '',
        customer_phone: document.getElementById('customerPhone')?.value || '',
        payment_method: getSelectedPaymentMethod(),
        payment_reference: document.getElementById('paymentReference')?.value || '',
        discount: parseFloat(document.getElementById('discount')?.value || 0),
        paid_amount: parseFloat(document.getElementById('cashReceived')?.value || calculateTotal())
    };
    
    // Send to server
    fetch('/ajax/sale.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            action: 'create',
            sale_data: saleData
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Sale completed successfully!', 'success');
            printReceipt(data.receipt);
            clearCart();
            closeModal('paymentModal');
        } else {
            showNotification(data.message || 'Error processing sale', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error processing sale', 'error');
    });
}

// Product Management
function initProducts() {
    // Initialize DataTable if present
    if (typeof $ !== 'undefined' && $.fn.DataTable) {
        $('#productsTable').DataTable({
            responsive: true,
            pageLength: 25,
            order: [[0, 'asc']],
            language: {
                search: 'Search products:'
            }
        });
    }
}

function loadProducts() {
    fetch('/ajax/product.php?action=list')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayProducts(data.products);
            }
        })
        .catch(error => console.error('Error loading products:', error));
}

function displayProducts(products) {
    const container = document.getElementById('productsGrid');
    if (!container) return;
    
    let html = '';
    products.forEach(product => {
        const stockStatus = product.quantity > 0 ? 
            '<span class="text-xs text-green-600 bg-green-50 px-2 py-1 rounded">In Stock</span>' :
            '<span class="text-xs text-red-600 bg-red-50 px-2 py-1 rounded">Out of Stock</span>';
        
        html += `
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden cursor-pointer hover:shadow-md transition-shadow" 
                 onclick="addToCart(${product.id}, '${escapeHtml(product.name)}', ${product.selling_price})">
                <div class="aspect-square bg-gray-100 flex items-center justify-center">
                    <i class="fas fa-wine-bottle text-4xl text-gray-400"></i>
                </div>
                <div class="p-3">
                    <h3 class="text-sm font-medium text-gray-900 truncate">${escapeHtml(product.name)}</h3>
                    <p class="text-xs text-gray-500">${product.unit}</p>
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-lg font-bold text-orange-600">${formatCurrency(product.selling_price)}</span>
                        ${stockStatus}
                    </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

// Charts
function initCharts() {
    // Initialize dashboard charts if Chart.js is loaded
    if (typeof Chart !== 'undefined') {
        initSalesChart();
        initCategoryChart();
    }
}

function initSalesChart() {
    const ctx = document.getElementById('salesChart');
    if (!ctx) return;
    
    new Chart(ctx.getContext('2d'), {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Sales',
                data: [32000, 28000, 35000, 42000, 38000, 45000, 48000],
                borderColor: '#ea580c',
                backgroundColor: 'rgba(234, 88, 12, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return formatCurrency(value);
                        }
                    }
                }
            }
        }
    });
}

// Utility Functions
function formatCurrency(amount) {
    const symbol = settings.currency_symbol || 'KSh';
    return `${symbol} ${parseFloat(amount).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    })}`;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-20 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full`;
    
    // Set color based on type
    switch(type) {
        case 'success':
            notification.classList.add('bg-green-500', 'text-white');
            break;
        case 'error':
            notification.classList.add('bg-red-500', 'text-white');
            break;
        case 'warning':
            notification.classList.add('bg-yellow-500', 'text-white');
            break;
        default:
            notification.classList.add('bg-blue-500', 'text-white');
    }
    
    notification.innerHTML = `
        <div class="flex items-center">
            <span>${escapeHtml(message)}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

function printReceipt(receiptHtml) {
    const printWindow = window.open('', '_blank', 'width=400,height=600');
    printWindow.document.write(`
        <html>
        <head>
            <title>Receipt</title>
            <style>
                body { font-family: monospace; padding: 20px; }
                .receipt { max-width: 300px; margin: 0 auto; }
            </style>
        </head>
        <body>
            <div class="receipt">${receiptHtml}</div>
            <script>
                window.onload = function() { 
                    window.print(); 
                    window.onafterprint = function() { window.close(); }
                }
            </script>
        </body>
        </html>
    `);
}

// Session Management
function checkSession() {
    // Check session every 5 minutes
    setInterval(() => {
        fetch('/ajax/check_session.php')
            .then(response => response.json())
            .then(data => {
                if (!data.valid) {
                    window.location.href = '/index.php?timeout=1';
                }
            })
            .catch(error => console.error('Session check error:', error));
    }, 5 * 60 * 1000);
}

// Load user data
function loadUserData() {
    const userDataEl = document.getElementById('userData');
    if (userDataEl) {
        try {
            currentUser = JSON.parse(userDataEl.textContent);
        } catch (e) {
            console.error('Error loading user data:', e);
        }
    }
}

// Load settings
function loadSettings() {
    const settingsEl = document.getElementById('appSettings');
    if (settingsEl) {
        try {
            settings = JSON.parse(settingsEl.textContent);
        } catch (e) {
            console.error('Error loading settings:', e);
        }
    }
}

// Barcode Scanner
function initBarcodeScan() {
    let barcodeBuffer = '';
    let barcodeTimeout;
    
    document.addEventListener('keydown', function(e) {
        // Check if we're in a text input
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
            return;
        }
        
        // Clear buffer after 100ms of inactivity
        clearTimeout(barcodeTimeout);
        barcodeTimeout = setTimeout(() => {
            barcodeBuffer = '';
        }, 100);
        
        // Enter key processes the barcode
        if (e.key === 'Enter' && barcodeBuffer.length > 0) {
            processBarcode(barcodeBuffer);
            barcodeBuffer = '';
        } else if (e.key.length === 1) {
            barcodeBuffer += e.key;
        }
    });
}

function processBarcode(barcode) {
    fetch(`/ajax/product.php?action=get_by_barcode&barcode=${barcode}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.product) {
                addToCart(data.product.id, data.product.name, data.product.selling_price);
            } else {
                showNotification('Product not found', 'error');
            }
        })
        .catch(error => console.error('Barcode lookup error:', error));
}

// Payment Methods
function initPaymentMethods() {
    document.querySelectorAll('[data-payment-method]').forEach(button => {
        button.addEventListener('click', function() {
            const method = this.getAttribute('data-payment-method');
            setPaymentMethod(method);
        });
    });
}

function setPaymentMethod(method) {
    // Update UI
    document.querySelectorAll('[data-payment-method]').forEach(button => {
        button.classList.remove('bg-orange-600', 'text-white');
        button.classList.add('bg-white', 'border-gray-300');
    });
    
    const selectedButton = document.querySelector(`[data-payment-method="${method}"]`);
    if (selectedButton) {
        selectedButton.classList.remove('bg-white', 'border-gray-300');
        selectedButton.classList.add('bg-orange-600', 'text-white');
    }
    
    // Show/hide payment fields
    document.querySelectorAll('[data-payment-field]').forEach(field => {
        field.classList.add('hidden');
    });
    
    const methodField = document.querySelector(`[data-payment-field="${method}"]`);
    if (methodField) {
        methodField.classList.remove('hidden');
    }
}

function getSelectedPaymentMethod() {
    const selected = document.querySelector('[data-payment-method].bg-orange-600');
    return selected ? selected.getAttribute('data-payment-method') : 'cash';
}

// Initialize tooltips
function initTooltips() {
    // Simple tooltip implementation
    document.querySelectorAll('[data-tooltip]').forEach(element => {
        element.addEventListener('mouseenter', function() {
            const tooltip = document.createElement('div');
            tooltip.className = 'absolute z-50 px-2 py-1 text-xs text-white bg-gray-900 rounded';
            tooltip.textContent = this.getAttribute('data-tooltip');
            tooltip.style.top = '-30px';
            tooltip.style.left = '50%';
            tooltip.style.transform = 'translateX(-50%)';
            this.style.position = 'relative';
            this.appendChild(tooltip);
        });
        
        element.addEventListener('mouseleave', function() {
            const tooltip = this.querySelector('.absolute');
            if (tooltip) tooltip.remove();
        });
    });
}

// Export functions for global use
window.addToCart = addToCart;
window.removeFromCart = removeFromCart;
window.updateQuantity = updateQuantity;
window.clearCart = clearCart;
window.processSale = processSale;
window.completeSale = completeSale;
window.openModal = openModal;
window.closeModal = closeModal;
window.showNotification = showNotification;
window.formatCurrency = formatCurrency;