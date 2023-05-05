<?php

class Cameras
{
    private $username = 'admin';
    private $passwd = 'S@ltex7509';
    public $url;

    private $conn;
    //Table
    private $db_table = 'slnt_cameras';
    //Columns
    public $camIntId;
    public $serverId;
    public $camName;
    public $groupId = 1;

    public function __construct($db)
    {
        $this->cronReadCameras();
        $this->postHeaders();
        $this->renderResponse();
        $this->conn = $db;
    }

    public function cronReadCameras()
    {
        $login = 'admin';
        $password = 'S@ltex7509';
        $url = $this->url;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'APIKEY: 111111111111111111111',
            'Content-Type: application/json',
        ]);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public function postHeaders()
    {
        //$this->response
    }

    public function renderResponse()
    {
    }

    public function callAPI($method, $url, $data)
    {
        $curl = curl_init();
        switch ($method) {
            case 'POST':
                curl_setopt($curl, CURLOPT_POST, 1);
                if ($data) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                }
                break;
            case 'PUT':
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
                if ($data) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                }
                break;
            default:
                if ($data) {
                    $url = sprintf('%s?%s', $url, http_build_query($data));
                }
        }
        // OPTIONS:
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'APIKEY: 111111111111111111111',
            'Content-Type: application/json',
        ]);
        $login = 'admin';
        $password = 'S@ltex7509';
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_USERPWD, "$login:$password");
        // EXECUTE:
        $result = curl_exec($curl);
        if (!$result) {
            die('Connection Failure');
        }
        curl_close($curl);
        return $result;
    }

    public function addCamera()
    {
        $query =
            'INSERT INTO ' .
            $this->db_table .
            "
         SET 
            camIntId = :camIntId,
            serverId = :serverId,
            camName = :camName,
            groupId = :groupId
         ";
        $stmt = $this->conn->prepare($query);

        //sanitaze
        $this->camIntId = htmlspecialchars(strip_tags($this->camIntId));
        $this->serverId = htmlspecialchars(strip_tags($this->serverId));
        $this->camName = htmlspecialchars(strip_tags($this->camName));
        $this->groupId = htmlspecialchars(strip_tags($this->groupId));
        //bind data
        $stmt->bindParam(':camIntId', $this->camIntId);
        $stmt->bindParam(':serverId', $this->serverId);
        $stmt->bindParam(':camName', $this->camName);
        $stmt->bindParam(':groupId', $this->groupId);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function isCamExist()
    {
        return true;
    }

    public function getCameras()
    {
        $queryGroups =
            'SELECT groupId,  groupName, groupEmail FROM slnt_groups';
        $stmtG = $this->conn->prepare($queryGroups);
        $stmtG->execute();
        $countG = 0;
        while ($rowG = $stmtG->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
            $query =
                "SELECT A.camIntId, B.serverName, C.groupName, C.groupEmail, A.camName  
                     FROM slnt_cameras AS A, slnt_servers AS B, slnt_groups AS C 
                     WHERE A.serverID = B.serverId 
                     AND A.groupId = C.groupId                      
                     AND A.groupId = '" .
                $rowG[0] .
                "'";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $count = 0;
            $data['groups'][$countG]['name'] = $rowG[1];
            $data['groups'][$countG]['email'][] = $rowG[2];
            while ($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
                $data['groups'][$countG]['camIntId'][] = $row[0];
                $data['groups'][$countG]['serverName'][] = $row[1];
                $data['groups'][$countG]['camName'][] = $row[4];
                $count++;
            }
            $countG++;
        }
        $data['total'] = $count;
        // print_r($data);
        return $data;
    }

    public function getCameraGroup($name)
    {
        $query = 'SELECT groupId FROM ' . $this->db_table . ' WHERE camName = ?';
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$name]);
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $row;
    }
}

?>
