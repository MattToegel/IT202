<?php
class Decision{
    private $id;
    private $content;
    private $parent_id; //the arc this is for
    private $created;
    private $modified;
    private $visibility;
    private $is_active;
    private $next_arc_id; //the arc this goes to when selected
    public function __construct($id, $content, $parent_id, $created, $modified, $vis, $isActive, $next_arc_id){
        $this->id = $id;
        $this->content = $content;
        $this->parent_id = $parent_id;
        $this->created = $created;
        $this->modified = $modified;
        $this->visibility = $vis;
        $this->is_active = $isActive;
        $this->next_arc_id = $next_arc_id;
    }
    //public function __set($name, $value) {}
    public function getId(){
        return $this->id;
    }
    public function getContent(){
        return $this->content;
    }
    public function getParentId(){
        return $this->parent_id;
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
    public function getNextArcId(){
        return $this->next_arc_id;
    }
}