<?php
// Bài 9: Đổi giây sang giờ:phút:giây
$kq = '';
if (isset($_POST['s9'])) {
    $s = intval($_POST['s9']);
    $h = floor($s / 3600);
    $m = floor(($s % 3600) / 60);
    $sec = $s % 60;
    $kq = sprintf('%02d:%02d:%02d', $h, $m, $sec);
}
?>
<div class="card shadow mb-4">
    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Bài 9: Đổi giây sang giờ:phút:giây</h6></div>
    <div class="card-body">
        <form method="post">
            <div class="form-group">
                <label>Nhập số giây:</label>
                <input type="number" name="s9" class="form-control" required value="<?=isset($_POST['s9'])?htmlspecialchars($_POST['s9']):''?>">
            </div>
            <button type="submit" class="btn btn-primary">Đổi</button>
        </form>
        <?php if($kq!==''): ?>
        <div class="alert alert-info">Kết quả: <?= $kq ?></div>
        <?php endif; ?>
    </div>
</div>
