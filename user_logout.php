<?php
session_start();
session_destroy();
header("Location: user_login.php"); // For users, or change to `admin_login.php` for admins
exit();
