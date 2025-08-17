<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// الاتصال بقاعدة البيانات
require_once __DIR__ . '/includes/db.php';

// تحديد الصفحة المطلوبة
$page = isset($_GET['page']) ? $_GET['page'] : 'login';

// الصفحات المسموح بها
$allowed_pages = ['login', 'register', 'tasks', 'logout'];

// التحقق من الصفحة المطلوبة
if (!in_array($page, $allowed_pages)) {
    echo "<h2>❌ الصفحة غير موجودة</h2>";
    exit;
}


// حماية الصفحات
if (!isset($_SESSION['user_id']) && $page !== 'login' && $page !== 'register') {
    header("Location: index.php?page=login");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/styles.css">
    <title>Personal Task Manager</title>
</head>
<body>

<?php if (isset($_SESSION['user_id'])): ?>
    <div style="text-align: right; padding: 10px;">
        <a href="index.php?page=logout" class="btn btn-danger">logout🚪</a>
    </div>
<?php endif; ?>

<?php
// هنا تضمين الصفحة المطلوبة
include __DIR__ . "/page/{$page}.php";
?>

<script src="./js/bootstrap.min.js"></script>
<script src="./js/scripts.js"></script>
</body>
</html>
