<?php
// Bài 8: Đếm số phần tử âm/dương trong mảng 10 số
$result = ['am'=>0,'duong'=>0];
$numbers = [];
if (isset($_POST['arr8'])) {
    $arr = explode(',', $_POST['arr8']);
    foreach ($arr as $num) {
        $num = floatval(trim($num));
        $numbers[] = $num;
        if ($num > 0) $result['duong']++;
        elseif ($num < 0) $result['am']++;
    }
}
?>
<div class="card shadow mb-4">
    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Bài 8: Đếm số phần tử âm/dương trong mảng 10 số</h6></div>
    <div class="card-body">
        <form method="post">
            <div class="form-group">
                <label>Nhập 10 số, cách nhau bằng dấu phẩy:</label>
                <input type="text" name="arr8" class="form-control" required value="<?=isset($_POST['arr8'])?htmlspecialchars($_POST['arr8']):''?>">
            </div>
            <button type="submit" class="btn btn-primary">Đếm</button>
        </form>
        <?php if($numbers): ?>
        <div class="alert alert-info">
            Số phần tử dương: <?= $result['duong'] ?><br>
            Số phần tử âm: <?= $result['am'] ?>
        </div>
        <?php endif; ?>
    </div>
</div>
