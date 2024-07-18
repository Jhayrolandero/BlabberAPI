
<?php
include_once __DIR__ . "/../Model/Database.php";
class Query extends Connection
{

    private $TABLE;
    function __construct($TABLE)
    {
        $this->TABLE = $TABLE;
    }

    public function selectQuery($cols = null, $cond = null)
    {
        $cols = $cols ? implode(",", $cols) : '*';
        $sql = "SELECT $cols from $this->TABLE";

        try {
            if (isset($cond)) {
                $condCol = $cond[0];
                $val = $cond[1];

                $sql .= " WHERE $condCol = ?";

                $stmt = $this->connect()->prepare($sql);

                $stmt->bindParam(1, $val);

                if ($stmt->execute()) {
                    return ["status" => 200, "message" => "Fetch successful", "data" => $stmt->fetchAll()];
                } else {
                    return ["status" => 500, "message" => "Failed to execute"];
                }
            }

            return  ["status" => 200, "message" => "Fetch successful", "data" => $this->connect()->query($sql)->fetchAll()];
        } catch (\PDOException $pDOException) {
            // return $pDOException;
            error_log($pDOException->getMessage());

            return ["status" => 500, "message" => "Failed to execute", "details" => $pDOException->getMessage()];
        }
    }

    public function unionQuery($cols = null, $bindID, $bindTable, $cond = null)
    {
        $cols = $cols ? implode(",", $cols) : '*';

        $sql = "SELECT $cols from $this->TABLE
                INNER JOIN `$bindTable[0]` on $this->TABLE.`$bindID[0]` = `$bindTable[0]`.`$bindID[0]`
                INNER JOIN `$bindTable[1]` on $this->TABLE.`$bindID[1]` = `$bindTable[1]`.`$bindID[1]`
        ";

        return $this->executeQuery($sql, $cond);
        // try {
        //     if (isset($cond)) {
        //         $condCol = $cond[0];
        //         $val = $cond[1];

        //         $sql .= " WHERE $condCol = ?";

        //         $stmt = $this->connect()->prepare($sql);
        //         if (is_array($val) && count($val) == 2) {
        //             $stmt->bindParam(1, $val[0]);
        //             $stmt->bindParam(2, $val[1]);
        //         } else {
        //             $stmt->bindParam(1, $val);
        //         }

        //         if ($stmt->execute()) {
        //             return ["status" => 200, "message" => "Fetch successful", "data" => $stmt->fetchAll()];
        //         } else {
        //             return ["status" => 500, "message" => "Failed to execute"];
        //         }
        //     }

        //     return  ["status" => 200, "message" => "Fetch successful", "data" => $this->connect()->query($sql)->fetchAll()];
        // } catch (\PDOException $pDOException) {
        //     // return $pDOException;
        //     error_log($pDOException->getMessage());

        //     return ["status" => 500, "message" => "Failed to execute", "details" => $pDOException->getMessage()];
        // }
    }

    public function executeQuery($sql, $cond = null)
    {
        try {
            if (isset($cond)) {
                $condCol = $cond[0];
                $val = $cond[1];

                $sql .= " WHERE $condCol = ?";

                $stmt = $this->connect()->prepare($sql);
                if (is_array($val) && count($val) == 2) {
                    $stmt->bindParam(1, $val[0]);
                    $stmt->bindParam(2, $val[1]);
                } else {
                    $stmt->bindParam(1, $val);
                }

                if ($stmt->execute()) {
                    return ["status" => 200, "message" => "Fetch successful", "data" => $stmt->fetchAll()];
                } else {
                    return ["status" => 500, "message" => "Failed to execute"];
                }
            }
            return  ["status" => 200, "message" => "Fetch successful", "data" => $this->connect()->query($sql)->fetchAll()];
        } catch (\PDOException $pDOException) {
            error_log($pDOException->getMessage());
            return ["status" => 500, "message" => "Failed to execute", "details" => $pDOException->getMessage()];
        }
    }
    public function insertQuery($data)
    {
        $cols = implode(",", $this->extractColumn($data)[0]);
        $placeholder = $this->extractColumn($data)[1];
        $vals = $this->extractValues($data);

        $sql = "INSERT INTO $this->TABLE ($cols)
                VALUES ($placeholder)";


        try {
            $stmt = $this->connect()->prepare($sql);
            // Glue
            $pos = 1;
            for ($i = 0; $i < count($vals); $i++) {
                $stmt->bindParam($pos, $vals[$i]);
                $pos += 1;
            }
            // return $stmt->execute();

            if ($stmt->execute()) {
                return ["status" => 200, "message" => "Insert successful"];
            } else {
                return ["status" => 500, "message" => "Failed to execute"];
            }
        } catch (\PDOException $pDOException) {
            // Log the error message
            error_log($pDOException->getMessage());

            return ["status" => 500, "message" => "Failed to execute", "details" => $pDOException->getMessage()];
        }
    }

    public function deleteQuery($cond, $id)
    {
        $sql = "DELETE FROM $this->TABLE WHERE $cond = ?";

        // return $sql;
        try {
            $stmt = $this->connect()->prepare($sql);
            $stmt->bindParam(1, $id);

            if ($stmt->execute()) {
                return ["status" => 200, "message" => "Delete successful"];
            } else {
                return ["status" => 500, "message" => "Failed to execute"];
            }
        } catch (\PDOException $pDOException) {
            // Log the error message
            error_log($pDOException->getMessage());

            return ["status" => 500, "message" => "Failed to execute", "details" => $pDOException->getMessage()];
        }
    }

    public function putQuery($data, $cond, $condVal)
    {
        $uCol = implode(",", $this->setColumn($data)[0]);
        $vals = $this->setColumn($data)[1];
        $sql = "UPDATE $this->TABLE
                SET $uCol
                WHERE $cond = ?";

        try {
            $stmt = $this->connect()->prepare($sql);
            // Glue
            $pos = 1;
            for ($i = 0; $i < count($vals); $i++) {
                $stmt->bindParam($pos, $vals[$i]);
                $pos += 1;
            }
            $stmt->bindParam($pos, $condVal);

            if ($stmt->execute()) {
                return ["status" => 200, "message" => "Updated successful"];
            } else {
                return ["status" => 500, "message" => "Failed to execute"];
            }
        } catch (\PDOException $pDOException) {
            // Log the error message
            error_log($pDOException->getMessage());

            return ["status" => 500, "message" => "Failed to execute", "details" => $pDOException->getMessage()];
        }
    }

    private function setColumn($data)
    {
        $setCols = [];
        $vals = [];
        foreach ($data as $key => $value) {
            $q = "$key = ?";
            array_push($setCols, $q);
            array_push($vals, $value);
            // echo $key . $value;
        }

        return [$setCols, $vals];
    }
    public function getLastID($table)
    {
        $env = parse_ini_file('.env');

        $DBName = $env["DB_NAME"];
        $sql = "SELECT AUTO_INCREMENT 
                FROM information_schema.TABLES 
                WHERE TABLE_SCHEMA = '$DBName' AND TABLE_NAME = '$table'";
        return $this->connect()->query($sql)->fetchAll();
    }

    private function extractColumn($data)
    {
        $cols = array_keys($data);
        $placeholderLen = count($cols);
        $placeHolder = [];

        for ($i = 0; $i < $placeholderLen; $i++) {
            array_push($placeHolder, "?");
        }

        return [$cols, implode(",", $placeHolder)];
    }

    private function extractValues($data)
    {
        $vals = array_values($data);
        return $vals;
    }
}
