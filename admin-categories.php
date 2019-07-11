<?php

use \Projeto\PageAdmin;
use \Projeto\Model\User;
use \Projeto\Model\Category;

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

//CRIANDO ROTA PRO MENU CATEGORIAS DO SITE
$app->get("/categories/:idcategory/", function($idcategory){

	$category = new Category();

	$category->get((int)$idcategory);

	//criando objeto da classe Page pra acessar as informaçoes
	$page = new Page();//nessa hora ele vai chamar o metodo construct e colocar o header na tela

	$page->setTpl("category", [
     'category'=>$category->getValues(),
     'products'=>[]

	]);
});


?>