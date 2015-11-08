<?php defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/MyREST_Controller.php';

class Event extends MyREST_Controller {
  private $activeuser;
  private $activebuilding;
  private $activeuf;
  private $activecards;
  private $activeevents;

  public function __construct() {
    parent::__construct();

    $this->activeuser = $this->user_data->uid;
    $this->activebuilding = sprintf('%04d', $this->user_data->building);
    $this->activeuf = 'bld'.$this->activebuilding.'_uf';
    $this->activecards = 'bld'.$this->activebuilding.'_cards';
    $this->activeevents = 'bld'.$this->activebuilding.'_events';
  }

  public function post(){
    $this->_validate_input_data($this->_args, array('evt_date', 'evt_type', 'evt_door', 'evt_card'));

    $_data = array(
      'evt_date' => $this->_args['evt_date'],
      'evt_type' => $this->_args['evt_type'],
      'evt_door' => $this->_args['evt_door'],
      'evt_card' => $this->_args['evt_card'],
    );
    $this->db->insert($this->activeevents, $_data);
    $this->response(array('status' => 1, 'result' => 'Evento registrado satisfactoriamente.'), 200);
  }
}