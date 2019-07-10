<?php 

session_start();
require_once("vendor/autoload.php");//é do composer responsavel por chamar as bibliotecas ou seja, as dependencias

use \Slim\Slim;
use \Projeto\Page;
use \Projeto\PageAdmin;
use \Projeto\Model\User;
use \Projeto\Model\Category;

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

	   header("Location: /admin/login/");
	   exit;


});

//CAMADA DE USUARIOS 
$app->get('/admin/users/', function(){

    //veirfica o login pra entrar na pagina
    User::verifyLogin();

    //metodo pra listar todos usuarios
    $users = User::listAll();

    $page = new PageAdmin();

    $page->setTpl("users", array(

    	"users"=>$users

    ));
});

$app->get('/admin/users/create/', function(){

    User::verifyLogin();

    $page = new PageAdmin();
    $page->setTpl("users-create");
});

$app->get('/admin/users/:iduser/delete/', function($iduser){

	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);

	$user->delete();

	header("Location: /admin/users/");
	exit;



});

$app->get('/admin/users/:iduser/', function($iduser){

    User::verifyLogin();

    $user = new  User();
    $user->get((int)$iduser);

    $page = new PageAdmin();
    $page->setTpl("users-update", array(
     "user"=>$user->getValues()

    )); 

});

$app->post('/admin/users/create/', function(){

	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

	$user->setData($_POST);

	$user->save();

	header("Location: /admin/users/");
	exit;


});

$app->post('/admin/users/:iduser/', function($iduser){

	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

	$user->get((int)$iduser);

	$user->setData($_POST);

	$user->update();

	header("Location: /admin/users/");
	exit;

});

//CRIANDO A ROTA DO FORGOT - EMAIL

$app->get("/admin/forgot/", function(){

	$page  = new PageAdmin([
        
        "header"=>false,
        "footer"=>false

	]);

	$page->setTpl("forgot");


});

$app->post("/admin/forgot/", function(){

	
	$user = User::getForgot($_POST["email"]);

	header("Location: /admin/forgot/sent/");
	exit;

});

$app->get("/admin/forgot/sent/", function(){

	$page  = new PageAdmin([
        
        "header"=>false,
        "footer"=>false

	]);

	$page->setTpl("forgot-sent");

});

//CRIANDO A ROTA NA HORA DE CLICAR NO EMAIL PRA RECUPERAR A SENHA
$app->get("/admin/forgot/reset", function(){
    
  //Validar e recuperar de que usuario pertence esse codigo
	$user = User::validForgotDecrypt($_GET["code"]);

	$page  = new PageAdmin([
        
        "header"=>false,
        "footer"=>false

	]);

	$page->setTpl("forgot-reset", array(

     "name"=>$user["desperson"],
     "code"=>$_GET["code"]
	));

});

$app->post("/admin/forgot/reset", function(){
 
 $forgot = User::validForgotDecrypt($_POST["code"]);

 User::setForgotUsed($forgot["idrecovery"]);

 $user = new User();

 $user->get((int)$forgot["iduser"]);

 $password = password_hash($_POST["password"], PASSWORD_DEFAULT, [

 	"cost"=>12

 ]);

 $user->setPassword($password);

 $page  = new PageAdmin([
        
        "header"=>false,
        "footer"=>false

	]);

	$page->setTpl("forgot-reset-success");

});

//CRIANDO A ROTA PRA ACESSO A CATEGORIAS

$app->get("/admin/categories/", function(){

	User::verifyLogin();

	$categories = Category::listAll();

   $page  = new PageAdmin();

	$page->setTpl("categories", [
      'categories'=>$categories

	]);

});

$app->get("/admin/categories/create/", function(){
	User::verifyLogin();
	
   $page  = new PageAdmin();

	$page->setTpl("categories-create");

});

$app->post("/admin/categories/create/", function(){
	User::verifyLogin();
	
   $category = new Category();

   $category->setData($_POST);

   $category->save();

   header('Location: /admin/categories/');
   exit;

});

$app->get("/admin/categories/:idcategory/delete", function($idcategory){

	User::verifyLogin();

  $category = new Category();

  $category->get((int)$idcategory);

  $category->delete();

  header('Location: /admin/categories/');
   exit;

});

$app->get("/admin/categories/:idcategory/", function($idcategory){

	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

$page  = new PageAdmin();

	$page->setTpl("categories-update", [

		'category'=>$category->getValues()
	]);

});

$app->post("/admin/categories/:idcategory/", function($idcategory){

    User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

    $category->setData($_POST);

    $category->save();

     header('Location: /admin/categories/');
   exit;

});



//Nossa chamada para execução é a chave de inginição do carro, ele que executa tudo
$app->run();

 ?>