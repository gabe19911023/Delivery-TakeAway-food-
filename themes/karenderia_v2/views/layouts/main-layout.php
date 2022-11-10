<!DOCTYPE html>
<html lang="<?php echo Yii::app()->language;?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="robots" content="noindex, nofollow" />
<meta name="<?php echo Yii::app()->request->csrfTokenName?>" content="<?php echo Yii::app()->request->csrfToken?>" />    
<link rel="apple-touch-icon" sizes="76x76" href="<?php echo Yii::app()->theme->baseUrl?>/assets/icons/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo Yii::app()->theme->baseUrl?>/assets/icons/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="<?php echo Yii::app()->theme->baseUrl?>/assets/icons/favicon-16x16.png">
<link rel="manifest" href="<?php echo Yii::app()->theme->baseUrl?>/assets/icons/site.webmanifest">
<link rel="mask-icon" href="<?php echo Yii::app()->theme->baseUrl?>/assets/icons/safari-pinned-tab.svg" color="#5bbad5">
<meta name="msapplication-TileColor" content="#da532c">
<meta name="theme-color" content="#ffffff">
<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>
<body class="position-relative <?php echo $this->getBodyClasses(); ?>" data-spy="scroll" data-target="#menu-category" data-offset="75" >
<?php echo $content; ?>
</body>
</html>