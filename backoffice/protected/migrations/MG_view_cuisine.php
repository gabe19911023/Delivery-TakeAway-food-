<?php
class MG_view_cuisine extends CDbMigration
{
	public function up()
	{
		if($check = Yii::app()->db->schema->getTable("{{view_cuisine}}")){
			if($schema = Yii::app()->db->createCommand("SELECT * FROM information_schema.tables 
			where table_name  LIKE '%{{view_cuisine}}%'
             ; ")->queryRow()){				
				if(strtolower($schema['TABLE_TYPE'])!="view"){
					$this->dropTable("{{view_cuisine}}");
				}				
			}
		}
		
		$stmt="		
		CREATE OR REPLACE VIEW {{view_cuisine}} as
		select
		a.cuisine_id,	
		IFNULL(b.language,'en') as language,
		IF(b.cuisine_name IS NULL or b.cuisine_name = '',a.cuisine_name,b.cuisine_name) as cuisine_name,	
		a.status,
		a.featured_image,
		a.slug,
		a.color_hex,
		a.font_color_hex
		
		from  {{cuisine}} a
		left join  {{cuisine_translation}} b
		on
		a.cuisine_id = b.cuisine_id
		";		
		$this->execute($stmt);
	}
	
	public function down()
	{
		
	}
}
/*end clas*/