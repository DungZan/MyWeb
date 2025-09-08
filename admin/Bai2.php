<?php
// Bài 2a: Tính tổng T = 1/2 + 2/3 + ... + n/(n+1)
$tong2a = '';
if (isset($_POST['n2a'])) {
    $n = intval($_POST['n2a']);
    $tong2a = 0;
    for ($i = 1; $i <= $n; $i++) {
        $tong2a += $i/($i+1);
    }
}
// Bài 2b: Tính tổng T = 1/2 + 1/4 + ... + 1/(n+2) với điều kiện e = 1/(n+2) > 0.0001
$tong2b = '';
if (isset($_POST['n2b'])) {
    $n = intval($_POST['n2b']);
    $tong2b = 0;
    $i = 1;
    while (true) {
        $e = 1/($i+2);
        if ($e <= 0.0001) break;
        $tong2b += $e;
        $i++;
    }
}
?>
<div class="card shadow mb-4">
    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Bài 2: Tính tổng chuỗi số</h6></div>
    <div class="card-body">
        <form method="post">
            <div class="form-group">
                <label>Tính tổng T = 1/2 + 2/3 + ... + n/(n+1):</label>
                <input type="number" name="n2a" class="form-control" placeholder="Nhập n" required value="<?=isset($_POST['n2a'])?htmlspecialchars($_POST['n2a']):''?>">
                <button type="submit" class="btn btn-primary mt-2">Tính tổng 2a</button>
            </div>
        </form>
        <?php if($tong2a!==''): ?>
        <div class="alert alert-info">Tổng 2a: <?= $tong2a ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="form-group">
                <label>Tính tổng T = 1/2 + 1/4 + ... + 1/(n+2) (e = 1/(n+2) > 0.0001):</label>
                <input type="number" name="n2b" class="form-control" placeholder="Nhập n" required value="<?=isset($_POST['n2b'])?htmlspecialchars($_POST['n2b']):''?>">
                <button type="submit" class="btn btn-primary mt-2">Tính tổng 2b</button>
            </div>
        </form>
        <?php if($tong2b!==''): ?>
        <div class="alert alert-info">Tổng 2b: <?= $tong2b ?></div>
        <?php endif; ?>
    </div>
</div>