<?php
require_once "common.php";
class AdminDAO
{
    /**
     * To retrive the admin password
     * 
     * @return string admin password
     */
    public function getPassword()
    {

        $conn_manager = new ConnectionManager();
        $conn = $conn_manager->getConnection();

        $sql = "SELECT password FROM Admin WHERE username = 'admin' ";

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        if ($row = $stmt->fetch()) {
            return $row['password'];
            $stmt->closeCursor();
            $conn = null;
        }

        $stmt->closeCursor();
        $conn = null;
        return null;
    }

    /**
     * To set the current round status or change round
     * 
     * @param int $round: the round to be set 
     * @param int $status: the status of the round (0 - ended; 1 - started)
     */
    public function setRound($round, $status)
    {

        $conn_manager = new ConnectionManager();
        $conn = $conn_manager->getConnection();

        $sql = "UPDATE admin SET round = :round, status = :status WHERE username = 'admin' ";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':round', $round, PDO::PARAM_INT);
        $stmt->bindParam(':status', $status, PDO::PARAM_INT);

        $stmt->execute();

        $stmt->closeCursor();
        $conn = null;
        return null;
    }

    /**
     * To retrieve the current round with its status
     * 
     * @return array: current round, the status of the round (0 - ended; 1 - started; -1: before bootstrap)
     * @param int $status: the status of the round (0 - ended; 1 - started)
     */
    public function getRound()
    {


        $conn_manager = new ConnectionManager();
        $conn = $conn_manager->getConnection();

        $sql = "SELECT round, status from Admin where username ='admin'";

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        if ($row = $stmt->fetch()) {
            return [$row['round'], $row['status']];
            $stmt->closeCursor();
            $conn = null;
        }

        $stmt->closeCursor();
        $conn = null;
        return null;
    }

    /**
     * To delete all information in the round clear table
     */
    public function deleteRoundClear()
    {

        $conn_manager = new ConnectionManager();
        $conn = $conn_manager->getConnection();

        $sql = "DELETE FROM round_clear WHERE TRUE";

        $stmt = $conn->prepare($sql);
        $status = $stmt->execute();

        return $status;
    }

    /**
     * To initialize the section status table in database when bootstrap
     * 
     * @param string $course_code: the course code of a section
     * @param string $section_id: the section number of the section
     * @param float $min_bid: the minimum bid of the section
     * @param int $vacancy: the vacancy of the section
     */
    public function initialiseSectionStatus($course_code, $section_id, $min_bid, $vacancy)
    {

        $conn_manager = new ConnectionManager();
        $conn = $conn_manager->getConnection();

        $sql = "INSERT INTO round_clear VALUES (:course_code, :section_id, :min_id, :vacancy)";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':course_code', $course_code, PDO::PARAM_STR);
        $stmt->bindParam(':section_id', $section_id, PDO::PARAM_STR);
        $stmt->bindParam(':min_id', $min_bid, PDO::PARAM_STR);
        $stmt->bindParam(':vacancy', $vacancy, PDO::PARAM_INT);

        $stmt->execute();

        $stmt->closeCursor();
        $conn = null;
        return null;
    }

    /**
     * To update the min bid and vacancy information of a particular section
     * 
     * @param string $course_code: the course code of a section
     * @param string $section_id: the section number of the section
     * @param float $min_bid: the new minimum bid of the section
     * @param int $vacancy: the new vacancy of the section
     * 
     */
    public function updateSectionStatus($course_code, $section_id, $min_bid, $vacancy)
    {
        $conn_manager = new ConnectionManager();
        $conn = $conn_manager->getConnection();

        $sql = "UPDATE round_clear SET min_bid= :min_bid, vacancy= :vacancy where course = :course_code and section =:section_id";

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':course_code', $course_code, PDO::PARAM_STR);
        $stmt->bindParam(':section_id', $section_id, PDO::PARAM_STR);
        $stmt->bindParam(':min_bid', $min_bid, PDO::PARAM_STR);
        $stmt->bindParam(':vacancy', $vacancy, PDO::PARAM_INT);

        $stmt->execute();

        $stmt->closeCursor();
        $conn = null;
        return null;
    }
    public function getVacancy($course_code, $section_id)
    {
        /**
         * To retrieve the vacancy of a particular section
         * 
         * @param string $course_code: the course code of a section
         * @param string $section_id: the section number of the section
         * 
         * @return int vacancy of the section
         */
        $conn_manager = new ConnectionManager();
        $conn = $conn_manager->getConnection();

        $sql = "SELECT vacancy from round_clear WHERE course = :course AND section = :section";

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':course', $course_code, PDO::PARAM_STR);
        $stmt->bindParam(':section', $section_id, PDO::PARAM_STR);

        $stmt->execute();
        if ($row = $stmt->fetch()) {
            return $row['vacancy'];
            $stmt->closeCursor();
            $conn = null;
        }

        $stmt->closeCursor();
        $conn = null;
        return null;
    }
    public function getMinBid($course_code, $section_id)
    {
        /**
         * To retrieve the minimum bid of a particular section
         * 
         * @param string $course_code: the course code of a section
         * @param string $section_id: the section number of the section
         * 
         * @return float minimum bid of the section
         */
        $conn_manager = new ConnectionManager();
        $conn = $conn_manager->getConnection();

        $sql = "SELECT min_bid from round_clear WHERE course = :course AND section = :section";

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->bindParam(':course', $course_code, PDO::PARAM_STR);
        $stmt->bindParam(':section', $section_id, PDO::PARAM_STR);

        $stmt->execute();
        if ($row = $stmt->fetch()) {
            return $row['min_bid'];
            $stmt->closeCursor();
            $conn = null;
        }

        $stmt->closeCursor();
        $conn = null;
        return null;
    }

    public function filterSectionInformation($conditions)
    {
        /**
         * To retrieve information of filtered sections based on the conditions from section-info.php
         * 
         * @param array $conditions: an associative array of the conditions in the form submitted by the user
         * 
         * @return array $sections: the sections fulfilled the conditions 
         */

        $conn_manager = new ConnectionManager();
        $conn = $conn_manager->getConnection();

        $sql = "SELECT 
                    s.course, 
                    s.section, 
                    c.school,
                    s.day,
                    s.start,
                    s.end,
                    s.instructor,
                    s.venue,
                    s.size
                 from section s INNER JOIN course c on c.course=s.course ";

        if (!empty($conditions)) {
            $sql .= " WHERE ";

            foreach ($conditions as $condition => $value) {
                if ($condition != 'school') {
                    $sql .= "s.$condition = '$value' AND ";
                } else {
                    $sql .= "c.$condition = '$value' AND ";
                }
            }
            $sql = substr($sql, 0, strlen($sql) - 4);
        }

        $stmt = $conn->prepare($sql);
        $status = $stmt->execute();
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
}
