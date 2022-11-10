


<h6 class="mb-4"><?php echo t("Reset Password")?></h6>


<?php if(Yii::app()->user->hasFlash('success')): ?>
	<div class="alert alert-success">
		<?php echo Yii::app()->user->getFlash('success'); ?>
	</div>
<?php endif;?>


<div class="mt-4">
<a href="<?php echo isset($back_link)?$back_link:''?>" 
class="dim underline"><?php echo t("Login here")?>
</a>
</div>