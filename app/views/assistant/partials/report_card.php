<div class="report-card" data-student-name="<?php echo $data['student']['first_name'] . ' ' . $data['student']['last_name']; ?>">
    <!-- هدر کارنامه -->
    <div class="text-center mb-4">
        <h3 class="text-primary">کارنامه آموزشی</h3>
        <h5 class="text-muted">هنرستان امام صادق (ع)</h5>
    </div>

    <!-- اطلاعات دانش‌آموز -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0"><i class="bi bi-person-circle me-2"></i>اطلاعات دانش‌آموز</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <strong>نام و نام خانوادگی:</strong>
                    <?php echo $data['student']['first_name'] . ' ' . $data['student']['last_name']; ?>
                </div>
                <div class="col-md-3">
                    <strong>شماره دانش‌آموزی:</strong>
                    <?php echo $data['student']['student_number']; ?>
                </div>
                <div class="col-md-3">
                    <strong>پایه:</strong>
                    <?php echo $data['student']['grade_name']; ?>
                </div>
                <div class="col-md-3">
                    <strong>رشته:</strong>
                    <?php echo $data['student']['major_name']; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- آمار کلی -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center bg-success text-white">
                <div class="card-body py-3">
                    <h5 class="card-title"><?php echo $data['total_average']; ?></h5>
                    <p class="card-text mb-0">معدل کل</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center bg-info text-white">
                <div class="card-body py-3">
                    <h5 class="card-title"><?php echo $data['poodmani_average']; ?></h5>
                    <p class="card-text mb-0">معدل پودمانی</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center bg-warning text-white">
                <div class="card-body py-3">
                    <h5 class="card-title"><?php echo $data['non_poodmani_average']; ?></h5>
                    <p class="card-text mb-0">معدل غیر پودمانی</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center bg-secondary text-white">
                <div class="card-body py-3">
                    <h5 class="card-title"><?php echo count($data['poodmani_courses']) + count($data['non_poodmani_courses']); ?></h5>
                    <p class="card-text mb-0">تعداد دروس</p>
                </div>
            </div>
        </div>
    </div>

    <!-- دروس غیر پودمانی -->
    <?php if (!empty($data['non_poodmani_courses'])): ?>
        <div class="card mb-4">
            <div class="card-header bg-warning text-white">
                <h6 class="mb-0"><i class="bi bi-journal-text me-2"></i>دروس غیر پودمانی</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered grade-table">
                        <thead>
                            <tr>
                                <th>ردیف</th>
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
                            <?php foreach ($data['non_poodmani_courses'] as $index => $course): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo $course['course_name']; ?></td>
                                    <td><?php echo $course['course_code']; ?></td>
                                    <td class="text-center">
                                        <?php if (isset($course['continuous1']) && $course['continuous1'] > 0): ?>
                                            <span class="badge bg-<?php echo $course['continuous1'] >= 10 ? 'success' : 'danger'; ?>">
                                                <?php echo $course['continuous1']; ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if (isset($course['term1']) && $course['term1'] > 0): ?>
                                            <span class="badge bg-<?php echo $course['term1'] >= 10 ? 'success' : 'danger'; ?>">
                                                <?php echo $course['term1']; ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if (isset($course['continuous2']) && $course['continuous2'] > 0): ?>
                                            <span class="badge bg-<?php echo $course['continuous2'] >= 10 ? 'success' : 'danger'; ?>">
                                                <?php echo $course['continuous2']; ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if (isset($course['term2']) && $course['term2'] > 0): ?>
                                            <span class="badge bg-<?php echo $course['term2'] >= 10 ? 'success' : 'danger'; ?>">
                                                <?php echo $course['term2']; ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-<?php echo $course['calculated_grade'] >= 10 ? 'primary' : 'danger'; ?> fs-6">
                                            <?php echo $course['calculated_grade']; ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-<?php echo $course['calculated_grade'] >= 10 ? 'success' : 'danger'; ?>">
                                            <?php echo $course['calculated_grade'] >= 10 ? 'قبول' : 'مردود'; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- دروس پودمانی -->
    <?php if (!empty($data['poodmani_courses'])): ?>
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="bi bi-journal-check me-2"></i>دروس پودمانی</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered grade-table">
                        <thead>
                            <tr>
                                <th>ردیف</th>
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
                            <?php foreach ($data['poodmani_courses'] as $index => $course): ?>
                                <tr>
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo $course['course_name']; ?></td>
                                    <td><?php echo $course['course_code']; ?></td>
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <td class="text-center">
                                            <?php if (isset($course['poodman' . $i]) && $course['poodman' . $i] > 0): ?>
                                                <span class="badge bg-<?php echo $course['poodman' . $i] >= 10 ? 'success' : 'danger'; ?>">
                                                    <?php echo $course['poodman' . $i]; ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                    <?php endfor; ?>
                                    <td class="text-center">
                                        <span class="badge bg-<?php echo $course['calculated_grade'] >= 10 ? 'primary' : 'danger'; ?> fs-6">
                                            <?php echo $course['calculated_grade']; ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-<?php echo $course['calculated_grade'] >= 10 ? 'success' : 'danger'; ?>">
                                            <?php echo $course['calculated_grade'] >= 10 ? 'قبول' : 'مردود'; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- خلاصه وضعیت -->
    <div class="card">
        <div class="card-header bg-dark text-white">
            <h6 class="mb-0"><i class="bi bi-graph-up me-2"></i>خلاصه وضعیت تحصیلی</h6>
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-4">
                    <div class="border rounded p-3">
                        <h4 class="text-success"><?php echo $data['total_average']; ?></h4>
                        <p class="mb-0 text-muted">معدل کل</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3">
                        <h4 class="text-info"><?php echo count(array_filter($data['poodmani_courses'], fn($c) => $c['calculated_grade'] >= 10)); ?> از <?php echo count($data['poodmani_courses']); ?></h4>
                        <p class="mb-0 text-muted">دروس پودمانی قبول</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded p-3">
                        <h4 class="text-warning"><?php echo count(array_filter($data['non_poodmani_courses'], fn($c) => $c['calculated_grade'] >= 10)); ?> از <?php echo count($data['non_poodmani_courses']); ?></h4>
                        <p class="mb-0 text-muted">دروس غیر پودمانی قبول</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>