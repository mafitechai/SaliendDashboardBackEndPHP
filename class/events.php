<?php

    class Events {
        private $conn;
        //Table
        private $db_table = "slnt_events";
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
        public function __construct($db){
            $this->conn = $db;

            $this->previousWeek = strtotime("-1 week +1 day");
            $this->startWeekP = strtotime("last monday midnight",$this->previousWeek);
            $this->endWeekP = strtotime("next sunday",$this->startWeekP);
            $this->startWeekP = date("Y-m-d",$this->startWeekP);
            $this->endWeekP = date("Y-m-d",$this->endWeekP);

            $this->thisWeek = strtotime("today");
            $this->startWeekC = strtotime("last monday midnight",$this->thisWeek);
            $this->startWeekC = date("Y-m-d",$this->startWeekC);
        }


        //Get today events
        public function countEvents($range, $event = false, $customDate = false){
            switch($range){
                case 'today':
                    $today = date("Y-m-d");
                    $whereDate = "event_date = '" . $today . "'";   
                    break; 
                case 'yesterday':
                    $yesterday = date('Y-m-d',strtotime("-1 days"));
                    $whereDate = "event_date = '" . $yesterday . "'"; 
                    break;
                case 'month':
                    $today = date("Y-m-d");
                    $month = date("m");
                    $year = date("Y");
                    $whereDate = "event_date BETWEEN '" . $year . "-" . $month . "-01' AND '" . $today . "'";    
                    break;
                case 'lastWeek':
                    $whereDate = "event_date BETWEEN '" . $this->startWeekP . "' AND '" . $this->endWeekP . "'";
                    break;
                case 'thisWeek':
                    $today = date("Y-m-d");
                    $whereDate = "event_date BETWEEN '" . $this->startWeekC . "' AND '" . $today . "'";
                    break;
                case 'custom':
                    $whereDate = "event_date = '" . $customDate . "'";
                    break;
                default:
                    $today = date("Y-m-d");
                    $whereDate = "event_date = '" . $today . "'";
                    break;
            }

            if($event){
                switch($event){
                    case 'failed':
                        $whereDate .= " AND  event_event = 'failed'";   
                        break;
                    case 'off':
                        $whereDate .= " AND  event_event = 'off'";   
                        break; 
                    default:
                        $whereDate .= "";
                        break;
                }
            }
            
            $query = "SELECT COUNT('event_id') FROM " . $this->db_table . " WHERE " . $whereDate;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
                $count = $row[0];
            }
            return $count;
        }

        


        public function countPendingToFix($range){
            return 0;
        }

        public function countRecurrentEvents($range){
            $today = date("Y-m-d");
            $arrCount = 1;

            if($range == 'thisWeek')
                $query = "SELECT event_camera FROM " . $this->db_table . " WHERE event_date BETWEEN '" . $this->startWeekC . "' AND '" . $today . "' GROUP BY 'event_camera' HAVING COUNT(event_camera) > 1";
            else if($range == 'lastWeek')
                $query = "SELECT event_camera, COUNT(event_camera) FROM " . $this->db_table . " WHERE event_date BETWEEN '" . $this->startWeekP . "' AND '" . $this->endWeekP . "' GROUP BY 'event_camera' HAVING COUNT(event_camera) > 1";

            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_NUM, PDO::FETCH_ORI_NEXT)) {
                $arrCount++;
            }
            
            return $arrCount;
        }

        public function getEquipmentLabels(){
            $dayLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            return $dayLabels;
        }

        public function countNewIssuesArr($range, $beginDayP = false){
            if($range == 'thisWeek'){
                $today = date("Y-m-d");
                $beginDay = $beginDayP === false ? $this->startWeekC : $beginDayP;
                if($beginDay <= $today){
                    $numEvents = $this->countEvents('custom', false, $beginDay);
                    array_push($this->arrIssuesC, $numEvents);
                    $beginDay = date("Y-m-d", strtotime($beginDay . "+1 days"));
                    $this->countNewIssuesArr($range, $beginDay);
                }
            } else if($range == 'lastWeek'){
                $beginDay = $beginDayP === false ? $this->startWeekP : $beginDayP;
                $lastDay = $this->endWeekP;
                if($beginDay <= $lastDay){
                    $numEvents = $this->countEvents('custom', false, $beginDay);
                    array_push($this->arrIssuesL, $numEvents);
                    $beginDay = date("Y-m-d", strtotime($beginDay . "+1 days"));
                    $this->countNewIssuesArr($range, $beginDay);
                } 
            }
            return $finalArray = $range == 'thisWeek' ? $this->arrIssuesC : $this->arrIssuesL;
        }

        public function countCamerasOFFArr($range, $beginDayP = false){
            if($range == 'thisWeek'){
                $today = date("Y-m-d");
                $beginDay = $beginDayP === false ? $this->startWeekC : $beginDayP;
                if($beginDay <= $today){
                    $numEvents = $this->countEvents('custom', 'off', $beginDay);
                    array_push($this->arrOffC, $numEvents);
                    $beginDay = date("Y-m-d", strtotime($beginDay . "+1 days"));
                    $this->countCamerasOFFArr($range, $beginDay);
                }
            } else if($range == 'lastWeek'){
                $beginDay = $beginDayP === false ? $this->startWeekP : $beginDayP;
                $lastDay = $this->endWeekP;
                if($beginDay <= $lastDay){
                    $numEvents = $this->countEvents('custom', 'off', $beginDay);
                    array_push($this->arrOffL, $numEvents);
                    $beginDay = date("Y-m-d", strtotime($beginDay . "+1 days"));
                    $this->countCamerasOFFArr($range, $beginDay);
                } 
            }
            return $finalArray = $range == 'thisWeek' ? $this->arrOffC : $this->arrOffL;
        }

        public function countIssuesFixed($range){
            return 0;
        }
        
        public function getTotalGroups(){
            return 0;
        }
        
    }
?>