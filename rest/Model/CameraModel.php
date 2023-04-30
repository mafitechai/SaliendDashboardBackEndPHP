<?php
require_once PROJECT_ROOT_PATH . "/Model/Database.php";
class CameraModel extends Database
{
    public function getCameras($limit)
    {
        return $this->select("SELECT * FROM slnt_cameras ORDER BY camId ASC LIMIT ?", ["i", $limit]);
    }
    
    public function getCamerasByServerId($serverId)
    {
        if($serverId != ''){
            return $this->select("SELECT * FROM slnt_cameras WHERE serverId ='" . $serverId . "' ORDER BY camId");
        } else {
            return $this->select("SELECT * FROM slnt_cameras ORDER BY camId");
        }
    }
}

?>