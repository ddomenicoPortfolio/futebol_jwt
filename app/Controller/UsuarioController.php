<?php

namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Dao\UsuarioDAO;
use App\Service\ArquivoService;
use App\Service\TokenService;
use App\Util\Config;
use App\Util\MensagemErro;
use PDOException;
use Throwable;

class UsuarioController {

    private UsuarioDAO $usuarioDAO;
	private ArquivoService $arquivoService;
	private TokenService $tokenService;

	public function __construct() {
		$this->usuarioDAO = new UsuarioDAO();
		$this->arquivoService = new ArquivoService();
		$this->tokenService = new TokenService();
	}

    public function perfilFoto(Request $request, Response $response, array $args): Response {
		$erro = "";
		
		$idUsuario = $args['id'];

		$usuario = $this->usuarioDAO->findById($idUsuario);
		if($usuario) {
			$params = $request->getQueryParams();
    		$arquivoFoto = "";
			if(isset($params['arquivo']))
				$arquivoFoto = trim($params['arquivo']);

			if($usuario->getFotoPerfil() && $usuario->getFotoPerfil() === $arquivoFoto) {
				//Carrega o parâmetro com a imagem da foto
				$filePath = Config::PATH_FILES . "/" . $usuario->getFotoPerfil();
				
				$mimeType = $this->arquivoService->getMimeType($filePath);
				$imagem = "";
				if($mimeType)
					$imagem = file_get_contents($filePath);
				
				if($mimeType && $imagem) {
					$response->getBody()->write($imagem);
					return $response->withHeader('Content-Type', $mimeType);
				} else
					$erro = "Imagem de perfil do usuário não encontrada na biblioteca de arquivos!";	
			} else
				$erro = "Usuário sem imagem de perfil cadadastrada!";	
		} else
			$erro = "Usuário não encontrado!";

		
		//Caso de erro, retorna NOT_FOUND
		$response->getBody()->write(MensagemErro::getJSONErro("Imagem não encontrada!", $erro, 404));
		return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus(404); //NOT_FOUND
	}

	public function perfilFotoAtualizar(Request $request, Response $response, array $args): Response {
		//Valida o token de acesso do usuário
		try {
			$this->tokenService->validarToken($request);
		} catch(Throwable $e) {
			//Retorna FORBIDDEN
			return MensagemErro::getResponseErro($response, "Token inválido", $e->getMessage(), 401);
		}
		
		$idUsuario = $args['id'];

		$usuario = $this->usuarioDAO->findById($idUsuario);
		if($usuario) {
			//$response->getBody()->write(json_encode($usuario));

			$msgErro = "";
			$statusErro = 0;
			
			$arquivos = $request->getUploadedFiles();
			
			if(isset($arquivos["arquivo"])) {
				$nomeArquivo = $this->arquivoService->salvarArquivo($arquivos["arquivo"]);

				if($nomeArquivo) {
					
					try {
						//Atualizar o usuário na base de dados
						$this->usuarioDAO->updateFotoPerfil($usuario->getId(), $nomeArquivo);

						//Remove o arquivo anterior
						$this->arquivoService->removerArquivo($usuario->getFotoPerfil());

						//Atualizar o nome do arquivo da foto de perfil
						$usuario->setFotoPerfil($nomeArquivo);

						$response->getBody()->write(json_encode($usuario, 
														JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE));
						return $response
							->withHeader('Content-Type', 'application/json')
							->withStatus(200); //OK
					} catch(PDOException $ex) {
						$msgErro = $ex->getMessage();
					}

				} else
					$msgErro = "Erro ao salvar o arquivo no diretório!";

				$statusErro = 500; //INTERNAL SERVER ERRO

			} else {
				$msgErro = "Arquivo não encontrado!";
				$statusErro = 400; //BAD_REQUEST
			}

			//Caso de erro, retorna-o
			$response->getBody()->write(MensagemErro::getJSONErro("Erro ao salvar o arquivo!", $msgErro, $statusErro));
			return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($statusErro);
		}

		return $response->withStatus(404); //NOT_FOUND
    }

}