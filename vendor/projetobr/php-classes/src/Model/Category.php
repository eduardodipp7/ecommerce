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
	}


	

}//fim da classe Category

?>