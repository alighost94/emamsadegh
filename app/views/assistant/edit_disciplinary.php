<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ویرایش تخلف انضباطی - پنل معاون</title>
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
        .violation-card { border-right: 4px solid #dc3545; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'app/views/assistant/partials/sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">
                        <i class="bi bi-pencil-square text-warning"></i>
                        ویرایش تخلف انضباطی
                    </h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="<?php echo BASE_URL; ?>assistant/disciplinary?student_id=<?php echo $data['record']['student_id']; ?>" 
                           class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-right"></i> بازگشت
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

                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="bi bi-exclamation-triangle"></i>
                            ویرایش تخلف انضباطی - 
                            <?php echo $data['record']['first_name'] . ' ' . $data['record']['last_name']; ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="editViolationForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">دانش‌آموز</label>
                                        <input type="text" class="form-control" 
                                               value="<?php echo $data['record']['first_name'] . ' ' . $data['record']['last_name'] . ' - ' . $data['record']['class_name']; ?>" 
                                               readonly>
                                        <small class="text-muted">شماره دانش‌آموزی: <?php echo $data['record']['student_number']; ?></small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">تاریخ تخلف</label>
                                        <input type="text" name="violation_date" class="form-control" id="violationDate" 
                                               value="<?php echo $data['record']['jalali_date']; ?>" required>
                                        <input type="hidden" name="violation_date_gregorian" id="violationDateGregorian" 
                                               value="<?php echo $data['record']['violation_date']; ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">نوع تخلف</label>
                                        <select name="violation_type" class="form-select" required>
                                            <option value="">انتخاب نوع تخلف</option>
                                            <option value="تاخیر در ورود به هنرستان" <?php echo $data['record']['violation_type'] == 'تاخیر در ورود به هنرستان' ? 'selected' : ''; ?>>تاخیر در ورود به هنرستان</option>
                                            <option value="تاخیر در ورود به کلاس" <?php echo $data['record']['violation_type'] == 'تاخیر در ورود به کلاس' ? 'selected' : ''; ?>>تاخیر در ورود به کلاس</option>
                                            <option value="بی نظمی در کلاس" <?php echo $data['record']['violation_type'] == 'بی نظمی در کلاس' ? 'selected' : ''; ?>>بی نظمی در کلاس</option>
                                            <!-- سایر گزینه‌ها مانند قبل -->
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                <div class="mb-3">
    <label class="form-label">کسر نمره</label>
    <select name="point_deduction" class="form-select" required>
        <option value="0.25" <?php echo $data['record']['point_deduction'] == '0.25' ? 'selected' : ''; ?>>۰.۲۵ نمره (تخلف بسیار جزئی)</option>
        <option value="0.5" <?php echo $data['record']['point_deduction'] == '0.5' ? 'selected' : ''; ?>>۰.۵ نمره (تخلف جزئی)</option>
        <option value="0.75" <?php echo $data['record']['point_deduction'] == '0.75' ? 'selected' : ''; ?>>۰.۷۵ نمره (تخلف جزئی-متوسط)</option>
        <option value="1" <?php echo $data['record']['point_deduction'] == '1' ? 'selected' : ''; ?>>۱ نمره (تخلف متوسط)</option>
        <option value="1.25" <?php echo $data['record']['point_deduction'] == '1.25' ? 'selected' : ''; ?>>۱.۲۵ نمره (تخلف متوسط-شدید)</option>
        <option value="1.5" <?php echo $data['record']['point_deduction'] == '1.5' ? 'selected' : ''; ?>>۱.۵ نمره (تخلف نسبتاً شدید)</option>
        <option value="1.75" <?php echo $data['record']['point_deduction'] == '1.75' ? 'selected' : ''; ?>>۱.۷۵ نمره (تخلف شدید)</option>
        <option value="2" <?php echo $data['record']['point_deduction'] == '2' ? 'selected' : ''; ?>>۲ نمره (تخلف بسیار شدید)</option>
        <option value="2.25" <?php echo $data['record']['point_deduction'] == '2.25' ? 'selected' : ''; ?>>۲.۲۵ نمره (تخلف بحرانی)</option>
        <option value="2.5" <?php echo $data['record']['point_deduction'] == '2.5' ? 'selected' : ''; ?>>۲.۵ نمره (تخلف بسیار بحرانی)</option>
        <option value="2.75" <?php echo $data['record']['point_deduction'] == '2.75' ? 'selected' : ''; ?>>۲.۷۵ نمره (تخلف فوق‌العاده شدید)</option>
        <option value="3" <?php echo $data['record']['point_deduction'] == '3' ? 'selected' : ''; ?>>۳ نمره (حداکثر کسر)</option>
    </select>
</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">شرح تخلف</label>
                                <textarea name="description" class="form-control" rows="4" required><?php echo $data['record']['description']; ?></textarea>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="<?php echo BASE_URL; ?>assistant/disciplinary?student_id=<?php echo $data['record']['student_id']; ?>" 
                                   class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> انصراف
                                </a>
                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-check-circle"></i> بروزرسانی تخلف
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/persian-date@1.1.0/dist/persian-date.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/js/persian-datepicker.min.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // فعال کردن تقویم شمسی
        $('#violationDate').persianDatepicker({
            format: 'YYYY/MM/DD',
            autoClose: true,
            initialValue: false,
            initialValueType: 'persian',
            position: 'auto',
            observer: true,
            calendar: {
                persian: {
                    locale: 'fa',
                    showHint: true
                }
            }
        });
    });
    </script>
</body>
</html>