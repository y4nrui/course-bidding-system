<?php
class Student
{
    private $userid; // string
    private $pwdHash; // hashed string
    private $name; // string
    private $school; // string
    private $edollar; // float

    public function __construct($userid, $passwordHashed, $name, $sch, $edollar)
    {
        $this->userid = $userid;
        $this->pwdHash = $passwordHashed;
        $this->name = $name;
        $this->school = $sch;
        $this->edollar = $edollar;
    }


public function getUserid()
{
    return $this->userid;
}

public function getPassword()
{
    return $this->pwdHash;
}

public function getName()
{
    return $this->name;
}

public function getSchool()
{
    return $this->school;
}

public function getEdollar()
{
    return $this->edollar;
}

public function setEdollar($edollar)
{
    $this->edollar = $edollar;
}
}