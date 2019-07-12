<?php

use \Projeto\Page;
use \Projeto\Model\Product;
use \Projeto\Model\Category;


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


	$category = new Category();

	$category->get((int)$idcategory);

	//criando objeto da classe Page pra acessar as informaçoes
	$page = new Page();//nessa hora ele vai chamar o metodo construct e colocar o header na tela

	$page->setTpl("category", [
     'category'=>$category->getValues(),
     'products'=>Product::checklist($category->getProducts())

	]);
});

?>