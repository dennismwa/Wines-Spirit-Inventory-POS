<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS - Wines & Spirits</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- In your page <head> -->
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="/assets/css/styles.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .product-grid { min-height: calc(100vh - 380px); }
        @media (max-width: 768px) {
            .sidebar-cart { transform: translateX(100%); }
            .sidebar-cart.active { transform: translateX(0); }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200 fixed top-0 left-0 right-0 z-40">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <a href="/dashboard" class="p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                        <i class="fas fa-arrow-left text-xl"></i>
                    </a>
                    <h1 class="ml-3 text-xl font-semibold text-gray-800">Point of Sale</h1>
                </div>

                <div class="flex items-center space-x-3">
                    <!-- Mobile Cart Toggle -->
                    <button id="cartToggle" class="lg:hidden relative p-2 text-gray-600 hover:text-gray-900">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <span id="cartCountMobile" class="absolute -top-1 -right-1 w-5 h-5 bg-orange-600 text-white text-xs rounded-full flex items-center justify-center">0</span>
                    </button>

                    <!-- User Info -->
                    <div class="flex items-center space-x-2 text-sm text-gray-700">
                        <div class="w-8 h-8 bg-orange-600 rounded-full flex items-center justify-center">
                            <span class="text-white font-semibold">A</span>
                        </div>
                        <span class="hidden sm:block font-medium">Admin</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="flex pt-16">
        <!-- Main Content -->
        <main class="flex-1 p-4 lg:pr-96">
            <!-- Search and Categories -->
            <div class="mb-4 space-y-4">
                <!-- Search Bar -->
                <div class="flex gap-2">
                    <div class="relative flex-1">
                        <input 
                            type="text" 
                            id="searchProduct"
                            placeholder="Search product or scan barcode..." 
                            class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-600 focus:border-transparent"
                        >
                        <i class="fas fa-search absolute left-3 top-4 text-gray-400"></i>
                    </div>
                    <button class="px-4 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        <i class="fas fa-barcode text-xl"></i>
                    </button>
                </div>

                <!-- Category Filters -->
                <div class="flex gap-2 overflow-x-auto pb-2">
                    <button class="px-4 py-2 bg-orange-600 text-white rounded-lg whitespace-nowrap">All Products</button>
                    <button class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 whitespace-nowrap">Whisky</button>
                    <button class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 whitespace-nowrap">Wine</button>
                    <button class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 whitespace-nowrap">Beer</button>
                    <button class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 whitespace-nowrap">Vodka</button>
                    <button class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 whitespace-nowrap">Gin</button>
                    <button class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 whitespace-nowrap">Rum</button>
                    <button class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 whitespace-nowrap">Soft Drinks</button>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="product-grid grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-3">
                <!-- Product Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden cursor-pointer hover:shadow-md transition-shadow" onclick="addToCart(1, 'Johnnie Walker Black', 3500)">
                    <div class="aspect-square bg-gray-100 flex items-center justify-center">
                        <i class="fas fa-wine-bottle text-4xl text-gray-400"></i>
                    </div>
                    <div class="p-3">
                        <h3 class="text-sm font-medium text-gray-900 truncate">Johnnie Walker Black</h3>
                        <p class="text-xs text-gray-500">750ml</p>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-lg font-bold text-orange-600">KSh 3,500</span>
                            <span class="text-xs text-green-600 bg-green-50 px-2 py-1 rounded">In Stock</span>
                        </div>
                    </div>
                </div>

                <!-- Product Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden cursor-pointer hover:shadow-md transition-shadow" onclick="addToCart(2, 'Hennessy VS', 4200)">
                    <div class="aspect-square bg-gray-100 flex items-center justify-center">
                        <i class="fas fa-wine-bottle text-4xl text-gray-400"></i>
                    </div>
                    <div class="p-3">
                        <h3 class="text-sm font-medium text-gray-900 truncate">Hennessy VS</h3>
                        <p class="text-xs text-gray-500">750ml</p>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-lg font-bold text-orange-600">KSh 4,200</span>
                            <span class="text-xs text-green-600 bg-green-50 px-2 py-1 rounded">In Stock</span>
                        </div>
                    </div>
                </div>

                <!-- Product Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden cursor-pointer hover:shadow-md transition-shadow" onclick="addToCart(3, 'Glenfiddich 12', 3800)">
                    <div class="aspect-square bg-gray-100 flex items-center justify-center">
                        <i class="fas fa-wine-bottle text-4xl text-gray-400"></i>
                    </div>
                    <div class="p-3">
                        <h3 class="text-sm font-medium text-gray-900 truncate">Glenfiddich 12</h3>
                        <p class="text-xs text-gray-500">750ml</p>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-lg font-bold text-orange-600">KSh 3,800</span>
                            <span class="text-xs text-green-600 bg-green-50 px-2 py-1 rounded">In Stock</span>
                        </div>
                    </div>
                </div>

                <!-- Product Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden cursor-pointer hover:shadow-md transition-shadow" onclick="addToCart(4, 'Tusker Lager', 200)">
                    <div class="aspect-square bg-gray-100 flex items-center justify-center">
                        <i class="fas fa-beer text-4xl text-gray-400"></i>
                    </div>
                    <div class="p-3">
                        <h3 class="text-sm font-medium text-gray-900 truncate">Tusker Lager</h3>
                        <p class="text-xs text-gray-500">500ml</p>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-lg font-bold text-orange-600">KSh 200</span>
                            <span class="text-xs text-green-600 bg-green-50 px-2 py-1 rounded">In Stock</span>
                        </div>
                    </div>
                </div>

                <!-- Product Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden cursor-pointer hover:shadow-md transition-shadow" onclick="addToCart(5, 'Absolut Vodka', 2800)">
                    <div class="aspect-square bg-gray-100 flex items-center justify-center">
                        <i class="fas fa-wine-bottle text-4xl text-gray-400"></i>
                    </div>
                    <div class="p-3">
                        <h3 class="text-sm font-medium text-gray-900 truncate">Absolut Vodka</h3>
                        <p class="text-xs text-gray-500">750ml</p>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-lg font-bold text-orange-600">KSh 2,800</span>
                            <span class="text-xs text-green-600 bg-green-50 px-2 py-1 rounded">In Stock</span>
                        </div>
                    </div>
                </div>

                <!-- Product Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden cursor-pointer hover:shadow-md transition-shadow" onclick="addToCart(6, 'Baileys Irish Cream', 2500)">
                    <div class="aspect-square bg-gray-100 flex items-center justify-center">
                        <i class="fas fa-wine-bottle text-4xl text-gray-400"></i>
                    </div>
                    <div class="p-3">
                        <h3 class="text-sm font-medium text-gray-900 truncate">Baileys Irish Cream</h3>
                        <p class="text-xs text-gray-500">750ml</p>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-lg font-bold text-orange-600">KSh 2,500</span>
                            <span class="text-xs text-green-600 bg-green-50 px-2 py-1 rounded">In Stock</span>
                        </div>
                    </div>
                </div>

                <!-- Product Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden cursor-pointer hover:shadow-md transition-shadow" onclick="addToCart(7, 'Captain Morgan Spiced', 2200)">
                    <div class="aspect-square bg-gray-100 flex items-center justify-center">
                        <i class="fas fa-wine-bottle text-4xl text-gray-400"></i>
                    </div>
                    <div class="p-3">
                        <h3 class="text-sm font-medium text-gray-900 truncate">Captain Morgan Spiced</h3>
                        <p class="text-xs text-gray-500">750ml</p>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-lg font-bold text-orange-600">KSh 2,200</span>
                            <span class="text-xs text-orange-600 bg-orange-50 px-2 py-1 rounded">Low Stock</span>
                        </div>
                    </div>
                </div>

                <!-- Product Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden cursor-pointer hover:shadow-md transition-shadow" onclick="addToCart(8, 'Red Label', 1800)">
                    <div class="aspect-square bg-gray-100 flex items-center justify-center">
                        <i class="fas fa-wine-bottle text-4xl text-gray-400"></i>
                    </div>
                    <div class="p-3">
                        <h3 class="text-sm font-medium text-gray-900 truncate">Red Label</h3>
                        <p class="text-xs text-gray-500">750ml</p>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-lg font-bold text-orange-600">KSh 1,800</span>
                            <span class="text-xs text-green-600 bg-green-50 px-2 py-1 rounded">In Stock</span>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Cart Sidebar -->
        <aside id="cartSidebar" class="sidebar-cart fixed right-0 top-16 bottom-0 w-full lg:w-96 bg-white border-l border-gray-200 transition-transform duration-300 lg:transform-none z-30 flex flex-col">
            <!-- Cart Header -->
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-800">Shopping Cart</h2>
                    <button id="closeCart" class="lg:hidden p-2 text-gray-600 hover:text-gray-900">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="p-4 border-b border-gray-200">
                <div class="flex gap-2">
                    <input 
                        type="text" 
                        placeholder="Customer Name (Optional)" 
                        class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm"
                    >
                    <input 
                        type="text" 
                        placeholder="Phone" 
                        class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm"
                    >
                </div>
            </div>

            <!-- Cart Items -->
            <div class="flex-1 overflow-y-auto p-4">
                <div id="cartItems" class="space-y-3">
                    <!-- Empty Cart Message -->
                    <div id="emptyCart" class="text-center py-8 text-gray-500">
                        <i class="fas fa-shopping-cart text-4xl mb-3"></i>
                        <p>Your cart is empty</p>
                    </div>
                </div>
            </div>

            <!-- Cart Summary -->
            <div class="border-t border-gray-200 p-4 bg-gray-50">
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal</span>
                        <span id="subtotal">KSh 0</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Tax (16%)</span>
                        <span id="tax">KSh 0</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Discount</span>
                        <input 
                            type="number" 
                            id="discount"
                            placeholder="0" 
                            class="w-24 px-2 py-1 border border-gray-300 rounded text-right text-sm"
                            onchange="updateTotals()"
                        >
                    </div>
                    <div class="flex justify-between text-lg font-bold border-t pt-2">
                        <span>Total</span>
                        <span id="total">KSh 0</span>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="mb-3">
                    <label class="text-sm font-medium text-gray-700 mb-1 block">Payment Method</label>
                    <div class="grid grid-cols-3 gap-2">
                        <button class="px-3 py-2 bg-orange-600 text-white rounded-lg text-sm" onclick="setPaymentMethod('cash')">
                            <i class="fas fa-money-bill-wave"></i> Cash
                        </button>
                        <button class="px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm" onclick="setPaymentMethod('mpesa')">
                            <i class="fas fa-mobile-alt"></i> M-Pesa
                        </button>
                        <button class="px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm" onclick="setPaymentMethod('card')">
                            <i class="fas fa-credit-card"></i> Card
                        </button>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="grid grid-cols-2 gap-2">
                    <button onclick="clearCart()" class="px-4 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium">
                        Clear Cart
                    </button>
                    <button onclick="processSale()" class="px-4 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-medium">
                        Pay Now
                    </button>
                </div>
            </div>
        </aside>
    </div>

    <!-- Payment Modal -->
    <div id="paymentModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <h3 class="text-lg font-semibold mb-4">Process Payment</h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Total Amount</label>
                    <input type="text" id="modalTotal" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50" readonly>
                </div>
                
                <div id="cashPayment">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cash Received</label>
                    <input type="number" id="cashReceived" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Enter amount" onkeyup="calculateChange()">
                </div>
                
                <div id="mpesaPayment" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1">M-Pesa Code</label>
                    <input type="text" id="mpesaCode" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Enter transaction code">
                </div>
                
                <div id="changeDisplay" class="bg-gray-50 p-3 rounded-lg">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Change</span>
                        <span id="changeAmount" class="font-bold text-lg">KSh 0</span>
                    </div>
                </div>
            </div>
            
            <div class="flex gap-3 mt-6">
                <button onclick="closePaymentModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Cancel
                </button>
                <button onclick="completeSale()" class="flex-1 px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
                    Complete Sale
                </button>
            </div>
        </div>
    </div>

    <script>
        let cart = [];
        let paymentMethod = 'cash';

        // Mobile cart toggle
        document.getElementById('cartToggle').addEventListener('click', () => {
            document.getElementById('cartSidebar').classList.toggle('active');
        });

        document.getElementById('closeCart').addEventListener('click', () => {
            document.getElementById('cartSidebar').classList.remove('active');
        });

        // Add to cart function
        function addToCart(id, name, price) {
            const existingItem = cart.find(item => item.id === id);
            
            if (existingItem) {
                existingItem.quantity++;
            } else {
                cart.push({ id, name, price, quantity: 1 });
            }
            
            updateCartDisplay();
            updateTotals();
        }

        // Update cart display
        function updateCartDisplay() {
            const cartItemsDiv = document.getElementById('cartItems');
            const emptyCart = document.getElementById('emptyCart');
            
            if (cart.length === 0) {
                cartItemsDiv.innerHTML = '<div id="emptyCart" class="text-center py-8 text-gray-500"><i class="fas fa-shopping-cart text-4xl mb-3"></i><p>Your cart is empty</p></div>';
                document.getElementById('cartCountMobile').textContent = '0';
                return;
            }
            
            let html = '';
            let totalItems = 0;
            
            cart.forEach(item => {
                totalItems += item.quantity;
                html += `
                    <div class="bg-gray-50 rounded-lg p-3">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-medium text-gray-800">${item.name}</h4>
                            <button onclick="removeFromCart(${item.id})" class="text-red-500 hover:text-red-700">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <button onclick="updateQuantity(${item.id}, -1)" class="w-7 h-7 bg-white border border-gray-300 rounded flex items-center justify-center hover:bg-gray-100">
                                    <i class="fas fa-minus text-xs"></i>
                                </button>
                                <span class="w-8 text-center">${item.quantity}</span>
                                <button onclick="updateQuantity(${item.id}, 1)" class="w-7 h-7 bg-white border border-gray-300 rounded flex items-center justify-center hover:bg-gray-100">
                                    <i class="fas fa-plus text-xs"></i>
                                </button>
                            </div>
                            <span class="font-semibold">KSh ${(item.price * item.quantity).toLocaleString()}</span>
                        </div>
                    </div>
                `;
            });
            
            cartItemsDiv.innerHTML = html;
            document.getElementById('cartCountMobile').textContent = totalItems;
        }

        // Update quantity
        function updateQuantity(id, change) {
            const item = cart.find(item => item.id === id);
            if (item) {
                item.quantity += change;
                if (item.quantity <= 0) {
                    removeFromCart(id);
                } else {
                    updateCartDisplay();
                    updateTotals();
                }
            }
        }

        // Remove from cart
        function removeFromCart(id) {
            cart = cart.filter(item => item.id !== id);
            updateCartDisplay();
            updateTotals();
        }

        // Clear cart
        function clearCart() {
            if (confirm('Are you sure you want to clear the cart?')) {
                cart = [];
                updateCartDisplay();
                updateTotals();
            }
        }

        // Update totals
        function updateTotals() {
            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const tax = subtotal * 0.16;
            const discount = parseFloat(document.getElementById('discount').value) || 0;
            const total = subtotal + tax - discount;
            
            document.getElementById('subtotal').textContent = `KSh ${subtotal.toLocaleString()}`;
            document.getElementById('tax').textContent = `KSh ${tax.toLocaleString()}`;
            document.getElementById('total').textContent = `KSh ${total.toLocaleString()}`;
        }

        // Set payment method
        function setPaymentMethod(method) {
            paymentMethod = method;
            
            // Update button styles
            document.querySelectorAll('.grid-cols-3 button').forEach(btn => {
                btn.className = 'px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm';
            });
            
            event.target.className = 'px-3 py-2 bg-orange-600 text-white rounded-lg text-sm';
        }

        // Process sale
        function processSale() {
            if (cart.length === 0) {
                alert('Cart is empty!');
                return;
            }
            
            const total = calculateTotal();
            document.getElementById('modalTotal').value = `KSh ${total.toLocaleString()}`;
            
            // Show/hide payment fields based on method
            if (paymentMethod === 'cash') {
                document.getElementById('cashPayment').classList.remove('hidden');
                document.getElementById('mpesaPayment').classList.add('hidden');
                document.getElementById('changeDisplay').classList.remove('hidden');
            } else if (paymentMethod === 'mpesa') {
                document.getElementById('cashPayment').classList.add('hidden');
                document.getElementById('mpesaPayment').classList.remove('hidden');
                document.getElementById('changeDisplay').classList.add('hidden');
            }
            
            document.getElementById('paymentModal').classList.remove('hidden');
        }

        // Calculate total
        function calculateTotal() {
            const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            const tax = subtotal * 0.16;
            const discount = parseFloat(document.getElementById('discount').value) || 0;
            return subtotal + tax - discount;
        }

        // Calculate change
        function calculateChange() {
            const total = calculateTotal();
            const received = parseFloat(document.getElementById('cashReceived').value) || 0;
            const change = received - total;
            
            document.getElementById('changeAmount').textContent = `KSh ${Math.max(0, change).toLocaleString()}`;
        }

        // Complete sale
        function completeSale() {
            // Here you would send the sale data to the server
            alert('Sale completed successfully! Receipt printed.');
            
            // Clear cart and close modal
            cart = [];
            updateCartDisplay();
            updateTotals();
            closePaymentModal();
            
            // Close mobile cart if open
            document.getElementById('cartSidebar').classList.remove('active');
        }

        // Close payment modal
        function closePaymentModal() {
            document.getElementById('paymentModal').classList.add('hidden');
            document.getElementById('cashReceived').value = '';
            document.getElementById('mpesaCode').value = '';
            document.getElementById('changeAmount').textContent = 'KSh 0';
        }

        // Search functionality
        document.getElementById('searchProduct').addEventListener('keyup', function(e) {
            // Implement search/filter logic here
            console.log('Searching for:', e.target.value);
        });

        // Initialize
        updateCartDisplay();
        updateTotals();
    </script>
</body>
</html>