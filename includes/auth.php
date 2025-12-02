<?php
// Kết nối database
$host = 'localhost';
$db = 'salesmanagement_db';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Kết nối thất bại: ' . $conn->connect_error);
}
// Hàm kiểm tra đăng nhập
function login($username, $password) {
    global $conn;
    $stmt = $conn->prepare('SELECT STT AS id, TenDangNhap AS username, MatKhau AS password, HoTen AS name, LoaiTK AS role, GhiChu AS note FROM taikhoan WHERE TenDangNhap = ? LIMIT 1');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $storedPassword = (string)($user['password'] ?? '');
        $passwordInfo = password_get_info($storedPassword);
        $isValid = false;
        if ($passwordInfo['algo'] !== 0) {
            $isValid = password_verify($password, $storedPassword);
        } else {
            $isValid = hash_equals($storedPassword, $password);
        }
        if ($isValid) {
            $user['is_admin'] = strtoupper($user['role'] ?? '') === 'ADMIN';
            $user['email'] = $user['note'] ?? '';
            return $user;
        }
    }
    return false;
}
function signup($name, $username, $email, $password, $isHashed = false) {
    global $conn;
    // Kiểm tra tên đăng nhập đã tồn tại
    $stmt = $conn->prepare('SELECT STT FROM taikhoan WHERE TenDangNhap = ? LIMIT 1');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        return [
            'success' => false,
            'message' => 'Tên đăng nhập đã được sử dụng.'
        ];
    }
    $stmt->close();

    if (!empty($email)) {
        $stmt = $conn->prepare('SELECT STT FROM taikhoan WHERE GhiChu = ? LIMIT 1');
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
    }

    // Mã hóa nếu chưa mã hóa
    $hashed_password = $isHashed ? $password : password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare('INSERT INTO taikhoan (TenDangNhap, MatKhau, HoTen, LoaiTK, GhiChu) VALUES (?, ?, ?, ?, ?)');
    $role = 'CUST';
    $note = $email;
    $stmt->bind_param('sssss', $username, $hashed_password, $name, $role, $note);
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
