<!DOCTYPE html>
<html lang="<?php echo Yii::app()->language;?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<link rel="apple-touch-icon" sizes="76x76" href="<?php echo Yii::app()->theme->baseUrl?>/assets/icons/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo Yii::app()->theme->baseUrl?>/assets/icons/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="<?php echo Yii::app()->theme->baseUrl?>/assets/icons/favicon-16x16.png">
<link rel="manifest" href="<?php echo Yii::app()->theme->baseUrl?>/assets/icons/site.webmanifest">
<link rel="mask-icon" href="<?php echo Yii::app()->theme->baseUrl?>/assets/icons/safari-pinned-tab.svg" color="#5bbad5">   
<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>
<body class="<?php echo $this->getBodyClasses(); ?>">


<div class="login-wrapper container-fluid">
  <div class="row m-0 p-0">
    <div class="col-md-6 m-0 p-0 left-container"></div>
    <div class="col-md-6 m-0 p-0 right-container">
    
     <div class="right-container-wrap">
	     <div class="logok-wrapper">
	       <img class="img-60 rounded-circle" src="<?php echo Yii::app()->theme->baseUrl?>/assets/images/logok@2x.png" />
	     </div>
	     
	      <div class="main-container">
		    <div class="main-container-wrap">
		       <?php echo $content;?>
		    </div>
		 </div>
		 <!--main-container-->
	     
	    <!--  <div class="app-store-wrap">
	     
	     <div class="d-flex justify-content-center">
		  <div class="p-2">
		    <a href="#">
		      <img src="<?php echo Yii::app()->theme->baseUrl?>/assets/images/app-store@2x.png" />
		    </a>
		  </div>
		  <div class="p-2">
		    <a href="#">
		      <img src="<?php echo Yii::app()->theme->baseUrl?>/assets/images/google-play@2x.png" />
		    </a>
		  </div>
		</div>  	     
	     </div> --> <!--app-store-->
	     
     </div>
     <!--right-container-wrap-->
    
    </div>
  </div>
</div>
<!--login-wrapper-->

</body>
</html> 