<?php
// بدء الجلسة (Session) فقط إذا لم تكن قد بدأت مسبقاً
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// إعداد بيانات الاتصال بقاعدة البيانات (ثوابت ثابتة لا تتغير)
define('DB_SERVER', 'localhost');   // اسم السيرفر (هنا محلي = localhost)
define('DB_USERNAME', 'root');      // اسم المستخدم لقاعدة البيانات
define('DB_PASSWORD', '');          // كلمة المرور (فارغة افتراضياً في XAMPP)
define('DB_NAME', 'simple_expenses_db'); // اسم قاعدة البيانات

// محاولة الاتصال بقاعدة البيانات باستخدام mysqli
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// التحقق من نجاح الاتصال بقاعدة البيانات
if($conn === false){
    die("خطأ في الاتصال: " . mysqli_connect_error()); // إيقاف التنفيذ مع رسالة خطأ
}

// تعيين الترميز UTF-8 لدعم اللغة العربية في التعامل مع البيانات
mysqli_set_charset($conn, "utf8");

// دالة للتحقق من تسجيل دخول المستخدم
function checkLogin() {
    if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true){
        // إذا لم يكن المستخدم مسجل دخول → إعادة توجيهه إلى صفحة تسجيل الدخول
        header("location: login.php");
        exit;
    }
}

// دالة تسجيل الدخول (بسيطة للتحقق فقط بدون قاعدة بيانات للمستخدمين)
function login($username, $password) {
    global $conn;
    
    // التحقق من اسم المستخدم وكلمة المرور بشكل ثابت (admin/admin123)
    if($username === "admin" && $password === "admin123"){
        // إذا كانت البيانات صحيحة → تخزين بيانات الجلسة (Session variables)
        $_SESSION["loggedin"] = true;
        $_SESSION["id"] = 1;  // ID افتراضي (لأننا لم نربط بجدول مستخدمين حقيقي)
        $_SESSION["username"] = $username;
        return true; // نجاح تسجيل الدخول
    } else {
        return false; // فشل تسجيل الدخول
    }
}

// دالة للحصول على إحصائيات النظام (الرصيد، الدخل، المصروفات...)
function getSystemStats() {
    global $conn;
    
    $stats = array(); // مصفوفة لتخزين النتائج

    // الحصول على الرصيد الكلي (مجموع كل العمليات)
    $sql = "SELECT SUM(amount) as total FROM transactions";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $stats['total_balance'] = $row['total'] ? $row['total'] : 0;
    
    // الحصول على إجمالي الدخل (كل المبالغ > 0)
    $sql = "SELECT SUM(amount) as total FROM transactions WHERE amount > 0";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $stats['total_income'] = $row['total'] ? $row['total'] : 0;
    
    // الحصول على إجمالي المصروفات (كل المبالغ < 0)
    $sql = "SELECT SUM(amount) as total FROM transactions WHERE amount < 0";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    // نستخدم abs() لإرجاع القيمة الموجبة فقط للمصروفات
    $stats['total_expenses'] = $row['total'] ? abs($row['total']) : 0;
    
    // الحصول على المصروفات موزعة حسب الفئات
    $sql = "SELECT category, SUM(amount) as total 
            FROM transactions 
            WHERE amount < 0 
            GROUP BY category";
    $result = mysqli_query($conn, $sql);
    $stats['expenses_by_category'] = array();
    while($row = mysqli_fetch_assoc($result)) {
        // تخزين كل فئة مع مجموع مصروفاتها (موجب باستخدام abs)
        $stats['expenses_by_category'][$row['category']] = abs($row['total']);
    }
    
    return $stats; // إرجاع المصفوفة كاملة
}
?>
