<?php
/**
 * includes/mobile-nav.php - Mobile Bottom Navigation
 */
?>
<nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-40">
    <div class="grid grid-cols-5 gap-1">
        <a href="/dashboard" class="flex flex-col items-center py-2 <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'text-orange-600' : 'text-gray-600'; ?>">
            <i class="fas fa-home text-xl"></i>
            <span class="text-xs mt-1">Home</span>
        </a>
        <a href="/pos" class="flex flex-col items-center py-2 <?php echo basename($_SERVER['PHP_SELF']) == 'pos.php' ? 'text-orange-600' : 'text-gray-600'; ?>">
            <i class="fas fa-cash-register text-xl"></i>
            <span class="text-xs mt-1">POS</span>
        </a>
        <a href="/products" class="flex flex-col items-center py-2 <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'text-orange-600' : 'text-gray-600'; ?>">
            <i class="fas fa-box text-xl"></i>
            <span class="text-xs mt-1">Products</span>
        </a>
        <a href="/reports" class="flex flex-col items-center py-2 <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'text-orange-600' : 'text-gray-600'; ?>">
            <i class="fas fa-chart-bar text-xl"></i>
            <span class="text-xs mt-1">Reports</span>
        </a>
        <a href="/settings" class="flex flex-col items-center py-2 <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'text-orange-600' : 'text-gray-600'; ?>">
            <i class="fas fa-cog text-xl"></i>
            <span class="text-xs mt-1">Settings</span>
        </a>
    </div>
</nav>
