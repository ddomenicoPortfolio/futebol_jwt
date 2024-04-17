<?php

namespace App\Mapper;

use App\Model\Usuario;

class UsuarioMapper {

    public function mapFromDatabaseArrayToObjectArray($regArray) {
        $arrayObj = array();

        foreach($regArray as $reg) {
            $regObj = $this->mapFromDatabaseToObject($reg);
            array_push($arrayObj, $regObj); 
        }

        return $arrayObj;
    }

    public function mapFromDatabaseToObject($regDatabase) {
        $obj = new Usuario();
        if(isset($regDatabase['id'])) 
            $obj->setId($regDatabase['id']);
        
        if(isset($regDatabase['nome']))
            $obj->setNome($regDatabase['nome']);

        if(isset($regDatabase['login']))
            $obj->setLogin($regDatabase['login']);
        
        if(isset($regDatabase['senha']))
            $obj->setSenha($regDatabase['senha']);
        
        return $obj;
    }

}