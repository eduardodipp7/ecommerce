<?php 

session_start();
require_once("vendor/autoload.php");//é do composer responsavel por chamar as bibliotecas ou seja, as dependencias

use \Slim\Slim;

$app = new Slim();//cria um objeto da classe Slim 

$app->config('debug', true);//mostra os erros detalhados 

require_once("functions.php");
require_once("site.php");
require_once("admin.php");
require_once("admin-users.php");
require_once("admin-categories.php");
require_once("admin-products.php");

//Nossa chamada para execução é a chave de inginição do carro, ele que executa tudo
$app->run();

 ?>