<?php
class AppListing extends CComponent
{
	public static $action_edit_path='';
	public static $debug=false;
	
	public static function format($cols=array(), $data=array())
	{		
				
		$data=Yii::app()->input->xssClean($data); 
						
		$resp = array();
		$resp['search']='';
		$resp['order']='';
		$resp['limit']='';
		
		$order=''; $where =''; $limit=''; $search='';
				
		if(is_array($data) && count($data)>=1){
			
			if(isset($data['order'])){
				if(is_array($data['order']) && count($data['order'])>=1){
					foreach ($data['order'] as $val) {								
						if(array_key_exists($val['column'], (array) $cols)){
							$order = "ORDER BY ". addslashes( $cols[$val['column']]['key'] ) ." ".addslashes($val["dir"]); 
						}
					}
				}
				if(!empty($order)){
					$resp['order']=$order;
				}
			}			
			
			if(isset($data['start']) && isset($data['length'])){
				$limit= "LIMIT ". q( (integer) $data['start']).",". q( (integer) $data['length']);
				$resp['limit']=$limit;
			} else $resp['limit']="LIMIT 0,10";
			
						
			if(isset($data['search'])){
				$search_string = isset($data['search'])?trim($data['search']):'';
				if(!empty($search_string)){
					$search.=" AND (";
					foreach ($cols as $cols) {					
						$search.=" $cols[key] LIKE ".q("%$search_string%")." OR\n";
					}
					$search = substr($search,0,-4).")";
					$resp['search']=$search;
				}
			}				
		}
				
		return $resp;
	}
	
	public static function Listing($cols=array(),$fields=array(), $stmt="",$post_data='',$and='')
	{		
		$resp = AppListing::format($fields,$post_data);
		
		$order = isset($resp['order'])?$resp['order']:'';
		$limit = isset($resp['limit'])?$resp['limit']:'';
		$search = isset($resp['search'])?$resp['search']:'';
			
		$stmt = str_replace("[and]",$and,$stmt);
		$stmt = str_replace("[search]",$search,$stmt);
		$stmt = str_replace("[order]",$order,$stmt);
		$stmt = str_replace("[limit]",$limit,$stmt);	
					
		if(self::$debug){dump($stmt);}
					
		if($res = Yii::app()->db->createCommand($stmt)->queryAll()){			
			if(self::$debug){dump($res);}
			$datas=array(); 
			foreach ($res as $val) {	
				$cols_data = array();
				foreach ($cols as $key_cols=> $cols_val) {
					if( array_key_exists($cols_val['value'],$val) ){
						$val[$cols_val['value']] = stripslashes($val[$cols_val['value']]);
						$val[$cols_val['value']] = t($val[$cols_val['value']]);
						
						if(isset($cols_val['action'])){							
							switch ($cols_val['action']) {
								case "editdelete":
									$primary_id = trim($val[$cols_val['id']]);	
									$hide_delete = isset($cols_val['hide_delete'])?$cols_val['hide_delete']:false;
									$hide_edit = isset($cols_val['hide_edit'])?$cols_val['hide_edit']:false;
									$primary_key = isset($cols_val['primary_key'])?$cols_val['primary_key']:'id';
									
									$edit_link = Yii::app()->createUrl(self::$action_edit_path,array(
									   $primary_key=>$primary_id
									));											
									
									$buttons = array();
									
									if(!$hide_edit):
									$buttons[] ='									
									  <a href="'.$edit_link.'" class="btn btn-light tool_tips"
									  data-toggle="tooltip" data-placement="top" title="'.t("Update").'"
									  >
									   <i class="zmdi zmdi-border-color"></i>
									  </a>';
									endif;
									
									if(!$hide_delete):						
									$buttons[] ='<a href="javascript:;" data-id="'.$primary_id.'" 
									  class="btn btn-light datatables_delete tool_tips"
									  data-toggle="tooltip" data-placement="top" title="'.t("Delete").'"
									  >
									  <i class="zmdi zmdi-delete"></i>
									  </a>									
									';
									endif;
								
									$cols_data[]=$buttons;									
									break;
																		
								  case "merchant_logo":																    				   
								      //$pic = CommonUtility::getPhoto($val[$cols_val['value']], CommonUtility::getPlaceholderPhoto('merchant') );								   								      								      
								      $pic = CMedia::getImage($val['logo'],$val['path'],'@thumbnail',CommonUtility::getPlaceholderPhoto('merchant'));
								      $cols_data[] = '<img src="'.CHtml::encode($pic).'" class="img-60 rounded-circle" />';
								      break;   
								   
								   case "item_photo":	   
								   case "photo":	 
								      $pic = CommonUtility::getPhoto($val[$cols_val['value']], CommonUtility::getPlaceholderPhoto('item') );								   
								      $cols_data[] = '<img src="'.CHtml::encode($pic).'" class="img-60 rounded-circle" />';
								   break;   
								   
								   case "gallery":
								   	  $pic = CommonUtility::getPhoto($val[$cols_val['value']], CommonUtility::getPlaceholderPhoto('item') );								   
								      $cols_data[] = '<img src="'.CHtml::encode($pic).'" class="card-img-top" />';
								   	break;   
								   
								   case "customer":								
								      $pic = CommonUtility::getPhoto($val[$cols_val['value']],CommonUtility::getPlaceholderPhoto('customer'));								   
								      $cols_data[] = '<img src="'.CHtml::encode($pic).'" class="img-60 rounded-circle" />';
								   break;      
									
									case "format":
									$args = array();
									$format = $cols_val['format'];								    
								    if(isset($cols_val['format_value'])){
								    	if(is_array($cols_val['format_value']) && count($cols_val['format_value'])>=1){
								    	foreach ($cols_val['format_value'] as $format_key=>$format_val) {
								    		if(is_array($format_val) && count($format_val)>=1){										    			
								    			$display = isset($format_val['display'])?$format_val['display']:'';
								    			$display_value = isset($format_val['value'])?$format_val['value']:'';
								    			switch ($display) {
								    				case "price":								    					
								    					$args[$format_key]=isset($val[$display_value])?Price_Formatter::formatNumber($val[$display_value]):'';
								    					break;
								    			
								    				case "short_text":	
								    				    $args[$format_key]=isset($val[$display_value])?CommonUtility::formatShortText($val[$display_value]):''; 
								    				    break;
								    				    
								    				case "date":	
								    				    $args[$format_key]=isset($val[$display_value])?Date_Formatter::date($val[$display_value]):''; 
								    				    break;    
								    				    
								    				case "datetime":	
								    				    $args[$format_key]=isset($val[$display_value])?Date_Formatter::dateTime($val[$display_value]):''; 
								    				    break;        
								    				
								    				case "time":	
								    				    if(!empty($val[$display_value])){
								    				        $args[$format_key]=isset($val[$display_value])?Date_Formatter::Time($val[$display_value]):''; 
								    				    } else $args[$format_key]='';
								    				    break;          
								    				    
								    				case "filesize":    
								    				     $args[$format_key]=isset($val[$display_value])?CommonUtility::HumanFilesize($val[$display_value]):''; 
								    				     break;    
								    				     
								    				case "explode":     
								    				    $json_line = '';								    				    
								    				    $json = isset($val[$display_value])? explode(",",$val[$display_value]): false ;	
								    				    if(is_array($json) && count($json)>=1){
								    				    	foreach ($json as $json_val) {								    				    		
								    				    		$json_line.= t($json_val).", ";
								    				    	}
								    				    	$json_line = substr($json_line,0,-2);
								    				    }								    				    
								    				    $args[$format_key] = $json_line;   
								    				  break;   
								    				       
								    				case "json":     
								    				    $json_line = '';								    				    
								    				    $json = isset($val[$display_value])? json_decode($val[$display_value],true): false ;	
								    				    if(is_array($json) && count($json)>=1){
								    				    	foreach ($json as $json_val) {								    				    		
								    				    		$json_line.= t($json_val).", ";
								    				    	}
								    				    	$json_line = substr($json_line,0,-2);
								    				    }								    				    
								    				    $args[$format_key] = $json_line;   
								    				  break; 
								    				    
								    				case "numerical":	
								    				    $args[$format_key]=isset($val[$display_value])?Price_Formatter::convertToRaw($val[$display_value],0):''; 
								    				    break;  	
								    				    
								    				case "pattern":    
								    				     $data_format = isset($format_val['data'])?$format_val['data']:array();								    				     
								    				     $values = isset($val[$display_value])?$val[$display_value]:''; 
								    				     if(array_key_exists($values,(array)$data_format)){
								    				     	$values = $data_format[$values];
								    				     }								    				     
								    				     $args[$format_key]=$values;
								    				     break;
								    				     
								    				case "percentage":    
								    				     $args[$format_key]=isset($val[$display_value])?Price_Formatter::convertToRaw($val[$display_value],0)."%":'';
								    				     break;     
								    				       
								    				default:
								    					break;
								    			}
								    		} else $args[$format_key]=isset($val[$format_val])?stripslashes( CHtml::encode($val[$format_val]) ):'';
								    	}								    	
								    	}								    	
								    	$format = t($format,$args);
								    }
								    $cols_data[] = $format;								    
									break;
									
									case "datetime":	
								      $cols_data[]= Date_Formatter::dateTime($val[$cols_val['value']]);
								    break;      
									
									case "custom_buttons":										
									    $args = array();
										$buttons = isset($cols_val['buttons'])?$cols_val['buttons']:'';		
										$button_created = array();
										if(isset($cols_val['format_value'])){
											if(is_array($cols_val['format_value']) && count($cols_val['format_value'])>=1){
												foreach ($cols_val['format_value'] as $format_key=>$format_val) {
												   $args[$format_key]=isset($val[$format_val])?stripslashes( CHtml::encode($val[$format_val]) ):'';	
												}												
											}
										}
										
										if(is_array($buttons) && count($buttons)>=1){
											foreach ($buttons as $button) {
												$button_created[] = t($button,$args);
											}
										}
																													
										$cols_data[]=$button_created;
										break;
									
								   case "checkbox":									
								     $primary_id = trim($val[$cols_val['id']]);									   
								     $field_id = $cols_val['key'];
								     $cols_data[] = Yii::app()->controller->renderPartial('/attributes/html_checkbox',array(
								       'id'=>$field_id."[$primary_id]",
								       'check'=>$val[$cols_val['value'] ]==1?true:false,
								       'value'=>$primary_id,
								       'label'=>'',
								       'class'=>isset($cols_val['class'])?$cols_val['class']:''
								      ),true);
								    break;  
								    
								   case "promo_type":  																     
								     $promo_type = isset($val['promo_type'])?$val['promo_type']:'';								     
								     if($promo_type=="buy_one_get_discount"){
								     	$words = "<h6>Buy [buy_qty] and get 1 at [get_qty]% off ([item_name])</h6>";
								     } elseif ( $promo_type=="buy_one_get_free"){
								     	$words = "<h6>Buy [buy_qty] to get the [get_qty] item free ([item_name])</h6>";
								     }			
								     
								     if($val['discount_start']!=null){
								        $words.='<p class="dim">[discount_start] to [discount_end]</p>';	
								     }
								     						     			
								     $cols_data[] = t($words,array(
								       '[buy_qty]'=>isset($val['buy_qty'])?$val['buy_qty']:0,
								       '[get_qty]'=>isset($val['get_qty'])?$val['get_qty']:0,
								       '[item_name]'=>isset($val['item_name'])?$val['item_name']:'',
								       '[discount_start]'=>isset($val['discount_start'])?Date_Formatter::date($val['discount_start']):'',
								       '[discount_end]'=>isset($val['discount_end'])?Date_Formatter::date($val['discount_end']):'',
								     ));
								     
								   break;	   
								   	
									default:
									$cols_data[]=CHtml::encode($val[$cols_val['value']]);
									break;
									
									
							}/* end switch*/
						} else $cols_data[]= CHtml::encode($val[$cols_val['value']]);
					}
				}
				$datas[]=$cols_data;
			}
			return $datas;
		}
		return false;
	}
	
}
/*end class*/