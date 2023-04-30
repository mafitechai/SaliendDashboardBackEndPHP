<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    include_once('config/database.php');
    include_once('class/users.php');

    $database = new Database();
    $db = $database->getConnection();
    $items = new Users($db);
    $stmt = $items->getUsers();
    $itemCount = $stmt->rowCount();

    if($itemCount > 0){
        $userArr = array();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);
            $e = (object) array(
                "id" => $id,
                "uuid" => $uuid,
                "displayName" => $displayName,
                "email" => $email
            );

            array_push($userArr, $e);
        }

        echo json_encode($userArr);
    } else {
        http_response_code(404);
        echo json_encode(
            array("message" => "No record found.")
        );
    }
?>