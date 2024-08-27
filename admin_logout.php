<?php
session_start();
session_destroy();
header("Location: admin_login.php"); // For users, or change to `admin_login.php` for admins
exit();
