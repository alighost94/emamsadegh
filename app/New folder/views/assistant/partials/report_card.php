<?php
// توابع محاسباتی
function calculateCourseGrade($course) {
    if ($course['course_type'] == 'poodmani') {
        $poodman_grades = [];
        for ($i = 1; $i <= 5; $i++) {
            if (isset($course['poodman' . $i]) && $course['poodman' . $i] !== null && $course['poodman' . $i] > 0) {
                $poodman_grades[] = $course['poodman' . $i];
            }
        }
        return !empty($poodman_grades) ? round(array_sum($poodman_grades) / count($poodman_grades), 2) : 0;
    } else {
        $continuous1 = $course['continuous1'] ?? 0;
        $term1 = $course['term1'] ?? 0;
        $continuous2 = $course['continuous2'] ?? 0;
        $term2 = $course['term2'] ?? 0;
        
        $term1_avg = ($continuous1 > 0 && $term1 > 0) ? ($continuous1 + $term1) / 2 : 0;
        $term2_avg = ($continuous2 > 0 && $term2 > 0) ? ($continuous2 + $term2) / 2 : 0;
        
        if ($term1_avg > 0 && $term2_avg > 0) {
            return ($term1_avg + $term2_avg) / 2;
        } elseif ($term1_avg > 0) {
            return $term1_avg;
        } elseif ($term2_avg > 0) {
            return $term2_avg;
        }
        return 0;
    }
}

function getGradeStatus($grade) {
    if ($grade >= 17) return ['text' => 'عالی', 'class' => 'text-success', 'color' => 'success'];
    if ($grade >= 15) return ['text' => 'خوب', 'class' => 'text-info', 'color' => 'info'];
    if ($grade >= 12) return ['text' => 'متوسط', 'class' => 'text-warning', 'color' => 'warning'];
    if ($grade >= 10) return ['text' => 'ضعیف', 'class' => 'text-orange', 'color' => 'orange'];
    return ['text' => 'مردود', 'class' => 'text-danger', 'color' => 'danger'];
}

// محاسبه معدل کل
$total_grade = 0;
$course_count = 0;
$all_courses = array_merge($data['non_poodmani_courses'], $data['poodmani_courses']);

foreach ($all_courses as $course) {
    $course_grade = calculateCourseGrade($course);
    if ($course_grade > 0) {
        $total_grade += $course_grade;
        $course_count++;
    }
}

$student_average = $course_count > 0 ? round($total_grade / $course_count, 2) : 0;
$status = getGradeStatus($student_average);
?>

<div data-student-name="<?php echo $data['student']['first_name'] . ' ' . $data['student']['last_name']; ?>">
    <!-- هدر کارنامه -->
    <div class="text-center mb-4">
        <h4 class="text-primary">کارنامه آموزشی</h4>
        <h5><?php echo $data['student']['first_name'] . ' ' . $data['student']['last_name']; ?></h5>
        <p class="text-muted">
            شماره دانش‌آموزی: <?php echo $data['student']['student_number']; ?> | 
            کلاس: <?php echo $data['student']['class_name']; ?>
        </p>
        <div class="<?php echo $status['class']; ?>">
            <h3>معدل کل: <?php echo number_format($student_average, 2); ?></h3>
            <span class="badge bg-<?php echo $status['color']; ?>"><?php echo $status['text']; ?></span>
        </div>
    </div>

    <!-- دروس غیر پودمانی -->
    <?php if (!empty($data['non_poodmani_courses'])): ?>
        <div class="non-poodmani-section mb-4">
            <h5 class="text-warning mb-3">
                <i class="bi bi-journal-check"></i> دروس غیر پودمانی
            </h5>
            <div class="table-responsive">
                <table class="table table-bordered grade-table">
                    <thead>
                        <tr>
                            <th>نام درس</th>
                            <th>کد درس</th>
                            <th>مستمر ۱</th>
                            <th>ترم ۱</th>
                            <th>مستمر ۲</th>
                            <th>ترم ۲</th>
                            <th>نمره نهایی</th>
                            <th>وضعیت</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['non_poodmani_courses'] as $course): ?>
                            <?php
                            $course_grade = calculateCourseGrade($course);
                            $course_status = getGradeStatus($course_grade);
                            ?>
                            <tr>
                                <td><?php echo $course['course_name']; ?></td>
                                <td><?php echo $course['course_code']; ?></td>
                                <td><?php echo $course['continuous1'] ?? '-'; ?></td>
                                <td><?php echo $course['term1'] ?? '-'; ?></td>
                                <td><?php echo $course['continuous2'] ?? '-'; ?></td>
                                <td><?php echo $course['term2'] ?? '-'; ?></td>
                                <td class="fw-bold <?php echo $course_status['class']; ?>">
                                    <?php echo number_format($course_grade, 1); ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo $course_status['color']; ?>">
                                        <?php echo $course_status['text']; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <!-- دروس پودمانی -->
    <?php if (!empty($data['poodmani_courses'])): ?>
        <div class="poodmani-section">
            <h5 class="text-info mb-3">
                <i class="bi bi-journal-bookmark"></i> دروس پودمانی
            </h5>
            <div class="table-responsive">
                <table class="table table-bordered grade-table">
                    <thead>
                        <tr>
                            <th>نام درس</th>
                            <th>کد درس</th>
                            <th>پودمان ۱</th>
                            <th>پودمان ۲</th>
                            <th>پودمان ۳</th>
                            <th>پودمان ۴</th>
                            <th>پودمان ۵</th>
                            <th>نمره نهایی</th>
                            <th>وضعیت</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data['poodmani_courses'] as $course): ?>
                            <?php
                            $course_grade = calculateCourseGrade($course);
                            $course_status = getGradeStatus($course_grade);
                            ?>
                            <tr>
                                <td><?php echo $course['course_name']; ?></td>
                                <td><?php echo $course['course_code']; ?></td>
                                <td><?php echo $course['poodman1'] ?? '-'; ?></td>
                                <td><?php echo $course['poodman2'] ?? '-'; ?></td>
                                <td><?php echo $course['poodman3'] ?? '-'; ?></td>
                                <td><?php echo $course['poodman4'] ?? '-'; ?></td>
                                <td><?php echo $course['poodman5'] ?? '-'; ?></td>
                                <td class="fw-bold <?php echo $course_status['class']; ?>">
                                    <?php echo number_format($course_grade, 1); ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo $course_status['color']; ?>">
                                        <?php echo $course_status['text']; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <!-- خلاصه پایانی -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card bg-light">
                <div class="card-body">
                    <h6>خلاصه کارنامه</h6>
                    <p>تعداد دروس: <?php echo count($all_courses); ?></p>
                    <p>دروس غیر پودمانی: <?php echo count($data['non_poodmani_courses']); ?></p>
                    <p>دروس پودمانی: <?php echo count($data['poodmani_courses']); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-light">
                <div class="card-body text-center">
                    <h6>معدل کل</h6>
                    <h3 class="<?php echo $status['class']; ?>"><?php echo number_format($student_average, 2); ?></h3>
                    <span class="badge bg-<?php echo $status['color']; ?>"><?php echo $status['text']; ?></span>
                </div>
            </div>
        </div>
    </div>
</div>