<?php

namespace Projeto\Model;
use \Projeto\DB\Sql;
use \Projeto\Model;
use \Projeto\Mailer;

class Category extends Model{

		
	public static function listAll(){

		$sql = new Sql();

		//Realizando um Join com a tabela pessoa
		return $sql->select("SELECT * FROM tb_categories ORDER BY descategory");
	}

	public function save(){

		$sql = new Sql();

		$results = $sql->select("CALL sp_categories_save(:idcategory, :descategory)", array(

        ":idcategory"=>$this->getidcategory(),
        ":descategory"=>$this->getdescategory()

		));

		$this->setData($results[0]);

		 //Chamada da função pra alteração do menu categories dinamico
		Category::updateFile();
	}

	public function get($idcategory){

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_categories WHERE idcategory = :idcategory", [

			':idcategory'=>$idcategory

		]);

        $this->setData($results[0]);
	}

	public function delete(){


		$sql = new Sql();

		$sql->query("DELETE FROM tb_categories WHERE idcategory = :idcategory", [

			':idcategory'=>$this->getidcategory()

		]);
        
        //Chamada da função pra alteração do menu categories dinamico
		Category::updateFile();
	}

	//Metodo de modificação para o menu categorias dinamico

	public static function updateFile(){

        //Traz todas as categorias que tem no banco de dados pelo metodo listAll
		$categories = Category::listAll();

		/*<li><a href="#">Categoria Um</a></li>
         Repetir esse trecho dacima la no arquivo html de categories-menu
		*/

         $html = [];// array vazio, necessário para o array_push no primeiro parametro não emitir aviso que é necessário um array.

         foreach ($categories as $row) {
         	//adiciona elementos no final do array, primeiro parametro adiciona array de entrada, segundo paramentro valor a ser add no final do array
         	array_push($html, '<li><a href="/categories/'.$row['idcategory'].'/">'.$row['descategory'].'</a></li>');
         }

         //Preciso gravar os dados no arquivo e salvar usando a função abaixo igualzito fopen, variavel html é um array precisa converter pra string usando implode pra gravar os dados no arquivo
         file_put_contents($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "categories-menu.html", implode('', $html));


         //Após isso fazer a chamada da função nas outras funções acima, save e delete que alteram as categorias
	}

	//Metodo que traga todos os produtos
	public function getProducts($related = true){

		$sql = new Sql();

		if($related === true){

		 return	$sql->select("SELECT * FROM tb_products WHERE idproduct IN(
                              SELECT a.idproduct
                              FROM tb_products  a
                              INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct 
                              WHERE b.idcategory = :idcategory); 
                        ", [
                          ':idcategory'=>$this->getidcategory()
                        ]);

		}else{

			return $sql->select("SELECT * FROM tb_products WHERE idproduct NOT IN(
                              SELECT a.idproduct
                              FROM tb_products  a
                              INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct 
                              WHERE b.idcategory = :idcategory); 
                        ", [

                          'idcategory'=>$this->getidcategory()
                        ]);

		}
	}

	public function getProductsPage($page = 1, $itemsPerPage = 3){

		$start = ($page -1) * $itemsPerPage;

		$sql = new Sql();

		$results = $sql->select(" 
			SELECT SQL_CALC_FOUND_ROWS *
			FROM tb_products a 
			INNER JOIN tb_productscategories b ON a.idproduct = b.idproduct
			INNER JOIN tb_categories c ON c.idcategory = b.idcategory
			WHERE c.idcategory = :idcategory
			LIMIT $start, $itemsPerPage;

			", [
                ':idcategory'=>$this->getidcategory()

			]);

		$resultTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal;");

		return [

			'data'=>Product::checklist($results),
			'total'=>(int)$resultTotal[0]["nrtotal"],
			'pages'=>ceil($resultTotal[0]["nrtotal"] / $itemsPerPage)
		];
	}


	public function addProduct(Product $product){

		$sql = new Sql();

		$sql->query("INSERT INTO tb_productscategories (idcategory, idproduct) VALUES (:idcategory, :idproduct)", [
         ':idcategory'=>$this->getidcategory(),
         ':idproduct'=>$product->getidproduct()

		]);
	}

	public function removeProduct(Product $product){

		$sql = new Sql();

		$sql->query("DELETE FROM tb_productscategories WHERE idcategory = :idcategory AND idproduct = :idproduct", [
         ':idcategory'=>$this->getidcategory(),
         ':idproduct'=>$product->getidproduct()

		]);
	}

	public static function getPage($page = 1, $itemsPerPage = 10){

		$start = ($page -1) * $itemsPerPage;

		$sql = new Sql();

		$results = $sql->select(" 
			SELECT SQL_CALC_FOUND_ROWS *
			FROM tb_categories 
			ORDER BY descategory
			LIMIT $start, $itemsPerPage;

			");

		$resultTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal;");

		return [

			'data'=>$results,
			'total'=>(int)$resultTotal[0]["nrtotal"],
			'pages'=>ceil($resultTotal[0]["nrtotal"] / $itemsPerPage)
		];
	}

	public static function getPageSearch($search, $page = 1, $itemsPerPage = 10){

		$start = ($page -1) * $itemsPerPage;

		$sql = new Sql();

		$results = $sql->select(" 
			SELECT SQL_CALC_FOUND_ROWS *
			FROM tb_categories 
			WHERE descategory LIKE :search 
			ORDER BY descategory
			LIMIT $start, $itemsPerPage;
         ", [
               ':search'=>'%'.$search.'%'

         ]);

		$resultTotal = $sql->select("SELECT FOUND_ROWS() AS nrtotal;");

		return [

			'data'=>$results,
			'total'=>(int)$resultTotal[0]["nrtotal"],
			'pages'=>ceil($resultTotal[0]["nrtotal"] / $itemsPerPage)
		];
	}






	

}//fim da classe Category

?>