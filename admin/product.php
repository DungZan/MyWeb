<button class="btn btn-primary" data-toggle="modal" data-target="#addProductModal">Thêm sản phẩm</button>



<!-- Modal Thêm Sản Phẩm -->
<div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="post" action="product.php">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalLabel">Thêm sản phẩm mới</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="productName">Tên sản phẩm</label>
                        <input type="text" class="form-control" id="productName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="productPrice">Giá</label>
                        <input type="number" class="form-control" id="productPrice" name="price" required>
                    </div>
                    <div class="form-group">
                        <label for="productCategory">Loại</label>
                        <input type="text" class="form-control" id="productCategory" name="category" required>
                    </div>
                    <div class="form-group">
                        <label for="productDescription">Mô tả</label>
                        <textarea class="form-control" id="productDescription" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary" name="add_product">Thêm</button>
                </div>
            </div>
        </form>
    </div>
</div>


                   <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Danh sách sản phẩm</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <?php
                                // Thiết lập phân trang
                                $limit = 3; // Số bản ghi mỗi trang
                                $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
                                if ($page < 1) $page = 1;
                                $offset = ($page - 1) * $limit;

                                // Kết nối database
                                $host = 'localhost';
                                $db = 'quanao_db';
                                $user = 'root';
                                $pass = '';
                                $conn = new mysqli($host, $user, $pass, $db);
                                if ($conn->connect_error) {
                                    die('Kết nối thất bại: ' . $conn->connect_error);
                                }
                                $conn->set_charset('utf8');

                                // Lấy tổng số bản ghi
                                $total_sql = "SELECT COUNT(*) as total FROM products";
                                $total_result = $conn->query($total_sql);
                                $total_row = $total_result ? $total_result->fetch_assoc() : ['total' => 0];
                                $total_records = (int)$total_row['total'];
                                $total_pages = $total_records > 0 ? ceil($total_records / $limit) : 1;

                                // Lấy dữ liệu trang hiện tại
                                $sql = "SELECT id, name, price, description, created_at FROM products ORDER BY id DESC LIMIT ? OFFSET ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("ii", $limit, $offset);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                ?>
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Tên sản phẩm</th>
                                            <th>Giá</th>
                                            <th>Mô tả</th>
                                            <th>Ngày tạo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($result && $result->num_rows > 0): ?>
                                            <?php while($row = $result->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?= $row['id'] ?></td>
                                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                                    <td><?= htmlspecialchars($row['price']) ?></td>
                                                    <td><?= htmlspecialchars($row['description']) ?></td>
                                                    <td><?= $row['created_at'] ?></td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr><td colspan="7">Không có sản phẩm nào.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                                <!-- Pagination -->
                                <nav>
                                    <ul class="pagination justify-content-center">
                                        <?php if ($page > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=product&p=<?= $page - 1 ?>">Trước</a>
                                            </li>
                                        <?php else: ?>
                                            <li class="page-item disabled">
                                                <span class="page-link">Trước</span>
                                            </li>
                                        <?php endif; ?>

                                        <?php
                                        // Hiển thị tối đa 5 trang, với trang hiện tại ở giữa nếu có thể
                                        $start = max(1, $page - 2);
                                        $end = min($total_pages, $page + 2);
                                        if ($end - $start < 4) {
                                            if ($start == 1) $end = min($total_pages, $start + 4);
                                            if ($end == $total_pages) $start = max(1, $end - 4);
                                        }
                                        for ($i = $start; $i <= $end; $i++): ?>
                                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                                <a class="page-link" href="?page=product&p=<?= $i ?>"><?= $i ?></a>
                                            </li>
                                        <?php endfor; ?>

                                        <?php if ($page < $total_pages): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=product&p=<?= $page + 1 ?>">Sau</a>
                                            </li>
                                        <?php else: ?>
                                            <li class="page-item disabled">
                                                <span class="page-link">Sau</span>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                                <?php
                                $stmt->close();
                                $conn->close();
                                ?>
                            </div>
                        </div>
                    </div>
<?php
// Kết nối CSDL

$host = 'localhost';
                                $db = 'quanao_db';
                                $user = 'root';
                                $pass = '';
                                $conn = new mysqli($host, $user, $pass, $db);
                                if ($conn->connect_error) {
                                    die('Kết nối thất bại: ' . $conn->connect_error);
                                }
                                $conn->set_charset('utf8');

// Lấy danh sách loại sản phẩm
$category_options = [];
$cat_result = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");
if ($cat_result && $cat_result->num_rows > 0) {
    while ($cat = $cat_result->fetch_assoc()) {
        $category_options[] = $cat;
    }
}

$name = $price = $description = '';
$category = '';
$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? ''); // category là id
    if ($name === '' || $price === '' || !is_numeric($price) || $category === '' || !is_numeric($category)) {
        $error = 'Vui lòng nhập tên, giá và chọn loại sản phẩm hợp lệ!';
    } else {
        $stmt = $conn->prepare("INSERT INTO products (name, price, description, category) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('sdsi', $name, $price, $description, $category);
        if ($stmt->execute()) {
            $success = 'Thêm sản phẩm thành công!';
            $name = $price = $description = $category = '';
        } else {
            $error = 'Lỗi khi thêm sản phẩm: ' . $conn->error;
        }
        $stmt->close();
    }
}
?>
<div class="container mt-4">
    <h2>Thêm sản phẩm mới</h2>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="form-group">
            <label for="name">Tên sản phẩm</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
        </div>
        <div class="form-group">
            <label for="category">Loại sản phẩm</label>
            <select class="form-control" id="category" name="category" required>
                <option value="">-- Chọn loại sản phẩm --</option>
                <?php foreach ($category_options as $cat): ?>
                    <option value="<?php echo (int)$cat['id']; ?>" <?php if ($category == $cat['id']) echo 'selected'; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="price">Giá</label>
            <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($price); ?>" required>
        </div>
        <div class="form-group">
            <label for="description">Mô tả</label>
            <textarea class="form-control" id="description" name="description"><?php echo htmlspecialchars($description); ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Thêm sản phẩm</button>
    </form>
</div>