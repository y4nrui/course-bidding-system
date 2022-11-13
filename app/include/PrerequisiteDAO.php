<?php
require_once 'common.php';
class PrerequisiteDAO
{

    public function retrieveAll()
    {

        $conn_manager = new ConnectionManager();
        $conn = $conn_manager->getConnection();

        $sql = "SELECT * FROM Prerequisite";

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $course_prerequisites = [];
        while ($row = $stmt->fetch()) {
            $course_prerequisites[] = new Prerequisite(
                $row['course'],
                $row['prerequisite']
            );
        }

        $stmt->closeCursor();
        $conn = null;

        return $course_prerequisites; // Return an array of Customer objects (if found any)
    }

    # This function is to get all pre-requisites for a course 
    public function retrieveByCourse($course)
    {
        $conn_manager = new ConnectionManager();
        $conn = $conn_manager->getConnection();

        $sql = "SELECT * FROM Prerequisite WHERE course = :course";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':course', $course, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $prerequisites = [];
        while ($row = $stmt->fetch()) {
            $prerequisites[] = new Prerequisite(
                $row['course'],
                $row['prerequisite']
            );
        }

        $stmt->closeCursor();
        $conn = null;

        return $prerequisites;
    }

    public function deleteAll()
    {
        $conn_manager = new ConnectionManager();
        $conn = $conn_manager->getConnection();

        $sql = "DELETE FROM Prerequisite WHERE TRUE";

        $stmt = $conn->prepare($sql);
        $status = $stmt->execute();

        return $status;
    }


    public function add($prereq)
    {
        $conn_manager = new ConnectionManager();
        $conn = $conn_manager->getConnection();

        $sql = "INSERT INTO Prerequisite (course, prerequisite) VALUES 
        (:course, :prerequisite)";

        $stmt = $conn->prepare($sql);
        @$stmt->bindParam(':course', $prereq->getCourse(), PDO::PARAM_STR);
        @$stmt->bindParam(':prerequisite', $prereq->getPrerequisite(), PDO::PARAM_STR);

        if (!$status = $stmt->execute()) {
            return $stmt->errorInfo()[2];
        }

        $stmt->closeCursor();
        $conn = null;

        return null;
    }
}
