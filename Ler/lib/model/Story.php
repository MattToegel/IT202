<?php
class Story{
	private $id;
	private $title;
	private $summary;
	private $author;
	private $created;
	private $modified;
	private $visibility;
	private $is_active;
	public function __construct($id, $title, $summary, $author, $created, $modified, $vis, $isActive){
		$this->id = $id;
		$this->title = $title;
		$this->summary = $summary;
		$this->author = $author;
		$this->created = $created;
		$this->modified = $modified;
		$this->visibility = $vis;
		$this->is_active = $isActive;
	}
	//public function __set($name, $value) {}
	public function getId(){
		return $this->id;
	}
	public function getTitle(){
		return $this->title;
	}
	public function getSummary(){
		return $this->summary;
	}
	public function getAuthor(){
		return $this->author;
	}
	public function getCreated(){
		return $this->created;
	}
	public function getModified(){
		return $this->modified;
	}
	public function getVisibility(){
		return $this->visibility;
	}
	public function getIsActive(){
		return $this->is_active;
	}
}
?>