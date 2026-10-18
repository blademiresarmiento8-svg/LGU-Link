<?php
require_once __DIR__ . '/../includes/auth.php';

$_SESSION = [];
session_destroy();

// "expired" comes from the browser-side idle timer.
header('Location: ' . (isset($_GET['expired']) ? '/LGU-Link/auth/login.php?expired=1' : '/LGU-Link/index.php'));
exit;
