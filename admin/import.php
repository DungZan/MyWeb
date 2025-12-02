<?php
require_once __DIR__ . '/../includes/auth.php';

$errors = [];
$success = '';

// Load reference data
$employees = [];
$employeeResult = $conn->query('SELECT STT, TenDangNhap, HoTen, LoaiTK FROM taikhoan ORDER BY HoTen');
if ($employeeResult) {
    while ($row = $employeeResult->fetch_assoc()) {
        $employees[$row['STT']] = $row;
    }
}

$products = [];
$productResult = $conn->query('SELECT STT, TenSP FROM sanpham ORDER BY TenSP');
if ($productResult) {
    while ($row = $productResult->fetch_assoc()) {
        $products[$row['STT']] = $row;
    }
}

$pnkStatusOptions = [
    'DaNhap' => 'Đã nhập kho',
    'DangNhap' => 'Đang xử lý',
    'DaHuy' => 'Đã hủy',
];
$selectedPnkId = (isset($_GET['pnk']) && ctype_digit((string) $_GET['pnk'])) ? (int) $_GET['pnk'] : null;

$pnkStatus = $_POST['pnk_status'] ?? 'DaNhap';
$pnkDateInput = $_POST['pnk_date'] ?? date('Y-m-d\TH:i');
$employeeId = $_POST['employee_id'] ?? '';
$productIds = $_POST['product_id'] ?? [];
$quantities = $_POST['quantity'] ?? [];
$prices = $_POST['price'] ?? [];
$barcodes = $_POST['barcode'] ?? [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['import_action'])) {
    if (!array_key_exists($pnkStatus, $pnkStatusOptions)) {
        $errors[] = 'Trạng thái phiếu nhập không hợp lệ.';
    }

    $pnkDate = date('Y-m-d H:i:s');
    if (!empty($pnkDateInput)) {
        $dateTime = DateTime::createFromFormat('Y-m-d\TH:i', $pnkDateInput);
        if ($dateTime) {
            $pnkDate = $dateTime->format('Y-m-d H:i:s');
        } else {
            $errors[] = 'Định dạng ngày giờ không hợp lệ.';
        }
    }

    if ($employeeId === '' || !ctype_digit((string) $employeeId) || !isset($employees[(int) $employeeId])) {
        $errors[] = 'Nhân viên phụ trách không hợp lệ.';
    }

    $lineItems = [];
    $rowCount = max(count($productIds), count($quantities), count($prices));
    for ($i = 0; $i < $rowCount; $i++) {
        $pid = $productIds[$i] ?? '';
        $qty = $quantities[$i] ?? '';
        $price = $prices[$i] ?? '';
        $barcode = trim($barcodes[$i] ?? '');

        if ($pid === '' && $qty === '' && $price === '' && $barcode === '') {
            continue;
        }

        if ($pid === '' || !ctype_digit((string) $pid) || !isset($products[(int) $pid])) {
            $errors[] = 'Sản phẩm ở dòng ' . ($i + 1) . ' không hợp lệ.';
            continue;
        }
        if ($qty === '' || !ctype_digit((string) $qty) || (int) $qty <= 0) {
            $errors[] = 'Số lượng ở dòng ' . ($i + 1) . ' phải là số nguyên dương.';
            continue;
        }
        if ($price === '' || !is_numeric($price) || (float) $price <= 0) {
            $errors[] = 'Giá nhập ở dòng ' . ($i + 1) . ' phải lớn hơn 0.';
            continue;
        }

        $lineItems[] = [
            'product_id' => (int) $pid,
            'quantity' => (int) $qty,
            'price' => (float) $price,
            'barcode' => $barcode,
        ];
    }

    if (!$lineItems) {
        $errors[] = 'Cần ít nhất một dòng sản phẩm hợp lệ.';
    }

    if (!$errors) {
        try {
            $conn->begin_transaction();

            $pnkStmt = $conn->prepare('INSERT INTO pnk (NgayGioNhap, TrangThai, MaNV) VALUES (?, ?, ?)');
            $employeeIdValue = (int) $employeeId;
            $pnkStmt->bind_param('ssi', $pnkDate, $pnkStatus, $employeeIdValue);
            if (!$pnkStmt->execute()) {
                throw new RuntimeException('Không thể tạo phiếu nhập: ' . $pnkStmt->error);
            }
            $pnkId = $conn->insert_id;
            $pnkStmt->close();

            $detailStmt = $conn->prepare('INSERT INTO ctpnk (MaPNK, MaSP, SoLuong, GiaNhap, MaVach) VALUES (?, ?, ?, ?, ?)');
            $stockStmt = $conn->prepare('UPDATE sanpham SET SoLuongTon = SoLuongTon + ? WHERE STT = ?');

            foreach ($lineItems as $item) {
                $barcodeValue = $item['barcode'] !== '' ? $item['barcode'] : null;
                $detailStmt->bind_param(
                    'iiids',
                    $pnkId,
                    $item['product_id'],
                    $item['quantity'],
                    $item['price'],
                    $barcodeValue
                );
                if (!$detailStmt->execute()) {
                    throw new RuntimeException('Không thể lưu chi tiết phiếu nhập: ' . $detailStmt->error);
                }

                $stockStmt->bind_param('ii', $item['quantity'], $item['product_id']);
                if (!$stockStmt->execute()) {
                    throw new RuntimeException('Không thể cập nhật tồn kho: ' . $stockStmt->error);
                }
            }

            $detailStmt->close();
            $stockStmt->close();

            $conn->commit();

            $success = 'Đã tạo phiếu nhập #' . $pnkId . ' và cập nhật tồn kho.';
            $pnkStatus = 'DaNhap';
            $pnkDateInput = date('Y-m-d\TH:i');
            $employeeId = '';
            $productIds = $quantities = $prices = $barcodes = [];
        } catch (Throwable $e) {
            $conn->rollback();
            $errors[] = $e->getMessage();
        }
    }
}

$itemRowCount = max(1, count($productIds));

$recentImports = [];
$recentImportResult = $conn->query(
    "SELECT p.STT, p.NgayGioNhap, p.TrangThai, tk.HoTen, tk.TenDangNhap,
            COALESCE(SUM(c.SoLuong), 0) AS total_qty, COUNT(c.MaSP) AS line_count
     FROM pnk p
     LEFT JOIN taikhoan tk ON p.MaNV = tk.STT
     LEFT JOIN ctpnk c ON c.MaPNK = p.STT
     GROUP BY p.STT
     ORDER BY p.NgayGioNhap DESC
     LIMIT 8"
);
if ($recentImportResult) {
    while ($row = $recentImportResult->fetch_assoc()) {
        $recentImports[] = $row;
    }
}

if ($selectedPnkId === null && $recentImports) {
    $selectedPnkId = (int) $recentImports[0]['STT'];
}

$selectedPnk = null;
$selectedPnkDetails = [];
if ($selectedPnkId !== null) {
    $pnkStmt = $conn->prepare(
        'SELECT p.STT, p.NgayGioNhap, p.TrangThai, tk.HoTen, tk.TenDangNhap
         FROM pnk p
         LEFT JOIN taikhoan tk ON p.MaNV = tk.STT
         WHERE p.STT = ?'
    );
    if ($pnkStmt) {
        $pnkStmt->bind_param('i', $selectedPnkId);
        if ($pnkStmt->execute()) {
            $result = $pnkStmt->get_result();
            $selectedPnk = $result ? $result->fetch_assoc() : null;
        }
        $pnkStmt->close();
    } else {
        $errors[] = 'Không thể tải phiếu nhập: ' . $conn->error;
    }

    if ($selectedPnk) {
        $detailStmt = $conn->prepare(
            'SELECT c.MaPNK, c.MaSP, c.SoLuong, c.GiaNhap, c.MaVach, sp.TenSP
             FROM ctpnk c
             JOIN sanpham sp ON sp.STT = c.MaSP
             WHERE c.MaPNK = ?
             ORDER BY c.MaPNK, c.MaSP'
        );
        if ($detailStmt) {
            $detailStmt->bind_param('i', $selectedPnkId);
            if ($detailStmt->execute()) {
                $detailResult = $detailStmt->get_result();
                while ($detailResult && ($row = $detailResult->fetch_assoc())) {
                    $selectedPnkDetails[] = $row;
                }
            }
            $detailStmt->close();
        } else {
            $errors[] = 'Không thể tải chi tiết phiếu nhập: ' . $conn->error;
        }
    }
}

$recentDetails = [];
$recentDetailResult = $conn->query(
    "SELECT c.MaPNK, c.MaSP, c.SoLuong, c.GiaNhap, c.MaVach, sp.TenSP
     FROM ctpnk c
     JOIN sanpham sp ON c.MaSP = sp.STT
     ORDER BY c.STT DESC
     LIMIT 10"
);
if ($recentDetailResult) {
    while ($row = $recentDetailResult->fetch_assoc()) {
        $recentDetails[] = $row;
    }
}
?>
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Phiếu nhập kho mới</h6>
        <small class="text-muted">Tạo bản ghi cho bảng pnk, ctpnk và cập nhật tồn kho</small>
    </div>
    <div class="card-body">
        <?php if ($errors): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <form method="post" id="import-form" novalidate>
            <input type="hidden" name="import_action" value="create">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Ngày giờ nhập</label>
                    <input type="datetime-local" name="pnk_date" class="form-control" value="<?= htmlspecialchars($pnkDateInput) ?>" required>
                </div>
                <div class="form-group col-md-4">
                    <label>Trạng thái</label>
                    <select name="pnk_status" class="form-control">
                        <?php foreach ($pnkStatusOptions as $value => $label): ?>
                            <option value="<?= $value ?>" <?= $pnkStatus === $value ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>Nhân viên phụ trách</label>
                    <select name="employee_id" class="form-control" required>
                        <option value="">-- Chọn nhân viên --</option>
                        <?php foreach ($employees as $employee): ?>
                            <option value="<?= $employee['STT'] ?>" <?= ((string) $employeeId === (string) $employee['STT']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($employee['HoTen'] ?: $employee['TenDangNhap']) ?> (<?= htmlspecialchars($employee['LoaiTK'] ?? '') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <h6 class="font-weight-bold mt-4">Danh sách sản phẩm nhập</h6>
            <div id="itemRows">
                <?php $existingRows = max($itemRowCount, 1); ?>
                <?php for ($i = 0; $i < $existingRows; $i++): ?>
                    <div class="form-row align-items-end border rounded p-3 mb-3 item-row">
                        <div class="form-group col-md-4">
                            <label>Sản phẩm</label>
                            <select name="product_id[]" class="form-control" required>
                                <option value="">-- Chọn sản phẩm --</option>
                                <?php foreach ($products as $product): ?>
                                    <option value="<?= $product['STT'] ?>" <?= isset($productIds[$i]) && (string) $productIds[$i] === (string) $product['STT'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($product['TenSP']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label>Số lượng</label>
                            <input type="number" name="quantity[]" min="1" class="form-control" value="<?= htmlspecialchars($quantities[$i] ?? '') ?>" required>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Giá nhập</label>
                            <input type="number" step="0.01" min="0" name="price[]" class="form-control" value="<?= htmlspecialchars($prices[$i] ?? '') ?>" required>
                        </div>
                        <div class="form-group col-md-2">
                            <label>Mã vạch/Batch</label>
                            <input type="text" name="barcode[]" class="form-control" value="<?= htmlspecialchars($barcodes[$i] ?? '') ?>">
                        </div>
                        <div class="form-group col-md-1 text-right">
                            <button type="button" class="btn btn-outline-danger remove-row" title="Xóa dòng">&times;</button>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
            <button type="button" class="btn btn-outline-primary mb-3" id="addRow">Thêm dòng</button>

            <div class="text-right">
                <button type="submit" class="btn btn-success">Lưu phiếu nhập</button>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <?php if ($selectedPnkId !== null): ?>
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Chi tiết phiếu nhập <?= $selectedPnk ? '#' . htmlspecialchars($selectedPnk['STT']) : '' ?>
                    </h6>
                    <div>
                        <?php if ($selectedPnk && isset($pnkStatusOptions[$selectedPnk['TrangThai']])): ?>
                            <span class="badge badge-info mr-2"><?= htmlspecialchars($pnkStatusOptions[$selectedPnk['TrangThai']]) ?></span>
                        <?php endif; ?>
                        <a href="?page=import" class="btn btn-sm btn-outline-secondary">Đóng</a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if ($selectedPnk): ?>
                        <div class="mb-3">
                            <p class="mb-1"><strong>Ngày nhập:</strong> <?= htmlspecialchars($selectedPnk['NgayGioNhap']) ?></p>
                            <p class="mb-1"><strong>Nhân viên:</strong> <?= htmlspecialchars($selectedPnk['HoTen'] ?? $selectedPnk['TenDangNhap'] ?? 'Không rõ') ?></p>
                        </div>
                        <?php if ($selectedPnkDetails): ?>
                            <?php
                                $sumQty = 0;
                                $sumCost = 0;
                                foreach ($selectedPnkDetails as $detail) {
                                    $sumQty += (int) $detail['SoLuong'];
                                    $sumCost += (float) $detail['SoLuong'] * (float) $detail['GiaNhap'];
                                }
                            ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Sản phẩm</th>
                                            <th>Số lượng</th>
                                            <th>Giá nhập</th>
                                            <th>Thành tiền</th>
                                            <th>Mã vạch</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($selectedPnkDetails as $index => $detail): ?>
                                            <tr>
                                                <td><?= $index + 1 ?></td>
                                                <td><?= htmlspecialchars($detail['TenSP']) ?></td>
                                                <td><?= (int) $detail['SoLuong'] ?></td>
                                                <td><?= number_format((float) $detail['GiaNhap'], 0, ',', '.') ?> đ</td>
                                                <td><?= number_format((float) $detail['GiaNhap'] * (int) $detail['SoLuong'], 0, ',', '.') ?> đ</td>
                                                <td><?= htmlspecialchars($detail['MaVach'] ?? '') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="2">Tổng cộng</th>
                                            <th><?= $sumQty ?></th>
                                            <th></th>
                                            <th><?= number_format($sumCost, 0, ',', '.') ?> đ</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        <?php else: ?>
                            <p class="mb-0">Phiếu nhập này chưa có chi tiết.</p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="mb-0 text-danger">Không tìm thấy phiếu nhập yêu cầu.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Phiếu nhập gần đây</h6>
            </div>
            <div class="card-body">
                <?php if ($recentImports): ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Ngày nhập</th>
                                    <th>Nhân viên</th>
                                    <th>Trạng thái</th>
                                    <th>Số dòng</th>
                                    <th>Tổng SL</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentImports as $import): ?>
                                    <tr>
                                        <td><?= $import['STT'] ?></td>
                                        <td><?= htmlspecialchars($import['NgayGioNhap']) ?></td>
                                        <td><?= htmlspecialchars($import['HoTen'] ?? $import['TenDangNhap'] ?? 'Không rõ') ?></td>
                                        <td><?= htmlspecialchars($pnkStatusOptions[$import['TrangThai']] ?? $import['TrangThai']) ?></td>
                                        <td><?= (int) $import['line_count'] ?></td>
                                        <td><?= (int) $import['total_qty'] ?></td>
                                        <td>
                                            <a href="?page=import&pnk=<?= $import['STT'] ?>" class="btn btn-sm btn-outline-primary">Xem</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="mb-0">Chưa có phiếu nhập nào.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Chi tiết nhập mới nhất</h6>
            </div>
            <div class="card-body">
                <?php if ($recentDetails): ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr>
                                    <th>Phiếu</th>
                                    <th>Sản phẩm</th>
                                    <th>SL</th>
                                    <th>Giá nhập</th>
                                    <th>Mã vạch</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentDetails as $detail): ?>
                                    <tr>
                                        <td><?= $detail['MaPNK'] ?></td>
                                        <td><?= htmlspecialchars($detail['TenSP']) ?></td>
                                        <td><?= (int) $detail['SoLuong'] ?></td>
                                        <td><?= number_format((float) $detail['GiaNhap'], 0, ',', '.') ?> đ</td>
                                        <td><?= htmlspecialchars($detail['MaVach'] ?? '') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="mb-0">Chưa có dữ liệu chi tiết.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<template id="rowTemplate">
    <div class="form-row align-items-end border rounded p-3 mb-3 item-row">
        <div class="form-group col-md-4">
            <label>Sản phẩm</label>
            <select name="product_id[]" class="form-control" required>
                <option value="">-- Chọn sản phẩm --</option>
                <?php foreach ($products as $product): ?>
                    <option value="<?= $product['STT'] ?>"><?= htmlspecialchars($product['TenSP']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group col-md-2">
            <label>Số lượng</label>
            <input type="number" name="quantity[]" min="1" class="form-control" required>
        </div>
        <div class="form-group col-md-3">
            <label>Giá nhập</label>
            <input type="number" step="0.01" min="0" name="price[]" class="form-control" required>
        </div>
        <div class="form-group col-md-2">
            <label>Mã vạch/Batch</label>
            <input type="text" name="barcode[]" class="form-control">
        </div>
        <div class="form-group col-md-1 text-right">
            <button type="button" class="btn btn-outline-danger remove-row" title="Xóa dòng">&times;</button>
        </div>
    </div>
</template>

<script>
(function() {
    const form = document.getElementById('import-form');
    const addRowBtn = document.getElementById('addRow');
    const rowContainer = document.getElementById('itemRows');
    const template = document.getElementById('rowTemplate');

    if (!form || !addRowBtn || !rowContainer || !template) {
        return;
    }

    addRowBtn.addEventListener('click', function() {
        const clone = template.content.cloneNode(true);
        rowContainer.appendChild(clone);
    });

    rowContainer.addEventListener('click', function(event) {
        if (event.target.classList.contains('remove-row')) {
            const rows = rowContainer.querySelectorAll('.item-row');
            if (rows.length <= 1) {
                alert('Cần ít nhất một dòng sản phẩm.');
                return;
            }
            event.target.closest('.item-row').remove();
        }
    });
})();
</script>
