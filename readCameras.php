<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    include_once('config/database.php');
    include_once('class/servers.php');



    $database = new Database();
    $db = $database->getConnection();
    $items = new Servers($db);
    $stmt = $items->getServers2();

    if(sizeof($stmt) > 0){
        $serverArr = array();
        for($i=0; $i<sizeof($stmt); $i++){
            $e = (object) array(
                "id" => $stmt[$i]['serverId'],
                "name" => $stmt[$i]['serverName'],
                "ip" => $stmt[$i]['serverIp'],
                "port" => $stmt[$i]['serverPort'],
                "created" => $stmt[$i]['serverCreatedDate'],
                "modified" => $stmt[$i]['serverModifiedDate'],
                "modifiedBy" => $stmt[$i]['serverModifyBy'],
                "createdBy" => $stmt[$i]['serverCreatedBy']
            );
            array_push($serverArr, $e);
        }
        //localStorage.setItem('cameras')
        echo json_encode($serverArr);
    } else {
        http_response_code(404);
        echo json_encode(
            array("message" => "No record found.")
        );
    }
?>