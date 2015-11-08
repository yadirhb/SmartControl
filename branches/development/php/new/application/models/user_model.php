<?php defined('BASEPATH') OR exit('No direct script access allowed');

class user_model extends CI_Model
{
    private $TB_NAME = "access";

    public function __construct()
    {
        parent::__construct();

        $this->load->library('encrypt');
        $this->load->database();
        $this->load->helper('string');
        $this->load->helper('security');
    }

    /**
     * Login function
     * @param $user
     * @param $pass
     * @return bool|string
     */
    public function login($user, $pass)
    {
        $test_user = $this->db->get_where('members', array('email' => $user));
        $test_user = reset($test_user->result()); //user object
        if (empty($test_user)) {
            return FALSE;
        }
        //die(print_r($test_user));
        $hashed_pass = $this->_generateUserPassword($pass, $test_user->salt);
        if ($hashed_pass != $test_user->password) {
            return FALSE;
        }

        //check user status, in case of the user is blocked deny the login
        //if (isset($test_user->status) && $test_user->status == 1){
        //  return FALSE;
        //}

        session_start();
        $sid = session_id();

        $test_session = $this->db->get_where('sessions', array('sid' => $sid));
        $test_session = reset($test_session->result());

        if (!$test_session) {
            //registrar la session y devolver el token
            $salt = random_string('alnum', 5);
            //generar el token
            $str = $test_user->id . $sid . $salt . time();
            $access_token = do_hash($str);

            $_data = array(
                'sid' => $sid,
                'uid' => $test_user->id,
                'host' => $_SERVER['HTTP_HOST'],
                'timestamp' => time(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                'salt' => $salt,
                'expire' => strtotime("+" . SESSION_EXPIRE_TIME . " second"),
                'access_token' => $access_token,
            );
            $this->db->insert('sessions', $_data);
            $this->db->insert('login_attempts', array('time' => time(), 'user_id' => $test_user->id));
            return $access_token;
        } else {
            //ver si esta la sesion creada actualizar los datos, retornar el token nuevo
            $salt = random_string('alnum', 5);
            //generar el token
            $str = $test_user->id . $sid . $salt . time();
            $access_token = do_hash($str);

            $_data = array(
                'uid' => $test_user->id,
                'host' => $_SERVER['HTTP_HOST'],
                'timestamp' => time(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                'expire' => strtotime("+" . SESSION_EXPIRE_TIME . " second"),
                'access_token' => $access_token,
            );
            $this->db->where('sid', $sid);
            $this->db->update('sessions', $_data);
            $this->db->insert('login_attempts', array('time' => time(), 'user_id' => $test_user->id));
            return $access_token;
        }

        /*
        // set timeout period in seconds
        $inactive = 600;

        // check to see if $_SESSION['timeout'] is set
        if (isset($_SESSION['timeout'])) {
          $session_life = time() - $_SESSION['timeout'];
          if ($session_life > $inactive) {
            session_destroy();
          }
        }
        $_SESSION['timeout'] = time(); */
    }

    public function check_login($user, $pass)
    {
        $test_user = $this->db->get_where('members', array('email' => $user));
        $test_user = reset($test_user->result()); //user object
        if (empty($test_user)) {
            return FALSE;
        }
        //die(print_r($test_user));
        $hashed_pass = $this->_generateUserPassword($pass, $test_user->salt);
        if ($hashed_pass != $test_user->password) {
            return FALSE;
        }

        return TRUE;
    }

    /**
     * Perform logout to a token
     * @param $access_token
     */
    public function logout($access_token)
    {
        session_start();
        session_destroy();
        $this->db->delete('sessions', array('access_token' => $access_token));
        $this->_session_garbage_collector();
    }

    public function _session_garbage_collector()
    {
        //eliminar las sesiones que sobran
        $this->db->query("DELETE FROM sessions WHERE expire < '" . time() . "'");
    }

    public function _validate_access_token($access_token)
    {
        $test_session = $this->db->get_where('sessions', array('access_token' => $access_token));
        $test_session = reset($test_session->result());
        if (!$test_session) {
            return FALSE;
        }
        $expire_date = $test_session->expire;
        if ($expire_date < time()) {
            return FALSE;
        }
        //si es valido alargo su vida util
        $_data = array(
            'timestamp' => time(),
            'expire' => strtotime("+" . SESSION_EXPIRE_TIME . " second"),
        );
        $this->db->where('sid', $test_session->sid);
        $this->db->update('sessions', $_data);
        //actualizar la fecha de acceso del usuario
        //$this->db->update('user_accounts', array('access' => time()), array('uid' => $test_session->uid));

        return $test_session;
    }

    public function getUserWhere($where)
    {
        $test_user = $this->getUsersWhere($where);
        return reset($test_user);
    }

    public function getUsersWhere($where = array())
    {
        $test_users = $this->db->get_where('members', $where);
        $result_users = $test_users->result();
        if (count($result_users) == 0) {
            return array();
        }
        foreach ($result_users as $key => $user) {
            unset($result_users[$key]->password);
            unset($result_users[$key]->salt);
            $result_users[$key]->mail = $result_users[$key]->email;
            unset($result_users[$key]->email);
            $result_users[$key]->name = $result_users[$key]->username;
            unset($result_users[$key]->username);
            $result_users[$key]->uid = $result_users[$key]->id;
            unset($result_users[$key]->id);
            //$result_users[$key]->building = sprintf('%04d', $result_users[$key]->building);
        }
        return $result_users;
    }

    public function insertAccount($data)
    {
        $test_user = $this->getUserWhere(array('username' => $data['user']));
        if ($test_user) {
            return 1;
        }

        $test_user = $this->getUserWhere(array('email' => $data['email']));
        if ($test_user) {
            return 2;
        }
        //validate user name and email
        //
        $salt = random_string('alnum', 128);
        $hashed_pass = $this->_generateUserPassword($data['pass'], $salt);
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
            } else {
                return 3;
            }
        } catch (Exception $error) {
            return 3;
        }
    }

    public function updateAccount($data)
    {
        $test_user = $this->getUserWhere(array('id' => $data['uid']));
        if (empty($test_user)) {
            return 1;
        }

        $session_data = (array)$this->_validate_access_token($data['access_token']);

        if ($session_data['uid'] != $test_user->uid) {
            return 2;
        }

        $test_user = $this->getUserWhere(array('name' => $data['user']));
        if ($test_user && $test_user->uid != $session_data['uid']) {
            return 3;
        }

        $test_user = $this->getUserWhere(array('mail' => $data['email']));
        if ($test_user && $test_user->uid != $session_data['uid']) {
            return 4;
        }

        //validate user name and email
        $salt = random_string('alnum', 128);
        $hashed_pass = $this->_generateUserPassword($data['pass'], $salt);
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
            } else {
                return 5;
            }
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

    function blockUser($uid)
    {
        //add field to represent the state of the user and check this field in login function
        //1 = blocked, 0 = unblocked
        //$this->db->update('members', array('status' => 1), array('id' => $uid));
    }

    function unblockUser($uid)
    {
        //1 = blocked, 0 = unblocked
        //$this->db->update('members', array('status' => 0), array('id' => $uid));
    }

    function changePassword($uid, $old_plain_passwd, $new_plain_passwd)
    {
        $test_user = $this->db->get_where('members', array('id' => $uid));
        $test_user = reset($test_user->result());
        if (empty($test_user)) {
            return 1;
        }

        $old_hashed_passwd = $this->_generateUserPassword($old_plain_passwd, $test_user->salt);
        if ($old_hashed_passwd != $test_user->password) {
            return 2;
        }

        $new_hashed_password = $this->_generateUserPassword($new_plain_passwd, $test_user->salt);
        $this->db->update('members', array('password' => $new_hashed_password), array('id' => $test_user->id));
        return 3;
    }

    function _generateUserPassword($plainPassword, $salt = NULL)
    {
        $password = hash('sha512', $plainPassword);
        if ($salt == NULL) {
            $salt = random_string('alnum', 128);
        }
        //$hashed_pass = $this->encrypt->sha1($plainPassword . $salt);
        $hashed_pass = hash('sha512', $password . $salt);
        return $hashed_pass;
    }
}