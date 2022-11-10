<?php
class MG_view_item_lang_size extends CDbMigration
{
	public function up()
	{
		if($check = Yii::app()->db->schema->getTable("{{view_item_lang_size}}")){
			if($schema = Yii::app()->db->createCommand("SELECT * FROM information_schema.tables 
			where table_name  LIKE '%{{view_item_lang_size}}%'
             ; ")->queryRow()){				
				if(strtolower($schema['TABLE_TYPE'])!="view"){
					$this->dropTable("{{view_item_lang_size}}");
				}				
			}
		}
		
		$stmt="		
		CREATE OR REPLACE VIEW {{view_item_lang_size}} as
		select 
		a.merchant_id,
		a.item_id,
		a.item_size_id,
		a.item_token as size_uuid,
		a.price,
		a.size_id,
		IFNULL(b.size_name,'') as size_name,
		IFNULL(b.language,'') as language,
		a.available,
		a.discount,
		a.discount_type,
		a.discount_start,
		a.discount_end
		
		from {{item_relationship_size}} a
		left join {{size_translation}} b
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