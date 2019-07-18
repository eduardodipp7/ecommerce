<?php

use \Projeto\Page;
use \Projeto\Model\Product;
use \Projeto\Model\Category;
use \Projeto\Model\Cart;


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
     'products'=>$cart->getProducts()
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

?>