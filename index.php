<?php 

require_once("vendor/autoload.php");//é do composer responsavel por chamar as bibliotecas ou seja, as dependencias

use \Slim\Slim;
use \Projeto\Page;
use \Projeto\PageAdmin;

$app = new Slim();//cria um objeto da classe Slim 

//$app = new \Slim\Slim();

$app->config('debug', true);//mostra os erros detalhados 

$app->get('/', function() { //pega a rota que eu estou chamando e executa a função e cria a nova pagina

	//criando objeto da classe Page pra acessar as informaçoes
	$page = new Page();//nessa hora ele vai chamar o metodo construct e colocar o header na tela

	$page->setTpl("index");//adiciona aquele arquivo index.html que tem h1

	//Depois da linha acima ele verifica que não tem nehuma chamada, ele chama o metodo destruct limpa a memoria e printa o footer. Com isso ele mescla todos os arquivos dentro da views

    
	/*$sql = new Projeto\DB\Sql();
	$results = $sql->select("SELECT * FROM tb_users");
	echo json_encode($results);*/

});


//CRIANDO NOVA ROTA PARA TEMPLATE ADMIN
$app->get('/admin/', function() { 

	
	$page = new PageAdmin();

	$page->setTpl("index");


});



//Nossa chamada para execução é a chave de inginição do carro, ele que executa tudo
$app->run();

 ?>