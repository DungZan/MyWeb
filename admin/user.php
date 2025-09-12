                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Danh sách người dùng</h6>
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
                                $total_sql = "SELECT COUNT(*) as total FROM users";
                                $total_result = $conn->query($total_sql);
                                $total_row = $total_result ? $total_result->fetch_assoc() : ['total' => 0];
                                $total_records = (int)$total_row['total'];
                                $total_pages = $total_records > 0 ? ceil($total_records / $limit) : 1;

                                // Lấy dữ liệu trang hiện tại
                                $sql = "SELECT id, name, email, phone, address, is_admin, created_at FROM users ORDER BY id DESC LIMIT ? OFFSET ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("ii", $limit, $offset);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                ?>
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Họ tên</th>
                                            <th>Email</th>
                                            <th>SĐT</th>
                                            <th>Địa chỉ</th>
                                            <th>Quyền</th>
                                            <th>Ngày tạo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($result && $result->num_rows > 0): ?>
                                            <?php while($row = $result->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?= $row['id'] ?></td>
                                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                                    <td><?= htmlspecialchars($row['phone']) ?></td>
                                                    <td><?= htmlspecialchars($row['address']) ?></td>
                                                    <td><?= $row['is_admin'] ? 'Admin' : 'User' ?></td>
                                                    <td><?= $row['created_at'] ?></td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr><td colspan="7">Không có tài khoản nào.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                                <!-- Pagination -->
                                <nav>
                                    <ul class="pagination justify-content-center">
                                        <?php if ($page > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=user&p=<?= $page - 1 ?>">Trước</a>
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
                                                <a class="page-link" href="?page=user&p=<?= $i ?>"><?= $i ?></a>
                                            </li>
                                        <?php endfor; ?>

                                        <?php if ($page < $total_pages): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=user&p=<?= $page + 1 ?>">Sau</a>
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