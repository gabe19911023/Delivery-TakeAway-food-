
 <!--COMPONENTS CHANGE ADDRESS--> 		   
<component-cart-preview
ref="childref"
@set-cartcount="cartCount"
@after-drawerclose="afterDrawerclose"
cart_preview="<?php echo $cart_preview?>"
:drawer="drawer_preview_cart"
:payload="['items','subtotal','distance_local','merchant_info','go_checkout','items_count']"
:label="{
  your_cart: '<?php echo t("Your cart from")?>',
  summary: '<?php echo t("Summary")?>',
  clear: '<?php echo t("Clear")?>',
  no_order: '<?php echo CJavaScript::quote(t("You don't have any orders here!"))?>',
  lets_change_that: '<?php echo CJavaScript::quote(t("let's change that!"))?>',
  cart: '<?php echo t("Cart")?>',
  go_checkout: '<?php echo t("Go to checkout")?>'
}"
>
</component-cart-preview> 
<!--END COMPONENTS CHANGE ADDRESS-->