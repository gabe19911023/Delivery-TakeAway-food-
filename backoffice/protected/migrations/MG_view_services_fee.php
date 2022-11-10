<?php
class MG_view_services_fee extends CDbMigration
{
	public function up()
	{
		if($check = Yii::app()->db->schema->getTable("{{view_services_fee}}")){
			if($schema = Yii::app()->db->createCommand("SELECT * FROM information_schema.tables 
			where table_name  LIKE '%{{view_services_fee}}%'
             ; ")->queryRow()){				
				if(strtolower($schema['TABLE_TYPE'])!="view"){
					$this->dropTable("{{view_services_fee}}");
				}				
			}
		}
		
		$stmt="		
		CREATE OR REPLACE VIEW {{view_services_fee}} as
    	select
		a.merchant_id,
		b.restaurant_name,
		b.merchant_type,
		a.meta_value as service_code,
		
		IFNULL(IF(b.merchant_type=2, 
		 (
		   select service_fee 
		   from {{services_fee}}
		   where merchant_id = 0
		   and service_id IN (
		     select service_id from {{services}} where service_code = a.meta_value
		   )
		 )
		, 
		
		(
		select service_fee
		from {{services_fee}}
		where merchant_id = a.merchant_id
		and service_id IN (
		     select service_id from {{services}} where service_code = a.meta_value
		   )
		)
		
		),0) as service_fee
		
		from {{merchant_meta}} a
		left join {{merchant}} b
		on
		a.merchant_id = b.merchant_id
		
		where 
		a.meta_name='services'		
		";		
		$this->execute($stmt);
	}
	
	public function down()
	{
		
	}
}
/*end clas*/