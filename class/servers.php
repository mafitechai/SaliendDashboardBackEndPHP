<?php

    class Servers {
        private $conn;
        //Table
        private $db_table = "slnt_servers";
        //Columns
        public $serverId;
        public $serverName;
        public $serverIp;
        public $serverPort;
        public $serverCreatedDate;
        public $serverModifiedDate;
        public $serverModifyBy;
        public $serverCreatedBy;
        public $servers;

        //db connection
        public function __construct($db){
            $this->conn = $db;
        }

        //Get all
        public function getServers(){
           $query = "SELECT serverId, serverName, serverIp, serverPort, serverCreatedDate, serverModifiedDate, serverModifyBy, serverCreatedBy FROM " . $this->db_table . "";
           $stmt = $this->conn->prepare($query);
           $stmt->execute();
           return $stmt;
        }
        private function getServersIDsByGroups($groups){
            $group = explode(',',$groups);
            $count = 0;
            for($i=0; $i<sizeof($group); $i++){
                $query = "SELECT groupServers FROM slnt_groups WHERE groupId='" . $group[$i] . "'";
                $stmt = $this->conn->prepare($query);
                $stmt->execute();
                $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);

                $serverIdRes = $dataRow['groupServers'];
                $serverIds = explode(',', $serverIdRes);
                for($j=0; $j<sizeof($serverIds); $j++){
                    $serverIdArr[$count] = $serverIds[$j];
                    $count++; 
                }
            }
            return $serverIdArr;
        }

        public function getServers2($groups){
            if($groups == 0){
                $query = "SELECT serverId, serverName, serverIp, serverPort, serverCreatedDate, serverModifiedDate, serverModifyBy, serverCreatedBy FROM " . $this->db_table . "";    
            } else {
                $group = $this->getServersIDsByGroups($groups);
                $query = "SELECT serverId, serverName, serverIp, serverPort, serverCreatedDate, serverModifiedDate, serverModifyBy, serverCreatedBy FROM " . $this->db_table . "";
                for($i=0; $i<sizeof($group); $i++){
                    if($i==0){
                        $query .= " WHERE serverId='" . $group[$i] . "'";
                    } else {
                        $query .= " OR serverId='" . $group[$i] . "'";
                    }   
                }
            }
             $stmt = $this->conn->prepare($query);
             $stmt->execute();
             $count = 0;
             while ($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
                 $data[$count]['serverId'] = $row[0];
                 $data[$count]['serverName'] = $row[1];
                 $data[$count]['serverIp'] = $row[2];
                 $data[$count]['serverPort'] = $row[3];
                 $data[$count]['serverCreatedDate'] = $row[4];
                 $data[$count]['serverModifiedDate'] = $row[5];
                 $data[$count]['serverModifyBy'] = $row[6];
                 $data[$count]['serverCreatedBy'] = $row[7];
                 $count++;
             }
             
             //print_r($data);
             return $data;
         }

         public function getServersByGroups(){
            $query = "SELECT serverId, serverName, serverIp, serverPort, serverCreatedDate, serverModifiedDate, serverModifyBy, serverCreatedBy FROM " . $this->db_table . "";
             $stmt = $this->conn->prepare($query);
             $stmt->execute();
             $count = 0;
             while ($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
                 $data[$count]['serverId'] = $row[0];
                 $data[$count]['serverName'] = $row[1];
                 $data[$count]['serverIp'] = $row[2];
                 $data[$count]['serverPort'] = $row[3];
                 $data[$count]['serverCreatedDate'] = $row[4];
                 $data[$count]['serverModifiedDate'] = $row[5];
                 $data[$count]['serverModifyBy'] = $row[6];
                 $data[$count]['serverCreatedBy'] = $row[7];
                 $count++;
             }
             return $data;
         }

        //Create
        public function createServer(){
            $query = "INSERT INTO " . $this->db_table . "
                SET 
                    serverName = :serverName,
                    serverIp = :serverIp,
                    serverPort = :serverPort,
                    serverCreatedDate = :serverCreatedDate,
                    serverCreatedBy = :serverCreatedBy
            ";
            $stmt = $this->conn->prepare($query);

            //sanitaze
            $this->serverName = htmlspecialchars(strip_tags($this->serverName));
            $this->serverIp = htmlspecialchars(strip_tags($this->serverIp));
            $this->serverPort = htmlspecialchars(strip_tags($this->serverPort));
            $this->serverCreatedDate = htmlspecialchars(strip_tags($this->serverCreatedDate));
            $this->serverCreatedBy = htmlspecialchars(strip_tags($this->serverCreatedBy));

            //bind data
            $stmt->bindParam(":serverName", $this->serverName);
            $stmt->bindParam(":serverIp", $this->serverIp);
            $stmt->bindParam(":serverPort", $this->serverPort);
            $stmt->bindParam(":serverCreatedDate", $this->serverCreatedDate);
            $stmt->bindParam(":serverCreatedBy", $this->serverCreatedBy);

            if($stmt->execute()){
                return true;
             }
             return false;
        }

        // read single Server
        public function getSingleServer(){
            $query = "SELECT 
                        serverId, serverName, serverIp, serverPort, serverCreatedDate, serverModifiedDate, serverModifyBy, serverCreatedBy
                    FROM 
                        " . $this->db_table . "
                    WHERE serverId=:serverId";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":serverId", $this->serverId);
            $stmt->execute();
            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->serverId = $dataRow['serverId'];
            $this->serverName = $dataRow['serverName'];
            $this->serverIp = $dataRow['serverIp'];
            $this->serverPort = $dataRow['serverPort'];
            $this->serverCreatedDate = $dataRow['serverCreatedDate'];
            $this->serverModifiedDate = $dataRow['serverModifiedDate'];
            $this->serverModifyBy = $dataRow['serverModifyBy'];
            $this->serverCreatedBy = $dataRow['serverCreatedBy'];
        }

        //Update
        public function updateServer(){
            $query = "UPDATE " . $this->db_table . " 
                SET 
                    serverName = :serverName, 
                    serverIp = :serverIp,
                    serverPort = :serverPort,
                    serverModifiedDate = :serverModifiedDate,
                    serverModifyBy = :serverModifyBy
                WHERE
                    id = :id
            ";

            $stmt = $this->conn->prepare($query);

            //sanitaze
            $this->serverName = htmlspecialchars(strip_tags($this->serverName));
            $this->serverIp = htmlspecialchars(strip_tags($this->serverIp));
            $this->serverPort = htmlspecialchars(strip_tags($this->serverPort));
            $this->serverModifiedDate = htmlspecialchars(strip_tags($this->serverModifiedDate));
            $this->serverModifyBy = htmlspecialchars(strip_tags($this->serverModifyBy));

            //bind data
            $stmt->bindParam(":serverName", $this->serverName);
            $stmt->bindParam(":serverIp", $this->serverIp);
            $stmt->bindParam(":serverPort", $this->serverPort);
            $stmt->bindParam(":serverModifiedDate", $this->serverModifiedDate);
            $stmt->bindParam(":serverModifyBy", $this->serverModifyBy);

            if($stmt->execute()){
                return true;
             }
             return false;
        }

        // Delete
        public function deleteServer(){
            $query = "DELETE FROM " . $this->db_table . " WHERE serverId= ?";
            $stmt = $this->conn->prepare($query);

            $this->serverId= htmlspecialchars(strip_tags($this->serverId));
            $stmt->bindParam(1,$this->serverId);

            if($stmt->execute()){
                return true;
            }
            return false;
        }
    }
?>