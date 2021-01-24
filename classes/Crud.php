<?php
    namespace classes;

    use app\classes\DbConn;
    
    class Crud extends DbConn{
        public function __construct(){
            parent::__construct();
        }

        public static function create($query, $binder){
            $resp = array();
            $stmt = parent::$conn->prepare($query) or die(self::$conn->error);
            $stmt->bind_param(...$binder);
            if($stmt->execute()){
                $resp['success'] = true;
                $resp['insert_id'] = $stmt->insert_id;
            }else{
                $resp['success'] = false;
            }
            return $resp;
        }

        public static function read($query,$binder){
            $stmt = self::$conn->prepare($query) or die(self::$conn->error);
            $stmt->bind_param(...$binder);
            $stmt->execute();
            $data = $stmt->get_result();
            return $data;
        }

        public static function read2($query){
            return self::$conn->query($query);
        }

        public static function update($query, $binder){
            $resp = array();
            $stmt = self::$conn->prepare($query) or die(self::$conn->error);
            $stmt->bind_param(...$binder);
            if($stmt->execute()){
                $resp['success'] = true;
            }else{
                $resp['success'] = false;
            }
            return $resp;
        }

        public static function delete($query, $binder){
            $resp = array();
            $stmt = self::$conn->prepare($query) or die(self::$conn->error);
            $stmt->bind_param(...$binder);
            if($stmt->execute()){
                $resp['success'] = true;
            }else{
                $resp['success'] = false;
            }
            return $resp;
        }


        

    }
?>