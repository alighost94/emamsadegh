<?php
// این فایل برای استفاده در تمام View های معاون
$assistant = $data['assistant'];
$profile = $data['profile'] ?? [];
$user_name = $data['user_name'];
?>
<div class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
    <div class="position-sticky pt-3">
        <div class="p-4 text-center text-white border-bottom">
            <h5>هنرستان امام صادق</h5>
            <small>پنل معاون</small>
            <div class="mt-3">
                <?php if (!empty($profile['profile_image'])): ?>
                    <img src="uploads/assistants/<?php echo $assistant['id']; ?>/<?php echo $profile['profile_image']; ?>" 
                         class="profile-image" alt="پروفایل" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid #667eea;">
                <?php else: ?>
                    <img src="https://via.placeholder.com/120/667eea/ffffff?text=<?php echo substr($user_name, 0, 1); ?>" 
                         class="profile-image" alt="پروفایل" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid #667eea;">
                <?php endif; ?>
            </div>
            <div class="mt-2 small">
                <i class="bi bi-person-circle"></i>
                <?php echo $user_name; ?>
            </div>
            <div class="mt-1 small text-muted">
                معاون پایه <?php echo $assistant['grade_name']; ?>
            </div>
            <?php if (!empty($profile['personnel_code'])): ?>
                <div class="mt-1 small text-muted">
                    کد پرسنلی: <?php echo $profile['personnel_code']; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" 
                   href="<?php echo BASE_URL; ?>assistant">
                    <i class="bi bi-speedometer2"></i> داشبورد
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>" 
                   href="<?php echo BASE_URL; ?>assistant/profile">
                    <i class="bi bi-person"></i> پروفایل من
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'students.php' ? 'active' : ''; ?>" 
                   href="<?php echo BASE_URL; ?>assistant/students">
                    <i class="bi bi-people"></i> مدیریت دانش‌آموزان
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'disciplinary.php' ? 'active' : ''; ?>" 
                   href="<?php echo BASE_URL; ?>assistant/disciplinary">
                    <i class="bi bi-shield-exclamation"></i> پرونده انضباطی
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'attendance.php' ? 'active' : ''; ?>" 
                   href="<?php echo BASE_URL; ?>assistant/attendance">
                    <i class="bi bi-clipboard-check"></i> حضور و غیاب
                </a>
            </li>

<!-- اضافه کردن این آیتم به منو -->
<li class="nav-item">
    <a class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'teacher_records.php' ? 'active' : ''; ?>" 
       href="<?php echo BASE_URL; ?>assistant/teacherRecords">
        <i class="bi bi-people"></i> پرونده معلمان
    </a>
</li>



            <li class="nav-item">
                <a class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'grades.php' ? 'active' : ''; ?>" 
                   href="<?php echo BASE_URL; ?>assistant/grades">
                    <i class="bi bi-journal-text"></i> کارنامه آموزشی
                </a>
            </li>
            <li class="nav-item">
    <a class="nav-link" href="<?= BASE_URL ?>assistant/addStudent">
        <i class="fas fa-user-plus"></i> افزودن دانش‌آموز
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