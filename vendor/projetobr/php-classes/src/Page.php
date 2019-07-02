<?php

//Especificando onde está classe("Projeto")
namespace Projeto;
//Utilizando o próprio namespace do Rain TPL
use Rain\Tpl;


class Page{

	      private $tpl;
	      private $options = [];
          private $defaults = [
          	"data" => [] //nossos dados vai estar nessa chave data
          ];

	public function __construct($opts = array(), $tpl_dir = "/views/"){

        //Sobscreve array defaults com os conteudos do array opts, ou seja, recebe os valores do construct, sempre vai prevalecer opts e guarda na variavel options
        $this->options = array_merge($this->defaults, $opts);

		$config = array(
					"tpl_dir"       => $_SERVER["DOCUMENT_ROOT"] .$tpl_dir, //Apartir da pasta do projeto root[DOCUMENT_ROOT] procure a pasta onde se encontra os templats 
					"cache_dir"     => $_SERVER["DOCUMENT_ROOT"] ."/views-cache/",
					"debug"         => false // set to false to improve the speed
				   );

	Tpl::configure( $config );//Classe Tpl recebe as configurações acima do nosso template

       
       //criace um objeto da classe Tpl nosso template
      $this->tpl = new Tpl();
        
        //Vamos pegar as informações da chave e o valor de data
       //foreach ($this->options["data"] as $key => $value) {
       //$this->tpl->assign($key, $value); //metodo assign do template espera uma chave e um valor, ou seja, pra atribuição de variaveis que vão aparecer no template em chaves {$titulo} ele vai pegar a chave e o valor dela
       //}//fim do foreach

      $this->setData($this->options["data"]);//diminuimos o codigo acima do foreach em uma linha só usando o metodo foreach do setData

       //Desenhando nosso template
       $this->tpl->draw("header");


	}//fim do metodo magico construct
 

    //METODO PAR REUTILIZAÇÃO DO LAÇO FOREACH
 	public function setData($data = array()){

		foreach ($data as $key => $value) {

        $this->tpl->assign($key, $value);

		}//fim do foreach

	}


	//METODO DO CONTEUDO HTML DA PAGINA 
	public function setTpl($name, $data = array(), $returnHTML = false){

		//Vamos pegar os dados que está na variavel data e chamar o assing e fazer uma por uma usando foreach. Porém vamos repetir codigo vamos criar outro metodo setData usando foreach para poder reutilizar o codigo acima.

		$this->setData($data);

		//vamos desenhar o template na tela
		return $this->tpl->draw($name, $returnHTML);


	}


	public function __destruct(){

     //após destruir da memória metodo acima mostra o footer
		$this->tpl->draw("footer");

	}//fim do metodo magico destruct


}//fim da classe page

?>