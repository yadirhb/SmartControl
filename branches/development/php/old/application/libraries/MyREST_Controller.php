<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: jose
 * Date: 11/4/13
 * Time: 9:12 AM
 */

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/REST_Controller.php';

//date_default_timezone_set("America/")

define('SESSION_EXPIRE_TIME', 604800); //in seconds
define('DUELO_EXPIRE_DATE', 86400); //in seconds
define('GUEST_ACCESS_TOKEN', '1234567890');

class MyREST_Controller extends REST_Controller{
  protected $request_vars;
  protected $access_token;
  protected $is_guest;
  protected $session_data;
  protected $user_data;
  protected $language;

  public function __construct(){
    parent::__construct();

    $this->request_vars = $this->get_request_headers();
    $this->_args = array_merge($this->_args, $this->request_vars);

    $this->load->library('encrypt');
    $this->load->database();
    $this->load->helper('string');
    $this->load->helper('security');
    $this->load->model('user_model');
    $this->load->helper('language');

    //checking security
    $access_token = '';
    if (isset($this->_args['Access-Token']))
      $access_token = $this->_args['Access-Token'];
    if (isset($this->_args['access_token']))
      $access_token = $this->_args['access_token'];

    $this->language = "es";
    if (isset($this->_args['lang']))
      $this->language = $this->_args['lang'];

    $lang = ($this->language == 'en' ? 'english' : 'spanish');
    $this->lang->load('rest_responses', $lang);

    $this->session_data = $this->user_model->_validate_access_token($access_token);
    if ($access_token != GUEST_ACCESS_TOKEN && !$this->session_data) {
      $this->response(array('status' => 0, 'error' => $this->lang->line('response_invalid_token')), 403);
    }
    $this->is_guest = empty($access_token) || $access_token == GUEST_ACCESS_TOKEN;
    $this->access_token = $access_token;
    if(!$this->is_guest)
      $this->user_data = $this->user_model->getUserWhere(array('id' => $this->session_data->uid));
    else
      $this->user_data = NULL;

    /**/
    //hack this user from the session
    //$this->user_data = $this->user_model->getUserWhere(array('id' => 2));
  }

  /**
   * Transforms $_SERVER HTTP headers into a nice associative array. For example:
   *   array(
   *       'Referer' => 'example.com',
   *       'X-Requested-With' => 'XMLHttpRequest'
   *   )
   */
  protected function get_request_headers() {
    $headers = array();
    foreach ($_SERVER as $key => $value) {
      if (strpos($key, 'HTTP_') === 0) {
        $index = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
        $headers[$index] = $value;
        $headers[strtolower($index)] = $value;
      }
    }
    return $headers;
  }

  protected function _validate_input_data($_data, $array_keys){
     foreach($array_keys as $key){
       if(!isset($_data[$key]) || empty($_data[$key])){
         $this->response(array('status' => 0, 'error' => $this->lang->line('response_field_expected') . $key), 419);
       }
     }
  }

}
