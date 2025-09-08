<?php
// Bài 7: Liệt kê ước số
$result = [];
if (isset($_POST['n7'])) {
    $n = intval($_POST['n7']);
    for ($i = 1; $i <= $n; $i++) {
        if ($n % $i == 0) $result[] = $i;
    }
}
?>
<div class="card shadow mb-4">
    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Bài 7: Liệt kê ước số của n</h6></div>
    <div class="card-body">
        <form method="post">
            <div class="form-group">
                <label>Nhập số nguyên dương n:</label>
                <input type="number" name="n7" class="form-control" required value="<?=isset($_POST['n7'])?htmlspecialchars($_POST['n7']):''?>">
            </div>
            <button type="submit" class="btn btn-primary">Liệt kê</button>
        </form>
        <?php if($result): ?>
        <div class="alert alert-info">Ước số của n: <?= implode(', ', $result) ?></div>
        <?php endif; ?>
    </div>
</div>
