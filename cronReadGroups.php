<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; text/plain; */*");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    require_once 'config/jwt_utils.php';
    include_once('config/database.php');
    include_once('class/cameras.php');

    $database = new Database();
    $db = $database->getConnection();
    $cameras = new Cameras($db);
    
    $camerasData = $cameras->getCameras();
    if(sizeof($camerasData) > 0){
        // for($i=0; $i < $camerasData['total']; $i++){

        // }

        echo json_encode( $camerasData);
    }  else {
        http_response_code(404);
        echo json_encode(
            array("message" => "No record found.")
        );
    }
?>