<?php


// مسح جميع بيانات الجلسة
session_unset();
session_destroy();

// إعادة التوجيه لصفحة تسجيل الدخول
header("Location: index.php?page=login");
exit;
?>
