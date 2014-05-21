<?php

/**
 * Created by PhpStorm.
 * User: ismael trascastro
 * Date: 21/12/13
 * Time: 22:44
 */

namespace controllers;

/* use models\UsersModel; */

use xen\mvc\Controller;
use xen\mvc\view\Phtml;

class ClientsController extends Controller {

    private $_clientModel;

    public function init() {
        if (!$this->_session->get('client')) {

            $this->_redirect('login', 'index');
        }
    }

    public function setClientModel($clientModel) {
        $this->_clientModel = $clientModel;
    }

    public function indexAction() {
        if ($this->_session->get('client')) {
            $this->_layout->title = 'Zona Clientes';
            $this->_layout->description = 'Zona de gestión del cliente';
            $partial = new Phtml('application/views/partials/importjQuery.phtml');
            $this->_view->addPartial('importjQuery', $partial);
            return $this->render();
        } else {
            $this->_redirect('login', 'index');
        }
    }

    /* public function addAction() {
      $this->_layout->title = 'Add a new user';
      $this->_layout->description = 'Insert a new user';

      return $this->render();
      }

      public function addDoAction() {
      $this->_model->add($this->_request->post('email'), $this->_request->post('password'));
      return $this->_forward('list');
      } */

    /* public function removeAction() {
      $this->_model->remove($this->_params['id']);
      return $this->_redirect('users', 'list');
      } */

    /* public function updateAction() {
      $user = $this->_model->getUserById($this->_params['id']);

      $this->_layout->title = 'Update an user';
      $this->_layout->description = 'Change user';

      $this->_view->user = $user;

      return $this->render();
      }

      public function updateDoAction() {
      $this->_model->update(
      $this->_request->post('id'), $this->_request->post('email'), $this->_request->post('password')
      );

      return $this->_redirect('users', 'list');
      } */

    /* public function listAction() {
      $users = $this->_model->all();

      $this->_layout->title = 'User List';
      $this->_layout->description = 'Show all users';

      $this->_view->users = $users;

      return $this->render();
      } */

    public function listOtsAction() {
        $ots = $this->_clientModel->listOts($this->_session->get('client'));
        $this->_layout->title = 'Lista de OT';
        $this->_layout->description = 'Muestra todas sus órdenes de transporte';
        $partial1 = new Phtml('application/views/partials/importjQuery.phtml');
        $this->_view->addPartial('importjQuery', $partial1);
        $partial2 = new Phtml('application/views/partials/importTableSorterListOts.phtml');
        $this->_view->addPartial('importTableSorterListOts', $partial2);
        $this->_view->ots = $ots;

        return $this->render();
    }

    //TODO: fix this action
    public function getClientIdByOtIdAction($id) {
        $clientId = $this->_clientModel->getClientIdByOtId();
        if ($clientId != $this->_session->get('client')->getId()) {
            $this->_redirect('client', 'index');
        }
    }

    public function describeOtAction() {
        $ot = $this->_clientModel->getOtByPlateAndDate($this->_params['plate'], $this->_params['date'], $this->_session->get('client')->getId());
        $this->_layout->title = 'Detalle OT';
        $this->_layout->description = 'Muestra la OT al detalle';
        $photos = $ot->getFotos();
        $photos = $this->_clientModel->downloadPhotos($ot->getIdCliente(), $photos);
        $partial1 = new Phtml('application/views/partials/importjQuery.phtml');
        $this->_view->addPartial('importjQuery', $partial1);
        $partial2 = new Phtml('application/views/partials/importTableSorterListIncidences.phtml');
        $this->_view->addPartial('importTableSorterListIncidences', $partial2);
        $partial3 = new Phtml('application/views/partials/importCarouselNivo.phtml');
        $this->_view->addPartial('importCarouselNivo', $partial3);
        $this->_view->ot = $ot;
        $this->_view->fotos = $photos;
        $this->_view->cliente = $this->_session->get('client');

        return $this->render();
    }

    public function reportIncidenceAction() {
        $ot = $this->_clientModel->getOtByPlateAndDate($this->_params['plate'], $this->_params['date'], $this->_session->get('client')->getId());
        $this->_layout->title = 'Informe incidencias';
        $this->_layout->description = 'Informe de posibles incidencias';
        $this->_view->ot = $ot;
        $partial = new Phtml('application/views/partials/importUpload.phtml');
        $this->_view->addPartial('importUpload', $partial);
        $partial2 = new Phtml('application/views/partials/importTableSorterListOts.phtml');
            $this->_view->addPartial('importTableSorterListOts', $partial2);
        return $this->render();
    }

    public function reportIncidenceDoAction() {
        $plate = $this->_request->post('plate');
        $date = $this->_request->post('date');
        $clientId = $this->_session->get('client')->getId();
        $incidence = filter_var($this->_request->post('incidence'), FILTER_SANITIZE_STRING);
        $photos = $this->_request->files('pictures');
        $this->_clientModel->addIncidence($idOt, $clientId, $incidence, $photos);
        $this->_redirect('clients', 'describeOt/id/' . $idOt);
    }

    public function uploadPhotoAjaxAction() {
        $this->_response->setHeaders('Content-Type: text/html');
        echo $this->_clientModel->uploadPhotoAjax(
                $this->_request->post('image'), $this->_request->post('imageName'), $this->_request->post('date'), $this->_request->post('plate'), $this->_session->get('client'));
        exit;
    }

    public function uploadIncidenceAjaxAction() {
        $this->_response->setHeaders('Content-Type: text/html');
        $this->_response->setHeaders('Access-Control-Allow-Origin: *');
        $this->_response->setHeaders('Access-Control-Max-Age: 3628800');
        $this->_response->setHeaders('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
        echo $this->_clientModel->uploadIncidenceAjax(
                $this->_request->get('plate'), $this->_request->get('date'), $this->_request->get('message'), $this->_session->get('client'));
        exit;
    }

}
