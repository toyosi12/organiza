<?php
class Events extends Crud{

    public function __construct(){
    }
    /**
     * List out all event types
     */
    public static function getEventTypes(){
        $query = "SELECT * FROM event_types";
        $stmt = parent::read2($query);
        $data = [];
        while($row = $stmt->fetch_assoc()){
            $data[] = $row;
        }
        return json_encode($data);

    }

    /**
     * create new event
     */
    public static function createEvent($textData, $fileData){
        foreach($textData as $key => $value){
            $textData[$key] = Helpers::validateData($value);
            $textData[$key] = self::$conn->real_escape_string($value);
        }

        $success = false;
        $uploadFile = Helpers::uploadImage($fileData);
        $uploadResponse = json_decode($uploadFile);

        $eventTypeId = explode(",", $textData['eventType']);

        if($uploadResponse->success){
            $query = "INSERT INTO events (`title`, `description`, `start_date`, `start_time`,
                        `end_date`, `end_time`, `address`, `image`, `user_id`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $binder = array("ssssssssi", "$textData[title]", "$textData[description]",
                                "$textData[startDate]", "$textData[startTime]", "$textData[endDate]",
                                "$textData[endTime]", "$textData[address]", "$uploadResponse->target_file", $_SESSION['user_id']);
            $stmt = parent::create($query, $binder);
            $eventId = $stmt['insert_id'];
            if($stmt['success']){
                foreach($eventTypeId as $e){
                    $query2 = "INSERT INTO `event_details` (`event_type_id`, `event_id`) VALUES (?, ?)";
                    $binder2 = array("ii", $e, $eventId);
                    $stmt2 = parent::create($query2, $binder2);
                    if($stmt2['success']){
                        $success = true;
                    }else{
                        return '{
                            "success": false,
                            "message": "Could not create event, please try again"
                        }';   
                    }
                }

                if($success){
                    return '{
                        "success": true,
                        "message": "Event created successfully"
                    }';   
                }
            }else{
                return '{
                    "success": false,
                    "message": "Could not create event, please try again"
                }';   
            } 
        }else{
            return json_encode([
                'success' => false,
                "message" => $uploadResponse->message
            ]);
        }
    }

    public static function getUserEvents(){
        $userId = $_SESSION['user_id'];
        $query = "SELECT e.id AS event_id, et.type, e.start_date, e.start_time, e.end_date, e.end_time, title,
                    e.description, e.address, u.id FROM users u
                    JOIN events e ON e.user_id = u.id
                    JOIN event_details ed ON e.id = ed.event_id
                    JOIN event_types et ON et.id = ed.event_type_id
                    WHERE u.id = ? GROUP BY event_id ORDER BY created_at DESC";
        $binder = array("i", $userId);
        $stmt = parent::read($query, $binder);
        $data = [];
        while($row = $stmt->fetch_assoc()){
            //get event types for a particular event;
            $eventId = $row['event_id'];
            $query2 = "SELECT et.type FROM event_types et 
                        JOIN event_details ed ON et.id = ed.event_type_id
                        WHERE event_id = ?";
            $binder2 = array("i", $eventId);
            $stmt2 = parent::read($query2, $binder2);
            $eventTypes = [];
            while($row2 = $stmt2->fetch_assoc()){
                $eventTypes[] = $row2['type'];
            }
            $row['event_types'] = $eventTypes;
            $data[] = $row;
        }

        return json_encode($data);
    }

    public static function getUserEvent($eventId){
        $query = "SELECT e.id AS event_id, et.type, e.start_date, e.start_time, e.end_date, e.end_time, title,
                    e.description, e.address, e.image, u.id FROM users u
                    JOIN events e ON e.user_id = u.id
                    JOIN event_details ed ON e.id = ed.event_id
                    JOIN event_types et ON et.id = ed.event_type_id
                    WHERE e.id = ?";
        $binder = array("i", $eventId);
        $stmt = parent::read($query, $binder);
        $data = [];
        if($stmt->num_rows === 0){
            return '{
                "error": 404,
                "message": "Not found"
            }';
        }
        while($row = $stmt->fetch_assoc()){
            //get event types for a particular event;
            $eventId = $row['event_id'];
            $query2 = "SELECT et.type FROM event_types et 
                        JOIN event_details ed ON et.id = ed.event_type_id
                        WHERE event_id = ?";
            $binder2 = array("i", $eventId);
            $stmt2 = parent::read($query2, $binder2);
            $eventTypes = [];
            while($row2 = $stmt2->fetch_assoc()){
                $eventTypes[] = $row2['type'];
            }
            $row['event_types'] = $eventTypes;
            $data[] = $row;
        }
        return json_encode($data);
    }

    public static function getAllEvents(){
        $query = "SELECT e.id AS event_id, e.image, et.type, e.start_date, e.start_time, e.end_date, e.end_time, title,
                    e.description, e.address, u.id, u.first_name, u.last_name FROM users u
                    JOIN events e ON e.user_id = u.id
                    JOIN event_details ed ON e.id = ed.event_id
                    JOIN event_types et ON et.id = ed.event_type_id
                    GROUP BY event_id ORDER BY created_at DESC";
        $stmt = parent::read2($query);
        $data = [];
        while($row = $stmt->fetch_assoc()){
            //get event types for a particular event;
            $eventId = $row['event_id'];
            $query2 = "SELECT et.type FROM event_types et 
                        JOIN event_details ed ON et.id = ed.event_type_id
                        WHERE event_id = ?";
            $binder2 = array("i", $eventId);
            $stmt2 = parent::read($query2, $binder2);
             $eventTypes = [];
            while($row2 = $stmt2->fetch_assoc()){
                $eventTypes[] = $row2['type'];
            }
            $row['event_types'] = $eventTypes;
            $data[] = $row;
        }

        return json_encode($data);
    }

    public static function editEvent($textData, $fileData){
        $imageIncluded;
        if(count($fileData) > 0){
            $imageIncluded = true;
        }else{
            $imageIncluded = false;
        }
        
        $success = false;

        if($imageIncluded){
            $uploadFile = Helpers::uploadImage($fileData);
            $uploadResponse = json_decode($uploadFile);

            if($uploadResponse->success){
                $query = "UPDATE events SET `title` = ?, `description` = ?, `start_date` = ?,
                 `start_time` = ?, `end_date` = ?, `end_time` = ?, `address` = ?, `image` = ? WHERE id = ?";
                $binder = array("ssssssssi", "$textData[title]", "$textData[description]",
                            "$textData[startDate]", "$textData[startTime]", "$textData[endDate]",
                            "$textData[endTime]", "$textData[address]", "$uploadResponse->target_file", $textData['eventId']);
                $stmt = parent::update($query, $binder);
                if($stmt['success']){
                    return '{
                        "success": true,
                        "message": "Event updated successfully"
                    }'; 
                }else{
                    return '{
                        "success": false,
                        "message": "Could not create event, please try again"
                    }';   
                } 
            }else{
                return json_encode([
                    'success' => false,
                    "message" => $uploadResponse->message
                ]);
            }
        }else{
            $query = "UPDATE events SET `title` = ?, `description` = ?, `start_date` = ?,
                 `start_time` = ?, `end_date` = ?, `end_time` = ?, `address` = ? WHERE id = ?";
                $binder = array("sssssssi", "$textData[title]", "$textData[description]",
                            "$textData[startDate]", "$textData[startTime]", "$textData[endDate]",
                            "$textData[endTime]", "$textData[address]", $textData['eventId']);
                $stmt = parent::update($query, $binder);
                if($stmt['success']){
                    return '{
                        "success": true,
                        "message": "Event updated successfully"
                    }'; 
                }else{
                    return '{
                        "success": false,
                        "message": "Could not create event, please try again"
                    }';   
                } 
        }

    }

    public static function getEventDetails($eventId){
        $query = "SELECT * FROM events WHERE id = ?";
        $binder = array("i", $eventId);
        $stmt = parent::read($query, $binder);
        while($row = $stmt->fetch_assoc()){
            $query2 = "SELECT et.type FROM event_types et 
            JOIN event_details ed ON et.id = ed.event_type_id
            WHERE event_id = ?";
            $binder2 = array("i", $eventId);
            $stmt2 = parent::read($query2, $binder2);
            $eventTypes = [];
            while($row2 = $stmt2->fetch_assoc()){
                $eventTypes[] = $row2['type'];
            }
            $row['event_types'] = $eventTypes;
            $data[] = $row;
        }
        return json_encode($data);
    }

    public static function deleteEvent($eventId){
        $query = "DELETE FROM events WHERE id = ?";
        $binder = array("i", $eventId);
        $stmt = parent::delete($query, $binder);
        if($stmt['success']){
            return '{
                "success": true,
                "message": "Event deleted successfully"
            }';
        }else{
            return '{
                "success": false,
                "message": "Could not delete event. Please try again"
            }';
        }
    }

    public static function attendEvent($data){
        //check duplicate
        $query = "SELECT * FROM attendees WHERE email = ? AND event_id = ?";
        $binder = array("si", "$data[email]", $data['eventId']);
        $stmt = parent::read($query, $binder);
        if($stmt->num_rows > 0){
            return '{
                "success": false,
                "message": "You have already registered for this event with this email"
            }'; 
        }

        $query2 = "INSERT INTO attendees (`first_name`, `last_name`, `email`, `event_id`)
                    VALUES (?, ?, ?, ?)";
        $binder2 = array("ssss", "$data[firstName]", "$data[lastName]", "$data[email]", "$data[eventId]");
        $stmt2 = parent::create($query2, $binder2);
        if($stmt2['success']){
            return '{
                "success": true,
                "message": "You have successfully registered for this event."
            }';
        }else{
            return '{
                "success": false,
                "message": "Could not register for the prevent. Please try again."
            }';
        }
    }

    public static function searchEvents($searchText){
        $searchText = "%$searchText[searchText]%";
        $query = "SELECT e.id AS event_id, e.image, et.type, e.start_date, e.start_time, e.end_date, e.end_time, title,
                    e.description, e.address, u.id, u.first_name, u.last_name FROM users u
                    JOIN events e ON e.user_id = u.id
                    JOIN event_details ed ON e.id = ed.event_id
                    JOIN event_types et ON et.id = ed.event_type_id
                    WHERE `title` LIKE ? OR `description` LIKE ?
                    GROUP BY event_id ORDER BY created_at DESC";
        $binder = array("ss", $searchText, $searchText);
        $stmt = parent::read($query, $binder);
        $data = [];
        while($row = $stmt->fetch_assoc()){
        //get event types for a particular event;
        $eventId = $row['event_id'];
        $query2 = "SELECT et.type FROM event_types et 
                    JOIN event_details ed ON et.id = ed.event_type_id
                    WHERE event_id = ?";
        $binder2 = array("i", $eventId);
        $stmt2 = parent::read($query2, $binder2);
        $eventTypes = [];
        while($row2 = $stmt2->fetch_assoc()){
            $eventTypes[] = $row2['type'];
        }
        $row['event_types'] = $eventTypes;
        $data[] = $row;
        }

        return json_encode($data);
    }

    
}