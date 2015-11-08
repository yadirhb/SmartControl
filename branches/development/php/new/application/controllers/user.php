<?php defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH . '/libraries/MyREST_Controller.php';

class User extends MyREST_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get()
    {
        if (isset($this->_args['logged_in'])) {
            //get current user data
            if ($this->is_guest)
                $this->response(array('status' => 0, 'error' => $this->lang->line('response_not_authenticated')), 404);
            else {
                $this->response(array(
                    'status' => 1,
                    'user' => $this->user_data,
                    'message' => 'The current user is ' . $this->user_data->name . ' current time: ' . date("d/M/Y H:i:s") . ' y la session expira ' . date("d/M/Y H:i:s", $this->session_data->expire)
                ), 200);
            }
        } elseif (isset($this->_args['logout'])) {
            //logout session
            $this->user_model->logout($this->access_token);
            $this->response(array('status' => 1, 'message' => 'User deslogueado satisfactoriamente'), 200);
        } else {
            //todo ver las validaciones
            //$this->_validate_input_data($this->_get_args, array('uid'));
            $id = $this->_get('uid');
            if (empty($id)) {
                $users = $this->user_model->getUsersWhere();
                if ($users)
                    $this->response(array('status' => 1, 'users' => $users), 200); // 200 being the HTTP response code
                else
                    $this->response(array('status' => 0, 'error' => 'No hay usuarios registrados'), 404);
            } else {
                $user = $this->user_model->getUserWhere(array('id' => $id));
                if ($user)
                    $this->response(array('status' => 1, 'user' => $user), 200); // 200 being the HTTP response code
                else
                    $this->response(array('status' => 0, 'error' => 'No existe el usuario con ese id'), 404);
            }
        }
    }

    public function post()
    {
        $data = $this->_args;

        if (isset($data['login'])) {
            //garbage collection
            $this->user_model->_session_garbage_collector();
            $this->_validate_input_data($this->_args, array('user', 'pass'));
            $access_token = $this->user_model->login($data['user'], $data['pass']);
            if (!$access_token)
                $this->response(array('status' => 0, 'error' => $this->lang->line('response_auth_invalid')), 403);
            else {
                $user = $this->user_model->getUserWhere(array('email' => $data['user']));
                $this->response(array('status' => 1, 'access_token' => $access_token, 'user' => $user), 200);
            }
        } elseif (isset($data['check_login'])) {
            $this->_validate_input_data($this->_args, array('user', 'pass'));
            $access_token = $this->user_model->check_login($data['user'], $data['pass']);
            if (!$access_token)
                $this->response(array('status' => 0, 'error' => $this->lang->line('response_auth_invalid')), 403);
            else {
                $user = $this->user_model->getUserWhere(array('email' => $data['user']));
                $this->response(array('status' => 1, 'checked' => ($user->userrole == 'adm' || $user->userrole == 'mst'), 'user' => $user), 200);
            }
        } elseif (isset($data['block'])) {
            $uid = $data['user_id'];
            //check permission userrole == MASTER
            if (empty($this->user_data->userrole))
                $this->response(array('status' => 0, 'error' => "No tiene permisos para realizar esta accion."), 403);
            $this->user_model->blockUser($uid);
            $this->response(array('status' => 1, 'result' => "Usuario bloqueado satisfactoriamente."), 403);
        } else {
            try {
                $data = $this->_args;
                $data['access_token'] = $this->access_token;

                //datos normales
                $this->_validate_input_data($this->_args, array('old_passwd', 'new_passwd'));

                $update_result = $this->user_model->changePassword($this->user_data->uid, $data['old_passwd'], $data['new_passwd']);
                if (is_int($update_result)) {
                    switch ($update_result) {
                        case 1:
                            throw new Exception('No existe el usuario con ese uid', 409);
                        case 2:
                            throw new Exception('Password anterior invalido', 403);
                    }
                }
                if ($update_result == 3) {
                    $this->response(array(
                        'status' => 1,
                        'result' => 'Contraseña cambiada satisfactoriamente.',
                    ), 200); //201 devolver el user
                } else
                    throw new Exception('Error al actualizar el usuario', 500);
            } catch (Exception $e) {
                // Here the model can throw exceptions like the following:
                // * For invalid input data: new Exception('Invalid request data', 400)
                // * For a conflict when attempting to create, like a resubmit: new Exception('Widget already exists', 409)
                $this->response(array('status' => 0, 'error' => $e->getMessage()), $e->getCode());
            }
        }
    }

}