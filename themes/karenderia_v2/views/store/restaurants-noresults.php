<?php $this->renderPartial("//store/nav-address",array(
 'local_info'=>$local_info
))?>

<div class="container mt-3 mb-3">

<div class="no-results-section mb-4 mt-5">
  <img class="img-350 m-auto d-block" src="<?php echo Yii::app()->theme->baseUrl."/assets/images/404@2x.png"?>" />
</div>

<div class="text-center w-50 m-auto">
  <h3><?php echo t("Sorry! We're not there yet")?></h3>
  <p><?php echo t("We're working hard to expand our area. However, we're not in this location yet. So sorry about this, we'd still love to have you as a customer.")?></p>
  <a href="<?php echo Yii::app()->createUrl("/")?>" class="btn btn-green w25">Go home</a>
</div>
 
</div> <!--container-->