<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پنل معلم - هنرستان امام صادق</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { font-family: 'Vazir', sans-serif; }
        .sidebar { background: #2c3e50; color: white; height: 100vh; position: fixed; width: 250px; }
        .sidebar .nav-link { color: #bdc3c7; padding: 15px 20px; border-bottom: 1px solid #34495e; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: white; background: #34495e; }
        .main-content { margin-right: 250px; padding: 20px; }
        .profile-image { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid #667eea; }
        .welcome-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px; padding: 30px; margin-bottom: 30px; }
        .stat-card { background: white; border-radius: 10px; padding: 25px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-bottom: 20px; border-right: 4px solid #667eea; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- سایدبار -->
            <div class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="p-4 text-center text-white border-bottom">
                        <h5>هنرستان امام صادق</h5>
                        <small>پنل معلم</small>
                        <div class="mt-3">
                            <?php if (!empty($data['profile']['profile_image'])): ?>
                                <img src="uploads/teachers/<?php echo $data['teacher']['id']; ?>/<?php echo $data['profile']['profile_image']; ?>" 
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
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link text-white active" href="<?php echo BASE_URL; ?>teacher">
                                <i class="bi bi-speedometer2"></i> داشبورد
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="<?php echo BASE_URL; ?>teacher/profile">
                                <i class="bi bi-person"></i> پروفایل من
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="<?php echo BASE_URL; ?>teacher/attendance">
                                <i class="bi bi-clipboard-check"></i> حضور و غیاب
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="<?php echo BASE_URL; ?>teacher/grades">
                                <i class="bi bi-journal-text"></i> ثبت نمرات
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
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="welcome-card">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3>سلام، <?php echo $data['user_name']; ?>!</h3>
                            <p>به پنل معلمین هنرستان امام صادق خوش آمدید</p>
                        </div>
                        <div class="col-md-4 text-center">
                            <?php if (!empty($data['profile']['profile_image'])): ?>
                                <img src="uploads/teachers/<?php echo $data['teacher']['id']; ?>/<?php echo $data['profile']['profile_image']; ?>" 
                                     class="profile-image" alt="پروفایل">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/120/667eea/ffffff?text=<?php echo substr($data['user_name'], 0, 1); ?>" 
                                     class="profile-image" alt="پروفایل">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="stat-card">
                            <i class="bi bi-journal-text" style="font-size: 2.5rem; color: #667eea;"></i>
                            <h4><?php echo count($data['courses']); ?></h4>
                            <p>تعداد دروس تدریسی</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <i class="bi bi-person-check" style="font-size: 2.5rem; color: #28a745;"></i>
                            <h4>۰</h4>
                            <p>دانش‌آموزان تحت تدریس</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <i class="bi bi-calendar-check" style="font-size: 2.5rem; color: #ffc107;"></i>
                            <h4>امروز</h4>
                            <p><?php echo date('Y/m/d'); ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>دروس تحت تدریس</h5>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($data['courses'])): ?>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>کد درس</th>
                                                    <th>نام درس</th>
                                                    <th>رشته</th>
                                                    <th>پایه</th>
                                                    <th>واحد</th>
                                                    <th>نوع</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($data['courses'] as $course): ?>
                                                    <tr>
                                                        <td><?php echo $course['course_code']; ?></td>
                                                        <td><?php echo $course['course_name']; ?></td>
                                                        <td><?php echo $course['major_name']; ?></td>
                                                        <td><?php echo $course['grade_name']; ?></td>
                                                        <td><?php echo $course['unit'] ?? 'N/A'; ?></td>
                                                        <td>
                                                            <?php echo $course['course_type'] == 'poodmani' ? 'پودمانی' : 'غیر پودمانی'; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted">هنوز درسی به شما تخصیص داده نشده است.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>