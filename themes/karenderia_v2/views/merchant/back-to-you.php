<div class="container mt-3 mb-3">


<div class="receipt-section mb-4 mt-5">
  <img src="<?php echo Yii::app()->theme->baseUrl."/assets/images/receipt.png"?>" />
</div>

<div class="text-center mb-5">
  <h3><?php echo t("You'll be contacted soon!")?></h3>
  <p>
  <?php echo t("{{website_title}} needs to be contact you for more information. You can expect a phone call or email in 1-3 business days",array(
    '{{website_title}}'=>Yii::app()->params['settings']['website_title']
  ))?>
  </p>  
</div>
 
</div> <!--container-->