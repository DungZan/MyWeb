<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ</title>
</head>
<body>
    <h1>Chào mừng bạn đến với trang chủ người dùng</h1>
    <p>Xin chào, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p>
    <p>Email: <?php echo htmlspecialchars($_SESSION['email']); ?></p>
    <p>Quyền: <?php echo !empty($_SESSION['is_admin']) ? 'Admin' : 'User'; ?></p>
    <button onclick="location.href='logout.php'">Đăng xuất</button>
</body>
</html>