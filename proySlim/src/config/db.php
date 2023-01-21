<?php

class db{
    private $dbHost = '127.0.0.2:3307';
    private $dbUser = 'root';
    private $dbPass = '';
    private $dbName = 'slim';

    public function conexionDB(){
        $mysqlConnect = "mysql:host=$this->dbHost;dbname=$this->dbName";
        $dbConexion = new PDO($mysqlConnect, $this->dbUser, $this->dbPass);
        $dbConexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $dbConexion;
    }
}


?>