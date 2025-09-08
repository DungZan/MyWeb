<?php
// Bài 3: Tính giá trị biểu thức S(x, n)
$s = '';
if (isset($_POST['x3']) && isset($_POST['n3'])) {
    $x = floatval($_POST['x3']);
    $n = intval($_POST['n3']);
    $s = $x;
    $gt = 1;
    for ($i = 2; $i <= $n; $i++) {
        $gt *= $i;
        $s += pow($x, $i) / $gt;
    }
}
?>
<div class="card shadow mb-4">
    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Bài 3: Tính giá trị biểu thức S(x, n)</h6></div>
    <div class="card-body">
        <form method="post">
            <div class="form-group">
                <label>Nhập x:</label>
                <input type="number" step="any" name="x3" class="form-control" required value="<?=isset($_POST['x3'])?htmlspecialchars($_POST['x3']):''?>">
            </div>
            <div class="form-group">
                <label>Nhập n:</label>
                <input type="number" name="n3" class="form-control" required value="<?=isset($_POST['n3'])?htmlspecialchars($_POST['n3']):''?>">
            </div>
            <button type="submit" class="btn btn-primary">Tính S(x, n)</button>
        </form>
        <?php if($s!==''): ?>
        <div class="alert alert-info">Giá trị S(x, n): <?= $s ?></div>
        <?php endif; ?>
    </div>
</div>
