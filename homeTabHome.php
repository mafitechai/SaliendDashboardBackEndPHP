<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; text/plain; */*");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    include_once('config/database.php');
    include_once('class/events.php');

    $database = new Database();
    $db = $database->getConnection();
    $events = new Events($db);
    
    // Alerts
    $alertsToday = $events->countEvents('today');
    $alertsYesterday = $events->countEvents('yesterday');
    $alertsTotalMonth = $events->countEvents('month');
    
    // Cameras OFF
    // $camerasOFFMonth = $events->countEvents('month', 'off');
    $camerasOFFMonth = 1;
    // $camerasOFFToday = $events->countEvents('today', 'off');
    $camerasOFFToday = 1;

    // Groups
    $groupsTotal = $events->getTotalGroups();

    // Failures
    $failuresToday = $events->countEvents('today', 'failed');
    $failuresMonth = $events->countEvents('month', 'failed');
    $failuresMonth = 1;

    // New Issues
    $newIssuesThisWeek = $events->countEvents('thisWeek');
    $totalOffEventsThisWeek = $events->countEvents('thisWeek','off');
    $issuesFixedThisWeek = $events->countIssuesFixed('thisWeek');
    $camerasOFFThisWeek = $events->countEvents('thisWeek', 'off');
    $pendingFixThisWeek = $events->countPendingToFix('thisWeek');
    $recurrentIssuesThisWeek = $events->countRecurrentEvents('thisWeek');
    $needAttentionThisWeek = $events->countPendingToFix('needAttention');

    $newIssuesLastWeek = $events->countEvents('lastWeek');
    $totalOffEventsLastWeek = $events->countEvents('lastWeek','off');
    $issuesFixedThisWeek = $events->countIssuesFixed('lastWeek');
    $camerasOFFLastWeek = $events->countEvents('lastWeek', 'off');
    $pendingFixLastWeek = $events->countPendingToFix('lastWeek');
    $recurrentIssuesLastWeek = $events->countRecurrentEvents('lastWeek');
    $needAttentionLastWeek = $events->countPendingToFix('needAttention');

    // Equipments Issues Summary
    $labelsEquipments = $events->getEquipmentLabels(); // array week days
    $newIssuesArrThisWeek = $events->countNewIssuesArr('thisWeek'); // array
    $newIssuesArrLastWeek = $events->countNewIssuesArr('lastWeek'); // array
    $camerasOFFArrThisWeek = $events->countCamerasOFFArr('thisWeek'); // array
    $camerasOFFArrLastWeek = $events->countCamerasOFFArr('lastWeek'); // array
    



    if(true){
    $arrToJson['project_dashboard_widgets']['value']['summary']['ranges']['DY'] = "Yesterday";
    $arrToJson['project_dashboard_widgets']['value']['summary']['ranges']['DT'] = "Today";
    $arrToJson['project_dashboard_widgets']['value']['summary']['currentRange'] = "DT";
    $arrToJson['project_dashboard_widgets']['value']['summary']['data']['name'] = "Alerts";
    $arrToJson['project_dashboard_widgets']['value']['summary']['data']['count']['DY'] = $alertsYesterday; //alerts yesterday
    $arrToJson['project_dashboard_widgets']['value']['summary']['data']['count']['DT'] = $alertsToday; // alerts today
    $arrToJson['project_dashboard_widgets']['value']['summary']['data']['extra']['name'] = "Total Month";
    $arrToJson['project_dashboard_widgets']['value']['summary']['data']['extra']['count']['DY'] = $alertsTotalMonth; //total cameras off yesterday
    $arrToJson['project_dashboard_widgets']['value']['summary']['data']['extra']['count']['DT'] = $alertsTotalMonth; //total cameras off today
    $arrToJson['project_dashboard_widgets']['value']['summary']['detail'] = "You can show some detailed information about this widget in here.";
    
    $arrToJson['project_dashboard_widgets']['value']['overdue']['title'] = "Cameras OFF";
    $arrToJson['project_dashboard_widgets']['value']['overdue']['data']['name'] = "Cameras";
    $arrToJson['project_dashboard_widgets']['value']['overdue']['data']['count'] = $camerasOFFToday; //cameras off today
    $arrToJson['project_dashboard_widgets']['value']['overdue']['data']['extra']['name'] = "Total Month";
    $arrToJson['project_dashboard_widgets']['value']['overdue']['data']['extra']['count'] = $camerasOFFMonth; //total events in this month
    $arrToJson['project_dashboard_widgets']['value']['overdue']['detail'] = "You can show some detailed information about this widget in here.";

    $arrToJson['project_dashboard_widgets']['value']['features']['title'] = "Total Groups";
    $arrToJson['project_dashboard_widgets']['value']['features']['data']['name'] = "Actives";
    $arrToJson['project_dashboard_widgets']['value']['features']['data']['count'] = $groupsTotal;
    $arrToJson['project_dashboard_widgets']['value']['features']['data']['extra']['name'] = "Implemented";
    $arrToJson['project_dashboard_widgets']['value']['features']['data']['extra']['count'] = $groupsTotal;
    $arrToJson['project_dashboard_widgets']['value']['features']['detail'] = "You can show some detailed information about this widget in here.";
    
    $arrToJson['project_dashboard_widgets']['value']['issues']['title'] = "Failures";
    $arrToJson['project_dashboard_widgets']['value']['issues']['data']['count'] = $failuresToday;
    $arrToJson['project_dashboard_widgets']['value']['issues']['data']['name'] = "Today";
    $arrToJson['project_dashboard_widgets']['value']['issues']['data']['extra']['name'] = "Total Month";
    $arrToJson['project_dashboard_widgets']['value']['issues']['data']['extra']['count'] = $failuresMonth;

    $arrToJson['project_dashboard_widgets']['value']['githubIssues']['overview']['this-week']['new-issues'] = $newIssuesThisWeek; // New Issues
    $arrToJson['project_dashboard_widgets']['value']['githubIssues']['overview']['this-week']['closed-issues'] = $totalOffEventsThisWeek; //total off events
    $arrToJson['project_dashboard_widgets']['value']['githubIssues']['overview']['this-week']['fixed'] = $issuesFixedThisWeek;
    $arrToJson['project_dashboard_widgets']['value']['githubIssues']['overview']['this-week']['wont-fix'] = $pendingFixThisWeek; // fix pending
    $arrToJson['project_dashboard_widgets']['value']['githubIssues']['overview']['this-week']['re-opened'] = $recurrentIssuesThisWeek; // Reoccur
    $arrToJson['project_dashboard_widgets']['value']['githubIssues']['overview']['this-week']['needs-triage'] = $needAttentionThisWeek; // needs attention
    $arrToJson['project_dashboard_widgets']['value']['githubIssues']['overview']['last-week']['new-issues'] = $newIssuesLastWeek; // New Issues
    $arrToJson['project_dashboard_widgets']['value']['githubIssues']['overview']['last-week']['closed-issues'] = $totalOffEventsLastWeek; //total off events
    $arrToJson['project_dashboard_widgets']['value']['githubIssues']['overview']['last-week']['fixed'] = $issuesFixedThisWeek;
    $arrToJson['project_dashboard_widgets']['value']['githubIssues']['overview']['last-week']['wont-fix'] = $pendingFixLastWeek; // fix pending
    $arrToJson['project_dashboard_widgets']['value']['githubIssues']['overview']['last-week']['re-opened'] = $recurrentIssuesLastWeek; // Reoccur
    $arrToJson['project_dashboard_widgets']['value']['githubIssues']['overview']['last-week']['needs-triage'] = $needAttentionLastWeek; // needs attention

    $arrToJson['project_dashboard_widgets']['value']['githubIssues']['ranges']['this-week'] = "This Week";
    $arrToJson['project_dashboard_widgets']['value']['githubIssues']['ranges']['last-week'] = "Last Week";
    $arrToJson['project_dashboard_widgets']['value']['githubIssues']['labels'] = $labelsEquipments;

    $arrToJson['project_dashboard_widgets']['value']['githubIssues']['series']['this-week'][0]['data'] = $newIssuesArrThisWeek;
    $arrToJson['project_dashboard_widgets']['value']['githubIssues']['series']['this-week'][0]['name'] = "New issues";
    $arrToJson['project_dashboard_widgets']['value']['githubIssues']['series']['this-week'][0]['type'] = "line";
    $arrToJson['project_dashboard_widgets']['value']['githubIssues']['series']['this-week'][1]['data'] = $camerasOFFArrThisWeek;
    $arrToJson['project_dashboard_widgets']['value']['githubIssues']['series']['this-week'][1]['name'] = "Cameras OFF";
    $arrToJson['project_dashboard_widgets']['value']['githubIssues']['series']['this-week'][1]['type'] = "column";

    $arrToJson['project_dashboard_widgets']['value']['githubIssues']['series']['last-week'][0]['data'] = $newIssuesArrLastWeek;
    $arrToJson['project_dashboard_widgets']['value']['githubIssues']['series']['last-week'][0]['name'] = "New issues";
    $arrToJson['project_dashboard_widgets']['value']['githubIssues']['series']['last-week'][0]['type'] = "line";
    $arrToJson['project_dashboard_widgets']['value']['githubIssues']['series']['last-week'][1]['data'] = $camerasOFFArrLastWeek;
    $arrToJson['project_dashboard_widgets']['value']['githubIssues']['series']['last-week'][1]['name'] = "Cameras OFF";
    $arrToJson['project_dashboard_widgets']['value']['githubIssues']['series']['last-week'][1]['type'] = "column";
    

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

