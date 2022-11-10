<?php
class MG_view_item_size extends CDbMigration
{
	public function up()
	{
		if($check = Yii::app()->db->schema->getTable("{{view_item_size}}")){
			if($schema = Yii::app()->db->createCommand("SELECT * FROM information_schema.tables 
			where table_name  LIKE '%{{view_item_size}}%'
             ; ")->queryRow()){				
				if(strtolower($schema['TABLE_TYPE'])!="view"){
					$this->dropTable("{{view_item_size}}");
				}				
			}
		}
		
		$stmt="		
		CREATE OR REPLACE VIEW {{view_item_size}} as
		select 
		a.item_size_id,
		a.merchant_id,
		a.item_token,
		a.item_id,
		a.size_id,
		IFNULL(b.size_name,'') as size_name,
		a.price,
		a.sku,
		a.sequence
		
		from
		{{item_relationship_size}} a
		left join {{size}} b
		on
		a.size_id = b.size_id
		";		
		$this->execute($stmt);
	}
	
	public function down()
	{
		
	}
}
/*end clas*/