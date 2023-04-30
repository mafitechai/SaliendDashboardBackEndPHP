<?php
require_once PROJECT_ROOT_PATH . "/Model/Database.php";
class ServerModel extends Database
{
    public function getServers($limit)
    {
        return $this->select("SELECT * FROM slnt_servers ORDER BY serverId  ASC LIMIT ?", ["i", $limit]);
    }

    public function getServersNameById($id)
    {
        return $this->select("SELECT serverName FROM slnt_servers WHERE serverId = '" . $id . "' ORDER BY serverId");
    }

}

?>