<?php include 'app/views/assistant/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php include 'app/views/assistant/sidebar.php'; ?>
        
        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-user-plus"></i>
                    افزودن دانش‌آموز جدید
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="<?= BASE_URL ?>assistant/students" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-list"></i> مشاهده لیست دانش‌آموزان
                    </a>
                </div>
            </div>

            <!-- Alerts -->
            <?php if (isset($success)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i>
                    <?= $success ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= $error ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-graduate"></i>
                        فرم ثبت دانش‌آموز و اولیا
                    </h5>
                </div>
                <div class="card-body">
                    <form method="post" class="row g-3">
                        <!-- اطلاعات دانش‌آموز -->
                        <div class="col-12">
                            <h5 class="text-primary border-bottom pb-2">
                                <i class="fas fa-user"></i>
                                اطلاعات دانش‌آموز
                            </h5>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">نام دانش‌آموز <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" 
                                   value="<?= $_POST['first_name'] ?? '' ?>" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">نام خانوادگی دانش‌آموز <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control" 
                                   value="<?= $_POST['last_name'] ?? '' ?>" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">شماره موبایل <span class="text-danger">*</span></label>
                            <input type="text" name="mobile" class="form-control" 
                                   value="<?= $_POST['mobile'] ?? '' ?>" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">کد ملی <span class="text-danger">*</span></label>
                            <input type="text" name="national_code" class="form-control" 
                                   value="<?= $_POST['national_code'] ?? '' ?>" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">تاریخ تولد</label>
                            <input type="date" name="birth_date" class="form-control" 
                                   value="<?= $_POST['birth_date'] ?? '' ?>">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">نام پدر</label>
                            <input type="text" name="father_name" class="form-control" 
                                   value="<?= $_POST['father_name'] ?? '' ?>">
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">آدرس</label>
                            <textarea name="address" class="form-control" rows="2"><?= $_POST['address'] ?? '' ?></textarea>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">انتخاب کلاس <span class="text-danger">*</span></label>
                            <select name="class_id" class="form-select" required>
                                <option value="">-- انتخاب کلاس --</option>
                                <?php foreach ($classes as $class): ?>
                                    <option value="<?= $class['id'] ?>" 
                                        <?= (isset($_POST['class_id']) && $_POST['class_id'] == $class['id']) ? 'selected' : '' ?>>
                                        <?= $class['name'] ?> - <?= $class['major_name'] ?>
                                        (ظرفیت: <?= $class['student_count'] ?>/<?= $class['capacity'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted">
                                فقط کلاس‌های پایه <?= $assistant['grade_name'] ?> نمایش داده می‌شوند
                            </small>
                        </div>

                        <!-- اطلاعات اولیا -->
                        <div class="col-12 mt-4">
                            <h5 class="text-info border-bottom pb-2">
                                <i class="fas fa-users"></i>
                                اطلاعات اولیا (اختیاری)
                            </h5>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">نسبت</label>
                            <select name="relation_type" class="form-select">
                                <option value="father" <?= (isset($_POST['relation_type']) && $_POST['relation_type'] == 'father') ? 'selected' : '' ?>>پدر</option>
                                <option value="mother" <?= (isset($_POST['relation_type']) && $_POST['relation_type'] == 'mother') ? 'selected' : '' ?>>مادر</option>
                                <option value="guardian" <?= (isset($_POST['relation_type']) && $_POST['relation_type'] == 'guardian') ? 'selected' : '' ?>>سرپرست</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">نام ولی</label>
                            <input type="text" name="parent_first_name" class="form-control" 
                                   value="<?= $_POST['parent_first_name'] ?? '' ?>">
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">نام خانوادگی ولی</label>
                            <input type="text" name="parent_last_name" class="form-control" 
                                   value="<?= $_POST['parent_last_name'] ?? '' ?>">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">شماره موبایل ولی</label>
                            <input type="text" name="parent_mobile" class="form-control" 
                                   value="<?= $_POST['parent_mobile'] ?? '' ?>">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">کد ملی ولی</label>
                            <input type="text" name="parent_national_code" class="form-control" 
                                   value="<?= $_POST['parent_national_code'] ?? '' ?>">
                        </div>

                        <!-- Buttons -->
                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> ثبت دانش‌آموز و اولیا
                            </button>
                            <a href="<?= BASE_URL ?>assistant" class="btn btn-secondary">
                                <i class="fas fa-arrow-right"></i> بازگشت به داشبورد
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include 'app/views/assistant/footer.php'; ?>