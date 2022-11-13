<?php
require_once 'common.php';
class BidDAO
{

    public function retrieveAll()
    {

        $conn_manager = new ConnectionManager();
        $conn = $conn_manager->getConnection();

        $sql = "SELECT * FROM Bid";

        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $bids = [];
        while ($row = $stmt->fetch()) {
            $bids[] = new Bid(
                $row['userid'],
                $row['amount'],
                $row['code'],
                $row['section'],
                $row['result']
            );
        }

        $stmt->closeCursor();
        $conn = null;

        return $bids; // Return an array of Customer objects (if found any)
    }

    public function retrieveBySection($course, $section)
    {
        $conn_manager = new ConnectionManager();
        $conn = $conn_manager->getConnection();

        $sql = "SELECT * FROM Bid WHERE code = :course AND section = :section";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':course', $course, PDO::PARAM_STR);
        $stmt->bindParam(':section', $section, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $section_bids = [];
        while ($row = $stmt->fetch()) {
            $section_bids[] = new Bid(
                $row['userid'],
                $row['amount'],
                $row['code'],
                $row['section'],
                $row['result']
            );
        }

        $stmt->closeCursor();
        $conn = null;
        return $section_bids;
    }

    public function retrieveByStudent($userid)
    {
        $conn_manager = new ConnectionManager();
        $conn = $conn_manager->getConnection();

        $sql = "SELECT * FROM Bid WHERE userid = :userid";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        $student_bids = [];
        while ($row = $stmt->fetch()) {
            $student_bids[] = new Bid(
                $row['userid'],
                $row['amount'],
                $row['code'],
                $row['section'],
                $row['result']
            );
        }

        $stmt->closeCursor();
        $conn = null;
        return $student_bids;
    }

    public function retrieveBid($userid, $course)
    {
        $conn_manager = new ConnectionManager();
        $conn = $conn_manager->getConnection();

        $sql = "SELECT * FROM Bid WHERE code = :course AND userid = :userid";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':course', $course, PDO::PARAM_STR);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $stmt->execute();

        if ($row = $stmt->fetch()) {
            return new Bid(
                $row['userid'],
                $row['amount'],
                $row['code'],
                $row['section'],
                $row['result']
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

        $sql = "DELETE FROM Bid WHERE TRUE";

        $stmt = $conn->prepare($sql);
        $status = $stmt->execute();

        return $status;
    }

    public function add($bid)
    {
        $conn_manager = new ConnectionManager();
        $conn = $conn_manager->getConnection();

        $sql = 'INSERT INTO Bid VALUES (:userid,:amount, :course, :section, :result)';

        $stmt = $conn->prepare($sql);
        @$stmt->bindParam(':userid', $bid->getUserid(), PDO::PARAM_STR);
        @$stmt->bindParam(':course', $bid->getCourse(), PDO::PARAM_STR);
        @$stmt->bindParam(':section', $bid->getSection(), PDO::PARAM_STR);
        @$stmt->bindParam(':amount', $bid->getAmount(), PDO::PARAM_STR);
        @$stmt->bindParam(':result', $bid->getResult(), PDO::PARAM_STR);


        if (!$status = $stmt->execute()) {
            return $stmt->errorInfo()[2];
        }

        $stmt->closeCursor();
        $conn = null;

        return null;
    }

    public function updateBid($userid, $course, $amount)
    {
        $conn_manager = new ConnectionManager();
        $conn = $conn_manager->getConnection();

        $sql = "UPDATE Bid SET amount = :amount WHERE userid = :userid AND code = :course";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':course', $course, PDO::PARAM_STR);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->bindParam(':amount', $amount, PDO::PARAM_STR);


        if (!$status = $stmt->execute()) {
            return $stmt->errorInfo()[2];
        }

        $stmt->closeCursor();
        $conn = null;

        return null;
    }

    public function dropBid($userid, $course, $section)
    {
        $conn_manager = new ConnectionManager();
        $conn = $conn_manager->getConnection();

        $sql = "DELETE FROM bid WHERE userid =:userid AND code =:course AND section =:section";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':course', $course, PDO::PARAM_STR);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->bindParam(':section', $section, PDO::PARAM_STR);


        $stmt->execute();

        return null;
    }

    public function updateResult($userid, $course, $round, $result)
    {
        $conn_manager = new ConnectionManager();
        $conn = $conn_manager->getConnection();

        $sql = "UPDATE Bid SET result = :result WHERE userid = :userid AND code = :course";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':course', $course, PDO::PARAM_STR);
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->bindParam(':result', $result, PDO::PARAM_STR);

        if (!$status = $stmt->execute()) {
            return $stmt->errorInfo()[2];
        }

        $stmt->closeCursor();
        $conn = null;

        return null;
    }
}
