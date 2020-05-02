<?php
class History{
    private $pdo;
    private $query_save_last_arc;
    private $query_get_last_arc;
    private $query_update_last_arc;
    private $query_delete_story_progress;
    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
        $this->query_save_last_arc = file_get_contents(__DIR__ . "/../../queries/save_last_arc.sql");
        $this->query_get_last_arc = file_get_contents(__DIR__ . "/../../queries/get_last_arc.sql");
        $this->query_update_last_arc = file_get_contents(__DIR__ . "/../../queries/update_last_arc.sql");
        $this->query_delete_story_progress = file_get_contents(__DIR__ . "/../../queries/delete_story_progress.sql");
    }
    private function getDB(){
        return $this->pdo;
    }

    /***Must have a parent, but doesn't need a next_arc
     * @param $user_id
     * @param $story_id
     * @param $last_arc_id
     * @return array|string
     */
    public function  update_last_arc_id($user_id, $story_id, $last_arc_id){
        try{
            if(Utils::get($this->get_last_arc($user_id, $story_id),
                    "last_arc_id", -1) > -1) {
                $stmt = $this->pdo->prepare($this->query_update_last_arc);
                $r = $stmt->execute(
                    array(
                        ":user_id" => $user_id,
                        ":story_id" => $story_id,
                        ":last_arc_id" => $last_arc_id
                    )
                );
            }
            else {
                $stmt = $this->pdo->prepare($this->query_save_last_arc);
                $r = $stmt->execute(
                    array(
                        ":user_id" => $user_id,
                        ":story_id" => $story_id,
                        ":last_arc_id" => $last_arc_id
                    )
                );
            }
            $ei = $stmt->errorInfo();
            if($ei[0] == "00000"){
                return array("status"=>"success", "message"=>"Updated last arc bookmark");
            }
            else{
                return array("status"=>"error","message"=>"An unknown error occurred, please try again later",
                    "errorInfo"=>$ei);
            }
            //return $stmt->errorInfo();
        }
        catch(Exception $e){
            return $e->getMessage();
        }

    }
    public function get_last_arc($user_id, $story_id){
        try{
            $stmt = $this->pdo->prepare($this->query_get_last_arc);
            $r = $stmt->execute(
                array(
                    ":story_id"=>$story_id,
                    ":user_id"=>$user_id
                )
            );
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $ei = $stmt->errorInfo();
            if ($ei[0] == "00000") {
                $last_arc_id = Utils::get($result, "last_arc_id", -1);
                return array("status" => "success", "last_arc_id" => $last_arc_id);
            } else {
                return array("status" => "error", "message" => "An unknown error occurred, please try again later",
                    "errorInfo" => $ei);
            }
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }
    public function delete_story_progress($user_id, $story_id){
        try{
            $stmt = $this->pdo->prepare($this->query_delete_story_progress);
            $r = $stmt->execute(
                array(
                    ":story_id"=>$story_id,
                    ":user_id"=>$user_id
                )
            );
            $ei = $stmt->errorInfo();
            if ($ei[0] == "00000") {
                return array("status" => "success", "message" => "Reset story progress");
            } else {
                return array("status" => "error", "message" => "An unknown error occurred, please try again later",
                    "errorInfo" => $ei);
            }
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }
}