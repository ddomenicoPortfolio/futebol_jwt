<?php 

namespace App\Util;

use Slim\Psr7\Response;

class MensagemErro {

    public static function getJSONErro($msg, $msgErro = "", $httpStatus = 500) {
        $erro['mensagem'] = $msg;
        $erro['mensagemErro'] = $msgErro;
        $erro['status'] = $httpStatus;
        return json_encode($erro);
    }

    public static function getResponseErro(Response $response, $msg, $msgErro = "", $httpStatus = 500) : Response {
        $response->getBody()->write(MensagemErro::getJSONErro($msg, $msgErro, $httpStatus));
		return $response
				->withHeader('Content-Type', 'application/json')
				->withStatus($httpStatus);
    }

}