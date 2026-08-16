<?php
require_once __DIR__ . '/../includes/auth.php';

$_SESSION = [];
session_destroy();

header('Location: /LGU-Link/index.php');
exit;
