<?php
class Arc{
    private $id;
    private $title;
    private $content;
    private $story_id;
    private $created;
    private $modified;
    private $visibility;
    private $is_active;
    private $decisions;
    public function __construct($id, $title, $content, $story_id, $created, $modified, $vis, $isActive, $decisions){
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->story_id = $story_id;
        $this->created = $created;
        $this->modified = $modified;
        $this->visibility = $vis;
        $this->is_active = $isActive;
        $this->decisions = $decisions;
    }
    //public function __set($name, $value) {}
    public function getId(){
        return $this->id;
    }
    public function getTitle(){
        return $this->title;
    }
    public function getContent(){
        return $this->content;
    }
    public function getStoryId(){
        return $this->story_id;
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
    public function getDecisions(){
        return $this->decisions;
    }
}