<?php
class UserController extends BaseController
{
    /** 
* "/user/list" Endpoint - Get list of users 
*/
    public function listUserAction()
    {
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];
        $arrQueryStringParams = $this->getQueryStringParams();
        if (strtoupper($requestMethod) == 'GET') {
            try {
                $userModel = new UserModel();
                $intLimit = 10;
                if (isset($arrQueryStringParams['limit']) && $arrQueryStringParams['limit']) {
                    $intLimit = $arrQueryStringParams['limit'];
                }
                $arrUsers = $userModel->getUsers($intLimit);
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

    public function loginAction(){
        $data = json_decode(file_get_contents('php://input'),true);
        $email = $data['email'];
        $passwd = $data['password'];
        $strErrorDesc = '';
        $requestMethod = $_SERVER["REQUEST_METHOD"];

        if (strtoupper($requestMethod) == 'POST') {
            try {
                $userModel = new UserModel();
                $intLimit = 10;
                $dataToSave = array('email'=> $email, 'password'=> $passwd);
                $arrUsers = $userModel->loginUser($dataToSave);
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

    public function addUserAction(){
        $data = json_decode(file_get_contents('php://input'),true);
        $strErrorDesc = '';
        //print_r($data);
        $userName = $data['userNameVal'];
        $userEmail = $data['userEmailVal'];
        $userPasswd = $data['userPasswdVal'];
        $userGroups = $data['userGroupsVal'];
        $userAccessToken = bin2hex(random_bytes(10));
        $isAdmin = $data['userAdmin'];

        $dataToSave = array('userName'=> $userName, 'accessToken'=> $userAccessToken, 'userEmail'=> $userEmail, 'userPasswd'=> $userPasswd, 'userGroups'=>$userGroups, 'isAdmin'=>$isAdmin);
        $userModel = new userModel();
        $resultAdd = $userModel->addUser($dataToSave);
        if (!$strErrorDesc) {
            $this->sendOutput(
                $resultAdd,
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