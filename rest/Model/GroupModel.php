<?php
require_once PROJECT_ROOT_PATH . "/Model/Database.php";
class GroupModel extends Database
{
    public function getGroups($limit)
    {
        return $this->select("SELECT * FROM slnt_groups ORDER BY groupId ASC LIMIT ?", ["i", $limit]);
    }

    public function addGroup($dataToSave){
        return $this->insert("INSERT INTO slnt_groups VALUES (null, '" . $dataToSave['groupName'] . "', '" . $dataToSave['groupEmail'] . "', '" . $dataToSave['groupServer'] . "')");
    }
}

?>