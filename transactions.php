<?php
include "config.php";
checkLogin();

// معالجة عمليات الحذف
if(isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $sql = "DELETE FROM transactions WHERE id = $id";
    if(mysqli_query($conn, $sql)) {
        header("Location: transactions.php?message=تم حذف المعاملة بنجاح");
        exit();
    }
}

// جلب جميع المعاملات
$sql = "SELECT * FROM transactions ORDER BY date DESC, created_at DESC";
$result = mysqli_query($conn, $sql);
$transactions = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المعاملات - نظام تتبع المصروفات الشخصي</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #FFC107;
            --primary-dark: #FFA000;
            --secondary: #FF9800;
            --success: #4CAF50;
            --danger: #F44336;
            --info: #2196F3;
            --warning: #FF5722;
            --light: #FFF8E1;
            --dark: #212529;
            --background: #FFFDE7;
            --card-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Tajawal', sans-serif;
        }

        body {
            background-color: var(--background);
            color: #333;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* الشريط العلوي */
        .topbar {
            background-color: white;
            border-radius: 16px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--card-shadow);
        }

        .user-info {
            display: flex;
            align-items: center;
        }

        .user-info .avatar {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-left: 10px;
            font-weight: bold;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .logout-btn {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 0.6rem 1.2rem;
            border-radius: 10px;
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: all 0.3s;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .logout-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }

        .logout-btn i {
            margin-left: 5px;
        }

        /* القائمة */
        .nav {
            display: flex;
            background: white;
            border-radius: 16px;
            margin-bottom: 1.5rem;
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }

        .nav a {
            padding: 1.2rem 1.8rem;
            text-decoration: none;
            color: #495057;
            font-weight: 500;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .nav a:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            z-index: -1;
            transform: translateY(100%);
            transition: transform 0.3s ease;
        }

        .nav a:hover:before, .nav a.active:before {
            transform: translateY(0);
        }

        .nav a:hover, .nav a.active {
            color: white;
        }

        .nav a i {
            margin-left: 8px;
            font-size: 1.2rem;
        }

        /* رأس الصفحة */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.8rem;
        }

        .page-title {
            font-size: 2rem;
            color: var(--dark);
            display: flex;
            align-items: center;
        }

        .page-title i {
            margin-left: 12px;
            color: var(--primary);
        }

        .btn {
            padding: 0.8rem 1.5rem;
            border-radius: 12px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: all 0.3s;
            font-weight: 500;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }

        .btn i {
            margin-left: 8px;
        }

        /* الجدول */
        .table-container {
            background-color: white;
            border-radius: 16px;
            padding: 1.8rem;
            box-shadow: var(--card-shadow);
            margin-bottom: 1.8rem;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 1.2rem;
            text-align: right;
            border-bottom: 1px solid #eee;
        }

        th {
            font-weight: 600;
            color: #495057;
            background-color: #FFF9C4;
        }

        tr:hover {
            background-color: #FFFDE7;
        }

        .badge {
            padding: 0.5rem 0.9rem;
            border-radius: 50rem;
            font-size: 0.85rem;
            font-weight: 700;
        }

        .badge-success {
            background-color: rgba(76, 175, 80, 0.15);
            color: #4CAF50;
        }

        .badge-danger {
            background-color: rgba(244, 67, 54, 0.15);
            color: #F44336;
        }

        .badge-warning {
            background-color: rgba(255, 152, 0, 0.15);
            color: #FF9800;
        }

        .badge-info {
            background-color: rgba(33, 150, 243, 0.15);
            color: #2196F3;
        }

        .badge-primary {
            background-color: rgba(255, 193, 7, 0.15);
            color: #FFC107;
        }

        .income {
            color: #4CAF50;
            font-weight: 600;
        }

        .expense {
            color: #F44336;
            font-weight: 600;
        }

        .action-buttons {
            display: flex;
            gap: 0.6rem;
        }

        .btn-sm {
            padding: 0.5rem 0.9rem;
            font-size: 0.85rem;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .btn-info {
            background: linear-gradient(135deg, var(--info), #1976D2);
            color: white;
        }

        .btn-info:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger), #D32F2F);
            color: white;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        /* نموذج الإضافة */
        .form-container {
            background-color: white;
            border-radius: 16px;
            padding: 1.8rem;
            box-shadow: var(--card-shadow);
            margin-bottom: 1.8rem;
            animation: slideDown 0.5s ease;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.6rem;
            font-weight: 600;
            color: #444;
        }

        .form-control {
            width: 100%;
            padding: 0.9rem 1.2rem;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.1);
        }

        .form-row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -12px;
            margin-left: -12px;
        }

        .form-col {
            flex: 1 0 0%;
            padding: 0 12px;
        }

        select.form-control {
            height: 50px;
        }

        /* التجاوب مع الشاشات المختلفة */
        @media (max-width: 768px) {
            .form-col {
                flex: 0 0 100%;
                margin-bottom: 1rem;
            }
            
            .form-row {
                margin-bottom: -1rem;
            }
            
            .action-buttons {
                flex-wrap: wrap;
            }
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
        }

        /* Animations */
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- الشريط العلوي -->
        <div class="topbar">
            <div class="user-info">
                <div class="avatar"><?php echo substr($_SESSION["username"], 0, 1); ?></div>
                <span><?php echo $_SESSION["username"]; ?></span>
            </div>
            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a>
        </div>

        <!-- القائمة -->
        <div class="nav">
            <a href="index.php"><i class="fas fa-home"></i> الرئيسية</a>
            <a href="transactions.php" class="active"><i class="fas fa-exchange-alt"></i> المعاملات</a>
        </div>

        <!-- رأس الصفحة -->
        <div class="page-header">
            <h1 class="page-title"><i class="fas fa-exchange-alt"></i> إدارة المعاملات</h1>
            <button onclick="showAddForm()" class="btn btn-primary"><i class="fas fa-plus"></i> إضافة معاملة جديدة</button>
        </div>

        <!-- نموذج إضافة معاملة -->
        <div class="form-container" id="addForm" style="display: none;">
            <h2 style="margin-bottom: 1.8rem;">إضافة معاملة جديدة</h2>
            <form action="add_transaction.php" method="post">
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="description">وصف المعاملة</label>
                            <input type="text" class="form-control" id="description" name="description" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="amount">المبلغ</label>
                            <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="category">الفئة</label>
                            <select class="form-control" id="category" name="category" required>
                                <option value="income">دخل</option>
                                <option value="food">طعام</option>
                                <option value="transport">مواصلات</option>
                                <option value="entertainment">ترفيه</option>
                                <option value="bills">فواتير</option>
                                <option value="shopping">تسوق</option>
                                <option value="health">صحة</option>
                                <option value="other">أخرى</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="date">التاريخ</label>
                            <input type="date" class="form-control" id="date" name="date" required value="<?php echo date('Y-m-d'); ?>">
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="payment_method">طريقة الدفع</label>
                            <select class="form-control" id="payment_method" name="payment_method" required>
                                <option value="cash">نقدي</option>
                                <option value="credit card">بطاقة ائتمان</option>
                                <option value="debit card">بطاقة خصم</option>
                                <option value="digital wallet">محفظة رقمية</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ المعاملة</button>
                    <button type="button" onclick="hideAddForm()" class="btn" style="background: #6c757d; color: white;"><i class="fas fa-times"></i> إلغاء</button>
                </div>
            </form>
        </div>

        <!-- جدول المعاملات -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>الوصف</th>
                        <th>المبلغ</th>
                        <th>الفئة</th>
                        <th>التاريخ</th>
                        <th>طريقة الدفع</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($transactions) > 0): ?>
                        <?php foreach($transactions as $transaction): ?>
                            <tr>
                                <td><?php echo $transaction['description']; ?></td>
                                <td class="<?php echo $transaction['amount'] >= 0 ? 'income' : 'expense'; ?>">
                                    <?php echo number_format($transaction['amount'], 2); ?> ر.س
                                </td>
                                <td>
                                    <?php 
                                    $category = $transaction['category'];
                                    $badge_class = '';
                                    if($category == 'income') $badge_class = 'badge-success';
                                    elseif($category == 'food') $badge_class = 'badge-danger';
                                    elseif($category == 'transport') $badge_class = 'badge-warning';
                                    elseif($category == 'entertainment') $badge_class = 'badge-info';
                                    else $badge_class = 'badge-primary';
                                    ?>
                                    <span class="badge <?php echo $badge_class; ?>"><?php echo $category; ?></span>
                                </td>
                                <td><?php echo $transaction['date']; ?></td>
                                <td><?php echo $transaction['payment_method']; ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="edit_transaction.php?id=<?php echo $transaction['id']; ?>" class="btn btn-info btn-sm"><i class="fas fa-edit"></i> تعديل</a>
                                        <a href="transactions.php?delete_id=<?php echo $transaction['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من حذف هذه المعاملة؟')"><i class="fas fa-trash"></i> حذف</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 2rem;">لا توجد معاملات مضافة حتى الآن</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function showAddForm() {
            document.getElementById('addForm').style.display = 'block';
        }

        function hideAddForm() {
            document.getElementById('addForm').style.display = 'none';
        }
    </script>
</body>
</html>