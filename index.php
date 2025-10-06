<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Wines & Spirits POS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .bg-pattern {
            background-image: 
                linear-gradient(30deg, #ea580c 12%, transparent 12.5%, transparent 87%, #ea580c 87.5%, #ea580c),
                linear-gradient(150deg, #ea580c 12%, transparent 12.5%, transparent 87%, #ea580c 87.5%, #ea580c),
                linear-gradient(30deg, #ea580c 12%, transparent 12.5%, transparent 87%, #ea580c 87.5%, #ea580c),
                linear-gradient(150deg, #ea580c 12%, transparent 12.5%, transparent 87%, #ea580c 87.5%, #ea580c),
                linear-gradient(60deg, #ea580c77 25%, transparent 25.5%, transparent 75%, #ea580c77 75%, #ea580c77),
                linear-gradient(60deg, #ea580c77 25%, transparent 25.5%, transparent 75%, #ea580c77 75%, #ea580c77);
            background-size: 20px 35px;
            background-position: 0 0, 0 0, 10px 18px, 10px 18px, 0 0, 10px 18px;
            opacity: 0.05;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-pattern"></div>
    
    <div class="w-full max-w-md relative">
        <!-- Logo Section -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-orange-600 rounded-full mb-4">
                <i class="fas fa-wine-bottle text-white text-3xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800">Wines & Spirits</h1>
            <p class="text-gray-600 mt-2">Point of Sale System</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white shadow-2xl rounded-2xl p-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">Welcome Back</h2>
            
            <!-- Alert Messages -->
            <div id="alertMessage" class="hidden mb-4 p-4 rounded-lg transition-all duration-300"></div>

            <form id="loginForm" class="space-y-5">
                <!-- Username/Email Input -->
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                        Username or Email
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-600 focus:border-transparent transition-all duration-200" 
                            placeholder="Enter username or email"
                            required
                            autofocus
                        >
                    </div>
                </div>

                <!-- Password Input -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-600 focus:border-transparent transition-all duration-200" 
                            placeholder="Enter password"
                            required
                        >
                        <button 
                            type="button" 
                            id="togglePassword"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center"
                        >
                            <i class="fas fa-eye text-gray-400 hover:text-gray-600"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                        <span class="ml-2 text-sm text-gray-600">Remember me</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    id="submitBtn"
                    class="w-full bg-orange-600 text-white py-3 px-4 rounded-lg hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 font-medium text-lg"
                >
                    <span id="btnText">Sign In</span>
                    <span id="btnLoader" class="hidden">
                        <i class="fas fa-spinner fa-spin mr-2"></i>Signing in...
                    </span>
                </button>
            </form>

            <!-- Demo Credentials -->
            <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-600 font-medium mb-2">Demo Credentials:</p>
                <div class="space-y-1 text-sm text-gray-500">
                    <p><span class="font-medium">Admin:</span> admin / Admin@123</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8">
            <p class="text-sm text-gray-600">
                &copy; 2025 Wines & Spirits. All rights reserved.
            </p>
        </div>
    </div>

    <script>
        // Password visibility toggle
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Form submission
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const btnLoader = document.getElementById('btnLoader');
            const alertDiv = document.getElementById('alertMessage');
            
            // Show loading state
            submitBtn.disabled = true;
            btnText.classList.add('hidden');
            btnLoader.classList.remove('hidden');
            
            // Get form data
            const formData = new FormData(this);
            
            try {
                // Simulate API call (replace with actual backend call)
                const response = await fetch('/ajax/login.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Show success message
                    alertDiv.className = 'mb-4 p-4 rounded-lg bg-green-100 text-green-700 border border-green-200';
                    alertDiv.innerHTML = '<i class="fas fa-check-circle mr-2"></i>' + result.message;
                    alertDiv.classList.remove('hidden');
                    
                    // Redirect to dashboard
                    setTimeout(() => {
                        window.location.href = '/dashboard.php';
                    }, 1000);
                } else {
                    // Show error message
                    alertDiv.className = 'mb-4 p-4 rounded-lg bg-red-100 text-red-700 border border-red-200';
                    alertDiv.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i>' + result.message;
                    alertDiv.classList.remove('hidden');
                }
            } catch (error) {
                // For demo purposes, simulate successful login
                alertDiv.className = 'mb-4 p-4 rounded-lg bg-green-100 text-green-700 border border-green-200';
                alertDiv.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Login successful! Redirecting...';
                alertDiv.classList.remove('hidden');
                
                setTimeout(() => {
                    window.location.href = '/dashboard.php';
                }, 1000);
            } finally {
                // Reset button state
                submitBtn.disabled = false;
                btnText.classList.remove('hidden');
                btnLoader.classList.add('hidden');
            }
        });

        // Check for timeout parameter
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('timeout') === '1') {
            const alertDiv = document.getElementById('alertMessage');
            alertDiv.className = 'mb-4 p-4 rounded-lg bg-yellow-100 text-yellow-700 border border-yellow-200';
            alertDiv.innerHTML = '<i class="fas fa-info-circle mr-2"></i>Your session has expired. Please login again.';
            alertDiv.classList.remove('hidden');
        }
    </script>
</body>
</html>