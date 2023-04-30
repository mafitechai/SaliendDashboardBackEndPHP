<?php

    class Users {
        private $conn;
        //Table
        private $db_table = "slnt_users";
        //Columns
        public $id;
        public $uuid;
        public $password;
        public $role;
        public $displayName;
        public $photoURL;
        public $email;

        //db connection
        public function __construct($db){
            $this->conn = $db;
        }

        //Get all
        public function getUsers(){
            $query = "SELECT id, accessToken, refreshToken, password, role, displayName, photoURL, email, userGroups FROM " . $this->db_table . "";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        }

        //Create
        public function createUser(){
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

        // read single user
        public function getSingleUser(){
            $query = "SELECT 
                        id, 
                        uuid,
                        password,
                        role,
                        displayName,
                        photoURL,
                        email
                    FROM 
                        " . $this->db_table . "
                    WHERE id=:id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $this->id);
            $stmt->execute();
            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->id = $dataRow['id'];
            $this->uuid = $dataRow['uuid'];
            $this->password = $dataRow['password'];
            $this->role = $dataRow['role'];
            $this->displayName = $dataRow['displayName'];
            $this->photoURL = $dataRow['photoURL'];
            $this->email = $dataRow['email'];

        }

        //Update
        public function updateUser(){
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
        public function deleteUser(){
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