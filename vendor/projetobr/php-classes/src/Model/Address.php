<?php

namespace Projeto\Model;

use \Projeto\DB\Sql;
use \Projeto\Model;


class Address extends Model {

	const SESSION_ERROR = "AddressError";

	public static function getCEP($nrcep){

		//Retirar os traços do CEP
		$nrcep = str_replace("-", "", $nrcep);

		//informar ao php que vamos rasterar um URL
        $ch = curl_init();

        //chamada da url
        curl_setopt($ch, CURLOPT_URL, "http://viacep.com.br/ws/$nrcep/json/");

        //Retorno das informações que será passada pra nós, será um processo assincrono mas totalmente dependente
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);//se não colocar essa opção por padrão vem true via ssl

        //Pegar o retorno das informações(com curl_exec), usando json_decode e serialzar usando true no final
        $data = json_decode(curl_exec($ch), true);

        //fecha o ponteiro
        curl_close($ch);

        return $data;
	}

	//Criando metodo para conversão de nomes para informar no banco de dados pois pelo webservice as informaçoes tem nomes diferentes

	public function loadFromCEP($nrcep){

		$data = Address::getCEP($nrcep);

			if (isset($data['logradouro']) && $data['logradouro']) {
			
				$this->setdesaddress($data['logradouro']);	
				$this->setdescomplement($data['complemento']);
				$this->setdesdistrict($data['bairro']);
				$this->setdescity($data['localidade']);
				$this->setdesstate($data['uf']);
				$this->setdescountry('Brasil');
				$this->setdeszipcode($nrcep);		


		}

	
	}

	public function save(){

			$sql = new Sql();
			$results = $sql->select("CALL sp_addresses_save(:idaddress, :idperson, :desaddress, :desnumber, :descomplement, :descity, :desstate, :descountry, :deszipcode, :desdistrict)", [
			':idaddress'=>$this->getidaddress(),
			':idperson'=>$this->getidperson(),
			':desaddress'=>$this->getdesaddress(),
			':desnumber'=>$this->getdesnumber(),
			':descomplement'=>$this->getdescomplement(),
			':descity'=>$this->getdescity(),
			':desstate'=>$this->getdesstate(),
			':descountry'=>$this->getdescountry(),
			':deszipcode'=>$this->getdeszipcode(),
			':desdistrict'=>$this->getdesdistrict()
			]);
			
			if (count($results) > 0) {

			$this->setData($results[0]);
			}
		}

		public static function setMsgError($msg)
		{
			$_SESSION[Address::SESSION_ERROR] = $msg;
		}
		public static function getMsgError()
		{
			$msg = (isset($_SESSION[Address::SESSION_ERROR])) ? $_SESSION[Address::SESSION_ERROR] : "";
			Address::clearMsgError();
			return $msg;
		}
		public static function clearMsgError()
		{
			$_SESSION[Address::SESSION_ERROR] = NULL;
		}

	
}
?>