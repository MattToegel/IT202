<?php

class DBH{
    private static function getDB(){
        global $common;
        if(isset($common)){
            return $common->getDB();
        }
        throw new Exception("Failed to find reference to common");
    }

    /** Wraps all responses in this wrapper as a contract for whoever calls this helper
     * @param $data
     * @param int $status
     * @param string $message
     * @return array
     */
    private static function response($data, $status = 200, $message = ""){
        return array("status"=>$status, "message"=>$message, "data"=>$data);
    }

    /*** Basic repetitive STMT check, throws exception
     * @param $stmt
     * @throws Exception
     */
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
                    error_log(var_export($roles, true));
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
                $id = DBH::getDB()->lastInsertId();
                return DBH::response(["user_id"=>$id],200, "Registration successful");
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

    /*** Logic to fetch a user's current total XP. Should be called seldomly with the result cached on the User's
     *   User record
     * @param $user_id
     * @return array
     */
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

    /*** Fetch System user ID, this is used for Points Transactions
     * @return array
     */
    public static function get_system_user_id(){
        try {
            $query = file_get_contents(__DIR__ . "/../sql/queries/login.sql");
            $stmt = DBH::getDB()->prepare($query);
            $stmt->execute([":email"=>"localhost"]);
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

    /*** Used to add/remove points, records a transaction between users or user and system
     * @param $user_id_src
     * @param $change
     * @param int $user_id_dest
     * @param string $type
     * @param string $memo
     * @return array
     */
    public static function changePoints($user_id_src, $change, $user_id_dest = -1, $type="earned", $memo="system"){
        try {
            //setup so src should be original player

            //grab system id from session (save a DB call)
            //commonly points will be from the system user
            if($user_id_dest <= 0){
                $user_id_dest = Common::get_system_id();
                if($user_id_dest <= 0){
                    $r = DBH::get_system_user_id();
                    $r = Common::get($r, "data", false);
                    if($r){
                        $user_id_dest = Common::get($r, "id", -1);
                    }
                }
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

    /*** Handles giving player XP, negative value can be used to deduct XP, but not a current features.
     *   Activity gets saved in a table as a new record similar to transactions.
     * @param $user_id
     * @param $amount
     * @param string $type
     * @param string $note
     * @return array
     */
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
    public static function get_aggregated_stats($user_id){
        try {
            $query = file_get_contents(__DIR__ . "/../sql/queries/get_aggregated_stats.sql");
            $stmt = DBH::getDB()->prepare($query);
            $result = $stmt->execute([":uid" => $user_id]);
            DBH::verify_sql($stmt);
            if($result){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
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
    public static function update_user_stats($user_id, $level, $xp, $points, $wins, $losses){
        try {
            $query = file_get_contents(__DIR__ . "/../sql/queries/update_user_stats.sql");
            $stmt = DBH::getDB()->prepare($query);
            $result = $stmt->execute([
                ":uid" => $user_id,
                ":level"=>$level,
                ":xp"=>$xp,
                ":points"=>$points,
                ":wins"=>$wins,
                ":losses"=>$losses
            ]);
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
    public static function create_tank($user_id, $name = ""){
        //defaulting to empty string for now, may add a feature to show name, but it doesn't matter right now
        try {
            $query = file_get_contents(__DIR__ . "/../sql/queries/create_tank.sql");
            $stmt = DBH::getDB()->prepare($query);
            $result = $stmt->execute([
                ":name" => $name,
                ":user_id" => $user_id
            ]);
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

    /*** Technically we're only going to have 1 tank per person, but I left it open to allow multiple
     * @param $user_id
     * @return array
     */
    public static function get_tanks($user_id){
        try {
            $query = file_get_contents(__DIR__ . "/../sql/queries/get_tanks.sql");
            $stmt = DBH::getDB()->prepare($query);
            $result = $stmt->execute([
                ":user_id" => $user_id
            ]);
            DBH::verify_sql($stmt);
            if($result){
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    public static function get_shop_items(){
        try {
            $query = file_get_contents(__DIR__ . "/../sql/queries/get_shop_items.sql");
            $stmt = DBH::getDB()->prepare($query);
            $result = $stmt->execute();
            DBH::verify_sql($stmt);
            if($result){
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    public static function get_item_info($items){
        try {
            //need to use a workaround for PDO
            $placeholders = str_repeat('?, ', count($items) - 1) . '?';
            $query = file_get_contents(__DIR__ . "/../sql/queries/get_items_by_stats.sql");
            $query .= "($placeholders)";
            $stmt = DBH::getDB()->prepare($query);
            $result = $stmt->execute($items);//not using associative array here
            DBH::verify_sql($stmt);
            if ($result) {
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return DBH::response($result,200, "success");
            }
            else{
                return DBH::response($result,400, "error");
            }
        }
        catch(Exception $e){
            error_log($e->getMessage());
            return DBH::response(NULL, 400, "DB Error: " . $e->getMessage());
        }
    }
    public static function save_order($data){
        try {
            $query = file_get_contents(__DIR__ . "/../sql/queries/get_max_order_id.sql");
            $stmt = DBH::getDB()->prepare($query);
            $result = $stmt->execute();
            DBH::verify_sql($stmt);
            if($result){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $max = (int)$result["max"];
                $max += 1;
                $query =  file_get_contents(__DIR__ . "/../sql/queries/insert_order_item.sql");
                $stmt = DBH::getDB()->prepare($query);
                $user_id = Common::get_user_id();
                foreach($data as $item){
                    $result = $stmt->execute([
                        ":order_id"=>$max,
                        ":item_id"=>$item["id"],
                        ":user_id"=>$user_id,
                        ":quantity"=>$item["quantity"],
                        ":price"=>$item["cost"]
                    ]);
                }
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
    public static function update_tank($tank){
        try {
            $query = file_get_contents(__DIR__ . "/../sql/queries/update_tank.sql");
            $stmt = DBH::getDB()->prepare($query);
            error_log(var_export($tank, true));
            $result = $stmt->execute([
                ":id"=>$tank["id"],
                ":uid"=>Common::get_user_id(),
                ":speed"=>$tank["speed"],
                ":range"=>$tank["range"],
                ":turnSpeed"=>$tank["turnSpeed"],
                ":fireRate"=>$tank["fireRate"],
                ":health"=>$tank["health"],
                ":damage"=>$tank["damage"]
            ]);
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
    public static function save_questionnaire($questionnaire){
        try {
            //Steps
            //create questionnaire
            /*
             * $questionnaire = [
                    "name"=>$questionnaire_name,
                    "description"=>$questionnaire_desc,
                    "attempts_per_day"=>$attempts_per_day,
                    "max_attempts"=>$max_attempts,
                    "use_max"=>$use_max,
                    "questions"=>$questions
                    ];
             */
            $query = file_get_contents(__DIR__ . "/../sql/queries/create_questionnaire.sql");
            $stmt = DBH::getDB()->prepare($query);
            $stmt->execute([
               ":name"=>Common::get($questionnaire, "name", null),
               ":desc"=>Common::get($questionnaire, "description", null),
               ":apd"=>Common::get($questionnaire, "attempts_per_day", 1),
               ":ma"=>Common::get($questionnaire, "max_attempts", 1),
               ":um"=>Common::get($questionnaire, "use_max", false)?1:0,//convert to tinyint
                ":uid"=>Common::get_user_id()
            ]);
            DBH::verify_sql($stmt);
            //get id
            $questionnaire_id = DBH::getDB()->lastInsertId();
            //batch insert questions
            $query = file_get_contents(__DIR__ . "/../sql/queries/create_question.sql");
            $params = [];
            $questions = Common::get($questionnaire, "questions", []);
            $qt = count($questions);
            $params[":user_id"] = Common::get_user_id();
            $params[":questionnaire_id"] = $questionnaire_id;
            //this is the only placeholder we need to loop over
            for($i = 0; $i < $qt; $i++){
                $params[":question$i"] = Common::get($questions[$i], "question", '');
                if(($i+1) < $qt) {
                    $ni = $i + 1;
                    $query .= ", (:question$ni, :user_id, :questionnaire_id)";
                }
            }
            error_log(var_export($query));
            $stmt = DBH::getDB()->prepare($query);
            $stmt->execute($params);
            DBH::verify_sql($stmt);
            //fetch ids
            $query = file_get_contents(__DIR__ . "/../sql/queries/get_question_ids_for_questionnaire.sql");
            $stmt = DBH::getDB()->prepare($query);
            $stmt->execute([":qid"=>$questionnaire_id]);
            DBH::verify_sql($stmt);
            $question_ids = $stmt->fetchAll(PDO::FETCH_ASSOC);
            //batch insert answers
            $qIndex = 0;
            $params = [];
            //$params[":user_id"] = Common::get_user_id();
            $query = file_get_contents(__DIR__ . "/../sql/queries/create_answer.partial.sql");
            foreach($questions as $question){
                $answers = Common::get($question, "answers", []);
                //$params[":question_id$qIndex"] = Common::get($results[$qIndex], "id", -1);
                $aIndex = 0;
                foreach($answers as $answer){
                    //TODO attempted named params. This would work, but I felt it was a bit messier to setup
                    // $params[":answer-$qIndex-$aIndex"] = Common::get($answer, "answer",'');
                    //$params[":oe-$qIndex-$aIndex"] = Common::get($answer, "open_ended", false)?1:0;
                    if($qIndex > 0 || $aIndex > 0){
                        $query .= ",";
                    }
                    //TODO switched to using positional placeholders instead
                    $query .= "(?, ?, ?, ?)";
                    array_push($params,
                        Common::get($answer, "answer",""),
                        Common::get($answer, "open_ended", false)?1:0,
                        Common::get_user_id(),
                        Common::get($question_ids[$qIndex], "id", -1)
                    );

                    //$query .= "(:answer-$qIndex-$aIndex, :oe-$qIndex-$aIndex, :user_id, :question_id$qIndex)";
                    $aIndex++;
                }

                $qIndex++;

            }
            error_log(var_export($query, true));
            error_log(var_export($params, true));
            $stmt = DBH::getDB()->prepare($query);
            $result = $stmt->execute($params);
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
    public static function get_available_surveys(){
        try {
            //need to use a workaround for PDO
            $query = file_get_contents(__DIR__ . "/../sql/queries/get_available_questionnaires.sql");
            $stmt = DBH::getDB()->prepare($query);
            $result = $stmt->execute([":uid"=>Common::get_user_id()]);//not using associative array here
            DBH::verify_sql($stmt);
            if ($result) {
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return DBH::response($result,200, "success");
            }
            else{
                return DBH::response($result,400, "error");
            }
        }
        catch(Exception $e){
            error_log($e->getMessage());
            return DBH::response(NULL, 400, "DB Error: " . $e->getMessage());
        }
    }
    public static function get_questionnaire_by_id($questionnaire_id){
        try {
            //need to use a workaround for PDO
            $query = file_get_contents(__DIR__ . "/../sql/queries/get_full_questionnaire.sql");
            $stmt = DBH::getDB()->prepare($query);
            $result = $stmt->execute([":questionnaire_id"=>$questionnaire_id]);//not using associative array here
            DBH::verify_sql($stmt);
            if ($result) {
                //TODO check https://phpdelusions.net/pdo PDO::FETCH_GROUP for details
                $result = $stmt->fetchAll(PDO::FETCH_GROUP);
                error_log(var_export($result, true));
                //TODO need to do some mapping
                /*$questions = [];
                foreach($result as $row){
                    $q = Common::get($row, "question_id", -1);
                    if($q > -1){
                        $found = false;
                        foreach($questions as $check){
                            if(Common::get($check, "id", -1) == $q){
                                $found = true;
                                break;
                            }
                        }
                        if(!$found){
                            $questions
                        }
                    }
                }*/
                return DBH::response($result,200, "success");
            }
            else{
                return DBH::response($result,400, "error");
            }
        }
        catch(Exception $e){
            error_log($e->getMessage());
            return DBH::response(NULL, 400, "DB Error: " . $e->getMessage());
        }
    }
    public static function check_survey_status($questionnaire_id){
        try {
            $query = file_get_contents(__DIR__ . "/../sql/queries/check_survey.sql");

            $user_id = Common::get_user_id();
            $stmt = DBH::getDB()->prepare($query);
            $result = $stmt->execute([":qid"=>$questionnaire_id, ":uid"=>$user_id]);
            DBH::verify_sql($stmt);
            if($result){
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
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
    public static function save_response($questionnaire_id, $response){
        try {
            $query = file_get_contents(__DIR__ . "/../sql/queries/insert_response.sql");
            $first = true;
            $params = [];
            $user_id = Common::get_user_id();
            foreach($response as $r){
                if(!$first){
                    $query .= ",";
                }
                $first = false;
                $query .= "(?,?,?,?,?)";

                array_push($params,
                    $questionnaire_id,
                    Common::get($r, "question_id", -1),
                    Common::get($r, "answer_id", -1),
                    Common::get($r, "user_input", null),
                    $user_id
                );
            }
            $stmt = DBH::getDB()->prepare($query);
            $result = $stmt->execute($params);
            DBH::verify_sql($stmt);
            if($result){
                return DBH::response(null,200, "success");
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