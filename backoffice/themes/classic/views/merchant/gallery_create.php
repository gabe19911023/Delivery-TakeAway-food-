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
  
<div class="dropzone_container" id="dropzone_multiple" data-action="gallery_create">
   <div class="dz-default dz-message">
    <i class="fas fa-cloud-upload-alt"></i>
     <p><?php echo t("Drop files here to upload")?></p>
    </div>
</div> 
<!--dropzone_container-->

<div class="item_gallery_preview">
<div class="row">

</div> <!--row-->
</div> <!--item_gallery_preview-->


<div class="d-flex justify-content-end">
<a href="<?php echo $done?>" class="btn btn-green">
<?php echo t("Done")?>
</a>
</div>

  </div> <!--card body-->
</div> <!--card-->

<?php $this->renderPartial("/admin/modal_delete_image");?>
