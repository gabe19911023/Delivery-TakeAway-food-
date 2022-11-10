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

/* items.html */
class __TwigTemplate_90e73b67262ab0f1eddbb8b8cd0d433002f1ad3eb92bdf0ff47840564c461732 extends Template
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
        echo "<table style=\"width:100%\"  class=\"items\" >
 <thead>
 <tr>
  <td style=\"width:50%;\"><b>";
        // line 4
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["label"] ?? null), "items_ordered", [], "any", false, false, false, 4), "html", null, true);
        echo "</b></td>
  <td style=\"width:30%;\"><b>";
        // line 5
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["label"] ?? null), "qty", [], "any", false, false, false, 5), "html", null, true);
        echo "</b></td>
  <td style=\"width:20%;\"><b>";
        // line 6
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["label"] ?? null), "price", [], "any", false, false, false, 6), "html", null, true);
        echo "</b></td>
 </tr>
 </thead>
 <tr>
  <td colspan=\"3\" style=\"padding:0;\"><div style=\"background-color:#B69A81; min-height:3px;\"></div></td>
 </tr>
 
  ";
        // line 13
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["items"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["item"]) {
            // line 14
            echo " <tr>
  <td>
  <b>";
            // line 16
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["item"], "item_name", [], "any", false, false, false, 16), "html", null, true);
            echo "</b>
  ";
            // line 17
            if (twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["item"], "price", [], "any", false, false, false, 17), "size_name", [], "any", false, false, false, 17)) {
                // line 18
                echo "  (";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["item"], "price", [], "any", false, false, false, 18), "size_name", [], "any", false, false, false, 18), "html", null, true);
                echo ")
  ";
            }
            // line 20
            echo "  
   <br/>
  
  ";
            // line 23
            if ((1 === twig_compare(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["item"], "price", [], "any", false, false, false, 23), "discount", [], "any", false, false, false, 23), 0))) {
                // line 24
                echo "    <del>";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["item"], "price", [], "any", false, false, false, 24), "pretty_price", [], "any", false, false, false, 24), "html", null, true);
                echo "</del> ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["item"], "price", [], "any", false, false, false, 24), "pretty_price_after_discount", [], "any", false, false, false, 24), "html", null, true);
                echo "
  ";
            } else {
                // line 26
                echo "     ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["item"], "price", [], "any", false, false, false, 26), "pretty_price", [], "any", false, false, false, 26), "html", null, true);
                echo "
  ";
            }
            // line 28
            echo "  
   ";
            // line 29
            if ((0 === twig_compare(twig_get_attribute($this->env, $this->source, $context["item"], "item_changes", [], "any", false, false, false, 29), "replacement"))) {
                // line 30
                echo "   <br>Replacement
   <br/>Replace \"";
                // line 31
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["item"], "item_name_replace", [], "any", false, false, false, 31), "html", null, true);
                echo "\"      
  ";
            }
            // line 33
            echo "  
  ";
            // line 34
            if (twig_get_attribute($this->env, $this->source, $context["item"], "special_instructions", [], "any", false, false, false, 34)) {
                // line 35
                echo "   ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["item"], "special_instructions", [], "any", false, false, false, 35), "html", null, true);
                echo "
  ";
            }
            // line 37
            echo "  
  ";
            // line 38
            if (twig_get_attribute($this->env, $this->source, $context["item"], "attributes", [], "any", false, false, false, 38)) {
                // line 39
                echo "  <br/>
     ";
                // line 40
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, $context["item"], "attributes", [], "any", false, false, false, 40));
                foreach ($context['_seq'] as $context["attributes_key"] => $context["attributes"]) {
                    // line 41
                    echo "        ";
                    $context['_parent'] = $context;
                    $context['_seq'] = twig_ensure_traversable($context["attributes"]);
                    foreach ($context['_seq'] as $context["attributes_index"] => $context["attributes_data"]) {
                        // line 42
                        echo "          ";
                        echo twig_escape_filter($this->env, $context["attributes_data"], "html", null, true);
                        echo "
             ";
                        // line 43
                        if ((-1 === twig_compare($context["attributes_index"], (twig_get_attribute($this->env, $this->source, $context["attributes"], "length", [], "any", false, false, false, 43) - 1)))) {
                            // line 44
                            echo "             ,
             ";
                        }
                        // line 46
                        echo "        ";
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
                // line 48
                echo "  ";
            }
            // line 49
            echo "  
  </td>
  <td style=\"padding:0 20px 0;\">";
            // line 51
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["item"], "qty", [], "any", false, false, false, 51), "html", null, true);
            echo "</td>
  <td>
   ";
            // line 53
            if ((1 === twig_compare(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["item"], "price", [], "any", false, false, false, 53), "discount", [], "any", false, false, false, 53), 0))) {
                // line 54
                echo "    ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["item"], "price", [], "any", false, false, false, 54), "pretty_total_after_discount", [], "any", false, false, false, 54), "html", null, true);
                echo "
  ";
            } else {
                // line 56
                echo "    ";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, $context["item"], "price", [], "any", false, false, false, 56), "pretty_total", [], "any", false, false, false, 56), "html", null, true);
                echo "
  ";
            }
            // line 58
            echo "  </td>
 </tr>      
 
<!--ADDON-->
";
            // line 62
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, $context["item"], "addons", [], "any", false, false, false, 62));
            foreach ($context['_seq'] as $context["index_addon"] => $context["addons"]) {
                // line 63
                echo "<tr>
 <td colspan=\"3\" style=\"padding:0 8px 0;\"><b>";
                // line 64
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["addons"], "subcategory_name", [], "any", false, false, false, 64), "html", null, true);
                echo "</b></td>     
</tr>
    
";
                // line 67
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, $context["addons"], "addon_items", [], "any", false, false, false, 67));
                foreach ($context['_seq'] as $context["_key"] => $context["addon_items"]) {
                    // line 68
                    echo "<tr>
 <td>";
                    // line 69
                    echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["addon_items"], "pretty_price", [], "any", false, false, false, 69), "html", null, true);
                    echo " ";
                    echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["addon_items"], "sub_item_name", [], "any", false, false, false, 69), "html", null, true);
                    echo "</td>
 <td style=\"padding:0 20px 0;\">";
                    // line 70
                    echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["addon_items"], "qty", [], "any", false, false, false, 70), "html", null, true);
                    echo "</td>
 <td>";
                    // line 71
                    echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["addon_items"], "pretty_addons_total", [], "any", false, false, false, 71), "html", null, true);
                    echo "</td>
</tr>
";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['addon_items'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 74
                echo "
";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['index_addon'], $context['addons'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 76
            echo "<!--ADDON-->

 <!-- ADDITIONAL CHARGE -->      
";
            // line 79
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->source, $context["item"], "additional_charge_list", [], "any", false, false, false, 79));
            foreach ($context['_seq'] as $context["_key"] => $context["item_charge"]) {
                // line 80
                echo "<tr>
 <td><i>";
                // line 81
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["item_charge"], "charge_name", [], "any", false, false, false, 81), "html", null, true);
                echo "</i></td>
 <td></td>
 <td>";
                // line 83
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["item_charge"], "pretty_price", [], "any", false, false, false, 83), "html", null, true);
                echo "</td>
</tr>
";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['item_charge'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 86
            echo "<!-- ADDITIONAL CHARGE -->  

 
 ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['item'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 89
        echo " <!--ITEMS-->
 
 <tr>
  <td colspan=\"3\" style=\"padding:0;\"><div style=\"background-color:#B69A81; min-height:3px;\"></div></td>
 </tr> 

 <!--SUMMARY-->    
";
        // line 96
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($context["summary"]);
        foreach ($context['_seq'] as $context["_key"] => $context["summary"]) {
            echo "    
 <tr class=\"summary_order\">
  <td></td>
  <td style=\"padding:0 20px 0;\">
  
      ";
            // line 101
            if ((0 === twig_compare(twig_get_attribute($this->env, $this->source, $context["summary"], "type", [], "any", false, false, false, 101), "total"))) {
                echo "  
       <b>";
                // line 102
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["summary"], "name", [], "any", false, false, false, 102), "html", null, true);
                echo " : </b>
      ";
            } else {
                // line 103
                echo "  
       ";
                // line 104
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["summary"], "name", [], "any", false, false, false, 104), "html", null, true);
                echo " :
      ";
            }
            // line 106
            echo "  
  </td>
  <td>
  
     ";
            // line 110
            if ((0 === twig_compare(twig_get_attribute($this->env, $this->source, $context["summary"], "type", [], "any", false, false, false, 110), "total"))) {
                echo "  
     <b>";
                // line 111
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["summary"], "value", [], "any", false, false, false, 111), "html", null, true);
                echo "</b>
     ";
            } else {
                // line 112
                echo "  
     ";
                // line 113
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, $context["summary"], "value", [], "any", false, false, false, 113), "html", null, true);
                echo "
     ";
            }
            // line 115
            echo "  
  </td>
 </tr> 
 ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['summary'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 118
        echo "      
 <!--SUMMARY-->      

</table>";
    }

    public function getTemplateName()
    {
        return "items.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  346 => 118,  337 => 115,  332 => 113,  329 => 112,  324 => 111,  320 => 110,  314 => 106,  309 => 104,  306 => 103,  301 => 102,  297 => 101,  287 => 96,  278 => 89,  269 => 86,  260 => 83,  255 => 81,  252 => 80,  248 => 79,  243 => 76,  236 => 74,  227 => 71,  223 => 70,  217 => 69,  214 => 68,  210 => 67,  204 => 64,  201 => 63,  197 => 62,  191 => 58,  185 => 56,  179 => 54,  177 => 53,  172 => 51,  168 => 49,  165 => 48,  153 => 46,  149 => 44,  147 => 43,  142 => 42,  137 => 41,  133 => 40,  130 => 39,  128 => 38,  125 => 37,  119 => 35,  117 => 34,  114 => 33,  109 => 31,  106 => 30,  104 => 29,  101 => 28,  95 => 26,  87 => 24,  85 => 23,  80 => 20,  74 => 18,  72 => 17,  68 => 16,  64 => 14,  60 => 13,  50 => 6,  46 => 5,  42 => 4,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("<table style=\"width:100%\"  class=\"items\" >
 <thead>
 <tr>
  <td style=\"width:50%;\"><b>{{ label.items_ordered }}</b></td>
  <td style=\"width:30%;\"><b>{{ label.qty }}</b></td>
  <td style=\"width:20%;\"><b>{{ label.price}}</b></td>
 </tr>
 </thead>
 <tr>
  <td colspan=\"3\" style=\"padding:0;\"><div style=\"background-color:#B69A81; min-height:3px;\"></div></td>
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
  <td colspan=\"3\" style=\"padding:0;\"><div style=\"background-color:#B69A81; min-height:3px;\"></div></td>
 </tr> 

 <!--SUMMARY-->    
{% for summary in summary %}    
 <tr class=\"summary_order\">
  <td></td>
  <td style=\"padding:0 20px 0;\">
  
      {% if summary.type=='total'  %}  
       <b>{{summary.name}} : </b>
      {% else %}  
       {{summary.name}} :
      {% endif %}
  
  </td>
  <td>
  
     {% if summary.type=='total'  %}  
     <b>{{summary.value}}</b>
     {% else %}  
     {{summary.value}}
     {% endif %}
  
  </td>
 </tr> 
 {% endfor %}      
 <!--SUMMARY-->      

</table>", "items.html", "/home/cilentw/www/geamenu.com/backoffice/twig/items.html");
    }
}
