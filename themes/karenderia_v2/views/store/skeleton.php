<?php 
$x = isset($line)?$line:3;
$line_height = isset($height)?$height:'170';
?>
<div id="skeleton-loader" class="row m-0 loading" > 
   <?php for ($x = 1; $x <= $line; $x++) :?>
   <div class="col-lg-12 col-md-3 p-0 position-relative mb-2" style="height:<?php echo $line_height?>px;">
      <div class="skeleton-placeholder"></div>
   </div>              
   <?php endfor;?>
</div> <!--skeleton-loader-->