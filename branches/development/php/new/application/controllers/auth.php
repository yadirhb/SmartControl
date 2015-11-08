<?php if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class auth extends CI_Controller {

  function __construct() {
    parent::__construct();
    $this->load->library('session');
    $this->load->helper('url');
  }

  function index() {
    if($this->input->post('submit')) {
      $user = $this->input->post('user');
      $pass = $this->input->post('password');
      if($user == "smart_control" && $pass == "Sm@rtC0ntr0l") {
        $this->session->set_userdata('logged_in', 1);
        header("Location: ".base_url()."api.php/backend");
        die("Ya esta autenticado");
      }
    }
    $this->session->unset_userdata('logged_in');
    $this->load->view('login');
  }
}