<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PhotoModel
 *
 * @author Alejandro Jurado
 */

namespace models;

class PhotoModel {

    private $_id;
    private $_plate;
    private $_date;
    private $_path;
    private $_author;
    private $_downloaded;

    public function __construct($_id, $_plate, $_date, $_path, $_author, $_downloaded = true) {
        $this->_id = $_id;
        $this->_plate = $_plate;
        $this->_date = $_date;
        $this->_path = $_path;
        $this->_author = $_author;
        $this->_downloaded = $_downloaded;
    }

    public function getId() {
        return $this->_id;
    }

    public function getPlate() {
        return $this->_plate;
    }

    public function getDate() {
        return $this->_date;
    }
    
    public function getPath() {
        return $this->_path;
    }

    public function getAuthor() {
        return $this->_author;
    }

    public function getDownloaded() {
        return $this->_downloaded;
    }

    public function setId($_id) {
        $this->_id = $_id;
    }

    public function setPlate($_plate) {
        $this->_plate = $_plate;
    }

    public function setDate($_date) {
        $this->_date = $_date;
    }
    
    public function setPath($_path) {
        $this->_path = $_path;
    }

    public function setAuthor($_author) {
        $this->_author = $_author;
    }

    public function setDownloaded($_downloaded) {
        $this->_downloaded = $_downloaded;
    }

}
