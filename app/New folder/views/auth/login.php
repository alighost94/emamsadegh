<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ورود به سیستم - هنرستان امام صادق</title>
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
        
        .login-container {
            background: white;
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            position: relative;
            overflow: hidden;
        }
        
        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(to left, #667eea, #764ba2);
        }
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo h1 {
            color: #333;
            font-size: 24px;
            margin-bottom: 10px;
            position: relative;
            display: inline-block;
        }
        
        .logo h1::after {
            content: '';
            position: absolute;
            bottom: -5px;
            right: 0;
            width: 50%;
            height: 3px;
            background: linear-gradient(to left, #667eea, #764ba2);
            border-radius: 3px;
        }
        
        .logo p {
            color: #666;
            font-size: 14px;
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
            padding: 12px 15px;
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
        
        .error {
            background: #fee;
            color: #c33;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #fcc;
            animation: shake 0.5s ease-in-out;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        .login-info {
            text-align: center;
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #eaeaea;
        }
        
        .copyright {
            text-align: center;
            margin-top: 15px;
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
            .login-container {
                padding: 30px 20px;
            }
            
            .logo h1 {
                font-size: 20px;
            }
            
            .logo p {
                font-size: 13px;
            }
            
            .form-group input {
                padding: 10px 12px;
                font-size: 13px;
            }
            
            .btn {
                padding: 10px;
                font-size: 15px;
            }
            
            .login-info {
                font-size: 11px;
                padding: 12px;
            }
        }
        
        @media (max-width: 350px) {
            .login-container {
                padding: 25px 15px;
            }
            
            .logo h1 {
                font-size: 18px;
            }
            
            .form-group label {
                font-size: 14px;
            }
            
            .copyright {
                font-size: 11px;
            }
        }
        
        /* انیمیشن ورود */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .login-container {
            animation: fadeIn 0.8s ease-out;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1>هنرستان امام صادق</h1>
            <p>سیستم مدیریت آموزشی</p>
        </div>
        
        <?php if (isset($data['error'])): ?>
            <div class="error"><?php echo $data['error']; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>شماره موبایل</label>
                <input type="text" name="mobile" required placeholder="09xxxxxxxxx">
                <i class="fas fa-mobile-alt"></i>
            </div>
            
            <div class="form-group">
                <label>رمز عبور (کد ملی)</label>
                <input type="password" name="password" required placeholder="کد ملی">
                <i class="fas fa-lock"></i>
            </div>
            
            <button type="submit" class="btn">ورود به سیستم</button>
        </form>
        
        <div class="login-info">
            <p>برای ورود از شماره موبایل و کد ملی خود استفاده کنید</p>
        </div>
        
        <div class="copyright">
            <i class="far fa-copyright"></i>
            طراحی شده توسط علیرضا میربیک
        </div>
    </div>
</body>
</html>