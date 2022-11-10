<?php
class CNotifier
{
	public static function send($provider='',$settings = array(), $data=array())
	{
		switch ($provider) {
			case "pusher":
				require_once 'pusher/vendor/autoload.php';
				$options = array(
				    'cluster' => isset($settings['cluster'])?$settings['cluster']:'',
				    'useTLS' => true
				);				
				$pusher = new Pusher\Pusher(
				    isset($settings['key'])?$settings['key']:'',
				    isset($settings['secret'])?$settings['secret']:'',
				    isset($settings['app_id'])?$settings['app_id']:'',
				    $options
				);			
				$resp = $pusher->trigger( 
				isset($settings['notication_channel'])?$settings['notication_channel']:''
				, 
				isset($settings['notification_event'])?$settings['notification_event']:''
				, $data);			
					
				return true;
				
				break;					
				
			case "ably":	
			    require_once 'ably/vendor/autoload.php';
				$client = new Ably\AblyRest($settings['ably_apikey']);
				$channel = $client->channel('admin-channel');
				$resp = $channel->publish('notification-event', $data ); // => true
				return true;
			   break;
			   
			case "piesocket":   
			
			    $curl = curl_init();
			    			    
				$post_fields = [
				    "key" => isset($settings['piesocket_api_key'])?$settings['piesocket_api_key']:'',
				    "secret" => isset($settings['piesocket_api_secret'])?$settings['piesocket_api_secret']:'',
				    "channelId" => isset($settings['notication_channel'])?$settings['notication_channel']:'',
				    "message" => array(
				      'event'=>isset($settings['notification_event'])?$settings['notification_event']:'',
				      'data'=>$data
				    )
				];
				
				$cluster = isset($settings['piesocket_clusterid'])?$settings['piesocket_clusterid']:'';
				
				curl_setopt_array($curl, array(
				  CURLOPT_URL => "https://".$cluster.".piesocket.com/api/publish",
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_CUSTOMREQUEST => "POST",
				  CURLOPT_POSTFIELDS => json_encode($post_fields),
				  CURLOPT_HTTPHEADER => array(
				    "Content-Type: application/json"
				  ),
				));
				
				$response = curl_exec($curl);
				return $response;
			
			   break;
		}
		throw new Exception( 'Error cannot send message' );
	}
}
/*end class*/