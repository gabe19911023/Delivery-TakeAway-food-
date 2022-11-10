<?php if(is_array($this->data) && count($this->data)>=1):?>
<table class="ktables">

<?php foreach ($this->data as $val):?>
<?php 
$logo = CMedia::getImage($val[$this->logo],'',Yii::app()->params->size_image_thumbnail,
				CommonUtility::getPlaceholderPhoto('merchant'));
?>
<tr>
 <td><img class="img-60 rounded-circle"
 src="<?php echo $logo;?>" />	  </td>
 <td>
   <h6><?php echo $val[$this->title]?> <span class="badge <?php echo $this->badge_type?> <?php echo $val[$this->status]?>">
   <?php echo $val[$this->status_title]?></span>
   </h6>
   <p class="dim">
   <?php 
   $args = array();
   if(is_array($this->description) && count($this->description)>=1){   	 
   	 $format = $this->description['format'];   	 
   	 foreach ($this->description['format_value'] as $key=>$formatval) {   	 	
   	 	$args["[$key]"] = $val[$formatval];
   	 }      	 
   	 echo t($format,$args);
   } else echo $val[$this->description];   
   ?> 
   <?php if(!empty($this->sub_string)):?>
     <br/><?php echo $val[$this->sub_string]?>
   <?php endif;?>
   </p>
   
   <?php       
   $link = str_replace("[id]",isset($val[$this->id])?$val[$this->id]:'',$this->view_url);   
   ?>
   <DIV class="actions">
   <div class="btn-group btn-group-actions" role="group">
    <a href="<?php echo $link?>" class="btn btn-light tool_tips" data-toggle="tooltip" data-placement="top" title="" data-original-title="View">
	  <i class="zmdi zmdi-eye"></i>
	  </a>
   </div>
   </DIV>
   
 </td>
</tr>
<?php endforeach;?>
</table>
<?php endif;?>
