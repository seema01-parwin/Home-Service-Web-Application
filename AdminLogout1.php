<?php
session_start();
session_unset();
session_destroy();
header("Location: AdminLogin1.php");
exit();
