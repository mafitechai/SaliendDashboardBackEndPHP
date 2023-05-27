<?php
include_once 'config/database.php';
include_once 'class/events.php';
include_once 'class/users.php';
include_once 'class/cameras.php';
include_once 'class/groups.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

$database = new Database();
$db = $database->getConnection();
$events = new Events($db);
$users = new Users($db);
$cameras = new Cameras($db);
$groups = new Groups($db);

$currentEvents = $events->getCurrentEvents();
$groupList = [];
$today = date('Y-m-d H:i:s');


foreach ($currentEvents as $key => $value) {
    $currentEvents[$key]['days'] =
        round((strtotime($today) - strtotime($value['event_date'])) / 86400);
    
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

    // $today = date('Y-m-d H:i:s');
    // $days = (strtotime($today) - strtotime($upinsert[4])) / 86400;

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
    $from = 'info@mafi.ai';
    $from_name = 'Support Notification System';

    $mail = new PHPMailer();

    $mail->isSMTP();

    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'info@mafi.ai';
    $mail->Password = 'zvjnkownpclikuef';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // $mail->SMTPDebug  = 3;
    // $mail->Debugoutput = function($str, $level) {echo "debug level $level; message: $str";}; //$mail->Debugoutput = 'echo';

    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true,
        ],
    ];

    $mail->IsHTML(true);
    $mail->setFrom('support@saltexgroup.com', 'Support Notification System');
    $mail->AddReplyTo($from, $from_name);
    // $mail->addBcc('mauricio@saltexgroup.com','Mauricio Salmon');
    // $mail->addBcc('marloncs@gmail.com','Marlon Cruces');
    // $mail->addBcc('nick@saltexgroup.com','Nick Pineda');
    $mail->Subject = $subject;
    $mail->Body = $template;
    $mail->AddAddress($to);

    if (!$mail->send()) {
        echo 'Message could not be sent.';
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        echo 'Message has been sent';
    }
}

?>
