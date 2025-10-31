<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تغییر رمز عبور الزامی - هنرستان امام صادق</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            font-family: 'Vazir', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .change-password-container {
            background: white;
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 450px;
            text-align: right;
            position: relative;
            overflow: hidden;
        }
        
        .change-password-container::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(to left, #667eea, #764ba2);
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #333;
            font-size: 24px;
            margin-bottom: 10px;
            position: relative;
            display: inline-block;
        }
        
        .header h1::after {
            content: '';
            position: absolute;
            bottom: -5px;
            right: 0;
            width: 50%;
            height: 3px;
            background: linear-gradient(to left, #667eea, #764ba2);
            border-radius: 3px;
        }
        
        .header p {
            color: #e53e3e;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 10px;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 2px solid #e1e1e1;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .form-group i {
            position: absolute;
            left: 15px;
            top: 40px;
            color: #999;
            transition: color 0.3s;
        }
        
        .form-group input:focus + i {
            color: #667eea;
        }
        
        .btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(to left, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            right: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(to right, rgba(255,255,255,0.2), rgba(255,255,255,0));
            transition: right 0.5s;
        }
        
        .btn:hover::before {
            right: 100%;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .btn:disabled {
            background: #a0aec0;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        .btn:disabled:hover::before {
            right: -100%;
        }
        
        .error {
            background: #ffebeb;
            color: #c53030;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #fcacac;
            font-size: 14px;
            animation: shake 0.5s ease-in-out;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        /* استایل‌های مربوط به اعتبارسنجی رمز عبور */
        .password-strength {
            margin-top: 8px;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .strength-meter {
            height: 5px;
            flex-grow: 1;
            background: #e1e1e1;
            border-radius: 3px;
            overflow: hidden;
        }
        
        .strength-fill {
            height: 100%;
            width: 0%;
            transition: width 0.3s, background 0.3s;
        }
        
        .strength-text {
            font-size: 11px;
            min-width: 70px;
        }
        
        .password-requirements {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
            font-size: 12px;
            color: #666;
            border: 1px solid #eaeaea;
        }
        
        .password-requirements h4 {
            margin-bottom: 8px;
            color: #333;
            font-size: 13px;
        }
        
        .requirement {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
        }
        
        .requirement i {
            margin-left: 5px;
            font-size: 10px;
        }
        
        .requirement.valid {
            color: #38a169;
        }
        
        .requirement.invalid {
            color: #e53e3e;
        }
        
        .error-message {
            color: #e53e3e;
            font-size: 12px;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
            animation: fadeIn 0.3s ease-out;
        }
        
        .error-message i {
            font-size: 10px;
        }
        
        .copyright {
            text-align: center;
            margin-top: 20px;
            color: #999;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }
        
        .copyright i {
            color: #667eea;
        }
        
        /* استایل‌های ریسپانسیو */
        @media (max-width: 480px) {
            .change-password-container {
                padding: 30px 20px;
            }
            
            .header h1 {
                font-size: 20px;
            }
            
            .header p {
                font-size: 13px;
            }
            
            .form-group input {
                padding: 10px 12px 10px 40px;
                font-size: 13px;
            }
            
            .btn {
                padding: 10px;
                font-size: 15px;
            }
            
            .password-requirements {
                font-size: 11px;
                padding: 12px;
            }
        }
        
        @media (max-width: 350px) {
            .change-password-container {
                padding: 25px 15px;
            }
            
            .header h1 {
                font-size: 18px;
            }
            
            .form-group label {
                font-size: 14px;
            }
            
            .copyright {
                font-size: 11px;
            }
        }
        
        /* انیمیشن‌ها */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .change-password-container {
            animation: fadeIn 0.8s ease-out;
        }
        
        .success-message {
            background: #f0fff4;
            color: #38a169;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #9ae6b4;
            font-size: 14px;
            animation: fadeIn 0.5s ease-out;
        }
    </style>
</head>
<body>
    <div class="change-password-container">
        <div class="header">
            <h1>تغییر رمز عبور</h1>
            <?php 
            // فرض می‌کنیم شماره موبایل کاربر از سشن در دسترس است
            $mobile = isset($_SESSION['mobile']) ? $_SESSION['mobile'] : 'کاربر'; 
            ?>
            <p>
                <?php echo $mobile; ?>، لطفاً برای ادامه رمز عبور جدید خود را تعریف کنید.
            </p>
            <p>
                <strong>(این کار برای حفظ امنیت حساب شما الزامی است)</strong>
            </p>
        </div>
        
        <?php 
        // در صورت وجود خطا در آرایه $data آن را نمایش می‌دهد
        if (isset($data['error'])): ?>
            <div class="error"><?php echo $data['error']; ?></div>
        <?php endif; ?>
        
        <?php 
        // در صورت موفقیت‌آمیز بودن تغییر رمز
        if (isset($data['success'])): ?>
            <div class="success-message"><?php echo $data['success']; ?></div>
        <?php endif; ?>

        <!-- فرض می‌کنیم که کنترلر ChangePassword داده‌های لازم را به $data ارسال می‌کند -->
        <form method="POST" id="changePasswordForm">
            <div class="form-group">
                <label for="new_password">رمز عبور جدید</label>
                <input type="password" id="new_password" name="new_password" required minlength="6" placeholder="حداقل ۶ کاراکتر">
                <i class="fas fa-lock"></i>
                
                <div class="password-strength">
                    <span class="strength-text">قدرت رمز:</span>
                    <div class="strength-meter">
                        <div class="strength-fill" id="strengthFill"></div>
                    </div>
                </div>
                
                <div class="password-requirements">
                    <h4>الزامات رمز عبور:</h4>
                    <div class="requirement invalid" id="reqLength">
                        <i class="fas fa-times"></i>
                        <span>حداقل ۸ کاراکتر</span>
                    </div>
                    <div class="requirement invalid" id="reqLowercase">
                        <i class="fas fa-times"></i>
                        <span>حاوی حروف کوچک</span>
                    </div>
                    <div class="requirement invalid" id="reqUppercase">
                        <i class="fas fa-times"></i>
                        <span>حاوی حروف بزرگ</span>
                    </div>
                    <div class="requirement invalid" id="reqNumber">
                        <i class="fas fa-times"></i>
                        <span>حاوی عدد</span>
                    </div>
                    <div class="requirement invalid" id="reqSpecial">
                        <i class="fas fa-times"></i>
                        <span>حاوی کاراکتر ویژه (!@#$%^&*)</span>
                    </div>
                </div>
                
                <div id="passwordErrors"></div>
            </div>

            <div class="form-group">
                <label for="confirm_password">تکرار رمز عبور جدید</label>
                <input type="password" id="confirm_password" name="confirm_password" required minlength="6" placeholder="تکرار رمز عبور">
                <i class="fas fa-lock"></i>
                <div id="passwordMatch" class="error-message" style="display: none;">
                    <i class="fas fa-times"></i> رمزهای عبور مطابقت ندارند
                </div>
            </div>

            <button type="submit" class="btn" id="submitBtn" disabled>ثبت و ورود</button>
        </form>
        
        <div class="copyright">
            <i class="far fa-copyright"></i>
            طراحی شده توسط علیرضا میربیک
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const newPasswordInput = document.getElementById('new_password');
            const confirmPasswordInput = document.getElementById('confirm_password');
            const strengthFill = document.getElementById('strengthFill');
            const strengthText = document.querySelector('.strength-text');
            const submitBtn = document.getElementById('submitBtn');
            const passwordMatch = document.getElementById('passwordMatch');
            const passwordErrors = document.getElementById('passwordErrors');
            
            // الزامات رمز عبور
            const requirements = {
                length: document.getElementById('reqLength'),
                lowercase: document.getElementById('reqLowercase'),
                uppercase: document.getElementById('reqUppercase'),
                number: document.getElementById('reqNumber'),
                special: document.getElementById('reqSpecial')
            };
            
            // بررسی قدرت رمز عبور
            newPasswordInput.addEventListener('input', function() {
                const password = this.value;
                checkPasswordStrength(password);
                validateForm();
            });
            
            // بررسی تطابق رمزهای عبور
            confirmPasswordInput.addEventListener('input', function() {
                validatePasswordMatch();
                validateForm();
            });
            
            // بررسی قدرت رمز عبور
            function checkPasswordStrength(password) {
                let strength = 0;
                let validRequirements = 0;
                const totalRequirements = 5;
                
                // بررسی طول رمز
                if (password.length >= 8) {
                    strength += 20;
                    validRequirements++;
                    updateRequirement(requirements.length, true);
                } else {
                    updateRequirement(requirements.length, false);
                }
                
                // بررسی حروف کوچک
                if (/[a-z]/.test(password)) {
                    strength += 20;
                    validRequirements++;
                    updateRequirement(requirements.lowercase, true);
                } else {
                    updateRequirement(requirements.lowercase, false);
                }
                
                // بررسی حروف بزرگ
                if (/[A-Z]/.test(password)) {
                    strength += 20;
                    validRequirements++;
                    updateRequirement(requirements.uppercase, true);
                } else {
                    updateRequirement(requirements.uppercase, false);
                }
                
                // بررسی اعداد
                if (/[0-9]/.test(password)) {
                    strength += 20;
                    validRequirements++;
                    updateRequirement(requirements.number, true);
                } else {
                    updateRequirement(requirements.number, false);
                }
                
                // بررسی کاراکترهای ویژه
                if (/[!@#$%^&*]/.test(password)) {
                    strength += 20;
                    validRequirements++;
                    updateRequirement(requirements.special, true);
                } else {
                    updateRequirement(requirements.special, false);
                }
                
                // به روزرسانی نمایش قدرت رمز
                strengthFill.style.width = strength + '%';
                
                if (strength < 40) {
                    strengthFill.style.background = '#e53e3e';
                    strengthText.textContent = 'ضعیف';
                } else if (strength < 80) {
                    strengthFill.style.background = '#d69e2e';
                    strengthText.textContent = 'متوسط';
                } else {
                    strengthFill.style.background = '#38a169';
                    strengthText.textContent = 'قوی';
                }
                
                // اگر رمز خالی است
                if (password.length === 0) {
                    strengthFill.style.width = '0%';
                    strengthText.textContent = 'قدرت رمز:';
                    resetRequirements();
                    clearErrors();
                }
                
                // نمایش خطاها
                showPasswordErrors(password);
            }
            
            // نمایش خطاهای رمز عبور
            function showPasswordErrors(password) {
                let errors = [];
                
                if (password.length > 0 && password.length < 8) {
                    errors.push('رمز عبور باید حداقل ۸ کاراکتر باشد');
                }
                
                if (password.length > 0 && !/[a-z]/.test(password)) {
                    errors.push('رمز عبور باید شامل حروف کوچک باشد');
                }
                
                if (password.length > 0 && !/[A-Z]/.test(password)) {
                    errors.push('رمز عبور باید شامل حروف بزرگ باشد');
                }
                
                if (password.length > 0 && !/[0-9]/.test(password)) {
                    errors.push('رمز عبور باید شامل اعداد باشد');
                }
                
                if (password.length > 0 && !/[!@#$%^&*]/.test(password)) {
                    errors.push('رمز عبور باید شامل کاراکترهای ویژه (!@#$%^&*) باشد');
                }
                
                // نمایش خطاها
                if (errors.length > 0) {
                    let errorHtml = '';
                    errors.forEach(error => {
                        errorHtml += `<div class="error-message"><i class="fas fa-exclamation-circle"></i> ${error}</div>`;
                    });
                    passwordErrors.innerHTML = errorHtml;
                } else {
                    passwordErrors.innerHTML = '';
                }
            }
            
            // پاک کردن خطاها
            function clearErrors() {
                passwordErrors.innerHTML = '';
            }
            
            // به روزرسانی وضعیت الزامات
            function updateRequirement(element, isValid) {
                if (isValid) {
                    element.classList.remove('invalid');
                    element.classList.add('valid');
                    element.querySelector('i').className = 'fas fa-check';
                } else {
                    element.classList.remove('valid');
                    element.classList.add('invalid');
                    element.querySelector('i').className = 'fas fa-times';
                }
            }
            
            // بازنشانی الزامات
            function resetRequirements() {
                for (const key in requirements) {
                    requirements[key].classList.remove('valid');
                    requirements[key].classList.add('invalid');
                    requirements[key].querySelector('i').className = 'fas fa-times';
                }
            }
            
            // بررسی تطابق رمزهای عبور
            function validatePasswordMatch() {
                const password = newPasswordInput.value;
                const confirmPassword = confirmPasswordInput.value;
                
                if (confirmPassword.length > 0 && password !== confirmPassword) {
                    passwordMatch.style.display = 'flex';
                    return false;
                } else {
                    passwordMatch.style.display = 'none';
                    return true;
                }
            }
            
            // اعتبارسنجی کلی فرم
            function validateForm() {
                const password = newPasswordInput.value;
                const isPasswordStrong = password.length >= 8 && 
                                        /[a-z]/.test(password) && 
                                        /[A-Z]/.test(password) && 
                                        /[0-9]/.test(password) && 
                                        /[!@#$%^&*]/.test(password);
                
                const isPasswordMatch = validatePasswordMatch();
                
                if (isPasswordStrong && isPasswordMatch && password.length > 0) {
                    submitBtn.disabled = false;
                    return true;
                } else {
                    submitBtn.disabled = true;
                    return false;
                }
            }
            
            // ارسال فرم
            document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
                if (!validateForm()) {
                    e.preventDefault();
                    
                    // نمایش پیام خطای کلی
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'error';
                    errorDiv.innerHTML = '<i class="fas fa-exclamation-triangle"></i> لطفاً تمام الزامات رمز عبور را رعایت کنید و رمزهای عبور را به درستی تکرار کنید.';
                    
                    // اگر قبلاً خطا نمایش داده شده، آن را حذف کن
                    const existingError = document.querySelector('.error:not(#passwordMatch)');
                    if (existingError) {
                        existingError.remove();
                    }
                    
                    // اضافه کردن خطای جدید در بالای فرم
                    this.parentNode.insertBefore(errorDiv, this);
                    
                    // اسکرول به بالای فرم برای دیدن خطا
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
</body>
</html>