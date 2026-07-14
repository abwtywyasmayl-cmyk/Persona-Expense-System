<?php
// استدعاء ملف الإعدادات (config.php) الذي يحتوي على الاتصال بقاعدة البيانات ودوال المساعدة
include "config.php";

// التحقق من تسجيل دخول المستخدم، إذا لم يكن مسجلاً سيتم تحويله لصفحة تسجيل الدخول
checkLogin();

// التأكد أن الطلب (Request) أُرسل عن طريق النموذج باستخدام طريقة POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // استلام قيمة الوصف (Description) من النموذج وتخزينها في متغير
    $description = $_POST['description'];

    // استلام قيمة المبلغ (Amount) من النموذج وتخزينها في متغير
    $amount = $_POST['amount'];

    // استلام الفئة (Category) من النموذج وتخزينها في متغير
    $category = $_POST['category'];

    // استلام التاريخ (Date) من النموذج وتخزينه في متغير
    $date = $_POST['date'];

    // استلام طريقة الدفع (Payment Method) من النموذج وتخزينها في متغير
    $payment_method = $_POST['payment_method'];

    // إنشاء استعلام SQL لإضافة البيانات إلى جدول المعاملات (transactions)
    $sql = "INSERT INTO transactions (description, amount, category, date, payment_method) 
            VALUES ('$description', $amount, '$category', '$date', '$payment_method')";

    // تنفيذ الاستعلام على قاعدة البيانات والتحقق من نجاح العملية
    if (mysqli_query($conn, $sql)) {
        
        // إذا نجحت العملية → إعادة توجيه المستخدم إلى صفحة المعاملات مع رسالة نجاح
        header("Location: transactions.php?message=تمت إضافة المعاملة بنجاح");
        
        // إيقاف تنفيذ الكود بعد إعادة التوجيه لتفادي أي مشاكل
        exit();
    } else {
        // إذا فشلت العملية → عرض رسالة خطأ مع تفاصيل الخطأ من قاعدة البيانات
        echo "خطأ: " . $sql . "<br>" . mysqli_error($conn);
    }
}
?>
