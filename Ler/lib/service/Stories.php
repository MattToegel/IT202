<?php
class Stories{
	private $pdo;
	private $query_create_story;
	private $query_get_all_user_stories;
	private $query_get_user_story;
	private $query_update_story;
	private $query_get_story;
	private $query_get_stories;
	private $query_set_starting_arc;
	private $query_delete_story;
	private $query_get_my_stories_with_progress;
	public function __construct(PDO $pdo){
		$this->pdo = $pdo;
		$this->query_create_story = file_get_contents(__DIR__ . "/../../queries/create_story.sql");
		$this->query_get_all_user_stories = file_get_contents(__DIR__ . "/../../queries/get_all_my_stories.sql");
		$this->query_get_user_story = file_get_contents(__DIR__ . "/../../queries/get_user_story.sql");
		$this->query_update_story = file_get_contents(__DIR__  . "/../../queries/update_story.sql");
        $this->query_get_story = file_get_contents(__DIR__ . "/../../queries/get_story.sql");
        $this->query_get_stories = file_get_contents(__DIR__ . '/../../queries/get_stories.sql');
        $this->query_set_starting_arc = file_get_contents(__DIR__ . '/../../queries/set_starting_arc.sql');
	    $this->query_delete_story = file_get_contents(__DIR__ . '/../../queries/delete_story.sql');
	    $this->query_get_my_stories_with_progress = file_get_contents(__DIR__ . '/../../queries/get_all_my_stories_with_progress.sql');
	}
	private function getDB(){
		return $this->pdo;
	}
	public function get_story($story_id){
        try {
            $stmt = $this->pdo->prepare($this->query_get_story);
            $r = $stmt->execute(
                array(
                    ":story_id" => $story_id
                )
            );
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $ei = $stmt->errorInfo();
            if ($ei[0] == "00000") {
                return array("status" => "success", "story" => $result);
            } else {
                return array("status" => "error", "message" => "An unknown error occurred, please try again later",
                    "errorInfo" => $ei);
            }
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }
	public function get_user_story($author_id, $story_id){
	    try {
            $stmt = $this->pdo->prepare($this->query_get_user_story);
            $r = $stmt->execute(
                array(
                    ":author" => $author_id,
                    ":story_id" => $story_id
                )
            );
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            /* Utils can't dynamically handle objects, so we'll stick with assoc for now
             *$story = new Story(
                $result['id'],
                $result['title'],
                $result['summary'],
                $result['author'],
                $result['created'],
                $result['modified'],
                $result['visibility'],
                $result['is_active']
            );*/
            $ei = $stmt->errorInfo();
            if ($ei[0] == "00000") {
                return array("status" => "success", "story" => $result);
            } else {
                return array("status" => "error", "message" => "An unknown error occurred, please try again later",
                    "errorInfo" => $ei);
            }
        }
        catch(Exception $e){
            return $e->getMessage();
        }
    }
    public function get_stories($title, $username){
        try{
            $stmt = $this->pdo->prepare($this->query_get_stories);
            $r = $stmt->execute(
                array(
                    ":title"=>$title,
                    ":username" => $username
                )
            );
            $stories = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
	public function get_all_user_stories($author_id){
		try{
			$stmt = $this->pdo->prepare($this->query_get_all_user_stories);
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
    public function get_my_stories_with_progress($author_id){
        try{
            $stmt = $this->pdo->prepare($this->query_get_my_stories_with_progress);
            $r = $stmt->execute(
                array(
                    ":user_id"=>$author_id
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
	public function set_starting_arc($story_id, $arc_id){
        //TODO do server side validation for parameters
        try{
            $stmt = $this->pdo->prepare($this->query_set_starting_arc);
            $r = $stmt->execute(
                array(
                    ":story_id"=>$story_id,
                    ":arc_id"=>$arc_id
                )
            );
            $ei = $stmt->errorInfo();
            if($ei[0] == "00000"){
                return array("status"=>"success", "message"=>"Assigned starting arc");
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
	public function update_story($story_id, $title, $summary, $visibility){
        //TODO do server side validation for parameters
        try{
            $stmt = $this->pdo->prepare($this->query_update_story);
            $r = $stmt->execute(
                array(
                    ":title"=>$title,
                    ":summary"=>$summary,
                    //":author"=>$author_id,
                    ":story_id"=>$story_id,
                    ":visibility"=>$visibility
                )
            );
            $ei = $stmt->errorInfo();
            if($ei[0] == "00000"){
                return array("status"=>"success", "message"=>"Updated story");
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
	public function create_story($title, $summary, $author_id, $visibility){
		//TODO do server side validation for parameters
		try{
			$stmt = $this->pdo->prepare($this->query_create_story);
			$r = $stmt->execute(
						array(
							":title"=>$title, 
							":summary"=>$summary, 
							":author"=>$author_id,
                            ":visibility"=>$visibility
							)
			);
			$ei = $stmt->errorInfo();
			if($ei[0] == "00000"){
				return array("status"=>"success", "message"=>"Created story", "story_id" => $this->pdo->lastInsertId());
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
	public function delete_story($story_id, $author_id){
        try{
            $stmt = $this->pdo->prepare($this->query_delete_story);
            $r = $stmt->execute(
                array(
                    ":story_id"=>$story_id,
                    ":author"=>$author_id,
                )
            );
            $ei = $stmt->errorInfo();
            if($ei[0] == "00000"){
                return array("status"=>"success", "message"=>"Deleted story");
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