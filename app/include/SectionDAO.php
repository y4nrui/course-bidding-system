<?php
require_once 'common.php';
class SectionDAO
{

    public function retrieveAll()
    {

        $conn_manager = new ConnectionManager();
        $conn = $conn_manager->getConnection();

        $sql = "SELECT * FROM Section";

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $sections = [];
        while ($row = $stmt->fetch()) {
            $sections[] = new Section(
                $row['course'],
                $row['section'],
                $row['day'],
                $row['start'],
                $row['end'],
                $row['instructor'],
                $row['venue'],
                $row['size']
            );
        }

        $stmt->closeCursor();
        $conn = null;

        return $sections;
    }

    public function retrieveSection($course, $section)
    {
        $conn_manager = new ConnectionManager();
        $conn = $conn_manager->getConnection();

        $sql = "SELECT * FROM Section 
            WHERE course = :course AND section = :section";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':course', $course, PDO::PARAM_STR);
        $stmt->bindParam(':section', $section, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        if ($row = $stmt->fetch()) {
            return new Section(
                $row['course'],
                $row['section'],
                $row['day'],
                $row['start'],
                $row['end'],
                $row['instructor'],
                $row['venue'],
                $row['size']
            );
        }

        $stmt->closeCursor();
        $conn = null;
        return null;
    }

    public function retrieveByCourse($course)
    {
        $conn_manager = new ConnectionManager();
        $conn = $conn_manager->getConnection();

        $sql = "SELECT * FROM Section 
            WHERE course = :course";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':course', $course, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $sections = [];
        while ($row = $stmt->fetch()) {
            $sections[] =  new Section(
                $row['course'],
                $row['section'],
                $row['day'],
                $row['start'],
                $row['end'],
                $row['instructor'],
                $row['venue'],
                $row['size']
            );
        }

        $stmt->closeCursor();
        $conn = null;
        return $sections;
    }


    public function deleteAll()
    {
        $conn_manager = new ConnectionManager();
        $conn = $conn_manager->getConnection();

        $sql = "DELETE FROM Section WHERE TRUE";

        $stmt = $conn->prepare($sql);
        $status = $stmt->execute();

        return $status;
    }

    public function add($section)
    {
        $conn_manager = new ConnectionManager();
        $conn = $conn_manager->getConnection();

        $sql = "INSERT INTO Section (course,section,day,start,end,instructor,venue,size) VALUES 
        (:course, :section, :day, :start, :end, :instructor, :venue, :size)";

        $stmt = $conn->prepare($sql);

        @$stmt->bindParam(':course', $section->getCourse(), PDO::PARAM_STR);
        @$stmt->bindParam(':section', $section->getSection(), PDO::PARAM_STR);
        @$stmt->bindParam(':day', $section->getDay(), PDO::PARAM_STR);
        @$stmt->bindParam(':start', $section->getStart(), PDO::PARAM_STR);
        @$stmt->bindParam(':end', $section->getEnd(), PDO::PARAM_STR);
        @$stmt->bindParam(':instructor', $section->getInstructor(), PDO::PARAM_STR);
        @$stmt->bindParam(':venue', $section->getVenue(), PDO::PARAM_STR);
        @$stmt->bindParam(':size', $section->getSize(), PDO::PARAM_STR);

        if (!$status = $stmt->execute()) {
            return $stmt->errorInfo()[2];
        }

        $stmt->closeCursor();
        $conn = null;

        return null;
    }
}
