<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; text/plain; */*");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    require_once 'config/jwt_utils.php';
    include_once('config/database.php');
    include_once('class/cameras.php');
    include_once('class/servers.php');

    $database = new Database();
    $db = $database->getConnection();
    $cameras = new Cameras($db);
    $servers = new Servers($db);
    $addCam = false;

    $groups = $_GET['groups'];
    $serversData = $servers->getServers2($groups);
    $failed = 0;
    $disable = 0;
    $camTotal = 0;

    if(sizeof($serversData) > 0){
        for($i=0; $i < sizeof($serversData); $i++){
            $cameras->url = 'http://' . $serversData[$i]['serverIp'] . ':' . $serversData[$i]['serverPort'] . '/cameras?accept=application/json';
            $cameraData = $cameras->cronReadCameras();
            $response = json_decode($cameraData, true);
            $tempCameras = $response['servers'];

            for($j=0; $j < sizeof($tempCameras); $j++){
                for($k=0; $k < sizeof($tempCameras[$j]['cameras']); $k++){
                    $camTotal++;
                    $cameras->serverId = $serversData[$i]['serverId'];
                    $dataCameras['rows'][$i]['cameras'][$k]['id'] = $tempCameras[$j]['cameras'][$k]['id'];
                    $cameras->camIntId = $tempCameras[$j]['cameras'][$k]['id'];
                    $dataCameras['rows'][$i]['cameras'][$k]['server'] = $tempCameras[$j]['name'];
                    $dataCameras['rows'][$i]['cameras'][$k]['name'] = $tempCameras[$j]['cameras'][$k]['name'];
                    $cameras->camName = $tempCameras[$j]['cameras'][$k]['name'];
                    $dataCameras['rows'][$i]['cameras'][$k]['status'] = $tempCameras[$j]['cameras'][$k]['state'];
                    if($tempCameras[$j]['cameras'][$k]['state'] == 'failed'){
                        $camFailed[$failed]['id'] = $tempCameras[$j]['cameras'][$k]['id'];
                        $camFailed[$failed]['server'] = $tempCameras[$j]['name'];
                        $camFailed[$failed]['name'] = $tempCameras[$j]['cameras'][$k]['name'];
                        $camFailed[$failed]['status'] = 'failed';
                        $camFailed[$failed]['ipStreamType'] = $tempCameras[$j]['cameras'][$k]['ipStreamType'];
                        $camFailed[$failed]['audio'] = $tempCameras[$j]['cameras'][$k]['audioEnabled'] == '' ? 'false' : 'true';
                        $camFailed[$failed]['light'] = $tempCameras[$j]['cameras'][$k]['lightAvailable'] == '' ? 'false' : 'true';
                        $failed = $failed + 1;
                    } else if ($tempCameras[$j]['cameras'][$k]['state'] == 'disabled'){
                        $camFailed[$failed]['id'] = $tempCameras[$j]['cameras'][$k]['id'];
                        $camFailed[$failed]['server'] = $tempCameras[$j]['name'];
                        $camFailed[$failed]['name'] = $tempCameras[$j]['cameras'][$k]['name'];
                        $camFailed[$failed]['status'] = 'failed';
                        $camFailed[$failed]['ipStreamType'] = $tempCameras[$j]['cameras'][$k]['ipStreamType'];
                        $camFailed[$failed]['audio'] = $tempCameras[$j]['cameras'][$k]['audioEnabled'] == '' ? 'false' : 'true';
                        $camFailed[$failed]['light'] = $tempCameras[$j]['cameras'][$k]['lightAvailable'] == '' ? 'false' : 'true';

                        $camDisabled[$disable]['id'] = $tempCameras[$j]['cameras'][$k]['id'];
                        $camDisabled[$disable]['server'] = $tempCameras[$j]['name'];
                        $camDisabled[$disable]['name'] = $tempCameras[$j]['cameras'][$k]['name'];
                        $camDisabled[$disable]['status'] = 'failed';
                        $camDisabled[$disable]['ipStreamType'] = $tempCameras[$j]['cameras'][$k]['ipStreamType'];
                        $camDisabled[$disable]['audio'] = $tempCameras[$j]['cameras'][$k]['audioEnabled'] == '' ? 'false' : 'true';
                        $camDisabled[$disable]['light'] = $tempCameras[$j]['cameras'][$k]['lightAvailable'] == '' ? 'false' : 'true';
                        $failed =  $failed + 1;
                        $disable =  $disable +1;
                    } 
                    $dataCameras['rows'][$i]['cameras'][$k]['ipStreamType'] = $tempCameras[$j]['cameras'][$k]['ipStreamType'];
                    $dataCameras['rows'][$i]['cameras'][$k]['audio'] = $tempCameras[$j]['cameras'][$k]['audioEnabled'] == '' ? 'false' : 'true';
                    $dataCameras['rows'][$i]['cameras'][$k]['light'] = $tempCameras[$j]['cameras'][$k]['lightAvailable'] == '' ? 'false' : 'true';

                    $addCam = $cameras->isCamExist();
                    if(!$addCam)
                        $cameras->addCamera();
                }
            }
        }
        $dataCameras['columns'] = [
                "ID",
                "Server",
                "Name",
                "State",
                "Audio Enable",
                "Light Enable",
                "IPStream Type"
        ];
        $dataCameras['failed'] = $failed;
        $dataCameras['disabled'] = $disable;
        $dataCameras['camFailed'] = $camFailed;
        $dataCameras['camDisabled'] = $camDisabled;
        $dataCameras['camTotal'] = $camTotal;
        echo json_encode( $dataCameras);
    }  else {
        http_response_code(404);
        echo json_encode(
            array("message" => "No record found.")
        );
    }
?>