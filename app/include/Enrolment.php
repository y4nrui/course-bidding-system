<?php
class Enrolment
{
    private $userid; //string
    private $course; //string
    private $section; //string
    private $amount; //float
    private $round; //string

    public function __construct($userid, $course, $section, $amount, $round)
    {
        $this->userid = $userid;
        $this->course = $course;
        $this->section = $section;
        $this->amount = $amount;
        $this->round = $round;
    }

    public function getUserid()
    {
        return $this->userid;
    }

    public function getCourse()
    {
        return $this->course;
    }

    public function getSection()
    {
        return $this->section;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getRound()
    {
        return $this->round;
    }
}
