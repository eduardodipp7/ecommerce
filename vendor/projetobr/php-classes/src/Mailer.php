<?php

namespace Projeto;

use Rain\Tpl;

class Mailer{

	const USERNAME = "eduardo.dipp7@gmail.com";
	const PASSWORD = "xxxxxxx";
	const NAME_FROM = "Semi joias Passo Fundo";

	private $mail;

	public function __construct($toAddress, $toName, $subject, $tplName, $data = array()){


		$config = array(
					"tpl_dir"       => $_SERVER["DOCUMENT_ROOT"] ."/views/email/", //Apartir da pasta do projeto root[DOCUMENT_ROOT] procure a pasta onde se encontra os templats 
					"cache_dir"     => $_SERVER["DOCUMENT_ROOT"] ."/views-cache/",
					"debug"         => false // set to false to improve the speed
				   );

	Tpl::configure( $config );

      $tpl = new Tpl;


      foreach ($data as $key => $value) {
      	
      	$tpl->assign($key, $value);
      }

      $html = $tpl->draw($tplName, true);

        // Instância do objeto PHPMailer
		$this->mail = new \PHPMailer;
        // Configura para envio de e-mails usando SMTP
		$this->mail->isSMTP();

		$this->mail->SMTPDebug = 0;

		$this->mail->Debugoutput = 'html';
        // Servidor SMTP
		$this->mail->Host = 'smtp.gmail.com';
        // Porta do servidor SMTP
		$this->mail->Port = 587;
        // Tipo de encriptação que será usado na conexão SMTP
		$this->mail->SMTPSecure = 'tls';
        // Usar autenticação SMTP
		$this->mail->SMTPAuth = true;
        // Usuário da conta
		$this->mail->Username = Mailer::USERNAME;
        // Senha da conta
		$this->mail->Password = Mailer::PASSWORD;
        // Email do Remetente e o nome
		$this->mail->setFrom(Mailer::USERNAME, MAILER::NAME_FROM);
        // Endereço do e-mail do destinatário
		$this->mail->addAddress($toAddress, $toName);
        // Assunto do e-mail
		$this->mail->subject = $subject;
        // Informa se vamos enviar mensagens usando HTML
		$this->mail->msgHTML($html);
        // Mensagem que vai no corpo do e-mail
		$this->mail->AltBody = 'This is a plain-text message body';

		
	}

	public function send(){

		return $this->mail->send();
	}
}

?>