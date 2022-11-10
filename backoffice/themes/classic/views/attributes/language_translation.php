<nav class="navbar navbar-light justify-content-between">
<?php
$this->widget('zii.widgets.CBreadcrumbs', 
array(
'links'=>$links,
'homeLink'=>false,
'separator'=>'<span class="separator">
<i class="zmdi zmdi-chevron-right"></i><i class="zmdi zmdi-chevron-right"></i></span>'
));
?>
</nav>
  
<div class="card">
  <div class="card-body">
  
<?php if(Yii::app()->user->hasFlash('success')): ?>
	<div class="alert alert-success">
		<?php echo Yii::app()->user->getFlash('success'); ?>
	</div>
<?php endif;?>

<?php if(Yii::app()->user->hasFlash('error')): ?>
	<div class="alert alert-danger">
		<?php echo Yii::app()->user->getFlash('error'); ?>
	</div>
<?php endif;?>

<div class="row align-items-center mb-3">
  <div class="col">
  
   <div class="d-flex flex-row align-items-center">
	  <div class="p-2">  
	  <a type="button" class="btn btn-black btn-circle"
	  href="<?php echo Yii::app()->CreateUrl("attributes/language_create_key",array('id'=>$id,'category'=>$category))?>">
	    <i class="zmdi zmdi-plus"></i>
	  </a>  
	  </div>
	  <div class="p-2 mr-4"><h5><?php echo t("Add Key")?></h5></div>	  		  
	</div> <!--flex-->      
  
  </div>
  <div class="col">

 
	<?php
	$form = $this->beginWidget(
		'CActiveForm',
		array(
			'id' => 'search-form',
			'enableAjaxValidation' => false,	
			'method' => 'get',		
		)
	);
	?>
   <div class="input-group">
    <input name="key" type="text" class="form-control" Placeholder="<?php echo t("Search key")?>"
	value="<?php echo $key ?>"
    >
    <div class="input-group-append">
      <button class="btn btn-secondary" type="submit" style="padding:.375rem .75rem;">
        <i class="fa fa-search"></i>
      </button>
    </div>
  </div> 
  <?php $this->endWidget(); ?>   
  
  
  </div> <!--col-->
</div>  
<!--row-->


<?php
$form = $this->beginWidget(
	'CActiveForm',
	array(
		'id' => 'upload-form',
		'enableAjaxValidation' => false,
		'htmlOptions' => array('enctype' => 'multipart/form-data'),
	)
);
?>

   <div class="table-responsive-md">
   <table class="table">
     <thead>
       <tr>
        <th width="10%">#</th>
        <th width="30%"><?php echo t("Key")?></th>
        <th width="50%"><?php echo t("Value")?></th>
       </tr>
     </thead>
     
     <?php if(is_array($models) && count($models)>=1):?>
     <tbody>
      <?php foreach ($models as $items):?>
      <tr>
       <td><?php echo $items->id?></td>
       <td><?php echo $items->message?></td>
       <td>
       <?php
       echo CHtml::textField("translation[$items->id]", $items->translation ,array(
         'class'=>"form-control form-control-text locale_title",
       ));
       ?>   
       </td>
      </tr>
     <?php endforeach;?> 
     </tbody>
     <?php else :?>
     <tr>
      <td colspan="3"><?php echo t("No results.")?></td>
     </tr>
     <?php endif;?>
   </table>
   </div>
   

<div class="row align-items-center">
  <div class="col">
  
   <?php $this->widget('CLinkPager', array(
      'pages' => $pages,      
      'header'=>'',
      'htmlOptions'=>array('class'=>'pagination pager mt-4'),
      'internalPageCssClass'=>"page-link",
      'firstPageCssClass'=>'page-link',
      'lastPageCssClass'=>'page-link',
      'nextPageCssClass'=>'page-link',
      'previousPageCssClass'=>'page-link',
      'selectedPageCssClass'=>"active",
      'firstPageLabel'=>t("First"),
      'lastPageLabel'=>t("Last"),
      'nextPageLabel'=>t("Next"),
      'prevPageLabel'=>t("Previous"),
   ));?>
  
  </div>
  <div class="col text-right">       
	<?php echo CHtml::submitButton('submit',array(
	'class'=>"btn btn-green w-25 mt-3",
	'value'=>t("Save")
	)); ?>  
  </div>
</div>

<?php $this->endWidget(); ?>   
  
  </div> <!--body-->
</div> <!--card-->