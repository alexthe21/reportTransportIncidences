<?php
/**
 * Created by PhpStorm.
 * User: ismael trascastro
 * Date: 21/12/13
 * Time: 19:01
 */

namespace controllers;

use models\UsersModel;
use xen\mvc\Controller;

class LoginController extends Controller
{
    private $_adminModel;
    private $_clientModel;
    
    public function init()
    {

    }

    public function setAdminModel($adminModel)
    {
        $this->_adminModel = $adminModel;
    } 
    
    public function setClientModel($clientModel)
    {
        $this->_clientModel = $clientModel;
    } 
    
    public function indexAction()
    {
        $this->_layout->title           = 'Formulario de bienvenida';
        $this->_layout->description     = 'Introduzca sus credenciales';

        $this->render();
    }

    public function loginDoAction()
    {
        $client = $this->_clientModel->login($this->_request->post('email'), 
                $this->_request->post('password'));

        if ($client != null) {

            $this->_session->set('client', $client);

            $this->_redirect('clients','index');

        } else {

            $this->_redirect('login', 'index');

        }
    }
    
    public function adminLoginAction(){
        $this->_layout->title           = 'Formulario de administradores';
        $this->_layout->description     = 'Introduzca sus credenciales';

        $this->render();
    }
    
    public function adminLoginDoAction()
    {
        $admin = $this->_adminModel->login($this->_request->post('nombre'), 
                $this->_request->post('password'));

        if ($admin != null) {

            $this->_session->set('admin', $admin);

            $this->_redirect('admin','index');

        } else {

            $this->_redirect('login', 'adminLogin');

        }
    }
    
    public function logOutAction()
    {
        if($this->_session->get('client')){
            $this->_session->delete('client');
        } else if($this->_session->get('admin')){
            $this->_session->delete('admin');
        }
        $this->_session->destroy();
        $this->_redirect('index','index');
    }

} 