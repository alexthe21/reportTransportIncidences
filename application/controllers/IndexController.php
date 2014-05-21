<?php
/**
 * Created by PhpStorm.
 * User: ismael trascastro
 * Date: 12/12/13
 * Time: 16:45
 */

namespace controllers;

use models\UserModel;
use xen\application\Request;
use xen\application\Router;
use xen\eventSystem\Event;
use xen\eventSystem\EventSystem;
use xen\mvc\Controller;
use xen\mvc\view\Phtml;

class IndexController extends Controller
{
    public function init()
    {

//        $event = new Event('Bootstrap_Load', array('msg' => 'event from Bootstrap'));
//        $eventSystem = $this->_bootstrap->getResource('EventSystem');
//        $eventSystem->raiseEvent($event);

    }

    public function indexAction()
    {
        $this->_layout->title           = 'otfoto';
        $this->_layout->description     = 'Sube las fotos de las incidencias';

        $partial = new Phtml('application/views/partials/importjQuery.phtml');
        $this->_view->addPartial('importjQuery', $partial);

        return $this->render();
    }
} 