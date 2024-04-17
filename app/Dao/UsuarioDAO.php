<?php

namespace App\Dao;

use App\Util\Connection;
use App\Mapper\UsuarioMapper;
use App\Model\Usuario;

use \Exception;

class UsuarioDAO {

    private $conn;
    private $usuarioMapper;

    public function __construct() {
        $this->conn = Connection::getConnection();
        $this->usuarioMapper = new UsuarioMapper();
    }

    public function list() {
        $sql = 'SELECT * FROM usuarios ORDER BY id';

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();

        return $this->usuarioMapper->mapFromDatabaseArrayToObjectArray($result);
    }

    public function findById(int $id) {
        $sql = 'SELECT * FROM usuarios WHERE id = :id';

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue("id", $id);
        $stmt->execute();
        $result = $stmt->fetchAll();

        $arrayObj = $this->usuarioMapper->mapFromDatabaseArrayToObjectArray($result);

        if(count($arrayObj) == 0)
            return null;
        else if(count($arrayObj) > 1)
            new Exception("Mais de um registro encontrado para o ID " . $id);
        else //count($arrayObj) == 1
            return $arrayObj[0];
    }

    public function findByLogin(string $login) {
        $sql = 'SELECT * FROM usuarios WHERE login = :login';

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue("login", $login);
        $stmt->execute();
        $result = $stmt->fetchAll();

        $arrayObj = $this->usuarioMapper->mapFromDatabaseArrayToObjectArray($result);

        if(count($arrayObj) == 0)
            return null;
        else if(count($arrayObj) > 1)
            new Exception("Mais de um registro encontrado para o Login " . $login);
        else //count($arrayObj) == 1
            return $arrayObj[0];
    }

}