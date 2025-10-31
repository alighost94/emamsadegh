<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پرونده همکاران - پنل مدیر</title>

    <!-- فونت و بوت‌استرپ -->
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        * { font-family: 'Vazir', sans-serif; }

        body {
            margin: 0;
            background: #f8f9fa;
        }

        /* والد فلکسی که سایدبار + محتوا را کنار هم نگه می‌دارد */
        .layout {
            display: flex;
            min-height: 100vh;
            align-items: flex-start;
        }

        .main-content {
            flex-grow: 1;
            background: #f8f9fa;
            padding: 20px;
            min-height: 100vh;
        }

        /* کارت‌ها و استایل‌ها */
        .staff-card {
            transition: all 0.3s;
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border-right: 4px solid transparent;
            background: #fff;
        }
        .staff-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        .teacher-card { border-right-color: #28a745; }
        .assistant-card { border-right-color: #17a2b8; }

        .score-excellent { color: #28a745; font-weight: bold; }
        .score-good { color: #20c997; font-weight: bold; }
        .score-average { color: #ffc107; font-weight: bold; }
        .score-poor { color: #fd7e14; font-weight: bold; }
        .score-very-poor { color: #dc3545; font-weight: bold; }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            margin-bottom: 25px;
            background: #fff;
        }

        .card-header {
            background: white;
            border-bottom: 1px solid #eaeaea;
            padding: 20px 25px;
            border-radius: 15px 15px 0 0 !important;
        }

        .card-header h5 {
            margin: 0;
            color: #2c3e50;
            font-weight: 600;
        }

        .card-body { padding: 25px; }

        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border-right: 4px solid;
            height: 100%;
        }

        .stats-card h4 { font-size: 1.8rem; font-weight: bold; margin-bottom: 5px; }
        .stats-card small { color: #6c757d; font-size: 0.9rem; }

        .btn { border-radius: 10px; padding: 10px 20px; font-weight: 500; }

        .staff-name { font-weight: 600; color: #2c3e50; margin-bottom: 10px; }
        .staff-info { color: #6c757d; font-size: 0.85rem; }
        .staff-info i { width: 20px; text-align: center; margin-left: 5px; }

        .score-badge {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
            padding: 8px 12px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 0.9rem;
        }

        .alert { border: none; border-radius: 10px; }

        /* ریسپانسیو */
        @media (max-width: 991px) {
            .layout { flex-direction: column; }
            .main-content { width: 100%; }
        }
    </style>
</head>
<body>

<div class="layout">
    <!-- سایدبار (فایل جداگانه) -->
    <?php include 'app/views/partials/sidebar.php'; ?>

    <!-- محتوای اصلی -->
    <div class="main-content">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">پرونده همکاران</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <a href="<?php echo BASE_URL; ?>admin/staffRecords" class="btn btn-outline-primary">
                    <i class="bi bi-list-check"></i> مشاهده تمام رکوردها
                </a>
            </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- آمار کلی -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="stats-card" style="border-right-color: #007bff;">
                    <h4 class="text-primary"><?php echo isset($data['teachers']) ? count($data['teachers']) : 0; ?></h4>
                    <small>تعداد معلمان</small>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stats-card" style="border-right-color: #17a2b8;">
                    <h4 class="text-info"><?php echo isset($data['assistants']) ? count($data['assistants']) : 0; ?></h4>
                    <small>تعداد معاونین</small>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stats-card" style="border-right-color: #28a745;">
                    <h4 class="text-success">
                        <?php
                        echo isset($data['staff_scores'])
                            ? count(array_filter($data['staff_scores'], function($s) { return ($s['current_score'] ?? 0) >= 90; }))
                            : 0;
                        ?>
                    </h4>
                    <small>همکاران عالی</small>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stats-card" style="border-right-color: #ffc107;">
                    <h4 class="text-warning">
                        <?php
                        echo isset($data['staff_scores'])
                            ? count(array_filter($data['staff_scores'], function($s) { return ($s['current_score'] ?? 0) < 80; }))
                            : 0;
                        ?>
                    </h4>
                    <small>نیاز به توجه</small>
                </div>
            </div>
        </div>

        <!-- لیست معلمان -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 text-success">
                    <i class="bi bi-person-badge me-2"></i>
                    معلمان
                    <span class="badge bg-success ms-2"><?php echo isset($data['teachers']) ? count($data['teachers']) . ' نفر' : '0 نفر'; ?></span>
                </h5>
            </div>

            <div class="card-body">
                <div class="row">
                    <?php if (!empty($data['teachers'])): ?>
                        <?php foreach ($data['teachers'] as $teacher): ?>
                            <?php
                            $staff_score = null;
                            if (!empty($data['staff_scores'])) {
                                foreach ($data['staff_scores'] as $score) {
                                    if (($score['staff_type'] ?? '') == 'teacher' && ($score['staff_id'] ?? '') == ($teacher['id'] ?? null)) {
                                        $staff_score = $score;
                                        break;
                                    }
                                }
                            }
                            $score_value = $staff_score['current_score'] ?? 100;

                            $score_class = 'score-excellent';
                            if ($score_value < 90) $score_class = 'score-good';
                            if ($score_value < 80) $score_class = 'score-average';
                            if ($score_value < 70) $score_class = 'score-poor';
                            if ($score_value < 60) $score_class = 'score-very-poor';
                            ?>
                            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                                <div class="card staff-card teacher-card h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div class="staff-name"><?php echo htmlspecialchars(($teacher['first_name'] ?? '') . ' ' . ($teacher['last_name'] ?? '')); ?></div>
                                            <span class="score-badge <?php echo $score_class; ?>"><?php echo htmlspecialchars($score_value); ?></span>
                                        </div>

                                        <div class="staff-info mb-3">
                                            <div class="mb-2"><i class="bi bi-telephone"></i> <?php echo htmlspecialchars($teacher['mobile'] ?? '-'); ?></div>
                                            <div class="mb-2"><i class="bi bi-briefcase"></i> <?php echo htmlspecialchars($teacher['expertise'] ?? 'ثبت نشده'); ?></div>
                                            <?php if (!empty($teacher['employment_date'])): ?>
                                                <div class="mb-2"><i class="bi bi-calendar"></i> استخدام: <?php echo htmlspecialchars($teacher['employment_date']); ?></div>
                                            <?php endif; ?>
                                        </div>

                                        <a href="<?php echo BASE_URL; ?>admin/staffDetail/teacher/<?php echo $teacher['id']; ?>" class="btn btn-outline-success w-100">
                                            <i class="bi bi-eye"></i> مشاهده پرونده کامل
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="alert alert-info text-center"><i class="bi bi-info-circle"></i> هیچ معلمی ثبت نشده است.</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- لیست معاونین -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 text-info">
                    <i class="bi bi-person-gear me-2"></i>
                    معاونین
                    <span class="badge bg-info ms-2"><?php echo isset($data['assistants']) ? count($data['assistants']) . ' نفر' : '0 نفر'; ?></span>
                </h5>
            </div>

            <div class="card-body">
                <div class="row">
                    <?php if (!empty($data['assistants'])): ?>
                        <?php foreach ($data['assistants'] as $assistant): ?>
                            <?php
                            $staff_score = null;
                            if (!empty($data['staff_scores'])) {
                                foreach ($data['staff_scores'] as $score) {
                                    if (($score['staff_type'] ?? '') == 'assistant' && ($score['staff_id'] ?? '') == ($assistant['id'] ?? null)) {
                                        $staff_score = $score;
                                        break;
                                    }
                                }
                            }
                            $score_value = $staff_score['current_score'] ?? 100;

                            $score_class = 'score-excellent';
                            if ($score_value < 90) $score_class = 'score-good';
                            if ($score_value < 80) $score_class = 'score-average';
                            if ($score_value < 70) $score_class = 'score-poor';
                            if ($score_value < 60) $score_class = 'score-very-poor';
                            ?>
                            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                                <div class="card staff-card assistant-card h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div class="staff-name"><?php echo htmlspecialchars(($assistant['first_name'] ?? '') . ' ' . ($assistant['last_name'] ?? '')); ?></div>
                                            <span class="score-badge <?php echo $score_class; ?>"><?php echo htmlspecialchars($score_value); ?></span>
                                        </div>

                                        <div class="staff-info mb-3">
                                            <div class="mb-2"><i class="bi bi-telephone"></i> <?php echo htmlspecialchars($assistant['mobile'] ?? '-'); ?></div>
                                            <div class="mb-2"><i class="bi bi-house-door"></i> پایه <?php echo htmlspecialchars($assistant['grade_name'] ?? '-'); ?></div>
                                            <div class="mb-2"><i class="bi bi-briefcase"></i> <?php echo htmlspecialchars($assistant['responsibilities'] ?? 'ثبت نشده'); ?></div>
                                        </div>

                                        <a href="<?php echo BASE_URL; ?>admin/staffDetail/assistant/<?php echo $assistant['id']; ?>" class="btn btn-outline-info w-100">
                                            <i class="bi bi-eye"></i> مشاهده پرونده کامل
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="alert alert-info text-center"><i class="bi bi-info-circle"></i> هیچ معاونی ثبت نشده است.</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div><!-- /.main-content -->
</div><!-- /.layout -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
