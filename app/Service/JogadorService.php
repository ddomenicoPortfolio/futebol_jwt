<?php

namespace App\Service;

use Psr\Http\Message\ResponseInterface as Response;

use App\Dao\ClubeDAO;
use App\Dao\JogadorDAO;
use App\Util\MensagemErro;
use App\Model\Clube;
use App\Model\Jogador;

use \PDOException;

class JogadorService {

    private JogadorDAO $jogadorDAO;

    public function __construct() {
        $this->jogadorDAO = new JogadorDAO();
    }

    public function inserir(Response $response, Jogador $jogador) : Response {
        //Valida os dados
		$erro = $this->validar($jogador);
		if($erro) {
			$response->getBody()->write(MensagemErro::getJSONErro($erro));
			return $response
					->withHeader('Content-Type', 'application/json')
					->withStatus(500); //INTERNAL_SERVER_ERROR
		}

		//Insere no banco de dados
		try {
			$jogador = $this->jogadorDAO->insert($jogador);
			
			$response->getBody()->write(json_encode($jogador, JSON_UNESCAPED_SLASHES));
			return $response
					->withHeader('Content-Type', 'application/json')
					->withStatus(201); //CREATED
		
		} catch(PDOException $ex) {
			$jsonErro = MensagemErro::getJSONErro("Erro ao inserir o jogador!", $ex->getMessage());
			$response->getBody()->write($jsonErro);
			return $response
					->withHeader('Content-Type', 'application/json')
					->withStatus(500); //INTERNAL_SERVER_ERROR
		}
        
    }

    private function validar(Jogador $jogador) {
        if(! $jogador->getNome())
            return "O campo nome é obrigatório.";
            
        if(! $jogador->getPosicao())
            return "O campo posição é obrigatório.";

        if(! $jogador->getNumero())
            return "O campo número é obrigatório e deve ser numérico.";

        if(! $jogador->getImagem())
            return "O campo imagem é obrigatório.";

        if((! $jogador->getClube()) ||  (! $jogador->getClube()->getId()))
            return "O campo clube é obrigatório.";

        if(! $this->validarIdClube($jogador->getClube()))
            return "O ID do clube é inválido ou não está cadastrado na base de dados.";

        return null;
    }

    private function validarIdClube(?Clube $clube) {
        $clubeDAO = new ClubeDAO();
        $clube = $clubeDAO->findById($clube->getId());

        if(! $clube)
            return false;

        return true;
    }
}