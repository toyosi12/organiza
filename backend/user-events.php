<?php
require_once __DIR__ . '/../autoload.php';

use app\classes\Events;
if(isset($_SESSION['user_id'])){
    print_r(Events::getUserEvents());
}else{
    echo '{
        "error": 401,
        "message": "Unauthorized"
    }';
}