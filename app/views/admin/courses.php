<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مدیریت دروس - هنرستان امام صادق</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * {
            font-family: 'Vazir', sans-serif;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include 'app/views/partials/sidebar.php'; ?>
            
            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">مدیریت دروس</h1>
                </div>

                <?php if (isset($data['success'])): ?>
                    <div class="alert alert-success"><?php echo $data['success']; ?></div>
                <?php endif; ?>

                <?php if (isset($data['error'])): ?>
                    <div class="alert alert-danger"><?php echo $data['error']; ?></div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5>افزودن درس جدید</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label class="form-label">کد درس</label>
                                        <input type="text" name="course_code" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">نام درس</label>
                                        <input type="text" name="name" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">رشته</label>
                                        <select name="major_id" class="form-select" required>
                                            <option value="">انتخاب رشته</option>
                                            <?php foreach ($data['majors'] as $major): ?>
                                                <option value="<?php echo $major['id']; ?>">
                                                    <?php echo $major['name']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">پایه</label>
                                        <select name="grade_id" class="form-select" required>
                                            <option value="">انتخاب پایه</option>
                                            <?php foreach ($data['grades'] as $grade): ?>
                                                <option value="<?php echo $grade['id']; ?>">
                                                    <?php echo $grade['name']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">تعداد واحد</label>
                                        <input type="number" name="unit" class="form-control" min="1" max="4" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">نوع درس</label>
                                        <select name="course_type" class="form-select" required>
                                            <option value="poodmani">پودمانی</option>
                                            <option value="non_poodmani">غیر پودمانی</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary">ایجاد درس</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5>لیست دروس</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>کد درس</th>
                                                <th>نام درس</th>
                                                <th>رشته</th>
                                                <th>پایه</th>
                                                <th>واحد</th>
                                                <th>نوع</th>
                                                <th>تاریخ ایجاد</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($data['courses'] as $course): ?>
                                                <tr>
                                                    <td><?php echo $course['course_code']; ?></td>
                                                    <td><?php echo $course['name']; ?></td>
                                                    <td><?php echo $course['major_name']; ?></td>
                                                    <td><?php echo $course['grade_name']; ?></td>
                                                    <td><?php echo $course['unit']; ?></td>
                                                    <td>
                                                        <?php 
                                                            echo $course['course_type'] == 'poodmani' 
                                                                ? 'پودمانی' 
                                                                : 'غیر پودمانی'; 
                                                        ?>
                                                    </td>
                                                    <td><?php echo $course['created_at']; ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
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