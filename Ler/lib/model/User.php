<?php
class User{
	private $id;
	private $username;
	private $email;
	private $roles;
	public function __construct($id, $username, $email, array $roles){
		$this->id = $id;
		$this->username = $username;
		$this->email = $email;
		$this->roles = $roles;
	}
	public function getId(){
		return $this->id;
	}
	public function getUsername(){
		return $this->username;
	}
	public function getEmail(){
		return $this->email;
	}
	public function hasRoleByName($roleName){
		foreach($this->roles as $r){
			if($r->getName() == $roleName){
				return true;
			}
		}
	}
	public function hasRoleById($roleId){
		foreach($this->roles as $r){
			if($r->getId() == $roleId){
				return true;
			}
		}
		return false;
		//return in_array($role, $roles);
	}
	public function getRoles(){
	    return $this->roles;
    }
}