<!DOCTYPE html>
<html lang="<?php echo Yii::app()->language;?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="robots" content="noindex, nofollow" />
<meta name="<?php echo Yii::app()->request->csrfTokenName?>" content="<?php echo Yii::app()->request->csrfToken?>" />    
<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>
<body>


<div class="container w-50  mt-5 h-100">
  <?php echo $content;?>
</div>

</body>
</html> 