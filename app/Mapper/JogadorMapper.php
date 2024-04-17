<?php

namespace App\Mapper;

use App\Model\Jogador;
use App\Model\Clube;

class JogadorMapper {

    public function mapFromDatabaseArrayToObjectArray($regArray) {
        $arrayObj = array();

        foreach($regArray as $reg) {
            $regObj = $this->mapFromDatabaseToObject($reg);
            array_push($arrayObj, $regObj); 
        }

        return $arrayObj;
    }

    public function mapFromDatabaseToObject($regDatabase, $mapClube = true) {
        $obj = new Jogador();
        if(isset($regDatabase['id'])) 
            $obj->setId($regDatabase['id']);
        
        if(isset($regDatabase['nome']))
            $obj->setNome($regDatabase['nome']);

        if(isset($regDatabase['posicao']))
            $obj->setPosicao($regDatabase['posicao']);

        if(isset($regDatabase['numero']) and is_integer($regDatabase['numero']))
            $obj->setNumero($regDatabase['numero']);

        if(isset($regDatabase['imagem']))
            $obj->setImagem($regDatabase['imagem']);

        if($mapClube)
            $obj->setClube($this->mapClubeFromDatabaseToObject($regDatabase));

        return $obj;
    }

    public function mapFromJsonToObject($regJson) {
        $obj = $this->mapFromDatabaseToObject($regJson, false);

        if(isset($regJson['clube']) and isset($regJson['clube']['id'])) {
            $clube = new Clube;
            $clube->setId($regJson['clube']['id']);
            $obj->setClube($clube);
        }

        return $obj;        
    }
    
    private function mapClubeFromDatabaseToObject($regDatabase) {
        $clube = new Clube();
        
        if(isset($regDatabase['id_clube']))
            $clube->setId($regDatabase['id_clube']);
            
        if(isset($regDatabase['nome_clube']))
            $clube->setNome($regDatabase['nome_clube']);

        if(isset($regDatabase['cidade_clube']))
            $clube->setCidade($regDatabase['cidade_clube']);

        if(isset($regDatabase['imagem_clube']))
            $clube->setImagem($regDatabase['imagem_clube']);
        
        return $clube;
    }


}