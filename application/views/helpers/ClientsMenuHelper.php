<?php
/**
 * Created by PhpStorm.
 * User: ismael trascastro
 * Date: 21/12/13
 * Time: 22:47
 */

namespace views\helpers;

use xen\mvc\helpers\ViewHelper;

class ClientsMenuHelper extends ViewHelper
{

    function __construct($params = array())
    {
        $this->_html = '
            <ul class="list-inline">
                <li><a href="/clients/listOts/">Consultar mis órdenes de transporte</a></li>
            </ul>
        ';
    }
}