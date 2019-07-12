<?php

namespace Projeto\Model;
use \Projeto\DB\Sql;
use \Projeto\Model;
use \Projeto\Mailer;

class Product extends Model{

		
	public static function listAll(){

		$sql = new Sql();

		//Realizando um Join com a tabela pessoa
		return $sql->select("SELECT * FROM tb_products ORDER BY desproduct");
	}

	public static function checklist($list){
              
              //Verifica no getValues se a foto existe ou não pra se inserida no site 
              foreach ($list as &$row) {
              	
              	$p = new Product();
              	$p->setData($row);
              	$row = $p->getValues();
              }
               
               //Retorna cada produto já formatado
              return $list;
	}


	public function save(){

		$sql = new Sql();

		$results = $sql->select("CALL sp_products_save(:idproduct, :desproduct, :vlprice, :vlwidth, :vlheight, :vllength, :vlweight, :desurl)", array(

        ":idproduct"=>$this->getidproduct(),
        ":desproduct"=>$this->getdesproduct(),
        ":vlprice"=>$this->getvlprice(),
        ":vlwidth"=>$this->getvlwidth(),
        ":vlheight"=>$this->getvlheight(),
        ":vllength"=>$this->getvllength(),
        ":vlweight"=>$this->getvlweight(),
        ":desurl"=>$this->getdesurl()


		));

		$this->setData($results[0]);

	}

	public function get($idproduct){

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_products WHERE idproduct = :idproduct", [

			':idproduct'=>$idproduct

		]);

        $this->setData($results[0]);
	}

	public function delete(){


		$sql = new Sql();

		$sql->query("DELETE FROM tb_products WHERE idproduct = :idproduct", [

			':idproduct'=>$this->getidproduct()

		]);
        
	}

	public function checkPhoto(){

		if(file_exists($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 
			"res" . DIRECTORY_SEPARATOR . 
			"site" . DIRECTORY_SEPARATOR . 
			"img" . DIRECTORY_SEPARATOR . 
			"products" . DIRECTORY_SEPARATOR . 
		    $this->getidproduct() . ".jpg"
		    )){
            //caso exista 
			$url = "/res/site/img/products/" . $this->getidproduct() . ".jpg";
		}else{

			$url =  "/res/site/img/product.jpg";
		}

		return $this->setdesphoto($url);
	}

	public function getValues(){

		$this->checkPhoto();

		$values = parent::getValues();

		return $values;
	}

	public function setPhoto($file){


		$extension = explode('.', $file['name']);
		$extension = end($extension);


		switch ($extension) {
			case 'jpg':

			case 'jpeg':
				$image = imagecreatefromjpeg($file["tmp_name"]);
				break;

			case 'gif':
				$image = imagecreatefromgif($file["tmp_name"]);
				break;
			
			case 'png':
				$image = imagecreatefrompng($file["tmp_name"]);
				break;
		}

		$dist = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 
			"res" . DIRECTORY_SEPARATOR . 
			"site" . DIRECTORY_SEPARATOR . 
			"img" . DIRECTORY_SEPARATOR . 
			"products" . DIRECTORY_SEPARATOR . 
		    $this->getidproduct() . ".jpg";

		    imagejpeg($image, $dist);

		    imagedestroy($image);

		    $this->checkPhoto();
	}


	

}//fim da classe Product
?>