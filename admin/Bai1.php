<!-- tính tổng số nguyên tố  < 100 -->
<?php
$sum = 0;
for ($i = 2; $i < 100; $i++) {
    $is_prime = true;
    for ($j = 2; $j <= sqrt($i); $j++) {
        if ($i % $j == 0) {
            $is_prime = false;
            break;
        }
    }
    if ($is_prime) {
        $sum += $i;
    }
}
echo "Tổng số nguyên tố < 100 là: " . $sum;
?>