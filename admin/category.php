<?php
session_start();
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit;
}
$host = 'localhost';
$db = 'quanao_db';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Kết nối thất bại: ' . $conn->connect_error);
}
$conn->set_charset('utf8');

$name = '';
$success = $error = '';

// Thêm loại sản phẩm
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = trim($_POST['name'] ?? '');
    if ($name === '') {
        $error = 'Vui lòng nhập tên loại sản phẩm!';
    } else {
        $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->bind_param('s', $name);
        if ($stmt->execute()) {
            $success = 'Thêm loại sản phẩm thành công!';
            $name = '';
        } else {
            $error = 'Lỗi khi thêm loại sản phẩm: ' . $conn->error;
        }
        $stmt->close();
    }
}
// Xoá loại sản phẩm
if (isset($_POST['delete_category']) && isset($_POST['category_id'])) {
    $delete_id = (int)$_POST['category_id'];
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param('i', $delete_id);
    $stmt->execute();
    $stmt->close();
    echo '<div class="alert alert-success">Đã xoá loại sản phẩm!</div>';
}
// Xử lý cập nhật loại sản phẩm
if (isset($_POST['edit_category']) && isset($_POST['edit_category_id'])) {
    $edit_id = (int)$_POST['edit_category_id'];
    $edit_name = trim($_POST['edit_name'] ?? '');
    if ($edit_name !== '') {
        $stmt = $conn->prepare("UPDATE categories SET name = ? WHERE id = ?");
        $stmt->bind_param('si', $edit_name, $edit_id);
        $stmt->execute();
        $stmt->close();
        echo '<div class="alert alert-success">Đã cập nhật loại sản phẩm!</div>';
    } else {
        echo '<div class="alert alert-danger">Tên loại sản phẩm không được để trống!</div>';
    }
}
// Phân trang
$limit = 5;
$page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;
$total_sql = "SELECT COUNT(*) as total FROM categories";
$total_result = $conn->query($total_sql);
$total_row = $total_result ? $total_result->fetch_assoc() : ['total' => 0];
$total_records = (int)$total_row['total'];
$total_pages = $total_records > 0 ? ceil($total_records / $limit) : 1;
$sql = "SELECT id, name, created_at FROM categories ORDER BY id DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>
<button class="btn btn-primary" data-toggle="modal" data-target="#addCategoryModal">Thêm loại sản phẩm</button>
<div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryModalLabel">Thêm loại sản phẩm mới</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="categoryName">Tên loại sản phẩm</label>
                        <input type="text" class="form-control" id="categoryName" name="name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary" name="add_category">Thêm</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="card shadow mb-4 mt-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Danh sách loại sản phẩm</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên loại sản phẩm</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= $row['created_at'] ?></td>
                                <td>
                                    <form method="post" style="display:inline;" onsubmit="return confirm('Bạn có chắc muốn xoá loại sản phẩm này?');">
                                        <input type="hidden" name="category_id" value="<?= $row['id'] ?>">
                                        <button type="submit" name="delete_category" class="btn btn-danger btn-sm">Xoá</button>
                                    </form>
                                    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editCategoryModal<?= $row['id'] ?>">Sửa</button>
                                    <!-- Modal Sửa -->
                                    <div class="modal fade" id="editCategoryModal<?= $row['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="editCategoryModalLabel<?= $row['id'] ?>" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <form method="post">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="editCategoryModalLabel<?= $row['id'] ?>">Chỉnh sửa loại sản phẩm</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label for="edit_name_<?= $row['id'] ?>">Tên loại sản phẩm</label>
                                                            <input type="text" class="form-control" id="edit_name_<?= $row['id'] ?>" name="edit_name" value="<?= htmlspecialchars($row['name']) ?>" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <input type="hidden" name="edit_category_id" value="<?= $row['id'] ?>">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                                                        <button type="submit" class="btn btn-primary" name="edit_category">Lưu</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4">Không có loại sản phẩm nào.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <!-- Pagination -->
            <nav>
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=category&p=<?= $page - 1 ?>">Trước</a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <span class="page-link">Trước</span>
                        </li>
                    <?php endif; ?>
                    <?php
                    $start = max(1, $page - 2);
                    $end = min($total_pages, $page + 2);
                    if ($end - $start < 4) {
                        if ($start == 1) $end = min($total_pages, $start + 4);
                        if ($end == $total_pages) $start = max(1, $end - 4);
                    }
                    for ($i = $start; $i <= $end; $i++): ?>
                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                            <a class="page-link" href="?page=category&p=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=category&p=<?= $page + 1 ?>">Sau</a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <span class="page-link">Sau</span>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
</div>
<?php
$stmt->close();
$conn->close();
?>
