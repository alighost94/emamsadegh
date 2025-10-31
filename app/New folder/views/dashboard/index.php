<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>داشبورد - هنرستان امام صادق</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * {
            font-family: 'Vazir', sans-serif;
        }
        
        .sidebar {
            background: #2c3e50;
            color: white;
            height: 100vh;
            position: fixed;
            width: 250px;
        }
        
        .sidebar .nav-link {
            color: #bdc3c7;
            padding: 15px 20px;
            border-bottom: 1px solid #34495e;
        }
        
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: white;
            background: #34495e;
        }
        
        .main-content {
            margin-right: 250px;
            padding: 20px;
            background: #f8f9fa;
            min-height: 100vh;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 30px 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            margin-bottom: 25px;
            border: none;
            transition: all 0.3s ease;
            text-align: center;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .stat-card i {
            font-size: 2.8rem;
            margin-bottom: 20px;
            opacity: 0.9;
        }
        
        .stat-card h4 {
            font-size: 2.2rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .stat-card p {
            color: #6c757d;
            font-size: 0.95rem;
            margin: 0;
        }
        
        .welcome-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 20px;
            padding: 35px 30px;
            margin-bottom: 35px;
            border: none;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
        }
        
        .today-teachers-card {
            border-right: 5px solid #28a745;
        }
        
        .today-teachers-card i {
            color: #28a745;
        }
        
        .majors-card i {
            color: #6f42c1;
        }
        
        .students-card i {
            color: #e83e8c;
        }
        
        .teachers-card i {
            color: #fd7e14;
        }
        
        .teacher-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #667eea;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        .week-day-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            cursor: pointer;
            height: 100%;
        }
        
        .week-day-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .week-day-card.today {
            border: 3px solid #28a745;
            background: linear-gradient(135deg, #f8fff8 0%, #e8f5e8 100%);
        }
        
        .activity-item {
            border-bottom: 1px solid #f0f0f0;
            padding: 15px 0;
            transition: background-color 0.2s ease;
            animation: fadeIn 0.5s ease-in;
        }
        
        .activity-item:hover {
            background-color: #f8f9fa;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-new {
            background-color: #f0f8ff;
            border-right: 4px solid #007bff;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .live-badge {
            background: #dc3545;
            color: white;
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 0.75rem;
            animation: pulse 2s infinite;
        }
        
        .connection-status {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
        }
        
        .connection-connected {
            background: #d4edda;
            color: #155724;
        }
        
        .connection-disconnected {
            background: #f8d7da;
            color: #721c24;
        }
        
        .connection-connecting {
            background: #fff3cd;
            color: #856404;
        }
        
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.7; }
            100% { opacity: 1; }
        }
        
        .col-lg-2-4 {
            flex: 0 0 auto;
            width: 20%;
        }
        
        @media (max-width: 1200px) {
            .col-lg-2-4 {
                width: 25%;
            }
        }
        
        @media (max-width: 992px) {
            .col-lg-2-4 {
                width: 33.333%;
            }
        }
        
        @media (max-width: 768px) {
            .col-lg-2-4 {
                width: 50%;
            }
        }
        
        @media (max-width: 576px) {
            .col-lg-2-4 {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="p-4 text-center border-bottom">
            <h4>هنرستان امام صادق</h4>
            <small>سیستم مدیریت</small>
        </div>
        
        <nav class="nav flex-column">
            <a class="nav-link active" href="<?php echo BASE_URL; ?>dashboard">
                <i class="bi bi-speedometer2"></i> داشبورد
            </a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>admin/users">
                <i class="bi bi-people"></i> مدیریت کاربران
            </a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>admin/majors">
                <i class="bi bi-book"></i> مدیریت رشته‌ها
            </a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>admin/courses">
                <i class="bi bi-journal-text"></i> مدیریت دروس
            </a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>admin/classes">
                <i class="bi bi-house-door"></i> مدیریت کلاس‌ها
            </a>
            <a class="nav-link" href="<?php echo BASE_URL; ?>auth/logout">
                <i class="bi bi-box-arrow-left"></i> خروج
            </a>
        </nav>
    </div>
    
    <div class="main-content">
        <!-- کارت خوش آمدگویی -->
        <div class="welcome-card">
            <h3>سلام، <?php echo $data['user_name']; ?>!</h3>
            <p>به پنل مدیریت هنرستان خوش آمدید</p>
            <small>امروز: <?php echo $data['today_date']; ?></small>
            <?php if (in_array($data['today_persian'], ['پنجشنبه', 'جمعه'])): ?>
                <div class="mt-2 p-2 bg-white bg-opacity-20 rounded">
                    <i class="bi bi-info-circle"></i>
                    امروز روز تعطیل هنرستان است
                </div>
            <?php else: ?>
                <div class="mt-2 p-2 bg-white bg-opacity-20 rounded">
                    <i class="bi bi-check-circle"></i>
                    امروز روز کاری هنرستان است
                </div>
            <?php endif; ?>
        </div>
        
        <!-- کارت‌های آماری -->
        <div class="row stats-row">
            <div class="col-md-3">
                <div class="stat-card majors-card">
                    <i class="bi bi-book"></i>
                    <h4><?php echo $data['majors_count']; ?></h4>
                    <p>تعداد رشته‌ها</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card students-card">
                    <i class="bi bi-people"></i>
                    <h4><?php echo $data['students_count']; ?></h4>
                    <p>تعداد دانش‌آموزان</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card teachers-card">
                    <i class="bi bi-person-badge"></i>
                    <h4><?php echo $data['teachers_count']; ?></h4>
                    <p>تعداد معلمان</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card today-teachers-card">
                    <i class="bi bi-calendar-check"></i>
                    <h4><?php echo count($data['today_teachers']); ?></h4>
                    <p>معلمان حاضر امروز</p>
                </div>
            </div>
        </div>
        
        <!-- بخش برنامه هفتگی معلمان -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0 text-dark">
                            <i class="bi bi-calendar-week text-primary me-2"></i>
                            برنامه هفتگی حضور معلمان
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php 
                            $today_english = null;
                            $persian_days_map = [
                                'saturday' => 'شنبه',
                                'sunday' => 'یکشنبه', 
                                'monday' => 'دوشنبه',
                                'tuesday' => 'سه‌شنبه',
                                'wednesday' => 'چهارشنبه'
                            ];
                            
                            foreach ($persian_days_map as $en => $fa) {
                                if ($fa === $data['today_persian']) {
                                    $today_english = $en;
                                    break;
                                }
                            }
                            
                            foreach ($data['weekly_presence'] as $day_en => $day_data): 
                                $is_today = ($day_en === $today_english);
                                $teacher_count = count($day_data['teachers']);
                            ?>
                            <div class="col-lg-2-4 mb-4">
                                <div class="card week-day-card h-100 <?php echo $is_today ? 'today' : ''; ?>" 
                                     data-bs-toggle="modal" 
                                     data-bs-target="#dayModal"
                                     data-day="<?php echo $day_data['fa_name']; ?>"
                                     data-teachers='<?php echo json_encode($day_data['teachers']); ?>'>
                                    <div class="card-body text-center p-4">
                                        <h5 class="card-title <?php echo $is_today ? 'text-success' : 'text-dark'; ?> mb-3">
                                            <?php if ($is_today): ?>
                                                <i class="bi bi-star-fill text-warning me-2"></i>
                                            <?php endif; ?>
                                            <?php echo $day_data['fa_name']; ?>
                                        </h5>
                                        <div class="day-content">
                                            <div class="teacher-count display-4 fw-bold text-primary mb-2">
                                                <?php echo $teacher_count; ?>
                                            </div>
                                            <p class="text-muted mb-0">معلم</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            
                            <!-- روزهای تعطیل -->
                            <div class="col-lg-2-4 mb-4">
                                <div class="card week-day-card h-100 bg-light">
                                    <div class="card-body text-center p-4">
                                        <h5 class="card-title text-muted mb-3">پنجشنبه</h5>
                                        <div class="day-content">
                                            <div class="teacher-count display-4 fw-bold text-muted mb-2">
                                                ۰
                                            </div>
                                            <p class="text-muted mb-0">تعطیل</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-2-4 mb-4">
                                <div class="card week-day-card h-100 bg-light">
                                    <div class="card-body text-center p-4">
                                        <h5 class="card-title text-muted mb-3">جمعه</h5>
                                        <div class="day-content">
                                            <div class="teacher-count display-4 fw-bold text-muted mb-2">
                                                ۰
                                            </div>
                                            <p class="text-muted mb-0">تعطیل</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- بخش آخرین فعالیت‌ها -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-dark">
                            <i class="bi bi-activity me-2"></i>
                            آخرین فعالیت‌های سیستم
                            <span class="live-badge ms-2">LIVE</span>
                        </h5>
                        <div>
                            <span class="connection-status connection-connecting me-2" id="connectionStatus">
                                <i class="bi bi-wifi"></i> در حال اتصال...
                            </span>
                            <span class="text-muted small" id="lastUpdate">
                                آخرین بروزرسانی: همین الان
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div id="activitiesContainer" style="max-height: 600px; overflow-y: auto;">
                            <?php if (!empty($data['recent_activities'])): ?>
                                <?php foreach ($data['recent_activities'] as $index => $activity): ?>
                                    <div class="activity-item <?php echo $index < 3 ? 'activity-new' : ''; ?>" id="activity-<?php echo $activity['id']; ?>">
                                        <div class="d-flex align-items-start px-4">
                                            <div class="me-3 mt-1">
                                                <?php 
                                                $icon = 'bi-person';
                                                $color = 'primary';
                                                switch ($activity['user_type']) {
                                                    case 'teacher':
                                                        $icon = 'bi-person-badge';
                                                        $color = 'warning';
                                                        break;
                                                    case 'assistant':
                                                        $icon = 'bi-shield-check';
                                                        $color = 'info';
                                                        break;
                                                    case 'student':
                                                        $icon = 'bi-person';
                                                        $color = 'success';
                                                        break;
                                                    case 'admin':
                                                        $icon = 'bi-gear';
                                                        $color = 'danger';
                                                        break;
                                                }
                                                ?>
                                                <i class="bi <?php echo $icon; ?> text-<?php echo $color; ?> fs-5"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <p class="mb-1"><?php echo $activity['description']; ?></p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted">
                                                        <i class="bi bi-person me-1"></i>
                                                        <?php echo $activity['first_name'] . ' ' . $activity['last_name']; ?>
                                                        <span class="badge bg-<?php echo $color; ?> bg-opacity-20 text-<?php echo $color; ?> ms-2">
                                                            <?php echo $activity['role_name']; ?>
                                                        </span>
                                                    </small>
                                                    <small class="text-muted" id="time-<?php echo $activity['id']; ?>">
                                                        <?php 
                                                        $created_at = new DateTime($activity['created_at']);
                                                        $now = new DateTime();
                                                        $diff = $now->diff($created_at);
                                                        
                                                        if ($diff->d > 0) {
                                                            echo $diff->d . ' روز پیش';
                                                        } elseif ($diff->h > 0) {
                                                            echo $diff->h . ' ساعت پیش';
                                                        } elseif ($diff->i > 0) {
                                                            echo $diff->i . ' دقیقه پیش';
                                                        } else {
                                                            echo 'همین الان';
                                                        }
                                                        ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="bi bi-activity text-muted" style="font-size: 4rem;"></i>
                                    <p class="text-muted mt-3">هیچ فعالیتی ثبت نشده است</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- مودال نمایش لیست معلمان -->
    <div class="modal fade" id="dayModal" tabindex="-1" aria-labelledby="dayModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dayModalLabel">لیست معلمان</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="modalDayName" class="text-center mb-3">
                        <h6 class="text-primary"></h6>
                    </div>
                    <div id="modalTeachersList">
                        <!-- لیست معلمان اینجا نمایش داده می‌شود -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">بستن</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
        class ActivityPoller {
            constructor() {
                this.lastActivityId = <?php echo !empty($data['recent_activities']) ? $data['recent_activities'][0]['id'] : 0; ?>;
                this.isPolling = true;
                this.pollInterval = null;
                this.retryCount = 0;
                this.maxRetries = 3;
                this.pollingDelay = 5000; // 5 ثانیه
                
                this.init();
            }
            
            init() {
                this.startPolling();
                
                // وقتی تب فعال میشه polling رو شروع کن
                document.addEventListener('visibilitychange', () => {
                    if (document.visibilityState === 'visible') {
                        if (!this.isPolling) {
                            this.startPolling();
                        }
                        // وقتی تب فعال شد، فوری چک کن
                        this.checkActivities();
                    } else {
                        this.stopPolling();
                    }
                });
            }
            
            startPolling() {
                if (this.isPolling) return;
                
                this.isPolling = true;
                this.updateConnectionStatus('connected', 'در حال بروزرسانی...');
                
                // شروع polling با interval
                this.pollInterval = setInterval(() => {
                    this.checkActivities();
                }, this.pollingDelay);
                
                console.log('Polling started');
            }
            
            stopPolling() {
                this.isPolling = false;
                if (this.pollInterval) {
                    clearInterval(this.pollInterval);
                    this.pollInterval = null;
                }
                this.updateConnectionStatus('disconnected', 'متوقف شده');
                console.log('Polling stopped');
            }
            
            async checkActivities() {
                if (!this.isPolling) return;
                
                try {
                    const response = await fetch(`<?php echo BASE_URL; ?>polling/getNewActivities?last_id=${this.lastActivityId}&t=${Date.now()}`);
                    
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        this.retryCount = 0; // reset retry count
                        this.updateConnectionStatus('connected', 'متصل');
                        
                        if (data.activities && data.activities.length > 0) {
                            this.handleNewActivities(data.activities);
                        }
                        
                        this.updateLastUpdateTime();
                    } else {
                        throw new Error(data.message || 'Server error');
                    }
                    
                } catch (error) {
                    console.error('Polling error:', error);
                    this.retryCount++;
                    this.updateConnectionStatus('disconnected', 'خطا در اتصال');
                    
                    if (this.retryCount >= this.maxRetries) {
                        console.log('Max retries reached. Waiting before retry...');
                        // بعد از 3 خطا، 30 ثانیه صبر کن
                        this.stopPolling();
                        setTimeout(() => {
                            this.retryCount = 0;
                            this.startPolling();
                        }, 30000);
                    }
                }
            }
            
            handleNewActivities(activities) {
                if (activities.length > 0) {
                    // فعالیت‌ها رو به ترتیب معکوس اضافه کن (جدیدترین اول)
                    activities.reverse().forEach(activity => {
                        this.addNewActivity(activity);
                    });
                    
                    // آپدیت last_id به جدیدترین فعالیت
                    this.lastActivityId = activities[activities.length - 1].id;
                    
                    // نمایش نوتیفیکیشن
                    this.showNotification(activities.length);
                    
                    console.log(`Added ${activities.length} new activities`);
                }
            }
            
            addNewActivity(activity) {
                const container = document.getElementById('activitiesContainer');
                
                // اگر فعالیت از قبل وجود دارد، اضافه نکن
                if (document.getElementById(`activity-${activity.id}`)) {
                    return;
                }
                
                const activityElement = this.createActivityElement(activity);
                
                // اضافه کردن به ابتدای لیست
                if (container.firstChild) {
                    container.insertBefore(activityElement, container.firstChild);
                } else {
                    container.appendChild(activityElement);
                }
                
                // حذف فعالیت‌های قدیمی اگر تعداد زیاد شد
                const allActivities = container.getElementsByClassName('activity-item');
                if (allActivities.length > 50) {
                    container.removeChild(allActivities[allActivities.length - 1]);
                }
                
                // انیمیشن برای فعالیت جدید
                this.animateNewActivity(activityElement);
            }
            
            createActivityElement(activity) {
                const iconInfo = this.getUserIcon(activity.user_type);
                const timeText = this.getRelativeTime(activity.created_at);
                
                const activityDiv = document.createElement('div');
                activityDiv.className = 'activity-item';
                activityDiv.id = `activity-${activity.id}`;
                
                activityDiv.innerHTML = `
                    <div class="d-flex align-items-start px-4">
                        <div class="me-3 mt-1">
                            <i class="bi ${iconInfo.icon} text-${iconInfo.color} fs-5"></i>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-1">${activity.description}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="bi bi-person me-1"></i>
                                    ${activity.first_name} ${activity.last_name}
                                    <span class="badge bg-${iconInfo.color} bg-opacity-20 text-${iconInfo.color} ms-2">
                                        ${activity.role_name}
                                    </span>
                                </small>
                                <small class="text-muted" id="time-${activity.id}">
                                    ${timeText}
                                </small>
                            </div>
                        </div>
                    </div>
                `;
                
                return activityDiv;
            }
            
            animateNewActivity(element) {
                // اضافه کردن کلاس انیمیشن
                element.classList.add('activity-new');
                
                // حذف کلاس انیمیشن بعد از 5 ثانیه
                setTimeout(() => {
                    element.classList.remove('activity-new');
                }, 5000);
            }
            
            getUserIcon(userType) {
                switch (userType) {
                    case 'teacher': return { icon: 'bi-person-badge', color: 'warning' };
                    case 'assistant': return { icon: 'bi-shield-check', color: 'info' };
                    case 'student': return { icon: 'bi-person', color: 'success' };
                    case 'admin': return { icon: 'bi-gear', color: 'danger' };
                    default: return { icon: 'bi-person', color: 'primary' };
                }
            }
            
            getRelativeTime(dateString) {
                const now = new Date();
                const date = new Date(dateString);
                const diffMs = now - date;
                const diffMins = Math.floor(diffMs / 60000);
                const diffHours = Math.floor(diffMs / 3600000);
                const diffDays = Math.floor(diffMs / 86400000);
                
                if (diffDays > 0) return `${diffDays} روز پیش`;
                if (diffHours > 0) return `${diffHours} ساعت پیش`;
                if (diffMins > 0) return `${diffMins} دقیقه پیش`;
                return 'همین الان';
            }
            
            updateConnectionStatus(status, text) {
                const statusElement = document.getElementById('connectionStatus');
                if (statusElement) {
                    statusElement.className = `connection-status connection-${status}`;
                    statusElement.innerHTML = `<i class="bi bi-wifi"></i> ${text}`;
                }
            }
            
            updateLastUpdateTime() {
                const lastUpdateElement = document.getElementById('lastUpdate');
                if (lastUpdateElement) {
                    lastUpdateElement.textContent = 
                        `آخرین بروزرسانی: ${new Date().toLocaleTimeString('fa-IR')}`;
                }
            }
            
            showNotification(count) {
                if (count > 0 && document.visibilityState === 'visible') {
                    this.showToast(`${count} فعالیت جدید ثبت شد`, 'success');
                }
            }
            
            showToast(message, type = 'info') {
                // ایجاد یک toast ساده
                const toast = document.createElement('div');
                toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
                toast.style.cssText = 'top: 20px; left: 20px; z-index: 1050; min-width: 300px;';
                toast.innerHTML = `
                    <i class="bi bi-bell me-2"></i>
                    <strong>${message}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                
                document.body.appendChild(toast);
                
                // حذف خودکار بعد از 5 ثانیه
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.remove();
                    }
                }, 5000);
            }
        }

        // راه‌اندازی سیستم وقتی DOM لود شد
        document.addEventListener('DOMContentLoaded', function() {
            // راه‌اندازی سیستم polling
            const activityPoller = new ActivityPoller();
            
            // دکمه بروزرسانی دستی
            window.refreshActivities = function() {
                activityPoller.checkActivities();
                activityPoller.updateConnectionStatus('connected', 'در حال بروزرسانی...');
            };
            
            // فعال کردن مودال برای نمایش لیست معلمان
            const dayModal = document.getElementById('dayModal');
            const modalDayName = document.getElementById('modalDayName');
            const modalTeachersList = document.getElementById('modalTeachersList');
            
            if (dayModal) {
                dayModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const dayName = button.getAttribute('data-day');
                    const teachers = JSON.parse(button.getAttribute('data-teachers'));
                    
                    modalDayName.innerHTML = `<h6 class="text-primary">معلمان روز ${dayName}</h6>`;
                    
                    if (teachers.length > 0) {
                        let teachersHtml = '';
                        teachers.forEach(teacher => {
                            teachersHtml += `
                                <div class="modal-teacher-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="teacher-avatar me-3">
                                            ${teacher.name.split(' ').map(n => n[0]).join('')}
                                        </div>
                                        <div>
                                            <h6 class="mb-0">${teacher.name}</h6>
                                            <small class="text-muted">${teacher.mobile}</small>
                                        </div>
                                    </div>
                                    <span class="badge bg-primary bg-opacity-20 text-primary">
                                        <i class="bi bi-person-check"></i>
                                    </span>
                                </div>
                            `;
                        });
                        modalTeachersList.innerHTML = teachersHtml;
                    } else {
                        modalTeachersList.innerHTML = `
                            <div class="text-center py-4">
                                <i class="bi bi-person-x text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-2">هیچ معلمی برای این روز برنامه‌ریزی نشده است</p>
                            </div>
                        `;
                    }
                });
            }
            
            // تمیزکاری وقتی صفحه بسته می‌شود
            window.addEventListener('beforeunload', () => {
                activityPoller.stopPolling();
            });
        });
    </script>
</body>
</html>