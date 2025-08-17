<?php

include './includes/db.php'; // الاتصال بقاعدة البيانات

// لو المستخدم مسجل دخول بالفعل
if (isset($_SESSION['user_id'])) {
    header("Location: index.php?page=tasks");
    exit;
}

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm = trim($_POST['confirm']);

    // التحقق من كلمة المرور
    if ($password !== $confirm) {
        $error = "❌ كلمة المرور وتأكيدها غير متطابقين";
    } else {
        // التحقق من عدم وجود المستخدم
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "❌ اسم المستخدم موجود بالفعل";
        } else {
            // تشفير كلمة المرور
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // إدخال المستخدم الجديد
            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hashed_password);
            if ($stmt->execute()) {
                $success = "✅ تم إنشاء الحساب بنجاح! يمكنك تسجيل الدخول الآن.";
            } else {
                $error = "❌ حدث خطأ أثناء إنشاء الحساب.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>creat acount</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
     <link rel="stylesheet" href="./css/Slogin.css">
</head>
<div class="background-circle"></div>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card p-4 shadow">
                <h3 class="text-center mb-4">creat acount</h3>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">confirm password</label>
                        <input type="password" name="confirm" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-success w-100">creat acount</button>
                </form>

                <p class="mt-3 text-center">
                    you have acount ?<a href="index.php?page=login">login</a>
                </p>
            </div>
        </div>
    </div>
</div>

</body>
</html>
