<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

use App\Controller\AutenticacaoController;
use App\Controller\ClubeController;
use App\Controller\JogadorController;
use Slim\Exception\HttpNotFoundException;

//require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/vendor/autoload.php');

//require_once(__DIR__ . "/util/config.php");
//require_once(__DIR__ . "/util/custom_autoloader.php");
//require_once(__DIR__ . '/controller/OrcamentoController.php');

$app = AppFactory::create();
$app->setBasePath("/futebol_jwt"); //Adicionar o nome da pasta do projeto

// Parse json, form data and xml
$app->addBodyParsingMiddleware();
//$app->addRoutingMiddleware(); //Serve para adicionar tratamentos padrões para erros retornados pelos ENDPoints
$app->addErrorMiddleware(true, true, true); //Retorna um erro do Framework caso não tratado

//CORS
$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});

$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

//ROTAS
$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Funcionou!");
    return $response;
});

$app->get('/hello/{name}', function (Request $request, Response $response, $args) {
    $name = $args['name'];
    $response->getBody()->write("Hello world!<br>");
    $response->getBody()->write("Thanks from visiting us Mr(s). $name");
    return $response;
});

//Chamar /hello2?name=Daniel
$app->get('/hello2', function (Request $request, Response $response, $args) {
    //$name = $args['name'];
    $params = $request->getQueryParams();
    $name = $params['name'];

    $response->getBody()->write("Hello world!<br>");
    $response->getBody()->write("Thanks from visiting us Mr(s). $name");
    return $response;
});

//Autenticação
$app->post('/autenticacao', AutenticacaoController::class . ':autenticar');
$app->get('/autenticacao/validar', AutenticacaoController::class . ':verificar');

//Clubes
//$app->get('/clubes', ClubeController::class . ':listar');
$app->get('/clubes', 'App\Controller\ClubeController:listar');
$app->get('/clubes/{id}', ClubeController::class . ':buscarPorId');
$app->post('/clubes', ClubeController::class . ':inserir');
$app->put('/clubes/{id}', ClubeController::class . ':atualizar');
$app->delete('/clubes/{id}', ClubeController::class . ':deletar');


//Jogadores
$app->get('/jogadores/clube/{idClube}', JogadorController::class . ':listarPorClube');
$app->get('/jogadores/{id}', JogadorController::class . ':buscarPorId');
$app->post('/jogadores', JogadorController::class . ':inserir');
/*
$app->get('/hello_class/{name}', HelloWorldController::class);

$app->get('/hello_class2', HelloWorldController::class . ':main');

$app->get('/hello_class3', HelloWorldController::class . ':json');
*/

//Tratamento para rota não encontrada
$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function ($request, $response) {
    throw new HttpNotFoundException($request);
});


$app->run();
