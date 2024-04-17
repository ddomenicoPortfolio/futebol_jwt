<?php

namespace App\Dao;

use App\Util\Connection;
use App\Mapper\JogadorMapper;
use App\Model\Jogador;

use \Exception;

class JogadorDAO {

    const SQL_BUSCA = "SELECT j.*, c.nome AS nome_clube, c.cidade AS cidade_clube, c.imagem AS imagem_clube" . 
                    " FROM jogadores j JOIN clubes c ON (c.id = j.id_clube)";

    private $conn;
    private $jogadorMapper;

    public function __construct() {
        $this->conn = Connection::getConnection();
        $this->jogadorMapper = new JogadorMapper();
    }

    public function listByClube(int $idClube) {
        $sql = JogadorDAO::SQL_BUSCA . ' WHERE j.id_clube = :id_clube ORDER BY id';

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue("id_clube", $idClube);
        $stmt->execute();
        $result = $stmt->fetchAll();

        return $this->jogadorMapper->mapFromDatabaseArrayToObjectArray($result);
    }

    public function findById(int $id) {
        $sql = JogadorDAO::SQL_BUSCA . ' WHERE j.id = :id';

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue("id", $id);
        $stmt->execute();
        $result = $stmt->fetchAll();

        $arrayObj = $this->jogadorMapper->mapFromDatabaseArrayToObjectArray($result);

        if(count($arrayObj) == 0)
            return null;
        else if(count($arrayObj) > 1)
            new Exception("Mais de um registro encontrado para o ID " . $id);
        else //count($arrayObj) == 1
            return $arrayObj[0];
    }

    public function insert(Jogador $jogador) {
        $sql = 'INSERT INTO jogadores (nome, posicao, numero, imagem, id_clube) VALUES (:nome, :posicao, :numero, :imagem, :id_clube)';

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue("nome", $jogador->getNome());
        $stmt->bindValue("posicao", $jogador->getPosicao());
        $stmt->bindValue("numero", $jogador->getNumero());
        $stmt->bindValue("imagem", $jogador->getImagem());
        $stmt->bindValue("id_clube", $jogador->getClube()->getId());
        $stmt->execute();

        $id = $this->conn->lastInsertId();
        $jogador->setId($id);
        return $jogador;
    }


}