<?php
define("PROJECT_ROOT_PATH", __DIR__ . "/../");
// include main configuration file 
require_once PROJECT_ROOT_PATH . "/inc/config.php";
// include the base controller file 
require_once PROJECT_ROOT_PATH . "/Controller/Api/BaseController.php";
// include the use model file 
require_once PROJECT_ROOT_PATH . "/Model/UserModel.php";
// include the group model file 
require_once PROJECT_ROOT_PATH . "/Model/GroupModel.php";
// include the camera model file 
require_once PROJECT_ROOT_PATH . "/Model/CameraModel.php";
// include the camera model file 
require_once PROJECT_ROOT_PATH . "/Model/ServerModel.php";
?>