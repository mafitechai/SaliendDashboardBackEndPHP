<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

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
                "password" => $password,
                "role" => $role,
                "accessToken" => $accessToken,
                "refreshToken" => $refreshToken,
                "displayName" => $displayName,
                "email" => $email,
                "groups" => $userGroups
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