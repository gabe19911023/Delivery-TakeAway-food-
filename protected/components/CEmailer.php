<?php
require 'mailjet/vendor/autoload.php';
require 'mailgun/vendor/autoload.php';
use \Mailjet\Resources;
use Mailgun\Mailgun;
					
class CEmailer
{
	private static $sender;
	private static $provider;
	private static $to;
	private static $to_name;
	private static $subject;
	private static $body;
	private static $model;
	
	public static function init()
	{		
		self::$model = AR_email_provider::model()->find('as_default=:as_default', 
		array(':as_default'=>1)); 		
		if(self::$model){
			self::$sender = self::$model->sender;
		    self::$provider = self::$model->provider_id;
		    return true;
		}
		throw new Exception( 'no default email provider' );
	}
	
	public static function setTo($to)
	{		
		self::$to = $to;
	}
	
	public static function setName($name='')
	{		
		self::$to_name = $name;
	}
	
	public static function setSubject($subject='')
	{
		self::$subject = $subject;
	}
	
	public static function setBody($body='')
	{
		self::$body = $body;
	}
	
	public static function send()
	{				
		$model = self::$model;
		$error = ''; $success = '';
				
		try {
					
			switch (self::$provider) {
				
				case "mailgun":					
																    
				    $ch = curl_init();				
					curl_setopt($ch, CURLOPT_URL, 'https://api.mailgun.net/v3/'.$model->smtp_host.'/messages');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_POST, 1);
					$post = array(
					    'from' =>$model->sender,					    
					    'to' => self::$to_name." ".self::$to,
					    'subject' => self::$subject,
					    'html' => self::$body
					);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
					curl_setopt($ch, CURLOPT_USERPWD, 'api' . ':' . $model->api_key );
					
					$result = curl_exec($ch);
					if (curl_errno($ch)) {
					    $error = 'Error:' . curl_error($ch);
					}
					curl_close($ch);
					
					if ($json = json_decode($result,true)){						
						if(isset($json['id'])){
							$success = "sent";
						} else $error = isset($json['message'])?$json['message']:'invalid response';
					} else $error = "invalid response";
				
					break;
				
				case "elastic":				   
	    		   $params = array(
	    		      'from' => $model->sender,
				      'fromName' => $model->sender_name,
				      'apikey' => $model->api_key,
				      'subject' => self::$subject,
				      'to' => self::$to,
				      'bodyHtml' => self::$body,
				      'isTransactional' => false
				   );				    
				   $options = array(
	                  'http' => array(
	                          'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
	                          'method'  => 'POST',
	                          'content' => http_build_query($params)
	                   )
	               );	               
	               $context  = stream_context_create($options);
                   $resp = file_get_contents("https://api.elasticemail.com/v2/email/send", false, $context);
                   if ($resp === FALSE) {
                   	   $error = "error occurred";
                   } else {
                   	  $elastic_results=json_decode($resp,true);               	   
	               	   if(is_array($elastic_results) && count($elastic_results)>=1){
		               	   if ($elastic_results['success']==1){
		               	   	   $success="sent";
		               	   } else $error = $elastic_results['error'];
	               	   } else $error = t("error occurred");
                   }
				   break;
					
				case "mailjet":		
				     $mj = new \Mailjet\Client($model->api_key,$model->secret_key,true,['version' => 'v3.1']);
				     
				     $body = [
					    'Messages' => [
					      [
					        'From' => [
					          'Email' => $model->sender,
					          'Name' => $model->sender_name
					        ],
					        'To' => [
					          [
					            'Email' => self::$to,
					            'Name' => CEmailer::$to_name
					          ]
					        ],
					        'Subject' => self::$subject,
					        'HTMLPart' => self::$body
					      ]
					    ]
					  ];					  
					  $response = $mj->post(Resources::$Email, ['body' => $body]);				      
				      if($response->success()){
				      	 $success = "sent";
				      } else $error = self::parseError($response->getData());				      
					  
					break;
				
				case "sendgrid":
										
					require 'sendgrid/vendor/autoload.php';
					$email = new \SendGrid\Mail\Mail();
					$email->setFrom($model->sender, $model->sender_name);
					$email->setSubject(self::$subject);
					$email->addTo(self::$to, CEmailer::$to_name );
					$email->addContent(
					    "text/html", self::$body
					);					
					$sendgrid = new \SendGrid($model->api_key);
					try {
						$response = $sendgrid->send($email);
						$resp_code = $response->statusCode();						
						if($resp_code==202 ){
							$success = "sent";
						} else $error = $response->body();							
					} catch (Exception $e) {
						$error =  $e->getMessage();
					}					
					break;
					
				case "smtp":						
					$mail = new YiiMailer();	
					$mail->clearLayout();	
					$mail->setSmtp($model->smtp_host, $model->smtp_port, $model->smtp_secure, true, 
					$model->smtp_username, $model->smtp_password);
										
					$mail->setFrom($model->sender ,  $model->sender_name );
					$mail->setTo(self::$to);
					$mail->setSubject(self::$subject);
					$mail->setBody(self::$body);
					if ($mail->send()) {
					    $success = "sent";
					} else $error = $mail->getError();					
					break;
			
				default:				    
					$mail = new YiiMailer();		
					$mail->clearLayout();			
					$mail->setFrom($model->sender ,  $model->sender_name );
					$mail->setTo(self::$to);
					$mail->setSubject(self::$subject);
					$mail->setBody(self::$body);
					if ($mail->send()) {
					    $success = "sent";
					} else $error = $mail->getError();	
					break;
			}
						
		} catch (Exception $e) {
		   $error = $e->getMessage();		  
		}		
					
		$log = new AR_email_logs;
		$log->email_address = self::$to;
		$log->sender = $model->sender;
		$log->subject = self::$subject;
		$log->content = self::$body;
		$log->status = !empty($success)? CommonUtility::cutString($success) : CommonUtility::cutString($error) ;
		$log->email_provider = self::$provider;
		if(!$log->save()){			
			$error.=CommonUtility::parseModelErrorToString( $log->getErrors() );
		} 
		
		if($success=="sent"){
			return true;
		} else throw new Exception( $error );
	}
	
	public static function parseError($error)
	{
		$error_colection = '';		
		if(is_array($error) && count($error)>=1){
			if(isset($error['ErrorMessage'])){
				return $error['ErrorMessage'];
			} elseif ($error['Messages']){
				foreach ($error['Messages'] as $item) {
					foreach ($item['Errors'] as $item_error) {
						$error_colection.= $item_error['ErrorMessage'];
					}
				}
				return $error_colection;
			}
		}
		return t("undefined error");
	}
		
}
/*end class*/