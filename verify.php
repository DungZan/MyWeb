<?php
session_start();
$error = '';
$success = '';
if (!isset($_SESSION['verify_email']) || !isset($_SESSION['verify_code'])) {
    header('Location: registration.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_code = trim($_POST['code'] ?? '');
    if ($input_code == $_SESSION['verify_code']) {
        // Đăng ký tài khoản
        require_once __DIR__ . '/includes/auth.php';
        $fullname = $_SESSION['verify_name'];
        $username = $_SESSION['verify_username'];
        $email = $_SESSION['verify_email'];
        $password = $_SESSION['verify_password'];
        $result = signup($fullname, $username, $email, $password, true);
        if (!empty($result['success'])) {
            $success = 'Xác thực thành công! Bạn có thể đăng nhập.';
            unset($_SESSION['verify_email'], $_SESSION['verify_name'], $_SESSION['verify_username'], $_SESSION['verify_password'], $_SESSION['verify_code']);
            Header('Refresh: 3; URL=login.php');
        } else {
            $error = $result['message'] ?? 'Đăng ký thất bại. Vui lòng thử lại.';
        }
    } else {
        $error = 'Mã xác nhận không đúng.';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Xác thực email</title>
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <h2>Xác thực email</h2>
            <p>Nhập mã xác nhận đã gửi đến email: <b><?= htmlspecialchars($_SESSION['verify_email']) ?></b></p>
            <?php if ($error): ?>
                <div style="color:red; text-align:center; margin-bottom:10px;"> <?= htmlspecialchars($error) ?> </div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div style="color:green; text-align:center; margin-bottom:10px;"> <?= htmlspecialchars($success) ?> </div>
            <?php else: ?>
            <form method="POST">
                <input type="text" name="code" placeholder="Nhập mã xác nhận" required>
                <button type="submit">Xác nhận</button>
            </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
