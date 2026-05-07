<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
} else {
    header("Location: admin_dashboard.php");
}
exit();
?>
