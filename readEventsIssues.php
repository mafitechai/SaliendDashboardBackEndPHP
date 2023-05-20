<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');

include_once 'config/database.php';
include_once 'class/events.php';

$database = new Database();
$db = $database->getConnection();
$events = new Events($db);


$issues = $events->getWeeklyEvents($_GET['group'], 'issues');
$disabled = $events->getWeeklyEvents($_GET['group'], 'disabled');
$failed = $events->getWeeklyEvents($_GET['group'], 'failed');
// print_r($events->getDailyEvents($_GET['group'], 'failed'));
$dailyDisabled = $events->getDailyEvents($_GET['group'], 'disabled');
$dailyIssues = $events->getDailyEvents($_GET['group'], 'issues');

$today = new DateTime(); // today
$begin = new DateTime(date('Y-m-d', strtotime('monday this week')));
$end = new DateTime();
$interval = new DateInterval('P1D'); // 1d interval range
$daterange = new DatePeriod($begin, $interval, $end); // it always runs forwards in date
foreach ($daterange as $date) {
    // date object
    $d[] = $date->format('Y-m-d'); // your date
}


$dataDisabled = $events->getListIssues($d, $dailyDisabled);
$dataIssues = $events->getListIssues($d, $dailyIssues);
$failuresToday = $events->countEvents('today', 'failed');
$dataAllEvents = $events->getAllEvents($_GET['group']);


$data['issues'] = $issues['Count'];
$data['disabled'] = $disabled['Count'];
$data['failed'] = $failed['Count'];
$data['graphDisabled'] =
    sizeof(array_values($dataDisabled)) > 0
        ? array_values($dataDisabled)
        : [0, 0, 0, 0, 0, 0, 0];
$data['graphIssues'] =
    sizeof(array_values($dataIssues)) > 0
        ? array_values($dataIssues)
        : [0, 0, 0, 0, 0, 0, 0];

$data['all'] = $dataAllEvents;

echo json_encode($data);


?>

