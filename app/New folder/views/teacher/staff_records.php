<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پرونده شخصی - پنل معلم</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { font-family: 'Vazir', sans-serif; }
        .sidebar { background: #2c3e50; color: white; height: 100vh; position: fixed; width: 250px; }
        .sidebar .nav-link { color: #bdc3c7; padding: 15px 20px; border-bottom: 1px solid #34495e; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: white; background: #34495e; }
        .main-content { margin-right: 250px; padding: 20px; }
        .record-card { border-right: 4px solid transparent; transition: all 0.3s; }
        .record-card:hover { transform: translateY(-2px); }
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
            <?php include 'app/views/teacher/partials/sidebar.php'; ?>
            
            <!-- محتوای اصلی -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">پرونده شخصی</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <span class="badge bg-primary">
                            امتیاز: 
                            <?php
                            $score = $data['staff_score']['current_score'] ?? 100;
                            $score_class = 'score-excellent';
                            if ($score < 90) $score_class = 'score-good';
                            if ($score < 80) $score_class = 'score-average';
                            if ($score < 70) $score_class = 'score-poor';
                            if ($score < 60) $score_class = 'score-very-poor';
                            ?>
                            <span class="<?php echo $score_class; ?>"><?php echo $score; ?></span>
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
                    <div class="col-md-4">
                        <div class="card text-white bg-success">
                            <div class="card-body text-center">
                                <h4><?php echo count(array_filter($data['staff_records'], function($r) { return $r['record_type'] == 'encouragement'; })); ?></h4>
                                <small>موارد تشویقی</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-danger">
                            <div class="card-body text-center">
                                <h4><?php echo count(array_filter($data['staff_records'], function($r) { return $r['record_type'] == 'disciplinary'; })); ?></h4>
                                <small>موارد انضباطی</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-info">
                            <div class="card-body text-center">
                                <h4><?php echo count($data['staff_records']); ?></h4>
                                <small>کل رکوردها</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- لیست رکوردها -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">سوابق پرونده</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($data['staff_records'])): ?>
                            <div class="row">
                                <?php foreach ($data['staff_records'] as $record): ?>
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
                                                            <i class="bi bi-calendar"></i>
                                                            تاریخ: <?php echo $record['jalali_date']; ?>
                                                            | 
                                                            <i class="bi bi-person"></i>
                                                            ثبت کننده: <?php echo $record['created_first_name'] . ' ' . $record['created_last_name']; ?>
                                                            |
                                                            <?php if ($record['points'] != 0): ?>
                                                                <strong class="<?php echo $record['record_type'] == 'encouragement' ? 'text-success' : 'text-danger'; ?>">
                                                                    <?php echo $record['record_type'] == 'encouragement' ? '+' : '-'; ?>
                                                                    <?php echo abs($record['points']); ?> امتیاز
                                                                </strong>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                    <div class="ms-3">
                                                        <span class="badge bg-<?php echo $record['record_type'] == 'encouragement' ? 'success' : 'danger'; ?>">
                                                            <?php echo $record['record_type'] == 'encouragement' ? 'تشویقی' : 'انضباطی'; ?>
                                                        </span>
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
                                هیچ رکوردی برای شما ثبت نشده است.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- خلاصه امتیازات -->
                <?php if ($data['staff_score']): ?>
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">خلاصه امتیازات</h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <h4 class="<?php echo $score_class; ?>"><?php echo $data['staff_score']['current_score']; ?></h4>
                                    <small>امتیاز فعلی</small>
                                </div>
                                <div class="col-md-4">
                                    <h4 class="text-success">+<?php echo $data['staff_score']['total_encouragement'] ?? 0; ?></h4>
                                    <small>مجموع تشویق‌ها</small>
                                </div>
                                <div class="col-md-4">
                                    <h4 class="text-danger">-<?php echo $data['staff_score']['total_disciplinary'] ?? 0; ?></h4>
                                    <small>مجموع انضباط‌ها</small>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>