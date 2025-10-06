<?php
/**
 * includes/sidebar.php - Sidebar Navigation
 */
?>
<aside id="sidebar" class="sidebar fixed left-0 top-16 bottom-0 w-64 bg-white border-r border-gray-200 transition-transform duration-300 z-30 lg:transform-none overflow-y-auto">
    <nav class="p-4 space-y-1">
        <a href="/dashboard" class="flex items-center space-x-3 px-3 py-2 rounded-lg <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'bg-orange-50 text-orange-600' : 'text-gray-700 hover:bg-gray-100'; ?>">
            <i class="fas fa-home w-5"></i>
            <span>Dashboard</span>
        </a>
        <a href="/pos" class="flex items-center space-x-3 px-3 py-2 rounded-lg <?php echo basename($_SERVER['PHP_SELF']) == 'pos.php' ? 'bg-orange-50 text-orange-600' : 'text-gray-700 hover:bg-gray-100'; ?>">
            <i class="fas fa-cash-register w-5"></i>
            <span>POS</span>
        </a>
        <a href="/products" class="flex items-center space-x-3 px-3 py-2 rounded-lg <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'bg-orange-50 text-orange-600' : 'text-gray-700 hover:bg-gray-100'; ?>">
            <i class="fas fa-wine-glass-alt w-5"></i>
            <span>Products</span>
        </a>
        <a href="/inventory" class="flex items-center space-x-3 px-3 py-2 rounded-lg <?php echo basename($_SERVER['PHP_SELF']) == 'inventory.php' ? 'bg-orange-50 text-orange-600' : 'text-gray-700 hover:bg-gray-100'; ?>">
            <i class="fas fa-warehouse w-5"></i>
            <span>Inventory</span>
        </a>
        <a href="/sales" class="flex items-center space-x-3 px-3 py-2 rounded-lg <?php echo basename($_SERVER['PHP_SELF']) == 'sales.php' ? 'bg-orange-50 text-orange-600' : 'text-gray-700 hover:bg-gray-100'; ?>">
            <i class="fas fa-chart-line w-5"></i>
            <span>Sales</span>
        </a>
        <a href="/reports" class="flex items-center space-x-3 px-3 py-2 rounded-lg <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'bg-orange-50 text-orange-600' : 'text-gray-700 hover:bg-gray-100'; ?>">
            <i class="fas fa-file-alt w-5"></i>
            <span>Reports</span>
        </a>
        <a href="/suppliers" class="flex items-center space-x-3 px-3 py-2 rounded-lg <?php echo basename($_SERVER['PHP_SELF']) == 'suppliers.php' ? 'bg-orange-50 text-orange-600' : 'text-gray-700 hover:bg-gray-100'; ?>">
            <i class="fas fa-truck w-5"></i>
            <span>Suppliers</span>
        </a>
        
        <?php if (isOwner()): ?>
        <a href="/users" class="flex items-center space-x-3 px-3 py-2 rounded-lg <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'bg-orange-50 text-orange-600' : 'text-gray-700 hover:bg-gray-100'; ?>">
            <i class="fas fa-users w-5"></i>
            <span>Users</span>
        </a>
        <?php endif; ?>
        
        <a href="/settings" class="flex items-center space-x-3 px-3 py-2 rounded-lg <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'bg-orange-50 text-orange-600' : 'text-gray-700 hover:bg-gray-100'; ?>">
            <i class="fas fa-cog w-5"></i>
            <span>Settings</span>
        </a>
    </nav>
</aside>