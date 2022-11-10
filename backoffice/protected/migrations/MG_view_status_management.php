<?php
class MG_view_status_management extends CDbMigration
{
	public function up()
	{
		if($check = Yii::app()->db->schema->getTable("{{view_status_management}}")){
			if($schema = Yii::app()->db->createCommand("SELECT * FROM information_schema.tables 
			where table_name  LIKE '%{{view_status_management}}%'
             ; ")->queryRow()){				
				if(strtolower($schema['TABLE_TYPE'])!="view"){
					$this->dropTable("{{view_status_management}}");
				}				
			}
		}
		
		$stmt="		
		CREATE OR REPLACE VIEW {{view_status_management}} as
		select
		a.status_id,a.group_name,a.status,a.title,a.color_hex,font_color_hex,
		
		IFNULL(b.language,'en') as language,
		IF(b.title IS NULL or b.title = '',a.title,b.title) as title_trans
		
		from  {{status_management}} a
		left join  {{status_management_translation}} b
		on
		a.status_id = b.status_id		
		";		
		$this->execute($stmt);
	}
	
	public function down()
	{
		
	}
}
/*end clas*/