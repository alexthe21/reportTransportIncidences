<?php
/**
 * Created by PhpStorm.
 * User: ismael trascastro
 * Date: 21/12/13
 * Time: 22:44
 */

namespace controllers;

use models\UsersModel;
use xen\mvc\Controller;
use xen\mvc\view\Phtml;

class AdminController extends Controller
{
    private $_adminModel;
    private $_clientModel;
    
    public function init()
    {
        if (!$this->_session->get('admin')) {

            $this->_redirect('login', 'adminLogin');
        }
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
        $this->_layout->title           = 'Admin Controller';
        $this->_layout->description     = 'Controller for app management';

        return $this->render();
    }

    public function addClientAction()
    {
        $this->_layout->title           = 'Crear cliente';
        $this->_layout->description     = 'Insertar nuevo cliente';

        return $this->render();
    }

    public function addClientDoAction()
    {
        $this->_clientModel->add($this->_request->post('id'),
                $this->_request->post('nombre'), 
                $this->_request->post('email'));
        return $this->_forward('listClients');
    }

    public function removeAction()
    {
        $this->_clientModel->remove($this->_params['id']);
        return $this->_redirect('admin', 'listClients');
    }

    public function updateAction()
    {
        $cliente = $this->_clientModel->getClientById($this->_params['id']);

        $this->_layout->title           = 'Actualiza un cliente';
        $this->_layout->description     = 'Modifica los datos de un cliente';

        $this->_view->cliente              = $cliente;

        return $this->render();
    }

    public function updateDoAction()
    {
        $this->_clientModel->update(
            $this->_request->post('id'),
            $this->_request->post('nombre'),
            $this->_request->post('email'),
            $this->_request->post('password')
        );

        return $this->_redirect('admin', 'listClients');
    }

    public function listClientsAction()
    {
        $clients = $this->_clientModel->all();

        $this->_layout->title           = 'Listar clientes';
        $this->_layout->description     = 'Muestra todos los clientes';

        $this->_view->clients = $clients;

        return $this->render();
    }
    
}