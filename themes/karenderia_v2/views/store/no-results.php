<div class="no-results-section mb-4 mt-5">
<img class="img-230 m-auto d-block" src="<?php echo Yii::app()->theme->baseUrl."/assets/images/no-results.png"?>" />
</div>

<div class="text-center w-50 m-auto">
<h3><?php echo isset($title)?$title:''?></h3>
<p><?php echo isset($message)?$message:'' ?></p>
<a href="<?php echo Yii::app()->createUrl("/")?>" class="btn btn-green w25">
 <?php echo t("Go home")?>
</a>
</div>