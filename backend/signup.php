<?php
require_once __DIR__ . '/../autoload.php';

use classes\Auth;
$_POST = json_decode(file_get_contents('php://input'), true);
print_r(Auth::signup($_POST));
