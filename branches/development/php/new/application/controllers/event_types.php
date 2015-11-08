<?php defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/MyREST_Controller.php';

class Event_types extends MyREST_Controller {
  public function __construct() {
    parent::__construct();
  }

  public function get(){
    $query = $this->db->get('evt_type');

    $this->response(array('status' => 1, 'result' => $query->result()), 200);
  }
}