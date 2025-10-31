<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تمامی رکوردها - پنل مدیر</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { font-family: 'Vazir', sans-serif; }
        .encouragement-row { background-color: #d4edda !important; }
        .disciplinary-row { background-color: #f8d7da !important; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- سایدبار -->
            <?php include 'app/views/partials/sidebar.php'; ?>
            
            <!-- محتوای اصلی -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4" style="margin-right: 250px; padding: 20px;">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">تمامی رکوردهای همکاران</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="<?php echo BASE_URL; ?>admin/staffFiles" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-right"></i> بازگشت به پرونده همکاران
                        </a>
                    </div>
                </div>

                <!-- فیلترها -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">فیلترها</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">نوع همکار</label>
                                <select name="staff_type" class="form-select">
                                    <option value="">همه</option>
                                    <option value="teacher" <?php echo $data['filters']['staff_type'] == 'teacher' ? 'selected' : ''; ?>>معلم</option>
                                    <option value="assistant" <?php echo $data['filters']['staff_type'] == 'assistant' ? 'selected' : ''; ?>>معاون</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">نوع رکورد</label>
                                <select name="record_type" class="form-select">
                                    <option value="">همه</option>
                                    <option value="encouragement" <?php echo $data['filters']['record_type'] == 'encouragement' ? 'selected' : ''; ?>>تشویقی</option>
                                    <option value="disciplinary" <?php echo $data['filters']['record_type'] == 'disciplinary' ? 'selected' : ''; ?>>انضباطی</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">از تاریخ</label>
                                <input type="date" name="start_date" class="form-control" 
                                       value="<?php echo $data['filters']['start_date']; ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">تا تاریخ</label>
                                <input type="date" name="end_date" class="form-control" 
                                       value="<?php echo $data['filters']['end_date']; ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">وضعیت</label>
                                <select name="status" class="form-select">
                                    <option value="">همه</option>
                                    <option value="pending" <?php echo $data['filters']['status'] == 'pending' ? 'selected' : ''; ?>>در انتظار</option>
                                    <option value="approved" <?php echo $data['filters']['status'] == 'approved' ? 'selected' : ''; ?>>تأیید شده</option>
                                    <option value="rejected" <?php echo $data['filters']['status'] == 'rejected' ? 'selected' : ''; ?>>رد شده</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-filter"></i> اعمال فیلتر
                                </button>
                                <a href="<?php echo BASE_URL; ?>admin/staffRecords" class="btn btn-secondary">
                                    <i class="bi bi-arrow-clockwise"></i> بازنشانی
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- لیست رکوردها -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">لیست رکوردها</h5>
                        <span class="badge bg-primary"><?php echo count($data['records']); ?> رکورد</span>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($data['records'])): ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>همکار</th>
                                            <th>نوع</th>
                                            <th>تاریخ</th>
                                            <th>عنوان</th>
                                            <th>امتیاز</th>
                                            <th>ثبت کننده</th>
                                            <th>وضعیت</th>
                                            <th>عملیات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data['records'] as $record): ?>
                                            <tr class="<?php echo $record['record_type'] == 'encouragement' ? 'encouragement-row' : 'disciplinary-row'; ?>">
                                                <td>
                                                    <strong><?php echo $record['staff_first_name'] . ' ' . $record['staff_last_name']; ?></strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        <?php echo $record['staff_type'] == 'teacher' ? 'معلم' : 'معاون'; ?>
                                                    </small>
                                                    <br>
                                                    <small class="text-muted"><?php echo $record['staff_mobile']; ?></small>
                                                </td>
                                                <td>
                                                    <?php if ($record['record_type'] == 'encouragement'): ?>
                                                        <span class="badge bg-success">تشویقی</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">انضباطی</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <small><?php echo $record['jalali_date']; ?></small>
                                                </td>
                                                <td>
                                                    <strong><?php echo $record['title']; ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?php echo substr($record['description'], 0, 80); ?>...</small>
                                                </td>
                                                <td>
                                                    <?php if ($record['record_type'] == 'encouragement'): ?>
                                                        <span class="text-success">+<?php echo $record['points']; ?></span>
                                                    <?php else: ?>
                                                        <span class="text-danger">-<?php echo $record['points']; ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <small><?php echo $record['created_first_name'] . ' ' . $record['created_last_name']; ?></small>
                                                </td>
                                                <td>
                                                    <?php if ($record['status'] == 'approved'): ?>
                                                        <span class="badge bg-success">تأیید شده</span>
                                                    <?php elseif ($record['status'] == 'rejected'): ?>
                                                        <span class="badge bg-danger">رد شده</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning text-dark">در انتظار</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="<?php echo BASE_URL; ?>admin/staffDetail/<?php echo $record['staff_type']; ?>/<?php echo $record['staff_id']; ?>" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info text-center">
                                <i class="bi bi-info-circle"></i>
                                هیچ رکوردی یافت نشد.
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