<?php if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Backend extends CI_Controller {

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

    function index()	{
        $this->load->helper('url');
        $this->load->view('backend');
    }
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */