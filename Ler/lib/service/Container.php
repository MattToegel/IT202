<?php
class Container{
	
	private $config;
    public function __construct(array $configuration){
        $this->config = $configuration;
    }
	
	private $pdo;
	public function getDB(){
		if($this->pdo === null){
			$this->pdo = new PDO(
					$this->config['db_cs'],
					$this->config['db_user'],
					$this->config['db_pass']
				);
		}
		return $this->pdo;
	}
	
	private $users;
	public function getUsers(){
		if($this->users === null){
			$this->users = new Users($this->getDB());
		}
		return $this->users;
	}
	
	private $stories;
	public function getStories(){
		if($this->stories === null){
			$this->stories = new Stories($this->getDB());
		}
		return $this->stories;
	}
	
	private $arcs;
	public function getArcs(){
		if($this->arcs === null){
			$this->arcs = new Arcs($this->getDB());
		}
		return $this->arcs;
	}

	private $history;
	public function getHistory(){
	    if($this->history === null){
	        $this->history = new History($this->getDB());
        }
	    return $this->history;
    }

    private $favorites;
	public function getFavorites(){
        if($this->favorites === null){
            $this->favorites = new Favorites($this->getDB());
        }
        return $this->favorites;
    }
}