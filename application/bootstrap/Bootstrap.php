<?php
/**
 * Created by PhpStorm.
 * User: ismael trascastro
 * Date: 21/12/13
 * Time: 23:17
 */

namespace bootstrap;

use xen\application\Application;
use xen\application\bootstrap\BootstrapBase;
use xen\mvc\view\Phtml;

class Bootstrap extends BootstrapBase
{
//    protected function _initLayoutPath()
//    {
//        return str_replace('/', DIRECTORY_SEPARATOR, 'application/layouts/default');
//    }

    /*
     * This resource is not needed anymore so we do not store it in the Bootstrap container (return null)
     * In fact this is not a resource, only do actions at the beginning of the new request
     */
    protected function _initEnvironment()
    {
        if ($this->_appEnv == Application::DEVELOPMENT || $this->_appEnv == Application::TEST) {

            error_reporting(E_ALL | E_STRICT);
            ini_set('display_errors', 'on');

        } else if ($this->_appEnv == Application::PRODUCTION) {

            error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
            ini_set('display_errors', 'off');
        }

        $timeZone = (string) $this->getResource('Config')->timezone;

        if (empty($timeZone)) {

            $timeZone = 'Europe/Madrid';
        }

        date_default_timezone_set($timeZone);

        return null;
    }

    /**
     *
     * @return Phtml
     *
     */
    protected function _initLayout()
    {
        $layout = $this->getResource('Layout');
        $config = $this->getResource('Config');

        $header = new Phtml($this->getResource('LayoutPath') . DIRECTORY_SEPARATOR . 'header.phtml');
        $header->charset = (string) $config->charset;

        if (isset($_SESSION['admin'])) {
            $admin = $_SESSION['admin'];
            $layout->loggedUser = 'LogOut(' . $admin->getName() . ')';

        } else if(isset($_SESSION['client'])) {
            $client = $_SESSION['client'];
            $layout->loggedUser = 'LogOut(' . $client->getEmail() . ')';
        } else {
             $layout->loggedUser = '';
        }

        $partials   = array(

            'header' => $header,
            'footer' => new Phtml($this->getResource('LayoutPath') . DIRECTORY_SEPARATOR . 'footer.phtml'),
        );

        $layout->addPartials($partials);

        return $layout;
    }
}
