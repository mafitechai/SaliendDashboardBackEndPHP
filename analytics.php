<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; text/plain; */*");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    include_once('config/database.php');
    include_once('class/events.php');

    $database = new Database();
    $db = $database->getConnection();
    $events = new Events($db);
    
    //$todayEvents = $events->getTodayEvents();

    if(true){
        // club 1
        $arrToJson['project_dashboard_widgets']['value']['newVsReturning']['uniqueVisitors'] = 5;
        $arrToJson['project_dashboard_widgets']['value']['newVsReturning']['labels'] = ['Online', 'Failed', 'Disable']; 
        $arrToJson['project_dashboard_widgets']['value']['newVsReturning']['series'] = [60, 40, 0];
        // club 2
        $arrToJson['project_dashboard_widgets']['value']['gender']['uniqueVisitors'] = 5;
        $arrToJson['project_dashboard_widgets']['value']['gender']['labels'] = ['Online', 'Failed', 'Disable']; 
        $arrToJson['project_dashboard_widgets']['value']['gender']['series'] = [100, 0, 0];
        // club 3
        $arrToJson['project_dashboard_widgets']['value']['age']['uniqueVisitors'] = 5;
        $arrToJson['project_dashboard_widgets']['value']['age']['labels'] = ['Online', 'Failed', 'Disable']; 
        $arrToJson['project_dashboard_widgets']['value']['age']['series'] = [80, 20, 0];
        // club 4
        $arrToJson['project_dashboard_widgets']['value']['language']['uniqueVisitors'] = 5;
        $arrToJson['project_dashboard_widgets']['value']['language']['labels'] = ['Online', 'Failed', 'Disable']; 
        $arrToJson['project_dashboard_widgets']['value']['language']['series'] = [80, 20, 20];

        // online
        $arrToJson['project_dashboard_widgets']['value']['conversions']['amount'] = 403;
        $arrToJson['project_dashboard_widgets']['value']['conversions']['labels'] = ['OnLine','OnLine','OnLine','OnLine'];
        $arrToJson['project_dashboard_widgets']['value']['conversions']['series'][0]['data'] = [403,405,404,403];
        $arrToJson['project_dashboard_widgets']['value']['conversions']['series'][0]['name'] = "Online";

        // failed
        $arrToJson['project_dashboard_widgets']['value']['impressions']['amount'] = 1;
        $arrToJson['project_dashboard_widgets']['value']['impressions']['labels'] = ['Failed','Failed','Failed','Failed'];
        $arrToJson['project_dashboard_widgets']['value']['impressions']['series'][0]['data'] = [0,0,1,1];
        $arrToJson['project_dashboard_widgets']['value']['impressions']['series'][0]['name'] = "Failed";

        // disabled
        $arrToJson['project_dashboard_widgets']['value']['visits']['amount'] = 0;
        $arrToJson['project_dashboard_widgets']['value']['visits']['labels'] = ['Disabled','Disabled','Disabled','Disabled'];
        $arrToJson['project_dashboard_widgets']['value']['visits']['series'][0]['data'] = [0,0,0,0];
        $arrToJson['project_dashboard_widgets']['value']['visits']['series'][0]['name'] = "Disabled";

        // issues overview
        $arrToJson['project_dashboard_widgets']['value']['visitors']['ranges']['this-year'] = "This Year";
        $arrToJson['project_dashboard_widgets']['value']['visitors']['ranges']['last-year'] = "Last Year";
        $arrToJson['project_dashboard_widgets']['value']['visitors']['series']['this-year'][0]['name'] = "Fails";
        $arrToJson['project_dashboard_widgets']['value']['visitors']['series']['last-year'][0]['name'] = "Fails";

        $eventData['this-year']['x'][] =  '2022-12-19' . 'T09:21:32.663Z';
        $eventData['this-year']['x'][] =  '2022-12-20' . 'T09:21:32.663Z';

        $eventData['this-year']['y'][] =  1;
        $eventData['this-year']['y'][] =  0;

        for($i=0; $i<sizeof($eventData['this-year']['x']); $i++){
            $arrToJson['project_dashboard_widgets']['value']['visitors']['series']['this-year'][0]['data'][$i]['x'] = $eventData['this-year']['x'][$i];
            $arrToJson['project_dashboard_widgets']['value']['visitors']['series']['this-year'][0]['data'][$i]['y'] = $eventData['this-year']['y'][$i];
        }
        
        $eventData['last-year']['x'][] =  '2021-12-31' . 'T09:21:32.663Z';
        $eventData['last-year']['y'][] =  0;

        for($i=0; $i<sizeof($eventData['last-year']['x']); $i++){
            $arrToJson['project_dashboard_widgets']['value']['visitors']['series']['last-year'][0]['data'][$i]['x'] = $eventData['last-year']['x'][$i];
            $arrToJson['project_dashboard_widgets']['value']['visitors']['series']['last-year'][0]['data'][$i]['y'] = $eventData['last-year']['y'][$i];
        }
        



    echo json_encode($arrToJson);


    }  else {
        http_response_code(404);
        echo json_encode(
            array("message" => "No record found.")
        );
    }

    // $strToJson = { 
    //     "project_dashboard_widgets": {
    //       "value": {
    //         "summary": {
    //           "ranges": {
    //             "DY": "Yesterday",
    //             "DT": "Today"
    //           },
    //           "currentRange": "DT",
    //           "data": {
    //             "name": "Alerts",
    //             "count": {
    //               "DY": 18,
    //               "DT": 14
    //             },
    //             "extra": {
    //               "name": "Total",
    //               "count": {
    //                 "DY": 6,
    //                 "DT": 7
    //               }
    //             }
    //           },
    //           "detail": "You can show some detailed information about this widget in here."
    //         },
    //         "overdue": {
    //           "title": "Cameras OFF",
    //           "data": {
    //             "name": "Cameras",
    //             "count": 3,
    //             "extra": {
    //               "name": "Total",
    //               "count": 24
    //             }
    //           },
    //           "detail": "You can show some detailed information about this widget in here."
    //         },
    //         "issues": {
    //           "title": "Issues",
    //           "data": {
    //             "name": "Open",
    //             "count": 2,
    //             "extra": {
    //               "name": "Closed today",
    //               "count": 0
    //             }
    //           },
    //           "detail": "You can show some detailed information about this widget in here."
    //         },
    //         "features": {
    //           "title": "Total Groups",
    //           "data": {
    //             "name": "Actives",
    //             "count": 4,
    //             "extra": {
    //               "name": "Implemented",
    //               "count": 4
    //             }
    //           },
    //           "detail": "You can show some detailed information about this widget in here."
    //         },
    //         "githubIssues": {
    //           "overview": {
    //             "this-week": {
    //               "new-issues": 16,
    //               "closed-issues": 5,
    //               "fixed": 3,
    //               "wont-fix": 4,
    //               "re-opened": 8,
    //               "needs-triage": 6
    //             },
    //             "last-week": {
    //               "new-issues": 6,
    //               "closed-issues": 5,
    //               "fixed": 6,
    //               "wont-fix": 11,
    //               "re-opened": 6,
    //               "needs-triage": 5
    //             }
    //           },
    //           "ranges": {
    //             "this-week": "This Week",
    //             "last-week": "Last Week"
    //           },
    //           "labels": [
    //             "Fri",
    //             "Sat",
    //             "Sun",
    //             "Mon",
    //             "Tue",
    //             "Wed",
    //             "Thu"
    //           ],
    //           "series": {
    //             "this-week": [
    //               {
    //                 "name": "New issues",
    //                 "type": "line",
    //                 "data": [
    //                   42,
    //                   28,
    //                   43,
    //                   34,
    //                   20,
    //                   25,
    //                   22
    //                 ]
    //               },
    //               {
    //                 "name": "Cameras OFF",
    //                 "type": "column",
    //                 "data": [
    //                   11,
    //                   10,
    //                   8,
    //                   11,
    //                   8,
    //                   10,
    //                   5
    //                 ]
    //               }
    //             ],
    //             "last-week": [
    //               {
    //                 "name": "New issues",
    //                 "type": "line",
    //                 "data": [
    //                   37,
    //                   32,
    //                   39,
    //                   27,
    //                   18,
    //                   24,
    //                   20
    //                 ]
    //               },
    //               {
    //                 "name": "Cameras OFF",
    //                 "type": "column",
    //                 "data": [
    //                   9,
    //                   8,
    //                   10,
    //                   12,
    //                   7,
    //                   11,
    //                   15
    //                 ]
    //               }
    //             ]
    //           }
    //         }
    //       }
    //     }
    //   };


?>

