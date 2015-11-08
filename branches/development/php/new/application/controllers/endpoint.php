<?php defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/MyREST_Controller.php';

class Endpoint extends MyREST_Controller
{
    private $activeuser;
    private $activebuilding;
    private $activeuf;
    private $activecards;
    private $activeevents;
    private $offset;
    private $limit;

    public function __construct()
    {
        parent::__construct();
        if (empty($this->user_data)) {
            $this->response(array('status' => 0, 'error' => "Must be authenticated"), 403);
        }
        $this->activeuser = $this->user_data->uid;
        $this->activebuilding = sprintf('%04d', $this->user_data->building);
        $this->activeuf = 'uf';
        $this->activecards = 'cards';
        $this->activeevents = 'events';
        $this->activemsgs = 'messages';
        $this->activealerts = 'alerts';
    }

    private function _mapping_function()
    {
        $args = $this->_args;
        if (!isset($args['operacion'])) {
            $this->response(array('status' => 0, 'error' => 'Debe especificar una operacion'), 404);
        } else {
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

    public function get()
    {
        $this->_mapping_function();
    }

    public function post()
    {
        $this->_mapping_function();
    }

    /**
     * DATOS Personales del USUARIO de la UNIDAD FUNCIONAL
     */
    public function getPersonalDataUser()
    {
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
    public function getCountGeneratedCards()
    {
        $cardsearch = "SELECT "
            . " username, card_id "
            . " FROM $this->activecards "
            . " WHERE uf "
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
    public function getBuildingData()
    {
        $buildingsearch = "SELECT "
            . " bld_address, bld_country, bld_city, adm_name, adm_mail, adm_tel "
            . " FROM buildings "
            . " WHERE building_id "
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
    public function getUsersData()
    {
        $usersearch = "SELECT "
            . " card_id, username, card_mail, card_tel, card_tz, card_img "
            . " FROM $this->activecards "
            . " WHERE uf "
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
    public function getEventosDataByUser()
    {
        $userfilter = "SELECT "
            . " username "
            . " FROM $this->activecards "
            . " WHERE uf "
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
    private function _getEventosDataArray($notification = FALSE)
    {
        $query_plus = "";
        $query_add = "";
        if ($this->user_data->userrole == "adm") {
            $query_add = " LEFT JOIN $this->activeuf ON uf = uf_id WHERE username IS NOT NULL ";
        } elseif ($this->user_data->userrole == "mst") {
            if (isset($this->_args['filter-users-enabled']) && !empty($this->_args['filter-users-enabled'])) {
                $this->_args['filter_users_enabled'] = $this->_args['filter-users-enabled'];
            }
            if (isset($this->_args['filter_users_enabled']) && !empty($this->_args['filter_users_enabled'])) {
                $list_usr_ids = explode(",", $this->_args['filter_users_enabled']);
                $string_usr_ids = "'" . implode("','", $list_usr_ids) . "'";
                $query_plus .= " LEFT JOIN $this->activeuf ON uf = uf_id WHERE username IS NOT NULL ";
                $query_plus .= " AND card_id IN ({$string_usr_ids}) ";
            } elseif ($notification == FALSE) {
                $query_add = " WHERE card_id "
                    . " IN (SELECT card_id FROM $this->activecards WHERE uf "
                    . " IN (SELECT uf_id FROM $this->activeuf WHERE uf_mail "
                    . " IN (SELECT email FROM members WHERE id = $this->activeuser))) ";
            } else {
                $query_add = " LEFT JOIN $this->activeuf ON uf = uf_id WHERE username IS NOT NULL ";
            }
        } else {
            $query_add = " WHERE card_id "
                . " IN (SELECT usr_id FROM $this->activecards WHERE uf "
                . " IN (SELECT uf_id FROM $this->activeuf WHERE uf_mail "
                . " IN (SELECT email FROM members WHERE id = $this->activeuser))) ";
        }

        $searchevents = "SELECT $this->activeevents.* ,$this->activecards.* FROM $this->activeevents "
            . "LEFT JOIN $this->activecards ON $this->activeevents.card_id = $this->activecards.card_id "
            . $query_add . $query_plus;
        //$this->response($searchevents);
        //Filter by User
        if (isset($this->_args['filter-user']) && !empty($this->_args['filter-user'])) {
            $this->_args['filter_user'] = $this->_args['filter-user'];
        }
        if (isset($this->_args['filter_user']) && !empty($this->_args['filter_user'])) {
            $searchevents .= " AND username LIKE '%{$this->_args['filter_user']}%' ";
        }

        //Filter by Event type
        if (isset($this->_args['filter-evt-type']) && !empty($this->_args['filter-evt-type'])) {
            $this->_args['filter_evt_info'] = $this->_args['filter-evt-type'];
        }
        if (isset($this->_args['filter_evt_info']) && !empty($this->_args['filter_evt_info'])) {
            $where_event_type = $this->_getFilterByEventType((int)$this->_args['filter_evt_info']);
            $searchevents .= " AND evt_info IN {$where_event_type} ";
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
            //$this->response($start_date);
        }
        if (isset($this->_args['filter_evt_date_end']) && !empty($this->_args['filter_evt_date_end'])) {
            $end_date = $this->_convert_date($this->_args['filter_evt_date_end']);
            $searchevents .= " AND evt_date <= '{$end_date}' ";
        }
        if ($this->user_data->userrole == "adm") {
            $searchevents .= " ORDER BY evt_id DESC";
        } else {
            $searchevents .= " ORDER BY evt_date DESC";
        }

        if (!empty($this->limit)) {
            $searchevents .= " LIMIT $this->limit";
        }
        if (!empty($this->offset)) {
            $searchevents .= " OFFSET $this->offset";
        }
        //$this->response($searchevents);
        $query = $this->db->query($searchevents);
        $result = array(); //d/m/Y H:m

        //get data building
        $building_query = "SELECT * FROM buildings WHERE building_id = " . $this->user_data->building;
        $data_building = (array)reset($this->db->query($building_query)->result());
        foreach ($query->result() as $row) {
            $row->evt_door = "";
            if (!empty($data_building)) {
                if (($row->evt_info == "S01" || $row->evt_info == "S02") && isset($data_building['bld_s12'])) {
                    $row->evt_door = $data_building['bld_s12'];
                }
                if (($row->evt_info == "S03" || $row->evt_info == "S04") && isset($data_building['bld_s34'])) {
                    $row->evt_door = $data_building['bld_s34'];
                }
                if (($row->evt_info == "S05" || $row->evt_info == "S06") && isset($data_building['bld_s56'])) {
                    $row->evt_door = $data_building['bld_s56'];
                }
                if (($row->evt_info == "S07" || $row->evt_info == "S08") && isset($data_building['bld_s78'])) {
                    $row->evt_door = $data_building['bld_s78'];
                }
                if (($row->evt_info == "S09" || $row->evt_info == "S10") && isset($data_building['bld_s910'])) {
                    $row->evt_door = $data_building['bld_s910'];
                }
                if (($row->evt_info == "V01" || $row->evt_info == "V02") && isset($data_building['bld_s12'])) {
                    $row->evt_door = $data_building['bld_s12'];
                }
                if (($row->evt_info == "V03" || $row->evt_info == "V04") && isset($data_building['bld_s34'])) {
                    $row->evt_door = $data_building['bld_s34'];
                }
                if (($row->evt_info == "V05" || $row->evt_info == "V06") && isset($data_building['bld_s56'])) {
                    $row->evt_door = $data_building['bld_s56'];
                }
                if (($row->evt_info == "V07" || $row->evt_info == "V08") && isset($data_building['bld_s78'])) {
                    $row->evt_door = $data_building['bld_s78'];
                }
                if (($row->evt_info == "V09" || $row->evt_info == "V10") && isset($data_building['bld_s910'])) {
                    $row->evt_door = $data_building['bld_s910'];
                }
                if (($row->evt_info == "I01" || $row->evt_info == "I02") && isset($data_building['bld_s12'])) {
                    $row->evt_door = $data_building['bld_s12'];
                }
                if (($row->evt_info == "I03" || $row->evt_info == "I04") && isset($data_building['bld_s34'])) {
                    $row->evt_door = $data_building['bld_s34'];
                }
                if (($row->evt_info == "I05" || $row->evt_info == "I06") && isset($data_building['bld_s56'])) {
                    $row->evt_door = $data_building['bld_s56'];
                }
                if (($row->evt_info == "I07" || $row->evt_info == "I08") && isset($data_building['bld_s78'])) {
                    $row->evt_door = $data_building['bld_s78'];
                }
                if (($row->evt_info == "I09" || $row->evt_info == "I10") && isset($data_building['bld_s910'])) {
                    $row->evt_door = $data_building['bld_s910'];
                }
                if (($row->evt_info == "A01" || $row->evt_info == "A02") && isset($data_building['bld_s12'])) {
                    $row->evt_door = $data_building['bld_s12'];
                }
                if (($row->evt_info == "A03" || $row->evt_info == "A04") && isset($data_building['bld_s34'])) {
                    $row->evt_door = $data_building['bld_s34'];
                }
                if (($row->evt_info == "A05" || $row->evt_info == "A06") && isset($data_building['bld_s56'])) {
                    $row->evt_door = $data_building['bld_s56'];
                }
                if (($row->evt_info == "A07" || $row->evt_info == "A08") && isset($data_building['bld_s78'])) {
                    $row->evt_door = $data_building['bld_s78'];
                }
                if (($row->evt_info == "A09" || $row->evt_info == "A10") && isset($data_building['bld_s910'])) {
                    $row->evt_door = $data_building['bld_s910'];
                }

                if (($row->evt_info == "E01" || $row->evt_info == "E02") && isset($data_building['bld_s12'])) {
                    $row->evt_door = $data_building['bld_s12'];
                }
                if (($row->evt_info == "E03" || $row->evt_info == "E04") && isset($data_building['bld_s34'])) {
                    $row->evt_door = $data_building['bld_s34'];
                }
                if (($row->evt_info == "E05" || $row->evt_info == "E06") && isset($data_building['bld_s56'])) {
                    $row->evt_door = $data_building['bld_s56'];
                }
                if (($row->evt_info == "E07" || $row->evt_info == "E08") && isset($data_building['bld_s78'])) {
                    $row->evt_door = $data_building['bld_s78'];
                }
                if (($row->evt_info == "E09" || $row->evt_info == "E10") && isset($data_building['bld_s910'])) {
                    $row->evt_door = $data_building['bld_s910'];
                }

                if (($row->evt_info == "B01" || $row->evt_info == "B02") && isset($data_building['bld_s12'])) {
                    $row->evt_door = $data_building['bld_s12'];
                }
                if (($row->evt_info == "B03" || $row->evt_info == "B04") && isset($data_building['bld_s34'])) {
                    $row->evt_door = $data_building['bld_s34'];
                }
                if (($row->evt_info == "B05" || $row->evt_info == "B06") && isset($data_building['bld_s56'])) {
                    $row->evt_door = $data_building['bld_s56'];
                }
                if (($row->evt_info == "B07" || $row->evt_info == "B08") && isset($data_building['bld_s78'])) {
                    $row->evt_door = $data_building['bld_s78'];
                }
                if (($row->evt_info == "B09" || $row->evt_info == "B10") && isset($data_building['bld_s910'])) {
                    $row->evt_door = $data_building['bld_s910'];
                }
            }
            $row->evt_info = $this->_getEventType($row->evt_info);
            $time = strtotime($row->evt_date);
            $row->evt_date_date = date("j/m/y", $time);
            $row->evt_date_hour = date("H:i", $time);
            $result[] = $row;
        }
        return $result;
    }

    public function getEventosData()
    {
        $result = $this->_getEventosDataArray();
        $this->response(array('status' => 1, 'result' => $result), 200);
    }

    private function _getFilterByEventType($type_number)
    {
        switch ($type_number) {
            case 1:
                return "('S1', 'S3', 'S5', 'S7', 'S9')";
            case 2:
                return "('S2', 'S4', 'S6', 'S8', 'S10')";
            case 3:
                return "('V01', 'V03', 'V05', 'V07', 'V09')";
            case 4:
                return "('V02', 'V04', 'V06', 'V08' ,'V10')";
            case 5:
                return "('INV')";
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

    private function _convert_date($date)
    {
        $explode = explode("/", $date);
        $time = mktime(12, 0, 0, (int)$explode[1], (int)$explode[0], (int)$explode[2]);
        return date('Y-m-d H:i:s', $time);
    }

    private function _getEventType($type_id)
    {
        /*
         *
    S1, S3, S5, S7, S9 = Entrada => 1

    S2, S4, S6, S8, S10 = Salida => 2

    V01, V03, V05, V07, V09 = Entrada Visita => 3

    V02, V04, V06, V08 ,V10 = Salida Visita => 4

    INV = Tarjeta Invalida => 5

    A01, A03, A05, A07, A09 = Puerta Abierta => 6

    E01, E03, E05, E07, E09 = Emergencia => 7

    B01, B03, B05, B07, B09 = Entrada con Pulsador => 8

    B02, B04, B06, B08, B10 = Salida con Pulsador => 9
         * */
        switch ($type_id) {
            case "S1":
            case "S3":
            case "S5":
            case "S7":
            case "S9":
                return "1";
            case "S2":
            case "S4":
            case "S8":
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
            case "INV":
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
    public function getEmergencyListData()
    {
        $searchalerts = "SELECT "
            . " evt_date, card_id, evt_info , $this->activecards.* "
            . " FROM $this->activeevents "
            . " LEFT JOIN $this->activecards ON card_id = card_id "
            . " WHERE evt_info LIKE 'e%' AND card_id "
            . " IN (SELECT card_id FROM $this->activecards WHERE uf "
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

    function sendNotificationMail()
    {
        $args = $this->_args;
        $toEmail = $args['email'];
        $message = $args['message'];
        $send_result = mail($toEmail, "Notificación de Smart Control System", $message);
        if ($send_result) {
            $this->response(array('status' => 1, 'result' => "Mensaje enviado"), 200);
        } else {
            $this->response(array('status' => 0, 'error' => "Error enviando el correo"), 500);
        }
    }

    function registerAlert()
    {
        $args = $this->_args;
        $alert_uf = $args['alert_uf'];
        $alert_type = $args['alert_type'];
        $alert_coords = $args['alert_coords'];
        $result = $this->db->insert($this->activealerts, array(
            'alert_uf' => $alert_uf,
            'alert_type' => $alert_type,
            'alert_coords' => $alert_coords,
        ));
        if ($result) {
            $this->response(array('status' => 1, 'result' => "Alerta registrada."), 200);
        } else {
            $this->response(array('status' => 0, 'error' => "Error registrando alerta."), 500);
        }
    }

    function getMessagesData()
    {
        $id = $this->input->get('msg_id');
        $msgsearch = "SELECT * "
            . " FROM $this->activemsgs "
            . " WHERE uf IS NULL ";
        if (!empty($this->user_data->uf)) {
            $msgsearch .= " OR uf = " . $this->user_data->uf;
        }

        if (!empty($id)) {
            $msgsearch .= " AND msg_id > " . $id;
        }

        $msgsearch .= " ORDER BY msg_id DESC ";

        if (!empty($this->limit)) {
            $msgsearch .= " LIMIT $this->limit";
        }
        if (!empty($this->offset)) {
            $msgsearch .= " OFFSET $this->offset";
        }

        $query = $this->db->query($msgsearch);
        $result = array(); //d/m/Y H:m
        foreach ($query->result() as $row) {
            $time = strtotime($row->msg_date);
            $row->msg_date_date = date("j/m/y", $time);
            $row->msg_date_hour = date("H:i", $time);
            $result[] = $row;
        }
        $this->response(array('status' => 1, 'result' => $result), 200);
    }

    function saveNotification()
    {
        $args = $this->_args;
        $usr = $this->user_data->uid;
        $usr_id = $args['usr_id'];
        $events = $args['events'];
        $this->db->delete('notifications', array('usr' => $usr, 'usr_id' => $usr_id));
        $result = $this->db->insert('notifications', array(
            'usr' => $usr,
            'usr_id' => $usr_id,
            'events' => $events
        ));

        if ($result) {
            $this->response(array('status' => 1, 'result' => "Notificación registrada."), 200);
        } else {
            $this->response(array('status' => 0, 'error' => "Error registrando notificación."), 500);
        }
    }

    function getNotifications()
    {
        $usr = $this->user_data->uid;
        $result = $this->db->get_where('notifications', array('usr' => $usr));
        $this->response(array('status' => 1, 'result' => $result->result()), 200);
    }

    function getEventosDataNotifications()
    {
        $result = $this->_getEventosDataArray(TRUE);
        $usr = $this->user_data->uid;
        $notifications = $this->db->get_where('notifications', array('usr' => $usr))->result();
        $flag = FALSE;
        foreach ($result as $row) {
            if ($notifications && is_array($notifications) && count($notifications) > 0) {
                foreach ($notifications as $notification) {
                    $notification_event_types = explode(",", $notification->events);
                    if ($row->usr_id == $notification->usr_id && in_array($row->evt_info, $notification_event_types)) {
                        $this->response(array('status' => 1, 'check' => TRUE, 'event_data' => $row), 200);
                        $flag = TRUE;
                    }
                    if ($flag) {
                        break;
                    }
                }
            }
            if ($flag) {
                break;
            }
        }
        $this->response(array('status' => 1, 'check' => FALSE), 200);
    }

}