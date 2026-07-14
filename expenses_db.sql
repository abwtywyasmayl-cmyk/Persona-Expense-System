-- إنشاء قاعدة البيانات باسم simple_expenses_db إذا لم تكن موجودة
CREATE DATABASE IF NOT EXISTS simple_expenses_db;

-- استخدام قاعدة البيانات التي أنشأناها
USE simple_expenses_db;

-- إنشاء جدول المعاملات المالية باسم transactions إذا لم يكن موجوداً
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    -- العمود id هو رقم تسلسلي فريد لكل عملية (يتم زيادته تلقائياً) ويُستخدم كمفتاح أساسي
    
    description VARCHAR(255) NOT NULL, 
    -- وصف نصي للعملية (مثل راتب، فاتورة، تسوق)، لا يمكن أن يكون فارغ
    
    amount DECIMAL(10,2) NOT NULL, 
    -- المبلغ المالي للعملية (10 أرقام كحد أقصى، منها رقمين بعد الفاصلة)، لا يمكن تركه فارغ
    
    category ENUM('food', 'transport', 'entertainment', 'income', 'bills', 'shopping', 'health', 'other') DEFAULT 'other', 
    -- نوع العملية (غذاء، نقل، تسلية، دخل، فواتير... إلخ)، إذا لم يحدد المستخدم يتم إدخاله كـ "other"
    
    date DATE NOT NULL, 
    -- تاريخ العملية بصيغة YYYY-MM-DD (مثل 2023-06-01)
    
    payment_method ENUM('cash', 'credit card', 'debit card', 'digital wallet') DEFAULT 'cash', 
    -- طريقة الدفع (نقدي، بطاقة ائتمان، بطاقة خصم، محفظة إلكترونية)، القيمة الافتراضية نقدي
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    -- يسجل وقت إنشاء السجل تلقائياً عند إضافته
    
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP 
    -- يسجل وقت آخر تعديل تلقائياً عند تحديث السجل
);

-- إدخال بيانات تجريبية (معاملات مالية أولية) داخل الجدول
INSERT INTO transactions (description, amount, category, date, payment_method) VALUES
('راتب الشهر', 5000.00, 'income', '2023-06-01', 'debit card'), 
-- دخل موجب: راتب رئيسي في 1 يونيو عن طريق بطاقة الخصم

('تسوق بقالة', -350.50, 'food', '2023-06-05', 'cash'), 
-- مصروف سالب: بقالة يوم 5 يونيو نقداً

('فاتورة الكهرباء', -200.00, 'bills', '2023-06-10', 'credit card'), 
-- مصروف: دفع فاتورة الكهرباء بالبطاقة الائتمانية

('تذاكر سينما', -120.00, 'entertainment', '2023-06-15', 'cash'), 
-- مصروف: تسلية (سينما) مدفوع نقداً

('وقود السيارة', -180.00, 'transport', '2023-06-20', 'credit card'), 
-- مصروف: وقود للسيارة مدفوع بالبطاقة الائتمانية

('عشاء في مطعم', -150.00, 'food', '2023-06-25', 'debit card'), 
-- مصروف: وجبة عشاء في مطعم ببطاقة الخصم

('راتب إضافي', 1000.00, 'income', '2023-06-28', 'debit card'), 
-- دخل إضافي موجب يوم 28 يونيو ببطاقة الخصم

('شراء ملابس', -400.00, 'shopping', '2023-06-30', 'credit card'); 
-- مصروف: شراء ملابس يوم 30 يونيو باستخدام البطاقة الائتمانية
