<?php
class Favorites{
    private $pdo;
    private $query_create_favorite;
    private $query_delete_favorite;
    private $query_get_story_stats;
    private $query_get_favorite;
    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
        $this->query_create_favorite = file_get_contents(__DIR__ . "/../../queries/create_favorite.sql");
        $this->query_delete_favorite = file_get_contents(__DIR__. "/../../queries/delete_favorite.sql");
        $this->query_get_story_stats = file_get_contents(__DIR__. "/../../queries/get_story_stats.sql");
        $this->query_get_favorite = file_get_contents(__DIR__."/../../queries/get_favorite.sql");
    }
    private function getDB(){
        return $this->pdo;
    }
    public function get_favorite($user_id, $story_id){
        try{
            $stmt = $this->pdo->prepare($this->query_get_favorite);
            $r = $stmt->execute(
                array(
                    ":user_id" => $user_id,
                    ":story_id" => $story_id
                )
            );
        }
        catch(Exception $e){
            return $e->getMessage();
        }
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $ei = $stmt->errorInfo();
        $count = (int)$result['favorite'];
        if($ei[0] == "00000"){
            return array("status"=>"success", "favorite"=>($count > 0));
        }
        else{
            return array("status"=>"error","message"=>"An unknown error occurred, please try again later",
                "errorInfo"=>$ei);
        }
    }
    public function create_favorite($user_id, $story_id){
        try{
            $stmt = $this->pdo->prepare($this->query_create_favorite);
            $r = $stmt->execute(
                array(
                    ":user_id" => $user_id,
                    ":story_id" => $story_id
                )
            );
        }
        catch(Exception $e){
            return $e->getMessage();
        }
        $ei = $stmt->errorInfo();
        if($ei[0] == "00000"){
            return array("status"=>"success", "message"=>"Favorited story");
        }
        else{
            return array("status"=>"error","message"=>"An unknown error occurred, please try again later",
                "errorInfo"=>$ei);
        }
    }
    public function delete_favorite($user_id, $story_id){
        try{
            $stmt = $this->pdo->prepare($this->query_delete_favorite);
            $r = $stmt->execute(
                array(
                    ":user_id" => $user_id,
                    ":story_id" => $story_id
                )
            );
        }
        catch(Exception $e){
            return $e->getMessage();
        }
        $ei = $stmt->errorInfo();
        if($ei[0] == "00000"){
            return array("status"=>"success", "message"=>"Unfavorited story");
        }
        else{
            return array("status"=>"error","message"=>"An unknown error occurred, please try again later",
                "errorInfo"=>$ei);
        }
    }
    public function get_story_stats($user_id, $story_id){
        try{
            $stmt = $this->pdo->prepare($this->query_get_story_stats);
            $r = $stmt->execute(
                array(
                    ":user_id" => $user_id,
                    ":story_id" => $story_id
                )
            );
        }
        catch(Exception $e){
            return $e->getMessage();
        }
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $ei = $stmt->errorInfo();
        if($ei[0] == "00000"){
            return array("status"=>"success", "favorites"=>$result['Favorites'], "progress"=>$result['Progress']);
        }
        else{
            return array("status"=>"error","message"=>"An unknown error occurred, please try again later",
                "errorInfo"=>$ei);
        }
    }
}