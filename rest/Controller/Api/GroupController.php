<?php

class GroupController extends BaseController
{
    /** 
* "/user/list" Endpoint - Get list of users 
*/
    public function listGroupAction()
    {
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $arrQueryStringParams = $this->getQueryStringParams();
        if (strtoupper($requestMethod) == 'GET') {
            try {
                $groupModel = new GroupModel();
                $intLimit = 10;
                if (isset($arrQueryStringParams['limit']) && $arrQueryStringParams['limit']) {
                    $intLimit = $arrQueryStringParams['limit'];
                }
                $arrUsers = $groupModel->getGroups($intLimit);
                $responseData = json_encode($arrUsers);
            } catch (Error $e) {
                $strErrorDesc = $e->getMessage().'Something went wrong! Please contact support.';
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }
        } else {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }
        // send output 
        if (!$strErrorDesc) {
            $this->sendOutput(
                $responseData,
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        } else {
            $this->sendOutput(json_encode(array('error' => $strErrorDesc)), 
                array('Content-Type: application/json', $strErrorHeader)
            );
        }
    }

    public function addGroupAction(){
        $data = json_decode(file_get_contents('php://input'),true);
        //print_r($data);
        $groupName = $data['groupNameVal'];
        $groupEmail = $data['groupEmailVal'];
        $groupServers = substr($data['groupServersVal'], 0, -1);

        $dataToSave = array('groupName'=> $groupName, 'groupEmail'=> $groupEmail, 'groupServer'=>$groupServers);
        $groupModel = new GroupModel();
        $resultAdd = $groupModel->addGroup($dataToSave);
    }

    public function groupCamAction(){
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $arrQueryStringParams = $this->getQueryStringParams();
        if (strtoupper($requestMethod) == 'GET') {
            try {
                $groupModel = new GroupModel();
                $cameraModel  = new CameraModel();
                $serverModel = new ServerModel();
                $intLimit = 10;
                if (isset($arrQueryStringParams['limit']) && $arrQueryStringParams['limit']) {
                    $intLimit = $arrQueryStringParams['limit'];
                }
                $arrGroups = $groupModel->getGroups($intLimit);
                for($i=0; $i<sizeof($arrGroups); $i++){
                    $camerasByServer = [];
                    $camInId = [];
                    $serverName = []; 
                    $camName = [];
                    $data['groups'][$i]['name'] = $arrGroups[$i]['groupName'];
                    $data['groups'][$i]['email'][] = $arrGroups[$i]['groupEmail'];

                    if($arrGroups[$i]['groupServers'] == ''){
                        $camerasByServer[] = $cameraModel->getCamerasByServerId('');
                    } else {
                        $arrServers = explode(',',$arrGroups[$i]['groupServers']);
                        for($k=0; $k<sizeof($arrServers); $k++){
                            $camerasByServer[] = $cameraModel->getCamerasByServerId($arrServers[$k]);
                        }
                    }

                    for($j=0; $j<sizeof($camerasByServer); $j++){
                        for($z=0; $z<sizeof($camerasByServer[$j]); $z++){
                            $camInId[] = $camerasByServer[$j][$z]['camIntId'];
                            $serverNameRes = $serverModel->getServersNameById($camerasByServer[$j][$z]['serverId']);
                            $serverName[] = $serverNameRes[0]['serverName'];
                            $camName[] = $camerasByServer[$j][$z]['camName'];
                        }
                    }

                    $data['groups'][$i]['camIntId'] = $camInId;
                    $data['groups'][$i]['serverName'] = $serverName;
                    $data['groups'][$i]['camName'] = $camName;                  
                }
                $responseData = json_encode($data);

            } catch (Error $e) {
                $strErrorDesc = $e->getMessage().'Something went wrong! Please contact support.';
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }

        } else {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }
        // send output 
        if (!$strErrorDesc) {
            $this->sendOutput(
                $responseData,
                array('Content-Type: application/json', 'HTTP/1.1 200 OK')
            );
        } else {
            $this->sendOutput(json_encode(array('error' => $strErrorDesc)), 
                array('Content-Type: application/json', $strErrorHeader)
            );
        }
    }
}
?>