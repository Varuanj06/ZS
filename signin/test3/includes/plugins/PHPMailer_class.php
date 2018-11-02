<?php
require 'PHPMailer/PHPMailerAutoload.php';

class Mail{

	private $mail;

	public function __construct(){

		/* init PHPMailer */
	 	$this->mail 				= new PHPMailer;

	 	/* SMTP configurations */
	 	$this->mail->IsSMTP();                                        // Set mailer to use SMTP
		$this->mail->Host 			= 'smtp.sendgrid.net';         	// Specify main and backup server
		$this->mail->Port 			= 587;                            // Set the SMTP port
		$this->mail->SMTPAuth 		= true;                           // Enable SMTP authentication
		$this->mail->Username 		= 'miracas';         // SMTP username
		$this->mail->Password 		= 'sreelaj117';       // SMTP password, (The API key, not your Mandrill password)
		$this->mail->SMTPSecure 	= 'tls';                          // Enable encryption, 'ssl' also accepted

		/* Who is sending the email? */
		$this->mail->From 			= 'care@miracas.com';
		$this->mail->FromName 		= 'Miracas';

	} 

	public function send_mail($email, $subject, $body, $alt_body){

		$this->mail->AddAddress	($email);          // Add a recipient
		$this->mail->IsHTML		(true);                      // Set email format to HTML

		$this->mail->Subject 	= $subject;
		$this->mail->Body    	= $body;
		$this->mail->AltBody 	= $alt_body;

		if(!$this->mail->Send()) {
		   echo 'Mailer Error: ' . $this->mail->ErrorInfo;
		   //exit;
		   return "error";
		}else{
			return "sent";
		}

	}

}