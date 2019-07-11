<?php

use \Projeto\PageAdmin;
use \Projeto\Model\User;


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

?>