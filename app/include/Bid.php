<?php

class Bid
{
    # This class is for a single bid of a section of a course by one student

    # attributes
    private $userid; // string ang.lee.2019
    private $amount; // float 12.3
    private $course; // string IS111
    private $section; // string S1
    private $result; // round

    public function __construct($userid, $amount, $code, $section, $result = 'Pending')
    {
        $this->userid = $userid;
        $this->amount = $amount;
        $this->course = $code;
        $this->section = $section;
        $this->result = $result;
    }

    public function getUserid()
    {
        return $this->userid;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getCourse()
    {
        return $this->course;
    }

    public function getSection()
    {
        return $this->section;
    }

    public function getResult()
    {
        return $this->result;
    }
}
