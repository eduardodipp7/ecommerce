<?php


namespace Projeto;

class Model{

	private $values = [];
    
    public function __call($name, $args){

       //verifica se é um method get ou set
       $method = substr($name, 0, 3);
       $fieldName = substr($name, 3, strlen($name));

       //var_dump($method, $fieldName);
       //exit;

       switch ($method) {
       	case 'get':
       		return $this->values[$fieldName];
       		break;

        case 'set':
       		$this->values[$fieldName] = $args[0]; // passa o codigo do usuario, ou seja os argumentos da função
       		break;
       }

    }//fim metodo magico call

    //
    public function setData($data = array()){

    	foreach ($data as $key => $value) {
    		 
    		 $this->{"set".$key}($value);

    	}
    }

    public function getValues(){

    	return $this->values;
    }

}//fim da classe model

?>