<?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: *");
    header("Access-Control-Allow-Headers: access");
    header("Content-Type: application/json; text/plain; */*");
    header("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Accept, X-Auth-Token, Origin, Application, Access-Control-Allow-Headers, Authorization, X-Requested-With");

   // defined('BASEPATH') OR exit('No Direct Script access allowed');

    class Groups extends CI_Controler {

        public function __construct(){
            parent::__construct();
            error_reporting(E_ALL & ~E_NOTICE);
            date_default_timezone_set('America/New_York');
            $date = date('Y-m-d h:i:s', time());
            $this->load->helper(array('url', 'html', 'form', 'security'));
            $this->load->library('session', 'parser');
            $this->load->library('form_validation');
            $this->load->library('pagination');
            $this->load->database();
            $this->load->model('UserModel');
        }

        public function addgroup()
        {
            echo "data comming soon"; die;
        }

    }








?>