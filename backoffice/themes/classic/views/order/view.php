<nav class="navbar navbar-light justify-content-between">
<?php
$this->widget('zii.widgets.CBreadcrumbs', 
array(
'links'=>$links,
'homeLink'=>false,
'separator'=>'<span class="separator">
<i class="zmdi zmdi-chevron-right"></i><i class="zmdi zmdi-chevron-right"></i></span>'
));
?>
</nav>


<div class="card">
  <div class="card-body">
  
   <div class="d-flex justify-content-between">
      <div><p><b>Order #123 Place on 28 Oct 2021 9:44 PM</b></p></div>
      <div class="flex-col">
      
        <div class="d-flex justify-content-end">
          <div class="flex-col mr-3"><button class="btn btn-black">Print</button></div>
          <div class="flex-col">
          
           <div class="dropdown dropleft">
			  <a class="rounded-pill rounded-button-icon d-inline-block" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			    <i class="zmdi zmdi-more"></i>
			  </a>
			
			  <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
			    <a class="dropdown-item" href="#">Delay order</a>
			    <a class="dropdown-item" href="#">Price adjustment</a>
			    <a class="dropdown-item" href="#">Contact customer</a>
			    <a class="dropdown-item" href="#">Cancel order</a>
			  </div>
		  </div>
          
          </div> <!--flex-col-->
        </div><!--flex--> 
      
      </div>
   </div> <!--flex-->
   
   
   <div class="row mt-2"> 
     <div class="col-md-5">
     
      <div class="d-flex">
        <div class="mr-2">
        <img class="img-20"  src="http://localhost/kmrs2/backoffice/themes/classic/assets/images/account.png">
        </div>
        <div>
          <h5 class="m-0">Customer :</h5>
          <p class="m-0">Ceazar valencia</p>
          <p class="m-0">+639995351201</p>          
          <p class="m-0">basti@yahoo.com</p>
        </div>
      </div> <!--flex-->
      
      <div class="d-flex mt-4">
        <div class="mr-2">
        <img class="img-20" src="http://localhost/kmrs2/backoffice/themes/classic/assets/images/location.png">
        </div>
        <div>
          <h5 class="m-0">Delivery information :</h5>
          <p class="m-0">Ceazar valencia</p>
          <p class="m-0">Bangkal, Makati, Metro Manila, Philippines</p>
          <p class="m-0"><a href="https://www.google.com/maps/dir/?api=1&amp;destination=14.559150623577855,121.04071420104981" target="_blank" class="a-12"><u>Get direction</u></a></p>
          <p class="m-0">+639995351201</p>
        </div>
      </div> <!--flex-->
      
      <div class="d-flex mt-4">
        <div class="mr-2">
        <img class="img-20" src="http://localhost/kmrs2/backoffice/themes/classic/assets/images/orders-icon.png">
        </div>
        <div>
          <h5 class="m-0">Merchant information :</h5>
          <p class="m-0">Jollibee</p>
          <p class="m-0">Bangkal, Makati, Metro Manila, Philippines</p>
          <p class="m-0"><a href="https://www.google.com/maps/dir/?api=1&amp;destination=14.559150623577855,121.04071420104981" target="_blank" class="a-12"><u>Get direction</u></a></p>
          <p class="m-0">+639995351201</p>
        </div>
      </div> <!--flex-->
      
      <table class="table table-bordered mt-4">
      <tr>
       <td>Oder Type</td>
       <td>Delivery</td>
      </tr>
      <tr>
       <td>Delivery Date/Time</td>
       <td>Sat,Oct 30, 08:50 AM - 09:10 AM</td>
      </tr>
      <tr>
       <td>Payment Type</td>
       <td>Paypal</td>
      </tr>
      <tr>
       <td>Payment Status</td>
       <td>Paid</td>
      </tr>
       <tr>
       <td>Include utensils </td>
       <td>Yes</td>
      </tr>
      <tr>
       <td>Courier Tip</td>
       <td>$10.00</td>
      </tr>
      </table>
     
     </div> <!--col-->
     <div class="col-md-7">
     
     
     <div class="card border">
       <div class="card-body pt-3">
       
        <div class="d-flex mb-4 justify-content-between align-items-center">
           <div><h5 class="m-0">Summary</h5></div>
           <div><a class="btn btn-green small" href=""><i class="zmdi zmdi-edit mr-2"></i>Edit</a></div>
         </div> 
         
        <div class="row">
         <div class="col-2 d-flex justify-content-center">
         <img class="rounded img-50" src="http://localhost/kmrs2/themes/karenderia_v2/assets/images/sample-merchant-logo@2x.png" >
         </div>
         <div class="col-7 d-flex justify-content-start flex-column">1x food item 1 </div>
         <div class="col-3 d-flex justify-content-start flex-column text-right">10.00$</div>
       </div>
       
        <hr>
       
       <div class="row">
         <div class="col-2 d-flex justify-content-center">
         <img class="rounded img-50" src="http://localhost/kmrs2/themes/karenderia_v2/assets/images/sample-merchant-logo@2x.png" >
         </div>
         <div class="col-7 d-flex justify-content-start flex-column">1x food item 1 </div>
         <div class="col-3 d-flex justify-content-start flex-column text-right">10.00$</div>
       </div>
       
       <hr>
       
       <div class="row mb-1">
         <div class="col-6 d-flex justify-content-center">         
         </div>
         <div class="col-3 d-flex justify-content-start flex-column">Sub total (2 items)</div>
         <div class="col-3 d-flex justify-content-start flex-column text-right">10.00$</div>
       </div>
       
        <div class="row">
         <div class="col-6 d-flex justify-content-center">         
         </div>
         <div class="col-3 d-flex justify-content-start flex-column">Service fee</div>
         <div class="col-3 d-flex justify-content-start flex-column text-right">10.00$</div>
       </div>
              
       <hr>
       
        <div class="row">
         <div class="col-6 d-flex justify-content-center">         
         </div>
         <div class="col-3 d-flex justify-content-start flex-column"><h6 class="m-0">Total</h6></div>
         <div class="col-3 d-flex justify-content-start flex-column text-right"><h6 class="m-0">117.60$</h6></div>
       </div>
       
       </div> <!--body-->
     </div><!-- card-->
  
     
     </div> <!--col-->
   </div> <!--row-->

  </div> <!--body-->
</div> <!--card-->  