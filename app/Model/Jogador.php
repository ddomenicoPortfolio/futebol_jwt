<?php

namespace App\Model;

use \JsonSerializable;

class Jogador implements JsonSerializable {

    private ?int $id;
    private ?string $nome;
    private ?string $posicao;
    private ?int $numero;
    private ?string $imagem;
    private ?Clube $clube;

    public function __construct() {
        $this->id = 0;
        $this->nome = null;
        $this->posicao = null;
        $this->numero = null;
        $this->imagem = null;
        $this->clube = null;
    }

    public function jsonSerialize() : array {
        return 
        [
            'id' => $this->id,
            'nome' => $this->nome,
            'posicao' => $this->posicao,
            'numero' => $this->numero,
            'imagem' => $this->imagem,
            'clube' => $this->clube
        ]; 
    }

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of nome
     */ 
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * Set the value of nome
     *
     * @return  self
     */ 
    public function setNome($nome)
    {
        $this->nome = $nome;

        return $this;
    }

    /**
     * Get the value of numero
     */ 
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set the value of numero
     *
     * @return  self
     */ 
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get the value of posicao
     */ 
    public function getPosicao()
    {
        return $this->posicao;
    }

    /**
     * Set the value of posicao
     *
     * @return  self
     */ 
    public function setPosicao($posicao)
    {
        $this->posicao = $posicao;

        return $this;
    }

    /**
     * Get the value of imagem
     */ 
    public function getImagem()
    {
        return $this->imagem;
    }

    /**
     * Set the value of imagem
     *
     * @return  self
     */ 
    public function setImagem($imagem)
    {
        $this->imagem = $imagem;

        return $this;
    }

    /**
     * Get the value of clube
     */ 
    public function getClube()
    {
        return $this->clube;
    }

    /**
     * Set the value of clube
     *
     * @return  self
     */ 
    public function setClube($clube)
    {
        $this->clube = $clube;

        return $this;
    }
    
}