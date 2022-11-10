<?php
class MG_view_item_relationship_subcategory extends CDbMigration
{
	public function up()
	{
		if($check = Yii::app()->db->schema->getTable("{{view_item_relationship_subcategory}}")){
			if($schema = Yii::app()->db->createCommand("SELECT * FROM information_schema.tables 
			where table_name  LIKE '%{{view_item_relationship_subcategory}}%'
             ; ")->queryRow()){				
				if(strtolower($schema['TABLE_TYPE'])!="view"){
					$this->dropTable("{{view_item_relationship_subcategory}}");
				}				
			}
		}
		
		
		$stmt="		
		CREATE OR REPLACE VIEW {{view_item_relationship_subcategory}} as		
		select 
		a.id,
		a.merchant_id,
		a.item_id,
		IFNULL(c.item_token,'') as item_token,
		a.item_size_id,
		IFNULL(b.item_token,'') as size_uuid,
		a.subcat_id,
		a.multi_option,
		a.multi_option_value,
		a.require_addon,
		a.pre_selected
		
		from st_item_relationship_subcategory a
		left join  st_item_relationship_size b
		on
		a.item_size_id = b.item_size_id
		
		left join  st_item c
		on
		a.item_id = c.item_id
		
		where a.item_size_id>0
		";
		
		$this->execute($stmt);
	}
	
	public function down()
	{
		
	}
}
/*end clas*/