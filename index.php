<?php
include 'autoload.php';
session_start();
$request =  $_SERVER['REQUEST_URI'];

/**
 * If path contains query parameter, compare only the actual path
 */
if(strpos($request, '?') !== false){
    $splitData = explode('?', $request);
    $request = $splitData[0];
}

switch ($request) {
    /**
     * Pages
     */
    case '' :
        require __DIR__ . '/views/index.php';
        break;
    case '/' :
        require __DIR__ . '/views/index.php';
        break;
    case '/login' :
        require __DIR__ . '/views/login.php';
        break;
    case '/signup' :
        require __DIR__ . '/views/signup.php';
        break;
    case '/event':
        require __DIR__ . '/views/event-details.php';
        break;
    case '/buy-membership': 
        require __DIR__ . '/views/buy-membership.php';
        break;

    /**
     * Dashboard
     */
    case '/dashboard/events' :
        require __DIR__ . '/views/events.php';
        break;
    case '/dashboard/events/create' :
        require __DIR__ . '/views/create-event.php';
        break;
    case '/dashboard/events/edit' :
        require __DIR__ . '/views/edit-event.php';
        break;
    case '/buy-membership': 
        require __DIR__ . '/views/buy-membership.php';
        break;

    /**
     * API
     */
    case '/api/signup':
        require __DIR__ . '/backend/signup.php';
        break;
    case '/api/login':
        require __DIR__ . '/backend/login.php';
        break;
    case '/api/logout':
        require __DIR__ . '/backend/logout.php';
        break;
    case '/api/event_types':
        require __DIR__ . '/backend/event-types.php';
        break;
    case '/api/events/create':
        require __DIR__ . '/backend/create-event.php';
        break;
    case '/api/events/edit':
        require __DIR__ . '/backend/edit-event.php';
        break;
    case '/api/user_events':
        require __DIR__ . '/backend/user-events.php';
        break;
    case '/api/user_event':
        require __DIR__ . '/backend/user-event.php';
        break;
    case '/api/events':
        require __DIR__ . '/backend/all-events.php';
        break;
    case '/api/events/search':
        require __DIR__ . '/backend/search-events.php';
        break;
    case '/api/event_details':
        require __DIR__ . '/backend/event-details.php';
        break;
    case '/api/events/attend':
        require __DIR__ . '/backend/attend-event.php';
        break;
    case '/api/events/delete':
        require __DIR__ . '/backend/delete-event.php';
        break;
    default:
        http_response_code(404);
        require __DIR__ . '/views/404.php';
        break;
}