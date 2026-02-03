<?php
session_start();

// Xoá toàn bộ session
session_unset();
session_destroy();

// Quay về trang login
header('Location: login.php');
exit;
