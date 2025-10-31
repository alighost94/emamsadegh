<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پرونده معلمان - پنل معاون</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { font-family: 'Vazir', sans-serif; }
        .sidebar { background: #2c3e50; color: white; height: 100vh; position: fixed; width: 250px; }
        .sidebar .nav-link { color: #bdc3c7; padding: 15px 20px; border-bottom: 1px solid #34495e; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: white; background: #34495e; }
        .main-content { margin-right: 250px; padding: 20px; }
        .teacher-card { transition: all 0.3s; }
        .teacher-card:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .record-card { border-right: 4px solid transparent; margin-bottom: 15px; }
        .encouragement { border-right-color: #28a745; background-color: #f8fff8; }
        .disciplinary { border-right-color: #dc3545; background-color: #fff8f8; }
        .score-excellent { color: #28a745; font-weight: bold; }
        .score-good { color: #20c997; font-weight: bold; }
        .score-average { color: #ffc107; font-weight: bold; }
        .score-poor { color: #fd7e14; font-weight: bold; }
        .score-very-poor { color: #dc3545; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- سایدبار -->
            <?php include 'app/views/assistant/partials/sidebar.php'; ?>
            
            <!-- محتوای اصلی -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">پرونده معلمان پایه <?php echo $data['assistant']['grade_name']; ?></h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <span class="badge bg-primary">
                            <?php echo count($data['staff_scores']); ?> معلم
                        </span>
                    </div>
                </div>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- آمار کلی -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-white bg-success">
                            <div class="card-body text-center">
                                <h4><?php echo count(array_filter($data['staff_scores'], function($s) { return $s['current_score'] >= 90; })); ?></h4>
                                <small>عالی</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-info">
                            <div class="card-body text-center">
                                <h4><?php echo count(array_filter($data['staff_scores'], function($s) { return $s['current_score'] >= 80 && $s['current_score'] < 90; })); ?></h4>
                                <small>خوب</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-warning">
                            <div class="card-body text-center">
                                <h4><?php echo count(array_filter($data['staff_scores'], function($s) { return $s['current_score'] >= 70 && $s['current_score'] < 80; })); ?></h4>
                                <small>متوسط</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-danger">
                            <div class="card-body text-center">
                                <h4><?php echo count(array_filter($data['staff_scores'], function($s) { return $s['current_score'] < 70; })); ?></h4>
                                <small>نیاز به توجه</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- لیست معلمان و امتیازات -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">امتیازات معلمان</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>معلم</th>
                                        <th>تخصص</th>
                                        <th>کد پرسنلی</th>
                                        <th>امتیاز فعلی</th>
                                        <th>تشویق‌ها</th>
                                        <th>انضباط‌ها</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data['staff_scores'] as $score): ?>
                                        <?php
                                        $score_class = 'score-excellent';
                                        if ($score['current_score'] < 90) $score_class = 'score-good';
                                        if ($score['current_score'] < 80) $score_class = 'score-average';
                                        if ($score['current_score'] < 70) $score_class = 'score-poor';
                                        if ($score['current_score'] < 60) $score_class = 'score-very-poor';
                                        ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo $score['first_name'] . ' ' . $score['last_name']; ?></strong>
                                                <br>
                                                <small class="text-muted"><?php echo $score['mobile']; ?></small>
                                            </td>
                                            <td><?php echo $score['expertise'] ?? 'ثبت نشده'; ?></td>
                                            <td><?php echo $score['personnel_code'] ?? 'ثبت نشده'; ?></td>
                                            <td>
                                                <span class="<?php echo $score_class; ?>">
                                                    <?php echo $score['current_score']; ?>
                                                </span>
                                            </td>
                                            <td class="text-success">+<?php echo $score['total_encouragement']; ?></td>
                                            <td class="text-danger">-<?php echo $score['total_disciplinary']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- رکوردهای اخیر -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">رکوردهای اخیر</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($data['staff_records'])): ?>
                            <div class="row">
                                <?php foreach (array_slice($data['staff_records'], 0, 10) as $record): // نمایش 10 رکورد آخر ?>
                                    <div class="col-12 mb-3">
                                        <div class="card record-card <?php echo $record['record_type'] == 'encouragement' ? 'encouragement' : 'disciplinary'; ?>">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <h6 class="card-title">
                                                            <?php if ($record['record_type'] == 'encouragement'): ?>
                                                                <i class="bi bi-award text-success"></i>
                                                            <?php else: ?>
                                                                <i class="bi bi-exclamation-triangle text-danger"></i>
                                                            <?php endif; ?>
                                                            <?php echo $record['title']; ?>
                                                        </h6>
                                                        <p class="card-text"><?php echo $record['description']; ?></p>
                                                        <div class="text-muted small">
                                                            <i class="bi bi-person"></i>
                                                            معلم: <?php echo $record['staff_first_name'] . ' ' . $record['staff_last_name']; ?>
                                                            | 
                                                            <i class="bi bi-calendar"></i>
                                                            تاریخ: <?php echo $record['jalali_date']; ?>
                                                            |
                                                            <i class="bi bi-person-plus"></i>
                                                            ثبت کننده: <?php echo $record['created_first_name'] . ' ' . $record['created_last_name']; ?>
                                                        </div>
                                                    </div>
                                                    <div class="ms-3">
                                                        <span class="badge bg-<?php echo $record['record_type'] == 'encouragement' ? 'success' : 'danger'; ?>">
                                                            <?php echo $record['record_type'] == 'encouragement' ? 'تشویقی' : 'انضباطی'; ?>
                                                        </span>
                                                        <?php if ($record['points'] != 0): ?>
                                                            <div class="mt-1 text-<?php echo $record['record_type'] == 'encouragement' ? 'success' : 'danger'; ?>">
                                                                <small>
                                                                    <?php echo $record['record_type'] == 'encouragement' ? '+' : '-'; ?>
                                                                    <?php echo abs($record['points']); ?>
                                                                </small>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info text-center">
                                <i class="bi bi-info-circle"></i>
                                هیچ رکوردی برای معلمان این پایه ثبت نشده است.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>