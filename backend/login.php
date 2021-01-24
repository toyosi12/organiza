<?php
require_once __DIR__ . '/../autoload.php';

$_POST = json_decode(file_get_contents('php://input'), true);
print_r(Auth::login($_POST));
