<?php
class MG_view_ratings extends CDbMigration
{
	public function up()
	{
		if($check = Yii::app()->db->schema->getTable("{{view_ratings}}")){
			if($schema = Yii::app()->db->createCommand("SELECT * FROM information_schema.tables 
			where table_name  LIKE '%{{view_ratings}}%'
             ; ")->queryRow()){				
				if(strtolower($schema['TABLE_TYPE'])!="view"){
					$this->dropTable("{{view_ratings}}");
				}				
			}
		}
		
		$stmt="						
		CREATE OR REPLACE VIEW {{view_ratings}} AS 
		select 
		  merchant_id AS merchant_id, 
		  count(0) AS review_count, 
		  (
		    sum(rating) / count(0)
		  ) AS ratings
		from 
		  {{review}}
		where 
		  (
		    status in ('publish', 'published')
		  ) 
		group by merchant_id;
		";		
		$this->execute($stmt);
	}
	
	public function down()
	{
		
	}
}
/*end clas*/