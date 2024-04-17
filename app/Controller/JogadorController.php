<?php

namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Dao\JogadorDAO;
use App\Mapper\JogadorMapper;
use App\Service\JogadorService;
//use App\Util\MensagemErro;

class JogadorController {

    private $jogadorDAO;
	private $jogadorMapper;
	private $jogadorService;

	public function __construct() {
		$this->jogadorDAO = new JogadorDAO();
		$this->jogadorMapper = new JogadorMapper();
		$this->jogadorService = new JogadorService();
	}

    public function listarPorClube(Request $request, Response $response, array $args): Response {
        $idClube = $args['idClube'];
		$dados = $this->jogadorDAO->listByClube($idClube);
		
		$json = json_encode($dados, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
		$response->getBody()->write($json);

		return $response
			->withHeader('Content-Type', 'application/json')
			->withStatus(200);
    }

    public function buscarPorId(Request $request, Response $response, array $args): Response {
		$id = $args['id'];
		$clube = $this->jogadorDAO->findById($id);
		
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
		//Carrega o jogador que veio na requisição em formato JSON
		$jogadorArrayAssoc = $request->getParsedBody(); //Retorna um array a partir do JSON
		$jogador = $this->jogadorMapper->mapFromJsonToObject($jogadorArrayAssoc);

        return $this->jogadorService->inserir($response, $jogador);
    }

}