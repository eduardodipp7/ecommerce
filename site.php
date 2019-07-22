<?php

use \Projeto\Page;
use \Projeto\Model\Product;
use \Projeto\Model\Category;
use \Projeto\Model\Cart;
use \Projeto\Model\Address;
use  \Projeto\Model\User;


$app->get('/', function() { //pega a rota que eu estou chamando e executa a função e cria a nova pagina

	$products = Product::listAll();

	//criando objeto da classe Page pra acessar as informaçoes
	$page = new Page();//nessa hora ele vai chamar o metodo construct e colocar o header na tela

	$page->setTpl("index", [
      
      'products'=>Product::checklist($products)  
 
	]);//adiciona aquele arquivo index.html que tem h1

	//Depois da linha acima ele verifica que não tem nehuma chamada, ele chama o metodo destruct limpa a memoria e printa o footer. Com isso ele mescla todos os arquivos dentro da views

    
	/*$sql = new Projeto\DB\Sql();
	$results = $sql->select("SELECT * FROM tb_users");
	echo json_encode($results);*/

});

//CRIANDO ROTA PRO MENU CATEGORIAS DO SITE
$app->get("/categories/:idcategory/", function($idcategory){

   $page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;

	$category = new Category();

	$category->get((int)$idcategory);

	$pagination = $category->getProductsPage($page);

	$pages = [];

	for ($i=1; $i <= $pagination['pages']; $i++) { 
		array_push($pages, [
			'link'=>'/categories/'.$category->getidcategory(). '?page='.$i,
			'page'=>$i
		]);
	}

	//criando objeto da classe Page pra acessar as informaçoes
	$page = new Page();//nessa hora ele vai chamar o metodo construct e colocar o header na tela

	$page->setTpl("category", [
     'category'=>$category->getValues(),
     'products'=>$pagination["data"],
     'page'=>$page

	]);
});

//CRIANDO ROTA PARA DETALHES DO PRODUTO NO SITE
$app->get("/products/:desurl", function($desurl){

	$product = new Product();
	$product->getFromURL($desurl);

	$page = new Page();

	$page->setTpl("product-detail", [
    
    'product'=>$product->getValues(),
    'categories'=>$product->getCategories()

	]);
});

//CRIANDO ROTA PARA CARRINHO DE COMPRAS PRO SITE

$app->get("/cart/", function(){

	$cart = Cart::getFromSession();

	$page = new Page();

	$page->setTpl("cart", [
     'cart'=>$cart->getValues(),
     'products'=>$cart->getProducts(),
     'error'=>Cart::getMsgError()
	]);
});

$app->get("/cart/:idproduct/add/", function($idproduct){

	$product = new Product();

	$product->get((int)$idproduct);

	$cart = Cart::getFromSession();

	$qtd = (isset($_GET['qtd'])) ? (int)$_GET['qtd'] : 1;

	for($i=0; $i < $qtd; $i++){

		$cart->addProduct($product);
	}


	header("Location: /cart/");
	exit;
});

//Remove um produto só

$app->get("/cart/:idproduct/minus/", function($idproduct){

	$product = new Product();

	$product->get((int)$idproduct);

	$cart = Cart::getFromSession();

	$cart->removeProduct($product);

	header("Location: /cart/");
	exit;
});

//Remove todos
$app->get("/cart/:idproduct/remove/", function($idproduct){

	$product = new Product();

	$product->get((int)$idproduct);

	$cart = Cart::getFromSession();

	$cart->removeProduct($product, true);

	header("Location: /cart/");
	exit;
});

$app->post("/cart/freight/", function(){

	$cart = Cart::getFromSession();

	$cart->setFreight($_POST['zipcode']);

	header("Location: /cart/");
	exit;
});

$app->get("/checkout/", function(){

	User::verifyLogin(false);

	$cart = Cart::getFromSession();

	$address = new Address();

	$page = new Page();

	$page->setTpl("checkout", [

		'cart'=>$cart->getValues(),
		'address'=>$address->getValues()

	]);
});

$app->get("/login/", function(){


	$page = new Page();

	$page->setTpl("login", [

		'error'=>User::getError(),
		'errorRegister'=>User::getErrorRegister(),
		'registerValues'=>(isset($_SESSION['registerValues'])) ? $_SESSION['registerValues'] : ['name'=> '', 'email'=>'', 'phone'=>'']

   
	]);

});


$app->post("/login/", function(){

	try{

	User::login($_POST['login'], $_POST['password']);

}catch(Exception $e){

	User::setError($e->getMessage());
}

	header("Location: /checkout/");
	exit;
});

$app->get("/logout/", function(){

	User::logout();

	header("Location: /login/");
	exit;
});

$app->post("/register/", function(){

	$_SESSION['registerValues'] = $_POST;

	if(!isset($_POST['name']) || $_POST['name'] == ''){



             User::setErrorRegister("Preeencha o seu nome.");
             header("Location: /login/");
             exit;
	}

	if(!isset($_POST['email']) || $_POST['email'] == ''){



             User::setErrorRegister("Preeencha o seu email.");
             header("Location: /login/");
             exit;
	}

	if(!isset($_POST['password']) || $_POST['password'] == ''){



             User::setErrorRegister("Preeencha a senha.");
             header("Location: /login/");
             exit;
	}

	if(User::checkLoginExist($_POST['email']) === true){
            
             User::setErrorRegister("Este endereço de email está sendo usado por outro usuário.");
             header("Location: /login/");
             exit;

	}

	$user = new User();

	$user->setData([

		'inadmin'=>0,
		'deslogin'=>$_POST['email'],
		'desperson'=>$_POST['name'],
		'desemail'=>$_POST['email'],
		'despassword'=>$_POST['password'],
		'nrphone'=>$_POST['phone']

	]);

	$user->save();

	User::login($_POST['email'], $_POST['password']);

	header('Location: /checkout/');
	exit;
});

$app->get("/forgot/", function(){

	$page  = new Page();

	$page->setTpl("forgot");


});

$app->post("/forgot/", function(){

	
	$user = User::getForgot($_POST["email"], false);

	header("Location: /forgot/sent/");
	exit;

});

$app->get("/forgot/sent/", function(){

	$page  = new Page();

	$page->setTpl("forgot-sent");

});

//CRIANDO A ROTA NA HORA DE CLICAR NO EMAIL PRA RECUPERAR A SENHA
$app->get("/forgot/reset", function(){
    
  //Validar e recuperar de que usuario pertence esse codigo
	$user = User::validForgotDecrypt($_GET["code"]);

	$page  = new Page();


	$page->setTpl("forgot-reset", array(

     "name"=>$user["desperson"],
     "code"=>$_GET["code"]
	));

});

$app->post("/forgot/reset/", function(){
 
 $forgot = User::validForgotDecrypt($_POST["code"]);

 User::setForgotUsed($forgot["idrecovery"]);

 $user = new User();

 $user->get((int)$forgot["iduser"]);

 $password = password_hash($_POST["password"], PASSWORD_DEFAULT, [

 	"cost"=>12

 ]);

 $user->setPassword($password);

 $page  = new Page();

	$page->setTpl("forgot-reset-success");

});

?>