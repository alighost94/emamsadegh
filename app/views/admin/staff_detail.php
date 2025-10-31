<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پرونده همکار - پنل مدیر</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { font-family: 'Vazir', sans-serif; }
        body, html { margin: 0; padding: 0; height: 100%; background: #f8f9fa; }
        
        /* --- Layout --- */
        .layout {
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        /* --- Sidebar --- */
        .sidebar {
            width: 250px;
            background: #2c3e50;
            color: white;
            flex-shrink: 0;
            position: sticky;
            top: 0;
            height: 100vh;
        }
        
        /* --- Main Content --- */
        .main-content {
            flex-grow: 1;
            padding: 20px;
        }
        
        /* --- Custom Components --- */
        .profile-image {
            width: 150px; height: 150px;
            border-radius: 50%; object-fit: cover;
            border: 4px solid #667eea;
        }
        .info-card { border-right: 4px solid #667eea; }
        .score-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white; border: none;
        }
        .encouragement-badge { background-color: #28a745; }
        .disciplinary-badge { background-color: #dc3545; }
        .file-preview {
            max-width: 200px; max-height: 150px;
            border: 1px solid #ddd; border-radius: 5px; padding: 5px;
        }
        
        /* --- Responsive --- */
        @media (max-width: 992px) {
            .layout { flex-direction: column; }
            .sidebar { width: 100%; height: auto; }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- سایدبار -->
            <?php include 'app/views/partials/sidebar.php'; ?>
            
            <!-- محتوای اصلی -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">
                        پرونده 
                        <?php echo $data['staff_type'] == 'teacher' ? 'معلم' : 'معاون'; ?>: 
                        <?php echo isset($data['staff_user']['first_name']) ? $data['staff_user']['first_name'] . ' ' . $data['staff_user']['last_name'] : 'نامشخص'; ?>
                    </h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="<?php echo BASE_URL; ?>admin/staffFiles" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-right"></i> بازگشت به لیست همکاران
                        </a>
                    </div>
                </div>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($data['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $data['error']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <!-- اطلاعات شخصی -->
                    <div class="col-md-4">
                        <div class="card info-card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">اطلاعات شخصی</h5>
                            </div>
                            <div class="card-body text-center">
                                <?php if (!empty($data['staff_profile']['profile_image'])): ?>
                                    <img src="<?php echo BASE_URL; ?>uploads/<?php echo $data['staff_type']; ?>s/<?php echo $data['staff']['id']; ?>/<?php echo $data['staff_profile']['profile_image']; ?>" 
                                         class="profile-image mb-3" alt="پروفایل" onerror="this.src='https://via.placeholder.com/150/667eea/ffffff?text=<?php echo isset($data['staff_user']['first_name']) ? substr($data['staff_user']['first_name'], 0, 1) : 'U'; ?>'">
                                <?php else: ?>
                                    <img src="https://via.placeholder.com/150/667eea/ffffff?text=<?php echo isset($data['staff_user']['first_name']) ? substr($data['staff_user']['first_name'], 0, 1) : 'U'; ?>" 
                                         class="profile-image mb-3" alt="پروفایل">
                                <?php endif; ?>
                                
                                <h5><?php echo isset($data['staff_user']['first_name']) ? $data['staff_user']['first_name'] . ' ' . $data['staff_user']['last_name'] : 'نامشخص'; ?></h5>
                                <p class="text-muted">
                                    <?php echo $data['staff_type'] == 'teacher' ? 'معلم' : 'معاون پایه ' . ($data['staff']['grade_name'] ?? 'نامشخص'); ?>
                                </p>
                                
                                <div class="text-start">
                                    <p><strong>شماره موبایل:</strong> <?php echo isset($data['staff_user']['mobile']) ? $data['staff_user']['mobile'] : 'ثبت نشده'; ?></p>
                                    <p><strong>کد ملی:</strong> <?php echo isset($data['staff_user']['national_code']) ? $data['staff_user']['national_code'] : 'ثبت نشده'; ?></p>
                                    <?php if (!empty($data['staff_profile']['personnel_code'])): ?>
                                        <p><strong>کد پرسنلی:</strong> <?php echo $data['staff_profile']['personnel_code']; ?></p>
                                    <?php endif; ?>
                                    <?php if (!empty($data['staff_profile']['education_degree'])): ?>
                                        <p><strong>مدرک تحصیلی:</strong> 
                                            <?php 
                                            $degrees = [
                                                'diploma' => 'دیپلم',
                                                'associate' => 'کاردانی', 
                                                'bachelor' => 'کارشناسی',
                                                'master' => 'کارشناسی ارشد',
                                                'phd' => 'دکتری'
                                            ];
                                            echo $degrees[$data['staff_profile']['education_degree']] ?? $data['staff_profile']['education_degree'];
                                            ?>
                                        </p>
                                    <?php endif; ?>
                                    <?php if (!empty($data['staff_profile']['major'])): ?>
                                        <p><strong>رشته تحصیلی:</strong> <?php echo $data['staff_profile']['major']; ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- اطلاعات تماس -->
                        <?php if (!empty($data['staff_profile']['address']) || !empty($data['staff_profile']['postal_code'])): ?>
                        <div class="card info-card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">اطلاعات تماس</h5>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($data['staff_profile']['address'])): ?>
                                    <p><strong>آدرس:</strong> <?php echo $data['staff_profile']['address']; ?></p>
                                <?php endif; ?>
                                <?php if (!empty($data['staff_profile']['postal_code'])): ?>
                                    <p><strong>کد پستی:</strong> <?php echo $data['staff_profile']['postal_code']; ?></p>
                                <?php endif; ?>
                                <?php if (!empty($data['staff_profile']['bank_account_number'])): ?>
                                    <p><strong>شماره حساب:</strong> <?php echo $data['staff_profile']['bank_account_number']; ?></p>
                                <?php endif; ?>
                                <?php if (!empty($data['staff_profile']['bank_card_number'])): ?>
                                    <p><strong>شماره کارت:</strong> <?php echo $data['staff_profile']['bank_card_number']; ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- مدارک -->
                        <div class="card info-card">
                            <div class="card-header">
                                <h5 class="mb-0">مدارک بارگذاری شده</h5>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($data['staff_profile']['national_card_image'])): ?>
                                    <div class="mb-2">
                                        <strong>کارت ملی:</strong>
                                        <img src="<?php echo BASE_URL; ?>uploads/<?php echo $data['staff_type']; ?>s/<?php echo $data['staff']['id']; ?>/<?php echo $data['staff_profile']['national_card_image']; ?>" 
                                             class="file-preview d-block mt-1" alt="کارت ملی" onerror="this.style.display='none'">
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($data['staff_profile']['birth_certificate_image'])): ?>
                                    <div class="mb-2">
                                        <strong>شناسنامه:</strong>
                                        <img src="<?php echo BASE_URL; ?>uploads/<?php echo $data['staff_type']; ?>s/<?php echo $data['staff']['id']; ?>/<?php echo $data['staff_profile']['birth_certificate_image']; ?>" 
                                             class="file-preview d-block mt-1" alt="شناسنامه" onerror="this.style.display='none'">
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($data['staff_profile']['decree_file'])): ?>
                                    <div class="mb-2">
                                        <strong>حکم کارگزینی:</strong>
                                        <a href="<?php echo BASE_URL; ?>uploads/<?php echo $data['staff_type']; ?>s/<?php echo $data['staff']['id']; ?>/<?php echo $data['staff_profile']['decree_file']; ?>" 
                                           target="_blank" class="btn btn-sm btn-outline-primary mt-1">
                                            مشاهده PDF
                                        </a>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($data['staff_profile']['resume_file'])): ?>
                                    <div class="mb-2">
                                        <strong>خلاصه سوابق:</strong>
                                        <a href="<?php echo BASE_URL; ?>uploads/<?php echo $data['staff_type']; ?>s/<?php echo $data['staff']['id']; ?>/<?php echo $data['staff_profile']['resume_file']; ?>" 
                                           target="_blank" class="btn btn-sm btn-outline-primary mt-1">
                                            مشاهده PDF
                                        </a>
                                    </div>
                                <?php endif; ?>

                                <?php if (empty($data['staff_profile']['national_card_image']) && empty($data['staff_profile']['birth_certificate_image']) && empty($data['staff_profile']['decree_file']) && empty($data['staff_profile']['resume_file'])): ?>
                                    <div class="alert alert-info text-center">
                                        <i class="bi bi-info-circle"></i>
                                        هیچ مدرکی بارگذاری نشده است.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- بخش سمت راست -->
                    <div class="col-md-8">
                        <!-- امتیاز و آمار -->
                        <div class="card score-card mb-4">
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-md-4">
                                        <h2 class="<?php echo ($data['staff_score']['current_score'] ?? 100) >= 80 ? 'text-success' : 'text-warning'; ?>">
                                            <?php echo $data['staff_score']['current_score'] ?? 100; ?>
                                        </h2>
                                        <small>امتیاز فعلی</small>
                                    </div>
                                    <div class="col-md-4">
                                        <h2 class="text-success">+<?php echo $data['staff_score']['total_encouragement'] ?? 0; ?></h2>
                                        <small>مجموع تشویق‌ها</small>
                                    </div>
                                    <div class="col-md-4">
                                        <h2 class="text-danger">-<?php echo $data['staff_score']['total_disciplinary'] ?? 0; ?></h2>
                                        <small>مجموع تخلفات</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- فرم ثبت رکورد جدید -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">ثبت مورد جدید</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">نوع رکورد</label>
                                            <select name="record_type" class="form-select" required onchange="togglePoints(this.value)">
                                                <option value="">انتخاب کنید</option>
                                                <option value="encouragement">تشویقی</option>
                                                <option value="disciplinary">انضباطی</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">تاریخ</label>
                                            <input type="date" name="record_date" class="form-control" 
                                                   value="<?php echo date('Y-m-d'); ?>" required>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">عنوان</label>
                                        <input type="text" name="title" class="form-control" 
                                               placeholder="عنوان مورد را وارد کنید" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">شرح کامل</label>
                                        <textarea name="description" class="form-control" rows="3" 
                                                  placeholder="شرح کامل مورد را وارد کنید" required></textarea>
                                    </div>
                                    
                                    <div class="mb-3" id="pointsSection" style="display: none;">
                                        <label class="form-label">امتیاز</label>
                                        <select name="points" class="form-select" required>
                                            <option value="">انتخاب امتیاز</option>
                                            <option value="1">۱ امتیاز</option>
                                            <option value="2">۲ امتیاز</option>
                                            <option value="3">۳ امتیاز</option>
                                            <option value="5">۵ امتیاز</option>
                                            <option value="10">۱۰ امتیاز</option>
                                        </select>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle"></i> ثبت رکورد
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- تاریخچه رکوردها -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">تاریخچه رکوردها</h5>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($data['staff_records'])): ?>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>تاریخ</th>
                                                    <th>نوع</th>
                                                    <th>عنوان</th>
                                                    <th>امتیاز</th>
                                                    <th>ثبت کننده</th>
                                                    <th>وضعیت</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($data['staff_records'] as $record): ?>
                                                    <tr>
                                                        <td>
                                                            <small><?php echo $record['jalali_date']; ?></small>
                                                        </td>
                                                        <td>
                                                            <?php if ($record['record_type'] == 'encouragement'): ?>
                                                                <span class="badge encouragement-badge">تشویقی</span>
                                                            <?php else: ?>
                                                                <span class="badge disciplinary-badge">انضباطی</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <small><?php echo $record['title']; ?></small>
                                                            <br>
                                                            <small class="text-muted"><?php echo substr($record['description'], 0, 50); ?>...</small>
                                                        </td>
                                                        <td>
                                                            <?php if ($record['record_type'] == 'encouragement'): ?>
                                                                <span class="text-success">+<?php echo $record['points']; ?></span>
                                                            <?php else: ?>
                                                                <span class="text-danger">-<?php echo $record['points']; ?></span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <small><?php echo $record['created_first_name'] . ' ' . $record['created_last_name']; ?></small>
                                                        </td>
                                                        <td>
                                                            <?php if ($record['status'] == 'approved'): ?>
                                                                <span class="badge bg-success">تأیید شده</span>
                                                            <?php elseif ($record['status'] == 'rejected'): ?>
                                                                <span class="badge bg-danger">رد شده</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-warning text-dark">در انتظار</span>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-info text-center">
                                        <i class="bi bi-info-circle"></i>
                                        هیچ رکوردی ثبت نشده است.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
    function togglePoints(recordType) {
        const pointsSection = document.getElementById('pointsSection');
        if (recordType) {
            pointsSection.style.display = 'block';
        } else {
            pointsSection.style.display = 'none';
        }
    }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>