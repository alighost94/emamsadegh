<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حضور و غیاب - پنل اولیا</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { font-family: Vazir, sans-serif; }
        body { background-color: #f9fafc; }
        .sidebar { background: #2c3e50; color: white; height: 100vh; position: fixed; width: 250px; }
        .sidebar .nav-link { color: #bdc3c7; padding: 15px 20px; border-bottom: 1px solid #34495e; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: white; background: #34495e; }
        .main-content { margin-right: 250px; padding: 25px; }
        .calendar-day { border: 1px solid #dee2e6; padding: 8px; text-align: center; height: 80px; cursor: pointer; transition: background-color 0.2s; }
        .calendar-day.today { background-color: #e7f3ff; border: 2px solid #007bff; }
        .calendar-day:hover { background-color: #f0f8ff; }
        .attendance-badge { font-size: 0.8rem; }
        .present { background-color: #d4edda !important; color: #155724 !important; }
        .absent { background-color: #f8d7da !important; color: #721c24 !important; }
        .late { background-color: #fff3cd !important; color: #856404 !important; }
        .excused { background-color: #e2e3e5 !important; color: #383d41 !important; }
    </style>
</head>

<body>
<div class="container-fluid">
    <div class="row">
        <?php include 'app/views/parent/partials/sidebar.php'; ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 main-content">

            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-3 mb-4 border-bottom">
                <h1 class="h3 mb-0">حضور و غیاب دانش‌آموز</h1>
                <span class="badge bg-primary fs-6">
                    <?php echo $data['student_info']['first_name'] . ' ' . $data['student_info']['last_name']; ?>
                </span>
            </div>

            <!-- ✅ فیلتر فقط بر اساس تاریخ شمسی -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light fw-bold">فیلتر بر اساس تاریخ شمسی</div>
                <div class="card-body">
                    <form method="GET" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">ماه</label>
                            <select name="jalali_month" class="form-select">
                                <?php
                                $jalali_months = JalaliDate::getJalaliMonths();
                                $current_jalali_month = JalaliDate::gregorianToJalali(date('Y-m-d'), 'n');
                                foreach ($jalali_months as $num => $name): ?>
                                    <option value="<?php echo $num; ?>"
                                        <?php echo $num == $current_jalali_month ? 'selected' : ''; ?>>
                                        <?php echo $name; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">سال</label>
                            <select name="jalali_year" class="form-select">
                                <?php
                                $jalali_years = JalaliDate::getJalaliYears();
                                $current_jalali_year = JalaliDate::gregorianToJalali(date('Y-m-d'), 'Y');
                                foreach ($jalali_years as $year): ?>
                                    <option value="<?php echo $year; ?>"
                                        <?php echo $year == $current_jalali_year ? 'selected' : ''; ?>>
                                        <?php echo $year; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" name="filter_jalali" value="1" class="btn btn-primary w-100">
                                <i class="bi bi-search"></i> نمایش
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- ✅ تقویم ماهانه شمسی -->
            <div class="card shadow-sm">
                <div class="card-header bg-light fw-bold">
                    خلاصه حضور و غیاب ماه <?php echo $data['jalali_selected_month']; ?>
                </div>
                <div class="card-body">
                    <?php
                    $jalali_now = JalaliDate::gregorianToJalali(date('Y-m-d'));
                    $jalali_now_parts = JalaliDate::parseJalaliDate($jalali_now);
                    $current_jalali_year = $jalali_now_parts['year'];
                    $current_jalali_month = $jalali_now_parts['month'];
                    $current_jalali_day = $jalali_now_parts['day'];
                    $jalali_calendar = JalaliDate::getJalaliMonthCalendar($current_jalali_year, $current_jalali_month);
                    $attendance_by_date = [];
                    foreach ($data['monthly_attendance'] as $record) {
                        $attendance_by_date[$record['attendance_date']][] = $record;
                    }
                    ?>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead>
                                <tr class="text-center bg-light">
                                    <th>شنبه</th>
                                    <th>یکشنبه</th>
                                    <th>دوشنبه</th>
                                    <th>سه‌شنبه</th>
                                    <th>چهارشنبه</th>
                                    <th>پنجشنبه</th>
                                    <th>جمعه</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($jalali_calendar['weeks'] as $week): ?>
                                    <tr>
                                        <?php foreach ($week as $day): ?>
                                            <td class="calendar-day 
                                                <?php echo ($day && $day['jalali_day'] == $current_jalali_day) ? 'today' : ''; ?>"
                                                <?php if ($day): ?> data-date="<?php echo $day['gregorian_date']; ?>" <?php endif; ?>>
                                                <?php if ($day): ?>
                                                    <div class="fw-bold"><?php echo $day['jalali_day']; ?></div>
                                                    <div class="mt-2">
                                                        <?php
                                                        $attList = $attendance_by_date[$day['gregorian_date']] ?? [];
                                                        if (!empty($attList)) {
                                                            $status = $attList[0]['status'];
                                                            $map = ['present'=>'ح','absent'=>'غ','late'=>'ت','excused'=>'ع'];
                                                            $cls = ['present'=>'present','absent'=>'absent','late'=>'late','excused'=>'excused'];
                                                            echo '<span class="badge '.$cls[$status].'">'.$map[$status].'</span>';
                                                        } else {
                                                            echo '<span class="badge bg-secondary">-</span>';
                                                        }
                                                        ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="alert alert-light mt-3">
                        <strong>راهنما:</strong>
                        <span class="badge present attendance-badge me-2">ح (حاضر)</span>
                        <span class="badge absent attendance-badge me-2">غ (غایب)</span>
                        <span class="badge late attendance-badge me-2">ت (تأخیر)</span>
                        <span class="badge excused attendance-badge me-2">ع (عذردار)</span>
                        <span class="badge bg-secondary attendance-badge me-2">- (ثبت نشده)</span>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- ✅ مودال نمایش جزئیات روز انتخاب‌شده -->
<div class="modal fade" id="attendanceModal" tabindex="-1" aria-labelledby="attendanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="attendanceModalLabel">جزئیات حضور و غیاب</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="attendanceModalBody">
                <div class="text-center text-muted py-4">
                    در حال بارگذاری...
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.calendar-day[data-date]').forEach(day => {
    day.addEventListener('click', function() {
        const date = this.getAttribute('data-date');
        const modal = new bootstrap.Modal(document.getElementById('attendanceModal'));
        const body = document.getElementById('attendanceModalBody');
        const title = document.getElementById('attendanceModalLabel');

        title.textContent = "حضور و غیاب روز " + date;
        body.innerHTML = '<div class="text-center text-muted py-4">در حال بارگذاری...</div>';
        modal.show();

        fetch('<?php echo BASE_URL; ?>parent/attendance?date=' + date + '&ajax=1')
            .then(res => res.text())
            .then(html => body.innerHTML = html)
            .catch(() => body.innerHTML = '<div class="alert alert-danger text-center">خطا در دریافت اطلاعات.</div>');
    });
});
</script>
</body>
</html>
