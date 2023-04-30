<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    include_once('config/database.php');
    include_once('class/groups.php');

    $database = new Database();
    $db = $database->getConnection();
    $items = new Groups($db);
    $stmt = $items->getGroups();
    $itemCount = $stmt->rowCount();

    if($itemCount > 0){
        $groupArr = array();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);
            $e = (object) array(
                "groupId" => $groupId,
                "groupName" => $groupName,
                "groupEmail" => $groupEmail
            );

            array_push($groupArr, $e);
        }

        echo json_encode($groupArr);
    } else {
        http_response_code(404);
        echo json_encode(
            array("message" => "No record found.")
        );
    }
?>