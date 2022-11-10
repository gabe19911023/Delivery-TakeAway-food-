<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* print_order.html */
class __TwigTemplate_1a2d3ebcecce244878daa1d2b16f4d0cd8e4ba7b513c996ae5179e48bfcc4455 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">
<html>
<head>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-15\">
<link href='http://fonts.googleapis.com/css2?family=Petrona:ital,wght@0,100;0,200;0,400;0,500;1,100;1,200&display=swap' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,600;1,300&display=swap' rel='stylesheet' type='text/css'>
<style type=\"text/css\">
body {
font-family: 'Petrona', serif;
}\t

p{
font-family: 'Petrona', serif;
font-size:14px;
margin:0;
}\t

h5{
font-size:17px;
}
h5,h4,h3,h2,h1{
margin:0;\t
}
table.collapse {
  border-collapse: collapse;  
  font-size:14px;
}
table.collapse thead{
font-size:15px;
font-weight:600;
}

table.collapse td {  
  padding:8px 10px;
}
table.summary td{
padding:3px 5px;
}

th,td {
  padding: 3pt;  
}

.summary td,
table.items td,
table.summary_order td
{
font-size:16px;
}
table.items thead td{
font-size:17px;
}

table.summary_order b{
font-size:18px;
}

</style>
</head>
<body>

<table class=\"collapse\" style=\"width:100%;\">
 <tr>
  <td style=\"background:#fef9ef;padding:20px 30px;\">
    <img style=\"max-width:20%;max-height:50px;\" src=\"";
        // line 65
        echo twig_escape_filter($this->env, ($context["receipt_logo"] ?? null), "html", null, true);
        echo "\">
  </td>
 </tr>
 <tr>
   <td valign=\"middle\" align=\"center\" style=\"padding:30px;\">
    <h2 style=\"margin:0;\">";
        // line 70
        echo twig_escape_filter($this->env, ($context["receipt_thank_you"] ?? null), "html", null, true);
        echo "</h2>
   </td>
 </tr>
 
 <tr>
  <td style=\"background:#fef9ef;\">
   <table style=\"width:100%\" class=\"summary\">
     <tr>
      <td style=\"width:50%;\" valign=\"top\">        
        <table style=\"width:100%;\">
         <tr>
          <td colspan=\"2\"><h3>SUMMARY</h3></td>          
         </tr>
         <tr>
          <td style=\"width:35%;\" valign=\"top\">Order #:</td>
          <td valign=\"top\">";
        // line 85
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["order"] ?? null), "order_info", [], "any", false, false, false, 85), "order_id", [], "any", false, false, false, 85), "html", null, true);
        echo "</td>
         </tr>
         <tr>
          <td style=\"width:35%;\" valign=\"top\">Place On:</td>
          <td valign=\"top\">";
        // line 89
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["order"] ?? null), "order_info", [], "any", false, false, false, 89), "place_on", [], "any", false, false, false, 89), "html", null, true);
        echo "</td>
         </tr>
         <tr>
          <td style=\"width:35%;\" valign=\"top\">Order Total:</td>
          <td valign=\"top\">";
        // line 93
        echo twig_escape_filter($this->env, ($context["total"] ?? null), "html", null, true);
        echo "</td>
         </tr>
         <tr>
          <td style=\"width:35%;\" valign=\"top\">Delivery Date/Time:</td>
          <td valign=\"top\" >
           ";
        // line 98
        if ((0 === twig_compare(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["order"] ?? null), "order_info", [], "any", false, false, false, 98), "whento_deliver", [], "any", false, false, false, 98), "now"))) {
            // line 99
            echo "\t\t   ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["order"] ?? null), "order_info", [], "any", false, false, false, 99), "schedule_at", [], "any", false, false, false, 99), "html", null, true);
            echo "
\t\t   ";
        } else {
            // line 101
            echo "\t\t   ";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["order"] ?? null), "order_info", [], "any", false, false, false, 101), "schedule_at", [], "any", false, false, false, 101), "html", null, true);
            echo "
\t\t   ";
        }
        // line 103
        echo "          </td>
         </tr>
        </table>
      </td>
      <td style=\"width:50%;\" valign=\"top\">       
        <table style=\"width:100%;\">
         <tr>
          <td colspan=\"2\"><h3>DELIVERY ADDRESS</h3></td>          
         </tr>
         <tr><td>";
        // line 112
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["order"] ?? null), "order_info", [], "any", false, false, false, 112), "customer_name", [], "any", false, false, false, 112), "html", null, true);
        echo "</td></tr>
         <tr><td>";
        // line 113
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["order"] ?? null), "order_info", [], "any", false, false, false, 113), "contact_number", [], "any", false, false, false, 113), "html", null, true);
        echo "</td></tr>
         <tr><td>";
        // line 114
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["order"] ?? null), "order_info", [], "any", false, false, false, 114), "delivery_address", [], "any", false, false, false, 114), "html", null, true);
        echo "</td></tr>
        </table>
      </td>
     </tr>
   </table>   
  </td>
 </tr>
 
 <tr>
   <td>
     <table style=\"width:100%\"  class=\"items\" >
     <thead>
     <tr>
      <td style=\"width:50%;\">ITEMS ORDERED</td>
      <td style=\"width:30%;\">QTY</td>
      <td style=\"width:20%;\">PRICE</td>
     </tr>
     </thead>
     <tr>
      <td colspan=\"3\" style=\"padding:0;\"><div style=\"border-bottom:thin solid black;\"></div></td>
     </tr>
     
      ";
        // line 136
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["items"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["item"]) {
            // line 137
            echo "     <tr>
      <td>
      <b>";
            // line 139
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["item"], "item_name", [], "any", false, false, false, 139), "html", null, true);
            echo "</b>
      ";
            // line 140
            if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["item"], "price", [], "any", false, false, false, 140), "size_name", [], "any", false, false, false, 140)) {
                // line 141
                echo "      (";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["item"], "price", [], "any", false, false, false, 141), "size_name", [], "any", false, false, false, 141), "html", null, true);
                echo ")
      ";
            }
            // line 143
            echo "      
       <br/>
      
      ";
            // line 146
            if ((1 === twig_compare(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["item"], "price", [], "any", false, false, false, 146), "discount", [], "any", false, false, false, 146), 0))) {
                // line 147
                echo "        <del>";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["item"], "price", [], "any", false, false, false, 147), "pretty_price", [], "any", false, false, false, 147), "html", null, true);
                echo "</del> ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["item"], "price", [], "any", false, false, false, 147), "pretty_price_after_discount", [], "any", false, false, false, 147), "html", null, true);
                echo "
      ";
            } else {
                // line 149
                echo "         ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["item"], "price", [], "any", false, false, false, 149), "pretty_price", [], "any", false, false, false, 149), "html", null, true);
                echo "
      ";
            }
            // line 151
            echo "      
       ";
            // line 152
            if ((0 === twig_compare(twig_get_attribute($this->env, $this->source, $context["item"], "item_changes", [], "any", false, false, false, 152), "replacement"))) {
                // line 153
                echo "       <br>Replacement
       <br/>Replace \"";
                // line 154
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["item"], "item_name_replace", [], "any", false, false, false, 154), "html", null, true);
                echo "\"      
      ";
            }
            // line 156
            echo "      
      ";
            // line 157
            if (twig_get_attribute($this->env, $this->source, $context["item"], "special_instructions", [], "any", false, false, false, 157)) {
                // line 158
                echo "       ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["item"], "special_instructions", [], "any", false, false, false, 158), "html", null, true);
                echo "
      ";
            }
            // line 160
            echo "      
      ";
            // line 161
            if (twig_get_attribute($this->env, $this->source, $context["item"], "attributes", [], "any", false, false, false, 161)) {
                // line 162
                echo "      <br/>
         ";
                // line 163
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, $context["item"], "attributes", [], "any", false, false, false, 163));
                foreach ($context['_seq'] as $context["attributes_key"] => $context["attributes"]) {
                    // line 164
                    echo "            ";
                    $context['_parent'] = $context;
                    $context['_seq'] = twig_ensure_traversable($context["attributes"]);
                    foreach ($context['_seq'] as $context["attributes_index"] => $context["attributes_data"]) {
                        // line 165
                        echo "              ";
                        echo twig_escape_filter($this->env, $context["attributes_data"], "html", null, true);
                        echo "
                 ";
                        // line 166
                        if ((-1 === twig_compare($context["attributes_index"], (twig_get_attribute($this->env, $this->source, $context["attributes"], "length", [], "any", false, false, false, 166) - 1)))) {
                            // line 167
                            echo "                 ,
                 ";
                        }
                        // line 169
                        echo "            ";
                    }
                    $_parent = $context['_parent'];
                    unset($context['_seq'], $context['_iterated'], $context['attributes_index'], $context['attributes_data'], $context['_parent'], $context['loop']);
                    $context = array_intersect_key($context, $_parent) + $_parent;
                    echo " 
         ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['attributes_key'], $context['attributes'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 171
                echo "      ";
            }
            // line 172
            echo "      
      </td>
      <td style=\"padding:0 20px 0;\">";
            // line 174
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["item"], "qty", [], "any", false, false, false, 174), "html", null, true);
            echo "</td>
      <td>
       ";
            // line 176
            if ((1 === twig_compare(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["item"], "price", [], "any", false, false, false, 176), "discount", [], "any", false, false, false, 176), 0))) {
                // line 177
                echo "        ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["item"], "price", [], "any", false, false, false, 177), "pretty_total_after_discount", [], "any", false, false, false, 177), "html", null, true);
                echo "
      ";
            } else {
                // line 179
                echo "        ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["item"], "price", [], "any", false, false, false, 179), "pretty_total", [], "any", false, false, false, 179), "html", null, true);
                echo "
      ";
            }
            // line 181
            echo "      </td>
     </tr>      
     
    <!--ADDON-->
    ";
            // line 185
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, $context["item"], "addons", [], "any", false, false, false, 185));
            foreach ($context['_seq'] as $context["index_addon"] => $context["addons"]) {
                // line 186
                echo "    <tr>
     <td colspan=\"3\" style=\"padding:0 8px 0;\"><b>";
                // line 187
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["addons"], "subcategory_name", [], "any", false, false, false, 187), "html", null, true);
                echo "</b></td>     
    </tr>
        
    ";
                // line 190
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, $context["addons"], "addon_items", [], "any", false, false, false, 190));
                foreach ($context['_seq'] as $context["_key"] => $context["addon_items"]) {
                    // line 191
                    echo "    <tr>
     <td>";
                    // line 192
                    echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["addon_items"], "pretty_price", [], "any", false, false, false, 192), "html", null, true);
                    echo " ";
                    echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["addon_items"], "sub_item_name", [], "any", false, false, false, 192), "html", null, true);
                    echo "</td>
     <td style=\"padding:0 20px 0;\">";
                    // line 193
                    echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["addon_items"], "qty", [], "any", false, false, false, 193), "html", null, true);
                    echo "</td>
     <td>";
                    // line 194
                    echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["addon_items"], "pretty_addons_total", [], "any", false, false, false, 194), "html", null, true);
                    echo "</td>
    </tr>
    ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['addon_items'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 197
                echo "    
    ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['index_addon'], $context['addons'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 199
            echo "    <!--ADDON-->
    
     <!-- ADDITIONAL CHARGE -->      
    ";
            // line 202
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, $context["item"], "additional_charge_list", [], "any", false, false, false, 202));
            foreach ($context['_seq'] as $context["_key"] => $context["item_charge"]) {
                // line 203
                echo "    <tr>
     <td><i>";
                // line 204
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["item_charge"], "charge_name", [], "any", false, false, false, 204), "html", null, true);
                echo "</i></td>
     <td></td>
     <td>";
                // line 206
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["item_charge"], "pretty_price", [], "any", false, false, false, 206), "html", null, true);
                echo "</td>
    </tr>
    ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['item_charge'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 209
            echo "    <!-- ADDITIONAL CHARGE -->  
    
     
     ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['item'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 212
        echo " <!--ITEMS-->
     
     <tr>
      <td colspan=\"3\" style=\"padding:0;\"><div style=\"border-bottom:thin solid black;\"></div></td>
     </tr> 
    
     <!--SUMMARY-->    
    ";
        // line 219
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($context["summary"]);
        foreach ($context['_seq'] as $context["_key"] => $context["summary"]) {
            echo "    
     <tr class=\"summary_order\">
      <td></td>
      <td style=\"padding:0 20px 0;\">
      
          ";
            // line 224
            if ((0 === twig_compare(twig_get_attribute($this->env, $this->source, $context["summary"], "type", [], "any", false, false, false, 224), "total"))) {
                echo "  
\t       <b>";
                // line 225
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["summary"], "name", [], "any", false, false, false, 225), "html", null, true);
                echo " : </b>
\t      ";
            } else {
                // line 226
                echo "  
\t       ";
                // line 227
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["summary"], "name", [], "any", false, false, false, 227), "html", null, true);
                echo " :
\t      ";
            }
            // line 229
            echo "      
      </td>
      <td>
      
         ";
            // line 233
            if ((0 === twig_compare(twig_get_attribute($this->env, $this->source, $context["summary"], "type", [], "any", false, false, false, 233), "total"))) {
                echo "  
\t     <b>";
                // line 234
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["summary"], "value", [], "any", false, false, false, 234), "html", null, true);
                echo "</b>
\t     ";
            } else {
                // line 235
                echo "  
\t     ";
                // line 236
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["summary"], "value", [], "any", false, false, false, 236), "html", null, true);
                echo "
\t     ";
            }
            // line 238
            echo "      
      </td>
     </tr> 
     ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['summary'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 241
        echo "      
     <!--SUMMARY-->      
    
     
     </table>
   </td>
 </tr>
  
 <tr>
  <td style=\"background:#fef9ef;padding:20px 30px;\">
    
    <table style=\"width:100%; table-layout: fixed;\">
\t  <tr>
\t    <th colspan=\"3\" style=\"text-align: left;\"><h5>Contact Us</h5></th>
\t    <th colspan=\"7\" style=\"text-align: left;\"><h5>Information</h5></th>
\t  </tr>
\t  <tr>
\t    <td colspan=\"3\" style=\"text-align: left; padding:0 3px;\" valign=\"top\">
\t     <p>";
        // line 259
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["site"] ?? null), "address", [], "any", false, false, false, 259), "html", null, true);
        echo "</p>
         <p>";
        // line 260
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["site"] ?? null), "contact", [], "any", false, false, false, 260), "html", null, true);
        echo "</p>
         <p>";
        // line 261
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["site"] ?? null), "email", [], "any", false, false, false, 261), "html", null, true);
        echo "</p
\t    </td>
\t    <td colspan=\"7\" valign=\"top\" style=\"padding:0 3px;\"><p>";
        // line 263
        echo twig_escape_filter($this->env, ($context["receipt_footer"] ?? null), "html", null, true);
        echo "</p></td>
\t  </tr>
\t</table>
  
  </td>
 </tr>
 
</table>

</body>
</html>";
    }

    public function getTemplateName()
    {
        return "print_order.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  528 => 263,  523 => 261,  519 => 260,  515 => 259,  495 => 241,  486 => 238,  481 => 236,  478 => 235,  473 => 234,  469 => 233,  463 => 229,  458 => 227,  455 => 226,  450 => 225,  446 => 224,  436 => 219,  427 => 212,  418 => 209,  409 => 206,  404 => 204,  401 => 203,  397 => 202,  392 => 199,  385 => 197,  376 => 194,  372 => 193,  366 => 192,  363 => 191,  359 => 190,  353 => 187,  350 => 186,  346 => 185,  340 => 181,  334 => 179,  328 => 177,  326 => 176,  321 => 174,  317 => 172,  314 => 171,  302 => 169,  298 => 167,  296 => 166,  291 => 165,  286 => 164,  282 => 163,  279 => 162,  277 => 161,  274 => 160,  268 => 158,  266 => 157,  263 => 156,  258 => 154,  255 => 153,  253 => 152,  250 => 151,  244 => 149,  236 => 147,  234 => 146,  229 => 143,  223 => 141,  221 => 140,  217 => 139,  213 => 137,  209 => 136,  184 => 114,  180 => 113,  176 => 112,  165 => 103,  159 => 101,  153 => 99,  151 => 98,  143 => 93,  136 => 89,  129 => 85,  111 => 70,  103 => 65,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">
<html>
<head>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-15\">
<link href='http://fonts.googleapis.com/css2?family=Petrona:ital,wght@0,100;0,200;0,400;0,500;1,100;1,200&display=swap' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,600;1,300&display=swap' rel='stylesheet' type='text/css'>
<style type=\"text/css\">
body {
font-family: 'Petrona', serif;
}\t

p{
font-family: 'Petrona', serif;
font-size:14px;
margin:0;
}\t

h5{
font-size:17px;
}
h5,h4,h3,h2,h1{
margin:0;\t
}
table.collapse {
  border-collapse: collapse;  
  font-size:14px;
}
table.collapse thead{
font-size:15px;
font-weight:600;
}

table.collapse td {  
  padding:8px 10px;
}
table.summary td{
padding:3px 5px;
}

th,td {
  padding: 3pt;  
}

.summary td,
table.items td,
table.summary_order td
{
font-size:16px;
}
table.items thead td{
font-size:17px;
}

table.summary_order b{
font-size:18px;
}

</style>
</head>
<body>

<table class=\"collapse\" style=\"width:100%;\">
 <tr>
  <td style=\"background:#fef9ef;padding:20px 30px;\">
    <img style=\"max-width:20%;max-height:50px;\" src=\"{{receipt_logo}}\">
  </td>
 </tr>
 <tr>
   <td valign=\"middle\" align=\"center\" style=\"padding:30px;\">
    <h2 style=\"margin:0;\">{{receipt_thank_you}}</h2>
   </td>
 </tr>
 
 <tr>
  <td style=\"background:#fef9ef;\">
   <table style=\"width:100%\" class=\"summary\">
     <tr>
      <td style=\"width:50%;\" valign=\"top\">        
        <table style=\"width:100%;\">
         <tr>
          <td colspan=\"2\"><h3>SUMMARY</h3></td>          
         </tr>
         <tr>
          <td style=\"width:35%;\" valign=\"top\">Order #:</td>
          <td valign=\"top\">{{order.order_info.order_id}}</td>
         </tr>
         <tr>
          <td style=\"width:35%;\" valign=\"top\">Place On:</td>
          <td valign=\"top\">{{order.order_info.place_on}}</td>
         </tr>
         <tr>
          <td style=\"width:35%;\" valign=\"top\">Order Total:</td>
          <td valign=\"top\">{{total}}</td>
         </tr>
         <tr>
          <td style=\"width:35%;\" valign=\"top\">Delivery Date/Time:</td>
          <td valign=\"top\" >
           {% if order.order_info.whento_deliver=='now' %}
\t\t   {{order.order_info.schedule_at}}
\t\t   {% else %}
\t\t   {{order.order_info.schedule_at}}
\t\t   {% endif %}
          </td>
         </tr>
        </table>
      </td>
      <td style=\"width:50%;\" valign=\"top\">       
        <table style=\"width:100%;\">
         <tr>
          <td colspan=\"2\"><h3>DELIVERY ADDRESS</h3></td>          
         </tr>
         <tr><td>{{order.order_info.customer_name}}</td></tr>
         <tr><td>{{order.order_info.contact_number}}</td></tr>
         <tr><td>{{order.order_info.delivery_address}}</td></tr>
        </table>
      </td>
     </tr>
   </table>   
  </td>
 </tr>
 
 <tr>
   <td>
     <table style=\"width:100%\"  class=\"items\" >
     <thead>
     <tr>
      <td style=\"width:50%;\">ITEMS ORDERED</td>
      <td style=\"width:30%;\">QTY</td>
      <td style=\"width:20%;\">PRICE</td>
     </tr>
     </thead>
     <tr>
      <td colspan=\"3\" style=\"padding:0;\"><div style=\"border-bottom:thin solid black;\"></div></td>
     </tr>
     
      {% for item in items %}
     <tr>
      <td>
      <b>{{item.item_name}}</b>
      {% if item.price.size_name %}
      ({{item.price.size_name}})
      {% endif %}
      
       <br/>
      
      {% if item.price.discount>0 %}
        <del>{{item.price.pretty_price}}</del> {{item.price.pretty_price_after_discount}}
      {% else %}
         {{item.price.pretty_price}}
      {% endif %}
      
       {% if item.item_changes=='replacement' %}
       <br>Replacement
       <br/>Replace \"{{item.item_name_replace}}\"      
      {% endif %}
      
      {% if item.special_instructions %}
       {{item.special_instructions}}
      {% endif %}
      
      {% if item.attributes %}
      <br/>
         {% for attributes_key, attributes in item.attributes %}
            {% for attributes_index, attributes_data in attributes %}
              {{attributes_data}}
                 {% if attributes_index<(attributes.length-1) %}
                 ,
                 {% endif %}
            {% endfor %} 
         {% endfor %}
      {% endif %}
      
      </td>
      <td style=\"padding:0 20px 0;\">{{item.qty}}</td>
      <td>
       {% if item.price.discount>0 %}
        {{ item.price.pretty_total_after_discount }}
      {% else %}
        {{ item.price.pretty_total }}
      {% endif %}
      </td>
     </tr>      
     
    <!--ADDON-->
    {% for index_addon, addons in item.addons %}
    <tr>
     <td colspan=\"3\" style=\"padding:0 8px 0;\"><b>{{addons.subcategory_name}}</b></td>     
    </tr>
        
    {% for addon_items in addons.addon_items %}
    <tr>
     <td>{{addon_items.pretty_price}} {{addon_items.sub_item_name}}</td>
     <td style=\"padding:0 20px 0;\">{{addon_items.qty}}</td>
     <td>{{addon_items.pretty_addons_total}}</td>
    </tr>
    {% endfor %}
    
    {% endfor %}
    <!--ADDON-->
    
     <!-- ADDITIONAL CHARGE -->      
    {% for item_charge in item.additional_charge_list %}
    <tr>
     <td><i>{{item_charge.charge_name}}</i></td>
     <td></td>
     <td>{{item_charge.pretty_price}}</td>
    </tr>
    {% endfor %}
    <!-- ADDITIONAL CHARGE -->  
    
     
     {% endfor %} <!--ITEMS-->
     
     <tr>
      <td colspan=\"3\" style=\"padding:0;\"><div style=\"border-bottom:thin solid black;\"></div></td>
     </tr> 
    
     <!--SUMMARY-->    
    {% for summary in summary %}    
     <tr class=\"summary_order\">
      <td></td>
      <td style=\"padding:0 20px 0;\">
      
          {% if summary.type=='total'  %}  
\t       <b>{{summary.name}} : </b>
\t      {% else %}  
\t       {{summary.name}} :
\t      {% endif %}
      
      </td>
      <td>
      
         {% if summary.type=='total'  %}  
\t     <b>{{summary.value}}</b>
\t     {% else %}  
\t     {{summary.value}}
\t     {% endif %}
      
      </td>
     </tr> 
     {% endfor %}      
     <!--SUMMARY-->      
    
     
     </table>
   </td>
 </tr>
  
 <tr>
  <td style=\"background:#fef9ef;padding:20px 30px;\">
    
    <table style=\"width:100%; table-layout: fixed;\">
\t  <tr>
\t    <th colspan=\"3\" style=\"text-align: left;\"><h5>Contact Us</h5></th>
\t    <th colspan=\"7\" style=\"text-align: left;\"><h5>Information</h5></th>
\t  </tr>
\t  <tr>
\t    <td colspan=\"3\" style=\"text-align: left; padding:0 3px;\" valign=\"top\">
\t     <p>{{site.address}}</p>
         <p>{{site.contact}}</p>
         <p>{{site.email}}</p
\t    </td>
\t    <td colspan=\"7\" valign=\"top\" style=\"padding:0 3px;\"><p>{{receipt_footer}}</p></td>
\t  </tr>
\t</table>
  
  </td>
 </tr>
 
</table>

</body>
</html>", "print_order.html", "/home/cilentw/www/geamenu.com/backoffice/twig/print_order.html");
    }
}
