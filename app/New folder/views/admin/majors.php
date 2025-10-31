<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مدیریت رشته‌ها - هنرستان امام صادق</title>
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
                    <h1 class="h2">مدیریت رشته‌های تحصیلی</h1>
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
                                <h5>افزودن رشته جدید</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label class="form-label">نام رشته</label>
                                        <input type="text" name="name" class="form-control" required 
                                               placeholder="مثال: الکتروتکنیک">
                                    </div>
                                    <button type="submit" class="btn btn-primary">ایجاد رشته</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5>لیست رشته‌ها</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>نام رشته</th>
                                                <th>تاریخ ایجاد</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($data['majors'] as $index => $major): ?>
                                                <tr>
                                                    <td><?php echo $index + 1; ?></td>
                                                    <td><?php echo $major['name']; ?></td>
                                                    <td><?php echo $major['created_at']; ?></td>
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