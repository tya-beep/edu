<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password | Al Amin HRMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        body {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 50%, #a7f3d0 100%);
            min-height: 100vh;
            position: relative;
            overflow: hidden;
        }
        /* Animated background circles */
        body::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at 30% 50%, rgba(5, 150, 105, 0.05) 0%, transparent 50%),
                        radial-gradient(circle at 70% 80%, rgba(16, 185, 129, 0.05) 0%, transparent 50%);
            animation: float 20s ease-in-out infinite;
            z-index: 0;
        }
        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(30px, -30px) rotate(2deg); }
            66% { transform: translate(-20px, 20px) rotate(-2deg); }
        }
        .card {
            position: relative;
            z-index: 1;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 25px 60px -15px rgba(5, 150, 105, 0.25);
            transition: all 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 35px 80px -15px rgba(5, 150, 105, 0.35);
        }
        .icon-wrapper {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #059669, #10b981);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
            box-shadow: 0 12px 30px -10px rgba(5, 150, 105, 0.4);
            animation: pulse-icon 3s ease-in-out infinite;
        }
        @keyframes pulse-icon {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        .input-group {
            position: relative;
        }
        .input-group .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            transition: color 0.3s ease;
            font-size: 1rem;
        }
        .input-group input {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 3rem;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: white;
            outline: none;
        }
        .input-group input:focus {
            border-color: #059669;
            box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.1);
        }
        .input-group input:focus + .input-icon,
        .input-group input:focus ~ .input-icon {
            color: #059669;
        }
        .input-group input:focus::placeholder {
            color: transparent;
        }
        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #9ca3af;
            transition: color 0.3s ease;
            background: none;
            border: none;
            font-size: 1rem;
        }
        .password-toggle:hover {
            color: #059669;
        }
        .btn-submit {
            background: linear-gradient(135deg, #059669, #10b981);
            color: white;
            font-weight: 700;
            padding: 0.875rem 2rem;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            box-shadow: 0 8px 25px -8px rgba(5, 150, 105, 0.4);
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px -8px rgba(5, 150, 105, 0.5);
        }
        .btn-submit:active {
            transform: translateY(0);
        }
        .error-box {
            background: #fef2f2;
            border-left: 4px solid #ef4444;
            padding: 0.875rem 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }
        .error-box ul {
            margin: 0;
            padding-left: 0;
            list-style: none;
        }
        .error-box ul li {
            color: #991b1b;
            font-size: 0.875rem;
            padding: 0.125rem 0;
        }
        .error-box ul li::before {
            content: '⚠️ ';
        }
        .password-requirements {
            margin-top: 0.75rem;
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .requirement {
            font-size: 0.7rem;
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            background: #f3f4f6;
            color: #6b7280;
            display: flex;
            align-items: center;
            gap: 0.375rem;
            transition: all 0.3s ease;
        }
        .requirement.met {
            background: #d1fae5;
            color: #065f46;
        }
        .requirement i {
            font-size: 0.6rem;
        }
        .divider {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 1.5rem 0;
        }
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(to right, transparent, #e5e7eb, transparent);
        }
        /* Password strength bar */
        .strength-bar {
            height: 4px;
            border-radius: 2px;
            background: #e5e7eb;
            margin-top: 0.75rem;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .strength-bar .fill {
            height: 100%;
            width: 0%;
            border-radius: 2px;
            transition: width 0.5s ease, background 0.3s ease;
        }
        @media (max-width: 480px) {
            .card {
                margin: 1rem;
                padding: 1.5rem !important;
            }
            .icon-wrapper {
                width: 60px;
                height: 60px;
            }
        }
    </style>
</head>
<body>

    <div class="w-full max-w-md mx-auto px-4 card rounded-3xl p-8">
        
        <!-- Icon -->
        <div class="icon-wrapper">
            <i class="fas fa-lock text-white text-3xl"></i>
        </div>

        <!-- Header -->
        <div class="text-center mb-6">
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Security Update</h1>
            <p class="text-gray-500 text-sm mt-1">Please set a new password to continue accessing your account.</p>
        </div>

        <!-- Validation Errors -->
        @if($errors->any())
            <div class="error-box">
                <div>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('change.password.submit') }}" id="passwordForm">
            @csrf

            <!-- New Password -->
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    <i class="fas fa-key mr-1 text-green-600"></i> New Password
                </label>
                <div class="input-group">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" 
                           name="password" 
                           id="password" 
                           required 
                           placeholder="Enter new password"
                           autocomplete="new-password">
                    <button type="button" class="password-toggle" onclick="togglePassword('password')">
                        <i class="fas fa-eye" id="password-icon"></i>
                    </button>
                </div>
                <!-- Password Strength -->
                <div class="strength-bar">
                    <div class="fill" id="strengthFill"></div>
                </div>
                <div class="password-requirements" id="requirements">
                    <span class="requirement" id="req-length">
                        <i class="fas fa-circle"></i> 8+ characters
                    </span>
                    <span class="requirement" id="req-upper">
                        <i class="fas fa-circle"></i> Uppercase
                    </span>
                    <span class="requirement" id="req-lower">
                        <i class="fas fa-circle"></i> Lowercase
                    </span>
                    <span class="requirement" id="req-number">
                        <i class="fas fa-circle"></i> Number
                    </span>
                </div>
            </div>

            <!-- Confirm Password -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    <i class="fas fa-check-double mr-1 text-green-600"></i> Confirm Password
                </label>
                <div class="input-group">
                    <i class="fas fa-check-circle input-icon"></i>
                    <input type="password" 
                           name="password_confirmation" 
                           id="password_confirmation" 
                           required 
                           placeholder="Confirm your password"
                           autocomplete="new-password">
                    <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')">
                        <i class="fas fa-eye" id="confirm-icon"></i>
                    </button>
                </div>
                <div id="matchMessage" class="text-sm mt-1.5 hidden"></div>
            </div>

            <!-- Divider -->
            <div class="divider">
                <span class="text-xs text-gray-400 font-medium uppercase tracking-wider">Secure Access</span>
            </div>

            <!-- Button -->
            <button type="submit" class="btn-submit" id="submitBtn">
                <i class="fas fa-shield-alt"></i>
                Update & Access System
                <i class="fas fa-arrow-right"></i>
            </button>

            <!-- Footer -->
            <div class="text-center mt-4">
                <a href="{{ route('login') }}" class="text-sm text-gray-400 hover:text-green-600 transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Login
                </a>
            </div>
        </form>
    </div>

    <script>
        // Password Toggle
        function togglePassword(fieldId) {
            var input = document.getElementById(fieldId);
            var icon = document.getElementById(fieldId === 'password' ? 'password-icon' : 'confirm-icon');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'fas fa-eye';
            }
        }

        // Password Strength & Requirements
        document.getElementById('password').addEventListener('input', function() {
            var password = this.value;
            
            // Requirements
            var length = password.length >= 8;
            var upper = /[A-Z]/.test(password);
            var lower = /[a-z]/.test(password);
            var number = /[0-9]/.test(password);
            
            updateRequirement('req-length', length);
            updateRequirement('req-upper', upper);
            updateRequirement('req-lower', lower);
            updateRequirement('req-number', number);
            
            // Strength Bar
            var strength = 0;
            if (length) strength += 25;
            if (upper) strength += 25;
            if (lower) strength += 25;
            if (number) strength += 25;
            
            var fill = document.getElementById('strengthFill');
            fill.style.width = strength + '%';
            
            if (strength <= 25) {
                fill.style.background = '#ef4444';
            } else if (strength <= 50) {
                fill.style.background = '#f59e0b';
            } else if (strength <= 75) {
                fill.style.background = '#3b82f6';
            } else {
                fill.style.background = '#10b981';
            }
            
            // Check confirm password match
            checkMatch();
        });

        function updateRequirement(id, met) {
            var el = document.getElementById(id);
            if (met) {
                el.className = 'requirement met';
                el.querySelector('i').className = 'fas fa-check-circle';
            } else {
                el.className = 'requirement';
                el.querySelector('i').className = 'fas fa-circle';
            }
        }

        // Confirm Password Match
        document.getElementById('password_confirmation').addEventListener('input', checkMatch);

        function checkMatch() {
            var password = document.getElementById('password').value;
            var confirm = document.getElementById('password_confirmation').value;
            var message = document.getElementById('matchMessage');
            
            if (confirm.length === 0) {
                message.className = 'text-sm mt-1.5 hidden';
                return;
            }
            
            if (password === confirm) {
                message.className = 'text-sm mt-1.5 text-green-600';
                message.innerHTML = '<i class="fas fa-check-circle mr-1"></i> Passwords match!';
                document.getElementById('submitBtn').disabled = false;
                document.getElementById('submitBtn').style.opacity = '1';
            } else {
                message.className = 'text-sm mt-1.5 text-red-500';
                message.innerHTML = '<i class="fas fa-exclamation-circle mr-1"></i> Passwords do not match';
                document.getElementById('submitBtn').disabled = true;
                document.getElementById('submitBtn').style.opacity = '0.5';
            }
        }

        // Initialize - disable submit button initially
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('submitBtn').disabled = true;
            document.getElementById('submitBtn').style.opacity = '0.5';
        });
    </script>

</body>
</html>