<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Al Amin HRMS | Secure Login</title>
    <!-- Tailwind + Google Fonts + Font Awesome -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Tiny custom style for fine-tuning & smooth interactions -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #eef9f0 0%, #d9f0e3 50%, #c8e9da 100%);
            background-attachment: fixed;
        }
        /* subtle glassmorphism / cleaner focus rings */
        .focus-ring:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.25);
            border-color: #059669;
        }
        @keyframes gentleFloat {
            0% { transform: translateY(0px); }
            100% { transform: translateY(-4px); }
        }
        .hover-lift:hover {
            transform: translateY(-2px);
            transition: all 0.2s ease;
        }
        .card-shadow {
            box-shadow: 0 20px 35px -12px rgba(0, 0, 0, 0.12), 0 1px 3px rgba(0,0,0,0.02);
        }
        /* custom transition for inputs */
        .input-transition {
            transition: border 0.2s, box-shadow 0.2s;
        }
    </style>
</head>
<body class="antialiased flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-md mx-auto animate-fade-in-up">
        
        <!-- Logo + Brand section (refined) -->
        <div class="text-center mb-6">
            <div class="inline-flex flex-col items-center">
                <!-- Elegant logo container with subtle elevated border + micro-interaction -->
                <div class="bg-white rounded-2xl shadow-md hover:shadow-lg transition-all duration-300 p-2.5 border border-emerald-100/80">
                    <!-- Using a modern placeholder SVG as company emblem (pure professional vector style) 
                         but retains the structure from original – replaced with a clean, modern HRMS icon set.
                         However the original code uses <img> with asset('images/logo.png') – I'll keep it dynamic 
                         to allow real logo, but also show a professional fallback design (gradient emblem) 
                         if image not found. But to look professional, I'll embed a crisp SVG company-style logo -->
                    <div class="h-14 w-14 flex items-center justify-center bg-gradient-to-br from-emerald-600 to-green-600 rounded-xl shadow-inner">
                        <i class="fas fa-building text-white text-2xl"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <h1 class="text-xl font-extrabold tracking-tight text-gray-800">Al Amin <span class="text-emerald-700">HRMS</span></h1>
                    <p class="text-emerald-600 text-xs font-medium tracking-wide">Human Resource Management System</p>
                </div>
            </div>
        </div>

        <!-- Modern Login Card with subtle depth -->
        <div class="bg-white rounded-2xl card-shadow overflow-hidden transition-all duration-200">
            <!-- Header with refined gradient, icon and tagline -->
            <div class="relative bg-gradient-to-r from-emerald-700 to-green-700 px-6 py-5 text-center">
                <div class="absolute top-0 right-0 opacity-10">
                    <i class="fas fa-chalkboard-user text-6xl -mt-2 mr-2"></i>
                </div>
                <div class="relative z-10">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-white/20 backdrop-blur-sm rounded-full mb-2">
                        <i class="fas fa-lock-open text-white text-xl"></i>
                    </div>
                    <h2 class="text-white text-xl font-bold tracking-wide">Secure Access</h2>
                    <p class="text-emerald-100 text-xs mt-1 font-medium">Sign in with your corporate credentials</p>
                </div>
            </div>

            <div class="p-6 md:p-7">
                <!-- Error message with modern styling -->
                                @if(session('error'))
                <div class="mb-5 bg-red-50 border-l-4 border-red-500 text-red-700 px-4 py-2.5 rounded-lg text-sm flex items-start gap-2 shadow-sm">
                    <i class="fas fa-circle-exclamation text-red-500 mt-0.5 text-xs"></i>
                    <span>{{ session('error') }}</span>
                </div>
                @endif

                <form method="POST" action="{{ route('login.submit') }}" class="space-y-4">
                    @csrf

                    <!-- Email Field with better icon & modern label -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1.5">
                            <i class="far fa-envelope mr-1 text-emerald-600"></i> Email Address
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400 text-sm group-focus-within:text-emerald-600 transition-colors"></i>
                            </div>
                            <input type="email" name="email" required autocomplete="username"
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:border-emerald-400 focus:ring-2 focus:ring-emerald-200 focus:outline-none input-transition text-gray-700 placeholder-gray-400 text-sm"
                                placeholder="alex.johnson@alamin.edu.my">
                        </div>
                    </div>

                    <!-- Password Field with show/hide optional (modern) -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1.5">
                            <i class="fas fa-key mr-1 text-emerald-600"></i> Password
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400 text-sm group-focus-within:text-emerald-600 transition-colors"></i>
                            </div>
                            <input type="password" name="password" required autocomplete="current-password"
                                class="w-full pl-10 pr-10 py-2.5 border border-gray-200 rounded-xl focus:border-emerald-400 focus:ring-2 focus:ring-emerald-200 focus:outline-none input-transition text-gray-700 text-sm"
                                placeholder="••••••••">
                            <!-- simple eye toggle functionality (optional but adds professional UX) -->
                            <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-emerald-600 transition">
                                <i class="fas fa-eye-slash text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Row: Remember me + Forgot password (clean flex) -->
                    <div class="flex items-center justify-between mt-1">
                        <label class="flex items-center cursor-pointer select-none">
                            <input type="checkbox" name="remember" class="w-3.5 h-3.5 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 focus:ring-offset-0 focus:ring-1">
                            <span class="ml-2 text-xs text-gray-600 font-medium">Keep me signed in</span>
                        </label>
                    </div>

                    <!-- Submit Button with elegant loading simulation & enhanced design -->
                    <button type="submit" 
                        class="mt-4 w-full bg-gradient-to-r from-emerald-700 to-green-700 hover:from-emerald-800 hover:to-green-800 text-white font-semibold py-2.5 rounded-xl transition-all duration-200 shadow-md hover:shadow-lg flex items-center justify-center gap-2 text-sm tracking-wide group">
                        <i class="fas fa-arrow-right-to-bracket text-xs group-hover:translate-x-0.5 transition-transform"></i>
                        <span>Sign In </span>
                    </button>
                </form>

                <!-- Divider with subtle note -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-100"></div>
                    </div>
                  
                </div>

                <!-- system status / additional info (professional touch) -->
                <div class="text-center text-[11px] text-gray-400 flex items-center justify-center gap-2">
                    <i class="fas fa-shield-alt text-emerald-500"></i>
                    <span> | Role-based Access</span>
                </div>
            </div>
        </div>

        <!-- Footer copyright & version -->
        <div class="text-center mt-6">
            <p class="text-gray-500 text-[11px] font-medium flex items-center justify-center gap-2 flex-wrap">
                <span>© {{ date('Y') }} Al Amin Eduoasis Sdn Bhd</span>
                <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                <span>HRMS v4.2.1</span>
                <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                <span><i class="far fa-clock"></i> Secure Session</span>
            </p>
        </div>
    </div>

    <!-- Additional script for password toggle & subtle interaction (makes it modern) -->
    <script>
        (function() {
            // Toggle password visibility professional enhancement
            const toggleBtn = document.getElementById('togglePassword');
            if (toggleBtn) {
                const passwordField = document.querySelector('input[name="password"]');
                toggleBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordField.setAttribute('type', type);
                    const icon = toggleBtn.querySelector('i');
                    if (icon) {
                        icon.classList.toggle('fa-eye');
                        icon.classList.toggle('fa-eye-slash');
                    }
                });
            }
            
            // Add a simple fade-in-up animation class if not already defined
            const styleSheet = document.createElement("style");
            styleSheet.textContent = `
                @keyframes fadeInUp {
                    0% { opacity: 0; transform: translateY(16px); }
                    100% { opacity: 1; transform: translateY(0); }
                }
                .animate-fade-in-up {
                    animation: fadeInUp 0.45s cubic-bezier(0.2, 0.9, 0.4, 1.1) forwards;
                }
                input:focus, button:focus {
                    outline: none;
                }
                /* custom scroll if needed */
                ::-webkit-scrollbar {
                    width: 5px;
                }
            `;
            document.head.appendChild(styleSheet);
            document.querySelector('.w-full.max-w-md').classList.add('animate-fade-in-up');
        })();
    </script>
</body>
</html>