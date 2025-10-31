<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>وضعیت انضباطی - پنل اولیا</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { font-family: 'Vazir', sans-serif; }
        .sidebar { background: #2c3e50; color: white; height: 100vh; position: fixed; width: 250px; }
        .sidebar .nav-link { color: #bdc3c7; padding: 15px 20px; border-bottom: 1px solid #34495e; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: white; background: #34495e; }
        .main-content { margin-right: 250px; padding: 20px; }
        .score-excellent { color: #28a745; font-weight: bold; }
        .score-good { color: #20c997; font-weight: bold; }
        .score-average { color: #ffc107; font-weight: bold; }
        .score-poor { color: #fd7e14; font-weight: bold; }
        .score-very-poor { color: #dc3545; font-weight: bold; }
        .violation-card { border-right: 4px solid #dc3545; margin-bottom: 15px; }
        .deduction-badge { font-size: 0.8rem; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- سایدبار -->
            <?php include 'app/views/parent/partials/sidebar.php'; ?>
            
            <!-- محتوای اصلی -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">وضعیت انضباطی دانش‌آموز</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <span class="badge bg-primary">
                            <?php echo $data['student_info']['first_name'] . ' ' . $data['student_info']['last_name']; ?>
                        </span>
                    </div>
                </div>

                <!-- کارت نمره انضباطی -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0"><i class="bi bi-shield-check"></i> کارت انضباطی</h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-md-4">
                                        <?php
                                        $current_score = $data['disciplinary_score']['current_score'] ?? 20;
                                        $total_deductions = $data['disciplinary_score']['total_deductions'] ?? 0;
                                        
                                        $score_class = 'score-excellent';
                                        if ($current_score < 20) $score_class = 'score-good';
                                        if ($current_score < 18) $score_class = 'score-average';
                                        if ($current_score < 16) $score_class = 'score-poor';
                                        if ($current_score < 14) $score_class = 'score-very-poor';
                                        ?>
                                        <h1 class="<?php echo $score_class; ?>"><?php echo $current_score; ?></h1>
                                        <small>نمره فعلی انضباطی</small>
                                    </div>
                                    <div class="col-md-4">
                                        <h2 class="text-danger"><?php echo $total_deductions; ?></h2>
                                        <small>مجموع کسر نمره</small>
                                    </div>
                                    <div class="col-md-4">
                                        <h2 class="text-success">۲۰</h2>
                                        <small>نمره پایه</small>
                                    </div>
                                </div>
                                
                                <!-- نوار پیشرفت -->
                                <div class="progress mt-3" style="height: 20px;">
                                    <?php
                                    $progress_percentage = ($current_score / 20) * 100;
                                    $progress_class = 'bg-success';
                                    if ($progress_percentage < 90) $progress_class = 'bg-info';
                                    if ($progress_percentage < 80) $progress_class = 'bg-warning';
                                    if ($progress_percentage < 70) $progress_class = 'bg-danger';
                                    ?>
                                    <div class="progress-bar <?php echo $progress_class; ?>" 
                                         role="progressbar" 
                                         style="width: <?php echo $progress_percentage; ?>%"
                                         aria-valuenow="<?php echo $current_score; ?>" 
                                         aria-valuemin="0" 
                                         aria-valuemax="20">
                                        <?php echo $current_score; ?> از ۲۰
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="bi bi-info-circle"></i> راهنمای نمره انضباطی</h6>
                            </div>
                            <div class="card-body">
                                <div class="small">
                                    <div class="mb-2">
                                        <span class="score-excellent">۲۰ - ۱۸: عالی</span>
                                    </div>
                                    <div class="mb-2">
                                        <span class="score-good">۱۷.۹ - ۱۶: خوب</span>
                                    </div>
                                    <div class="mb-2">
                                        <span class="score-average">۱۵.۹ - ۱۴: متوسط</span>
                                    </div>
                                    <div class="mb-2">
                                        <span class="score-poor">۱۳.۹ - ۱۲: نیاز به توجه</span>
                                    </div>
                                    <div class="mb-0">
                                        <span class="score-very-poor">زیر ۱۲: نیاز به پیگیری فوری</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- لیست تخلفات -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">تخلفات ثبت شده</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($data['disciplinary_records'])): ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>تاریخ</th>
                                            <th>نوع تخلف</th>
                                            <th>شرح</th>
                                            <th>کسر نمره</th>
                                            <th>ثبت کننده</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data['disciplinary_records'] as $record): ?>
                                            <tr>
                                                <td>
                                                    <small><?php echo $record['jalali_date']; ?></small>
                                                </td>
                                                <td>
                                                    <strong><?php echo $record['violation_type']; ?></strong>
                                                </td>
                                                <td>
                                                    <small><?php echo $record['description']; ?></small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-danger deduction-badge">
                                                        -<?php echo $record['point_deduction']; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <small><?php echo $record['assistant_first_name'] . ' ' . $record['assistant_last_name']; ?></small>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-success text-center">
                                <i class="bi bi-check-circle"></i>
                                هیچ تخلف انضباطی برای دانش‌آموز ثبت نشده است.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- توصیه‌ها -->
                <div class="card mt-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="bi bi-lightbulb"></i> توصیه‌های آموزشی</h6>
                    </div>
                    <div class="card-body">
                        <?php
                        $current_score = $data['disciplinary_score']['current_score'] ?? 20;
                        $recommendations = [];
                        
                        if ($current_score < 16) {
                            $recommendations[] = "با معاون آموزشی پایه تماس بگیرید تا در مورد وضعیت انضباطی دانش‌آموز گفتگو کنید.";
                            $recommendations[] = "با دانش‌آموز در مورد اهمیت رعایت قوانین مدرسه صحبت کنید.";
                        }
                        
                        if ($current_score < 18) {
                            $recommendations[] = "بررسی کنید که آیا دانش‌آموز با مشکلات خاصی در مدرسه روبرو است.";
                            $recommendations[] = "با معلمان در مورد رفتار دانش‌آموز در کلاس‌های درس مشورت کنید.";
                        }
                        
                        if (empty($recommendations)) {
                            $recommendations[] = "وضعیت انضباطی دانش‌آموز مطلوب است. همچنان بر رفتارهای مثبت تأکید کنید.";
                        }
                        ?>
                        
                        <ul class="list-unstyled">
                            <?php foreach ($recommendations as $recommendation): ?>
                                <li class="mb-2">
                                    <i class="bi bi-check text-success"></i>
                                    <?php echo $recommendation; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        
                        <div class="alert alert-info mt-3">
                            <i class="bi bi-telephone"></i>
                            <strong>تماس با مدرسه:</strong> در صورت نیاز به مشاوره بیشتر، با دفتر معاونت آموزشی تماس بگیرید.
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>