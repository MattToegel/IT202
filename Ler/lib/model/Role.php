<?php
class Role{
	private $id;
	private $name;
	public function __contruct($id, $name){
		$this->id = $id;
		$this->name = $name;
	}
	public function getId(){
		return $this->id;
	}
	public function getName(){
		return $this->name;	
	}
}
?>