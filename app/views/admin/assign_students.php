<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تخصیص دانش‌آموزان - هنرستان امام صادق</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { font-family: 'Vazir', sans-serif; }
        .student-card { transition: all 0.3s; }
        .student-card:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'app/views/partials/sidebar.php'; ?>
            
            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">تخصیص دانش‌آموزان به کلاس <?php echo $data['class']['name']; ?></h1>
                    <a href="<?php echo BASE_URL; ?>admin/classes" class="btn btn-secondary">بازگشت به لیست کلاس‌ها</a>
                </div>

                <?php if (isset($data['success'])): ?>
                    <div class="alert alert-success"><?php echo $data['success']; ?></div>
                <?php endif; ?>

                <?php if (isset($data['error'])): ?>
                    <div class="alert alert-danger"><?php echo $data['error']; ?></div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">دانش‌آموزان رشته <?php echo $data['class']['major_name']; ?></h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" id="assignForm">
                                    <div class="row">
                                        <?php if (!empty($data['students'])): ?>
                                            <?php foreach ($data['students'] as $student): ?>
                                                <div class="col-md-6 mb-3">
                                                    <div class="card student-card">
                                                        <div class="card-body">
                                                            <div class="form-check">
                                                                <input class="form-check-input student-checkbox" 
                                                                       type="checkbox" 
                                                                       name="student_ids[]" 
                                                                       value="<?php echo $student['id']; ?>"
                                                                       id="student_<?php echo $student['id']; ?>">
                                                                <label class="form-check-label w-100" for="student_<?php echo $student['id']; ?>">
                                                                    <strong><?php echo $student['first_name'] . ' ' . $student['last_name']; ?></strong>
                                                                    <br>
                                                                    <small class="text-muted">
                                                                        کد ملی: <?php echo $student['national_code']; ?>
                                                                    </small>
                                                                    <?php if (!empty($student['student_number'])): ?>
                                                                        <br>
                                                                        <small class="text-muted">
                                                                            شماره دانش‌آموزی: <?php echo $student['student_number']; ?>
                                                                        </small>
                                                                    <?php endif; ?>
                                                                    <?php if ($student['class_id']): ?>
                                                                        <br>
                                                                        <small class="text-warning">
                                                                            کلاس فعلی: <?php echo $student['current_class_name'] ?? 'نامشخص'; ?>
                                                                        </small>
                                                                    <?php endif; ?>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="col-12">
                                                <div class="alert alert-info">هیچ دانش‌آموزی برای این رشته یافت نشد</div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <?php if (!empty($data['students'])): ?>
                                        <div class="mt-3">
                                            <button type="submit" class="btn btn-success">
                                                <i class="bi bi-check-lg"></i>
                                                تخصیص دانش‌آموزان انتخاب شده
                                            </button>
                                            <button type="button" class="btn btn-outline-primary" id="selectAll">
                                                انتخاب همه
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary" id="deselectAll">
                                                لغو انتخاب همه
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">دانش‌آموزان فعلی کلاس</h5>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($data['current_students'])): ?>
                                    <div class="list-group">
                                        <?php foreach ($data['current_students'] as $student): ?>
                                            <div class="list-group-item">
                                                <strong><?php echo $student['first_name'] . ' ' . $student['last_name']; ?></strong>
                                                <br>
                                                <small class="text-muted">
                                                    شماره دانش‌آموزی: <?php echo $student['student_number']; ?>
                                                </small>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-warning">هنوز دانش‌آموزی به این کلاس تخصیص داده نشده است</div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0">اطلاعات کلاس</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>نام کلاس:</strong> <?php echo $data['class']['name']; ?></p>
                                <p><strong>رشته:</strong> <?php echo $data['class']['major_name']; ?></p>
                                <p><strong>پایه:</strong> <?php echo $data['class']['grade_name']; ?></p>
                                <p><strong>ظرفیت:</strong> <?php echo $data['class']['capacity']; ?></p>
                                <p><strong>تعداد دانش‌آموزان فعلی:</strong> <?php echo count($data['current_students']); ?></p>
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
            // انتخاب همه
            document.getElementById('selectAll').addEventListener('click', function() {
                document.querySelectorAll('.student-checkbox').forEach(checkbox => {
                    checkbox.checked = true;
                });
            });
            
            // لغو انتخاب همه
            document.getElementById('deselectAll').addEventListener('click', function() {
                document.querySelectorAll('.student-checkbox').forEach(checkbox => {
                    checkbox.checked = false;
                });
            });
            
            // بررسی ظرفیت قبل از ارسال فرم
            document.getElementById('assignForm').addEventListener('submit', function(e) {
                const selectedCount = document.querySelectorAll('.student-checkbox:checked').length;
                const currentCount = <?php echo count($data['current_students']); ?>;
                const capacity = <?php echo $data['class']['capacity']; ?>;
                
                if (currentCount + selectedCount > capacity) {
                    e.preventDefault();
                    alert(`ظرفیت کلاس تکمیل است! \nتعداد دانش‌آموزان فعلی: ${currentCount} \nظرفیت کلاس: ${capacity} \nتعداد انتخاب شده: ${selectedCount}`);
                }
            });
        });
    </script>
</body>
</html>