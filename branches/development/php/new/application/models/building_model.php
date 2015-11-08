<?php defined('BASEPATH') OR exit('No direct script access allowed');

class building_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->load->database();
    }

    public function getDataBuildings()
    {

    }

    public function getDataBuildingByID($bld_id)
    {

    }

    public function insertBuilding($data)
    {
        $test_user = $this->getUserWhere(array('username' => $data['user']));
        if ($test_user)
            return 1;

        $test_user = $this->getUserWhere(array('email' => $data['email']));
        if ($test_user)
            return 2;
        //validate user name and email
        //
        $salt = random_string('alnum', 128);
        $hashed_pass = $this->encrypt->sha1($data['pass'] . $salt);
        $_data = array(
            'username' => $data['user'],
            'password' => $hashed_pass,
            'salt' => $salt,
            'email' => $data['email'],
        );
        try {
            $this->db->insert('members', $_data);
            if ($this->db->affected_rows() == 1) {
                return $this->getUserWhere(array('username' => $data['user']));
            } else
                return 3;
        } catch (Exception $error) {
            return 3;
        }
    }

    public function updateAccount($data)
    {
        $test_user = $this->getUserWhere(array('id' => $data['uid']));
        if (empty($test_user))
            return 1;

        $session_data = (array)$this->_validate_access_token($data['access_token']);

        if ($session_data['uid'] != $test_user->uid)
            return 2;

        $test_user = $this->getUserWhere(array('name' => $data['user']));
        if ($test_user && $test_user->uid != $session_data['uid'])
            return 3;

        $test_user = $this->getUserWhere(array('mail' => $data['email']));
        if ($test_user && $test_user->uid != $session_data['uid'])
            return 4;

        //validate user name and email
        $salt = random_string('alnum', 5);
        $hashed_pass = $this->encrypt->sha1($data['pass'] . $salt);
        $_data = array(
            'username' => $data['user'],
            'password' => $hashed_pass,
            'salt' => $salt,
            'email' => $data['email']
        );
        $update_str = array();
        foreach ($_data as $key => $new_value) {
            $update_str[] = $key . " = '" . $new_value . "'";
        }
        $update_str = implode(", ", $update_str);
        try {
            $this->db->update('members', $_data, array('id' => $data['uid']));
            if ($this->db->affected_rows() == 1) {
                return $this->getUserWhere(array('username' => $data['user']));
            } else
                return 5;
        } catch (Exception $error) {
            return 5;
        }
    }

    function removeUser($uid)
    {
        $this->db->delete('members', array('id' => $uid));
        $this->db->delete('sessions', array('uid' => $uid));
        $this->db->delete('login_attempts', array('id' => $uid));
    }
}