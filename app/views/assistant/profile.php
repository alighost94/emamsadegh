<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پروفایل معاون - هنرستان امام صادق</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/css/persian-datepicker.min.css">
    <style>
        * { font-family: 'Vazir', sans-serif; }
        .sidebar { background: #2c3e50; color: white; height: 100vh; position: fixed; width: 250px; }
        .sidebar .nav-link { color: #bdc3c7; padding: 15px 20px; border-bottom: 1px solid #34495e; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: white; background: #34495e; }
        .main-content { margin-right: 250px; padding: 20px; }
        .profile-image { width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 4px solid #667eea; cursor: pointer; transition: all 0.3s ease; }
        .profile-image:hover { border-color: #5a67d8; transform: scale(1.05); }
        .file-preview { max-width: 200px; max-height: 150px; border: 1px solid #ddd; border-radius: 5px; padding: 5px; cursor: pointer; }
        .file-preview:hover { border-color: #667eea; }
        .upload-progress { display: none; margin-top: 5px; }
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            color: white;
            font-size: 18px;
        }
        .loading-spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 2s linear infinite;
            margin-bottom: 15px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .form-required::after {
            content: " *";
            color: red;
        }
        .preview-container {
            position: relative;
            display: inline-block;
        }
        .preview-actions {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            display: none;
        }
        .preview-container:hover .preview-actions {
            display: block;
        }
        .image-modal .modal-dialog {
            max-width: 90%;
        }
        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: none;
        }
        .date-input-group {
            position: relative;
        }
        .date-picker-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            z-index: 5;
        }
        .persian-datepicker-container {
            z-index: 1060 !important;
        }
        .input-group-text {
            background-color: #e9ecef;
            border: 1px solid #ced4da;
        }
        .upload-placeholder {
            width: 150px;
            height: 150px;
            border: 3px dashed #dee2e6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }
        .upload-placeholder:hover {
            border-color: #667eea;
            background-color: #e9ecef;
        }
        .upload-icon {
            font-size: 3rem;
            color: #6c757d;
        }
        .retired-checkbox {
            border-left: 4px solid #ffc107;
            background-color: #fffbf0;
            padding: 10px 15px;
            border-radius: 5px;
            margin-top: 10px;
        }
        .file-required::after {
            content: " *";
            color: red;
        }
        .file-placeholder {
            width: 200px;
            height: 150px;
            border: 2px dashed #dee2e6;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
            margin-top: 10px;
        }
        .file-placeholder:hover {
            border-color: #667eea;
            background-color: #e9ecef;
        }
        .assistant-badge {
            background: linear-gradient(45deg, #667eea, #764ba2);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="text-center">
            <div class="loading-spinner"></div>
            <div>در حال ذخیره اطلاعات...</div>
            <small class="text-muted">لطفاً شکیبا باشید</small>
        </div>
    </div>

    <!-- Modal برای نمایش بزرگ عکس -->
    <div class="modal fade image-modal" id="imageModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">پیش‌نمایش عکس</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="" id="modalImage" style="max-width: 100%; max-height: 70vh;">
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- سایدبار -->
            <?php include 'app/views/assistant/partials/sidebar.php'; ?>
            
            <!-- محتوای اصلی -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">تکمیل پروفایل معاون</h1>
                    <span class="assistant-badge">
                        <i class="bi bi-person-gear"></i> پنل معاون
                    </span>
                </div>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
                <?php endif; ?>
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <?php endif; ?>
                <?php if (isset($data['error'])): ?>
                    <div class="alert alert-danger"><?php echo $data['error']; ?></div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header">
                        <h5><i class="bi bi-person-badge"></i> فرم تشکیل پرونده الکترونیکی معاون</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" id="profileForm" onsubmit="return validateForm()">
                            <!-- فیلد مخفی برای is_retired -->
                            <input type="hidden" name="is_retired" id="is_retired_hidden" value="<?php echo $data['profile']['is_retired'] ?? 0; ?>">
                            
                            <div class="row">
                                <div class="col-md-4 text-center mb-4">
                                    <div class="preview-container">
                                        <label for="profile_image" class="d-block mb-3">
                                            <?php if (!empty($data['profile']['profile_image']) && file_exists('uploads/assistants/' . $data['assistant']['id'] . '/' . $data['profile']['profile_image'])): ?>
                                                <img src="<?php echo BASE_URL . 'uploads/assistants/' . $data['assistant']['id'] . '/' . $data['profile']['profile_image']; ?>" 
                                                     class="profile-image" id="profile_preview" alt="پروفایل"
                                                     onclick="showImageModal(this.src)">
                                            <?php else: ?>
                                                <div class="upload-placeholder" id="profile_placeholder" onclick="document.getElementById('profile_image').click()">
                                                    <i class="bi bi-camera upload-icon"></i>
                                                </div>
                                                <img src="" class="profile-image" id="profile_preview" alt="پروفایل" 
                                                     style="display: none;" onclick="showImageModal(this.src)">
                                            <?php endif; ?>
                                        </label>
                                        <div class="preview-actions">
                                            <button type="button" class="btn btn-sm btn-light" onclick="document.getElementById('profile_image').click()">
                                                <i class="bi bi-camera"></i> تغییر عکس
                                            </button>
                                        </div>
                                    </div>
                                    <input type="file" name="profile_image" id="profile_image" accept="image/*" class="d-none" onchange="previewImage(this, 'profile_preview', 'profile_placeholder')" required>
                                    <div class="text-muted small file-required">عکس پرسنلی (حداکثر 2MB)</div>
                                    <div class="error-message" id="profile_image_error"></div>
                                </div>
                                
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label form-required">کد پرسنلی</label>
                                            <input type="text" name="personnel_code" class="form-control english-number" 
                                                   value="<?php echo $data['profile']['personnel_code'] ?? ''; ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">کد ملی</label>
                                            <input type="text" class="form-control" 
                                                   value="<?php echo $_SESSION['national_code'] ?? ''; ?>" disabled>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-12">
                                            <label class="form-label">پایه تحت مدیریت</label>
                                            <div class="border p-3 rounded bg-light">
                                                <?php 
                                                $grades = [
                                                    1 => 'پایه دهم',
                                                    2 => 'پایه یازدهم', 
                                                    3 => 'پایه دوازدهم'
                                                ];
                                                $assistant_grade = $data['assistant']['grade_id'] ?? 1;
                                                ?>
                                                <h6 class="text-primary">
                                                    <i class="bi bi-mortarboard"></i>
                                                    <?php echo $grades[$assistant_grade] ?? 'پایه دهم'; ?>
                                                </h6>
                                                <small class="text-muted">پایه تحت مدیریت شما به صورت خودکار تنظیم شده است</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label form-required">شماره حساب بانک ملی</label>
                                            <input type="text" name="bank_account_number" class="form-control english-number" 
                                                   value="<?php echo $data['profile']['bank_account_number'] ?? ''; ?>" 
                                                   placeholder="XXXX-XXXX-XXXX-XXXX" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label form-required">شماره کارت بانک ملی</label>
                                            <input type="text" name="bank_card_number" class="form-control english-number" 
                                                   value="<?php echo $data['profile']['bank_card_number'] ?? ''; ?>" 
                                                   placeholder="XXXX-XXXX-XXXX-XXXX" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label form-required">شماره شبا</label>
                                            <div class="input-group">
                                                <span class="input-group-text">IR</span>
                                                <input type="text" name="sheba_number" class="form-control english-number" 
                                                       value="<?php echo isset($data['profile']['sheba_number']) ? str_replace('IR', '', $data['profile']['sheba_number']) : ''; ?>" 
                                                       placeholder="24 رقم" required maxlength="24">
                                            </div>
                                            <div class="error-message" id="sheba_error"></div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label form-required">نام پدر</label>
                                            <input type="text" name="father_name" class="form-control" 
                                                   value="<?php echo $data['profile']['father_name'] ?? ''; ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label form-required">تاریخ تولد</label>
                                            <div class="date-input-group">
                                                <input type="text" name="birth_date_jalali" class="form-control date-picker" 
                                                       id="birth_date_jalali" 
                                                       value="<?php 
                                                           if (!empty($data['profile']['birth_date']) && $data['profile']['birth_date'] != '0000-00-00') {
                                                               echo \JalaliDate::gregorianToJalali($data['profile']['birth_date'], 'Y/m/d');
                                                           }
                                                       ?>" 
                                                       placeholder="1400/01/01" required readonly>
                                                <i class="bi bi-calendar-date date-picker-icon" onclick="$('#birth_date_jalali').focus();"></i>
                                            </div>
                                            <input type="hidden" name="birth_date" id="birth_date" 
                                                   value="<?php echo (!empty($data['profile']['birth_date']) && $data['profile']['birth_date'] != '0000-00-00') ? $data['profile']['birth_date'] : ''; ?>">
                                            <div class="error-message" id="birth_date_error"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label form-required">مدرک تحصیلی</label>
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
                                    <label class="form-label form-required">رشته تحصیلی</label>
                                    <input type="text" name="major" class="form-control" 
                                           value="<?php echo $data['profile']['major'] ?? ''; ?>" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label form-required">کد پستی</label>
                                    <input type="text" name="postal_code" class="form-control english-number" 
                                           value="<?php echo $data['profile']['postal_code'] ?? ''; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label form-required">روزهای حضور در هنرستان</label>
                                    <div class="border p-3 rounded">
                                        <?php 
                                        $days = ['saturday' => 'شنبه', 'sunday' => 'یکشنبه', 'monday' => 'دوشنبه', 'tuesday' => 'سه‌شنبه', 'wednesday' => 'چهارشنبه'];
                                        $selected_days = explode(',', $data['profile']['presence_days'] ?? '');
                                        ?>
                                        <?php foreach ($days as $key => $label): ?>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input presence-day" type="checkbox" name="presence_days[]" 
                                                       value="<?php echo $key; ?>" 
                                                       <?php echo in_array($key, $selected_days) ? 'checked' : ''; ?>>
                                                <label class="form-check-label"><?php echo $label; ?></label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="error-message" id="presence_days_error">حداقل یک روز باید انتخاب شود</div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label form-required">آدرس کامل</label>
                                <textarea name="address" class="form-control" rows="3" required><?php echo $data['profile']['address'] ?? ''; ?></textarea>
                            </div>

                            <hr>
                            
                            <h6 class="mb-3"><i class="bi bi-files"></i> بارگذاری مدارک</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label file-required">تصویر کارت ملی</label>
                                    <input type="file" name="national_card_image" class="form-control" accept="image/*" onchange="previewImage(this, 'national_card_preview', 'national_card_placeholder')" id="national_card_input">
                                    <?php if (!empty($data['profile']['national_card_image']) && file_exists('uploads/assistants/' . $data['assistant']['id'] . '/' . $data['profile']['national_card_image'])): ?>
                                        <div class="mt-2">
                                            <img src="<?php echo BASE_URL . 'uploads/assistants/' . $data['assistant']['id'] . '/' . $data['profile']['national_card_image']; ?>" 
                                                 class="file-preview" id="national_card_preview" alt="کارت ملی"
                                                 onclick="showImageModal(this.src)">
                                            <button type="button" class="btn btn-sm btn-outline-danger mt-1" onclick="removeFile('national_card_image')">
                                                <i class="bi bi-trash"></i> حذف
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="mt-2">
                                            <div class="file-placeholder" id="national_card_placeholder" onclick="document.getElementById('national_card_input').click()">
                                                <i class="bi bi-card-image upload-icon"></i>
                                            </div>
                                            <img src="" class="file-preview" id="national_card_preview" alt="کارت ملی"
                                                 onclick="showImageModal(this.src)" style="display: none;">
                                        </div>
                                    <?php endif; ?>
                                    <div class="error-message" id="national_card_error"></div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label file-required">تصویر شناسنامه</label>
                                    <input type="file" name="birth_certificate_image" class="form-control" accept="image/*" onchange="previewImage(this, 'birth_certificate_preview', 'birth_certificate_placeholder')" id="birth_certificate_input">
                                    <?php if (!empty($data['profile']['birth_certificate_image']) && file_exists('uploads/assistants/' . $data['assistant']['id'] . '/' . $data['profile']['birth_certificate_image'])): ?>
                                        <div class="mt-2">
                                            <img src="<?php echo BASE_URL . 'uploads/assistants/' . $data['assistant']['id'] . '/' . $data['profile']['birth_certificate_image']; ?>" 
                                                 class="file-preview" id="birth_certificate_preview" alt="شناسنامه"
                                                 onclick="showImageModal(this.src)">
                                            <button type="button" class="btn btn-sm btn-outline-danger mt-1" onclick="removeFile('birth_certificate_image')">
                                                <i class="bi bi-trash"></i> حذف
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <div class="mt-2">
                                            <div class="file-placeholder" id="birth_certificate_placeholder" onclick="document.getElementById('birth_certificate_input').click()">
                                                <i class="bi bi-file-earmark-image upload-icon"></i>
                                            </div>
                                            <img src="" class="file-preview" id="birth_certificate_preview" alt="شناسنامه"
                                                 onclick="showImageModal(this.src)" style="display: none;">
                                        </div>
                                    <?php endif; ?>
                                    <div class="error-message" id="birth_certificate_error"></div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label file-required">فایل PDF حکم کارگزینی</label>
                                    <input type="file" name="decree_file" class="form-control" accept=".pdf" id="decree_input">
                                    <?php if (!empty($data['profile']['decree_file']) && file_exists('uploads/assistants/' . $data['assistant']['id'] . '/' . $data['profile']['decree_file'])): ?>
                                        <div class="mt-2">
                                            <a href="<?php echo BASE_URL . 'uploads/assistants/' . $data['assistant']['id'] . '/' . $data['profile']['decree_file']; ?>" 
                                               target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i> مشاهده حکم
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile('decree_file')">
                                                <i class="bi bi-trash"></i> حذف
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                    <div class="error-message" id="decree_error"></div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">فرم خلاصه سوابق (PDF)</label>
                                    <input type="file" name="resume_file" class="form-control" accept=".pdf" id="resume_input">
                                    <?php if (!empty($data['profile']['resume_file']) && file_exists('uploads/assistants/' . $data['assistant']['id'] . '/' . $data['profile']['resume_file'])): ?>
                                        <div class="mt-2">
                                            <a href="<?php echo BASE_URL . 'uploads/assistants/' . $data['assistant']['id'] . '/' . $data['profile']['resume_file']; ?>" 
                                               target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i> مشاهده سوابق
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile('resume_file')">
                                                <i class="bi bi-trash"></i> حذف
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                    <div class="retired-checkbox">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="is_retired_checkbox" id="is_retired_checkbox" 
                                                   <?php echo ($data['profile']['is_retired'] ?? 0) ? 'checked' : ''; ?>
                                                   onchange="toggleResumeRequired()">
                                            <label class="form-check-label" for="is_retired_checkbox">
                                                <i class="bi bi-person-check text-warning"></i> بازنشسته هستم
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>
                            
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="terms_accepted" id="terms_accepted" 
                                           <?php echo ($data['profile']['terms_accepted'] ?? 0) ? 'checked' : ''; ?> required>
                                    <label class="form-check-label" for="terms_accepted">
                                        <i class="bi bi-shield-check"></i> با کلیه شرایط و قوانین هنرستان امام صادق موافقم و متعهد به رعایت آنها هستم.
                                    </label>
                                </div>
                            </div>
                            
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                    <i class="bi bi-check-circle"></i> تکمیل پروفایل و ورود به پنل معاون
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- اضافه کردن jQuery و کتابخانه‌های مورد نیاز -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/persian-date@1.1.0/dist/persian-date.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/js/persian-datepicker.min.js"></script>
    
    <script>
    // تبدیل اعداد فارسی به انگلیسی
    function convertToEnglishNumbers(text) {
        if (!text) return '';
        const persianNumbers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        const arabicNumbers = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        const englishNumbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        
        let result = text.toString();
        for (let i = 0; i < 10; i++) {
            result = result.replace(new RegExp(persianNumbers[i], 'g'), englishNumbers[i]);
            result = result.replace(new RegExp(arabicNumbers[i], 'g'), englishNumbers[i]);
        }
        return result;
    }

    // تبدیل اعداد انگلیسی به فارسی
    function convertToPersianNumbers(text) {
        if (!text) return '';
        const englishNumbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        const persianNumbers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        
        let result = text.toString();
        for (let i = 0; i < 10; i++) {
            result = result.replace(new RegExp(englishNumbers[i], 'g'), persianNumbers[i]);
        }
        return result;
    }

    // تبدیل تاریخ شمسی به میلادی
    function jalaliToGregorian(jalaliDate) {
        if (!jalaliDate) return '';
        
        try {
            const parts = jalaliDate.split('/');
            if (parts.length !== 3) return '';
            
            const year = parseInt(convertToEnglishNumbers(parts[0]));
            const month = parseInt(convertToEnglishNumbers(parts[1]));
            const day = parseInt(convertToEnglishNumbers(parts[2]));
            
            if (isNaN(year) || isNaN(month) || isNaN(day)) return '';
            
            // استفاده از کتابخانه PersianDate
            const pDate = new persianDate([year, month, day]);
            const gDate = pDate.toCalendar('gregorian');
            
            const gYear = gDate.year();
            const gMonth = String(gDate.month()).padStart(2, '0');
            const gDay = String(gDate.date()).padStart(2, '0');
            
            return `${gYear}-${gMonth}-${gDay}`;
        } catch (e) {
            console.error('Error converting date:', e);
            return '';
        }
    }

    // فعال‌سازی datepicker برای فیلد تاریخ
    $(document).ready(function() {
        $('#birth_date_jalali').persianDatepicker({
            format: 'YYYY/MM/DD',
            autoClose: true,
            initialValue: false,
            observer: true,
            calendar: {
                persian: {
                    locale: 'fa',
                    showHint: true
                }
            },
            onSelect: function(unixDate) {
                try {
                    const selectedDate = new persianDate(unixDate);
                    const jalaliDate = selectedDate.format('YYYY/MM/DD');
                    const gregorianDate = jalaliToGregorian(jalaliDate);
                    
                    console.log('Jalali Date:', jalaliDate);
                    console.log('Gregorian Date:', gregorianDate);
                    
                    if (gregorianDate && !gregorianDate.includes('NaN')) {
                        $('#birth_date').val(gregorianDate);
                        $('#birth_date_jalali').val(convertToPersianNumbers(jalaliDate));
                        $('#birth_date_error').hide();
                    } else {
                        alert('تاریخ انتخاب شده معتبر نیست. لطفاً مجدداً انتخاب کنید.');
                    }
                    
                } catch (error) {
                    console.error('Date conversion error:', error);
                    alert('خطا در تبدیل تاریخ. لطفاً تاریخ را مجدداً انتخاب کنید.');
                }
            }
        });
        
        // تنظیم اولیه وضعیت فایل سوابق
        toggleResumeRequired();
    });

    // پیش‌نمایش عکس
    function previewImage(input, previewId, placeholderId) {
        const file = input.files[0];
        if (!file) return;

        // بررسی حجم فایل (حداکثر 2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('حجم فایل نباید بیشتر از 2 مگابایت باشد');
            input.value = '';
            return;
        }

        const preview = document.getElementById(previewId);
        const placeholder = document.getElementById(placeholderId);
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            if (placeholder) {
                placeholder.style.display = 'none';
            }
        }
        
        reader.readAsDataURL(file);
    }

    // نمایش عکس در مودال
    function showImageModal(imageSrc) {
        document.getElementById('modalImage').src = imageSrc;
        const modal = new bootstrap.Modal(document.getElementById('imageModal'));
        modal.show();
    }

    // تغییر وضعیت تیک بازنشستگی
    function toggleResumeRequired() {
        const isRetiredCheckbox = document.getElementById('is_retired_checkbox');
        const isRetiredHidden = document.getElementById('is_retired_hidden');
        const resumeInput = document.getElementById('resume_input');
        
        if (isRetiredCheckbox.checked) {
            resumeInput.required = false;
            resumeInput.removeAttribute('required');
            isRetiredHidden.value = "1";
        } else {
            resumeInput.required = true;
            resumeInput.setAttribute('required', 'required');
            isRetiredHidden.value = "0";
        }
        
        console.log('Is Retired Checkbox:', isRetiredCheckbox.checked);
        console.log('Is Retired Hidden:', isRetiredHidden.value);
    }

    // اعتبارسنجی شماره شبا
    function validateSheba(sheba) {
        if (!sheba.startsWith('IR')) {
            sheba = 'IR' + sheba;
        }
        
        const shebaPattern = /^IR[0-9]{24}$/;
        return shebaPattern.test(sheba);
    }

    // اعتبارسنجی روزهای حضور
    function validatePresenceDays() {
        const checkboxes = document.querySelectorAll('.presence-day:checked');
        return checkboxes.length > 0;
    }

    // اعتبارسنجی تاریخ تولد
    function validateBirthDate() {
        const gregorianDate = $('#birth_date').val();
        return gregorianDate && gregorianDate !== '0000-00-00' && !gregorianDate.includes('NaN');
    }

    // اعتبارسنجی فایل‌های اجباری
// حذف فایل
function removeFile(fieldName) {
    if (confirm('آیا از حذف این فایل اطمینان دارید؟')) {
        // ایجاد یک فیلد مخفی برای نشان‌گذاری حذف فایل
        let hiddenInput = document.getElementById('remove_' + fieldName);
        if (!hiddenInput) {
            hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'remove_' + fieldName;
            hiddenInput.id = 'remove_' + fieldName;
            hiddenInput.value = '1';
            document.getElementById('profileForm').appendChild(hiddenInput);
        }
        
        // مخفی کردن فایل نمایش داده شده
        const fileElement = document.getElementById(fieldName + '_preview');
        const placeholder = document.getElementById(fieldName + '_placeholder');
        const fileInput = document.getElementById(fieldName + '_input');
        
        if (fileElement) fileElement.style.display = 'none';
        if (placeholder) placeholder.style.display = 'flex';
        if (fileInput) fileInput.value = '';
        
        // برای فایل‌های PDF، مخفی کردن لینک مشاهده
        const viewLink = document.querySelector('a[href*="' + fieldName + '"]');
        if (viewLink) {
            viewLink.parentElement.style.display = 'none';
        }
    }
}

// پیش‌نمایش عکس - نسخه بهبود یافته
function previewImage(input, previewId, placeholderId) {
    const file = input.files[0];
    if (!file) return;

    // بررسی حجم فایل (حداکثر 2MB)
    if (file.size > 2 * 1024 * 1024) {
        alert('حجم فایل نباید بیشتر از 2 مگابایت باشد');
        input.value = '';
        return;
    }

    const preview = document.getElementById(previewId);
    const placeholder = document.getElementById(placeholderId);
    const reader = new FileReader();
    
    reader.onload = function(e) {
        preview.src = e.target.result;
        preview.style.display = 'block';
        if (placeholder) {
            placeholder.style.display = 'none';
        }
        
        // حذف علامت حذف فایل اگر وجود دارد
        const fieldName = input.name;
        const removeInput = document.getElementById('remove_' + fieldName);
        if (removeInput) {
            removeInput.remove();
        }
    }
    
    reader.readAsDataURL(file);
}

// اعتبارسنجی فایل‌های اجباری - نسخه بهبود یافته
function validateRequiredFiles() {
    let isValid = true;

    // عکس پروفایل
    const profileImage = document.getElementById('profile_image');
    const hasProfileImage = profileImage.files[0] || <?php echo !empty($data['profile']['profile_image']) ? 'true' : 'false'; ?>;
    
    if (!hasProfileImage) {
        document.getElementById('profile_image_error').textContent = 'عکس پروفایل الزامی است';
        document.getElementById('profile_image_error').style.display = 'block';
        isValid = false;
    } else {
        document.getElementById('profile_image_error').style.display = 'none';
    }

    // کارت ملی
    const nationalCard = document.getElementById('national_card_input');
    const hasNationalCard = nationalCard.files[0] || <?php echo !empty($data['profile']['national_card_image']) ? 'true' : 'false'; ?>;
    
    if (!hasNationalCard) {
        document.getElementById('national_card_error').textContent = 'تصویر کارت ملی الزامی است';
        document.getElementById('national_card_error').style.display = 'block';
        isValid = false;
    } else {
        document.getElementById('national_card_error').style.display = 'none';
    }

    // شناسنامه
    const birthCertificate = document.getElementById('birth_certificate_input');
    const hasBirthCertificate = birthCertificate.files[0] || <?php echo !empty($data['profile']['birth_certificate_image']) ? 'true' : 'false'; ?>;
    
    if (!hasBirthCertificate) {
        document.getElementById('birth_certificate_error').textContent = 'تصویر شناسنامه الزامی است';
        document.getElementById('birth_certificate_error').style.display = 'block';
        isValid = false;
    } else {
        document.getElementById('birth_certificate_error').style.display = 'none';
    }

    // حکم کارگزینی
    const decreeFile = document.getElementById('decree_input');
    const hasDecreeFile = decreeFile.files[0] || <?php echo !empty($data['profile']['decree_file']) ? 'true' : 'false'; ?>;
    
    if (!hasDecreeFile) {
        document.getElementById('decree_error').textContent = 'فایل حکم کارگزینی الزامی است';
        document.getElementById('decree_error').style.display = 'block';
        isValid = false;
    } else {
        document.getElementById('decree_error').style.display = 'none';
    }

    // سوابق (فقط اگر بازنشسته نیست)
    const isRetired = document.getElementById('is_retired_checkbox').checked;
    const resumeFile = document.getElementById('resume_input');
    const hasResumeFile = resumeFile.files[0] || <?php echo !empty($data['profile']['resume_file']) ? 'true' : 'false'; ?>;
    
    if (!isRetired && !hasResumeFile) {
        alert('فایل خلاصه سوابق برای پرسنل فعال الزامی است');
        isValid = false;
    }

    return isValid;
}

    // اعتبارسنجی کلی فرم
    function validateForm() {
        let isValid = true;

        // اعتبارسنجی شماره شبا
        const shebaInput = document.querySelector('input[name="sheba_number"]');
        let shebaValue = convertToEnglishNumbers(shebaInput.value);
        
        if (!shebaValue.startsWith('IR')) {
            shebaValue = 'IR' + shebaValue;
        }
        
        const shebaError = document.getElementById('sheba_error');
        if (!validateSheba(shebaValue)) {
            shebaError.textContent = 'لطفاً شماره شبا را به صورت صحیح وارد کنید (24 رقم)';
            shebaError.style.display = 'block';
            isValid = false;
        } else {
            shebaError.style.display = 'none';
        }

        // اعتبارسنجی روزهای حضور
        const presenceDaysError = document.getElementById('presence_days_error');
        if (!validatePresenceDays()) {
            presenceDaysError.style.display = 'block';
            isValid = false;
        } else {
            presenceDaysError.style.display = 'none';
        }

        // اعتبارسنجی تاریخ تولد
        const birthDateError = document.getElementById('birth_date_error');
        if (!validateBirthDate()) {
            birthDateError.textContent = 'لطفاً تاریخ تولد را انتخاب کنید';
            birthDateError.style.display = 'block';
            isValid = false;
        } else {
            birthDateError.style.display = 'none';
        }

        // اعتبارسنجی فایل‌های اجباری
        if (!validateRequiredFiles()) {
            isValid = false;
        }

        if (isValid) {
            // تبدیل تمام اعداد فارسی به انگلیسی قبل از ارسال
            convertAllNumbersToEnglish();
            
            // دیباگ نهایی
            console.log('=== FINAL FORM DATA ===');
            console.log('Birth Date:', $('#birth_date').val());
            console.log('Is Retired:', document.getElementById('is_retired_hidden').value);
            
            showLoading();
            return true;
        } else {
            const firstError = document.querySelector('.error-message[style="display: block;"]');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
            return false;
        }
    }

    // تبدیل تمام اعداد فارسی به انگلیسی قبل از ارسال
    function convertAllNumbersToEnglish() {
        const inputs = document.querySelectorAll('.english-number');
        inputs.forEach(input => {
            input.value = convertToEnglishNumbers(input.value);
        });
        
        const jalaliDateInput = document.getElementById('birth_date_jalali');
        jalaliDateInput.value = convertToEnglishNumbers(jalaliDateInput.value);
    }

    // نمایش Loading هنگام ارسال فرم
    function showLoading() {
        document.getElementById('loadingOverlay').style.display = 'flex';
        document.getElementById('submitBtn').disabled = true;
    }

    // کلیک روی placeholders
    document.addEventListener('DOMContentLoaded', function() {
        const profilePlaceholder = document.getElementById('profile_placeholder');
        if (profilePlaceholder) {
            profilePlaceholder.addEventListener('click', function() {
                document.getElementById('profile_image').click();
            });
        }
    });

    // اعتبارسنجی لحظه‌ای روزهای حضور
    document.querySelectorAll('.presence-day').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const presenceDaysError = document.getElementById('presence_days_error');
            if (validatePresenceDays()) {
                presenceDaysError.style.display = 'none';
            }
        });
    });

    // تبدیل اعداد به فارسی هنگام لود صفحه
    $(document).ready(function() {
        $('.english-number').each(function() {
            $(this).val(convertToPersianNumbers($(this).val()));
        });
        
        const jalaliDateInput = $('#birth_date_jalali');
        if (jalaliDateInput.val()) {
            jalaliDateInput.val(convertToPersianNumbers(jalaliDateInput.val()));
        }
    });

    // محدودیت ورود فقط عدد برای فیلدهای عددی
    $('.english-number').on('input', function() {
        this.value = this.value.replace(/[^0-9۰-۹]/g, '');
    });

    </script>
    </body>
    </html>