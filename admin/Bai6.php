<?php
// Bài 6: Tính giai thừa
$gt = '';

// Sử dụng BCMath để tính giai thừa lớn
function factorial_bcmath($n) {
    $result = '1';
    for ($i = 2; $i <= $n; $i++) {
        $result = bcmul($result, (string)$i);
    }
    return $result;
}
if (isset($_POST['n6'])) {
    $n = intval($_POST['n6']);
    if ($n < 0) {
        $gt = 'Không xác định cho n < 0';
    } elseif ($n <= 20) {
        // Sử dụng hàm tích lũy cho n nhỏ
        $gt = 1;
        for ($i = 2; $i <= $n; $i++) {
            $gt *= $i;
        }
    } else {
        // Sử dụng BCMath cho n lớn
        $gt = factorial_bcmath($n);
    }
}
?>
<div class="card shadow mb-4">
    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Bài 6: Tính giai thừa n!</h6></div>
    <div class="card-body">
        <form method="post">
            <div class="form-group">
                <label>Nhập n:</label>
                <input type="number" name="n6" class="form-control" required value="<?=isset($_POST['n6'])?htmlspecialchars($_POST['n6']):''?>">
            </div>
            <button type="submit" class="btn btn-primary">Tính giai thừa</button>
        </form>
        <?php if($gt!==''): ?>
        <div class="alert alert-info">Giai thừa n! = <?= $gt ?></div>
        <?php endif; ?>
    </div>
</div>
