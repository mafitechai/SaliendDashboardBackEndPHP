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

$currentEvents = $events->getEventsFiveMinutes();
$groupList = [];
$emailTo = [];
// print_r($currentEvents);

foreach ($currentEvents as $key => $value) {
    $groupList[$key] = $value['groupId'];
}

$uniqueGroups = array_unique($groupList);

foreach ($uniqueGroups as $key => $value) {
    $email = $groups->getEmailsGroup($value)[0]['groupEmail'];
    $emails = $users->getEmailsGroup($value);
    $filterGroup = array_filter($currentEvents, function ($event) use ($value) {
        return $event['groupId'] == $value;
    });
    foreach ($emails as $emailKey => $emailValue) {
        $emailTo[$emailKey] = $emailValue['email'];
    }
    // echo '<pre>';
    // print_r($emailTo);
    // echo '</pre>';

    if(!in_array($email, $emailTo)){
        array_push($emailTo, $email);
    }

    // echo '<pre>';
    // print_r($emailTo);
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

    echo implode(',', $emailTo);
    echo $template;

    $to = implode(',', $emailTo);
    $subject = 'Report Cameras Fails';

    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type:text/html;charset=UTF-8' . "\r\n";

    $headers .= 'From: <wmmartinez.007@gmail.com>' . "\r\n";

    // mail($to, $subject, $template, $headers);
}

?>
