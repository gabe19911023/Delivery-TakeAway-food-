<?php
class MG_view_order_status extends CDbMigration
{
	public function up()
	{
		if($check = Yii::app()->db->schema->getTable("{{view_order_status}}")){
			if($schema = Yii::app()->db->createCommand("SELECT * FROM information_schema.tables 
			where table_name  LIKE '%{{view_order_status}}%'
             ; ")->queryRow()){				
				if(strtolower($schema['TABLE_TYPE'])!="view"){
					$this->dropTable("{{view_order_status}}");
				}				
			}
		}
		
		$stmt="		
		CREATE OR REPLACE VIEW {{view_order_status}} as
		select
		a.stats_id,
		a.description,
		IFNULL(b.language,'en') as language,
		IF(b.description IS NULL or b.description = '',a.description,b.description) as description_trans
		
		from  {{order_status}} a
		left join  {{order_status_translation}} b
		on
		a.stats_id = b.stats_id
		";		
		$this->execute($stmt);
	}
	
	public function down()
	{
		
	}
}
/*end clas*/