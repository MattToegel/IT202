<?php
class Stories{
	private $pdo;
	private $query_create_story;
	private $query_get_all_my_stories;
	public function __construct(PDO $pdo){
		$this->pdo = $pdo;
		$this->query_create_story = file_get_contents(__DIR__ . "/../../queries/create_story.sql");
		$this->query_get_all_my_stories = file_get_contents(__DIR__ . "/../../queries/get_all_my_stories.sql");
	}
	private function getDB(){
		return $this->pdo;
	}
	public function get_all_my_stories($author_id){
		try{
			$stmt = $this->pdo->prepare($this->query_get_all_my_stories);
			$r = $stmt->execute(
						array(
							":author"=>$author_id
							)
			);
			$stories = $stmt->fetchAll(PDO::FETCH_ASSOC);
			/*PDO::FETCH_CLASS, 'Story', ['id','title','summary','author','created',
												'modified', 'visibility','isactive']);*/
			$ei = $stmt->errorInfo();
			if($ei[0] == "00000"){
				return array("status"=>"success", "stories"=> $stories);
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
	public function create_story($title, $summary, $author_id){
		//TODO do server side validation for parameters
		try{
			$stmt = $this->pdo->prepare($this->query_create_story);
			$r = $stmt->execute(
						array(
							":title"=>$title, 
							":summary"=>$summary, 
							":author"=>$author_id
							)
			);
			$ei = $stmt->errorInfo();
			if($ei[0] == "00000"){
				return array("status"=>"success", "message"=>"Created story");
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
}
 ?>