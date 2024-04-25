<?php

namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Dao\ClubeDAO;
use App\Mapper\ClubeMapper;
use App\Service\ClubeService;
use App\Service\TokenService;
use App\Util\MensagemErro;

use \PDOException;
use Throwable;

class ClubeController {

	private $clubeDAO;
	private $clubeMapper;
	private $clubeService;
	private TokenService $tokenService;

	public function __construct() {
		$this->clubeDAO = new ClubeDAO();
		$this->clubeMapper = new ClubeMapper();
		$this->clubeService = new ClubeService();
		$this->tokenService = new TokenService();
	}

    public function listar(Request $request, Response $response, array $args): Response {
		//Valida o token de acesso do usuário
		try {
			$this->tokenService->validarToken($request);
		} catch(Throwable $e) {
			//Retorna FORBIDDEN
			return MensagemErro::getResponseErro($response, "Token inválido", $e->getMessage(), 401);
		}

		$dados = $this->clubeDAO->list();
		
		$json = json_encode($dados, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
		$response->getBody()->write($json);

		return $response
			->withHeader('Content-Type', 'application/json')
			->withStatus(200);
    }

	public function buscarPorId(Request $request, Response $response, array $args): Response {
		//Valida o token de acesso do usuário
		try {
			$this->tokenService->validarToken($request);
		} catch(Throwable $e) {
			//Retorna FORBIDDEN
			return MensagemErro::getResponseErro($response, "Token inválido", $e->getMessage(), 401);
		}

		$id = $args['id'];
		$clube = $this->clubeDAO->findById($id);
		
		if($clube) { 
			$json = json_encode($clube, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
			$response->getBody()->write($json);
			
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus(200);
		}

		return $response->withStatus(404); //NOT_FOUND
    }

	public function inserir(Request $request, Response $response, array $args): Response {
		//Valida o token de acesso do usuário
		try {
			$this->tokenService->validarToken($request);
		} catch(Throwable $e) {
			//Retorna FORBIDDEN
			return MensagemErro::getResponseErro($response, "Token inválido", $e->getMessage(), 401);
		}

		//Carrega o clube que veio na requisição em formato JSON
		$clubeArrayAssoc = $request->getParsedBody(); //Retorna um array a partir do JSON
		$clube = $this->clubeMapper->mapFromJsonToObject($clubeArrayAssoc);

		//Valida os dados
		$erro = $this->clubeService->validar($clube);
		if($erro) {
			$response->getBody()->write(MensagemErro::getJSONErro($erro));
			return $response
					->withHeader('Content-Type', 'application/json')
					->withStatus(500); //INTERNAL_SERVER_ERROR
		}

		//Insere no banco de dados
		try {
			$clube = $this->clubeDAO->insert($clube);
			
			$response->getBody()->write(json_encode($clube, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE));
			return $response
					->withHeader('Content-Type', 'application/json')
					->withStatus(201); //CREATED
		
		} catch(PDOException $ex) {
			$jsonErro = MensagemErro::getJSONErro("Erro ao inserir o clube!", $ex->getMessage());
			$response->getBody()->write($jsonErro);
			return $response
					->withHeader('Content-Type', 'application/json')
					->withStatus(500); //INTERNAL_SERVER_ERROR
		}
    }

	public function atualizar(Request $request, Response $response, array $args): Response {
		//Valida o token de acesso do usuário
		try {
			$this->tokenService->validarToken($request);
		} catch(Throwable $e) {
			//Retorna FORBIDDEN
			return MensagemErro::getResponseErro($response, "Token inválido", $e->getMessage(), 401);
		}

		$id = $args['id'];
		$clube = $this->clubeDAO->findById($id);
		
		if($clube) { 
			//Carrega o clube que veio na requisição em formato JSON
			$clubeArrayAssoc = $request->getParsedBody(); //Retorna um array a partir do JSON
			$clube = $this->clubeMapper->mapFromJsonToObject($clubeArrayAssoc);
			$clube->setId($id);
			
			try {
				//Atualiza no banco de dados
				$clube = $this->clubeDAO->update($clube);		

				$response->getBody()->write(json_encode($clube, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE));
				return $response
						->withHeader('Content-Type', 'application/json')
						->withStatus(200); //OK
			
			} catch(PDOException $ex) {
				$jsonErro = MensagemErro::getJSONErro("Erro ao atualizar o clube!", $ex->getMessage());
				$response->getBody()->write($jsonErro);
				return $response
						->withHeader('Content-Type', 'application/json')
						->withStatus(500); //INTERNAL_SERVER_ERROR
			}
		}

		return $response->withStatus(404); //NOT_FOUND
    }

	public function deletar(Request $request, Response $response, array $args): Response {
		//Valida o token de acesso do usuário
		try {
			$this->tokenService->validarToken($request);
		} catch(Throwable $e) {
			//Retorna FORBIDDEN
			return MensagemErro::getResponseErro($response, "Token inválido", $e->getMessage(), 401);
		}

		$id = $args['id'];
		$clube = $this->clubeDAO->findById($id);
		
		if($clube) { 
			
			try{
				//Deleta do banco de dados
				$this->clubeDAO->deleteById($id);
				return $response->withStatus(200); //OK
			} catch(PDOException $ex) {
				$jsonErro = MensagemErro::getJSONErro("Erro ao deletar o clube!", $ex->getMessage());
				$response->getBody()->write($jsonErro);
				return $response
						->withHeader('Content-Type', 'application/json')
						->withStatus(500); //INTERNAL_SERVER_ERROR
			}
		}

		return $response->withStatus(404); //NOT_FOUND
    }

}