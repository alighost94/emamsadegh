<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پنل معاون - هنرستان امام صادق</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { font-family: 'Vazir', sans-serif; }
        
        /* استایل سایدبار */
        .sidebar { 
            background: #2c3e50; 
            color: white; 
            height: 100vh; 
            position: fixed; 
            width: 250px; 
            transition: all 0.3s;
            z-index: 1000;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-content {
                margin-right: 0 !important;
                padding: 15px;
            }
        }

        .sidebar .nav-link { color: #bdc3c7; padding: 15px 20px; border-bottom: 1px solid #34495e; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: white; background: #34495e; }
        .main-content { margin-right: 250px; padding: 20px; transition: all 0.3s; }
        
        /* استایل‌های اصلی */
        .profile-image { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid #667eea; }
        .welcome-card { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            color: white; 
            border-radius: 15px; 
            padding: 30px; 
            margin-bottom: 30px; 
        }
        .stat-card { 
            background: white; 
            border-radius: 10px; 
            padding: 25px; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.1); 
            margin-bottom: 20px; 
            border-right: 4px solid #667eea;
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .quick-action { 
            background: white; 
            border-radius: 10px; 
            padding: 20px; 
            text-align: center; 
            transition: transform 0.3s;
            border: 1px solid #e9ecef;
            height: 100%;
        }
        .quick-action:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 10px 25px rgba(0,0,0,0.1); 
            border-color: #667eea;
        }

        /* استایل‌های ریسپانسیو */
        @media (max-width: 768px) {
            .welcome-card {
                padding: 20px;
                margin-bottom: 20px;
            }
            .stat-card {
                padding: 20px;
                margin-bottom: 15px;
            }
            .quick-action {
                padding: 15px;
                margin-bottom: 15px;
            }
            .profile-image {
                width: 80px;
                height: 80px;
            }
        }

        /* دکمه منو برای موبایل */
        .mobile-menu-btn {
            display: none;
            position: fixed;
            top: 15px;
            right: 15px;
            z-index: 1100;
            background: #dc3545;
            border: none;
            border-radius: 5px;
            color: white;
            padding: 8px 12px;
            font-size: 1.2rem;
        }
        
        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: block;
            }
        }

        /* استایل برای overlay در موبایل */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }
        
        @media (max-width: 768px) {
            .sidebar-overlay.show {
                display: block;
            }
        }

        /* رنگ‌های مختلف برای کارت‌های آمار */
        .stat-card:nth-child(1) { border-right-color: #667eea; }
        .stat-card:nth-child(2) { border-right-color: #28a745; }
        .stat-card:nth-child(3) { border-right-color: #dc3545; }
        .stat-card:nth-child(4) { border-right-color: #ffc107; }

        /* بهبود ظاهر هشدارها */
        .alert-custom {
            border-radius: 10px;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <!-- دکمه منو برای موبایل -->
    <button class="mobile-menu-btn" id="mobileMenuBtn">
        <i class="bi bi-list"></i>
    </button>

    <!-- Overlay برای موبایل -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="container-fluid">
        <div class="row">
            <!-- سایدبار -->
            <div class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="p-4 text-center text-white border-bottom">
                        <h5>هنرستان امام صادق</h5>
                        <small>پنل معاون</small>
                        <div class="mt-3">
                            <?php if (!empty($data['profile']['profile_image'])): ?>
                                <img src="uploads/assistants/<?php echo $data['assistant']['id']; ?>/<?php echo $data['profile']['profile_image']; ?>" 
                                     class="profile-image" alt="پروفایل">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/120/667eea/ffffff?text=<?php echo substr($data['user_name'], 0, 1); ?>" 
                                     class="profile-image" alt="پروفایل">
                            <?php endif; ?>
                        </div>
                        <div class="mt-2 small">
                            <i class="bi bi-person-circle"></i>
                            <?php echo $data['user_name']; ?>
                        </div>
                        <div class="mt-1 small text-muted">
                            معاون پایه <?php echo $data['assistant']['grade_name']; ?>
                        </div>
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link text-white active" href="<?php echo BASE_URL; ?>assistant">
                                <i class="bi bi-speedometer2"></i> داشبورد
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="<?php echo BASE_URL; ?>assistant/profile">
                                <i class="bi bi-person"></i> پروفایل من
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="<?php echo BASE_URL; ?>assistant/students">
                                <i class="bi bi-people"></i> مدیریت دانش‌آموزان
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="<?php echo BASE_URL; ?>assistant/disciplinary">
                                <i class="bi bi-shield-exclamation"></i> پرونده انضباطی
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="<?php echo BASE_URL; ?>assistant/attendance">
                                <i class="bi bi-clipboard-check"></i> حضور و غیاب
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="<?php echo BASE_URL; ?>assistant/grades">
                                <i class="bi bi-journal-text"></i> کارنامه آموزشی
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="<?php echo BASE_URL; ?>auth/logout">
                                <i class="bi bi-box-arrow-left"></i> خروج
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- محتوای اصلی -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show alert-custom" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <div>
                                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="welcome-card">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="mb-3">سلام، <?php echo $data['user_name']; ?>! 👋</h3>
                            <p class="mb-2">به پنل معاونت پایه <strong><?php echo $data['assistant']['grade_name']; ?></strong> خوش آمدید</p>
                            <p class="mb-0 opacity-75">شما مسئول نظارت بر امور آموزشی و انضباطی این پایه هستید.</p>
                        </div>
                        <div class="col-md-4 text-center">
                            <?php if (!empty($data['profile']['profile_image'])): ?>
                                <img src="uploads/assistants/<?php echo $data['assistant']['id']; ?>/<?php echo $data['profile']['profile_image']; ?>" 
                                     class="profile-image" alt="پروفایل">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/120/667eea/ffffff?text=<?php echo substr($data['user_name'], 0, 1); ?>" 
                                     class="profile-image" alt="پروفایل">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- آمار کلی -->
                <div class="row">
                    <div class="col-xl-3 col-md-6">
                        <div class="stat-card">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-people" style="font-size: 2.5rem; color: #667eea;"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h4 class="mb-1"><?php echo $data['students_count']; ?></h4>
                                    <p class="mb-0 text-muted">تعداد دانش‌آموزان</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="stat-card">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-house-door" style="font-size: 2.5rem; color: #28a745;"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h4 class="mb-1"><?php echo $data['classes_count']; ?></h4>
                                    <p class="mb-0 text-muted">تعداد کلاس‌ها</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="stat-card">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-person-x" style="font-size: 2.5rem; color: #dc3545;"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h4 class="mb-1"><?php echo $data['today_absent_count']; ?></h4>
                                    <p class="mb-0 text-muted">غایبین امروز</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="stat-card">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-shield-exclamation" style="font-size: 2.5rem; color: #ffc107;"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h4 class="mb-1"><?php echo $data['disciplinary_cases'] ?? '0'; ?></h4>
                                    <p class="mb-0 text-muted">پرونده‌های انضباطی</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- اقدامات سریع -->
                <div class="row mt-4">
                    <div class="col-12">
                        <h4 class="mb-3">🔗 اقدامات سریع</h4>
                    </div>
                    <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                        <a href="<?php echo BASE_URL; ?>assistant/students" class="text-decoration-none">
                            <div class="quick-action">
                                <i class="bi bi-people-fill mb-3" style="font-size: 2.5rem; color: #667eea;"></i>
                                <h6 class="mb-2">مدیریت دانش‌آموزان</h6>
                                <small class="text-muted">مشاهده و مدیریت اطلاعات دانش‌آموزان</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                        <a href="<?php echo BASE_URL; ?>assistant/disciplinary" class="text-decoration-none">
                            <div class="quick-action">
                                <i class="bi bi-shield-exclamation mb-3" style="font-size: 2.5rem; color: #dc3545;"></i>
                                <h6 class="mb-2">ثبت تخلف انضباطی</h6>
                                <small class="text-muted">ثبت تخلفات و مدیریت نمره انضباطی</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                        <a href="<?php echo BASE_URL; ?>assistant/attendance" class="text-decoration-none">
                            <div class="quick-action">
                                <i class="bi bi-clipboard-check mb-3" style="font-size: 2.5rem; color: #28a745;"></i>
                                <h6 class="mb-2">گزارش حضور و غیاب</h6>
                                <small class="text-muted">مشاهده گزارشات روزانه حضور و غیاب</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-xl-3 col-lg-4 col-md-6 mb-3">
                        <a href="<?php echo BASE_URL; ?>assistant/grades" class="text-decoration-none">
                            <div class="quick-action">
                                <i class="bi bi-graph-up mb-3" style="font-size: 2.5rem; color: #ffc107;"></i>
                                <h6 class="mb-2">کارنامه آموزشی</h6>
                                <small class="text-muted">مشاهده نمرات و عملکرد آموزشی</small>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- هشدارهای مهم -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card border-warning alert-custom">
                            <div class="card-header bg-warning text-dark d-flex align-items-center">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <h6 class="mb-0">هشدارهای مهم</h6>
                            </div>
                            <div class="card-body">
                                <?php if ($data['today_absent_count'] > 0): ?>
                                    <div class="alert alert-warning mb-3 alert-custom">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-person-x me-2"></i>
                                            <div>
                                                <strong>امروز <?php echo $data['today_absent_count']; ?> دانش‌آموز غایب هستند.</strong>
                                                <a href="<?php echo BASE_URL; ?>assistant/attendance" class="alert-link me-2">مشاهده جزئیات</a>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-success mb-3 alert-custom">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-check-circle me-2"></i>
                                            <div>
                                                <strong>امروز همه دانش‌آموزان حاضر هستند.</strong>
                                                <span class="text-muted">وضعیت مطلوب</span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (($data['disciplinary_cases'] ?? 0) > 0): ?>
                                    <div class="alert alert-danger mb-3 alert-custom">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-shield-exclamation me-2"></i>
                                            <div>
                                                <strong><?php echo $data['disciplinary_cases']; ?> پرونده انضباطی نیاز به رسیدگی دارد.</strong>
                                                <a href="<?php echo BASE_URL; ?>assistant/disciplinary" class="alert-link me-2">بررسی پرونده‌ها</a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="alert alert-info mb-0 alert-custom">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-info-circle me-2"></i>
                                        <div>
                                            <strong>آخرین بروزرسانی:</strong>
                                            <span class="text-muted"><?php echo date('Y/m/d H:i'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- اطلاعات پایه -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card border-info alert-custom">
                            <div class="card-header bg-info text-white d-flex align-items-center">
                                <i class="bi bi-info-circle me-2"></i>
                                <h6 class="mb-0">اطلاعات پایه <?php echo $data['assistant']['grade_name']; ?></h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-people-fill me-3 text-primary" style="font-size: 1.5rem;"></i>
                                            <div>
                                                <h6 class="mb-1"><?php echo $data['students_count']; ?> دانش‌آموز</h6>
                                                <small class="text-muted">تعداد کل دانش‌آموزان</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-house-door me-3 text-success" style="font-size: 1.5rem;"></i>
                                            <div>
                                                <h6 class="mb-1"><?php echo $data['classes_count']; ?> کلاس</h6>
                                                <small class="text-muted">تعداد کل کلاس‌ها</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-book me-3 text-warning" style="font-size: 1.5rem;"></i>
                                            <div>
                                                <h6 class="mb-1"><?php echo $data['majors_count'] ?? '3'; ?> رشته</h6>
                                                <small class="text-muted">تعداد رشته‌های تحصیلی</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // مدیریت منوی موبایل
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const sidebar = document.querySelector('.sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const mainContent = document.querySelector('.main-content');

        function toggleMobileMenu() {
            sidebar.classList.toggle('show');
            sidebarOverlay.classList.toggle('show');
            document.body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : '';
        }

        mobileMenuBtn.addEventListener('click', toggleMobileMenu);
        sidebarOverlay.addEventListener('click', toggleMobileMenu);

        // بستن منو هنگام کلیک روی لینک‌ها در موبایل
        if (window.innerWidth <= 768) {
            const navLinks = document.querySelectorAll('.sidebar .nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', toggleMobileMenu);
            });
        }

        // انیمیشن برای کارت‌های آمار
        const statCards = document.querySelectorAll('.stat-card');
        statCards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
        });
    });
    </script>
</body>
</html>