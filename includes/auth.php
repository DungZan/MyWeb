<?php
// Kết nối database
$host = 'localhost';
$db = 'quanao_db';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Kết nối thất bại: ' . $conn->connect_error);
}
// Hàm kiểm tra đăng nhập
function login($email, $password) {
    global $conn;
    $stmt = $conn->prepare('SELECT id, name, email, password, is_admin FROM users WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if ($password === $user['password']) { // Giả sử mật khẩu chưa mã hóa
            return $user;
        }
    }
    return false;
}
?>
