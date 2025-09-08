<?php
// Bài 4: Nhập dãy số cho đến khi nhập 0 thì dừng
$result = [];
if (isset($_POST['numbers'])) {
    $numbers = explode(',', $_POST['numbers']);
    $filtered = array_filter($numbers, function($v){ return trim($v) !== '' && is_numeric($v); });
    $result = array_map('floatval', $filtered);
}
?>
<div class="card shadow mb-4">
    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Bài 4: Nhập dãy số cho đến khi nhập 0 thì dừng</h6></div>
    <div class="card-body">
        <form method="post">
            <div class="form-group">
                <label>Nhập dãy số, cách nhau bằng dấu phẩy (0 để dừng):</label>
                <input type="text" name="numbers" class="form-control" required value="<?=isset($_POST['numbers'])?htmlspecialchars($_POST['numbers']):''?>">
            </div>
            <button type="submit" class="btn btn-primary">Xem kết quả</button>
        </form>
        <?php if($result): ?>
        <div class="alert alert-info">Dãy số đã nhập (dừng ở số 0):<br>
            <?php
            foreach ($result as $num) {
                if ($num == 0) break;
                echo $num . ' ';
            }
            ?>
        </div>
        <?php endif; ?>
    </div>
</div>
