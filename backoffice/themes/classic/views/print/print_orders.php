<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-15">
<link href='http://fonts.googleapis.com/css2?family=Petrona:ital,wght@0,100;0,200;0,400;0,500;1,100;1,200&display=swap' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,600;1,300&display=swap' rel='stylesheet' type='text/css'>
<style type="text/css">
body {
font-family: 'Petrona', serif;
}	

p{
font-family: 'Petrona', serif;
font-size:14px;
margin:0;
}	

h5,h4{
margin:0;	
}
table.collapse {
  border-collapse: collapse;
  border: 1pt solid black;  
  font-size:14px;
}
table.collapse thead{
font-size:15px;
font-weight:600;
}

table.collapse td {
  border: 1pt solid black;
}
th,td {
  padding: 3pt;
}
thead {
  background-color: #eeeeee;
}
</style>
</head>
<body>


<table style="width: 90%;margin:auto;" >
<tr>
 <td style="width: 50%; vertical-align: middle;"><img style="width:20%;max-height:50px;" src="../../upload/all/447da499-36e3-11ec-a24b-9c5c8e164c2c.png"/></td>
 <td style="width: 50%; vertical-align: middle;text-align: right;" ><h4>RECEIPT</h4></td>
</tr>

<tr>
 <td colspan="2" style="width: 100%; vertical-align: middle;">  
  <h5>{{merchant.restaurant_name}}</h5>
  <p>{{merchant.merchant_address}}</p>
  <p>Phone : {{merchant.contact_phone}} /  Email : {{merchant.contact_phone}}</p>  
 </td> 
</tr>

<tr>
 <td style="width:50%; vertical-align: top; padding-top:20px;" >
  <p>Order ID : {{order_info.order_id}}</p>
  <p>Customer Name : {{order_info.customer_name}}</p>
  <p>Phone : {{order_info.contact_number}}</p>
  <p>Address : {{order_info.delivery_address}}</p>
  <p>{{order_info.payment_name}}</p>
 </td>
 <td style="width:50%;text-align: right;vertical-align: top; padding-top:20px;"><p>{{order_info.place_on}}</p></td>
</tr>

<tr>
 <td colspan="2" style="padding-top:20px; padding-bottom:20px;">
 
 <table class="collapse" style="width: 100%"  > 
   <thead>
    <tr>
    <td style="width:20%">Qty</td>
    <td>Description</td>
    <td style="width:25%">Price</td>
    </tr>
   </thead>
   <tbody> 
    <tr>
     <td>1</td>
     <td>Dried fish</td>
     <td>10.00$</td>
    </tr>
    
    <tr>
     <td></td>
     <td>Sub total</td>
     <td>10.00$</td>
    </tr>
    <tr>
     <td></td>
     <td>Total</td>
     <td>10.00$</td>
    </tr>
    
   </tbody>
 </table>
 
 
 </td>
</tr>

</table>

</body>
</html>