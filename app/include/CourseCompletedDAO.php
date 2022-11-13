<?php
require_once 'common.php';
class CourseCompletedDAO
{
    public function retrieveAll()
    {

        $conn_manager = new ConnectionManager();
        $conn = $conn_manager->getConnection();

        $sql = "SELECT * FROM Course_Completed";

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $courses_completed = [];
        while ($row = $stmt->fetch()) {
            $courses_completed[] = new CourseCompleted(
                $row['userid'],
                $row['code']
            );
        }

        $stmt->closeCursor();
        $conn = null;

        return $courses_completed; // Return an array of Customer objects (if found any)
    }


    public function retrieveByStudent($userid)
    {
        $conn_manager = new ConnectionManager();
        $conn = $conn_manager->getConnection();

        $sql = "SELECT * FROM Course_Completed WHERE userid = :userid";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $courses_completed = [];
        while ($row = $stmt->fetch()) {
            $courses_completed[] = new CourseCompleted(
                $row['userid'],
                $row['code']
            );
        }

        $stmt->closeCursor();
        $conn = null;

        return $courses_completed;
    }

    public function deleteAll()
    {
        $conn_manager = new ConnectionManager();
        $conn = $conn_manager->getConnection();

        $sql = "DELETE FROM Course_Completed WHERE TRUE";

        $stmt = $conn->prepare($sql);
        $status = $stmt->execute();

        return $status;
    }


    public function add($course_completed)
    {
        $conn_manager = new ConnectionManager();
        $conn = $conn_manager->getConnection();

        $sql = "INSERT INTO Course_Completed (userid, code) VALUES 
        (   \"{$course_completed->getUserid()}\",
            \"{$course_completed->getCourse()}\"
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
