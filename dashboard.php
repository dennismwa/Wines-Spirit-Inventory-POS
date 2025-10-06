<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Wines & Spirits POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- In your page <head> -->
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="/assets/css/styles.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200 fixed top-0 left-0 right-0 z-40">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Left side -->
                <div class="flex items-center">
                    <button id="sidebarToggle" class="p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 lg:hidden">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <div class="flex items-center ml-3 lg:ml-0">
                        <div class="flex items-center justify-center w-10 h-10 bg-orange-600 rounded-full">
                            <i class="fas fa-wine-bottle text-white"></i>
                        </div>
                        <h1 class="ml-3 text-xl font-semibold text-gray-800 hidden sm:block">Wines & Spirits POS</h1>
                    </div>
                </div>

                <!-- Right side -->
                <div class="flex items-center space-x-3 sm:space-x-4">
                    <!-- Notifications -->
                    <button class="relative p-2 text-gray-600 hover:text-gray-900">
                        <i class="fas fa-bell text-xl"></i>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>

                    <!-- User dropdown -->
                    <div class="relative">
                        <button id="userDropdown" class="flex items-center space-x-2 text-sm text-gray-700 hover:text-gray-900">
                            <div class="w-8 h-8 bg-orange-600 rounded-full flex items-center justify-center">
                                <span class="text-white font-semibold">A</span>
                            </div>
                            <span class="hidden sm:block font-medium">Admin</span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        <!-- Dropdown menu -->
                        <div id="userMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 z-50">
                            <a href="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i> Profile
                            </a>
                            <a href="/settings" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-cog mr-2"></i> Settings
                            </a>
                            <hr class="my-1">
                            <a href="/logout" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar fixed left-0 top-16 bottom-0 w-64 bg-white border-r border-gray-200 transition-transform duration-300 z-30 lg:transform-none overflow-y-auto">
        <nav class="p-4 space-y-1">
            <a href="/dashboard" class="flex items-center space-x-3 px-3 py-2 rounded-lg bg-orange-50 text-orange-600">
                <i class="fas fa-home w-5"></i>
                <span>Dashboard</span>
            </a>
            <a href="/pos" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100">
                <i class="fas fa-cash-register w-5"></i>
                <span>POS</span>
            </a>
            <a href="/products" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100">
                <i class="fas fa-wine-glass-alt w-5"></i>
                <span>Products</span>
            </a>
            <a href="/inventory" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100">
                <i class="fas fa-warehouse w-5"></i>
                <span>Inventory</span>
            </a>
            <a href="/sales" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100">
                <i class="fas fa-chart-line w-5"></i>
                <span>Sales</span>
            </a>
            <a href="/reports" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100">
                <i class="fas fa-file-alt w-5"></i>
                <span>Reports</span>
            </a>
            <a href="/suppliers" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100">
                <i class="fas fa-truck w-5"></i>
                <span>Suppliers</span>
            </a>
            <a href="/users" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100">
                <i class="fas fa-users w-5"></i>
                <span>Users</span>
            </a>
            <a href="/settings" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100">
                <i class="fas fa-cog w-5"></i>
                <span>Settings</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="lg:ml-64 pt-16 pb-20 lg:pb-8">
        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <!-- Page Header -->
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Dashboard</h2>
                <p class="text-gray-600">Welcome back! Here's what's happening with your store today.</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Today's Sales -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Today's Sales</p>
                            <p class="text-2xl font-bold text-gray-800">KSh 45,280</p>
                            <p class="text-xs text-green-600 mt-1">
                                <i class="fas fa-arrow-up"></i> 12% from yesterday
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Products -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Total Products</p>
                            <p class="text-2xl font-bold text-gray-800">342</p>
                            <p class="text-xs text-blue-600 mt-1">
                                <i class="fas fa-plus"></i> 8 new this week
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-box text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Low Stock Items -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Low Stock</p>
                            <p class="text-2xl font-bold text-gray-800">12</p>
                            <p class="text-xs text-orange-600 mt-1">
                                <i class="fas fa-exclamation-triangle"></i> Needs attention
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-exclamation text-orange-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Today's Orders -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Orders Today</p>
                            <p class="text-2xl font-bold text-gray-800">28</p>
                            <p class="text-xs text-purple-600 mt-1">
                                <i class="fas fa-clock"></i> 3 pending
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-shopping-cart text-purple-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Sales Chart -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Sales Overview</h3>
                        <select class="text-sm border border-gray-300 rounded-lg px-3 py-1">
                            <option>Last 7 days</option>
                            <option>Last 30 days</option>
                            <option>Last 3 months</option>
                        </select>
                    </div>
                    <canvas id="salesChart" height="150"></canvas>
                </div>

                <!-- Top Products -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Top Selling Products</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gray-100 rounded-lg"></div>
                                <div>
                                    <p class="font-medium text-gray-800">Johnnie Walker Black</p>
                                    <p class="text-xs text-gray-500">28 units sold</p>
                                </div>
                            </div>
                            <span class="font-semibold text-gray-800">KSh 84,000</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gray-100 rounded-lg"></div>
                                <div>
                                    <p class="font-medium text-gray-800">Hennessy VS</p>
                                    <p class="text-xs text-gray-500">24 units sold</p>
                                </div>
                            </div>
                            <span class="font-semibold text-gray-800">KSh 72,000</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gray-100 rounded-lg"></div>
                                <div>
                                    <p class="font-medium text-gray-800">Glenfiddich 12</p>
                                    <p class="text-xs text-gray-500">22 units sold</p>
                                </div>
                            </div>
                            <span class="font-semibold text-gray-800">KSh 66,000</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gray-100 rounded-lg"></div>
                                <div>
                                    <p class="font-medium text-gray-800">Tusker Lager</p>
                                    <p class="text-xs text-gray-500">120 units sold</p>
                                </div>
                            </div>
                            <span class="font-semibold text-gray-800">KSh 24,000</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Sales Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Recent Sales</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Items</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Payment</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#INV001</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Walk-in Customer</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell">3</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">KSh 4,500</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">Cash</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Completed
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#INV002</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">John Doe</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell">5</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">KSh 12,800</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">M-Pesa</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Completed
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Mobile Bottom Navigation -->
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-40">
        <div class="grid grid-cols-5 gap-1">
            <a href="/dashboard" class="flex flex-col items-center py-2 text-orange-600">
                <i class="fas fa-home text-xl"></i>
                <span class="text-xs mt-1">Home</span>
            </a>
            <a href="/pos" class="flex flex-col items-center py-2 text-gray-600">
                <i class="fas fa-cash-register text-xl"></i>
                <span class="text-xs mt-1">POS</span>
            </a>
            <a href="/products" class="flex flex-col items-center py-2 text-gray-600">
                <i class="fas fa-box text-xl"></i>
                <span class="text-xs mt-1">Products</span>
            </a>
            <a href="/reports" class="flex flex-col items-center py-2 text-gray-600">
                <i class="fas fa-chart-bar text-xl"></i>
                <span class="text-xs mt-1">Reports</span>
            </a>
            <a href="/settings" class="flex flex-col items-center py-2 text-gray-600">
                <i class="fas fa-cog text-xl"></i>
                <span class="text-xs mt-1">Settings</span>
            </a>
        </div>
    </nav>

    <script>
        // Sidebar toggle
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            
            if (window.innerWidth < 1024 && 
                !sidebar.contains(event.target) && 
                !sidebarToggle.contains(event.target) &&
                sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
            }
        });

        // User dropdown
        document.getElementById('userDropdown').addEventListener('click', function() {
            document.getElementById('userMenu').classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('userDropdown');
            const menu = document.getElementById('userMenu');
            
            if (!dropdown.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });

        // Sales Chart
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Sales',
                    data: [32000, 28000, 35000, 42000, 38000, 45000, 45280],
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
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'KSh ' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'KSh ' + (value / 1000) + 'K';
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>