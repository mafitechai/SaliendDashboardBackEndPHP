<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    include_once 'config/database.php';
    include_once 'class/events.php';

    $database = new Database();
    $db = $database->getConnection();
    $events = new Events($db);


    $today = new DateTime(); // today
    $begin = $today->sub(new DateInterval('P30D')); //created 30 days interval back
    $end = new DateTime();
    $end = $end->modify('+1 day'); // interval generates upto last day
    $interval = new DateInterval('P1D'); // 1d interval range
    $daterange = new DatePeriod($begin, $interval, $end); // it always runs forwards in date
    foreach ($daterange as $date) {
        $d[] = $date->format('Y-m-d'); // your date
    }

    $eventsDays = $events->getLastMonthEvents($_GET['group']);
    

    $monthlyData = $events->getListIssues($d, $eventsDays, 'M d');

    $data = array_values($monthlyData);
    $labels = array_keys($monthlyData);
    $list['data'] = $data;
    $list['labels'] = $labels;
    // print_r($listEvents);
    if (sizeof($data) > 0) {
        echo json_encode($list);
    } else {
        http_response_code(404);
        echo json_encode(['message' => 'No record found.']);
    }

?>
