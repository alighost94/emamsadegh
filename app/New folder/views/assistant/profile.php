<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پروفایل معاون - هنرستان امام صادق</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { font-family: 'Vazir', sans-serif; }
        .sidebar { background: #2c3e50; color: white; height: 100vh; position: fixed; width: 250px; }
        .sidebar .nav-link { color: #bdc3c7; padding: 15px 20px; border-bottom: 1px solid #34495e; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: white; background: #34495e; }
        .main-content { margin-right: 250px; padding: 20px; }
        .profile-image { width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 4px solid #667eea; cursor: pointer; }
        .file-preview { max-width: 200px; max-height: 150px; border: 1px solid #ddd; border-radius: 5px; padding: 5px; }
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
                        <small>پنل معاون</small>
                        <div class="mt-3">
                            <?php if (!empty($data['profile']['profile_image'])): ?>
                                <img src="uploads/assistants/<?php echo $data['assistant']['id']; ?>/<?php echo $data['profile']['profile_image']; ?>" 
                                     class="profile-image" alt="پروفایل">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/150/667eea/ffffff?text=عکس" 
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
                            <a class="nav-link text-white" href="<?php echo BASE_URL; ?>assistant">
                                <i class="bi bi-speedometer2"></i> داشبورد
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white active" href="<?php echo BASE_URL; ?>assistant/profile">
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
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">تکمیل پروفایل معاون</h1>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        برای دسترسی به پنل معاون، باید پروفایل خود را تکمیل کنید.
                    </div>
                </div>

                <?php if (isset($data['error'])): ?>
                    <div class="alert alert-danger"><?php echo $data['error']; ?></div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header">
                        <h5>فرم تشکیل پرونده الکترونیکی معاون</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-4 text-center mb-4">
                                    <label for="profile_image" class="d-block mb-3">
                                        <?php if (!empty($data['profile']['profile_image'])): ?>
                                            <img src="uploads/assistants/<?php echo $data['assistant']['id']; ?>/<?php echo $data['profile']['profile_image']; ?>" 
                                                 class="profile-image" id="profile_preview" alt="پروفایل">
                                        <?php else: ?>
                                            <img src="https://via.placeholder.com/150/667eea/ffffff?text=عکس" 
                                                 class="profile-image" id="profile_preview" alt="پروفایل">
                                        <?php endif; ?>
                                    </label>
                                    <input type="file" name="profile_image" id="profile_image" accept="image/*" class="d-none" onchange="previewImage(this, 'profile_preview')">
                                    <div class="text-muted small">عکس پرسنلی</div>
                                </div>
                                
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">کد پرسنلی</label>
                                            <input type="text" name="personnel_code" class="form-control" 
                                                   value="<?php echo $data['profile']['personnel_code'] ?? ''; ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">کد ملی</label>
                                            <input type="text" class="form-control" 
                                                   value="<?php echo $_SESSION['national_code'] ?? ''; ?>" disabled>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">شماره حساب بانک ملی</label>
                                            <input type="text" name="bank_account_number" class="form-control" 
                                                   value="<?php echo $data['profile']['bank_account_number'] ?? ''; ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">شماره کارت بانک ملی</label>
                                            <input type="text" name="bank_card_number" class="form-control" 
                                                   value="<?php echo $data['profile']['bank_card_number'] ?? ''; ?>" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">مدرک تحصیلی</label>
                                    <select name="education_degree" class="form-select" required>
                                        <option value="">انتخاب کنید</option>
                                        <option value="diploma" <?php echo ($data['profile']['education_degree'] ?? '') == 'diploma' ? 'selected' : ''; ?>>دیپلم</option>
                                        <option value="associate" <?php echo ($data['profile']['education_degree'] ?? '') == 'associate' ? 'selected' : ''; ?>>کاردانی</option>
                                        <option value="bachelor" <?php echo ($data['profile']['education_degree'] ?? '') == 'bachelor' ? 'selected' : ''; ?>>کارشناسی</option>
                                        <option value="master" <?php echo ($data['profile']['education_degree'] ?? '') == 'master' ? 'selected' : ''; ?>>کارشناسی ارشد</option>
                                        <option value="phd" <?php echo ($data['profile']['education_degree'] ?? '') == 'phd' ? 'selected' : ''; ?>>دکتری</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">رشته تحصیلی</label>
                                    <input type="text" name="major" class="form-control" 
                                           value="<?php echo $data['profile']['major'] ?? ''; ?>" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">کد پستی</label>
                                    <input type="text" name="postal_code" class="form-control" 
                                           value="<?php echo $data['profile']['postal_code'] ?? ''; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">روزهای حضور در هنرستان</label>
                                    <div class="border p-3 rounded">
                                        <?php 
                                        $days = ['saturday' => 'شنبه', 'sunday' => 'یکشنبه', 'monday' => 'دوشنبه', 'tuesday' => 'سه‌شنبه', 'wednesday' => 'چهارشنبه'];
                                        $selected_days = explode(',', $data['profile']['presence_days'] ?? '');
                                        ?>
                                        <?php foreach ($days as $key => $label): ?>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" name="presence_days[]" 
                                                       value="<?php echo $key; ?>" 
                                                       <?php echo in_array($key, $selected_days) ? 'checked' : ''; ?>>
                                                <label class="form-check-label"><?php echo $label; ?></label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">آدرس کامل</label>
                                <textarea name="address" class="form-control" rows="3" required><?php echo $data['profile']['address'] ?? ''; ?></textarea>
                            </div>

                            <hr>
                            
                            <h6 class="mb-3">بارگذاری مدارک</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">تصویر کارت ملی</label>
                                    <input type="file" name="national_card_image" class="form-control" accept="image/*">
                                    <?php if (!empty($data['profile']['national_card_image'])): ?>
                                        <div class="mt-2">
                                            <img src="uploads/assistants/<?php echo $data['assistant']['id']; ?>/<?php echo $data['profile']['national_card_image']; ?>" 
                                                 class="file-preview" alt="کارت ملی">
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">تصویر شناسنامه</label>
                                    <input type="file" name="birth_certificate_image" class="form-control" accept="image/*">
                                    <?php if (!empty($data['profile']['birth_certificate_image'])): ?>
                                        <div class="mt-2">
                                            <img src="uploads/assistants/<?php echo $data['assistant']['id']; ?>/<?php echo $data['profile']['birth_certificate_image']; ?>" 
                                                 class="file-preview" alt="شناسنامه">
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">فایل PDF حکم کارگزینی</label>
                                    <input type="file" name="decree_file" class="form-control" accept=".pdf">
                                    <?php if (!empty($data['profile']['decree_file'])): ?>
                                        <div class="mt-2">
                                            <a href="uploads/assistants/<?php echo $data['assistant']['id']; ?>/<?php echo $data['profile']['decree_file']; ?>" 
                                               target="_blank" class="btn btn-sm btn-outline-primary">
                                                مشاهده حکم
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">فرم خلاصه سوابق (PDF)</label>
                                    <input type="file" name="resume_file" class="form-control" accept=".pdf">
                                    <?php if (!empty($data['profile']['resume_file'])): ?>
                                        <div class="mt-2">
                                            <a href="uploads/assistants/<?php echo $data['assistant']['id']; ?>/<?php echo $data['profile']['resume_file']; ?>" 
                                               target="_blank" class="btn btn-sm btn-outline-primary">
                                                مشاهده سوابق
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <hr>
                            
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="terms_accepted" id="terms_accepted" 
                                           <?php echo ($data['profile']['terms_accepted'] ?? 0) ? 'checked' : ''; ?> required>
                                    <label class="form-check-label" for="terms_accepted">
                                        با کلیه شرایط و قوانین هنرستان امام صادق موافقم و متعهد به رعایت آنها هستم.
                                    </label>
                                </div>
                            </div>
                            
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary btn-lg">تکمیل پروفایل و ورود به پنل</button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        const file = input.files[0];
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
        }
        
        if (file) {
            reader.readAsDataURL(file);
        }
    }
    
    // کلیک روی عکس پروفایل
    document.getElementById('profile_preview').addEventListener('click', function() {
        document.getElementById('profile_image').click();
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>