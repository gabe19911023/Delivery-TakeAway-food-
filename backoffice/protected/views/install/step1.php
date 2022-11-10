


<div class="card">

  <div class="text-center p-2 pt-4">
    <h5>Checking file permission</h5>
  </div>

 <div class="card-body">
	 <ul class="list-group">
	  <li class="list-group-item d-flex justify-content-between align-items-center">
	    PHP = <?php echo isset($check['php'])?$check['php']:'' ?>
	    <?php if($check['php']>=7):?>
	    <span class="badge badge-info rounded-circle"><i class="zmdi zmdi-check"></i></span>
	    <?php else :?>
	    <span class="badge badge-danger rounded-circle"><i class="zmdi zmdi-close"></i></span>
	    <?php endif;?>
	  </li>
	  <li class="list-group-item d-flex justify-content-between align-items-center">
	    Database connection
	    <?php if($check['database']):?>
	    <span class="badge badge-info rounded-circle"><i class="zmdi zmdi-check"></i></span>
	    <?php else :?>
	    <span class="badge badge-danger rounded-circle"><i class="zmdi zmdi-close"></i></span>
	    <?php endif;?>
	  </li>
	  <li class="list-group-item d-flex justify-content-between align-items-center">
	    Curl PHP Extension
	    <?php if($check['curl_enabled']):?>
	    <span class="badge badge-info rounded-circle"><i class="zmdi zmdi-check"></i></span>
	    <?php else :?>
	    <span class="badge badge-danger rounded-circle"><i class="zmdi zmdi-close"></i></span>
	    <?php endif;?>
	  </li>
	  <li class="list-group-item d-flex justify-content-between align-items-center">
	    PDO 	    
	    <?php if($check['pdo']):?>
	    <span class="badge badge-info rounded-circle"><i class="zmdi zmdi-check"></i></span>
	    <?php else :?>
	    <span class="badge badge-danger rounded-circle"><i class="zmdi zmdi-close"></i></span>
	    <?php endif;?>
	  </li>
	  <li class="list-group-item d-flex justify-content-between align-items-center">
	    PHP Session
	    <?php if($check['pdo']):?>
	    <span class="badge badge-info rounded-circle"><i class="zmdi zmdi-check"></i></span>
	    <?php else :?>
	    <span class="badge badge-danger rounded-circle"><i class="zmdi zmdi-close"></i></span>
	    <?php endif;?>
	  </li>
	  <li class="list-group-item d-flex justify-content-between align-items-center">
	    Backoffice runtime folder permission
	    <?php if($check['runtime_backoffice']):?>
	    <span class="badge badge-info rounded-circle"><i class="zmdi zmdi-check"></i></span>
	    <?php else :?>
	    <span class="badge badge-danger rounded-circle"><i class="zmdi zmdi-close"></i></span>
	    <?php endif;?>
	  </li>
	  <li class="list-group-item d-flex justify-content-between align-items-center">
	    Front runtime folder permission
	    <?php if($check['runtime_home']):?>
	    <span class="badge badge-info rounded-circle"><i class="zmdi zmdi-check"></i></span>
	    <?php else :?>
	    <span class="badge badge-danger rounded-circle"><i class="zmdi zmdi-close"></i></span>
	    <?php endif;?>
	  </li>
	  <li class="list-group-item d-flex justify-content-between align-items-center">
	    Upload folder permission
	    <?php if($check['upload_folder']):?>
	    <span class="badge badge-info rounded-circle"><i class="zmdi zmdi-check"></i></span>
	    <?php else :?>
	    <span class="badge badge-danger rounded-circle"><i class="zmdi zmdi-close"></i></span>
	    <?php endif;?>
	  </li>
	</ul>
	
	<div class="tex-center m-3">
	  <a href="<?php echo Yii::app()->createUrl('/install/step2',array('version'=> date("c")))?>" class="btn btn-success w-100 rounded-0">Continue</a>
	</div>
	
</div> 
 
</div> 
<!--card-->