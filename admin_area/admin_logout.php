<?php
session_start();

// Destroy all admin session data
session_unset();
session_destroy();

// Redirect to login page (or homepage)
header("Location: admin_login.php");  // change the path if your login file is elsewhere
exit();
?>
