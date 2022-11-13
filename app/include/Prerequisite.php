<?php

class Prerequisite {
    
    private $course; // string
    private $prerequisite; // string

    public function __construct($course, $prereq){
        $this->course = $course;
        $this->prerequisite = $prereq;
    }

    public function getCourse(){
        return $this->course;
    }

    public function getPrerequisite(){
        return $this->prerequisite;
    }
    
}

?>