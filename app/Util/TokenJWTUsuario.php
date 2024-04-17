<?php 

namespace App\Util;

class TokenJWTUsuario {

    public static function getJSON($tokenJWT, $idUsuario, $nomeUsuario) {
        $dados['token'] = $tokenJWT;
        $dados['idUsuario'] = $idUsuario;
        $dados['nomeUsuario'] = $nomeUsuario;
        return json_encode($dados);
    }

}