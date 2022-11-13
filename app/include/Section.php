<?php
class Section {
    
    private $course; // string
    private $section; // string
    private $day; // string, eg 1 for Monday
    private $start; // time
    private $end; // time
    private $instructor; // string
    private $venue; // string
    private $size; // int

    public function __construct($course, $section, $day, $start, $end, $instructor, $venue, $size){
        $this->course = $course;
        $this->section = $section;
        $this->day = $day;
        $this->start = $start;
        $this->end = $end;
        $this->instructor = $instructor;
        $this->venue = $venue;
        $this->size = $size;
    }

    public function getCourse(){
        return $this->course;
    }

    public function getSection(){
        return $this->section;
    }

    public function getDay(){
        return $this->day;
    }

    public function getStart(){
        return $this->start;
    }

    public function getEnd(){
        return $this->end;
    }

    public function getInstructor(){
        return $this->instructor;
    }

    public function getVenue(){
        return $this->venue;
    }

    public function getSize(){
        return $this->size;
    }
}


?>