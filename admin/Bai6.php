<?php
// Bài 6: Tính giai thừa
$gt = '';
if (isset($_POST['n6'])) {
    $n = intval($_POST['n6']);
    $gt = 1;
    for ($i = 2; $i <= $n; $i++) {
        $gt *= $i;
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
