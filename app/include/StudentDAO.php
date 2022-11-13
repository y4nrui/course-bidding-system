<?php
require_once 'common.php';

class StudentDAO
{

    public function retrieveAll()
    {

        $conn_manager = new ConnectionManager();
        $conn = $conn_manager->getConnection();

        $sql = "SELECT * FROM Student";

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $students = [];
        while ($row = $stmt->fetch()) {
            $students[] = new Student(
                $row['userid'],
                $row['password'],
                $row['name'],
                $row['school'],
                $row['edollar']
            );
        }

        $stmt->closeCursor();
        $conn = null;

        return $students; // Return an array of Customer objects (if found any)
    }

    public function retrieveStudent($userid)
    {
        $conn_manager = new ConnectionManager();
        $conn = $conn_manager->getConnection();

        $sql = "SELECT * FROM Student WHERE userid = :userid";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        if ($row = $stmt->fetch()) {
            return new Student(
                $row['userid'],
                $row['password'],
                $row['name'],
                $row['school'],
                $row['edollar']
            );
        }

        $stmt->closeCursor();
        $conn = null;

        return null;
    }

    public function deleteAll()
    {
        $conn_manager = new ConnectionManager();
        $conn = $conn_manager->getConnection();

        $sql = "DELETE FROM Student WHERE TRUE";

        $stmt = $conn->prepare($sql);
        $status = $stmt->execute();

        $stmt->closeCursor();
        $conn = null;

        return $status;
    }

    # $students here is an array of Student objects

    public function add($student)
    {
        $conn_manager = new ConnectionManager();
        $conn = $conn_manager->getConnection();

        $sql = "INSERT INTO Student (userid, password, name, school, edollar) VALUES 
        (   \"{$student->getUserid()}\",
            \"{$student->getPassword()}\",
            \"{$student->getName()}\",
            \"{$student->getSchool()}\",
            {$student->getEdollar()}
        )";

        $stmt = $conn->prepare($sql);

        if (!$status = $stmt->execute()) {
            return $stmt->errorInfo()[2];
        }

        $stmt->closeCursor();
        $conn = null;

        return null;
    }

    public function updateEdollar($userid, $edollar)
    {
        $conn_manager = new ConnectionManager();
        $conn = $conn_manager->getConnection();

        $sql = "UPDATE Student SET edollar = :edollar WHERE userid = :userid";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':edollar', $edollar, PDO::PARAM_STR);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);

        if (!$status = $stmt->execute()) {
            return $stmt->errorInfo()[2];
        }

        $stmt->closeCursor();
        $conn = null;

        return null;
    }
}
