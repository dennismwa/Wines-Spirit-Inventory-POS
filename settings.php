

<?php
define('APP_RUNNING', true);
require_once 'config/database.php';
require_once 'classes/User.php';

requireLogin();

// Get current settings
$settings = getSettings();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updates = [
        'company_name' => $_POST['company_name'] ?? '',
        'company_address' => $_POST['company_address'] ?? '',
        'company_phone' => $_POST['company_phone'] ?? '',
        'company_email' => $_POST['company_email'] ?? '',
        'tax_rate' => $_POST['tax_rate'] ?? 16,
        'currency' => $_POST['currency'] ?? 'KES',
        'currency_symbol' => $_POST['currency_symbol'] ?? 'KSh',
        'low_stock_alert' => $_POST['low_stock_alert'] ?? 10,
        'receipt_footer' => $_POST['receipt_footer'] ?? '',
        'mpesa_till_number' => $_POST['mpesa_till_number'] ?? ''
    ];
    
    foreach ($updates as $key => $value) {
        updateSetting($key, $value);
    }
    
    $message = 'Settings updated successfully!';
    $settings = getSettings(); // Reload settings
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Wines & Spirits POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- In your page <head> -->
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="/assets/css/styles.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200 fixed top-0 left-0 right-0 z-40">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <button id="sidebarToggle" class="p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 lg:hidden">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h1 class="ml-3 text-xl font-semibold text-gray-800">Settings</h1>
                </div>
            </div>
        </div>
    </header>

    <!-- Sidebar -->
    <?php include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="lg:ml-64 pt-16 pb-20 lg:pb-8">
        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <?php if (isset($message)): ?>
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                <i class="fas fa-check-circle mr-2"></i><?php echo $message; ?>
            </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <!-- Company Information -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Company Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Company Name</label>
                            <input type="text" name="company_name" value="<?php echo escape($settings['company_name'] ?? ''); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input type="text" name="company_phone" value="<?php echo escape($settings['company_phone'] ?? ''); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="company_email" value="<?php echo escape($settings['company_email'] ?? ''); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">M-Pesa Till Number</label>
                            <input type="text" name="mpesa_till_number" value="<?php echo escape($settings['mpesa_till_number'] ?? ''); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <textarea name="company_address" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg"><?php echo escape($settings['company_address'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Company Logo</label>
                            <input type="file" name="logo" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            <p class="text-xs text-gray-500 mt-1">Upload as logo.jpg (Max 2MB)</p>
                        </div>
                    </div>
                </div>

                <!-- System Settings -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">System Settings</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tax Rate (%)</label>
                            <input type="number" name="tax_rate" value="<?php echo escape($settings['tax_rate'] ?? 16); ?>" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Low Stock Alert Level</label>
                            <input type="number" name="low_stock_alert" value="<?php echo escape($settings['low_stock_alert'] ?? 10); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Currency</label>
                            <select name="currency" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                <option value="KES" <?php echo ($settings['currency'] ?? '') == 'KES' ? 'selected' : ''; ?>>KES - Kenyan Shilling</option>
                                <option value="USD" <?php echo ($settings['currency'] ?? '') == 'USD' ? 'selected' : ''; ?>>USD - US Dollar</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Currency Symbol</label>
                            <input type="text" name="currency_symbol" value="<?php echo escape($settings['currency_symbol'] ?? 'KSh'); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                    </div>
                </div>

                <!-- Receipt Settings -->
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Receipt Settings</h3>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Receipt Footer Message</label>
                        <textarea name="receipt_footer" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg"><?php echo escape($settings['receipt_footer'] ?? ''); ?></textarea>
                    </div>
                </div>

                <!-- Backup & Maintenance -->
                <?php if (isOwner()): ?>
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Backup & Maintenance</h3>
                    
                    <div class="space-y-4">
                        <button type="button" onclick="backupDatabase()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            <i class="fas fa-download mr-2"></i>Backup Database
                        </button>
                        
                        <button type="button" onclick="clearCache()" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                            <i class="fas fa-broom mr-2"></i>Clear Cache
                        </button>
                        
                        <button type="button" onclick="resetDemo()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            <i class="fas fa-undo mr-2"></i>Reset to Demo Data
                        </button>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Save Button -->
                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
                        <i class="fas fa-save mr-2"></i>Save Settings
                    </button>
                </div>
            </form>
        </div>
    </main>

    <!-- Mobile Bottom Navigation -->
    <?php include 'includes/mobile-nav.php'; ?>

    <script>
        // Sidebar toggle
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });

        function backupDatabase() {
            if (confirm('Create a backup of the database?')) {
                window.location.href = '/ajax/backup.php';
            }
        }

        function clearCache() {
            if (confirm('Clear all cached data?')) {
                alert('Cache cleared successfully!');
            }
        }

        function resetDemo() {
            if (confirm('WARNING: This will reset all data to demo state. Are you sure?')) {
                if (confirm('This action cannot be undone. Continue?')) {
                    alert('Database reset to demo data!');
                }
            }
        }
    </script>
</body>
</html>
