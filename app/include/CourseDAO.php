<?php
require_once "common.php";
class CourseDAO
{

    public function retrieveAll()
    {

        $conn_manager = new ConnectionManager();
        $conn = $conn_manager->getConnection();

        $sql = "SELECT * FROM Course";

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $courses = [];
        while ($row = $stmt->fetch()) {
            $courses[] = new Course(
                $row['course'],
                $row['school'],
                $row['title'],
                $row['description'],
                $row['exam date'],
                $row['exam start'],
                $row['exam end']
            );
        }

        $stmt->closeCursor();
        $conn = null;

        return $courses; // Return an array of Customer objects (if found any)
    }

    public function retrieveCourse($course)
    {
        $conn_manager = new ConnectionManager();
        $conn = $conn_manager->getConnection();

        $sql = "SELECT * FROM Course WHERE course = :course";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':course', $course, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        if ($row = $stmt->fetch()) {
            return new Course(
                $row['course'],
                $row['school'],
                $row['title'],
                $row['description'],
                $row['exam date'],
                $row['exam start'],
                $row['exam end']
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

        $sql = "DELETE FROM Course WHERE TRUE";

        $stmt = $conn->prepare($sql);
        $status = $stmt->execute();

        return $status;
    }

    public function add($course)
    {
        $conn_manager = new ConnectionManager();
        $conn = $conn_manager->getConnection();

        $sql = "INSERT INTO Course (course,school,title,description,`exam date`,`exam start`,`exam end`) VALUES 
        (   \"{$course->getCourse()}\",
            \"{$course->getSchool()}\",
            \"{$course->getTitle()}\",
            \"{$course->getDescription()}\",
            \"{$course->getExamDate()}\",
            \"{$course->getExamStart()}\",
            \"{$course->getExamEnd()}\"
        )";

        $stmt = $conn->prepare($sql);

        if (!$status = $stmt->execute()) {
            return $stmt->errorInfo()[2];
        }

        $stmt->closeCursor();
        $conn = null;

        return null;
    }
}
