<?php
require_once __DIR__ . '/../includes/auth.php';

$errors = [];
$success = '';
$productError = '';
$isEditing = false;
$editingProductId = null;
$detailErrors = [];
$detailSuccess = '';
$detailProductId = null;
$categoryErrors = [];
$categorySuccess = '';
$brandErrors = [];
$brandSuccess = '';
$detailParam = null;
$perPage = 10;
$currentPage = (isset($_GET['p']) && ctype_digit((string) $_GET['p'])) ? (int) $_GET['p'] : 1;
if ($currentPage < 1) {
    $currentPage = 1;
}
$totalProducts = 0;
$totalPages = 1;
$offset = 0;
$name = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');
$price = $_POST['price'] ?? '';
$stock = $_POST['stock'] ?? '';
$image = trim($_POST['image'] ?? '');
$categoryId = $_POST['category_id'] ?? '';
$brandId = $_POST['brand_id'] ?? '';

$categories = [];
$brands = [];

if (isset($_GET['detail']) && ctype_digit((string) $_GET['detail'])) {
    $detailProductId = (int) $_GET['detail'];
    $detailParam = $detailProductId;
}

$productOptions = [];
$productOptionResult = $conn->query('SELECT STT, TenSP FROM sanpham ORDER BY TenSP');
if ($productOptionResult) {
    while ($row = $productOptionResult->fetch_assoc()) {
        $productOptions[] = $row;
    }
}
if (!$detailProductId && $productOptions) {
    $detailProductId = (int) $productOptions[0]['STT'];
}
if ($detailProductId !== null) {
    $detailParam = $detailProductId;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['detail_action'])) {
    $detailAction = $_POST['detail_action'];
    $detailProductIdValue = $_POST['detail_product_id'] ?? '';

    if (!ctype_digit((string) $detailProductIdValue)) {
        $detailErrors[] = 'Sản phẩm cần cập nhật chi tiết không hợp lệ.';
    } else {
        $detailProductId = (int) $detailProductIdValue;
        $detailParam = $detailProductId;
        $detailId = $_POST['detail_id'] ?? '';
        $detailBarcode = trim($_POST['detail_barcode'] ?? '');
        $detailStatus = trim($_POST['detail_status'] ?? '');
        $detailNote = trim($_POST['detail_note'] ?? '');

        if ($detailAction === 'add') {
            if ($detailBarcode === '') {
                $detailErrors[] = 'Mã vạch không được để trống.';
            }
            if (!$detailErrors) {
                $stmt = $conn->prepare('INSERT INTO ctsanpham (MaSP, MaVach, TrangThaiBan, GhiChu) VALUES (?, ?, ?, ?)');
                $stmt->bind_param('isss', $detailProductId, $detailBarcode, $detailStatus, $detailNote);
                if ($stmt->execute()) {
                    $detailSuccess = 'Đã thêm chi tiết sản phẩm mới!';
                } else {
                    $detailErrors[] = 'Không thể thêm chi tiết: ' . $stmt->error;
                }
                $stmt->close();
            }
        } elseif (in_array($detailAction, ['edit', 'delete'], true)) {
            if (!ctype_digit((string) $detailId)) {
                $detailErrors[] = 'Chi tiết sản phẩm không hợp lệ.';
            } elseif ($detailAction === 'edit') {
                if ($detailBarcode === '') {
                    $detailErrors[] = 'Mã vạch không được để trống.';
                }
                if (!$detailErrors) {
                    $stmt = $conn->prepare('UPDATE ctsanpham SET MaVach = ?, TrangThaiBan = ?, GhiChu = ? WHERE STT = ? AND MaSP = ?');
                    $detailIdValue = (int) $detailId;
                    $stmt->bind_param('sssii', $detailBarcode, $detailStatus, $detailNote, $detailIdValue, $detailProductId);
                    if ($stmt->execute()) {
                        $detailSuccess = 'Đã cập nhật chi tiết sản phẩm!';
                    } else {
                        $detailErrors[] = 'Không thể cập nhật chi tiết: ' . $stmt->error;
                    }
                    $stmt->close();
                }
            } else {
                $stmt = $conn->prepare('DELETE FROM ctsanpham WHERE STT = ? AND MaSP = ?');
                $detailIdValue = (int) $detailId;
                $stmt->bind_param('ii', $detailIdValue, $detailProductId);
                if ($stmt->execute() && $stmt->affected_rows) {
                    $detailSuccess = 'Đã xóa chi tiết sản phẩm!';
                } else {
                    $detailErrors[] = 'Không thể xóa chi tiết. Vui lòng thử lại.';
                }
                $stmt->close();
            }
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cat_action'])) {
    $catAction = $_POST['cat_action'];
    $catName = trim($_POST['cat_name'] ?? '');
    $catDescription = trim($_POST['cat_description'] ?? '');
    $catId = $_POST['cat_id'] ?? '';

    if ($catAction === 'add') {
        if ($catName === '') {
            $categoryErrors[] = 'Tên loại sản phẩm không được để trống.';
        }
        if (!$categoryErrors) {
            $stmt = $conn->prepare('INSERT INTO loaisp (Ten, MoTa) VALUES (?, ?)');
            $stmt->bind_param('ss', $catName, $catDescription);
            if ($stmt->execute()) {
                $categorySuccess = 'Đã thêm loại sản phẩm mới!';
            } else {
                $categoryErrors[] = 'Không thể thêm loại sản phẩm: ' . $stmt->error;
            }
            $stmt->close();
        }
    } elseif (in_array($catAction, ['edit', 'delete'], true)) {
        if (!ctype_digit((string) $catId)) {
            $categoryErrors[] = 'Loại sản phẩm không hợp lệ.';
        } elseif ($catAction === 'edit') {
            if ($catName === '') {
                $categoryErrors[] = 'Tên loại sản phẩm không được để trống.';
            }
            if (!$categoryErrors) {
                $stmt = $conn->prepare('UPDATE loaisp SET Ten = ?, MoTa = ? WHERE STT = ?');
                $catIdValue = (int) $catId;
                $stmt->bind_param('ssi', $catName, $catDescription, $catIdValue);
                if ($stmt->execute()) {
                    $categorySuccess = 'Đã cập nhật loại sản phẩm!';
                } else {
                    $categoryErrors[] = 'Không thể cập nhật loại sản phẩm: ' . $stmt->error;
                }
                $stmt->close();
            }
        } else {
            $stmt = $conn->prepare('DELETE FROM loaisp WHERE STT = ?');
            $catIdValue = (int) $catId;
            $stmt->bind_param('i', $catIdValue);
            if ($stmt->execute() && $stmt->affected_rows) {
                $categorySuccess = 'Đã xóa loại sản phẩm!';
            } else {
                $categoryErrors[] = 'Không thể xóa loại sản phẩm. Vui lòng thử lại.';
            }
            $stmt->close();
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['brand_action'])) {
    $brandAction = $_POST['brand_action'];
    $brandName = trim($_POST['brand_name'] ?? '');
    $brandDescription = trim($_POST['brand_description'] ?? '');
    $brandId = $_POST['brand_id'] ?? '';

    if ($brandAction === 'add') {
        if ($brandName === '') {
            $brandErrors[] = 'Tên thương hiệu không được để trống.';
        }
        if (!$brandErrors) {
            $stmt = $conn->prepare('INSERT INTO th (Ten, MoTa) VALUES (?, ?)');
            $stmt->bind_param('ss', $brandName, $brandDescription);
            if ($stmt->execute()) {
                $brandSuccess = 'Đã thêm thương hiệu mới!';
            } else {
                $brandErrors[] = 'Không thể thêm thương hiệu: ' . $stmt->error;
            }
            $stmt->close();
        }
    } elseif (in_array($brandAction, ['edit', 'delete'], true)) {
        if (!ctype_digit((string) $brandId)) {
            $brandErrors[] = 'Thương hiệu không hợp lệ.';
        } elseif ($brandAction === 'edit') {
            if ($brandName === '') {
                $brandErrors[] = 'Tên thương hiệu không được để trống.';
            }
            if (!$brandErrors) {
                $stmt = $conn->prepare('UPDATE th SET Ten = ?, MoTa = ? WHERE STT = ?');
                $brandIdValue = (int) $brandId;
                $stmt->bind_param('ssi', $brandName, $brandDescription, $brandIdValue);
                if ($stmt->execute()) {
                    $brandSuccess = 'Đã cập nhật thương hiệu!';
                } else {
                    $brandErrors[] = 'Không thể cập nhật thương hiệu: ' . $stmt->error;
                }
                $stmt->close();
            }
        } else {
            $stmt = $conn->prepare('DELETE FROM th WHERE STT = ?');
            $brandIdValue = (int) $brandId;
            $stmt->bind_param('i', $brandIdValue);
            if ($stmt->execute() && $stmt->affected_rows) {
                $brandSuccess = 'Đã xóa thương hiệu!';
            } else {
                $brandErrors[] = 'Không thể xóa thương hiệu. Vui lòng thử lại.';
            }
            $stmt->close();
        }
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'create';
    $productId = $_POST['product_id'] ?? '';

    if ($action === 'delete') {
        if (!ctype_digit((string)$productId)) {
            $errors[] = 'Sản phẩm cần xóa không hợp lệ.';
        } else {
            $stmt = $conn->prepare('DELETE FROM sanpham WHERE STT = ?');
            $productIdValue = (int) $productId;
            $stmt->bind_param('i', $productIdValue);
            if ($stmt->execute() && $stmt->affected_rows) {
                $success = 'Đã xóa sản phẩm thành công!';
                $name = $description = $image = '';
                $price = $stock = '';
                $categoryId = $brandId = '';
            } else {
                $errors[] = 'Không thể xóa sản phẩm. Vui lòng thử lại.';
            }
            $stmt->close();
        }
    } else {
        if ($action === 'update') {
            if (!ctype_digit((string)$productId)) {
                $errors[] = 'Sản phẩm cần cập nhật không hợp lệ.';
            } else {
                $editingProductId = (int) $productId;
                $isEditing = true;
            }
        }

        if ($name === '') {
            $errors[] = 'Tên sản phẩm không được để trống.';
        }
        if ($price === '' || !is_numeric($price) || floatval($price) <= 0) {
            $errors[] = 'Giá phải là số lớn hơn 0.';
        }
        if ($stock === '' || !ctype_digit((string)$stock) || intval($stock) < 0) {
            $errors[] = 'Tồn kho phải là số nguyên không âm.';
        }
        if ($categoryId !== '' && !ctype_digit((string)$categoryId)) {
            $errors[] = 'Danh mục không hợp lệ.';
        }
        if ($brandId !== '' && !ctype_digit((string)$brandId)) {
            $errors[] = 'Thương hiệu không hợp lệ.';
        }

        if (!$errors) {
            $priceValue = floatval($price);
            $stockValue = intval($stock);
            $categoryValue = $categoryId !== '' ? intval($categoryId) : null;
            $brandValue = $brandId !== '' ? intval($brandId) : null;

            if ($action === 'update' && $editingProductId) {
                $stmt = $conn->prepare('UPDATE sanpham SET TenSP = ?, MoTa = ?, HinhAnh = ?, GiaMuaCoBan = ?, SoLuongTon = ?, MaLoai = ?, MaTH = ? WHERE STT = ?');
                $stmt->bind_param(
                    'sssdiiii',
                    $name,
                    $description,
                    $image,
                    $priceValue,
                    $stockValue,
                    $categoryValue,
                    $brandValue,
                    $editingProductId
                );
                if ($stmt->execute()) {
                    $success = 'Cập nhật sản phẩm thành công!';
                    $isEditing = false;
                    $editingProductId = null;
                    $name = $description = $image = '';
                    $price = $stock = '';
                    $categoryId = $brandId = '';
                } else {
                    $errors[] = 'Không thể cập nhật sản phẩm: ' . $stmt->error;
                }
                $stmt->close();
            } else {
                $stmt = $conn->prepare('INSERT INTO sanpham (TenSP, MoTa, HinhAnh, GiaMuaCoBan, SoLuongTon, MaLoai, MaTH) VALUES (?, ?, ?, ?, ?, ?, ?)');
                $stmt->bind_param(
                    'sssdiii',
                    $name,
                    $description,
                    $image,
                    $priceValue,
                    $stockValue,
                    $categoryValue,
                    $brandValue
                );
                if ($stmt->execute()) {
                    $success = 'Thêm sản phẩm thành công!';
                    $name = $description = $image = '';
                    $price = $stock = '';
                    $categoryId = $brandId = '';
                } else {
                    $errors[] = 'Không thể thêm sản phẩm: ' . $stmt->error;
                }
                $stmt->close();
            }
        }
    }
}

if (!$isEditing && $_SERVER['REQUEST_METHOD'] !== 'POST' && isset($_GET['edit']) && ctype_digit((string) $_GET['edit'])) {
    $editingProductId = (int) $_GET['edit'];
    $stmt = $conn->prepare('SELECT STT, TenSP, MoTa, HinhAnh, GiaMuaCoBan, SoLuongTon, MaLoai, MaTH FROM sanpham WHERE STT = ?');
    $stmt->bind_param('i', $editingProductId);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result && ($productData = $result->fetch_assoc())) {
            $isEditing = true;
            $name = $productData['TenSP'];
            $description = $productData['MoTa'] ?? '';
            $image = $productData['HinhAnh'] ?? '';
            $price = $productData['GiaMuaCoBan'];
            $stock = $productData['SoLuongTon'];
            $categoryId = $productData['MaLoai'] ?? '';
            $brandId = $productData['MaTH'] ?? '';
        } else {
            $errors[] = 'Không tìm thấy sản phẩm cần sửa.';
            $editingProductId = null;
        }
    }
    $stmt->close();
}

$countResult = $conn->query('SELECT COUNT(*) AS total FROM sanpham');
if ($countResult && ($countRow = $countResult->fetch_assoc())) {
    $totalProducts = (int) ($countRow['total'] ?? 0);
}
$totalPages = max(1, (int) ceil($totalProducts / $perPage));
if ($currentPage > $totalPages) {
    $currentPage = $totalPages;
}
$offset = ($currentPage - 1) * $perPage;
if ($offset < 0) {
    $offset = 0;
}

$products = [];
$productQuery = "SELECT sp.STT, sp.TenSP, sp.GiaMuaCoBan, sp.SoLuongTon, sp.HinhAnh, sp.MoTa, l.Ten AS loai, t.Ten AS thuong_hieu, NULL AS created_at
                 FROM sanpham sp
                 LEFT JOIN loaisp l ON sp.MaLoai = l.STT
                 LEFT JOIN th t ON sp.MaTH = t.STT
                 ORDER BY sp.STT DESC
                 LIMIT $perPage OFFSET $offset";
$productResult = $conn->query($productQuery);
if ($productResult) {
    while ($row = $productResult->fetch_assoc()) {
        $products[] = $row;
    }
} else {
    $productError = 'Không thể tải danh sách sản phẩm: ' . $conn->error;
}

$detailProductInfo = null;
$detailRows = [];
if ($detailProductId) {
    $stmt = $conn->prepare('SELECT sp.STT, sp.TenSP, sp.MoTa, sp.HinhAnh, sp.GiaMuaCoBan, sp.SoLuongTon, l.Ten AS loai, t.Ten AS thuong_hieu FROM sanpham sp LEFT JOIN loaisp l ON sp.MaLoai = l.STT LEFT JOIN th t ON sp.MaTH = t.STT WHERE sp.STT = ?');
    $stmt->bind_param('i', $detailProductId);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $detailProductInfo = $result ? $result->fetch_assoc() : null;
    }
    $stmt->close();

    $detailStmt = $conn->prepare('SELECT STT, MaVach, TrangThaiBan, GhiChu FROM ctsanpham WHERE MaSP = ? ORDER BY STT DESC');
    $detailStmt->bind_param('i', $detailProductId);
    if ($detailStmt->execute()) {
        $detailResult = $detailStmt->get_result();
        while ($detailResult && ($row = $detailResult->fetch_assoc())) {
            $detailRows[] = $row;
        }
    }
    $detailStmt->close();
}

$categoryResult = $conn->query('SELECT STT, Ten, MoTa FROM loaisp ORDER BY Ten');
if ($categoryResult) {
    while ($row = $categoryResult->fetch_assoc()) {
        $categories[] = $row;
    }
}

$brandResult = $conn->query('SELECT STT, Ten, MoTa FROM th ORDER BY Ten');
if ($brandResult) {
    while ($row = $brandResult->fetch_assoc()) {
        $brands[] = $row;
    }
}

$paginationBaseParams = ['page' => 'products'];
if ($detailParam !== null) {
    $paginationBaseParams['detail'] = $detailParam;
}
if (isset($_GET['edit']) && ctype_digit((string) $_GET['edit'])) {
    $paginationBaseParams['edit'] = (int) $_GET['edit'];
}
$buildPageLink = function (int $pageNumber) use ($paginationBaseParams): string {
    $params = $paginationBaseParams;
    $params['p'] = max(1, $pageNumber);
    return '?' . http_build_query($params);
};
$cancelEditParams = $paginationBaseParams;
unset($cancelEditParams['edit']);
if ($currentPage > 1) {
    $cancelEditParams['p'] = $currentPage;
} else {
    unset($cancelEditParams['p']);
}
$cancelEditUrl = '?' . http_build_query($cancelEditParams);
$currentPageParam = $currentPage;
?>
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <?= $isEditing ? 'Chỉnh sửa sản phẩm' : 'Thêm sản phẩm mới' ?>
        </h6>
        <div class="d-flex">
            <a href="?page=import" class="btn btn-sm btn-outline-success mr-2">Đi đến nhập kho</a>
            <?php if ($isEditing): ?>
                <a href="<?= htmlspecialchars($cancelEditUrl) ?>" class="btn btn-sm btn-outline-secondary">Hủy chỉnh sửa</a>
            <?php endif; ?>
        </div>
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
        <form method="post" novalidate>
            <input type="hidden" name="action" value="<?= $isEditing ? 'update' : 'create' ?>">
            <?php if ($isEditing && $editingProductId): ?>
                <input type="hidden" name="product_id" value="<?= (int) $editingProductId ?>">
            <?php endif; ?>
            <div class="form-group">
                <label for="name">Tên sản phẩm</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Mô tả</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($description) ?></textarea>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="price">Giá nhập (VNĐ)</label>
                    <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?= htmlspecialchars($price) ?>" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="stock">Tồn kho</label>
                    <input type="number" class="form-control" id="stock" name="stock" value="<?= htmlspecialchars($stock) ?>" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="category_id">Danh mục</label>
                    <select class="form-control" id="category_id" name="category_id">
                        <option value="">-- Chưa chọn --</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['STT'] ?>" <?= ($categoryId == $category['STT']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category['Ten']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="brand_id">Thương hiệu</label>
                    <select class="form-control" id="brand_id" name="brand_id">
                        <option value="">-- Chưa chọn --</option>
                        <?php foreach ($brands as $brand): ?>
                            <option value="<?= $brand['STT'] ?>" <?= ($brandId == $brand['STT']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($brand['Ten']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="image">Đường dẫn hình ảnh</label>
                    <input type="text" class="form-control" id="image" name="image" value="<?= htmlspecialchars($image) ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">
                <?= $isEditing ? 'Cập nhật sản phẩm' : 'Lưu sản phẩm' ?>
            </button>
        </form>
    </div>
</div>

<div class="card shadow">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Danh sách sản phẩm mới nhất</h6>
    </div>
    <div class="card-body">
        <?php if ($productError): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($productError) ?></div>
        <?php endif; ?>
        <?php if ($products): ?>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tên</th>
                            <th>Giá</th>
                            <th>Tồn kho</th>
                            <th>Danh mục</th>
                            <th>Thương hiệu</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $item): ?>
                            <tr>
                                <td><?= $item['STT'] ?></td>
                                <td><?= htmlspecialchars($item['TenSP']) ?></td>
                                <td><?= number_format((float)$item['GiaMuaCoBan'], 0, ',', '.') ?> đ</td>
                                <td><?= $item['SoLuongTon'] ?></td>
                                <td><?= htmlspecialchars($item['loai'] ?? 'Chưa phân loại') ?></td>
                                <td><?= htmlspecialchars($item['thuong_hieu'] ?? 'Không rõ') ?></td>
                                <td><?= htmlspecialchars($item['created_at'] ?? '--') ?: '--' ?></td>
                                <td>
                                    <a href="?page=products&edit=<?= $item['STT'] ?>" class="btn btn-sm btn-outline-primary">Sửa</a>
                                    <form method="post" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="product_id" value="<?= $item['STT'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if ($totalPages > 1 && $totalProducts > 0): ?>
                <?php
                    $pageWindow = 2;
                    $startPage = max(1, $currentPage - $pageWindow);
                    $endPage = min($totalPages, $currentPage + $pageWindow);
                ?>
                <nav aria-label="Phân trang sản phẩm" class="mt-3">
                    <ul class="pagination justify-content-center mb-0">
                        <li class="page-item <?= $currentPage === 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= htmlspecialchars($buildPageLink(max(1, $currentPage - 1))) ?>" aria-label="Trang trước">&laquo;</a>
                        </li>
                        <?php if ($startPage > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= htmlspecialchars($buildPageLink(1)) ?>">1</a>
                            </li>
                            <?php if ($startPage > 2): ?>
                                <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php for ($page = $startPage; $page <= $endPage; $page++): ?>
                            <?php if ($page === $currentPage): ?>
                                <li class="page-item active" aria-current="page"><span class="page-link"><?= $page ?></span></li>
                            <?php else: ?>
                                <li class="page-item"><a class="page-link" href="<?= htmlspecialchars($buildPageLink($page)) ?>"><?= $page ?></a></li>
                            <?php endif; ?>
                        <?php endfor; ?>
                        <?php if ($endPage < $totalPages): ?>
                            <?php if ($endPage < $totalPages - 1): ?>
                                <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= htmlspecialchars($buildPageLink($totalPages)) ?>"><?= $totalPages ?></a>
                            </li>
                        <?php endif; ?>
                        <li class="page-item <?= $currentPage === $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= htmlspecialchars($buildPageLink(min($totalPages, $currentPage + 1))) ?>" aria-label="Trang sau">&raquo;</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php else: ?>
            <p class="mb-0">Chưa có sản phẩm nào.</p>
        <?php endif; ?>
    </div>
</div>

<div class="card shadow mt-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Chi tiết sản phẩm</h6>
        <form method="get" class="form-inline">
            <input type="hidden" name="page" value="products">
            <input type="hidden" name="p" value="<?= (int) $currentPageParam ?>">
            <?php if ($isEditing && $editingProductId): ?>
                <input type="hidden" name="edit" value="<?= (int) $editingProductId ?>">
            <?php endif; ?>
            <div class="form-group mb-0 mr-2">
                <select class="form-control" name="detail">
                    <?php foreach ($productOptions as $option): ?>
                        <option value="<?= $option['STT'] ?>" <?= ($detailProductId == $option['STT']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($option['TenSP']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-sm btn-outline-primary">Xem</button>
        </form>
    </div>
    <div class="card-body">
        <?php if ($detailErrors): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($detailErrors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <?php if ($detailSuccess): ?>
            <div class="alert alert-success"><?= htmlspecialchars($detailSuccess) ?></div>
        <?php endif; ?>

        <?php if ($detailProductInfo): ?>
            <div class="mb-4">
                <h6 class="font-weight-bold">Thông tin chung</h6>
                <p class="mb-1"><strong>Tên:</strong> <?= htmlspecialchars($detailProductInfo['TenSP']) ?></p>
                <p class="mb-1"><strong>Danh mục:</strong> <?= htmlspecialchars($detailProductInfo['loai'] ?? 'Chưa phân loại') ?></p>
                <p class="mb-1"><strong>Thương hiệu:</strong> <?= htmlspecialchars($detailProductInfo['thuong_hieu'] ?? 'Không rõ') ?></p>
                <p class="mb-1"><strong>Giá nhập:</strong> <?= number_format((float)$detailProductInfo['GiaMuaCoBan'], 0, ',', '.') ?> đ</p>
                <p class="mb-2"><strong>Tồn kho:</strong> <?= (int) $detailProductInfo['SoLuongTon'] ?></p>
                <?php if (!empty($detailProductInfo['MoTa'])): ?>
                    <p class="mb-0"><strong>Mô tả:</strong> <?= nl2br(htmlspecialchars($detailProductInfo['MoTa'])) ?></p>
                <?php endif; ?>
            </div>

            <h6 class="font-weight-bold">Danh sách mã chi tiết</h6>
            <?php if ($detailRows): ?>
                <?php foreach ($detailRows as $row): ?>
                    <form method="post" class="border rounded p-3 mb-3">
                        <input type="hidden" name="detail_id" value="<?= $row['STT'] ?>">
                        <input type="hidden" name="detail_product_id" value="<?= $detailProductId ?>">
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label>Mã vạch</label>
                                <input type="text" class="form-control" name="detail_barcode" value="<?= htmlspecialchars($row['MaVach']) ?>" required>
                            </div>
                            <div class="form-group col-md-2">
                                <label>Trạng thái</label>
                                <input type="text" class="form-control" name="detail_status" value="<?= htmlspecialchars($row['TrangThaiBan'] ?? '') ?>">
                            </div>
                            <div class="form-group col-md-5">
                                <label>Ghi chú</label>
                                <input type="text" class="form-control" name="detail_note" value="<?= htmlspecialchars($row['GhiChu'] ?? '') ?>">
                            </div>
                            <div class="form-group col-md-2">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" name="detail_action" value="edit" class="btn btn-sm btn-primary mr-2">Lưu</button>
                                    <button type="submit" name="detail_action" value="delete" class="btn btn-sm btn-danger" onclick="return confirm('Xóa chi tiết này?');">Xóa</button>
                                </div>
                            </div>
                        </div>
                    </form>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Chưa có chi tiết nào cho sản phẩm này.</p>
            <?php endif; ?>

            <h6 class="font-weight-bold mt-4">Thêm chi tiết mới</h6>
            <form method="post" class="border rounded p-3">
                <input type="hidden" name="detail_product_id" value="<?= $detailProductId ?>">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label>Mã vạch</label>
                        <input type="text" class="form-control" name="detail_barcode" required>
                    </div>
                    <div class="form-group col-md-2">
                        <label>Trạng thái</label>
                        <input type="text" class="form-control" name="detail_status">
                    </div>
                    <div class="form-group col-md-5">
                        <label>Ghi chú</label>
                        <input type="text" class="form-control" name="detail_note">
                    </div>
                    <div class="form-group col-md-2">
                        <label>&nbsp;</label>
                        <div>
                            <button type="submit" name="detail_action" value="add" class="btn btn-sm btn-success">Thêm mới</button>
                        </div>
                    </div>
                </div>
            </form>
        <?php else: ?>
            <p class="mb-0">Không tìm thấy sản phẩm để hiển thị chi tiết.</p>
        <?php endif; ?>
    </div>
</div>

<div class="card shadow mt-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Quản lý loại sản phẩm</h6>
    </div>
    <div class="card-body">
        <?php if ($categoryErrors): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($categoryErrors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <?php if ($categorySuccess): ?>
            <div class="alert alert-success"><?= htmlspecialchars($categorySuccess) ?></div>
        <?php endif; ?>

        <?php if ($categories): ?>
            <div class="table-responsive mb-3">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tên loại</th>
                            <th>Mô tả</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><?= $category['STT'] ?></td>
                                <td colspan="3">
                                    <form method="post" class="form-row align-items-center">
                                        <input type="hidden" name="cat_id" value="<?= $category['STT'] ?>">
                                        <div class="form-group col-md-3 mb-2">
                                            <input type="text" class="form-control" name="cat_name" value="<?= htmlspecialchars($category['Ten'] ?? '') ?>" required>
                                        </div>
                                        <div class="form-group col-md-6 mb-2">
                                            <input type="text" class="form-control" name="cat_description" value="<?= htmlspecialchars($category['MoTa'] ?? '') ?>">
                                        </div>
                                        <div class="form-group col-md-3 mb-2">
                                            <button type="submit" name="cat_action" value="edit" class="btn btn-sm btn-primary mr-2">Cập nhật</button>
                                            <button type="submit" name="cat_action" value="delete" class="btn btn-sm btn-danger" onclick="return confirm('Xóa loại sản phẩm này?');">Xóa</button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>Chưa có loại sản phẩm nào.</p>
        <?php endif; ?>

        <h6 class="font-weight-bold">Thêm loại sản phẩm mới</h6>
        <form method="post" class="form-row">
            <div class="form-group col-md-4">
                <label>Tên loại</label>
                <input type="text" class="form-control" name="cat_name" required>
            </div>
            <div class="form-group col-md-6">
                <label>Mô tả</label>
                <input type="text" class="form-control" name="cat_description">
            </div>
            <div class="form-group col-md-2 d-flex align-items-end">
                <button type="submit" name="cat_action" value="add" class="btn btn-success">Thêm</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow mt-4 mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Quản lý thương hiệu</h6>
    </div>
    <div class="card-body">
        <?php if ($brandErrors): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($brandErrors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <?php if ($brandSuccess): ?>
            <div class="alert alert-success"><?= htmlspecialchars($brandSuccess) ?></div>
        <?php endif; ?>

        <?php if ($brands): ?>
            <div class="table-responsive mb-3">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tên thương hiệu</th>
                            <th>Mô tả</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($brands as $brand): ?>
                            <tr>
                                <td><?= $brand['STT'] ?></td>
                                <td colspan="3">
                                    <form method="post" class="form-row align-items-center">
                                        <input type="hidden" name="brand_id" value="<?= $brand['STT'] ?>">
                                        <div class="form-group col-md-3 mb-2">
                                            <input type="text" class="form-control" name="brand_name" value="<?= htmlspecialchars($brand['Ten'] ?? '') ?>" required>
                                        </div>
                                        <div class="form-group col-md-6 mb-2">
                                            <input type="text" class="form-control" name="brand_description" value="<?= htmlspecialchars($brand['MoTa'] ?? '') ?>">
                                        </div>
                                        <div class="form-group col-md-3 mb-2">
                                            <button type="submit" name="brand_action" value="edit" class="btn btn-sm btn-primary mr-2">Cập nhật</button>
                                            <button type="submit" name="brand_action" value="delete" class="btn btn-sm btn-danger" onclick="return confirm('Xóa thương hiệu này?');">Xóa</button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>Chưa có thương hiệu nào.</p>
        <?php endif; ?>

        <h6 class="font-weight-bold">Thêm thương hiệu mới</h6>
        <form method="post" class="form-row">
            <div class="form-group col-md-4">
                <label>Tên thương hiệu</label>
                <input type="text" class="form-control" name="brand_name" required>
            </div>
            <div class="form-group col-md-6">
                <label>Mô tả</label>
                <input type="text" class="form-control" name="brand_description">
            </div>
            <div class="form-group col-md-2 d-flex align-items-end">
                <button type="submit" name="brand_action" value="add" class="btn btn-success">Thêm</button>
            </div>
        </form>
    </div>
</div>
