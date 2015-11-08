<?php defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/MyREST_Controller.php';

class Endpoint extends MyREST_Controller {
  private $activeuser;
  private $activebuilding;
  private $activeuf;
  private $activecards;
  private $activeevents;
  private $offset;
  private $limit;

  public function __construct() {
    parent::__construct();
    if (empty($this->user_data)) {
      $this->response(array('status' => 0, 'error' => "Must be authenticated"), 403);
    }
    $this->activeuser = $this->user_data->uid;
    $this->activebuilding = sprintf('%04d', $this->user_data->building);
    $this->activeuf = 'bld' . $this->activebuilding . '_uf';
    $this->activecards = 'bld' . $this->activebuilding . '_cards';
    $this->activeevents = 'bld' . $this->activebuilding . '_events';
  }

  private function _mapping_function() {
    $args = $this->_args;
    if (!isset($args['operacion'])) {
      $this->response(array('status' => 0, 'error' => 'Debe especificar una operacion'), 404);
    }
    else {
      try {
        //settings limit and offset
        $this->limit = isset($args['limit']) ? $args['limit'] : 0;
        $this->offset = isset($args['offset']) ? $args['offset'] : 0;

        $this->$args['operacion']();
      } catch (Exception $e) {
        $this->response(array('status' => 0, 'error' => $e->getMessage()), $e->getCode());
      }
    }
  }

  public function get() {
    $this->_mapping_function();
  }

  public function post() {
    $this->_mapping_function();
  }

  /**
   * DATOS Personales del USUARIO de la UNIDAD FUNCIONAL
   */
  public function getPersonalDataUser() {
    $ownersearch = "SELECT "
      . " uf_id, uf_cont, uf_mail, uf_tel, uf_tel2 "
      . " FROM $this->activeuf "
      . " WHERE uf_mail "
      . " IN (SELECT email FROM members WHERE id = $this->activeuser)";

    if (!empty($this->limit)) {
      $ownersearch .= " LIMIT $this->limit";
    }
    if (!empty($this->offset)) {
      $ownersearch .= " OFFSET $this->offset";
    }

    $query = $this->db->query($ownersearch);
    $this->response(array('status' => 1, 'result' => $query->result()), 200);
  }

  /**
   * Cantidad de TARJETAS generadas para cada UNIDAD FUNCIONAL
   */
  public function getCountGeneratedCards() {
    $cardsearch = "SELECT "
      . " usr_name, usr_id "
      . " FROM $this->activecards "
      . " WHERE usr_uf "
      . " IN (SELECT uf_id FROM $this->activeuf WHERE uf_mail "
      . " IN (SELECT email FROM members WHERE id = $this->activeuser))";

    if (!empty($this->limit)) {
      $cardsearch .= " LIMIT $this->limit";
    }
    if (!empty($this->offset)) {
      $cardsearch .= " OFFSET $this->offset";
    }

    $query = $this->db->query($cardsearch);
    $this->response(array('status' => 1, 'result' => $query->result()), 200);
  }

  /**
   * DATOS del EDIFICIO al que pertenece el USUARIO
   */
  public function getBuildingData() {
    $buildingsearch = "SELECT "
      . " bld_addr, bld_loc, bld_cty, adm_name, adm_mail, adm_tel "
      . " FROM bld_index "
      . " WHERE bld_id "
      . " IN (SELECT building FROM members WHERE id = $this->activeuser)";

    if (!empty($this->limit)) {
      $buildingsearch .= " LIMIT $this->limit";
    }
    if (!empty($this->offset)) {
      $buildingsearch .= " OFFSET $this->offset";
    }

    $query = $this->db->query($buildingsearch);
    $this->response(array('status' => 1, 'result' => $query->result()), 200);
  }

  /**
   * DATOS de USUARIOS generado para cada UNIDAD FUNCIONAL
   */
  public function getUsersData() {
    $usersearch = "SELECT "
      . " usr_name, usr_mail, usr_tel, usr_dni, usr_birth, usr_type, usr_time, usr_img "
      . " FROM $this->activecards "
      . " WHERE usr_uf "
      . " IN (SELECT uf_id FROM $this->activeuf WHERE uf_mail "
      . " IN (SELECT email FROM members WHERE id = $this->activeuser))";

    if (!empty($this->limit)) {
      $usersearch .= " LIMIT $this->limit";
    }
    if (!empty($this->offset)) {
      $usersearch .= " OFFSET $this->offset";
    }

    $query = $this->db->query($usersearch);
    $this->response(array('status' => 1, 'result' => $query->result()), 200);
  }

  /**
   * BUSQUEDA de EVENTOS por USUARIO
   */
  public function getEventosDataByUser() {
    $userfilter = "SELECT "
      . " usr_name "
      . " FROM $this->activecards "
      . " WHERE usr_uf "
      . " IN (SELECT uf_id FROM $this->activeuf WHERE uf_mail "
      . " IN (SELECT email FROM members WHERE id = $this->activeuser))";

    if (!empty($this->limit)) {
      $userfilter .= " LIMIT $this->limit";
    }
    if (!empty($this->offset)) {
      $userfilter .= " OFFSET $this->offset";
    }

    $query = $this->db->query($userfilter);
    $this->response(array('status' => 1, 'result' => $query->result()), 200);
  }

  /**
   * LISTADO de EVENTOS de cada UNIDAD FUNCIONAL
   */
  public function getEventosData() {
    $searchevents = "SELECT "
      . " $this->activeevents.* ,$this->activecards.* "
      . " FROM $this->activeevents "
      . " LEFT JOIN $this->activecards ON evt_card = usr_id "
      . " WHERE evt_card "
      . " IN (SELECT usr_id FROM $this->activecards WHERE usr_uf "
      . " IN (SELECT uf_id FROM $this->activeuf WHERE uf_mail "
      . " IN (SELECT email FROM members WHERE id = $this->activeuser))) ";

    //Filter by User
    if (isset($this->_args['filter-user']) && !empty($this->_args['filter-user'])) {
      $this->_args['filter_user'] = $this->_args['filter-user'];
    }
    if (isset($this->_args['filter_user']) && !empty($this->_args['filter_user'])) {
      $searchevents .= " AND usr_name LIKE '%{$this->_args['filter_user']}%' ";
    }

    //Filter by Event type
    if (isset($this->_args['filter-evt-type']) && !empty($this->_args['filter-evt-type'])) {
      $this->_args['filter_evt_type'] = $this->_args['filter-evt-type'];
    }
    if (isset($this->_args['filter_evt_type']) && !empty($this->_args['filter_evt_type'])) {
      $where_event_type = $this->_getFilterByEventType((int) $this->_args['filter_evt_type']);
      $searchevents .= " AND evt_type IN {$where_event_type} ";
    }
    //Filter by Event date
    if (isset($this->_args['filter-evt-date-start']) && !empty($this->_args['filter-evt-date-start'])) {
      $this->_args['filter_evt_date_start'] = $this->_args['filter-evt-date-start'];
    }
    if (isset($this->_args['filter-evt-date-end']) && !empty($this->_args['filter-evt-date-end'])) {
      $this->_args['filter_evt_date_end'] = $this->_args['filter-evt-date-end'];
    }
    if (isset($this->_args['filter-evt-date-start-not']) && !empty($this->_args['filter-evt-date-start-not'])) {
      $this->_args['filter_evt_date_start_not'] = $this->_args['filter-evt-date-start-not'];
    }
    if (isset($this->_args['filter_evt_date_start']) && !empty($this->_args['filter_evt_date_start'])) {
      $start_date = $this->_convert_date($this->_args['filter_evt_date_start']);
      $searchevents .= " AND evt_date >= '{$start_date}'";
    }
    if (isset($this->_args['filter_evt_date_start_not']) && !empty($this->_args['filter_evt_date_start_not'])) {
      $start_date = $this->_args['filter_evt_date_start_not'];
      $searchevents .= " AND evt_date > '{$start_date}'";
    }
    if (isset($this->_args['filter_evt_date_end']) && !empty($this->_args['filter_evt_date_end'])) {
      $end_date = $this->_convert_date($this->_args['filter_evt_date_end']);
      $searchevents .= " AND evt_date <= '{$end_date}' ";
    }

    $searchevents .= " ORDER BY evt_date DESC";

    if (!empty($this->limit)) {
      $searchevents .= " LIMIT $this->limit";
    }
    if (!empty($this->offset)) {
      $searchevents .= " OFFSET $this->offset";
    }
//die($searchevents);
    $query = $this->db->query($searchevents);
    $result = array(); //d/m/Y H:m
    foreach ($query->result() as $row) {
      $row->evt_type = $this->_getEventType($row->evt_type);
      $time = strtotime($row->evt_date);
      $row->evt_date_date = date("j/m/y", $time);
      $row->evt_date_hour = date("H:i", $time);
      $result[] = $row;
    }

    $this->response(array('status' => 1, 'result' => $query->result()), 200);
  }

  private function _getFilterByEventType($type_number) {
    switch ($type_number) {
      case 1:
        return "('S01', 'S03', 'S05', 'S07', 'S09')";
      case 2:
        return "('S02', 'S04', 'S06', 'S08', 'S10')";
      case 3:
        return "('V01', 'V03', 'V05', 'V07', 'V09')";
      case 4:
        return "('V02', 'V04', 'V06', 'V08' ,'V10')";
      case 5:
        return "('I01', 'I02', 'I03', 'I04', 'I05', 'I06', 'I07', 'I08', 'I09', 'I10')";
      case 6:
        return "('A01', 'A03', 'A05', 'A07', 'A09')";
      case 7:
        return "('E01', 'E03', 'E05', 'E07', 'E09')";
      case 8:
        return "('B01', 'B03', 'B05', 'B07', 'B09')";
      case 9:
        return "('B02', 'B04', 'B06', 'B08', 'B10')";
      default:
        return "()";
    }
  }

  private function _convert_date($date) {
    $explode = explode("/", $date);
    $time = mktime(12, 0, 0, (int) $explode[1], (int) $explode[0], (int) $explode[2]);
    return date('Y-m-d H:i:s', $time);
  }

  private function _getEventType($type_id) {
    /*
     *
S01, S03, S05, S07, S09 = Entrada => 1

S02, S04, S06, S08, S10 = Salida => 2

V01, V03, V05, V07, V09 = Entrada Visita => 3

V02, V04, V06, V08 ,V10 = Salida Visita => 4

I01 a I10 = Tarjeta Invalida => 5

A01, A03, A05, A07, A09 = Puerta Abierta => 6

E01, E03, E05, E07, E09 = Emergencia => 7

B01, B03, B05, B07, B09 = Entrada con Pulsador => 8

B02, B04, B06, B08, B10 = Salida con Pulsador => 9
     * */
    switch ($type_id) {
      case "S01":
      case "S03":
      case "S05":
      case "S07":
      case "S09":
        return "1";
      case "S02":
      case "S04":
      case "S08":
      case "S10":
        return "2";
      case "V01":
      case "V03":
      case "V05":
      case "V07":
      case "V09":
        return "3";
      case "V02":
      case "V04":
      case "V06":
      case "V08":
      case "V10":
        return "4";
      case "I01":
      case "I02":
      case "I03":
      case "I04":
      case "I05":
      case "I06":
      case "I07":
      case "I08":
      case "I09":
      case "I10":
        return "5";
      case "A01":
      case "A03":
      case "A05":
      case "A07":
      case "A09":
        return "6";
      case "E01":
      case "E03":
      case "E05":
      case "E07":
      case "E09":
        return "7";
      case "B01":
      case "B03":
      case "B05":
      case "B07":
      case "B09":
        return "8";
      case "B02":
      case "B04":
      case "B06":
      case "B08":
      case "B10":
        return "9";
      default:
        return "0";
    }
  }

  /**
   * LISTADO de EMERGENCIAS de cada UNIDAD FUNCIONAL
   */
  public function getEmergencyListData() {
    $searchalerts = "SELECT "
      . " evt_date, evt_card, evt_type ,$this->activecards.* "
      . " FROM $this->activeevents "
      . " LEFT JOIN $this->activecards ON evt_card = usr_id "
      . " WHERE evt_type LIKE 'e%' AND evt_card "
      . " IN (SELECT usr_id FROM $this->activecards WHERE usr_uf "
      . " IN (SELECT uf_id FROM $this->activeuf WHERE uf_mail "
      . " IN (SELECT email FROM members WHERE id = $this->activeuser))) "
      . " ORDER BY evt_date DESC";

    if (!empty($this->limit)) {
      $searchalerts .= " LIMIT $this->limit";
    }
    if (!empty($this->offset)) {
      $searchalerts .= " OFFSET $this->offset";
    }

    $query = $this->db->query($searchalerts);
    $this->response(array('status' => 1, 'result' => $query->result()), 200);
  }

  function sendNotificationMail() {
    $args = $this->_args;
    $toEmail = $args['email'];
    $message = $args['message'];
    $send_result = mail($toEmail, "Notificación de Smart Control System", $message);
    if ($send_result) {
      $this->response(array('status' => 1, 'result' => "Mensaje enviado"), 200);
    }
    else {
      $this->response(array('status' => 0, 'error' => "Error enviando el correo"), 500);
    }
  }

}