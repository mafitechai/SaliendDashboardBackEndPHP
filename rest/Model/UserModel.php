<?php
require_once PROJECT_ROOT_PATH . "/Model/Database.php";
class UserModel extends Database
{
    public function getUsers($limit)
    {
        return $this->select("SELECT * FROM slnt_users ORDER BY id ASC LIMIT ?", ["i", $limit]);
    }

    public function addUser($dataToSave){
        return $this->insert("INSERT INTO slnt_users VALUES (null, '" . $dataToSave['accessToken'] . "', '" . $dataToSave['accessToken'] . "', '" . 
                                $dataToSave['userPasswd'] . "', '" . $dataToSave['isAdmin'] . "', '" . $dataToSave['userName'] . "', 
                                'assets/images/avatars/servers_avatar.jpg', '" . $dataToSave['userEmail'] . "', 
                                '" . $dataToSave['userGroups'] . "')");
    }

    public function loginUser($dataUser)
    {
        return $this->select("SELECT accessToken, refreshToken FROM slnt_users WHERE email='" . $dataUser['email'] . "' AND `password`='" . $dataUser['password'] . "'");
    }
}

?>