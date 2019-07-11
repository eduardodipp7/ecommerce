<?php

use \Projeto\PageAdmin;
use \Projeto\Model\User;
use \Projeto\Model\Product;

//ROTA PRINCIPAL PRODUTOS
$app->get("/admin/products/", function(){

	User::verifyLogin();

	$products = Product::listAll();

	$page = new PageAdmin();

	$page->setTpl("products", [
   'products'=>$products
	]);
});

//ROTA DA CRIACAO DOS PRODUTOS
$app->get("/admin/products/create/", function(){

	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("products-create");

});

$app->post("/admin/products/create/", function(){

	User::verifyLogin();

    $product = new Product();

    $product->setData($_POST);

    $product->save();

    header("Location: /admin/products/");
    exit;

});

//CRIAR A ROTA DO EDITAR PRODUCT
$app->get("/admin/products/:idproduct/", function($idproduct){

	User::verifyLogin();

	$product = new Product();

	$product->get((int)$idproduct);

	$page = new PageAdmin();

	$page->setTpl("products-update", [
    'product'=>$product->getValues()
	]);

});

$app->post("/admin/products/:idproduct/", function($idproduct){

    User::verifyLogin();

	$product = new Product();

	$product->get((int)$idproduct);

	$product->setData($_POST);

	$product->save();

     //ESSA VERIFICAÇÃO ELIMINA O ERRO, acessamos o arquivo e verificamos se o seu nome é diferente de vazio. Isso só será possivel se um arquivo for enviado. Com essa verificação elimina o erro de não encontrar a imagem
	if($_FILES["file"]["name"] !== "") $product->setPhoto($_FILES["file"]);

	header('Location: /admin/products');
	exit;

});

$app->get("/admin/products/:idproduct/delete/", function($idproduct){

	User::verifyLogin();

	$product = new Product();

	$product->get((int)$idproduct);

	$product->delete();

	header('Location: /admin/products');
	exit;


});


?>