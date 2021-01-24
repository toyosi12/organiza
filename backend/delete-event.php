<?php
require_once __DIR__ . '/../autoload.php';
use classes\Events;


if(isset($_SESSION['user_id'])){
    $eventId = json_decode(file_get_contents('php://input'), true)['eventId'];
    print_r(Events::deleteEvent($eventId));   
}else{
    echo '{
        "error": 401,
        "message": "Unauthorized"
    }';
}

