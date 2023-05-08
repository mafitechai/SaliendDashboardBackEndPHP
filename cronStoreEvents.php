<?php
// date_default_timezone_set('America/New_York');
include_once 'config/database.php';
include_once 'class/cameras.php';
include_once 'class/servers.php';
include_once 'class/events.php';

$database = new Database();
$db = $database->getConnection();
$cameras = new Cameras($db);
$servers = new Servers($db);
$events = new Events($db);
$cameraFailed = [];
$countEvents = 0;

$stmt = $servers->getServers();

$count = 0;
while ($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
    $serverData[$count]['serverId'] = $row[0];
    $serverData[$count]['serverName'] = $row[1];
    $serverData[$count]['serverIp'] = $row[2];
    $serverData[$count]['serverPort'] = $row[3];
    $serverData[$count]['serverCreatedDate'] = $row[4];
    $serverData[$count]['serverModifiedDate'] = $row[5];
    $serverData[$count]['serverModifyBy'] = $row[6];
    $serverData[$count]['serverCreatedBy'] = $row[7];
    $count++;
}

// print_r($serverData);

if (sizeof($serverData) > 0) {
    foreach ($serverData as $serverKey => $serverValue) {
        // print_r($serverValue);
        $cameras->url =
            'http://' .
            // $serverData[$serverKey]['serverIp'] .
            $serverValue['serverIp'] .
            ':' .
            // $serverData[$serverKey]['serverPort'] .
            $serverValue['serverPort'] .
            '/cameras?accept=application/json';
        $cameraData = $cameras->cronReadCameras();
        $response = json_decode($cameraData, true);
        $tempCameras = $response['servers'];

        foreach ($tempCameras as $key => $value) {
            foreach ($value['cameras'] as $key2 => $value2) {
                switch ($value['cameras'][$key2]['state']) {
                    case 'failed':
                        $cameraFailed[$countEvents]['serverId'] =
                            $serverValue['serverId'];
                        $cameraFailed[$countEvents]['id'] =
                            $value['cameras'][$key2]['id'];
                        $cameraFailed[$countEvents]['server'] = $value['name'];
                        $cameraFailed[$countEvents]['name'] =
                            $value['cameras'][$key2]['name'];
                        $cameraFailed[$countEvents]['event'] = 'failed';
                        $cameraFailed[$countEvents]['date'] = date(
                            'Y-m-d H:i:s'
                        );
                        $countEvents++;
                        break;
                    case 'disabled':
                        $cameraFailed[$countEvents]['id'] =
                            $value['cameras'][$key2]['id'];
                        $cameraFailed[$countEvents]['server'] = $value['name'];
                        $cameraFailed[$countEvents]['name'] =
                            $value['cameras'][$key2]['name'];
                        $cameraFailed[$countEvents]['event'] = 'disabled';
                        $cameraFailed[$countEvents]['date'] = date(
                            'Y-m-d H:i:s'
                        );
                        $countEvents++;
                        break;

                    default:
                        break;
                }
            }
        }
    }
}
print_r($cameraFailed);
foreach ($cameraFailed as $key => $value) {
    $response = $events->storeEvents($value);
}
?>
