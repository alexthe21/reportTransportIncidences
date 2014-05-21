<?php

/**
 * Created by PhpStorm.
 * User: ismael trascastro
 * Date: 21/12/13
 * Time: 19:03
 */

namespace models;

class AdminModel {

    private $_id;
    private $_name;
    private $_password;

    public function __construct($_id, $_name, $_password) {
        $this->_id = $_id;
        $this->_name = $_name;
        $this->_password = $_password;
    }

    /**
     * @param mixed $password
     */
    public function getName() {
        return $this->_name;
    }

    public function setName($_name) {
        $this->_name = $_name;
    }

    public function setPassword($password) {
        $this->_password = $password;
    }

    /**
     * @return mixed
     */
    public function getPassword() {
        return $this->_password;
    }

    /**
     * @param mixed $id
     */
    public function setId($id) {
        $this->_id = $id;
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->_id;
    }

}
