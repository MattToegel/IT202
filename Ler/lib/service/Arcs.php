<?php
class Arcs{
    private $pdo;
    private $query_create_arc;
    private $query_update_arc;
    private $query_update_decision;
    private $query_get_arc;
    private $query_get_decisions;
    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
        $this->query_create_arc = file_get_contents(__DIR__ . "/../../queries/create_arc.sql");
        $this->query_update_arc = file_get_contents(__DIR__ . "/../../queries/update_arc.sql");
        $this->query_update_decision = file_get_contents(__DIR__ . "/../../queries/update_decision.sql");
        $this->query_get_arc = file_get_contents(__DIR__ . "/../../queries/get_arc.sql");
        $this->query_get_decisions = file_get_contents(__DIR__. '/../../queries/get_decisions_for_arc.sql');
    }
    private function getDB(){
        return $this->pdo;
    }

    /***Must have a parent, but doesn't need a next_arc
     * @param $parent_arc_id
     * @param $content
     * @param $next_arc_id
     * @return array|string
     */
    public function  create_decision($parent_arc_id, $content, $next_arc_id){
        try{
            $stmt = $this->pdo->prepare($this->query_create_decision);
            $r = $stmt->execute(
                array(
                    ":parent_id"=>$parent_arc_id,
                    ":content"=>$content,
                    ":next_arc_id"=>$next_arc_id
                )
            );
            $ei = $stmt->errorInfo();
            if($ei[0] == "00000"){
                return array("status"=>"success", "message"=>"Created decision");
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
    public function get_decisions($arc_id){
        try{
            $stmt = $this->pdo->prepare($this->query_get_decisions);
            $r = $stmt->execute(
                array(
                    ":arc_id"=>$arc_id
                )
            );
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $ei = $stmt->errorInfo();
            if ($ei[0] == "00000") {
                return array("status" => "success", "decisions" => $result);
            } else {
                return array("status" => "error", "message" => "An unknown error occurred, please try again later",
                    "errorInfo" => $ei);
            }
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }
    public function update_arc($arc_id, $title, $content, $decisions = array()){
        //TODO do server side validation for parameters
        try{
            $stmt = $this->pdo->prepare($this->query_update_arc);
            $r = $stmt->execute(
                array(
                    ":title"=>$title,
                    ":content"=>$content,
                    //":author"=>$author_id,
                    ":arc_id"=>$arc_id
                )
            );
            $ei = $stmt->errorInfo();
            //TODO assign/unassign decisions
            //UPDATE Decisions set parent_arc_id = -1 where parent_arc_id = :arc_id
            /*UPDATE Decisions set next_arc_id = NVL(:next_arc_id, -1)
                 where id in (:list_of_decisions)
            */
            //TODO improve efficiency, though should really be ok for <5 decisions since
            //TODO the anticipated limit is 3
            foreach ($decisions as $d){
                $stmt = $this->pdo->prepare($this->query_update_decision);
                //echo var_export($d, true);
                $stmt->execute(array(
                    ":parent_arc_id" => $arc_id,
                    ":next_arc_id"=> $d->getNextArcId(),
                    ":decision_id"=> $d->getId(),
                    ":content" => $d->getContent()
                ));
                //echo var_export($stmt->errorInfo(), true);

            }
            if($ei[0] == "00000"){
                return array("status"=>"success", "message"=>"Updated arc");
            }
            else{
                return array("status"=>"error","message"=>"An unknown error occurred, please try again later",
                    "errorInfo"=>$ei);
            }
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }
    public function create_arc($title, $content, $story_id, $decisions = array()){
        //TODO do server side validation for parameters
        try{
            $stmt = $this->pdo->prepare($this->query_create_arc);
            $r = $stmt->execute(
                array(
                    ":title"=>$title,
                    ":content"=>$content,
                    ":story_id"=>$story_id
                )
            );
            $ei = $stmt->errorInfo();
            $arc_id = $this->pdo->lastInsertId();
            //TODO improve efficiency, though should really be ok for <5 decisions since
            //TODO the anticipated limit is 3
            foreach ($decisions as $d){
                $stmt = $this->pdo->prepare($this->query_update_decision);
                $stmt->execute(array(
                    ":parent_arc_id" => $arc_id,
                    ":next_arc_id"=> $d->getNextArcId(),
                    ":decision_id"=> $d->getId(),
                    ":content" => $d->getContent()
                ));

            }
            if($ei[0] == "00000"){
                return array("status"=>"success", "message"=>"Created arc");
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
    public function get_arc($arc_id){
        try{
            $stmt = $this->pdo->prepare($this->query_get_arc);
            $r = $stmt->execute(
                array(
                    ":arc_id"=>$arc_id
                )
            );
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $ei = $stmt->errorInfo();
            if ($ei[0] == "00000") {
                return array("status" => "success", "arc" => $result);
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