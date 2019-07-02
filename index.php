<?php 

session_start();
require_once("vendor/autoload.php");//é do composer responsavel por chamar as bibliotecas ou seja, as dependencias

use \Slim\Slim;
use \Projeto\Page;
use \Projeto\PageAdmin;
use \Projeto\Model\User;

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

     User::verifyLogin();
	
	$page = new PageAdmin();

	$page->setTpl("index");


});

//CRIANDO NOVA ROTA PARA LOGIN
$app->get('/admin/login/', function() {
    
    //Como não tem header e footer vamos desabiltar nos contrutores e modificar a classe Page pois PageAdmin herda dela
	$page  = new PageAdmin([
        
        "header"=>false,
        "footer"=>false

	]);

	//chamar nosso novo template
	$page->setTpl("login");


});

//VALIDANDO LOGIN
$app->post('/admin/login/', function() {

    //validando login
	User::login($_POST["login"], $_POST["password"]);

    //caso de certo acima a validação, passa para pagina Admin
	header("Location: /admin/");
	exit;

});

//DESLOGAR

$app->get('/admin/logout/', function(){

	   User::logout();

	   header("Location: /admin/login");
	   exit;


});



//Nossa chamada para execução é a chave de inginição do carro, ele que executa tudo
$app->run();

 ?>