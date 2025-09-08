<?php
// Bài 5: Kiểm tra số hoàn hảo
$isPerfect = '';
if (isset($_POST['n5'])) {
    $n = intval($_POST['n5']);
    $sum = 0;
    for ($i = 1; $i < $n; $i++) {
        if ($n % $i == 0) $sum += $i;
    }
    $isPerfect = ($sum == $n && $n > 0);
}
?>
<div class="card shadow mb-4">
    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Bài 5: Kiểm tra số hoàn hảo</h6></div>
    <div class="card-body">
        <form method="post">
            <div class="form-group">
                <label>Nhập số nguyên dương n:</label>
                <input type="number" name="n5" class="form-control" required value="<?=isset($_POST['n5'])?htmlspecialchars($_POST['n5']):''?>">
            </div>
            <button type="submit" class="btn btn-primary">Kiểm tra</button>
        </form>
        <?php if($isPerfect!==''): ?>
        <div class="alert alert-info">
            <?= $isPerfect ? 'Đây là số hoàn hảo!' : 'Không phải số hoàn hảo.' ?>
        </div>
        <?php endif; ?>
    </div>
</div>
