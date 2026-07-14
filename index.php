
<?php
include "config.php";
checkLogin();
$stats = getSystemStats();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - نظام تتبع المصروفات الشخصي</title>
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

        /* قسم الترحيب */
        .welcome-section {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 2.5rem;
            border-radius: 16px;
            margin-bottom: 2rem;
            box-shadow: var(--card-shadow);
            text-align: center;
            animation: fadeIn 1s ease;
        }

        .welcome-section h2 {
            font-size: 2.2rem;
            margin-bottom: 0.8rem;
        }

        .welcome-section p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        /* إحصائيات */
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1.8rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background-color: white;
            border-radius: 16px;
            padding: 1.8rem;
            display: flex;
            align-items: center;
            box-shadow: var(--card-shadow);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
            animation: scaleIn 0.7s ease;
        }

        .stat-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
        }

        .stat-card:before {
            content: '';
            position: absolute;
            width: 150px;
            height: 150px;
            background: linear-gradient(135deg, transparent, rgba(255, 193, 7, 0.1), transparent);
            transform: rotate(45deg);
            right: -75px;
            top: -75px;
        }

        .stat-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: 15px;
            font-size: 1.8rem;
            color: white;
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .stat-icon.blue { background: linear-gradient(135deg, var(--info), #1976D2); }
        .stat-icon.green { background: linear-gradient(135deg, var(--success), #388E3C); }
        .stat-icon.orange { background: linear-gradient(135deg, var(--secondary), #E65100); }
        .stat-icon.red { background: linear-gradient(135deg, var(--danger), #D32F2F); }

        .stat-info h3 {
            font-size: 1rem;
            color: #6c757d;
            margin-bottom: 0.8rem;
        }

        .stat-info p {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark);
        }

        .sphere-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .sphere {
            background: white;
            border-radius: 50%;
            aspect-ratio: 1/1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: var(--card-shadow);
            padding: 2rem;
            text-align: center;
            transition: all 0.4s ease;
            animation: float 6s ease-in-out infinite;
            position: relative;
            overflow: hidden;
        }

        .sphere:nth-child(1) { 
            background: linear-gradient(135deg, #FFF9C4, var(--primary));
            animation-delay: 0s;
        }
        .sphere:nth-child(2) { 
            background: linear-gradient(135deg, #FFECB3, var(--primary-dark));
            animation-delay: 0.5s;
        }
        .sphere:nth-child(3) { 
            background: linear-gradient(135deg, #FFE082, var(--secondary));
            animation-delay: 1s;
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0px); }
        }

        .sphere:before {
            content: '';
            position: absolute;
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            top: -30px;
            left: -30px;
        }

        .sphere:after {
            content: '';
            position: absolute;
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            bottom: -20px;
            right: -20px;
        }

        .sphere h3 {
            font-size: 1.3rem;
            color: #5D4037;
            margin-bottom: 1rem;
            z-index: 1;
        }

        .sphere p {
            font-size: 1.8rem;
            font-weight: 800;
            color: #5D4037;
            z-index: 1;
        }

        /* التجاوب مع الشاشات المختلفة */
        @media (max-width: 768px) {
            .stats, .sphere-container {
                grid-template-columns: 1fr;
            }
            
            .nav {
                flex-direction: column;
            }
            
            .sphere {
                border-radius: 20px;
                aspect-ratio: unset;
                height: 200px;
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

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
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
            <a href="index.php" class="active"><i class="fas fa-home"></i> الرئيسية</a>
            <a href="transactions.php"><i class="fas fa-exchange-alt"></i> المعاملات</a>
        </div>

        <!-- قسم الترحيب -->
        <div class="welcome-section">
            <h2>مرحباً <?php echo $_SESSION["username"]; ?>!</h2>
            <p>هنا يمكنك تتبع مصروفاتك ودخلك بسهولة وكفاءة.</p>
        </div>
        
        <!-- الإحصائيات في شكل كرات -->
        <div class="sphere-container">
            <div class="sphere">
                <h3>الرصيد الكلي</h3>
                <p><?php echo number_format($stats['total_balance'], 2); ?> ر.س</p>
            </div>
            <div class="sphere">
                <h3>إجمالي الدخل</h3>
                <p>+<?php echo number_format($stats['total_income'], 2); ?> ر.س</p>
            </div>
            <div class="sphere">
                <h3>إجمالي المصروفات</h3>
                <p>-<?php echo number_format($stats['total_expenses'], 2); ?> ر.س</p>
            </div>
        </div>
        
        <!-- الإحصائيات -->
        <div class="stats">
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="stat-info">
                    <h3>الرصيد الحالي</h3>
                    <p><?php echo number_format($stats['total_balance'], 2); ?> ر.س</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon green">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-info">
                    <h3>إجمالي الدخل</h3>
                    <p>+<?php echo number_format($stats['total_income'], 2); ?> ر.س</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-info">
                    <h3>إجمالي المصروفات</h3>
                    <p>-<?php echo number_format($stats['total_expenses'], 2); ?> ر.س</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon red">
                    <i class="fas fa-chart-pie"></i>
                </div>
                <div class="stat-info">
                    <h3>عدد الفئات</h3>
                    <p><?php echo count($stats['expenses_by_category']); ?></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>