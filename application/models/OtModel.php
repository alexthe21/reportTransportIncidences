<?php
/**
 * Description of OtModel
 *
 * @author Alejandro Jurado
 */
namespace models;

class OtModel {

    private $_fechaHora;
    private $_matricula;
    private $_idCliente;
    private $_idOperario;
    private $_incidencias = array();
    private $_fotos;

    public function __construct($_fechaHora, $_matricula, $_idCliente, 
            $_idOperario, $_incidencias, $_fotos) {
        $this->_fechaHora = $_fechaHora;
        $this->_matricula = $_matricula;
        $this->_idCliente = $_idCliente;
        $this->_idOperario = $_idOperario;
        $this->_incidencias = $_incidencias;
        $this->_fotos = $_fotos;
    }

    public function getFechaHora() {
        return $this->_fechaHora;
    }

    public function getMatricula() {
        return $this->_matricula;
    }

    public function getIdCliente() {
        return $this->_idCliente;
    }

    public function getIdOperario() {
        return $this->_idOperario;
    }

    public function getIncidencias() {
        return $this->_incidencias;
    }

    public function getFotos() {
        return $this->_fotos;
    }

    public function setFechaHora($_fechaHora) {
        $this->_fechaHora = $_fechaHora;
    }

    public function setMatricula($_matricula) {
        $this->_matricula = $_matricula;
    }

    public function setIdCliente($_idCliente) {
        $this->_idCliente = $_idCliente;
    }

    public function setIdOperario($_idOperario) {
        $this->_idOperario = $_idOperario;
    }

    public function setIncidencias($_incidencias) {
        $this->_incidencias = $_incidencias;
    }
    
    public function setFotos($fotos) {
        $this->_fotos = $fotos;
    }

}
