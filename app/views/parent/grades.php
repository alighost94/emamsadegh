<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نمرات - پنل اولیا</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { font-family: 'Vazir', sans-serif; }
        .sidebar { background: #2c3e50; color: white; height: 100vh; position: fixed; width: 250px; }
        .sidebar .nav-link { color: #bdc3c7; padding: 15px 20px; border-bottom: 1px solid #34495e; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: white; background: #34495e; }
        .main-content { margin-right: 250px; padding: 20px; }
        .grade-excellent { color: #28a745; font-weight: bold; }
        .grade-good { color: #20c997; font-weight: bold; }
        .grade-average { color: #ffc107; font-weight: bold; }
        .grade-poor { color: #fd7e14; font-weight: bold; }
        .grade-fail { color: #dc3545; font-weight: bold; }
        .course-card { transition: all 0.3s; border-left: 4px solid transparent; }
        .course-card:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .poodmani-card { border-left-color: #17a2b8; }
        .non-poodmani-card { border-left-color: #6f42c1; }
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
                    <h1 class="h2">کارنامه تحصیلی دانش‌آموز</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <span class="badge bg-primary">
                            <?php echo $data['student_info']['first_name'] . ' ' . $data['student_info']['last_name']; ?>
                        </span>
                    </div>
                </div>

                <!-- آمار کلی -->
                <?php
                $total_courses = count($data['grades']);
                $excellent_courses = count(array_filter($data['grades'], function($g) { 
                    return $g['calculated_grade'] >= 17; 
                }));
                $good_courses = count(array_filter($data['grades'], function($g) { 
                    return $g['calculated_grade'] >= 15 && $g['calculated_grade'] < 17; 
                }));
                $average_courses = count(array_filter($data['grades'], function($g) { 
                    return $g['calculated_grade'] >= 12 && $g['calculated_grade'] < 15; 
                }));
                $poor_courses = count(array_filter($data['grades'], function($g) { 
                    return $g['calculated_grade'] < 12; 
                }));
                
                $total_grade = array_sum(array_column($data['grades'], 'calculated_grade'));
                $average_grade = $total_courses > 0 ? round($total_grade / $total_courses, 2) : 0;
                ?>

                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-white bg-primary">
                            <div class="card-body text-center p-3">
                                <h4><?php echo $total_courses; ?></h4>
                                <small>تعداد دروس</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-success">
                            <div class="card-body text-center p-3">
                                <h4><?php echo $average_grade; ?></h4>
                                <small>معدل کل</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-info">
                            <div class="card-body text-center p-3">
                                <h4><?php echo $excellent_courses; ?></h4>
                                <small>دروس عالی</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-warning">
                            <div class="card-body text-center p-3">
                                <h4><?php echo $poor_courses; ?></h4>
                                <small>نیاز به توجه</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- دروس پودمانی -->
                <!-- در بخش نمایش دروس پودمانی -->
<?php if (!empty($data['poodmani_grades'])): ?>
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="bi bi-journal-check"></i> دروس پودمانی</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <?php foreach ($data['poodmani_grades'] as $grade): ?>
                    <?php
                    $course_grade = $grade['calculated_grade'] ?? 0;
                    $grade_class = 'grade-excellent';
                    if ($course_grade < 17) $grade_class = 'grade-good';
                    if ($course_grade < 15) $grade_class = 'grade-average';
                    if ($course_grade < 12) $grade_class = 'grade-poor';
                    if ($course_grade < 10) $grade_class = 'grade-fail';
                    ?>
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card course-card poodmani-card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h6 class="card-title"><?php echo $grade['course_name']; ?></h6>
                                    <?php if ($course_grade > 0): ?>
                                        <span class="<?php echo $grade_class; ?>">
                                            <?php echo $course_grade; ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">ثبت نشده</span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="course-info mb-3">
                                    <small class="text-muted d-block">
                                        <strong>کد درس:</strong> <?php echo $grade['course_code']; ?>
                                    </small>
                                    <small class="text-muted d-block">
                                        <strong>واحد:</strong> <?php echo $grade['unit']; ?>
                                    </small>
                                    <?php if (!empty($grade['teacher_first_name'])): ?>
                                        <small class="text-muted d-block">
                                            <strong>معلم:</strong> <?php echo $grade['teacher_first_name'] . ' ' . $grade['teacher_last_name']; ?>
                                        </small>
                                    <?php endif; ?>
                                </div>

                                <!-- نمرات پودمانی -->
                                <div class="poodmani-grades">
                                    <small class="text-muted d-block mb-2"><strong>نمرات پودمانی:</strong></small>
                                    <div class="row text-center">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <div class="col">
                                                <small>پ<?php echo $i; ?></small>
                                                <div class="fw-bold">
                                                    <?php 
                                                    $poodman_grade = $grade['poodman' . $i] ?? null;
                                                    echo $poodman_grade ?: '-'; 
                                                    ?>
                                                </div>
                                            </div>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

                <!-- دروس غیر پودمانی -->
                <?php if (!empty($data['non_poodmani_grades'])): ?>
                    <div class="card">
                        <div class="card-header bg-purple text-white" style="background-color: #6f42c1 !important;">
                            <h5 class="mb-0"><i class="bi bi-journal-text"></i> دروس غیر پودمانی</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php foreach ($data['non_poodmani_grades'] as $grade): ?>
                                    <?php
                                    $course_grade = $grade['calculated_grade'];
                                    $grade_class = 'grade-excellent';
                                    if ($course_grade < 17) $grade_class = 'grade-good';
                                    if ($course_grade < 15) $grade_class = 'grade-average';
                                    if ($course_grade < 12) $grade_class = 'grade-poor';
                                    if ($course_grade < 10) $grade_class = 'grade-fail';
                                    ?>
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="card course-card non-poodmani-card h-100">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-3">
                                                    <h6 class="card-title"><?php echo $grade['course_name']; ?></h6>
                                                    <span class="<?php echo $grade_class; ?>">
                                                        <?php echo $course_grade; ?>
                                                    </span>
                                                </div>
                                                
                                                <div class="course-info mb-3">
                                                    <small class="text-muted d-block">
                                                        <strong>کد درس:</strong> <?php echo $grade['course_code']; ?>
                                                    </small>
                                                    <small class="text-muted d-block">
                                                        <strong>واحد:</strong> <?php echo $grade['unit']; ?>
                                                    </small>
                                                    <small class="text-muted d-block">
                                                        <strong>معلم:</strong> <?php echo $grade['teacher_first_name'] . ' ' . $grade['teacher_last_name']; ?>
                                                    </small>
                                                </div>

                                                <!-- نمرات نیمسال -->
                                                <div class="non-poodmani-grades">
                                                    <small class="text-muted d-block mb-2"><strong>نیمسال اول:</strong></small>
                                                    <div class="row text-center mb-2">
                                                        <div class="col">
                                                            <small>مستمر</small>
                                                            <div class="fw-bold"><?php echo $grade['continuous1'] ?? '-'; ?></div>
                                                        </div>
                                                        <div class="col">
                                                            <small>ترم</small>
                                                            <div class="fw-bold"><?php echo $grade['term1'] ?? '-'; ?></div>
                                                        </div>
                                                    </div>
                                                    
                                                    <small class="text-muted d-block mb-2"><strong>نیمسال دوم:</strong></small>
                                                    <div class="row text-center">
                                                        <div class="col">
                                                            <small>مستمر</small>
                                                            <div class="fw-bold"><?php echo $grade['continuous2'] ?? '-'; ?></div>
                                                        </div>
                                                        <div class="col">
                                                            <small>ترم</small>
                                                            <div class="fw-bold"><?php echo $grade['term2'] ?? '-'; ?></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (empty($data['grades'])): ?>
                    <div class="alert alert-info text-center">
                        <i class="bi bi-info-circle"></i>
                        هیچ نمره‌ای برای دانش‌آموز ثبت نشده است.
                    </div>
                <?php endif; ?>

                <!-- راهنمای رنگ‌ها -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0">راهنمای نمرات</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3">
                                <span class="grade-excellent">۱۷ - ۲۰ (عالی)</span>
                            </div>
                            <div class="col-md-3">
                                <span class="grade-good">۱۵ - ۱۶.۹ (خوب)</span>
                            </div>
                            <div class="col-md-3">
                                <span class="grade-average">۱۲ - ۱۴.۹ (متوسط)</span>
                            </div>
                            <div class="col-md-3">
                                <span class="grade-poor">۱۰ - ۱۱.۹ (ضعیف)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>