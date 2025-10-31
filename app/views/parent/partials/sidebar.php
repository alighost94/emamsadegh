<?php
$user_name = $data['user_name'];
?>
<div class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
    <div class="position-sticky pt-3">
        <div class="p-4 text-center text-white border-bottom">
            <h5>هنرستان امام صادق</h5>
            <small>پنل اولیا</small>
            <div class="mt-3">
                <img src="https://via.placeholder.com/80/667eea/ffffff?text=اولیا" 
                     class="rounded-circle" alt="پروفایل" style="width: 80px; height: 80px;">
            </div>
            <div class="mt-2 small">
                <i class="bi bi-person-circle"></i>
                <?php echo $user_name; ?>
            </div>
            <?php if (isset($data['student_info'])): ?>
                <div class="mt-1 small text-muted">
                    <i class="bi bi-person"></i>
                    <?php echo $data['student_info']['first_name'] . ' ' . $data['student_info']['last_name']; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" 
                   href="<?php echo BASE_URL; ?>parent">
                    <i class="bi bi-speedometer2"></i> داشبورد
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'attendance.php' ? 'active' : ''; ?>" 
                   href="<?php echo BASE_URL; ?>parent/attendance">
                    <i class="bi bi-clipboard-check"></i> حضور و غیاب
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'grades.php' ? 'active' : ''; ?>" 
                   href="<?php echo BASE_URL; ?>parent/grades">
                    <i class="bi bi-journal-text"></i> نمرات
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'disciplinary.php' ? 'active' : ''; ?>" 
                   href="<?php echo BASE_URL; ?>parent/disciplinary">
                    <i class="bi bi-shield-exclamation"></i> انضباط
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>" 
                   href="<?php echo BASE_URL; ?>parent/profile">
                    <i class="bi bi-person"></i> پروفایل
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