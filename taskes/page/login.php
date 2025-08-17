<?php

include './includes/db.php'; // الاتصال بقاعدة البيانات

// لو المستخدم مسجل دخول بالفعل، نحوله لصفحة المهام
if (isset($_SESSION['user_id'])) {
    header("Location: index.php?page=tasks");
    exit;
}

// التحقق من إرسال الفورم
$error = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // البحث عن المستخدم
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // التحقق من كلمة المرور
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: index.php?page=tasks");
            exit;
        } else {
            $error = "❌ password not correct";
        }
    } else {
        $error = "❌ username not found";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/7.2.0/mdb.min.css" rel="stylesheet"/>
    <script src="https://kit.fontawesome.com/a2e0c6d9b8.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <title>Login</title>
    <link rel="stylesheet" href="./css/Slogin.css">
</head>
<body>
<div class="background-circle"></div>
<div class="container px-4 py-5 px-md-5 text-center text-lg-start my-5">
    <div class="row gx-lg-5 align-items-center mb-5">
      <div class="col-lg-6 mb-5 mb-lg-0" style="z-index: 10">
        <h1 class="my-5 display-5 fw-bold ls-tight" style="color: hsl(218, 81%, 95%)">
          The Personal Task Manager <br />
          <span style="color: hsl(218, 81%, 75%)">for your business</span>
        </h1>
        <p class="mb-4 opacity-70" style="color: hsl(218, 81%, 85%)">
            This is a simple task management system that allows you to create, edit, and delete tasks. 
            You can also mark tasks as done and delete multiple tasks at once.
            <br>
            <strong>Login to manage your tasks</strong>
        </p>
      </div>
      <div class="col-lg-6 mb-5 mb-lg-0 position-relative">
        <div class="card bg-glass">
          <div class="card-body px-4 py-5 px-md-5">
            <div class="login-container">
                <h2>Login</h2>
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                <form action="" method="POST">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" name="username" id="username" placeholder="Enter username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" placeholder="Enter password" required>
                    </div>
                    <button type="submit" class="btn btn-primary mb-4">Sign In</button>
                    <p class="mt-3 text-center">
                        No account? <a href="index.php?page=register">Create Account</a>
                    </p>            
                </form>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>
</body>
</html>
