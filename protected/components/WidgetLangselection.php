<?php
class WidgetLangselection extends CWidget 
{	
	public function run() {		        
                
        $dependency = CCacheData::dependency();        
        $model = AR_language::model()->cache(Yii::app()->params->cache, $dependency)->find("code=:code",array(
            ':code'=>Yii::app()->language
        ));           

        
        $criteria=new CDbCriteria();
        $criteria->select="code,title,description,flag,rtl";
        $criteria->condition = "status=:status ";		    
        $criteria->params  = array(			  
            ':status'=>'publish'
        );
        $criteria->order ="sequence ASC";
        $data = AR_language::model()->cache(Yii::app()->params->cache, $dependency)->findAll($criteria); 

        $enabled =  isset(Yii::app()->params['settings']['enabled_language_bar_front'])?Yii::app()->params['settings']['enabled_language_bar_front']:false;
        $enabled = $enabled==1?true:false;
        
        if($enabled){
            $this->render('lang_selection', array(
                'data'=>$data,
                'flag'=>$model?$model->flag:'us',
                'current_lang'=>Yii::app()->language,
            ));
        }
	}

    public static function getData()
    {
        $default = [];
        $dependency = CCacheData::dependency();        
        $model = AR_language::model()->cache(Yii::app()->params->cache, $dependency)->find("code=:code",array(
            ':code'=>Yii::app()->language
        ));         
        if($model){
            $default = [
                'code'=>$model->code,
                'title'=>$model->title,
                'description'=>$model->description,
                'flag'=>CMedia::themeAbsoluteUrl()."/assets/flag/". strtolower($model->flag) .".svg",
                'rtl'=>$model->rtl,
            ];
        }

        $data = array();
        $criteria=new CDbCriteria();
        $criteria->select="code,title,description,flag,rtl";
        $criteria->condition = "status=:status ";		    
        $criteria->params  = array(			  
            ':status'=>'publish'
        );
        $criteria->order ="sequence ASC";
        $model = AR_language::model()->cache(Yii::app()->params->cache, $dependency)->findAll($criteria); 
        if($model){
            foreach ($model as $items) {
                $data[] = [
                   'code'=>$items->code,
                   'title'=>$items->title,
                   'description'=>$items->description,
                   'flag'=>CMedia::themeAbsoluteUrl()."/assets/flag/". strtolower($items->flag) .".svg",
                   'rtl'=>$items->rtl, 
                   'link'=>websiteUrl()."/?language=".$items->code
                ];
            }
        }            
        $enabled =  isset(Yii::app()->params['settings']['enabled_language_bar_front'])?Yii::app()->params['settings']['enabled_language_bar_front']:false;
        $enabled = $enabled==1?true:false;        
        return [
           'enabled'=>$enabled,           
           'default'=>$default,
           'data'=>$data,           
        ];
    }
	
}
/*end class*/