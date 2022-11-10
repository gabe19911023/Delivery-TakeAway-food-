<!DOCTYPE html>
<html lang="<?php echo Yii::app()->language;?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="robots" content="noindex, nofollow" />
<meta name="<?php echo Yii::app()->request->csrfTokenName?>" content="<?php echo Yii::app()->request->csrfToken?>" />    
<link rel="apple-touch-icon" sizes="76x76" href="<?php echo Yii::app()->theme->baseUrl?>/assets/icons/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo Yii::app()->theme->baseUrl?>/assets/icons/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="<?php echo Yii::app()->theme->baseUrl?>/assets/icons/favicon-16x16.png">
<link rel="manifest" href="<?php echo Yii::app()->theme->baseUrl?>/assets/icons/site.webmanifest">
<link rel="mask-icon" href="<?php echo Yii::app()->theme->baseUrl?>/assets/icons/safari-pinned-tab.svg" color="#5bbad5">
<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>
<body>

<div class="container-fluid m-0 p-0">
 
 <div class="sidebar-panel nice-scroll">
	 <div class="sidebar-wrap">
	 
	  <div class="sidebar-logo">	   
	   <?php 
       $this->widget('application.components.WidgetLogo',array(
         'class_name'=>'top-logo',
		 'link'=>Yii::app()->createUrl("/admin/dashboard")
       ));
       ?>
	  </div>
	  
	  <div class="sidebar-profile">
	     <div class="row m-0">
	       <div class="col-md-3 m-0 p-0" >
	         <img class="rounded-circle" src="<?php echo AdminTools::getProfilePhoto()?>" />
	       </div>
	       <div class="col-md-9 m-0 p-0" >
	         <h6><?php echo AdminTools::displayAdminName();?></h6>
	         <p class="dim">	         
	         <?php 
	         if(!empty(Yii::app()->user->contact_number)){
	         	echo t("T.")." ".Yii::app()->user->contact_number;
	         }
	         if(!empty(Yii::app()->user->email_address)){
	         	echo '<br/>'.t("E.")." ".Yii::app()->user->email_address;
	         }	        	        
	         ?>
	         </p>
	       </div>
	     </div> <!--row-->
	  </div> 
	  <!--sidebar-profile-->
	  
	  <div class="siderbar-menu">	  
	   <?php 
	   $this->widget('application.components.WidgetMenu',array(
	     'menu_type'=>"admin"
	   ));
	   ?>
	  </div> <!--siderbar-menu-->
	  
	  <div class="pl-4 mt-3">		
        <p class="m-0">&copy; 
			<?php 
			$backend_version = Yii::app()->params['settings'];
			$backend_version = isset($backend_version['backend_version'])?$backend_version['backend_version']:"1.0.0";
			echo t("Version {{version}}",['{{version}}'=>$backend_version]);
			?>			
	  </div>

	 </div> <!--sidebar-wrap-->
 </div> <!--sidebar-panel-->
 
 <div class="top-container headroom"  >
 
   <!--desktop-top-menu-->
    <div id="desktop-top-menu" class="row m-0"
	>
        <div class="col-md-4 d-none d-lg-block" >	    
        <div class="d-flex flex-row">        
         <div class="p-2 align-self-center">
          <a class="btn btn-sm" href="<?php echo CMedia::homeUrl()?>" target="_blank" title="<?php echo t("Preview store")?>" ><i class="zmdi zmdi-desktop-mac"></i>
		  </a>
         </div>                
        </div>
        <!--flex-->
        
	    </div> <!--col-->
	    <div class="col-lg-8 col-md-12" >
	    
	      <div class="top-menu-nav float-right">
	      
	      <div class="d-flex flex-row align-items-center">

		   <div class="p-0 p-lg-2 ">     
		   <?php $this->widget('application.components.WidgetLanguageselection');?>
	       </div>

	       <div class="p-2 d-none d-lg-block ">     
	         <img class="img-40 rounded-circle" src="<?php echo AdminTools::getProfilePhoto()?>" />	       
	       </div>		   
	       <div class="p-2 mr-4 d-none d-lg-block  line-bottom">

	           <div class="dropdown userprofile">	      
				  <a class="btn btn-sm dropdown-toggle text-truncate" href="#" 
		          role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				    <?php echo AdminTools::displayAdminName();?>
				  </a>
				
				  <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
				    <a class="dropdown-item" href="<?php echo Yii::app()->createUrl("/admin/profile");?>"><?php echo t("Profile")?></a>
				    <a class="dropdown-item" href="<?php echo Yii::app()->createUrl("/admin/logout")?>">
				    <?php echo t("Logout")?>
				    </a>			    
				  </div>
				</div>	
	            
			  <!-- <div class="line"></div>  				 -->
	       </div>		   

		   <div class="p-0 d-block d-lg-none align-self-center ">     	         
			 <div class="dropdown userprofile">	      
			   <a class="btn btn-sm dropdown-toggle text-truncate" href="javascript:;" 
			   role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				 <img class="img-40 rounded-circle" src="<?php echo AdminTools::getProfilePhoto()?>" />
			   </a>
			 
			   <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
				 <a class="dropdown-item" href="<?php echo Yii::app()->createUrl("/admin/profile");?>">
				 <?php echo t("Profile")?>
				 </a>
				 <a class="dropdown-item" href="<?php echo Yii::app()->createUrl("/admin/logout")?>">
				 <?php echo t("Logout")?>
				 </a>			    
			   </div>
			 </div>			  
	       </div>	<!--p-3-->
	       	       		   
		   <div id="vue-notifications" class="p-2 mr-2 mr-lg-4 ">
		     <components-notification
		     ref="notification"
		     ajax_url="<?php echo Yii::app()->createUrl("/api")?>" 		     
		     view_url="<?php echo Yii::app()->createUrl("/notifications/all_notification")?>" 
		     :realtime="{
			   enabled : '<?php echo Yii::app()->params['realtime_settings']['enabled']==1?true:false ;?>',  
			   provider : '<?php echo CJavaScript::quote( Yii::app()->params['realtime_settings']['provider'] )?>',  			   
			   key : '<?php echo CJavaScript::quote( Yii::app()->params['realtime_settings']['key'] )?>',  			   
			   cluster : '<?php echo CJavaScript::quote( Yii::app()->params['realtime_settings']['cluster'] )?>', 
			   ably_apikey : '<?php echo CJavaScript::quote( Yii::app()->params['realtime_settings']['ably_apikey'] )?>', 
			   piesocket_api_key : '<?php echo CJavaScript::quote( Yii::app()->params['realtime_settings']['piesocket_api_key'] )?>', 
			   piesocket_websocket_api : '<?php echo CJavaScript::quote( Yii::app()->params['realtime_settings']['piesocket_websocket_api'] )?>', 
			   piesocket_clusterid : '<?php echo CJavaScript::quote( Yii::app()->params['realtime_settings']['piesocket_clusterid'] )?>', 
			   channel : '<?php echo CJavaScript::quote( Yii::app()->params->realtime['admin_channel'] )?>',  			   
			   event : '<?php echo CJavaScript::quote( Yii::app()->params->realtime['notification_event'] )?>',  
			 }"  			 
		     :label="{
			  title : '<?php echo CJavaScript::quote(t("Notification"))?>',  
			  clear : '<?php echo CJavaScript::quote(t("Clear all"))?>',  
			  view : '<?php echo CJavaScript::quote(t("View all"))?>',  			  
			  pushweb_start_failed : '<?php echo CJavaScript::quote(t("Could not push web notification"))?>',  			  
			  no_notification : '<?php echo CJavaScript::quote(t("No notifications yet"))?>',  	
			  no_notification_content : '<?php echo CJavaScript::quote(t("When you get notifications, they'll show up here"))?>',  	
			}"  			 
		     >		      
		     </components-notification>
		   </div> <!--p-2-->		   
		   
		   <div class="d-block d-lg-none">
         
           <div class="hamburger hamburger--3dx ssm-toggle-nav">
		    <div class="hamburger-box">
		      <div class="hamburger-inner"></div>
		    </div>
		    </div> 
         
         </div> <!--p2-->
		   
		  </div> <!--flex-->
	      
	      </div><!--top-menu-nav-->	      
	      
	    </div>
    </div> <!--row desktop-top-menu-->
    
 </div> <!--top-container-->
 
 <div class="main-container">
    <div class="main-container-wrap">
       <div class="container">
       <?php echo $content;?>
       </div> <!--container-->
    </div> <!--main-container-wrap-->
 </div><!-- main-container-->
 
 <div class="ssm-overlay ssm-toggle-nav"></div>
 
</div><!--container-->

</body>
</html> 