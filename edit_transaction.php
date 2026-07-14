<?php
include "config.php";
checkLogin();

if(!isset($_GET['id'])) {
    header("Location: transactions.php");
    exit();
}

$id = $_GET['id'];

// جلب بيانات المعاملة
$sql = "SELECT * FROM transactions WHERE id = $id";
$result = mysqli_query($conn, $sql);
$transaction = mysqli_fetch_assoc($result);

if(!$transaction) {
    header("Location: transactions.php");
    exit();
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $description = $_POST['description'];
    $amount = $_POST['amount'];
    $category = $_POST['category'];
    $date = $_POST['date'];
    $payment_method = $_POST['payment_method'];
    
    $sql = "UPDATE transactions SET 
            description = '$description', 
            amount = $amount, 
            category = '$category', 
            date = '$date', 
            payment_method = '$payment_method'
            WHERE id = $id";
    
    if(mysqli_query($conn, $sql)) {
        header("Location: transactions.php?message=تم تحديث المعاملة بنجاح");
        exit();
    } else {
        header("Location: transactions.php?error=حدث خطأ أثناء تحديث المعاملة");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل المعاملة - نظام تتبع المصروفات الشخصي</title>
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
            max-width: 800px;
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
            animation: slideDown 0.5s ease;
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
            animation: slideDown 0.6s ease;
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

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }

        .btn i {
            margin-left: 8px;
        }

        /* نموذج التعديل */
        .form-container {
            background-color: white;
            border-radius: 16px;
            padding: 1.8rem;
            box-shadow: var(--card-shadow);
            margin-bottom: 1.8rem;
            animation: fadeIn 0.8s ease;
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

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
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
            <h1 class="page-title"><i class="fas fa-edit"></i> تعديل المعاملة</h1>
            <a href="transactions.php" class="btn btn-secondary"><i class="fas fa-arrow-right"></i> العودة إلى القائمة</a>
        </div>

        <!-- نموذج تعديل المعاملة -->
        <div class="form-container">
            <form method="post">
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="description">وصف المعاملة</label>
                            <input type="text" class="form-control" id="description" name="description" value="<?php echo $transaction['description']; ?>" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="amount">المبلغ</label>
                            <input type="number" step="0.01" class="form-control" id="amount" name="amount" value="<?php echo $transaction['amount']; ?>" required>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="category">الفئة</label>
                            <select class="form-control" id="category" name="category" required>
                                <option value="income" <?php echo $transaction['category'] == 'income' ? 'selected' : ''; ?>>دخل</option>
                                <option value="food" <?php echo $transaction['category'] == 'food' ? 'selected' : ''; ?>>طعام</option>
                                <option value="transport" <?php echo $transaction['category'] == 'transport' ? 'selected' : ''; ?>>مواصلات</option>
                                <option value="entertainment" <?php echo $transaction['category'] == 'entertainment' ? 'selected' : ''; ?>>ترفيه</option>
                                <option value="bills" <?php echo $transaction['category'] == 'bills' ? 'selected' : ''; ?>>فواتير</option>
                                <option value="shopping" <?php echo $transaction['category'] == 'shopping' ? 'selected' : ''; ?>>تسوق</option>
                                <option value="health" <?php echo $transaction['category'] == 'health' ? 'selected' : ''; ?>>صحة</option>
                                <option value="other" <?php echo $transaction['category'] == 'other' ? 'selected' : ''; ?>>أخرى</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="date">التاريخ</label>
                            <input type="date" class="form-control" id="date" name="date" value="<?php echo $transaction['date']; ?>" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="payment_method">طريقة الدفع</label>
                            <select class="form-control" id="payment_method" name="payment_method" required>
                                <option value="cash" <?php echo $transaction['payment_method'] == 'cash' ? 'selected' : ''; ?>>نقدي</option>
                                <option value="credit card" <?php echo $transaction['payment_method'] == 'credit card' ? 'selected' : ''; ?>>بطاقة ائتمان</option>
                                <option value="debit card" <?php echo $transaction['payment_method'] == 'debit card' ? 'selected' : ''; ?>>بطاقة خصم</option>
                                <option value="digital wallet" <?php echo $transaction['payment_method'] == 'digital wallet' ? 'selected' : ''; ?>>محفظة رقمية</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ التعديلات</button>
                    <a href="transactions.php" class="btn btn-secondary"><i class="fas fa-times"></i> إلغاء</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>