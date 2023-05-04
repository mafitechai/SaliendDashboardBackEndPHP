<?php
include_once 'config/database.php';
include_once 'class/events.php';
include_once 'class/users.php';
include_once 'class/cameras.php';
include_once 'class/groups.php';

$database = new Database();
$db = $database->getConnection();
$events = new Events($db);
$users = new Users($db);
$cameras = new Cameras($db);
$groups = new Groups($db);

$currentEvents = $events->getCurrentEvents();
$groupList = [];

foreach ($currentEvents as $key => $value) {
    $groupList[$key] = $value['groupId'];
}

$uniqueGroups = array_unique($groupList);

foreach ($uniqueGroups as $key => $value) {
    $email = $groups->getEmailsGroup($value)[0]['groupEmail'];
    $filterGroup = array_filter($currentEvents, function ($event) use ($value) {
        return $event['groupId'] == $value;
    });
    // echo '<pre>';
    // print_r($filterGroup);
    // echo '</pre>';

    $table = '';

    foreach ($filterGroup as $keyList => $valueList) {
        $template = file_get_contents('views/tempa.html');
        $row = file_get_contents('views/row.html');
        foreach ($valueList as $key => $value) {
            $row = str_replace('{{ ' . $key . ' }}', $value, $row);
        }

        $table = $table . $row;
    }

    $template = str_replace('{{ row }}', $table, $template);

    echo $email;
    echo $template;

    $to = $email;
    $subject = 'Report Cameras Fails';

    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type:text/html;charset=UTF-8' . "\r\n";

    $headers .= 'From: <wmmartinez.007@gmail.com>' . "\r\n";

    // mail($to, $subject, $template, $headers);
}

?>
