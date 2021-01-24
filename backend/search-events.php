<?php

require_once __DIR__ . '/../autoload.php';  
use classes\Events;

print_r(Events::searchEvents($_POST));
