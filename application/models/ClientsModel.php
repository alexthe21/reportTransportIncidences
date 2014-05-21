<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ClientsModel
 *
 * @author Alejandro Jurado
 */

namespace models;

use xen\db\Adapter;

class ClientsModel {

    private $_db;
    private $_mssqlDb;

    public function __construct() {
        $server = 'Write the IP in here';
        $this->_mssqlDb = mssql_connect($server, 'user', 'password');

        if (!$this->_mssqlDb) {
            throw new \Exception('Something went wrong while attempting to connect '
            . 'MS SQL Database');
        } else {
            mssql_select_db('databasename', $this->_mssqlDb);
        }
        ini_set('mssql.charset', 'utf-8');
    }

    public function setDb($_db) {
        $this->_db = $_db;
    }

    public function add($idCliente, $nombre, $email) {
        $query = "SELECT NIF"
                . " FROM gen_Clientes"
                . " WHERE ID_Cliente = '$idCliente'";
        $stmt = mssql_query($query);
        $nif = mssql_fetch_assoc($stmt);
        $nombre = mb_strtoupper($nombre, 'utf-8');
        $email = mb_strtoupper($email, 'utf-8');
        $password = $nif['NIF'];
        $password = str_split($password);
        for ($i = 0; $i < sizeof($password); $i++) {
            if (preg_match('/[A-Za-z]/', $password[$i])) {
                $password[$i] = 0;
            }
        }
        $password = implode('', $password);
        $password = sqrt($password);
        $password = '' . $password;
        $password = str_replace('.', '0', $password);
        $password = substr($password, 0, 6);
        $sql = "INSERT INTO clientes (id, nombre, email, password) "
                . "VALUES (:id, :nombre, :email, :password)";
        $query = $this->_db->prepare($sql);
        $query->bindParam(':id', $idCliente);
        $query->bindParam(':nombre', $nombre);
        $query->bindParam(':email', $email);
        $query->bindParam(':password', $password);
        $query->execute();
    }

    public function all() {
        $sql = "SELECT * FROM clientes";
        $query = $this->_db->prepare($sql);
        $query->execute();

        $clientes = array();

        while ($row = $query->fetch(Adapter::FETCH_OBJ)) {
            $clientes[] = new ClientModel($row->id, $row->nombre, $row->email, $row->password);
        }

        return $clientes;
    }

    public function login($email, $password) {
        $email = mb_strtoupper($email, 'utf-8');
        $sql = "SELECT * FROM clientes WHERE email = :email AND password = :password";
        $query = $this->_db->prepare($sql);
        $query->bindParam(':email', $email);
        $query->bindParam(':password', $password);
        $query->execute();

        if ($row = $query->fetch(Adapter::FETCH_ASSOC)) {
            return new ClientModel($row['id'], $row['nombre'], $row['email'], $row['password']);
        }

        return null;
    }

    public function listOts($cliente) {
        $idCliente = $cliente->getId();
        $server = '88.87.207.130';
        $sqlServerConn = mssql_connect($server, 'ro', 'Read0nly');

        if (!$sqlServerConn) {
            throw new Exception('No se pudo conectar a la Base de Datos S4TransERP');
        } else {
            mssql_select_db('S4TransERP', $sqlServerConn);
        }
        ini_set('mssql.charset', 'utf-8');
        $query = "SELECT TOP(30) Codigo_cliente, Fecha_Confirmacion, Contenedor, "
                . "Nombre_cliente, Su_referencia "
                . "FROM alm_Ordenes_transporte "
                . "WHERE Codigo_cliente = $idCliente "
                . "ORDER BY Fecha_Confirmacion desc";
        $stmt = mssql_query($query);
        $ots = array();
        while ($row = mssql_fetch_assoc($stmt)) {
            $date = new \DateTime($row['Fecha_Confirmacion']);
            array_push($ots, new OtModel($date, $row['Contenedor'], $row['Codigo_cliente'], null, null, null));
        }
        return $ots;
    }

    public function getOtByPlateAndDate($plate, $date, $idCliente) {
        $plate = str_split($plate);
        $newPlate = array();
        for ($i = 0; $i < sizeof($plate); $i++) {
            $l = $plate[$i];
            if (!preg_match('/[A-Z]/', $l, $matches)) {
                array_push($newPlate, ' ');
                $finalPlate = array_merge($newPlate, array_slice($plate, $i));
                break;
            } else {
                array_push($newPlate, $matches[0]);
            }
        }
        $plate = implode('', $finalPlate);
        $dateObj = new \DateTime($date);
        $date = $dateObj->format('Y-m-d');
        $server = '88.87.207.130';
        $sqlServerConn = mssql_connect($server, 'ro', 'Read0nly');

        if (!$sqlServerConn) {
            throw new Exception('No se pudo conectar a la Base de Datos S4TransERP');
        } else {
            mssql_select_db('S4TransERP', $sqlServerConn);
        }
        ini_set('mssql.charset', 'utf-8');

        $sql = "SELECT * FROM fotos "
                . "WHERE matricula = :matricula "
                . "AND fecha = :fecha";
        $query = $this->_db->prepare($sql);
        $query->bindParam(':matricula', $plate);
        $query->bindParam(':fecha', $date);
        $query->execute();
        $fotos = array();
        while ($row = $query->fetch(Adapter::FETCH_ASSOC)) {
            $date = new \DateTime($row['fecha']);
            $photo = new PhotoModel($row['id'], $row['matricula'], $date, $row['foto'], $row['id_operario'], $row['id_cliente']);
            array_push($fotos, $photo);
        }
        $dateStr = $date->format('Y-m-d');
        $query = "SELECT TOP(30) Codigo_cliente, Fecha_Confirmacion, Contenedor, "
                . "Su_referencia "
                . "FROM alm_Ordenes_transporte "
                . "WHERE Contenedor = '$plate' AND Fecha_Confirmacion = '$dateStr' "
                . "ORDER BY Fecha_Confirmacion desc";
        $stmt = mssql_query($query);
        if ($row = mssql_fetch_assoc($stmt)) {
            $date = new \DateTime($row['Fecha_Confirmacion']);
            $ot = new OtModel($date, $row['Contenedor'], $row['Codigo_cliente'], null, null, $fotos);
            $sql = "SELECT * FROM incidencias "
                    . "WHERE matricula = :matricula "
                    . "AND fecha = :fecha "
                    . "ORDER BY id desc";
            $query = $this->_db->prepare($sql);
            $query->bindParam(':matricula', $plate);
            $query->bindParam(':fecha', $row['Fecha_Confirmacion']);
            $query->execute();
            $incidencias = array();
            while ($row = $query->fetch(Adapter::FETCH_ASSOC)) {
                $row['mensaje'] = str_replace('&#39;', "'", $row['mensaje']);
                $row['mensaje'] = str_replace('&#34;', '"', $row['mensaje']);
                array_push($incidencias, $row);
            }
            $ot->setIncidencias($incidencias);
            return $ot;
        }
        return null;
    }

    private function uploadFile($cid, $idOt, $idCliente, $hoy, $localFileName, $remoteFileName) {
        $hoyStr = $hoy->format('Y-m-d H:i:s');
        if (ftp_put($cid, $remoteFileName, $localFileName, FTP_BINARY)) {

            $sql = "insert into registros set"
                    . " id_orden_transporte = :idOt,"
                    . " fecha_hora = :hoy,"
                    . " id_cliente = :idCliente";
            $query = $this->_db->prepare($sql);
            $query->bindParam(':idOt', $idOt);
            $query->bindParam(':hoy', $hoyStr);
            $query->bindParam(':idCliente', $idCliente);
            $query->execute();
        }
    }

    private function connectFtp() {
        /* $ftp_server = '88.87.207.130';
          $ftp_user = 'rw';
          $ftp_pass = 'ReadWrite'; */
        $ftp_server = '88.87.207.130';
        $ftp_user = 'tlsa';
        $ftp_pass = 'fitipaldi';
        //Primero creamos un ID de conexión a nuestro servidor
        $cid = ftp_connect($ftp_server);

        $resultado = ftp_login($cid, $ftp_user, $ftp_pass);
        //Comprobamos que se creo el Id de conexión y se pudo hacer el login
        if ($cid && $resultado) {
            return $cid;
        } else {
            throw new \Exception('No se pudo conectar con el servidor huésped de '
            . 'fotografías');
        }
    }

    private function rrmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir") {
                        $this->rrmdir($dir . "/" . $object);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    public function getClientIdByOtId($idOt) {
        $sql = "SELECT id_cliente FROM ordenestransporte WHERE id_orden_transporte = :id";
        $query = $this->_db->prepare($sql);
        $query->bindParam(':id', $idOt);
        $query->execute();
        if ($row = $query->fetch(Adapter::FETCH_ASSOC)) {
            return $row['id_cliente'];
        } else {
            throw new \Exception('No existe la orden de transporte en la base de datos.');
        }
    }

    public function downloadPhotos($idCliente, $photos) {
        $hoy = new \DateTime('now');
        $hoyStr = $hoy->format('dmY_His');
        $contentClientFolder = scandir('public/images/' . $idCliente);
        $dateLimit = $hoy->sub(new \DateInterval('P1D'));
        foreach ($contentClientFolder as $element) {
            if ($element != '.' && $element != '..') {
                $elementDay = substr($element, 0, 2);
                $elementMonth = substr($element, 2, 2);
                $elementYear = substr($element, 4, 4);
                $elementHour = substr($element, 9, 2);
                $elementMinute = substr($element, 11, 2);
                $elementSecond = substr($element, 13, 2);
                $elementDate = new \DateTime($elementYear . '-' . $elementMonth
                        . '-' . $elementDay . ' ' . $elementHour . ':'
                        . $elementMinute . ':' . $elementSecond);
                if ($elementDate < $dateLimit) {
                    $this->rrmdir('public/images/' . $idCliente . '/' . $element);
                }
            }
        }

        $cid = $this->connectFtp();

        $content = scandir('public/images');
        if (!in_array($idCliente, $content)) {
            if (!mkdir('public/images/' . $idCliente)) {
                throw new \Exception('No se ha podido crear la carpeta cliente '
                . 'temporal para la descarga.');
            }
            if (!chmod('public/images/' . $idCliente, 0777)) {
                throw new \Exception('No se han podido modificar los permisos '
                . 'para la carpeta cliente temporal.');
            }
        }

        if (!mkdir('public/images/' . $idCliente . '/' . $hoyStr)) {
            throw new \Exception('No se ha podido crear una carpeta '
            . 'temporal única para la descarga.');
        }
        if (!chmod('public/images/' . $idCliente . '/' . $hoyStr, 0777)) {
            throw new \Exception('No se han podido modificar los permisos '
            . 'para la carpeta única temporal.');
        }

        for ($i = 0; $i < sizeof($photos); $i++) {
            $localFileName = 'public/images/' . $idCliente . '/' . $hoyStr . '/'
                    . $i . '.jpg';
            $photos[$i]->setPath('' . $photos[$i]->getPath());
            $ruta = ftp_pwd($cid);
            $contenido = ftp_nlist($cid, '.');
            if (!ftp_get($cid, $localFileName, $photos[$i]->getPath(), FTP_BINARY)) {
                $photos[$i]->setDownloaded(FALSE);
            } else {
                $photos[$i]->setPath('/images/' . $idCliente . '/' . $hoyStr . '/' . $i . '.jpg');
            }
        }
        ftp_close($cid);
        return $photos;
    }

    public function uploadPhotoAjax($img, $imgName, $date, $plate, $client) {
        $hoy = new \DateTime('now');
        $content = scandir('public/images');
        if (!in_array($client->getId(), $content)) {
            if (!mkdir('public/images/' . $client->getId())) {
                return 1 . 'No se ha podido crear la carpeta cliente '
                        . 'temporal para la descarga.';
            }
            if (!chmod('public/images/' . $client->getId(), 0777)) {
                return 1 . 'No se han podido modificar los permisos '
                        . 'para la carpeta cliente temporal.';
            }
        }
        $content = scandir('public/images/' . $client->getId());
        if (!in_array($date, $content)) {
            if (!mkdir('public/images/' . $client->getId() . '/' . $date)) {
                return 1 . 'No se ha podido crear la carpeta cliente '
                        . 'temporal para la descarga.';
            }
            if (!chmod('public/images/' . $client->getId() . '/' . $date, 0777)) {
                return 1 . 'No se han podido modificar los permisos '
                        . 'para la carpeta cliente temporal.';
            }
        }

        $destination = 'public/images/' . $client->getId() . '/' . $date;
        $dateLimit = $hoy->sub(new \DateInterval('P1D'));
        $contentClientFolder = scandir('public/images/' . $client->getId());
        foreach ($contentClientFolder as $element) {
            if ($element != '.' && $element != '..') {
                $elementDay = substr($element, 0, 2);
                $elementMonth = substr($element, 2, 2);
                $elementYear = substr($element, 4, 4);
                $elementHour = substr($element, 9, 2);
                $elementMinute = substr($element, 11, 2);
                $elementSecond = substr($element, 13, 2);
                $elementDate = new \DateTime($elementYear . '-' . $elementMonth
                        . '-' . $elementDay . ' ' . $elementHour . ':'
                        . $elementMinute . ':' . $elementSecond);
                if ($elementDate < $dateLimit) {
                    $this->rrmdir('public/images/' . $client->getId() . '/' . $element);
                }
            }
        }
        $day = substr($date, 0, 2);
        $day = intval($day);
        $month = substr($date, 2, 2);
        $month = intval($month);
        $year = substr($date, 4, 4);
        $year = intval($year);
        $hour = substr($date, 9, 2);
        $hour = intval($hour);
        $minute = substr($date, 11, 2);
        $minute = intval($minute);
        $second = substr($date, 13, 2);
        $second = intval($second);
        $hoy->setDate($year, $month, $day);
        $hoy->setTime($hour, $minute, $second);
        $hoyStr = $hoy->format('Y-m-d H:i:s');
        $img = str_replace('data:image/jpeg;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);
        $fileName = uniqid() . '.jpg';
        $localFile = 'public/images/' . $client->getId() . '/' . $date . '/' . $fileName;
        $plate = str_replace(' ', '_', $plate);
        $remoteFile = '/' . $date . '/' . $plate . '/' . $fileName;
        $success = file_put_contents($localFile, $data);
        if ($success > 0) {
            $cid = $this->connectFtp();
            $dateStr = './' . $date;
            $content = ftp_nlist($cid, '.');
            if (!in_array($dateStr, $content)) {
                if (!ftp_mkdir($cid, $date)) {
                    return 1 . 'No se ha podido crear la carpeta para '
                            . ' su carga de fotografías para la presente orden de transporte. '
                            . 'Contacte con la empresa proveedora del servicio. '
                            . 'Disculpe las molestias.';
                }
            }
            if (!ftp_chdir($cid, $date)) {
                return 1 . 'Problemas en el cambio de carpeta';
            }

            $content = ftp_nlist($cid, '.');
            $plateStr = './' . $plate;
            if (!in_array($plateStr, $content)) {
                if (!ftp_mkdir($cid, $plate)) {
                    return 1 . 'No se ha podido crear la carpeta para '
                            . ' su carga de fotografías para la presente orden de transporte. '
                            . 'Contacte con la empresa proveedora del servicio. '
                            . 'Disculpe las molestias.';
                }
            }
            $content = ftp_nlist($cid, '.');
            $ruta = ftp_pwd($cid);
            //$file = fopen($localFile, 'a+');
            //copy($data, $localFile);
            $imagick = new \Imagick($localFile);
            $imagick->setCompression(\imagick::COMPRESSION_JPEG);
            $imagick->setCompressionQuality(100);
            $imagick->stripImage();
            $imagick->writeImage($localFile);
            $hoyStr = $hoy->format('Y-m-d');
            $plate = str_replace('_', ' ', $plate);
            $clientId = $client->getId();
            if (ftp_put($cid, $remoteFile, $localFile, FTP_BINARY)) {
                $sql = "INSERT INTO fotos SET matricula = :plate, fecha = :fecha,"
                        . " foto = :foto, id_cliente = :id_cliente, visible = 1";
                $query = $this->_db->prepare($sql);
                $query->bindParam(':plate', $plate);
                $query->bindParam(':fecha', $hoyStr);
                $query->bindParam(':foto', $remoteFile);
                $query->bindParam(':id_cliente', $clientId);
                if (!$query->execute()) {
                    return 1 . 'No se pudo insertar la foto';
                };
                return 0 . $imgName . ' cargada con éxito';
            } else {
                return 1 . 'Error en la transferencia ftp';
            }
        } else {
            return 1 . 'Error en la subida de ficheros al servidor';
        }
    }

    public function uploadIncidenceAjax($plate, $date, $message, $client) {
        $hoy = new \DateTime('now');
        $day = substr($date, 0, 2);
        $day = intval($day);
        $month = substr($date, 2, 2);
        $month = intval($month);
        $year = substr($date, 4, 4);
        $year = intval($year);
        $hour = substr($date, 9, 2);
        $hour = intval($hour);
        $minute = substr($date, 11, 2);
        $minute = intval($minute);
        $second = substr($date, 13, 2);
        $second = intval($second);
        $hoy->setDate($year, $month, $day);
        $hoy->setTime($hour, $minute, $second);
        $hoyTimeStr = $hoy->format('Y-m-d H:i:s');
        $hoyStr = $hoy->format('Y-m-d');
        $clientId = $client->getId();
        $sql = "insert into registros set"
                . " matricula = :matricula,"
                . " fecha = :hoy,"
                . " id_cliente = :idCliente,"
                . " fecha_hora_registro = :fecha_hora_registro";
        $query = $this->_db->prepare($sql);
        $query->bindParam(':matricula', $plate);
        $query->bindParam(':hoy', $hoyStr);
        $query->bindParam(':idCliente', $clientId);
        $query->bindParam(':fecha_hora_registro', $hoyTimeStr);
        if (!$query->execute()) {
            return 1 . 'No se pudo insertar el registro';
        };

        $sql = "INSERT INTO incidencias SET matricula = :matricula,"
                . " mensaje = :mensaje, fecha = :fecha,"
                . " id_cliente = :id_cliente";
        $query = $this->_db->prepare($sql);
        $query->bindParam(':matricula', $plate);
        $query->bindParam(':mensaje', $message);
        $query->bindParam(':fecha', $hoyStr);
        $query->bindParam(':id_cliente', $clientId);
        if (!$query->execute()) {
            return 1 . 'Problemas en la inserción de la incidencia en la base de datos (ejecución)';
        }
        return '0';
    }

}
