<?php

include './includes/db.php';

// لو المستخدم مش مسجل دخول يرجع لصفحة الدخول
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?page=login");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = "";

// إضافة مهمة جديدة
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_task'])) {
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $task_id = isset($_POST['task_id']) ? intval($_POST['task_id']) : 0;

        if (!empty($title)) {
            if ($task_id > 0) {
                // تعديل
                $stmt = $conn->prepare("UPDATE tasks SET title = ?, description = ? WHERE id = ? AND user_id = ?");
                $stmt->bind_param("ssii", $title, $description, $task_id, $user_id);
                $stmt->execute();
                $message = "✅ تم تعديل المهمة بنجاح";
            } else {
                // إضافة
                $stmt = $conn->prepare("INSERT INTO tasks (user_id, title, description) VALUES (?, ?, ?)");
                $stmt->bind_param("iss", $user_id, $title, $description);
                $stmt->execute();
                $message = "✅ تم إضافة المهمة بنجاح";
            }
        } else {
            $message = "⚠️ عنوان المهمة مطلوب";
        }
    }

}
 // حذف جماعي
    if (isset($_POST['delete_selected']) && !empty($_POST['task_ids'])) {
        $ids = implode(",", array_map('intval', $_POST['task_ids']));
        $conn->query("DELETE FROM tasks WHERE id IN ($ids) AND user_id = $user_id");
        $message = "🗑 تم حذف المهام المحددة";
    }

// حذف مهمة
if (isset($_GET['delete'])) {
    $task_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $task_id, $user_id);
    $stmt->execute();
    header("Location: index.php?page=tasks");
    exit;
}

// تحديد مهمة كمكتملة
if (isset($_GET['done'])) {
    $task_id = intval($_GET['done']);
    $stmt = $conn->prepare("UPDATE tasks SET status='done' WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $task_id, $user_id);
    $stmt->execute();
    header("Location: index.php?page=tasks");
    exit;
    
}
// تعديل - جلب بيانات المهمة
$edit_task = null;
if (isset($_GET['edit'])) {
    $task_id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $task_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_task = $result->fetch_assoc();
}
// جلب المهام
$stmt = $conn->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$tasks = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>Personal Task Manager📋</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Personal Task Manager📋 </h2>
        <a href="index.php?page=logout" class="btn btn-danger">logout🚪</a>
    </div>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <!-- إضافة / تعديل مهمة -->
    <div class="card p-4 mb-4 shadow">
        <h4><?= $edit_task ? "Edit A Task" : "Add A Task" ?></h4>
        <form method="POST">
            <?php if ($edit_task): ?>
                <input type="hidden" name="task_id" value="<?= $edit_task['id'] ?>">
            <?php endif; ?>
            <div class="mb-3">
                <label class="form-label">عنوان المهمة</label>
                <input type="text" name="title" class="form-control" required value="<?= $edit_task['title'] ?? '' ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">وصف المهمة</label>
                <textarea name="description" class="form-control"><?= $edit_task['description'] ?? '' ?></textarea>
            </div>
           
            <button type="submit" name="save_task" class="btn btn-success"><?= $edit_task ? "تعديل" : "إضافة" ?></button>
        </form>
    </div>

    <!-- عرض المهام -->
    <div class="card p-4 shadow">
        <h4>List Task</h4>
        <?php if (count($tasks) > 0): ?>
            <form method="POST">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>تحديد</th>
                            <th>العنوان</th>
                            <th>الوصف</th>
                            <th>الحالة</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tasks as $task): ?>
                            <tr>
                                <td><input type="checkbox" name="task_ids[]" value="<?= $task['id'] ?>"></td>
                                <td><?= htmlspecialchars($task['title']) ?></td>
                                <td><?= htmlspecialchars($task['description']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $task['status'] == 'done' ? 'success' : 'warning' ?>">
                                        <?= $task['status'] == 'done' ? 'منجزة' : 'معلقة' ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($task['status'] != 'done'): ?>
                                        <a href="index.php?page=tasks&done=<?= $task['id'] ?>" class="btn btn-sm btn-primary">ok</a>
                                    <?php endif; ?>
                                    <a href="index.php?page=tasks&edit=<?= $task['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="index.php?page=tasks&delete=<?= $task['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد من الحذف؟')">delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <button type="submit" name="delete_selected" class="btn btn-danger mt-2" onclick="return confirm('هل أنت متأكد من الحذف الجماعي؟')">delete all </button>
            </form>
        <?php else: ?>
            <p>NOT TASK</p>
        <?php endif; ?>
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // إضافة أي سكريبتات جافاسكريبت هنا إذا لزم الأمر
    });
</body>
</html>
