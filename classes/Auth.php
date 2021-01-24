<?php
namespace classes;

use classes\Crud;
use classes\Helpers;
class Auth extends Crud{
    public static function signup($data){
        foreach($data as $key => $value){
            $data[$key] = Helpers::validateData($value);
            $data[$key] = self::$conn->real_escape_string($value);
        }

        if(empty($data['firstName']) || empty($data['lastName']) || 
                empty($data['email']) || empty($data['password']) || empty($data['phone'])){
            return '{
                "success": false,
                "message": "All fields are required"
            }';
        }

        if($data['password'] !== $data['confirmPassword']){
            return '{
                "success": false,
                "message": "Passwords do not match"
            }';
        }

        if(\strlen($data['password']) < 6){
            return '{
                "success": false,
                "message": "Password must be at lease 6 characters long"
            }';
        }
        
        //check duplicate

        $duplicateQuery = "SELECT email FROM users WHERE email = ?";
        $duplicateBinder = array("s", "$data[email]");
        $duplicateStmt = parent::read($duplicateQuery, $duplicateBinder);
        if($duplicateStmt->num_rows > 0){
            return '{
                "success": false,
                "message": "An account already exists with this email"
            }'; 
        }

        $query = "INSERT INTO users (`first_name`, `last_name`, `email`, `phone`, `password`)
                        VALUES (?, ?, ?, ?, ?)";
        $binder = array("sssss", "$data[firstName]", "$data[lastName]", "$data[email]", "$data[phone]", \sha1($data['password']));
        $stmt = parent::create($query, $binder);
        if($stmt['success']){
            $userId = $stmt['insert_id'];
            $firstName = $data['firstName'];
            if(!isset($_SESSION)){
                session_start();
            }
            $_SESSION['user_id'] = $userId;
            $_SESSION['first_name'] = $firstName;
            return '{
                "success": true,
                "message": "Account created successfully."
            }';
        }else{
            return '{
                "success": false,
                "message": "Failed, please try again"
            }';
        }

    }

    public static function login($data){
        foreach($data as $key => $value){
            $data[$key] = Helpers::validateData($value);
            $data[$key] = self::$conn->real_escape_string($value);
        }

        if(empty($data['email']) || empty($data['password'])){
            return '{
                "success": false,
                "message": "All fields are required"
            }';
        }

        $data['password'] = \sha1($data['password']);

        $query = "SELECT id, first_name FROM users WHERE `email` = ? AND `password` = ?";
        $binder = array("ss", "$data[email]", "$data[password]");
        $stmt = parent::read($query, $binder);
        if($stmt->num_rows === 1){
            $row = $stmt->fetch_assoc();
            $userId = $row['id'];
            $firstName = $row['first_name'];
            if(!isset($_SESSION)){
                session_start();
            }
            $_SESSION['user_id'] = $userId;
            $_SESSION['first_name'] = $firstName;
            return '{
                "success": true,
                "message": "Login succesful"
            }';
        }else{
            return '{
                "success": false,
                "message": "Invalid Login"
            }';
        }
    }
}