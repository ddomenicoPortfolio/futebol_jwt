<?php

namespace App\Model;

use \JsonSerializable;

class Usuario implements JsonSerializable {

    private ?int $id;
    private ?string $nome;
    private ?string $login;
    private ?string $senha;
    private ?string $fotoPerfil;

    public function __construct() {
        $this->id = 0;
        $this->fotoPerfil = "";
    }

    public function jsonSerialize() : array {
        return array(
            "id" => $this->id,
            "nome" => $this->nome,
            "login" => $this->login,
            "fotoPerfil" => $this->fotoPerfil
        ); 
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(?string $nome): self
    {
        $this->nome = $nome;

        return $this;
    }
    
    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(?string $login): self
    {
        $this->login = $login;

        return $this;
    }
    
    public function getSenha(): ?string
    {
        return $this->senha;
    }

    public function setSenha(?string $senha): self
    {
        $this->senha = $senha;

        return $this;
    }

    public function getFotoPerfil(): ?string
    {
        return $this->fotoPerfil;
    }

    public function setFotoPerfil(?string $fotoPerfil): self
    {
        $this->fotoPerfil = $fotoPerfil;

        return $this;
    }
}