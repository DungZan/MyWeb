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
        // mã hóa mật khẩu
        if (password_verify($password, $user['password'])) {
            return $user;
        }
    }
    return false;
}
function signup($name, $email, $password, $isHashed = false) {
    global $conn;
    // Kiểm tra email đã tồn tại
    $stmt = $conn->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        return [
            'success' => false,
            'message' => 'Email đã được sử dụng.'
        ];
    }
    $stmt->close();

    // Mã hóa nếu chưa mã hóa
    $hashed_password = $isHashed ? $password : password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
    $stmt->bind_param('sss', $name, $email, $hashed_password);
    if ($stmt->execute()) {
        $user_id = $stmt->insert_id;
        $stmt->close();
        return [
            'success' => true,
            'user_id' => $user_id,
            'message' => 'Đăng ký thành công!'
        ];
    } else {
        $error = $stmt->error;
        $stmt->close();
        return [
            'success' => false,
            'message' => 'Đăng ký thất bại: ' . $error
        ];
    }
}
?>
