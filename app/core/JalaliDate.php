<?php
require_once 'jdf.php';

class JalaliDate {
    
    /**
     * تبدیل اعداد فارسی به انگلیسی
     */
    public static function convertToEnglishNumbers($string) {
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        
        $string = str_replace($persian, $english, $string);
        $string = str_replace($arabic, $english, $string);
        
        return $string;
    }
    
    /**
     * تبدیل تاریخ میلادی به شمسی
     */
    public static function gregorianToJalali($gregorian_date, $format = 'Y/m/d') {
        if (empty($gregorian_date)) {
            return '';
        }
        
        $timestamp = strtotime($gregorian_date);
        if ($timestamp === false) {
            return $gregorian_date;
        }
        
        return jdate($format, $timestamp);
    }
    
    /**
     * تبدیل تاریخ شمسی به میلادی
     */
    public static function jalaliToGregorian($jalali_date, $separator = '/') {
        if (empty($jalali_date)) {
            return '';
        }
        
        // تبدیل اعداد فارسی به انگلیسی
        $jalali_date = self::convertToEnglishNumbers($jalali_date);
        
        $parts = explode($separator, $jalali_date);
        if (count($parts) !== 3) {
            return $jalali_date;
        }
        
        list($year, $month, $day) = $parts;
        $gregorian = jalali_to_gregorian($year, $month, $day);
        
        return sprintf('%04d-%02d-%02d', $gregorian[0], $gregorian[1], $gregorian[2]);
    }
    
    /**
     * دریافت تاریخ شمسی فعلی
     */
    public static function now($format = 'Y/m/d') {
        return jdate($format);
    }
    
    /**
     * دریافت تاریخ و زمان شمسی فعلی
     */
    public static function nowWithTime($format = 'Y/m/d H:i:s') {
        return jdate($format);
    }
    
    /**
     * فرمت کردن تاریخ شمسی
     */
    public static function format($timestamp, $format = 'Y/m/d') {
        return jdate($format, $timestamp);
    }
    
    /**
     * بررسی معتبر بودن تاریخ شمسی
     */
    public static function isValidJalaliDate($year, $month, $day) {
        // تبدیل اعداد به انگلیسی
        $year = (int)self::convertToEnglishNumbers($year);
        $month = (int)self::convertToEnglishNumbers($month);
        $day = (int)self::convertToEnglishNumbers($day);
        
        if ($month < 1 || $month > 12 || $day < 1) {
            return false;
        }
        
        // بررسی تعداد روزهای ماه
        $days_in_month = self::getDaysInJalaliMonth($year, $month);
        return $day <= $days_in_month;
    }
    
    /**
     * دریافت تعداد روزهای یک ماه شمسی
     */
    public static function getDaysInJalaliMonth($year, $month) {
        // تبدیل اعداد به انگلیسی و سپس integer
        $year = (int)self::convertToEnglishNumbers($year);
        $month = (int)self::convertToEnglishNumbers($month);
        
        if ($month <= 6) {
            return 31;
        } elseif ($month <= 11) {
            return 30;
        } else {
            // اسفند - بررسی سال کبیسه
            return self::isLeapJalaliYear($year) ? 30 : 29;
        }
    }
    
    /**
     * بررسی سال کبیسه شمسی
     */
    public static function isLeapJalaliYear($year) {
        // تبدیل اعداد به انگلیسی و سپس integer
        $year = (int)self::convertToEnglishNumbers($year);
        $a = $year % 33;
        return $a == 1 || $a == 5 || $a == 9 || $a == 13 || $a == 17 || $a == 22 || $a == 26 || $a == 30;
    }
    
    /**
     * تولید لیست ماه‌های شمسی
     */
    public static function getJalaliMonths() {
        return [
            1 => 'فروردین',
            2 => 'اردیبهشت',
            3 => 'خرداد',
            4 => 'تیر',
            5 => 'مرداد',
            6 => 'شهریور',
            7 => 'مهر',
            8 => 'آبان',
            9 => 'آذر',
            10 => 'دی',
            11 => 'بهمن',
            12 => 'اسفند'
        ];
    }
    
    /**
     * تولید لیست سال‌های شمسی
     */
    public static function getJalaliYears($start = -5, $end = 5) {
        $current_year = (int)self::convertToEnglishNumbers(jdate('Y'));
        $years = [];
        
        for ($i = $start; $i <= $end; $i++) {
            $year = $current_year + $i;
            $years[$year] = $year;
        }
        
        return $years;
    }
    
    /**
     * محاسبه اختلاف بین دو تاریخ
     */
    public static function dateDiff($start_date, $end_date, $unit = 'days') {
        $start = strtotime($start_date);
        $end = strtotime($end_date);
        
        if ($start === false || $end === false) {
            return 0;
        }
        
        $diff = $end - $start;
        
        switch ($unit) {
            case 'seconds':
                return $diff;
            case 'minutes':
                return floor($diff / 60);
            case 'hours':
                return floor($diff / 3600);
            case 'days':
                return floor($diff / 86400);
            case 'weeks':
                return floor($diff / 604800);
            default:
                return $diff;
        }
    }
    
    /**
     * تولید روزهای ماه شمسی
     */
    public static function getJalaliMonthDays($year, $month) {
        // تبدیل اعداد به انگلیسی
        $year = (int)self::convertToEnglishNumbers($year);
        $month = (int)self::convertToEnglishNumbers($month);
        
        $days_in_month = self::getDaysInJalaliMonth($year, $month);
        $first_day_gregorian = jalali_to_gregorian($year, $month, 1);
        $first_day_timestamp = strtotime("{$first_day_gregorian[0]}-{$first_day_gregorian[1]}-{$first_day_gregorian[2]}");
        $first_day_of_week = date('N', $first_day_timestamp);
        
        // تبدیل به شمسی برای محاسبه روز هفته (جمعه=0, شنبه=1, ...)
        $first_day_jalali_week = ($first_day_of_week + 1) % 7;
        
        $days = [];
        
        // روزهای خالی قبل از شروع ماه
        for ($i = 0; $i < $first_day_jalali_week; $i++) {
            $days[] = null;
        }
        
        // روزهای ماه
        for ($day = 1; $day <= $days_in_month; $day++) {
            $gregorian_date = jalali_to_gregorian($year, $month, $day);
            $gregorian_date_str = sprintf('%04d-%02d-%02d', $gregorian_date[0], $gregorian_date[1], $gregorian_date[2]);
            $days[] = [
                'jalali_day' => $day,
                'gregorian_date' => $gregorian_date_str,
                'day_of_week' => ($first_day_jalali_week + $day - 1) % 7
            ];
        }
        
        return $days;
    }
    
    /**
     * دریافت اطلاعات کامل ماه شمسی
     */
    public static function getJalaliMonthCalendar($year, $month) {
        // تبدیل اعداد به انگلیسی
        $year = (int)self::convertToEnglishNumbers($year);
        $month = (int)self::convertToEnglishNumbers($month);
        
        $days = self::getJalaliMonthDays($year, $month);
        $weeks = [];
        $current_week = [];
        
        foreach ($days as $day) {
            $current_week[] = $day;
            
            if (count($current_week) === 7) {
                $weeks[] = $current_week;
                $current_week = [];
            }
        }
        
        // اضافه کردن هفته آخر اگر کامل نبود
        if (!empty($current_week)) {
            while (count($current_week) < 7) {
                $current_week[] = null;
            }
            $weeks[] = $current_week;
        }
        
        return [
            'year' => $year,
            'month' => $month,
            'month_name' => self::getJalaliMonths()[$month],
            'weeks' => $weeks
        ];
    }
    
    /**
     * تجزیه تاریخ شمسی به اجزاء
     */
    public static function parseJalaliDate($jalali_date, $separator = '/') {
        $jalali_date = self::convertToEnglishNumbers($jalali_date);
        $parts = explode($separator, $jalali_date);
        
        if (count($parts) !== 3) {
            return null;
        }
        
        return [
            'year' => (int)$parts[0],
            'month' => (int)$parts[1],
            'day' => (int)$parts[2]
        ];
    }
}
?>