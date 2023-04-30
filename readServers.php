<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    include_once('config/database.php');
    include_once('class/servers.php');

    $database = new Database();
    $db = $database->getConnection();
    $items = new Servers($db);
    $stmt = $items->getServers();
    $itemCount = $stmt->rowCount();

    if($itemCount > 0){
        $serverArr = array();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);
            $e = (object) array(
                "serverId" => $serverId,
                "serverName" => $serverName,
                "serverIp" => $serverIp,
                "serverPort" => $serverPort,
                "serverCreatedDate" => $serverCreatedDate,
                "serverModifiedDate" => $serverModifiedDate,
                "serverModifyBy" => $serverModifyBy,
                "serverCreatedBy" => $serverCreatedBy
            );

            array_push($serverArr, $e);
        }

        echo json_encode($serverArr);
    } else {
        http_response_code(404);
        echo json_encode(
            array("message" => "No record found.")
        );
    }
?>