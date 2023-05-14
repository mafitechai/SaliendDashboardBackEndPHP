<?php

class Events
{
    private $conn;
    //Table
    private $db_table = 'slnt_events';
    //Columns
    public $id;
    public $camera;
    public $server;
    public $event;
    public $date;
    private $previousWeek;
    private $startWeekP;
    private $endWeekP;
    private $thisWeek;
    private $startWeekC;
    private $arrIssuesC = [];
    private $arrIssuesL = [];
    private $arrOffC = [];
    private $arrOffL = [];

    //db connection
    public function __construct($db)
    {
        $this->conn = $db;

        $this->previousWeek = strtotime('-1 week +1 day');
        $this->startWeekP = strtotime(
            'last monday midnight',
            $this->previousWeek
        );
        $this->endWeekP = strtotime('next sunday', $this->startWeekP);
        $this->startWeekP = date('Y-m-d', $this->startWeekP);
        $this->endWeekP = date('Y-m-d', $this->endWeekP);

        $this->thisWeek = strtotime('today');
        $this->startWeekC = strtotime('last monday midnight', $this->thisWeek);
        $this->startWeekC = date('Y-m-d', $this->startWeekC);
    }

    //Get today events
    public function countEvents($range, $event = false, $customDate = false)
    {
        switch ($range) {
            case 'today':
                $today = date('Y-m-d');
                $whereDate = "event_date = '" . $today . "'";
                break;
            case 'yesterday':
                $yesterday = date('Y-m-d', strtotime('-1 days'));
                $whereDate = "event_date = '" . $yesterday . "'";
                break;
            case 'month':
                $today = date('Y-m-d');
                $month = date('m');
                $year = date('Y');
                $whereDate =
                    "event_date BETWEEN '" .
                    $year .
                    '-' .
                    $month .
                    "-01' AND '" .
                    $today .
                    "'";
                break;
            case 'lastWeek':
                $whereDate =
                    "event_date BETWEEN '" .
                    $this->startWeekP .
                    "' AND '" .
                    $this->endWeekP .
                    "'";
                break;
            case 'thisWeek':
                $today = date('Y-m-d');
                $whereDate =
                    "event_date BETWEEN '" .
                    $this->startWeekC .
                    "' AND '" .
                    $today .
                    "'";
                break;
            case 'custom':
                $whereDate = "event_date = '" . $customDate . "'";
                break;
            default:
                $today = date('Y-m-d');
                $whereDate = "event_date = '" . $today . "'";
                break;
        }

        if ($event) {
            switch ($event) {
                case 'failed':
                    $whereDate .= " AND  event_event = 'failed'";
                    break;
                case 'off':
                    $whereDate .= " AND  event_event = 'off'";
                    break;
                default:
                    $whereDate .= '';
                    break;
            }
        }

        $query =
            "SELECT COUNT('event_id') FROM " .
            $this->db_table .
            ' WHERE ' .
            $whereDate;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
            $count = $row[0];
        }
        return $count;
    }

    public function countPendingToFix($range)
    {
        return 0;
    }

    public function countRecurrentEvents($range)
    {
        $today = date('Y-m-d');
        $arrCount = 1;

        if ($range == 'thisWeek') {
            $query =
                'SELECT event_camera FROM ' .
                $this->db_table .
                " WHERE event_date BETWEEN '" .
                $this->startWeekC .
                "' AND '" .
                $today .
                "' GROUP BY 'event_camera' HAVING COUNT(event_camera) > 1";
        } elseif ($range == 'lastWeek') {
            $query =
                'SELECT event_camera, COUNT(event_camera) FROM ' .
                $this->db_table .
                " WHERE event_date BETWEEN '" .
                $this->startWeekP .
                "' AND '" .
                $this->endWeekP .
                "' GROUP BY 'event_camera' HAVING COUNT(event_camera) > 1";
        }

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
            $arrCount++;
        }

        return $arrCount;
    }

    public function getEquipmentLabels()
    {
        $dayLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        return $dayLabels;
    }

    public function countNewIssuesArr($range, $beginDayP = false)
    {
        if ($range == 'thisWeek') {
            $today = date('Y-m-d');
            $beginDay = $beginDayP === false ? $this->startWeekC : $beginDayP;
            if ($beginDay <= $today) {
                $numEvents = $this->countEvents('custom', false, $beginDay);
                array_push($this->arrIssuesC, $numEvents);
                $beginDay = date('Y-m-d', strtotime($beginDay . '+1 days'));
                $this->countNewIssuesArr($range, $beginDay);
            }
        } elseif ($range == 'lastWeek') {
            $beginDay = $beginDayP === false ? $this->startWeekP : $beginDayP;
            $lastDay = $this->endWeekP;
            if ($beginDay <= $lastDay) {
                $numEvents = $this->countEvents('custom', false, $beginDay);
                array_push($this->arrIssuesL, $numEvents);
                $beginDay = date('Y-m-d', strtotime($beginDay . '+1 days'));
                $this->countNewIssuesArr($range, $beginDay);
            }
        }
        return $finalArray =
            $range == 'thisWeek' ? $this->arrIssuesC : $this->arrIssuesL;
    }

    public function countCamerasOFFArr($range, $beginDayP = false)
    {
        if ($range == 'thisWeek') {
            $today = date('Y-m-d');
            $beginDay = $beginDayP === false ? $this->startWeekC : $beginDayP;
            if ($beginDay <= $today) {
                $numEvents = $this->countEvents('custom', 'off', $beginDay);
                array_push($this->arrOffC, $numEvents);
                $beginDay = date('Y-m-d', strtotime($beginDay . '+1 days'));
                $this->countCamerasOFFArr($range, $beginDay);
            }
        } elseif ($range == 'lastWeek') {
            $beginDay = $beginDayP === false ? $this->startWeekP : $beginDayP;
            $lastDay = $this->endWeekP;
            if ($beginDay <= $lastDay) {
                $numEvents = $this->countEvents('custom', 'off', $beginDay);
                array_push($this->arrOffL, $numEvents);
                $beginDay = date('Y-m-d', strtotime($beginDay . '+1 days'));
                $this->countCamerasOFFArr($range, $beginDay);
            }
        }
        return $finalArray =
            $range == 'thisWeek' ? $this->arrOffC : $this->arrOffL;
    }

    public function countIssuesFixed($range)
    {
        return 0;
    }

    public function getTotalGroups()
    {
        return 0;
    }

    public function getLastMonthEvents($group)
    {
        if (intval($group) === 0) {
            $query =
                'SELECT DATE_FORMAT(' .
                $this->db_table .
                ".event_date, '%Y-%m-%d') AS 'Date' , COUNT(" .
                $this->db_table .
                ".event_date) AS 'Count' FROM " .
                $this->db_table .
                ' WHERE ' .
                $this->db_table .
                '.event_date BETWEEN (CURDATE() - INTERVAL 30 DAY) AND (CURDATE() + INTERVAL 1 DAY) AND ' .
                $this->db_table .
                '.event_event = ? GROUP BY DATE_FORMAT(' .
                $this->db_table .
                ".event_date, '%Y-%m-%d')";

            $stmt = $this->conn->prepare($query);

            $stmt->execute(['failed']);
        } else {
            $query =
                'SELECT DATE_FORMAT(' .
                $this->db_table .
                ".event_date, '%Y-%m-%d') AS 'Date' , COUNT(" .
                $this->db_table .
                ".event_date) AS 'Count' FROM " .
                $this->db_table .
                ' LEFT JOIN slnt_cameras ON ' .
                $this->db_table .
                '.event_camera_id = slnt_cameras.camIntId AND ' .
                $this->db_table .
                '.event_server_id = slnt_cameras.serverId WHERE ' .
                $this->db_table .
                '.event_date BETWEEN (CURDATE() - INTERVAL 30 DAY) AND (CURDATE() + INTERVAL 1 DAY) AND slnt_cameras.groupId = ? AND ' .
                $this->db_table .
                '.event_event = ? GROUP BY DATE_FORMAT(' .
                $this->db_table .
                ".event_date, '%Y-%m-%d')";

            $stmt = $this->conn->prepare($query);

            $stmt->execute([$group, 'failed']);
        }

        // $stmt = $this->conn->prepare($query);

        // $stmt->execute();

        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $row;
    }

    public function getDailyEvents($group, $event)
    {
        if (intval($group) === 0) {
            switch ($event) {
                case 'issues':
                    $query =
                        'SELECT DATE_FORMAT(' .
                        $this->db_table .
                        '.event_date, "%Y-%m-%d") AS "Date", COUNT(' .
                        $this->db_table .
                        '.event_date) AS "Count" FROM ' .
                        $this->db_table .
                        ' WHERE WEEKOFYEAR(' .
                        $this->db_table .
                        '.event_date) = (weekofyear(CURDATE())) GROUP BY DATE_FORMAT(' .
                        $this->db_table .
                        '.event_date, "%Y-%m-%d")';

                    $stmt = $this->conn->prepare($query);

                    $stmt->execute();
                    break;
                case 'disabled':
                    $query =
                        'SELECT DATE_FORMAT(' .
                        $this->db_table .
                        '.event_date, "%Y-%m-%d") AS "Date", COUNT(' .
                        $this->db_table .
                        '.event_date) AS "Count" FROM ' .
                        $this->db_table .
                        ' WHERE WEEKOFYEAR(' .
                        $this->db_table .
                        '.event_date) = (weekofyear(CURDATE())) AND ' .
                        $this->db_table .
                        '.event_event = ? GROUP BY DATE_FORMAT(' .
                        $this->db_table .
                        '.event_date, "%Y-%m-%d")';

                    $stmt = $this->conn->prepare($query);

                    $stmt->execute(['disabled']);
                    break;
                case 'failed':
                    $query =
                        'SELECT DATE_FORMAT(' .
                        $this->db_table .
                        '.event_date, "%Y-%m-%d") AS "Date", COUNT(' .
                        $this->db_table .
                        '.event_date) AS "Count" FROM ' .
                        $this->db_table .
                        ' WHERE WEEKOFYEAR(' .
                        $this->db_table .
                        '.event_date) = (weekofyear(CURDATE())) AND ' .
                        $this->db_table .
                        '.event_event = ? GROUP BY DATE_FORMAT(' .
                        $this->db_table .
                        '.event_date, "%Y-%m-%d")';

                    $stmt = $this->conn->prepare($query);

                    $stmt->execute(['failed']);
                    break;

                default:
                    # code...
                    break;
            }
        } else {
            switch ($event) {
                case 'issues':
                    $query =
                        'SELECT DATE_FORMAT(' .
                        $this->db_table .
                        '.event_date, "%Y-%m-%d") AS "Date", COUNT(' .
                        $this->db_table .
                        '.event_date) AS "Count" FROM ' .
                        $this->db_table .
                        ' LEFT JOIN slnt_cameras ON ' .
                        $this->db_table .
                        '.event_camera_id = slnt_cameras.camIntId AND ' .
                        $this->db_table .
                        '.event_server_id = slnt_cameras.serverId WHERE WEEKOFYEAR(' .
                        $this->db_table .
                        '.event_date) = (weekofyear(CURDATE())) and slnt_cameras.groupId = ? GROUP BY DATE_FORMAT(' .
                        $this->db_table .
                        '.event_date, "%Y-%m-%d")';

                    $stmt = $this->conn->prepare($query);

                    $stmt->execute([$group]);
                    break;
                case 'disabled':
                    $query =
                        'SELECT DATE_FORMAT(' .
                        $this->db_table .
                        '.event_date, "%Y-%m-%d") AS "Date", COUNT(' .
                        $this->db_table .
                        '.event_date) AS "Count" FROM ' .
                        $this->db_table .
                        ' LEFT JOIN slnt_cameras ON ' .
                        $this->db_table .
                        '.event_camera_id = slnt_cameras.camIntId AND ' .
                        $this->db_table .
                        '.event_server_id = slnt_cameras.serverId WHERE WEEKOFYEAR(' .
                        $this->db_table .
                        '.event_date) = (weekofyear(CURDATE())) and slnt_cameras.groupId = ? AND ' .
                        $this->db_table .
                        '.event_event = ? GROUP BY DATE_FORMAT(' .
                        $this->db_table .
                        '.event_date, "%Y-%m-%d")';

                    $stmt = $this->conn->prepare($query);

                    $stmt->execute([$group, 'disabled']);
                    break;
                case 'failed':
                    $query =
                        'SELECT DATE_FORMAT(' .
                        $this->db_table .
                        '.event_date, "%Y-%m-%d") AS "Date", COUNT(' .
                        $this->db_table .
                        '.event_date) AS "Count" FROM ' .
                        $this->db_table .
                        ' LEFT JOIN slnt_cameras ON ' .
                        $this->db_table .
                        '.event_camera_id = slnt_cameras.camIntId AND ' .
                        $this->db_table .
                        '.event_server_id = slnt_cameras.serverId WHERE WEEKOFYEAR(' .
                        $this->db_table .
                        '.event_date) = (weekofyear(CURDATE())) and slnt_cameras.groupId = ? AND ' .
                        $this->db_table .
                        '.event_event = ? GROUP BY DATE_FORMAT(' .
                        $this->db_table .
                        '.event_date, "%Y-%m-%d")';

                    $stmt = $this->conn->prepare($query);

                    $stmt->execute([$group, 'failed']);
                    break;

                default:
                    # code...
                    break;
            }
        }

        // $row = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $row;
    }

    public function getWeeklyEvents($group, $event)
    {
        if (intval($group) === 0) {
            switch ($event) {
                case 'issues':
                    $query =
                        'SELECT COUNT(' .
                        $this->db_table .
                        ".event_date) AS 'Count' FROM " .
                        $this->db_table .
                        ' WHERE WEEKOFYEAR(' .
                        $this->db_table .
                        '.event_date) = (weekofyear(CURDATE()))';

                    $stmt = $this->conn->prepare($query);

                    $stmt->execute();
                    break;
                case 'disabled':
                    $query =
                        'SELECT COUNT(' .
                        $this->db_table .
                        ".event_date) AS 'Count' FROM " .
                        $this->db_table .
                        ' WHERE WEEKOFYEAR(' .
                        $this->db_table .
                        '.event_date) = (weekofyear(CURDATE())) AND ' .
                        $this->db_table .
                        '.event_event = ?';

                    $stmt = $this->conn->prepare($query);

                    $stmt->execute(['disabled']);
                    break;
                case 'failed':
                    $query =
                        'SELECT COUNT(' .
                        $this->db_table .
                        ".event_date) AS 'Count' FROM " .
                        $this->db_table .
                        ' WHERE WEEKOFYEAR(' .
                        $this->db_table .
                        '.event_date) = (weekofyear(CURDATE())) AND ' .
                        $this->db_table .
                        '.event_event = ?';

                    $stmt = $this->conn->prepare($query);

                    $stmt->execute(['failed']);
                    break;

                default:
                    # code...
                    break;
            }
        } else {
            switch ($event) {
                case 'issues':
                    $query =
                        'SELECT COUNT(' .
                        $this->db_table .
                        '.event_date) AS Count FROM ' .
                        $this->db_table .
                        ' LEFT JOIN slnt_cameras ON ' .
                        $this->db_table .
                        '.event_camera_id = slnt_cameras.camIntId AND ' .
                        $this->db_table .
                        '.event_server_id = slnt_cameras.serverId WHERE WEEKOFYEAR(' .
                        $this->db_table .
                        '.event_date) = (weekofyear(CURDATE())) AND slnt_cameras.groupId = ?';

                    $stmt = $this->conn->prepare($query);

                    $stmt->execute([$group]);
                    break;
                case 'disabled':
                    $query =
                        'SELECT COUNT(' .
                        $this->db_table .
                        '.event_date) AS "Count" FROM ' .
                        $this->db_table .
                        ' LEFT JOIN slnt_cameras ON ' .
                        $this->db_table .
                        '.event_camera_id = slnt_cameras.camIntId AND ' .
                        $this->db_table .
                        '.event_server_id = slnt_cameras.serverId WHERE WEEKOFYEAR(' .
                        $this->db_table .
                        '.event_date) = (weekofyear(CURDATE())) AND slnt_cameras.groupId = ? AND ' .
                        $this->db_table .
                        '.event_event = ?';

                    $stmt = $this->conn->prepare($query);

                    $stmt->execute([$group, 'disabled']);
                    break;
                case 'failed':
                    $query =
                        'SELECT COUNT(' .
                        $this->db_table .
                        '.event_date) AS "Count" FROM ' .
                        $this->db_table .
                        ' LEFT JOIN slnt_cameras ON ' .
                        $this->db_table .
                        '.event_camera_id = slnt_cameras.camIntId AND ' .
                        $this->db_table .
                        '.event_server_id = slnt_cameras.serverId WHERE WEEKOFYEAR(' .
                        $this->db_table .
                        '.event_date) = (weekofyear(CURDATE())) AND slnt_cameras.groupId = ? AND ' .
                        $this->db_table .
                        '.event_event = ?';

                    $stmt = $this->conn->prepare($query);

                    $stmt->execute([$group, 'failed']);
                    break;

                default:
                    # code...
                    break;
            }
        }

        // $row = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row;
    }

    public function getListIssues($period, $list, $dateFormat = 'D')
    {
        $listEvents = [];
        $listGraph = [];
        foreach ($period as $keyDay => $valueDay) {
            foreach ($list as $key => $value) {
                if ($value['Date'] == $valueDay) {
                    $date = date_create($valueDay);
                    $listEvents[date_format($date, $dateFormat)] = intval(
                        $value['Count']
                    );
                    // $listEvents[$valueDay] = intval($value['Count']);
                    break;
                } else {
                    $date = date_create($valueDay);
                    $listEvents[date_format($date, $dateFormat)] = 0;
                }
            }
        }
    
        $data = array_values($listEvents);
        $labels = array_keys($listEvents);
        $listGraph['data'] = $data;
        $listGraph['labels'] = $labels;
    
        return $listEvents;
    }

    public function storeEvents($data)
    {
        $upinsert = $this->getEvent($data);

        list(
            'id' => $id,
            'serverId' => $serverId,
            'server' => $server,
            'name' => $name,
            'event' => $event,
            'date' => $date,
        ) = $data;

        // print_r($upinsert);

        // die;

        if ($upinsert) {
            if (is_null($upinsert[5])) {
                $today = date('Y-m-d H:i:s');
                $days = (strtotime($today) - strtotime($upinsert[4])) / 86400;

                if ($days > 1) {
                    $response = $this->saveEvent(
                        $name,
                        $server,
                        $event,
                        $date,
                        $serverId,
                        $id
                    );
                    return $response;
                }

                $response = $this->updateEventDate($upinsert[0], $date);
                return $response;
            } else {
                $days = (strtotime($date) - strtotime($upinsert[5])) / 86400;

                if ($days <= 1) {
                    $response = $this->updateEventDate($upinsert[0], $date);
                    return $response;
                } else {
                    $response = $this->saveEvent(
                        $name,
                        $server,
                        $event,
                        $date,
                        $serverId,
                        $id
                    );

                    return $response;
                }
            }
        } else {
            $response = $this->saveEvent(
                $name,
                $server,
                $event,
                $date,
                $serverId,
                $id
            );

            return $response;
        }
        return $upinsert;
    }

    public function getEvent($data)
    {
        list(
            'id' => $id,
            'server' => $server,
            'name' => $name,
            'event' => $event,
            'date' => $date,
        ) = $data;

        $query =
            'SELECT * FROM ' .
            $this->db_table .
            ' WHERE 
            event_camera 
             = ? AND 
            event_server 
             = ? AND  
            event_event =  ? 
             ORDER BY event_date DESC LIMIT 1';

        $stmt = $this->conn->prepare($query);

        $stmt->execute([$name, $server, $event]);

        // $row = $stmt->fetchAll();

        $row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT);

        // $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row;
    }

    public function updateEventDate($id, $date)
    {
        $query =
            'UPDATE ' .
            $this->db_table .
            ' SET event_updated_at= ? WHERE event_id=?';

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$date, $id]);
        $row = $stmt->rowCount();
        return $row;
    }

    public function saveEvent(
        $name,
        $server,
        $event,
        $date,
        $serverId,
        $cameraId
    ) {
        $query =
            'INSERT INTO ' .
            $this->db_table .
            " (event_camera, event_server, event_event, event_date, event_updated_at, event_server_id, event_camera_id)
                    VALUES (?, ?, ?,?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            $name,
            $server,
            $event,
            $date,
            null,
            $serverId,
            $cameraId,
        ]);
        $row = $stmt->rowCount();

        return $row;
    }

    public function getCurrentEvents()
    {
        $query =
            'SELECT ' .
            $this->db_table .
            '.*, slnt_cameras.groupId FROM ' .
            $this->db_table .
            ' INNER JOIN slnt_cameras ON slnt_cameras.camIntId = ' .
            $this->db_table .
            '.event_camera_id AND ' .
            $this->db_table .
            '.event_server_id = slnt_cameras.serverId 
            WHERE ' .
            $this->db_table .
            '.event_date >= CURRENT_DATE() OR ' .
            $this->db_table .
            '.event_updated_at >= CURRENT_DATE()';

        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $row;
    }

    public function getEventsFiveMinutes()
    {
        $query =
            'SELECT ' .
            $this->db_table .
            '.*, slnt_cameras.groupId FROM ' .
            $this->db_table .
            ' INNER JOIN slnt_cameras ON slnt_cameras.camIntId = ' .
            $this->db_table .
            '.event_camera_id AND ' .
            $this->db_table .
            '.event_server_id = slnt_cameras.serverId 
            WHERE ' .
            $this->db_table .
            '.event_date > now() - interval 5 minute';

        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $row;
    }
}
?>
