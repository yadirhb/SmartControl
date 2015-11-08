<?php if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Client extends CI_Controller {
  var $http_response_header;

  function __construct() {
    parent::__construct();
    $this->load->library('session');
    $this->load->helper('url');

    $logged = $this->session->userdata('logged_in');
    if(!$logged){
      header("Location: " . base_url() . "api.php/auth");
      die("No esta autenticado");
    }
  }

  function index() {
    session_start();
    $this->load->helper('url');
    $this->load->helper('form');

    $options = array(
      'autenticar' => 'Autenticar usuario',
      'get_intentos' => 'Obtener intentos en las pruebas',
    );

    $service_data = array(
      'autenticar' => array('method' => 'POST', 'url' => site_url('user/login')),
      'get_intentos' => array('method' => 'GET', 'url' => site_url('endpoint')),
    );

    $form_fields = array(
      'autenticar' => array( 'access_token','user','pass' ),
      'get_intentos' => array('access_token'),
    );

    $data['options'] = $options;
    $data['service_data'] = '';
    $data['form_fields'] = '';
    $data['result_test'] = '';

    if($this->input->post('get_fields_method')) {
      $_SESSION['method'] = $this->input->post('method');
    }

    if(isset($_SESSION['method'])){
      $data['service_data'] = $service_data[$_SESSION['method']];
      $data['form_fields'] = $form_fields[$_SESSION['method']];
    }

    if ($this->input->post('get_result_method')) {
      $operation = $_SESSION['method'];
      $request_vars = array();
      foreach($form_fields[$operation] as $field){
        $request_vars[$field] = $this->input->post($field);
      }
      $method = $service_data[$operation]['method'];
      $url = $service_data[$operation]['url'];
      if($_SESSION['method'] == 'obtener_usuario')
        $url .= $this->input->post('uid');
      $request = new RestRequest($url, $method, $request_vars);
      $request->execute();
      $json_data = $request->getResponseBody();
      $json_pretty = $json_data;
      $result = array(
        'responseBody' => $json_pretty,
      );
      $data['result_test'] = $result;
    }

    $this->load->view('client', $data);
  }
}

class RestRequest {
  protected $url;
  protected $verb;
  protected $requestBody;
  protected $requestLength;
  protected $username;
  protected $password;
  protected $acceptType;
  protected $responseBody;
  protected $responseInfo;

  public function __construct($url = NULL, $verb = 'GET', $requestBody = NULL) {
    $this->url = $url;
    $this->verb = $verb;
    $this->requestBody = $requestBody;
    $this->requestLength = 0;
    $this->username = NULL;
    $this->password = NULL;
    $this->acceptType = 'application/json';
    $this->responseBody = NULL;
    $this->responseInfo = NULL;

    if ($this->requestBody !== NULL) {
      $this->buildPostBody();
    }
  }

  public function flush() {
    $this->requestBody = NULL;
    $this->requestLength = 0;
    $this->verb = 'GET';
    $this->responseBody = NULL;
    $this->responseInfo = NULL;
  }

  public function execute() {
    $ch = curl_init();
    $this->setAuth($ch);

    try {
      switch (strtoupper($this->verb)) {
        case 'GET':
          $this->executeGet($ch);
          break;
        case 'POST':
          $this->executePost($ch);
          break;
        case 'PUT':
          $this->executePut($ch);
          break;
        case 'DELETE':
          $this->executeDelete($ch);
          break;
        default:
          throw new InvalidArgumentException('Current verb (' . $this->verb . ') is an invalid REST verb.');
      }
    }
    catch (InvalidArgumentException $e) {
      curl_close($ch);
      throw $e;
    }
    catch (Exception $e) {
      curl_close($ch);
      throw $e;
    }

  }

  public function buildPostBody($data = NULL) {
    $data = ($data !== NULL) ? $data : $this->requestBody;

    if (!is_array($data)) {
      throw new InvalidArgumentException('Invalid data input for postBody.  Array expected');
    }

    $data = http_build_query($data, '', '&');
    $this->requestBody = $data;
  }

  protected function executeGet($ch) {
    if (!empty($this->requestBody))
      $this->url .= '?' . $this->requestBody;
    $this->doExecute($ch);
  }

  protected function executePost($ch) {
    if (!is_string($this->requestBody)) {
      $this->buildPostBody();
    }

    curl_setopt($ch, CURLOPT_POSTFIELDS, $this->requestBody);
    curl_setopt($ch, CURLOPT_POST, 1);

    $this->doExecute($ch);
  }

  protected function executePut($ch) {
    if (!is_string($this->requestBody)) {
      $this->buildPostBody();
    }

    $this->requestLength = strlen($this->requestBody);

    $fh = fopen('php://memory', 'rw');
    fwrite($fh, $this->requestBody);
    rewind($fh);

    curl_setopt($ch, CURLOPT_INFILE, $fh);
    curl_setopt($ch, CURLOPT_INFILESIZE, $this->requestLength);
    curl_setopt($ch, CURLOPT_PUT, TRUE);

    $this->doExecute($ch);

    fclose($fh);
  }

  protected function executeDelete($ch) {
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    if (!empty($this->requestBody))
      $this->url .= '?' . $this->requestBody;
    $this->doExecute($ch);
  }

  protected function doExecute(&$curlHandle) {
    $this->setCurlOpts($curlHandle);
    $this->responseBody = curl_exec($curlHandle);
    $this->responseInfo = curl_getinfo($curlHandle);

    curl_close($curlHandle);
  }

  protected function setCurlOpts(&$curlHandle) {
    curl_setopt($curlHandle, CURLOPT_TIMEOUT, 10);
    curl_setopt($curlHandle, CURLOPT_URL, $this->url);
    curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array('Accept: ' . $this->acceptType));
  }

  protected function setAuth(&$curlHandle) {
    if ($this->username !== NULL && $this->password !== NULL) {
      curl_setopt($curlHandle, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
      curl_setopt($curlHandle, CURLOPT_USERPWD, $this->username . ':' . $this->password);
    }
  }

  public function getAcceptType() {
    return $this->acceptType;
  }

  public function setAcceptType($acceptType) {
    $this->acceptType = $acceptType;
  }

  public function getPassword() {
    return $this->password;
  }

  public function setPassword($password) {
    $this->password = $password;
  }

  public function getResponseBody() {
    return $this->responseBody;
  }

  public function getResponseInfo() {
    return $this->responseInfo;
  }

  public function getUrl() {
    return $this->url;
  }

  public function setUrl($url) {
    $this->url = $url;
  }

  public function getUsername() {
    return $this->username;
  }

  public function setUsername($username) {
    $this->username = $username;
  }

  public function getVerb() {
    return $this->verb;
  }

  public function setVerb($verb) {
    $this->verb = $verb;
  }
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */