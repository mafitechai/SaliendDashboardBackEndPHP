<?php
      header("Access-Control-Allow-Origin: http://pirulin.com:3000"); 
      header("Content-Type: application/json; charset=UTF-8");
      header("Access-Control-Allow-Methods: POST");
      header("Access-Control-Max-Age: 3600");
      header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
      
    include_once('../config/database.php');
    include_once('../class/users.php');

    $database = new Database();
    $db = $database->getConnection();
    $item = new Users($db);

    $data = json_decode(file_get_contents("php://input"));
    $item->uuid = $data->uuid;
    $item->password = $data->password;
    $item->role ='staff';
    $item->displayName = $data->displayName;
    $item->photoURL = 'assets/images/avatars/marlon-cruces.jpg';
    $item->email = $data->email;

    if($item->createUser()){
        echo 'User created successfully.';
    } else{
        http_response_code(404);
        echo json_encode(
            array("message" => "User could not be created.")
        );
    }
?>