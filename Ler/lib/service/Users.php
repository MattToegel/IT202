<?php
class Users{
	private $pdo;
	private $query_login;
	private $query_register;
	private $query_getroles;
	public function __construct(PDO $pdo){
		$this->pdo = $pdo;
		$this->query_login = file_get_contents(__DIR__ . "/../../queries/login.sql");
		$this->query_register = file_get_contents(__DIR__ . "/../../queries/register.sql");
		$this->query_getroles = file_get_contents(__DIR__ . "/../../queries/getroles.sql");
		
	}
	private function getDB(){
		return $this->pdo;
	}
	public function login($username, $password){
		try{
			$stmt = $this->pdo->prepare($this->query_login);
			$stmt->execute(array(":email"=>$username));
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			if($result){
				$p = $result['password'];
				if(password_verify($password, $p)){
					$stmt = $this->pdo->prepare($this->query_getroles);
					$stmt->execute(array(":id"=>$result['id']));
					$results = $stmt->fetchAll(PDO::FETCH_CLASS, 'Role');
					if(!$results){
						$results = array();
					}
					$user = new User(
						$result['id'],
						$result['username'],
						$result['email'],
						$results//roles if any
					);
					return $user;
				}
			}
		}
		catch(Exception $e){
			
		}
		return false;
	}
	public function register($username, $email, $password, $confirm){
		if(empty(trim($password)) || empty(trim($confirm))){
			return array("status"=>"error", "message"=>"Password and Confirm Password cannot be empty");
		}
		if($password != $confirm){
			return array("status"=>"error", "message"=>"Password and confirm password do not match");
		}
		if(empty(trim($username))){
			return array("status"=>"error","message"=>"Username cannot be empty");
		}
		if(empty(trim($email))){
			return array("status"=>"error","message"=>"Email cannot be empty");
		}
		$hash = password_hash($password, PASSWORD_BCRYPT);
		try{
			$stmt = $this->pdo->prepare($this->query_register);
			$r = $stmt->execute(
						array(
							":username"=>$username, 
							":email"=>$email, 
							":password"=>$hash
							)
			);
			$ei = $stmt->errorInfo();
			if($ei[0] == "00000"){
				return array("status"=>"success", "message"=>"Successfully registered");
			}
			else if($ei[0] == "23000"){
				return array("status"=>"error", "message"=>"Username or email already in use");
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
		return null;
	}
}
?>