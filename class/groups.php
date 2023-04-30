<?php

    class Groups {
        private $conn;
        //Table
        private $db_table = "slnt_groups";
        //Columns
        public $groupId;
        public $groupName;
        public $groupEmail;

        //db connection
        public function __construct($db){
            $this->conn = $db;
        }

        //Get all
        public function getGroups(){
            $query = "SELECT groupId, groupName, groupEmail FROM " . $this->db_table . "";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        }

        //Create
        public function createGroup(){
            $query = "INSERT INTO " . $this->db_table . "
                SET 
                    uuid = :uuid, 
                    password = :password,
                    role = :role,
                    displayName = :displayName,
                    photoURL = :photoURL,
                    email = :email
            ";
            $stmt = $this->conn->prepare($query);

            //sanitaze
            $this->uuid = htmlspecialchars(strip_tags($this->uuid));
            $this->password = htmlspecialchars(strip_tags($this->password));
            $this->role = htmlspecialchars(strip_tags($this->role));
            $this->displayName = htmlspecialchars(strip_tags($this->displayName));
            $this->photoURL = htmlspecialchars(strip_tags($this->photoURL));
            $this->email = htmlspecialchars(strip_tags($this->email));

            //bind data
            $stmt->bindParam(":uuid", $this->uuid);
            $stmt->bindParam(":password", $this->password);
            $stmt->bindParam(":role", $this->role);
            $stmt->bindParam(":displayName", $this->displayName);
            $stmt->bindParam(":photoURL", $this->photoURL);
            $stmt->bindParam(":email", $this->email);

            if($stmt->execute()){
                return true;
             }
             return false;
        }

        //Update
        public function updateGroup(){
            $query = "UPDATE " . $this->db_table . " 
                SET 
                    uuid = :uuid, 
                    password = :password,
                    role = :role,
                    displayName = :displayName,
                    photoURL = :photoURL,
                    email = :email 
                WHERE
                    id = :id
            ";

            $stmt = $this->conn->prepare($query);

            //sanitaze
            $this->uuid = htmlspecialchars(strip_tags($this->uuid));
            $this->password = htmlspecialchars(strip_tags($this->password));
            $this->role = htmlspecialchars(strip_tags($this->role));
            $this->displayName = htmlspecialchars(strip_tags($this->displayName));
            $this->photoURL = htmlspecialchars(strip_tags($this->photoURL));
            $this->email = htmlspecialchars(strip_tags($this->email));

            //bind data
            $stmt->bindParam(":uuid", $this->uuid);
            $stmt->bindParam(":password", $this->password);
            $stmt->bindParam(":role", $this->role);
            $stmt->bindParam(":displayName", $this->displayName);
            $stmt->bindParam(":photoURL", $this->photoURL);
            $stmt->bindParam(":email", $this->email);

            if($stmt->execute()){
                return true;
             }
             return false;
        }

        // Delete
        public function deleteGroup(){
            $query = "DELETE FROM " . $this->db_table . " WHERE id= ?";
            $stmt = $this->conn->prepare($query);

            $this->id= htmlspecialchars(strip_tags($this->id));
            $stmt->bindParam(1,$this->id);

            if($stmt->execute()){
                return true;
            }
            return false;
        }
    }
?>