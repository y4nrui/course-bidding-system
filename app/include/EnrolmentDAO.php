<?php
require_once "common.php";

class EnrolmentDAO
{
    public function retrieveAll()
    {
        $conn_manager = new ConnectionManager();
        $conn = $conn_manager->getConnection();

        $sql = "SELECT * FROM Enrolment";

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $enrolments = [];
        while ($row = $stmt->fetch()) {
            $enrolments[] = new Enrolment(
                $row['userid'],
                $row['code'],
                $row['section'],
                $row['amount'],
                $row['round']
            );
        }

        $stmt->closeCursor();
        $conn = null;

        return $enrolments; // Return an array of Customer objects (if found any)
    }

    public function retrieveEnrolmentBySection($course, $section)
    {
        $conn_manager = new ConnectionManager();
        $conn = $conn_manager->getConnection();

        $sql = "SELECT * FROM Enrolment WHERE code = :course AND section = :section ";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':course', $course, PDO::PARAM_STR);
        $stmt->bindParam(':section', $section, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $enrolments = [];
        while ($row = $stmt->fetch()) {
            $enrolments[] = new Enrolment(
                $row['userid'],
                $row['code'],
                $row['section'],
                $row['amount'],
                $row['round']
            );
        }

        $stmt->closeCursor();
        $conn = null;

        return $enrolments; // Return an array of Customer objects (if found any)
    }

    public function retrieveEnrolmentByRound($round)
    {
        $conn_manager = new ConnectionManager();
        $conn = $conn_manager->getConnection();

        $sql = "SELECT * FROM Enrolment WHERE round = :round ";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':round', $round, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $enrolments = [];
        while ($row = $stmt->fetch()) {
            $enrolments[] = new Enrolment(
                $row['userid'],
                $row['code'],
                $row['section'],
                $row['amount'],
                $row['round']
            );
        }

        $stmt->closeCursor();
        $conn = null;

        return $enrolments; // Return an array of Customer objects (if found any)
    }

    public function retrieveEnrolmentByStudent($userid)
    {
        $conn_manager = new ConnectionManager();
        $conn = $conn_manager->getConnection();

        $sql = "SELECT * FROM Enrolment WHERE userid = :userid ";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $enrolments = [];
        while ($row = $stmt->fetch()) {
            $enrolments[] = new Enrolment(
                $row['userid'],
                $row['code'],
                $row['section'],
                $row['amount'],
                $row['round']
            );
        }

        $stmt->closeCursor();
        $conn = null;

        return $enrolments; // Return an array of Customer objects (if found any)
    }

    public function deleteAll()
    {
        $conn_manager = new ConnectionManager();
        $conn = $conn_manager->getConnection();

        $sql = "DELETE FROM Enrolment WHERE TRUE";

        $stmt = $conn->prepare($sql);
        $status = $stmt->execute();

        return $status;
    }

    public function deleteEnrolment($userid, $course)
    {
        $conn_manager = new ConnectionManager();
        $conn = $conn_manager->getConnection();

        $sql = "DELETE FROM Enrolment WHERE userid = :userid and code = :course";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':course', $course, PDO::PARAM_STR);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $status = $stmt->execute();

        return $status;
    }

    public function addEnrolment($enrolment)
    {
        $conn_manager = new ConnectionManager();
        $conn = $conn_manager->getConnection();

        $sql = "INSERT INTO Enrolment (userid, code, section, amount, round) VALUES 
        (   \"{$enrolment->getUserid()}\",
            \"{$enrolment->getCourse()}\",
            \"{$enrolment->getSection()}\",
            \"{$enrolment->getAmount()}\",
            {$enrolment->getRound()}
        )";

        $stmt = $conn->prepare($sql);

        if (!$status = $stmt->execute()) {
            return $stmt->errorInfo()[2];
        }

        $stmt->closeCursor();
        $conn = null;

        return null;
    }
    public function retrieveEnrolmentBySectionRound($course, $section, $round)
    {
        $conn_manager = new ConnectionManager();
        $conn = $conn_manager->getConnection();

        $sql = "SELECT * FROM Enrolment WHERE code = :course AND section = :section AND round = :round";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':course', $course, PDO::PARAM_STR);
        $stmt->bindParam(':section', $section, PDO::PARAM_STR);
        $stmt->bindParam(':round', $round, PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $enrolments = [];
        while ($row = $stmt->fetch()) {
            $enrolments[] = new Enrolment(
                $row['userid'],
                $row['code'],
                $row['section'],
                $row['amount'],
                $row['round']
            );
        }

        $stmt->closeCursor();
        $conn = null;

        return $enrolments; // Return an array of Customer objects (if found any)
    }

    public function retrieveEnrolmentByStudentCourse($userid, $course) {
        $conn_manager = new ConnectionManager();
        $conn = $conn_manager->getConnection();

        $sql = "SELECT * FROM Enrolment WHERE code = :course AND userid = :userid";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':course', $course, PDO::PARAM_STR);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        
        if ($row = $stmt->fetch()) {
            return new Enrolment(
                $row['userid'],
                $row['code'],
                $row['section'],
                $row['amount'],
                $row['round']
            );
        }

        $stmt->closeCursor();
        $conn = null;

        return null;
    }
}
