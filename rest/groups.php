<?php
require __DIR__ . "/inc/bootstrap.php";

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );
// print_r($uri);
if ((isset($uri[5]) && $uri[5] != 'group') || !isset($uri[6])) {
    header("HTTP/1.1 404 Not Found");
    exit();
}
require PROJECT_ROOT_PATH . "/Controller/Api/GroupController.php";
$objFeedController = new GroupController();
$strMethodName = $uri[6] . 'Action';
$objFeedController->{$strMethodName}();
?>