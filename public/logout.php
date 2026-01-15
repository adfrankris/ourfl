<?php
declare(strict_types=1);
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../includes/helpers.php";
logout_user();
header("Location: " . base_url("/"));
exit;
