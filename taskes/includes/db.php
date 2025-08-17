<?php
$servername = "localhost";
$username = "root"; // الافتراضي في MAMP
$password = "root"; // الافتراضي في MAMP
$dbname = "task_manager"; // اسم قاعدة البيانات

// إنشاء الاتصال MySQLi
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}

// إنشاء الاتصال PDO
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("فشل الاتصال بقاعدة البيانات: " . $e->getMessage());
}
?>
