<?php

namespace App\Service;

use App\Model\Usuario;
use App\Util\Config;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Throwable;

class TokenService {

    public static function gerarToken(Usuario $usuario) {
        $payloadToken = array(
            "exp" => time() + 600, //10 minutos
            "iat" => time(),
            "idUsuario" => $usuario->getId(),
            "nomeUsuario" => $usuario->getNome()
        );

        $encodedToken = JWT::encode($payloadToken, Config::TOKEN_KEY, Config::TOKEN_ALGO);
        return $encodedToken;
    }

    public static function decodificarToken(string $tokenJwt) {
        try {
            $dadosToken = JWT::decode($tokenJwt, new Key(Config::TOKEN_KEY, Config::TOKEN_ALGO));
            return $dadosToken;
        } catch(Throwable $e) {
            throw $e;
        }
    }

    public static function validarToken($dadosTokenJwt) : bool {
        if(property_exists($dadosTokenJwt, 'idUsuario')) 
            return true;

        return false;
    }
    
}