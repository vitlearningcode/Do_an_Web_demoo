<?php
session_start();
// Đặt flag để index.php biết cần xóa localStorage cart phía client
$_SESSION['xoa_cart_local'] = true;
session_unset();
session_destroy();
// Cookie flag: xóa localStorage cart sau khi redirect về index.php
setcookie('xoa_cart_local', '1', time() + 60, '/');
header("Location: index.php");
exit();
?>