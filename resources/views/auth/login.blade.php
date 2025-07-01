<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 50%, #22d3ee 100%);
        }
        
        .glass-effect {
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            background: rgba(6, 182, 212, 0.15);
            border: 1px solid rgba(34, 211, 238, 0.3);
        }
        
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
        
        @keyframes floating {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .slide-in {
            animation: slideIn 0.8s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .input-group {
            position: relative;
        }
        
        .input-group input:focus + label,
        .input-group input:not(:placeholder-shown) + label {
            transform: translateY(-20px) scale(0.8);
            color: #0891b2;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 4px;
            width: fit-content;
            padding-bottom: 4px;
        }
        
        .input-group label {
            position: absolute;
            left: 12px;
            top: 12px;
            color: #6b7280;
            transition: all 0.3s ease;
            pointer-events: none;
            background: rgba(255, 255, 255, 0); /* tambahkan opacity di background */
            padding: 0 4px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #0891b2 0%, #0e7490 50%, #155e75 100%);
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(8, 145, 178, 0.4);
            background: linear-gradient(135deg, #0e7490 0%, #155e75 50%, #164e63 100%);
        }
        
        .social-btn {
            transition: all 0.3s ease;
        }
        
        .social-btn:hover {
            transform: translateY(-2px);
        }
        
        .particle {
            position: absolute;
            background: rgba(34, 211, 238, 0.2);
            border-radius: 50%;
            animation: particle 15s linear infinite;
        }
        
        @keyframes particle {
            0% {
                transform: translateY(100vh) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-100px) rotate(360deg);
                opacity: 0;
            }
        }
    </style>
</head>
<body class="min-h-screen gradient-bg relative overflow-hidden">
    <!-- Animated Background Particles -->
    <script>
        // Generate random particles for animated background within viewport
        for (let i = 0; i < 50; i++) {
            const particle = document.createElement('div');
            const sizes = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
            const size = sizes[Math.floor(Math.random() * sizes.length)];
            // Calculate max left/top so particle stays inside viewport
            const maxOffset = 100 - (size / 4); // Tailwind w-10 ≈ 2.5rem, 1rem ≈ 4% of 100vw at 400px
            const left = Math.random() * maxOffset;
            const top = Math.random() * maxOffset;
            const delay = Math.random() * 16;

            particle.className = `particle w-${size} h-${size}`;
            particle.style.left = `${left}%`;
            particle.style.top = `${top}%`;
            particle.style.animationDelay = `${delay}s`;

            document.body.appendChild(particle);
        }
    </script>

    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="max-w-md w-full">
            <!-- Logo & Title -->
            <div class="text-center mb-8 slide-in">
                <div class="floating">
                    <div class="w-20 h-20 bg-gradient-to-br from-cyan-400 to-blue-500 rounded-full mx-auto mb-4 flex items-center justify-center shadow-lg">
                        <i class="fas fa-tint text-3xl text-white"></i>
                    </div>
                </div>
                <h2 class="text-3xl font-bold text-white mb-2">Selamat Datang</h2>
                <p class="text-cyan-100">Masuk ke akun Anda untuk melanjutkan</p>
            </div>

            <!-- Login Form -->
            <div class="glass-effect rounded-2xl p-8 shadow-2xl slide-in">
                <!-- Session Status Alert -->
                <div id="session-status" class="hidden mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                    <i class="fas fa-check-circle mr-2"></i>
                    Status berhasil diperbarui
                </div>

                <form id="loginForm" class="space-y-6"  method="POST" action="{{ route('login') }}">
                    @csrf
                    <!-- Email Field -->
                    <div class="input-group">
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            placeholder=" "
                            required
                            class="w-full px-3 py-3 border-2 border-cyan-200 rounded-lg focus:border-cyan-500 focus:outline-none transition-all duration-300 bg-white/90 backdrop-blur-sm"
                        >
                        <label for="email">Email Address</label>
                        <div class="absolute right-3 top-3 text-cyan-500">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div id="email-error" class="hidden mt-2 text-red-500 text-sm">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            Email tidak valid
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div class="input-group">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            placeholder=" "
                            required
                            class="w-full px-3 py-3 border-2 border-cyan-200 rounded-lg focus:border-cyan-500 focus:outline-none transition-all duration-300 bg-white/90 backdrop-blur-sm pr-12"
                        >
                        <label for="password">Password</label>
                        <button type="button" id="togglePassword" class="absolute right-3 top-3 text-cyan-500 hover:text-cyan-700">
                            <i class="fas fa-eye"></i>
                        </button>
                        <div id="password-error" class="hidden mt-2 text-red-500 text-sm">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            Password wajib diisi
                        </div>
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input type="checkbox" id="remember" name="remember" class="w-4 h-4 text-cyan-600 border-gray-300 rounded focus:ring-cyan-500">
                            <span class="ml-2 text-sm text-white">Ingat saya</span>
                        </label>
                        <a href="#" class="text-sm text-cyan-200 hover:text-white transition-colors duration-300">
                            Lupa password?
                        </a>
                    </div>

                    <!-- Login Button -->
                    <button 
                        type="submit" 
                        class="w-full btn-primary text-white font-semibold py-3 px-4 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center"
                        id="loginBtn"
                    >
                        <span id="loginText">Masuk</span>
                        <i id="loginSpinner" class="fas fa-spinner fa-spin ml-2 hidden"></i>
                    </button>
                </form>

                <!-- Divider -->
                <div class="my-6 flex items-center">
                    <div class="flex-1 border-t border-gray-300"></div>
                    <span class="px-4 text-gray-500 text-sm">atau</span>
                    <div class="flex-1 border-t border-gray-300"></div>
                </div>

                <!-- Social Login -->
                <div class="space-y-3">
                    <button class="w-full social-btn bg-white hover:bg-gray-50 text-cyan-700 font-semibold py-3 px-4 border border-cyan-300 rounded-lg shadow flex items-center justify-center">
                        <i class="fas fa-user-shield text-cyan-500 mr-3"></i>
                        Masuk dengan SSO Sulteng Prov.
                    </button>
                </div>

                <!-- Register Link -->
                <div class="mt-6 text-center">
                    <p class="text-white">
                        Belum punya akun? 
                        <a href="#" class="text-cyan-200 hover:text-white font-semibold transition-colors duration-300">
                            Daftar sekarang
                        </a>
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-8 text-cyan-100 text-sm">
                <p>&copy; 2025 DKIPS</p>
            </div>
        </div>
    </div>

    <script>
        // Toggle Password Visibility
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

        // Form Validation & Submission
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const loginBtn = document.getElementById('loginBtn');
            const loginText = document.getElementById('loginText');
            const loginSpinner = document.getElementById('loginSpinner');
            
            // Reset error states
            document.getElementById('email-error').classList.add('hidden');
            document.getElementById('password-error').classList.add('hidden');
            
            let hasError = false;
            
            // Validate email
            if (!email || !email.includes('@')) {
                document.getElementById('email-error').classList.remove('hidden');
                hasError = true;
            }
            
            // Validate password
            if (!password) {
                document.getElementById('password-error').classList.remove('hidden');
                hasError = true;
            }
            
            if (hasError) return;
            
            // Show loading state
            loginBtn.disabled = true;
            loginText.textContent = 'Sedang masuk...';
            loginSpinner.classList.remove('hidden');
            
            // Simulate login process
            setTimeout(() => {
                // Reset button state
                loginBtn.disabled = false;
                loginText.textContent = 'Masuk';
                loginSpinner.classList.add('hidden');
                
                // Show success message
                document.getElementById('session-status').classList.remove('hidden');
                
                // In real implementation, redirect to dashboard
                // alert('Login berhasil! Redirecting to dashboard...');
                this.submit();
            }, 2000);
        });

        // Input focus effects
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('focus', function() {
                const label = this.parentElement.querySelector('label');
                if (label) {
                    label.style.color = '#0891b2';
                }
            });
            
            input.addEventListener('blur', function() {
                const label = this.parentElement.querySelector('label');
                if (label && !this.value) {
                    label.style.color = '#6b7280';
                }
            });
        });

        // Add entrance animation delay to form elements
        const formElements = document.querySelectorAll('.input-group');
        formElements.forEach((element, index) => {
            element.style.animationDelay = `${0.2 + (index * 0.1)}s`;
            element.classList.add('slide-in');
        });
    </script>
</body>
</html>