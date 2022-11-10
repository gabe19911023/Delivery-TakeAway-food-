<nav class="navbar navbar-light justify-content-between">
<?php
$this->widget('zii.widgets.CBreadcrumbs',$links);
?>
</nav>

<DIV id="vue-theme-menu" class="card">
 <div class="card-body">
  
     <div class="position-relative">
     <components-menu-list
     ref="menu_list"    
     ajax_url="<?php echo $ajax_url?>"
     :label="{
	    title:'<?php echo CJavaScript::quote("Add menu items")?>', 	 
	  }"  
	 @set-currentmenu="setCurrentmenu" 
	 @create-newmenu="createNewmenu" 
     >
     </components-menu-list>    
     </div>
	
	<div class="row mt-4">
	    <div class="col-md-4">
	    
	     <components-menu-allpages
	     ref="all_pages"    
         ajax_url="<?php echo $ajax_url?>"
         :menu_id="current_menu"
         :label="{
		    title:'<?php echo CJavaScript::quote("Add menu items")?>', 
		    pages:'<?php echo CJavaScript::quote("Pages")?>', 
		    custom_links:'<?php echo CJavaScript::quote("Custom links")?>', 
		  }"  
		 @after-addpage="afterAddpage"
	     >
	     </components-menu-allpages>
	    
	    </div> <!--col-->
	    
	    <div class="col-md-8">
	    
	     <components-menu-structure
	     ref="menu_structure"    
         ajax_url="<?php echo $ajax_url?>"
         :current_menu="current_menu"
         :label="{
		    title:'<?php echo CJavaScript::quote("Menu structure")?>', 
		    pages:'<?php echo CJavaScript::quote("Pages")?>', 
		    delete_confirmation:'<?php echo CJavaScript::quote("Delete Confirmation")?>', 
		    are_you_sure:'<?php echo CJavaScript::quote("Are you sure you want to permanently delete the selected item?")?>', 
		    cancel:'<?php echo CJavaScript::quote("Cancel")?>', 
		    delete:'<?php echo CJavaScript::quote("Delete")?>', 
		  }"  		  
		 @after-savemenu="afterSavemenu"  
		 @after-cancelmenu="afterCancelmenu"  
	     >
	     </components-menu-structure>
	    
	    </div> <!--col-->
	    
	</div>
	<!--row-->
  
 </div><!-- card body-->
</DIV> <!--theme-->