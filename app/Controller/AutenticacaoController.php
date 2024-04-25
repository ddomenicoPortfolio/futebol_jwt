<?php

namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Dao\UsuarioDAO;
use App\Service\TokenService;
use App\Util\MensagemErro;
use App\Util\TokenJWTUsuario;
use Throwable;

class AutenticacaoController {

	private $usuarioDAO;
	private TokenService $tokenService;
	
	public function __construct() {
		$this->usuarioDAO = new UsuarioDAO();
		$this->tokenService = new TokenService();
	}

	public function autenticar(Request $request, Response $response, array $args): Response {
		//Carrega o usuário que veio na requisição em formato JSON
		$usuarioArray = $request->getParsedBody(); //Retorna um array a partir do JSON

		//Busca o usuário no banco
		$usuario = $this->usuarioDAO->findByLogin($usuarioArray['login']);
		if($usuario) {
			
			if(password_verify($usuarioArray['senha'], $usuario->getSenha())) {
				$token = $this->tokenService->gerarToken($usuario);

				$retorno = TokenJWTUsuario::getJSON($token, 
								$usuario->getId(), $usuario->getNome());

				$response->getBody()->write($retorno);
				return $response
					->withHeader('Content-Type', 'application/json')
					->withStatus(200); //OK
			}
		}

		//Caso o usuário não foi encontrado, retorna FORBIDDEN
		$response->getBody()->write(MensagemErro::getJSONErro("Usuário não autenticado!", "", 401));
		return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus(401); //FORBIDDEN
    }

	public function verificar(Request $request, Response $response, array $args): Response {
		try{
			$this->tokenService->validarToken($request);
			
			$dadosToken = $this->tokenService->getDadosFromToken($request);

			$response->getBody()->write(json_encode($dadosToken));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus(200); //OK
		} catch(Throwable $e) {
			//Caso token seja inválido, retorna FORBIDDEN
			return MensagemErro::getResponseErro($response, "Token inválido!", $e->getMessage(), 401);
		}
	}

	public function perfil(Request $request, Response $response, array $args): Response {
		$erro = "";
		try{
			$usuario = $this->tokenService->getUsuarioFromToken($request);

			if($usuario) {
				$response->getBody()->write(json_encode($usuario));
				return $response
					->withHeader('Content-Type', 'application/json')
					->withStatus(200); //OK
			} else
				$erro = "Usuário não encontrado!";

		} catch(Throwable $e) {
			$erro = $e->getMessage();
		}	
		
		//Caso token seja inválido, retorna FORBIDDEN
		return MensagemErro::getResponseErro($response, "Token inválido!", $erro, 401);
	}

}