<?php

namespace Projeto\Model;
use \Projeto\DB\Sql;
use \Projeto\Model;

class User extends Model{

	const SESSION = "User";

	public static function login($login, $password){

		//vamos pegar no banco e verificar se esse login e senha existe realmente. A senha vamos comparar pelo hash

		$sql =  new Sql();

		$results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", array(

                ":LOGIN"=>$login

		));

		if(count($results) === 0){
			//coloca o barra em Expetion pra pegar da raiz
			throw new \Exception("Usuário inexistente ou senha inválida");
			
		}
         
        //caso passe pela validação acima armazena a informação na variavel data
		$data = $results[0];
	    
	    //Verificar a senha do usuario

	    if(password_verify($password, $data["despassword"]) === true) 
	    {
	    	$user = new User();

	    	$user->setData($data);

	    	$_SESSION[User::SESSION] = $user->getValues();

	    	return $user;


	    }else{

	    	throw new \Exception("Usuário inexistente ou senha inválida");

	    }//fim do else


	}//fim do metodo statico login

	public static function verifyLogin($inadmin = true){

		if(
			!isset($_SESSION[User::SESSION]) || !$_SESSION[User::SESSION] || !(int)$_SESSION[User::SESSION]["iduser"] > 0 
			|| (bool)$_SESSION[User::SESSION]["inadmin"] !== $inadmin
		){

			header("Location: /admin/login/");
			exit;

		}
	}//fim do metodo verifyLogin

	public static function logout(){

		$_SESSION[User::SESSION] = NULL;
	}



}//fim da classe User

?>