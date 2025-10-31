<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>حضور و غیاب - پنل معلم</title>
  <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/css/persian-datepicker.min.css">
  <style>
    * { font-family: Vazir, sans-serif; }
    body { background: #f8f9fa; }

    /* Sidebar */
    .sidebar {
      background: #2c3e50;
      color: #fff;
      height: 100vh;
      position: fixed;
      width: 250px;
    }
    .sidebar .nav-link {
      color: #bdc3c7;
      padding: 12px 20px;
      border-bottom: 1px solid #34495e;
      transition: all 0.2s;
    }
    .sidebar .nav-link:hover, .sidebar .nav-link.active {
      color: #fff;
      background: #34495e;
    }

    /* Layout */
    .main-content { margin-right: 250px; padding: 20px; }

    /* Table Row Status */
    .present { background: #e8f5e8 !important; border-right: 4px solid #28a745; }
    .absent { background: #fde8e8 !important; border-right: 4px solid #dc3545; }
    .late { background: #fff9e6 !important; border-right: 4px solid #ffc107; }
    .excused { background: #f0f0f0 !important; border-right: 4px solid #6c757d; }

    /* Cards */
    .card-custom {
      border: none;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.08);
      transition: all 0.3s ease;
    }
    .card-custom:hover { transform: translateY(-3px); }

    .btn-custom { border-radius: 10px; padding: 8px 20px; }
    .btn-gradient-primary { background: linear-gradient(135deg,#667eea,#764ba2); border:none; color:#fff; }
    .btn-gradient-success { background: linear-gradient(135deg,#28a745,#20c997); border:none; color:#fff; }

    .stats-card {
      background: #fff;
      border-radius: 12px;
      padding: 20px;
      text-align: center;
      border-left: 4px solid;
    }
    .stats-present { border-left-color: #28a745; }
    .stats-absent { border-left-color: #dc3545; }
    .stats-late { border-left-color: #ffc107; }
    .stats-excused { border-left-color: #6c757d; }

    .status-badge { font-size: .8rem; padding: 5px 10px; border-radius: 20px; }
    
    /* Class Badge */
    .class-badge {
      background: #e3f2fd;
      color: #1976d2;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 0.8rem;
      border: 1px solid #bbdefb;
    }
  </style>
</head>

<body>
<div class="container-fluid">
  <div class="row">
    <!-- Sidebar -->
    <?php include 'app/views/teacher/partials/sidebar.php'; ?>

    <main class="col-md-9 ms-sm-auto col-lg-10 main-content">

      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
        <div>
          <h2 class="h4 text-dark mb-1">حضور و غیاب دانش‌آموزان</h2>
          <small class="text-muted">مدیریت و ثبت وضعیت حضور دانش‌آموزان</small>
        </div>
        <button class="btn btn-outline-secondary" onclick="window.print()">
          <i class="bi bi-printer"></i> چاپ
        </button>
      </div>

      <!-- پیام موفقیت -->
      <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
          <i class="bi bi-check-circle me-2"></i><?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <!-- فیلتر -->
      <div class="card card-custom mb-4">
        <div class="card-header bg-white fw-bold text-primary">
          <i class="bi bi-funnel me-2"></i> فیلتر و جستجو
        </div>
        <div class="card-body">
          <form method="GET" id="filterForm" class="row g-3">
            <div class="col-md-4">
              <label class="form-label fw-bold">درس مورد نظر</label>
              <select name="course_id" id="courseSelect" class="form-select" required 
                      onchange="AttendanceApp.loadClasses(this.value)">
                <option value="">انتخاب درس</option>
                <?php foreach ($data['courses'] as $c): ?>
                  <option value="<?= $c['course_id'] ?>" 
                          data-major-id="<?= $c['major_id'] ?>"
                          data-grade-id="<?= $c['grade_id'] ?>"
                          <?= $data['selected_course']==$c['course_id']?'selected':'' ?>>
                    <?= $c['course_code'].' - '.$c['course_name'].' ('.$c['major_name'].' - '.$c['grade_name'].')' ?>
                    <?php if (!empty($c['class_name'])): ?>
                      - <?= $c['class_name'] ?>
                    <?php endif; ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            
            <!-- 🔥 بخش جدید: انتخاب کلاس -->
            <div class="col-md-3" id="classSelection" style="<?= empty($data['selected_class']) ? 'display: none;' : '' ?>">
              <label class="form-label fw-bold">کلاس</label>
              <select name="class_id" id="classSelect" class="form-select">
                <option value="">همه کلاس‌ها</option>
                <?php if (!empty($data['selected_class'])): ?>
                  <option value="<?= $data['selected_class'] ?>" selected>
                    <?= $data['students'][0]['class_name'] ?? 'کلاس انتخاب شده' ?>
                  </option>
                <?php endif; ?>
              </select>
              <small class="text-muted">در صورت عدم انتخاب، همه کلاس‌ها نمایش داده می‌شوند</small>
            </div>
            
            <div class="col-md-3">
    <label class="form-label fw-bold">تاریخ (شمسی)</label>
    <div class="input-group">
        <span class="input-group-text"><i class="bi bi-calendar"></i></span>
        <input type="text" name="date" id="dateInput" readonly
               value="<?= $data['display_date'] ?>" 
               class="form-control jalali-date" required>
    </div>
</div>
            <div class="col-md-2 d-flex align-items-end">
              <button type="submit" class="btn btn-gradient-primary w-100 btn-custom">
                <i class="bi bi-search"></i> جستجو
              </button>
            </div>
          </form>
        </div>
      </div>

      <?php if ($data['selected_course']): ?>
        <?php
          $map = fn($s)=>count(array_filter($data['existing_attendance'],fn($a)=>$a['status']==$s));
          $present=$map('present'); $absent=$map('absent'); $late=$map('late'); $excused=$map('excused');
          $total=count($data['students']);
          
          // 🔥 اطلاعات کلاس انتخاب شده
          $selected_class_name = !empty($data['students']) ? $data['students'][0]['class_name'] : 'همه کلاس‌ها';
        ?>
        
        <!-- هدر اطلاعات کلاس -->
        <div class="alert alert-info d-flex justify-content-between align-items-center">
          <div>
            <i class="bi bi-info-circle me-2"></i>
            <strong>کلاس انتخاب شده:</strong> 
            <span class="class-badge"><?= $selected_class_name ?></span>
            | <strong>تعداد دانش‌آموزان:</strong> <?= $total ?> نفر
          </div>
          <div class="text-muted small">
            <?= $data['controller']->getJalaliDate($data['selected_date']) ?>
          </div>
        </div>

        <!-- Stats -->
        <div class="row g-3 mb-4">
          <div class="col-md-3"><div class="stats-card stats-present"><i class="bi bi-check-circle-fill text-success fs-2"></i><h3><?=$present?></h3><p>حاضر</p></div></div>
          <div class="col-md-3"><div class="stats-card stats-absent"><i class="bi bi-x-circle-fill text-danger fs-2"></i><h3><?=$absent?></h3><p>غایب</p></div></div>
          <div class="col-md-3"><div class="stats-card stats-late"><i class="bi bi-clock-fill text-warning fs-2"></i><h3><?=$late?></h3><p>تأخیر</p></div></div>
          <div class="col-md-3"><div class="stats-card stats-excused"><i class="bi bi-info-circle-fill text-secondary fs-2"></i><h3><?=$excused?></h3><p>عذردار</p></div></div>
        </div>

        <!-- فرم حضور -->
        <div class="card card-custom">
          <div class="card-header bg-white fw-bold text-primary">
            <i class="bi bi-clipboard-check me-2"></i> ثبت حضور و غیاب
            <span class="class-badge float-start"><?= $selected_class_name ?></span>
          </div>
          <div class="card-body">
            <?php if ($data['students']): ?>
              <form method="POST" id="attendanceForm">
                <input type="hidden" name="class_id" value="<?= $data['selected_class'] ?>">
                
                <div class="table-responsive">
                  <table class="table align-middle">
                    <thead class="table-light">
                      <tr>
                        <th>#</th>
                        <th>نام دانش‌آموز</th>
                        <th>شماره دانش‌آموزی</th>
                        <th>کلاس</th>
                        <th>وضعیت</th>
                        <th>توضیحات</th>
                        <th>عملیات سریع</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($data['students'] as $i=>$s):
                        $att = null;
                        // پیدا کردن وضعیت حضور برای این دانش‌آموز
                        foreach ($data['existing_attendance'] as $attendance) {
                          if ($attendance['student_id'] == $s['id']) {
                            $att = $attendance;
                            break;
                          }
                        }
                        $status = $att['status'] ?? '';
                      ?>
                      <tr class="<?= $status ?>">
                        <td class="fw-bold"><?= $i+1 ?></td>
                        <td>
                          <div class="d-flex align-items-center">
                            <i class="bi bi-person-circle text-muted me-2"></i>
                            <?= $s['first_name'].' '.$s['last_name'] ?>
                          </div>
                        </td>
                        <td>
                          <span class="text-muted"><?= $s['student_number'] ?></span>
                        </td>
                        <td>
                          <span class="class-badge"><?= $s['class_name'] ?></span>
                        </td>
                        <td>
                          <select name="attendance[<?= $s['id'] ?>]" class="form-select form-select-sm attendance-status">
                            <?php foreach(['present'=>'حاضر','absent'=>'غایب','late'=>'تأخیر','excused'=>'عذردار'] as $k=>$v): ?>
                              <option value="<?= $k ?>" <?= $status==$k?'selected':'' ?>><?= $v ?></option>
                            <?php endforeach; ?>
                          </select>
                        </td>
                        <td>
                          <input name="notes[<?= $s['id'] ?>]" 
                                 value="<?= $att['notes']??'' ?>" 
                                 class="form-control form-control-sm" 
                                 placeholder="توضیحات اختیاری...">
                        </td>
                        <td>
                          <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-success quick" data-id="<?= $s['id'] ?>" data-status="present" title="حاضر">
                              <i class="bi bi-check-lg"></i>
                            </button>
                            <button type="button" class="btn btn-outline-danger quick" data-id="<?= $s['id'] ?>" data-status="absent" title="غایب">
                              <i class="bi bi-x-lg"></i>
                            </button>
                            <button type="button" class="btn btn-outline-warning quick" data-id="<?= $s['id'] ?>" data-status="late" title="تأخیر">
                              <i class="bi bi-clock"></i>
                            </button>
                          </div>
                        </td>
                      </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
                
                <div class="mt-3 d-flex justify-content-between align-items-center border-top pt-3">
                  <div class="form-check">
                    <input type="checkbox" id="confirmAll" class="form-check-input">
                    <label for="confirmAll" class="form-check-label">تأیید صحت اطلاعات وارد شده</label>
                  </div>
                  <div>
                    <button type="reset" class="btn btn-secondary btn-custom">
                      <i class="bi bi-arrow-clockwise"></i> بازنشانی
                    </button>
                    <button type="submit" id="submitBtn" class="btn btn-gradient-success btn-custom" disabled>
                      <i class="bi bi-check-circle"></i> ثبت نهایی حضور و غیاب
                    </button>
                  </div>
                </div>
              </form>
            <?php else: ?>
              <div class="text-center py-5 text-muted">
                <i class="bi bi-people display-4 d-block mb-3"></i>
                دانش‌آموزی در این کلاس یافت نشد.
              </div>
            <?php endif; ?>
          </div>
        </div>
      <?php else: ?>
        <div class="alert alert-info text-center">
          <i class="bi bi-info-circle me-2"></i>
          لطفاً یک درس و تاریخ را انتخاب کنید.
        </div>
      <?php endif; ?>

    </main>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/persian-date@1.1.0/dist/persian-date.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/persian-datepicker@1.2.0/dist/js/persian-datepicker.min.js"></script>

<script>
const AttendanceApp = {
  init() {
    this.bindDatepicker();
    this.bindEvents();
  },
  
  bindDatepicker() {
    $('.jalali-date').persianDatepicker({
        format: 'YYYY/MM/DD',
        autoClose: true,
        onSelect: (unixDate) => {
            // 🔥 تبدیل تاریخ انتخاب شده به رشته شمسی
            const selectedDate = new persianDate(unixDate);
            const jalaliDateString = selectedDate.format('YYYY/MM/DD');
            
            // آپدیت مقدار فیلد
            $('#dateInput').val(jalaliDateString);
            
            // ارسال فرم
            $('#filterForm').submit();
        }
    });
},
  
  bindEvents() {
    $(document)
      .on('change', '#courseSelect', e => {
        if (e.target.value && $('#dateInput').val()) {
          $('#filterForm').submit();
        }
      })
      .on('change', '.attendance-status', e => this.updateRow(e))
      .on('click', '.quick', e => this.quickAction(e))
      .on('change', '#confirmAll', e => $('#submitBtn').prop('disabled', !e.target.checked))
      .on('keydown', e => { 
        if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') e.preventDefault(); 
      });
  },
  
  // 🔥 تابع جدید برای بارگذاری کلاس‌ها
  loadClasses(courseId) {
    const classSelection = $('#classSelection');
    const classSelect = $('#classSelect');
    
    if (!courseId) {
      classSelection.hide();
      return;
    }
    
    const selectedOption = $(`#courseSelect option[value="${courseId}"]`);
    const majorId = selectedOption.data('major-id');
    const gradeId = selectedOption.data('grade-id');
    
    if (majorId && gradeId) {
      // بارگذاری کلاس‌های مربوط به این رشته و پایه
      fetch(`<?php echo BASE_URL; ?>teacher/getClassesByCourse/${courseId}`)
        .then(response => response.json())
        .then(classes => {
          let html = '<option value="">همه کلاس‌ها</option>';
          
          if (classes && classes.length > 0) {
            classes.forEach(cls => {
              html += `<option value="${cls.id}">${cls.name}</option>`;
            });
          }
          
          classSelect.html(html);
          classSelection.show();
        })
        .catch(error => {
          console.error('Error:', error);
          classSelection.hide();
        });
    } else {
      classSelection.hide();
    }
  },
  
  updateRow(e) {
    const row = e.target.closest('tr');
    row.className = e.target.value;
    this.toast('وضعیت به‌روز شد', 'success');
  },
  
  quickAction(e) {
    const {id, status} = e.currentTarget.dataset;
    const select = $(`select[name="attendance[${id}]"]`);
    select.val(status).trigger('change');
  },
  
  toast(msg, type='info') {
    const t = $(`<div class="toast align-items-center text-white bg-${type} border-0 position-fixed bottom-0 end-0 m-3">
        <div class="d-flex"><div class="toast-body"><i class="bi bi-info-circle-fill me-2"></i>${msg}</div>
        <button class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div>`);
    $('body').append(t);
    const toast = new bootstrap.Toast(t[0]); toast.show();
    t.on('hidden.bs.toast',()=>t.remove());
  }
};

$(AttendanceApp.init.bind(AttendanceApp));
</script>
</body>
</html>