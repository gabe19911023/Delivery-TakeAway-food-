<?php
class MG_view_item_relationship_subcategory_item extends CDbMigration
{
	public function up()
	{
		if($check = Yii::app()->db->schema->getTable("{{view_item_relationship_subcategory_item}}")){
			if($schema = Yii::app()->db->createCommand("SELECT * FROM information_schema.tables 
			where table_name  LIKE '%{{view_item_relationship_subcategory_item}}%'
             ; ")->queryRow()){				
				if(strtolower($schema['TABLE_TYPE'])!="view"){
					$this->dropTable("{{view_item_relationship_subcategory_item}}");
				}				
			}
		}
		
		/*$stmt="		
		CREATE OR REPLACE VIEW {{view_item_relationship_subcategory_item}} as		
		select a.*,
		IFNULL(b.item_token,'') as item_token
		from {{item_relationship_subcategory_item}} a
		left join {{item}} b
		on
		a.item_id = b.item_id
		";*/		
		
		$stmt="		
		CREATE OR REPLACE VIEW {{view_item_relationship_subcategory_item}} as		
		select 
		b.merchant_id,
		b.item_id,
		(
		select item_token from {{item}}
		where item_id = b.item_id
		limit 0,1
		) as item_token, 
		b.item_size_id,
		a.subcat_id,a.sub_item_id
		from {{subcategory_item_relationships}} a
		left join {{item_relationship_subcategory}} b
		on
		a.subcat_id = b.subcat_id
		
		where merchant_id IS NOT NULL
		";
		
		$this->execute($stmt);
	}
	
	public function down()
	{
		
	}
}
/*end clas*/