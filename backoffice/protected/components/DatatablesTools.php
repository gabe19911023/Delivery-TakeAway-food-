<?php
class DatatablesTools
{
	public static $action_edit_path='';
	public static $process_link;
	public static $action_view_path='';
	public static $view_label='';
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
				$search_string = isset($data['search']['value'])?trim($data['search']['value']):'';
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
	
	public static function getTables($cols=array(),$stmt="",$post_data=array(), $and='')
	{		
		$resp = DatatablesTools::format($cols,$post_data);
				
		$feed_data = array();
		$order = $resp['order'];
		$limit = $resp['limit'];
		$search = $resp['search'];
		
		$stmt = str_replace("[and]",$and,$stmt);
		$stmt = str_replace("[search]",$search,$stmt);
		$stmt = str_replace("[order]",$order,$stmt);
		$stmt = str_replace("[limit]",$limit,$stmt);	
						
		if(self::$debug){		
		   dump($stmt);
		}
		
		//if($res = Yii::app()->db->createCommand($stmt)->queryAll()){			
		if($res = CCacheData::queryAll($stmt)){
			if(self::$debug){
				dump($res);
			}
			$total_records=0;									
			if($resc = Yii::app()->db->createCommand("SELECT FOUND_ROWS() as total_records")->queryRow()){				
				$total_records=$resc['total_records'];
			}			
			//$feed_data['draw']=(integer)$post_data['draw'];
			$feed_data['draw']=  isset($post_data['draw'])?$post_data['draw']:'';
			$feed_data['recordsTotal']=$total_records;
			$feed_data['recordsFiltered']=$total_records;
						
			$datas=array(); 
			foreach ($res as $val) {												
				$cols_data = array();
				foreach ($cols as $key_cols=> $cols_val) {					
					if( array_key_exists($cols_val['value'],$val) ){
						
						$val[$cols_val['value']] = CHtml::decode($val[$cols_val['value']]);
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
									
									$html='';
									
									if(!$hide_edit):
									$html.='
									<div class="btn-group btn-group-actions" role="group" >
									  <a href="'.$edit_link.'" class="btn btn-light tool_tips"
									  data-toggle="tooltip" data-placement="top" title="'.t("Update").'"
									  >
									   <i class="zmdi zmdi-border-color"></i>
									  </a>';
									endif;
												
									if(!$hide_delete):						
									$html.='<a href="javascript:;" data-id="'.$primary_id.'" 
									  class="btn btn-light datatables_delete tool_tips"
									  data-toggle="tooltip" data-placement="top" title="'.t("Delete").'"
									  >
									  <i class="zmdi zmdi-delete"></i>
									  </a>
									</div>
									';
									endif;
									
									$cols_data[]=$html;
									break;
								
								case "orders":		
								    $primary_id = trim($val['order_id_token']);	
									$edit_link = Yii::app()->createUrl(self::$action_edit_path,array(
									 'id'=>$primary_id
									));											
									$view_link = Yii::app()->createUrl(self::$action_view_path,array(
									 'id'=>$primary_id
									));											
									$cols_data[] = self::ordersActions($primary_id,$view_link,$edit_link);
								    break;
								    
								case "orders_actions":    
								    $primary_id = trim($val['order_uuid']);	
								    
									$edit_link = Yii::app()->createUrl(self::$action_edit_path,array(
									 'id'=>$primary_id
									));											
									
									$view_link = Yii::app()->createUrl(self::$action_view_path,array(
									 'id'=>$primary_id
									));											
									$cols_data[] = self::ordersActions($primary_id,$view_link,$edit_link);
								    break;
								    
								case "approved":   
								   $primary_id = trim($val['order_id_token']);									   
								   $request_cancel_status = $val['request_cancel_status'];								   
								   
								   $edit_link = Yii::app()->createUrl(self::$action_edit_path,array(
									 'id'=>$primary_id
									));											
									$view_link = Yii::app()->createUrl(self::$action_view_path,array(
									 'id'=>$primary_id
									));											
									
								   if($request_cancel_status=="approved"):								  
								     $cols_data[] = self::ordersActions($primary_id,$view_link,$edit_link);
								   else :
								   $cols_data[]='
								   <div class="btn-group btn-group-actions" role="group" >
								   
								    <a href="javascript:;" data-id="'.$primary_id.'"
									  class="btn btn-light order_approved tool_tips" 
									  data-toggle="tooltip" data-placement="top" title="'.t("Approved").'"
									  >
									    <i class="zmdi zmdi-check">
									  </i>
									  </a>
									  
									  <a href="javascript:;" data-id="'.$primary_id.'"
									  class="btn btn-light order_decline tool_tips"
									   data-toggle="tooltip" data-placement="top" title="'.t("Decline").'"
									  >
									  <i class="zmdi zmdi-close"></i>
									  </a>
								   
								   </div>
								   '; 
								   endif;
								break;
									
								case "view_delete_process":
									$primary_id = trim($val[$cols_val['id']]);	
									$edit_link = Yii::app()->createUrl(self::$action_edit_path,array(
									 'id'=>$primary_id
									));					
																															
									$cols_data[]='
									<div class="btn-group btn-group-actions" role="group" >
									  <a href="javascript:;" class="btn btn-light view_delete_process tool_tips '.self::$process_link.' " 
									  data-id="'.$primary_id.'" 
									  data-toggle="tooltip" data-placement="top" title="'.t("Process").'"
									   >
									   <i class="zmdi zmdi-mail-send"></i>
									   </a>									   									   
									   
									  <a href="'.$edit_link.'" class="btn btn-light tool_tips"
									  data-toggle="tooltip" data-placement="top" title="'.t("View").'"
									  >
									  <i class="zmdi zmdi-eye"></i>
									  </a>
									  
									  <a href="javascript:;" data-id="'.$primary_id.'"									  
									  class="btn btn-light datatables_delete tool_tips"
									  data-toggle="tooltip" data-placement="top" title="'.t("Delete").'"
									  >
									  <i class="zmdi zmdi-delete"></i>
									  </a>
									</div>
									';
									break;
																	  
								case "merchant_logo":																    				   
								   //$pic = CommonUtility::getPhoto($val[$cols_val['value']], CommonUtility::getPlaceholderPhoto('merchant') );												   
								   $folder = isset($val['path'])?$val['path']:'';
								   $fallback_image = CommonUtility::getPlaceholderPhoto('merchant');
								   $pic = CMedia::getImage($val[$cols_val['value']],$folder,'@thumbnail',$fallback_image);
								   								   				   			   
								   $cols_data[] = '<img src="'.CHtml::encode($pic).'" class="img-60 rounded-circle" />';
								   break;   
								   
								case "item_photo":	   
								case "photo":	 
								   $folder = isset($val['path'])?$val['path']:'';
								   
								   $fallback_image = CommonUtility::getPlaceholderPhoto('item_photo');
								   $pic = CMedia::getImage($val[$cols_val['value']],$folder,'@thumbnail',$fallback_image);
								   							   
								   $cols_data[] = '<img src="'.CHtml::encode($pic).'" class="img-60 rounded-circle" />';
								   break;   
								   								
								case "photo_or_class":	 
																   
								   /*$pic = CommonUtility::getPhoto($val[$cols_val['value']], 
								   CommonUtility::getPlaceholderPhoto('item') ); */
								   
								   $logo_type = $val[$cols_val['logo_type']];								  
								   $logo_class = $val[$cols_val['logo_class']];
								   
								   if($logo_type=="image"){
								    
								   	  $folder = isset($val['path'])?$val['path']:'';  
								   	  
								   	  $fallback_image = CommonUtility::getPlaceholderPhoto('item_photo');
								      $pic = CMedia::getImage($val[$cols_val['value']],$folder,'@thumbnail',$fallback_image);
								   	 
								   	  $cols_data[] = '<img src="'.CHtml::encode($pic).'" class="img-60 rounded-circle" />';
								   } else $cols_data[] = '<div class="logo_class"><i class="'.$logo_class.'"></i></div>';
								   break;     
								   
								case "customer":								
								   
								   $folder = isset($val['path'])?$val['path']:'';  	
								   $fallback_image = CommonUtility::getPlaceholderPhoto('customer');
								   $pic = CMedia::getImage($val[$cols_val['value']],$folder,'@thumbnail',$fallback_image);
								   		   
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
								    				  
								    				case "fixed_discount":		
								    				    $discount_type = isset($val[$format_val['discount_type']])?$val[$format_val['discount_type']]:'';								    				    
								    				    if($discount_type=="percentage"){
								    				    	$args[$format_key]=isset($val[$display_value])?Price_Formatter::convertToRaw($val[$display_value],0)."%":'';
								    				    } else {
								    				    	$value = isset($val[$display_value])?$val[$display_value]:'';
								    				    	$args[$format_key]=$value>0?Price_Formatter::formatNumber($value):'';
								    				    }
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
									
								case "price":	
								    $cols_data[]= Price_Formatter::formatNumber($val[$cols_val['value']]);
								   break;
								   
								case "date":	
								    $cols_data[]= Date_Formatter::date($val[$cols_val['value']]);
								   break;   
								   
								case "datetime":	
								    $cols_data[]= Date_Formatter::dateTime($val[$cols_val['value']]);
								   break;      
								   
								case "short_text":   
								   $cols_data[]= CommonUtility::formatShortText($val[$cols_val['value']]);
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
								   
								case "checkbox_active":									
								   $primary_id = trim($val[$cols_val['id']]);									   
								   $field_id = $cols_val['key'];
								   $cols_data[] = Yii::app()->controller->renderPartial('/attributes/html_checkbox',array(
								     'id'=>$field_id."[$primary_id]",
								     'check'=>$val[$cols_val['value'] ]=="active"?true:false,
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
							}
						} else $cols_data[]= CHtml::encode($val[$cols_val['value']]);						
					}					
				}
				$datas[]=$cols_data;
			}						
			$feed_data['data']=$datas;
			return $feed_data;
		}
		return false;
	}
	
	private function ordersActions($primary_id='',$view_link='',$edit_link='')
	{
		return '
		<div class="btn-group btn-group-actions" role="group" >
		
		  <a href="'.$view_link.'" class="btn btn-light tool_tips"
		  data-toggle="tooltip" data-placement="top" title="'.t("View").'"
		  >
		  <i class="zmdi zmdi-eye"></i>
		  </a>
		  
		  <a href="'.$edit_link.'" class="btn btn-light tool_tips"
		  data-toggle="tooltip" data-placement="top" title="'.t("Update").'"
		  >
		  <i class="zmdi zmdi-border-color"></i>
		  </a>
		  
		  <a href="javascript:;" data-id="'.$primary_id.'"
		  class="btn btn-light datatables_delete tool_tips" 
		  data-toggle="tooltip" data-placement="top" title="'.t("Delete").'"
		  >
			<i class="zmdi zmdi-delete">
		  </i>
		  </a>
		
		</div>
		';
	}	
	
	public static function FilterDates($filter_date='',$fields='')
	{
		$filter_stmt='';		
		if(!empty($filter_date)){
			$two_date = explode("to",$filter_date);
	 		if(is_array($two_date) && count($two_date)>=1){		 			
	 			$filter_stmt.=" AND CAST($fields as DATE) BETWEEN ".q( trim($two_date[0]) )." AND ".q( trim($two_date[1]) )." ";
	 		}
		}
		return $filter_stmt;
	}
	
	public static function FilterStatus($status_filter='',$fields='')
	{
		$filter_stmt='';		
		if(!empty($status_filter)){
			if(is_array($status_filter) && count($status_filter)>=1){
				$in = '';
	 			foreach ($status_filter as $status_filter_val) {
	 				$in.=q($status_filter_val).",";
	 			}		 			
	 			$in = substr($in,0,-1);		 			
	 			$filter_stmt.="\nAND $fields IN ($in)";
			}
		}
		return $filter_stmt;
	}
	
} /*end class*/