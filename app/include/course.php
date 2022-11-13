<?php
class Course
{
    private $course; // string
    private $school; //string
    private $title; //string
    private $description; //string
    private $exam_date; //date
    private $exam_start; //string
    private $exam_end; // string

    public function __construct($course, $sch, $title, $description, $date, $start, $end)
    {
        $this->course = $course;
        $this->school = $sch;
        $this->title = $title;
        $this->description = $description;
        $this->exam_date = $date;
        $this->exam_start = $start;
        $this->exam_end = $end;
    }

    public function getCourse()
    {
        return $this->course;
    }

    public function getSchool()
    {
        return $this->school;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getExamDate()
    {
        return $this->exam_date;
    }

    public function getExamStart()
    {
        return $this->exam_start;
    }

    public function getExamEnd()
    {
        return $this->exam_end;
    }
}
?>