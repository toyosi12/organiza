<?php
require_once __DIR__ . '/../autoload.php';
use classes\Events;

$eventId = json_decode(file_get_contents('php://input'), true)['eventId'];
print_r(Events::getEventDetails($eventId));   

