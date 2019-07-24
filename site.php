<?php

use \Projeto\Page;
use \Projeto\Model\Product;
use \Projeto\Model\Category;
use \Projeto\Model\Cart;
use \Projeto\Model\Address;
use  \Projeto\Model\User;
use  \Projeto\Model\Order;
use  \Projeto\Model\OrderStatus;


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
	$address = new Address();
	$cart = Cart::getFromSession();
	if (!isset($_GET['zipcode'])) {
		$_GET['zipcode'] = $cart->getdeszipcode();
	}
	if (isset($_GET['zipcode'])) {
		$address->loadFromCEP($_GET['zipcode']);
		$cart->setdeszipcode($_GET['zipcode']);
		$cart->save();
		$cart->getCalculateTotal();
	}
	if (!$address->getdesaddress()) $address->setdesaddress('');
	if (!$address->getdesnumber()) $address->setdesnumber('');
	if (!$address->getdescomplement()) $address->setdescomplement('');
	if (!$address->getdesdistrict()) $address->setdesdistrict('');
	if (!$address->getdescity()) $address->setdescity('');
	if (!$address->getdesstate()) $address->setdesstate('');
	if (!$address->getdescountry()) $address->setdescountry('');
	if (!$address->getdeszipcode()) $address->setdeszipcode('');
	$page = new Page();
	$page->setTpl("checkout", [
		'cart'=>$cart->getValues(),
		'address'=>$address->getValues(),
		'products'=>$cart->getProducts(),
		'error'=>Address::getMsgError()
	]);
});
$app->post("/checkout/", function(){
	User::verifyLogin(false);
	if (!isset($_POST['zipcode']) || $_POST['zipcode'] === '') {
		Address::setMsgError("Informe um CEP Válido.");
		header('Location: /checkout/');
		exit;
	}
	if (!isset($_POST['desaddress']) || $_POST['desaddress'] === '') {
		Address::setMsgError("Informe um endereço Válido.");
		header('Location: /checkout/');
		exit;
	}
	if (!isset($_POST['desdistrict']) || $_POST['desdistrict'] === '') {
		Address::setMsgError("Informe um bairro Válido.");
		header('Location: /checkout/');
		exit;
	}
	if (!isset($_POST['descity']) || $_POST['zipcode'] === '') {
		Address::setMsgError("Informe uma cidade Válida.");
		header('Location: /checkout/');
		exit;
	}
	if (!isset($_POST['desstate']) || $_POST['desstate'] === '') {
		Address::setMsgError("Informe um estado Válido.");
		header('Location: /checkout/');
		exit;
	}
	if (!isset($_POST['descountry']) || $_POST['descountry'] === '') {
		Address::setMsgError("Informe um Pais Válido.");
		header('Location: /checkout/');
		exit;
	}
	$user = User::getFromSession();
	$address = new Address();
	$_POST['deszipcode'] = $_POST['zipcode'];
	$_POST['idperson'] = $user->getidperson();
	$address->setData($_POST);
	$address->save();
	$cart = Cart::getFromSession();
	$cart->getCalculateTotal();
	$order = new Order();
	$order->setData([
		'idcart'=>$cart->getidcart(),
		'idaddress'=>$address->getidaddress(),
		'iduser'=>$user->getiduser(),
		'idstatus'=>OrderStatus::EM_ABERTO,
		'vltotal'=>$cart->getvltotal()
	]);
	$order->save();

	header("Location: /order/".$order->getidorder());
	exit;
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
	Cart::removeFromSession();
	session_regenerate_id();
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

$app->get("/profile/", function(){

	User::verifyLogin(false);

	$user = User::getFromSession();

	$page = new Page();

	$page->setTpl("profile", [

		'user'=>$user->getValues(),
		'profileMsg'=>User::getSuccess(),
		'profileError'=>User::getError()

	]);
});

$app->post("/profile/", function(){

	User::verifyLogin(false);

	if(!isset($_POST['desperson']) || $_POST['desperson'] === ''){
		User::setError("Preeencha o seu nome.");
		header('Location: /profile/');
		exit;
	}

	if(!isset($_POST['desemail']) || $_POST['desemail'] === ''){
		User::setError("Preeencha o seu e-mail.");
		header('Location: /profile/');
		exit;
	}

	$user = User::getFromSession();

	if($_POST['desemail'] !== $user->getdesemail()){

		if(User::checkLoginExist($_POST['desemail']) === true){
			User::setError("Este endereço de e-mail já está cadastrado");
			header('Location: /profile/');
		exit;
		}
	}

    $_POST['iduser'] = $user->getiduser();
	$_POST['inadmin'] = $user->getinadmin();
	$_POST['despassword'] = $user->getdespassword();
	$_POST['deslogin'] = $_POST['desemail'];

	$user->setData($_POST);

	$user->update(false);

	$_SESSION[User::SESSION] = $user->getValues();

	User::setSuccess("Dados alterados com sucesso");

	header('Location: /profile/');
	exit;
});

$app->get("/order/:idorder/", function($idorder){

	User::verifyLogin(false);

	$order = new Order();

	$order->get((int)$idorder);

	$page = new Page();

	$page->setTpl("payment", [
  'order'=>$order->getValues()

	]);
});

$app->get("/boleto/:idorder", function($idorder){

	User::verifyLogin(false);

	$order = new Order();

	$order->get((int)$idorder);

	// DADOS DO BOLETO PARA O SEU CLIENTE
$dias_de_prazo_para_pagamento = 10;
$taxa_boleto = 5.00;
$data_venc = date("d/m/Y", time() + ($dias_de_prazo_para_pagamento * 86400));  // Prazo de X dias OU informe data: "13/04/2006"; 
$valor_cobrado = $order->getvltotal(); // Valor - REGRA: Sem pontos na milhar e tanto faz com "." ou "," ou com 1 ou 2 ou sem casa decimal
$valor_cobrado = str_replace(",", ".",$valor_cobrado);
$valor_boleto=number_format($valor_cobrado+$taxa_boleto, 2, ',', '');

$dadosboleto["nosso_numero"] = $order->getidorder();  // Nosso numero - REGRA: Máximo de 8 caracteres!
$dadosboleto["numero_documento"] = $order->getidorder();	// Num do pedido ou nosso numero
$dadosboleto["data_vencimento"] = $data_venc; // Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA
$dadosboleto["data_documento"] = date("d/m/Y"); // Data de emissão do Boleto
$dadosboleto["data_processamento"] = date("d/m/Y"); // Data de processamento do boleto (opcional)
$dadosboleto["valor_boleto"] = $valor_boleto; 	// Valor do Boleto - REGRA: Com vírgula e sempre com duas casas depois da virgula

// DADOS DO SEU CLIENTE
$dadosboleto["sacado"] = $order->getdesperson();
$dadosboleto["endereco1"] = $order->getdesaddress() . " " .$order->getdesdistrict();
$dadosboleto["endereco2"] = $order->getdescity(). " - " .$order->getdesstate(). " - " .$order->getdescountry(). " -  CEP: " . $order->getdeszipcode();

// INFORMACOES PARA O CLIENTE
$dadosboleto["demonstrativo1"] = "Pagamento de Compra na Loja Hcode E-commerce";
$dadosboleto["demonstrativo2"] = "Taxa bancária - R$ 0,00";
$dadosboleto["demonstrativo3"] = "";
$dadosboleto["instrucoes1"] = "- Sr. Caixa, cobrar multa de 2% após o vencimento";
$dadosboleto["instrucoes2"] = "- Receber até 10 dias após o vencimento";
$dadosboleto["instrucoes3"] = "- Em caso de dúvidas entre em contato conosco: suporte@hcode.com.br";
$dadosboleto["instrucoes4"] = "&nbsp; Emitido pelo sistema Projeto Loja Hcode E-commerce - www.hcode.com.br";

// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE
$dadosboleto["quantidade"] = "";
$dadosboleto["valor_unitario"] = "";
$dadosboleto["aceite"] = "";		
$dadosboleto["especie"] = "R$";
$dadosboleto["especie_doc"] = "";


// ---------------------- DADOS FIXOS DE CONFIGURAÇÃO DO SEU BOLETO --------------- //


// DADOS DA SUA CONTA - ITAÚ
$dadosboleto["agencia"] = "1690"; // Num da agencia, sem digito
$dadosboleto["conta"] = "48781";	// Num da conta, sem digito
$dadosboleto["conta_dv"] = "2"; 	// Digito do Num da conta

// DADOS PERSONALIZADOS - ITAÚ
$dadosboleto["carteira"] = "175";  // Código da Carteira: pode ser 175, 174, 104, 109, 178, ou 157

// SEUS DADOS
$dadosboleto["identificacao"] = "Semi joias Passo Fundo";
$dadosboleto["cpf_cnpj"] = "24.700.731/0001-08";
$dadosboleto["endereco"] = "Rua PIO XI, 283 - Lucas Araújo, 99060020";
$dadosboleto["cidade_uf"] = "Passo Fundo - RS";
$dadosboleto["cedente"] = "SEMIJOIASPASSOFUNDO LTDA - ME";

// NÃO ALTERAR!
$path = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "res" . DIRECTORY_SEPARATOR . "boletophp" . DIRECTORY_SEPARATOR . "include" . DIRECTORY_SEPARATOR;
require_once($path . "funcoes_itau.php");
require_once($path . "layout_itau.php");
//include("include/funcoes_itau.php"); 
//include("include/layout_itau.php");



});

$app->get("/profile/orders/", function(){

	User::verifyLogin(false);

	$user = User::getFromSession();

	$page = new Page();

	$page->setTpl("profile-orders", [
    
    'orders'=>$user->getOrders()

	]);

});

$app->get("/profile/orders/:idorder/", function($idorder){

	User::verifyLogin(false);

	$order = new Order();

	$order->get((int)$idorder);

	$cart = new Cart();

	$cart->get((int)$order->getidcart());

	$cart->getCalculateTotal();

	$page = new Page();

	$page->setTpl("profile-orders-detail", [
    
    'order'=>$order->getValues(),
    'cart'=>$cart->getValues(),
    'products'=>$cart->getProducts()


	]);


});

$app->get("/profile/change-password/", function(){

	User::verifyLogin(false);

	$page = new Page();

	$page->setTpl("profile-change-password", [

		'changePassError'=>User::getError(),
		'changePassSuccess'=>User::getSuccess()


	]);
});

$app->post("/profile/change-password/", function(){

	User::verifyLogin(false);

	if(!isset($_POST['current_pass']) || $_POST['current_pass'] === ''){

		User::setError("Digite a senha atual.");
		header("Location: /profile/change-password/");
		exit;
	}
	if(!isset($_POST['new_pass']) || $_POST['new_pass'] === ''){

		User::setError("Digite a nova senha.");
		header("Location: /profile/change-password/");
		exit;
	}
	if(!isset($_POST['new_pass_confirm']) || $_POST['new_pass_confirm'] === ''){

		User::setError("Confirme a nova senha.");
		header("Location: /profile/change-password/");
		exit;
	}

	if($_POST['current_pass'] === $_POST['new_pass']){

		User::setError("A sua nova senha deve ser diferente da atual.");
		header("Location: /profile/change-password/");
		exit;
	}

	if($_POST['new_pass'] !== $_POST['new_pass_confirm']){

		User::setError("A senha de confirmação de ser igual a nova senha.");
		header("Location: /profile/change-password/");
		exit;
	}

     $user = User::getFromSession();

     if(!password_verify($_POST['current_pass'], $user->getdespassword())){

     	User::setError("A senha está invalida.");
		header("Location: /profile/change-password/");
		exit;

     }

     $user->setdespassword(User::getPasswordHash($_POST['new_pass']));

     $user->update();

     $_SESSION[User::SESSION] = $user->getValues();

     User::setSuccess("Senha alterada com sucesso");
     header("Location: /profile/change-password/");
		exit;


});
?>