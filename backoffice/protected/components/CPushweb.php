<?php
class CPushweb
{
	public static function send($settings = array(), $data=array())
	{
		 $provider = isset($settings['provider'])?$settings['provider']:'';
		 /*dump($settings);
		 dump($data);*/
		 
		 switch ($provider) {
		 	case "pusher":
		 		
		 		require_once 'pusher-push/vendor/autoload.php';
		 		$beamsClient = new \Pusher\PushNotifications\PushNotifications(array(
				  "instanceId" => isset($settings['pusher_instance_id'])?$settings['pusher_instance_id']:'',
				  "secretKey" => isset($settings['pusher_primary_key'])?$settings['pusher_primary_key']:'',
				));
				
				$resp = $beamsClient->publishToInterests(
				  array( isset($settings['channel'])?$settings['channel']:'' ),
				  array("web" => 
				      array("notification" => $data
				  ),
				));				
				$resp = (array)$resp;				
				if(isset($resp['publishId'])){
					return $resp['publishId'];
				}
				throw new Exception( isset($resp[0])?$resp[0]:$resp );		 				 		
		 		break;		
		 		
		 	case "onesignal":
		 		
		 		$fields = array(
			        'app_id' => isset($settings['onesignal_app_id'])?$settings['onesignal_app_id']:'' ,
			        'include_player_ids' => isset($data['device_ids'])?(array)$data['device_ids']:array() ,	 
			        //'included_segments'=>array('Subscribed Users'),
			        'data' => array(),
			        'contents'=> array('en'=> isset($data['title'])?$data['title']:'' ),	 
			        'headings'=> array('en'=> isset($data['body'])?$data['body']:'' ),	 			      
			    );			    
			    $ch = curl_init();
			    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
			    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			        'Content-Type: application/json; charset=utf-8',
			        'Authorization: Basic '.$settings['onesignal_rest_apikey']
			    ));
			    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			    curl_setopt($ch, CURLOPT_HEADER, FALSE);
			    curl_setopt($ch, CURLOPT_POST, TRUE);
			    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
			    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			    
			    $resp = curl_exec($ch);
			    if (curl_errno($ch)) {				    
				    throw new Exception( 'Error:' . curl_error($ch) );
				}
				/*dump($fields);
				dump($resp);*/
			    curl_close($ch);			    
			    if($json=json_decode($resp,true)){			    	
			    	$id = isset($json['id'])?$json['id']:'';
			    	$errors = isset($json['errors'])?$json['errors']:'';
			    	if(!empty($id)){
			    		return $id;
			    	} else {
			    		if(is_array($errors) && count($errors)>=1){
			    			foreach ($errors as $error) {
			    				$error.=$error."\n";
			    			}			    			
			    			throw new Exception($error);
			    		}
			    	}
			    }
			    throw new Exception( json_encode($resp) );				 				 		
		 		break;
		 }
		 
		 throw new Exception( 'Error cannot send message' );
	}	
}
/*end class*/