<?php
// Bài 10: Class PERSON và SINHVIEN
class Person {
    public $name;
    public $dob;
    public $address;
    function __construct($name, $dob, $address) {
        $this->name = $name;
        $this->dob = $dob;
        $this->address = $address;
    }
}
class SinhVien extends Person {
    public $lop;
    function __construct($name, $dob, $address, $lop) {
        parent::__construct($name, $dob, $address);
        $this->lop = $lop;
    }
}
$sv = null;
if (isset($_POST['name10'])) {
    $sv = new SinhVien(
        $_POST['name10'],
        $_POST['dob10'],
        $_POST['address10'],
        $_POST['lop10']
    );
}
?>
<div class="card shadow mb-4">
    <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Bài 10: Class PERSON và SINHVIEN</h6></div>
    <div class="card-body">
        <form method="post">
            <div class="form-group">
                <label>Họ tên:</label>
                <input type="text" name="name10" class="form-control" required value="<?=isset($_POST['name10'])?htmlspecialchars($_POST['name10']):''?>">
            </div>
            <div class="form-group">
                <label>Ngày sinh:</label>
                <input type="date" name="dob10" class="form-control" required value="<?=isset($_POST['dob10'])?htmlspecialchars($_POST['dob10']):''?>">
            </div>
            <div class="form-group">
                <label>Quê quán:</label>
                <input type="text" name="address10" class="form-control" required value="<?=isset($_POST['address10'])?htmlspecialchars($_POST['address10']):''?>">
            </div>
            <div class="form-group">
                <label>Lớp:</label>
                <input type="text" name="lop10" class="form-control" required value="<?=isset($_POST['lop10'])?htmlspecialchars($_POST['lop10']):''?>">
            </div>
            <button type="submit" class="btn btn-primary">Tạo sinh viên</button>
        </form>
        <?php if($sv): ?>
        <div class="alert alert-info">
            <strong>Thông tin sinh viên:</strong><br>
            Họ tên: <?= htmlspecialchars($sv->name) ?><br>
            Ngày sinh: <?= htmlspecialchars($sv->dob) ?><br>
            Quê quán: <?= htmlspecialchars($sv->address) ?><br>
            Lớp: <?= htmlspecialchars($sv->lop) ?>
        </div>
        <?php endif; ?>
    </div>
</div>
