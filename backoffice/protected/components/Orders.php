<?php
class Orders extends CComponent
{
	public static function getHistory($token_id='')
	{
		$stmt="
		SELECT 
		a.order_id,a.status,a.remarks,a.remarks2,a.remarks_args,a.date_created,
		b.order_id
		FROM {{order_history}} a
		LEFT JOIN {{order}} b
		ON
		a.order_id = b.order_id
		WHERE 
		b.order_id_token=".q($token_id)."
		ORDER BY id DESC
		";		
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){
			return $res;
		}
		throw new Exception( 'Order not found' );
	}
	
	public static function RecentOrder($limit=5, $merchant_id=0)
	{
		$and = "";
		if($merchant_id>0){
			$and.=" AND a.merchant_id =".q( (integer) $merchant_id )." ";
		}
		
		$stmt = "
		SELECT a.order_id,a.order_id_token,a.client_id , a.merchant_id,a.status,
		concat(b.first_name,' ',b.last_name) as customer_name,
		concat(b.street,' ',b.city,' ',b.state,' ',b.zipcode ) as customer_address,
		c.restaurant_name as merchant_name,
		client.avatar, c.logo as merchant_logo,
		
		(
		 select count(*) from {{order_details}}
		 where order_id = a.order_id
		) as total_items,
		
		 IFNULL((
		 select description_trans from {{view_order_status}}
		 where
		 description=a.status 
		 and language=".q(Yii::app()->language)."
		 ),a.status) as status_title
		
		FROM {{order}} a
		LEFT JOIN {{order_delivery_address}} b
		ON
		a.order_id = b.order_id
		
		LEFT JOIN {{merchant}} c
		ON
		a.merchant_id = c.merchant_id
		
		LEFT JOIN {{client}} client
		ON
		a.client_id = client.client_id
		
		WHERE
		a.status NOT IN (".q(AttributesTools::initialStatus()).")
		$and
		ORDER BY a.date_created DESC
		LIMIT 0,$limit
		";		
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){		
			return $res;
		}		
		return false;
	}
}
/*end class*/