<?php if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class user_admin extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper('url');

        $logged = $this->session->userdata('logged_in');
        if (!$logged) {
            header("Location: " . base_url() . "api.php/auth");
            die("No esta autenticado");
        }
        $this->load->helper('form');
        $this->load->model('user_model');
    }

    public function index() {
        $users = $this->user_model->getUsersWhere();
        //die('<pre>'.print_r($users, TRUE));
        $this->load->view('user_list', array('users' => $users));
    }

    public function eliminar($uid) {
        $user = $this->user_model->getUserWhere(array('uid' => $uid));

        $this->user_model->removeUser($uid);
        @$path = str_replace(base_url(), "./", $user->data['url']);
        @unlink($path);

        $this->index();
    }
}