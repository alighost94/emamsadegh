<?php
// سایدبار حرفه‌ای برای پنل مدیریت
// داده‌های مورد نیاز: $user_name, $role
?>
<style>
body {
    margin: 0;
    font-family: "IRANSans", Tahoma, sans-serif;
}

.layout {
    display: flex;
    min-height: 100vh;
}

/* --- Sidebar --- */
.sidebar {
    width: 250px;
    background: #2c3e50;
    color: white;
    display: flex;
    flex-direction: column;
    position: sticky;
    top: 0;
    height: 100vh;
    box-shadow: -2px 0 8px rgba(0,0,0,0.3);
}

.sidebar-header {
    text-align: center;
    padding: 20px;
    border-bottom: 1px solid rgba(255,255,255,0.2);
}

.sidebar-header h5 {
    margin: 0;
    font-size: 18px;
}

.sidebar-header small {
    color: #bdc3c7;
}

.sidebar-user {
    margin-top: 10px;
    font-size: 14px;
}

.sidebar-user i {
    margin-left: 5px;
}

/* --- Sidebar Nav --- */
.sidebar ul {
    list-style: none;
    padding: 0;
    margin: 0;
    flex-grow: 1;
}

.sidebar ul li {
    border-bottom: 1px solid rgba(255,255,255,0.05);
}

.sidebar ul li a {
    display: block;
    color: white;
    text-decoration: none;
    padding: 12px 20px;
    transition: background 0.2s ease;
}

.sidebar ul li a:hover {
    background: #34495e;
}

.sidebar ul li a.active {
    background: #1abc9c;
    font-weight: bold;
}

/* --- Main Content --- */
.main-content {
    flex-grow: 1;
    background: #f8f9fa;
    padding: 20px;
    overflow-x: hidden;
}

/* --- Responsive --- */
@media (max-width: 768px) {
    .layout {
        flex-direction: column;
    }
    .sidebar {
        width: 100%;
        height: auto;
        position: relative;
    }
    .main-content {
        margin: 0;
        width: 100%;
    }
}
</style>

<div class="layout">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h5>هنرستان امام صادق</h5>
            <small>سیستم مدیریت</small>
            <div class="sidebar-user">
                <i class="bi bi-person-circle"></i>
                <?php echo isset($user_name) ? $user_name : 'مدیر سیستم'; ?>
            </div>
        </div>
        <ul>
            <li><a class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>dashboard"><i class="bi bi-speedometer2"></i> داشبورد</a></li>
            <li><a class="<?php echo ($_GET['url'] ?? '') == 'admin/users' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>admin/users"><i class="bi bi-people"></i> مدیریت کاربران</a></li>
            <li><a class="<?php echo ($_GET['url'] ?? '') == 'admin/students' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>admin/students"><i class="bi bi-person-check"></i> دانش‌آموزان</a></li>
            <li><a class="<?php echo ($_GET['url'] ?? '') == 'admin/teachers' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>admin/teachers"><i class="bi bi-person-badge"></i> معلمان</a></li>
            <li><a class="<?php echo ($_GET['url'] ?? '') == 'admin/staffFiles' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>admin/staffFiles"><i class="bi bi-folder"></i> پرونده همکاران</a></li>
            <li><a class="<?php echo ($_GET['url'] ?? '') == 'admin/assignTeacherCourse' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>admin/assignTeacherCourse"><i class="bi bi-journal-plus"></i> تخصیص دروس</a></li>
            <li><a class="<?php echo ($_GET['url'] ?? '') == 'admin/majors' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>admin/majors"><i class="bi bi-book"></i> مدیریت رشته‌ها</a></li>
            <li><a class="<?php echo ($_GET['url'] ?? '') == 'admin/courses' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>admin/courses"><i class="bi bi-journal-text"></i> مدیریت دروس</a></li>
            <li><a class="<?php echo ($_GET['url'] ?? '') == 'admin/classes' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>admin/classes"><i class="bi bi-house-door"></i> مدیریت کلاس‌ها</a></li>
            <li><a href="<?php echo BASE_URL; ?>auth/logout"><i class="bi bi-box-arrow-left"></i> خروج</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- محتوای اصلی اینجا میاد -->
