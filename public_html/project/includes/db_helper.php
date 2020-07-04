<?php

class DBH{
    private static function getDB(){
        global $common;
        if(isset($common)){
            return $common->getDB();
        }
        throw new Exception("Failed to find reference to common");
    }
    private static function response($data, $status = 200, $message = ""){
        return array("status"=>$status, "message"=>$message, "data"=>$data);
    }
    private static function verify_sql($stmt){
        if(!isset($stmt)){
            throw new Exception("stmt object is undefined");
        }
        $e = $stmt->errorInfo();
        if($e[0] != '00000'){
            $error = var_export($e, true);
            error_log($error);
            throw new Exception("SQL Error: $error");
        }
    }
    public static function login($email, $pass){
        try {
            $query = file_get_contents(__DIR__ . "/../sql/queries/login.sql");
            $stmt = DBH::getDB()->prepare($query);
            $stmt->execute([":email" => $email]);
            DBH::verify_sql($stmt);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user) {
                if (password_verify($pass, $user["password"])) {
                    unset($user["password"]);//TODO remove password before we return results
                    //TODO get roles
                    $query = file_get_contents(__DIR__ . "/../sql/queries/get_roles.sql");
                    $stmt = DBH::getDB()->prepare($query);
                    $stmt->execute([":user_id"=>$user["id"]]);
                    DBH::verify_sql($stmt);
                    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $user["roles"] = $roles;
                    return DBH::response($user);
                } else {
                    return DBH::response(NULL, 403, "Invalid email or password");
                }
            } else {
                return DBH::response(NULL, 403, "Invalid email or password");
            }
        }
        catch(Exception $e){
            error_log($e->getMessage());
            return DBH::response(NULL, 400, "DB Error: " . $e->getMessage());
        }
    }
    public static function register($email, $pass){
        try {
            $query = file_get_contents(__DIR__ . "/../sql/queries/register.sql");
            $stmt = DBH::getDB()->prepare($query);
            $pass = password_hash($pass, PASSWORD_BCRYPT);
            $result = $stmt->execute([":email" => $email, ":password" => $pass]);
            DBH::verify_sql($stmt);
            if($result){
                return DBH::response(NULL,200, "Registration successful");
            }
            else{
                return DBH::response(NULL, 400, "Registration unsuccessful");
            }
        }
        catch(Exception $e){
            error_log($e->getMessage());
            return DBH::response(NULL, 400, "DB Error: " . $e->getMessage());
        }
    }
    public static function getTotalXP($user_id){
        try {
            $query = file_get_contents(__DIR__ . "/../sql/queries/get_total_xp.sql");
            $stmt = DBH::getDB()->prepare($query);
            $result = $stmt->execute([":uid" => $user_id]);
            DBH::verify_sql($stmt);
            if($result){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $total = Common::get($result,"total", 0);
                $data = ["total"=>$total];
                return DBH::response($data,200, "success");
            }
            else{
                return DBH::response(NULL, 400, "error");
            }
        }
        catch(Exception $e){
            error_log($e->getMessage());
            return DBH::response(NULL, 400, "DB Error: " . $e->getMessage());
        }
    }
    public static function get_system_user_id(){
        try {
            $query = file_get_contents(__DIR__ . "/../sql/queries/login.sql");
            $stmt = DBH::getDB()->prepare($query);
            $result = $stmt->execute([":email"=>"localhost"]);
            DBH::verify_sql($stmt);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if($result){
                return DBH::response($result,200, "success");
            }
            else{
                return DBH::response(NULL, 400, "error");
            }
        }
        catch(Exception $e){
            error_log($e->getMessage());
            return DBH::response(NULL, 400, "DB Error: " . $e->getMessage());
        }
    }
    public static function changePoints($user_id_src, $change, $user_id_dest = -1, $type="earned", $memo="system"){
        try {
            //setup so src should be original player

            //grab system id from session (save a DB call)
            //commonly points will be from the system user
            if($user_id_dest <= 0){
                $user_id_dest = Common::get_system_id();
            }
            error_log("System user $user_id_dest");
            $query = file_get_contents(__DIR__ . "/../sql/queries/change_points.sql");
            $stmt = DBH::getDB()->prepare($query);
            //from System to User (most likely)
            $change *= -1;//flip it because if we're adding to user it's subtracted from system (or vice versa)
            $result = $stmt->execute([":src" => $user_id_dest, ":dest"=>$user_id_src,
                ":change"=>$change, ":type"=>$type, ":memo"=>$memo]);
            DBH::verify_sql($stmt);
            $change *= -1;//flip it, now we need the other half of the transaction
            //swap src/dest since it's the inverse of the previous part of the transaction
            $result2 = $stmt->execute([":src" => $user_id_src, ":dest"=>$user_id_dest,
                ":change"=>$change, ":type"=>$type, ":memo"=>$memo]);
            DBH::verify_sql($stmt);
            if($result && $result2){
                return DBH::response(NULL,200, "success");
            }
            else{
                return DBH::response(NULL, 400, "error");
            }
        }
        catch(Exception $e){

            error_log($e->getMessage());
            return DBH::response(NULL, 400, "DB Error: " . $e->getMessage());
        }
    }
    public static function addXP($user_id, $amount, $type="system", $note=""){
        try {
            $query = file_get_contents(__DIR__ . "/../sql/queries/add_xp.sql");
            $stmt = DBH::getDB()->prepare($query);
            $result = $stmt->execute([":uid" => $user_id, ":amount"=>$amount, ":type"=>$type, ":note"=>$note]);
            DBH::verify_sql($stmt);
            if($result){
                return DBH::response(NULL,200, "success");
            }
            else{
                return DBH::response(NULL, 400, "error");
            }
        }
        catch(Exception $e){
            error_log($e->getMessage());
            return DBH::response(NULL, 400, "DB Error: " . $e->getMessage());
        }
    }
}