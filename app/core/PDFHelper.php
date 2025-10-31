<?php
class PDFHelper extends \TCPDF {
    
    public function __construct() {
        parent::__construct('P', 'mm', 'A4', true, 'vazir', true);
        
        $this->SetCreator('نظم نو');
        $this->SetAuthor('سیستم مدیریت هنرستان');
        $this->SetTitle('لیست دانش‌آموزان');
        $this->SetSubject('لیست دانش‌آموزان');
        $this->SetKeywords('دانش‌آموز, هنرستان, لیست, نظم نو');
        
        // تنظیمات حاشیه بهینه
        $this->SetMargins(10, 22, 10);
        $this->SetHeaderMargin(6);
        $this->SetFooterMargin(8);
        
        $this->SetAutoPageBreak(TRUE, 12);
        $this->SetFont('vazir', '', 9);
        
        ob_start();
    }
    
    // هدر صفحه - با فاصله مناسب
    public function Header() {
        // عنوان اصلی
        $this->SetY(10);
        $this->SetFont('vazir', 'B', 15);
        $this->SetTextColor(58, 83, 155);
        $this->Cell(0, 6, 'سیستم مدیریتی نظم نو', 0, 1, 'C');
        
        // عنوان فرعی
        $this->SetFont('vazir', '', 11);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(0, 5, 'هنرستان نمونه امام صادق (ع)', 0, 1, 'C');
        
        // خط جداکننده با فاصله مناسب
        $this->SetLineWidth(0.5);
        $this->SetDrawColor(58, 83, 155);
        $this->Line(10, 22, 200, 22); // خط دقیقاً زیر عناوین
        
        $this->Ln(3);
    }
    
    // فوتر صفحه
    public function Footer() {
        $this->SetY(-12);
        $this->SetFont('vazir', 'I', 8);
        $this->SetTextColor(128, 128, 128);
        
        $footer_text = 'صفحه ' . $this->getAliasNumPage() . ' از ' . $this->getAliasNbPages() . 
                      ' | استخراج: ' . JalaliDate::now('Y/m/d') . ' | نظم نو v1.2';
        $this->Cell(0, 4, $footer_text, 0, 0, 'C');
        
        // خط جداکننده فوتر
        $this->SetLineWidth(0.3);
        $this->SetDrawColor(150, 150, 150);
        $this->Line(10, 278, 200, 278);
    }
    
    // ایجاد جدول دانش‌آموزان با سایز کمی بزرگتر
    public function createStudentsTable($header, $data, $class_info) {
        // اطلاعات کلاس - کمی بزرگتر
        $this->SetFont('vazir', 'B', 11);
        $this->SetTextColor(58, 83, 155);
        $this->Cell(0, 5, 'لیست دانش‌آموزان کلاس ' . $class_info['name'], 0, 1, 'R');
        
        $this->SetFont('vazir', '', 10);
        $this->SetTextColor(0, 0, 0);
        
        $class_info_text = 'پایه: ' . $class_info['grade_name'] . 
                          ' | رشته: ' . $class_info['major_name'] . 
                          ' | تعداد: ' . $this->convertToPersianNumbers($class_info['student_count']) . ' نفر';
        $this->Cell(0, 4, $class_info_text, 0, 1, 'R');
        $this->Ln(3);
        
        // هدر جدول - کمی بزرگتر
        $this->SetFillColor(58, 83, 155);
        $this->SetTextColor(255);
        $this->SetDrawColor(80, 80, 80);
        $this->SetLineWidth(0.3);
        $this->SetFont('vazir', 'B', 9);
        
        // عرض ستون‌ها - بهینه شده
        $w = array(8, 25, 68, 48, 37); // ردیف، شماره دانش‌آموزی، نام و نام خانوادگی، نام پدر، توضیحات
        
        // هدر جدول
        $this->Cell($w[4], 6, $header[4], 'LTRB', 0, 'C', 1);
        $this->Cell($w[3], 6, $header[3], 'LTRB', 0, 'C', 1);
        $this->Cell($w[2], 6, $header[2], 'LTRB', 0, 'C', 1);
        $this->Cell($w[1], 6, $header[1], 'LTRB', 0, 'C', 1);
        $this->Cell($w[0], 6, $header[0], 'LTRB', 0, 'C', 1);
        $this->Ln();
        
        // داده‌ها - کمی بزرگتر
        $this->SetFillColor(245, 247, 255);
        $this->SetTextColor(0);
        $this->SetFont('vazir', '', 9);
        $this->SetDrawColor(100, 100, 100);
        $this->SetLineWidth(0.2);
        
        $fill = false;
        $counter = 1;
        
        foreach($data as $row) {
            // چک کردن ارتفاع صفحه
            if($this->GetY() + 6 > 275) {
                $this->AddPage();
                // هدر جدول در صفحه جدید
                $this->SetFillColor(58, 83, 155);
                $this->SetTextColor(255);
                $this->SetFont('vazir', 'B', 9);
                $this->SetDrawColor(80, 80, 80);
                $this->SetLineWidth(0.3);
                
                $this->Cell($w[4], 6, $header[4], 'LTRB', 0, 'C', 1);
                $this->Cell($w[3], 6, $header[3], 'LTRB', 0, 'C', 1);
                $this->Cell($w[2], 6, $header[2], 'LTRB', 0, 'C', 1);
                $this->Cell($w[1], 6, $header[1], 'LTRB', 0, 'C', 1);
                $this->Cell($w[0], 6, $header[0], 'LTRB', 0, 'C', 1);
                $this->Ln();
                
                $this->SetFillColor(245, 247, 255);
                $this->SetTextColor(0);
                $this->SetFont('vazir', '', 9);
                $this->SetDrawColor(100, 100, 100);
                $this->SetLineWidth(0.2);
                $fill = false;
            }
            
            // آماده کردن داده‌ها - تغییر: اول نام خانوادگی سپس نام
            $student_number = $this->convertToPersianNumbers($row['student_number'] ?? '-');
            $full_name = $row['last_name'] . ' ' . $row['first_name']; // تغییر: اول نام خانوادگی
            $father_name = $row['father_name'] ?? '-';
            $description = $row['description'] ?? '';
            
            // ردیف داده با ارتفاع کمی بیشتر
            $this->Cell($w[4], 6, $description, 'LTRB', 0, 'R', $fill);
            $this->Cell($w[3], 6, $father_name, 'LTRB', 0, 'R', $fill);
            $this->Cell($w[2], 6, $full_name, 'LTRB', 0, 'R', $fill);
            $this->Cell($w[1], 6, $student_number, 'LTRB', 0, 'C', $fill);
            $this->Cell($w[0], 6, $this->convertToPersianNumbers($counter), 'LTRB', 0, 'C', $fill);
            $this->Ln();
            
            $fill = !$fill;
            $counter++;
        }
        
        $this->Ln(2);
    }
    
    // تبدیل اعداد به فارسی
    private function convertToPersianNumbers($number) {
        $persian_numbers = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
        $english_numbers = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        return str_replace($english_numbers, $persian_numbers, (string)$number);
    }
    
    // اضافه کردن اطلاعات پایانی
    public function addFooterInfo($assistant_name) {
        $this->SetFont('vazir', 'I', 8);
        $this->SetTextColor(100, 100, 100);
        
        $signature_line = 'امضاء معاون: ' . $assistant_name . ' | تاریخ: ' . JalaliDate::now('Y/m/d');
        $this->Cell(0, 3, $signature_line, 0, 1, 'L');
    }
    
    // متد برای پاک کردن خروجی‌های قبلی
    public function cleanOutput() {
        if (ob_get_length()) {
            ob_end_clean();
        }
    }
}
?>