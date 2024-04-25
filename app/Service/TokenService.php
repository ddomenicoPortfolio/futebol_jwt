<?php

namespace App\Service;

use App\Dao\UsuarioDAO;
use App\Model\Usuario;
use App\Util\Config;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Slim\Psr7\Request;
use stdClass;
use Throwable;

class TokenService {

    private UsuarioDAO $usuarioDAO;

    public function __construct() {
        $this->usuarioDAO = new UsuarioDAO();
    }

    public function gerarToken(Usuario $usuario) {
        $payloadToken = array(
            "exp" => time() + 600, //10 minutos
            "iat" => time(),
            "idUsuario" => $usuario->getId(),
            "nomeUsuario" => $usuario->getNome()
        );

        $encodedToken = JWT::encode($payloadToken, Config::TOKEN_KEY, Config::TOKEN_ALGO);
        return $encodedToken;
    }

    public function getDadosFromToken(Request $request) {
        try {
            $tokenJwt = $this->getTokenFromRequest($request);

            if(! $tokenJwt)
                throw new Exception("Token de acesso não recebido na requisição!");

            //Decodificar o token
            $dadosToken = JWT::decode($tokenJwt, new Key(Config::TOKEN_KEY, Config::TOKEN_ALGO));
            return $dadosToken;
        } catch(Throwable $e) {
            throw $e;
        }
    }

    public function validarToken(Request $request) {
        try {
            if(! Config::REQUIRES_AUTHENTICATION)
                return;

            $dadosTokenJwt = $this->getDadosFromToken($request);

            //Verifica se exite o dado 'idusuario' no objeto do token
            if(! property_exists($dadosTokenJwt, 'idUsuario')) 
                throw new Exception("Token de acesso inválido!");

        } catch(Throwable $e) {
            throw $e;
        }
    }

    public function getUsuarioFromToken(Request $request) : Usuario {
        try{ 
            //Recebe os dados do token
            $dadosTokenJwt = $this->getDadosFromToken($request);

            //Retorna o usuário
            $id = $dadosTokenJwt->idUsuario;
        
            $usuario = $this->usuarioDAO->findById($id);
            return $usuario;
        } catch(Throwable $e) {
            throw $e;
        }
    }
    
    /* ---------------- Métodos privados --------------- */
    private function getTokenFromRequest(Request $request) {
		//Recebe o token da requisição
		$authTokens = $request->getHeader("Authorization");// $_SERVER['HTTP_AUTHORIZATION'];
		$authToken = "";
		if($authTokens)
			$authToken = trim(str_replace("Bearer", "", $authTokens[0]));

		return $authToken;
	}

    
}