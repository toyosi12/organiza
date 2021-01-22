<?php
namespace classes;
class Helpers{
    private static $targetDir = "uploads/";
    private static $maxSize = 100000;//100kb
    public static function validateData($data){
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    public static function uploadImage($files){
        $targetFile = self::$targetDir . basename($files["image"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($targetFile,PATHINFO_EXTENSION));
        
        // Check if image file is a actual image or fake image
          $check = \getimagesize($files["image"]["tmp_name"]);
          if($check === false) {
            return '{
                "success": false,
                "message": "Uploaded file is not an image"
            }';  
          }
        
        if ($files["image"]["size"] > self::$maxSize) {
            return '{
                "success": false,
                "message": "File size must not be above 100kb"
            }';
            }
        
        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" ) {
          return '{
                "success": false,
                "message": "Unsupported file format"
          }';
        }
       
        if (move_uploaded_file($files["image"]["tmp_name"], $targetFile)) {
            return json_encode([
                "success" => true,
                "message" => "Image uploaded successfully",
                "target_file" => $targetFile
            ]);
        } else {
            return '{
                "success": false,
                "message": "Could not upload image, please try again"
            }';
        }
    }

}