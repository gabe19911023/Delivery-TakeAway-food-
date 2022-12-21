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

/* __string_template__220d85a76f90bddf5c293d8591c2f5cee73018e052f85ad77ae93056e139dbf3 */
class __TwigTemplate_ba7210bcac79bc3c3b636200ecd177be27399e05fbf721a771161c6ee7732baf extends Template
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
        $this->loadTemplate("header.html", "__string_template__220d85a76f90bddf5c293d8591c2f5cee73018e052f85ad77ae93056e139dbf3", 1)->display($context);
        // line 2
        echo "<table style=\"width:100%;\">
 <tbody><tr>
  <td style=\"background:#fef9ef;padding:20px 30px;\">
    <img style=\"max-width:20%;max-height:50px;\" src=\"";
        // line 5
        echo twig_escape_filter($this->env, ($context["logo"] ?? null), "html", null, true);
        echo "\">
  </td>
 </tr>
 <tr>
   <td style=\"padding:30px;background:#ffffff;\" valign=\"middle\" align=\"left\">
    
    <p>Hi ";
        // line 11
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["order_info"] ?? null), "customer_name", [], "any", false, false, false, 11), "html", null, true);
        echo ",</p>
\t
\t<p>We are sorry the item(s) from your order ";
        // line 13
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["order_info"] ?? null), "order_id", [], "any", false, false, false, 13), "html", null, true);
        echo " is taking longer than expected. 
\tWe are working closely with the restaurant team to deliver this order as soon as possible.​</p>
\t
\t<p><b>";
        // line 16
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["order_info"] ?? null), "delayed_order", [], "any", false, false, false, 16), "html", null, true);
        echo "</b></p>
\t
\t<p>
\tPlease make sure to turn on your App notification to get the latest updates on your order. 
\t</p>
    
   </td>
 </tr>
 
 <tr>
  <td style=\"background:#fef9ef;\">
  
     ";
        // line 28
        $this->loadTemplate("summary.html", "__string_template__220d85a76f90bddf5c293d8591c2f5cee73018e052f85ad77ae93056e139dbf3", 28)->display($context);
        // line 29
        echo "   
  </td>
 </tr>
 
 <tr>
   <td style=\"background:#ffffff;\">
     ";
        // line 35
        $this->loadTemplate("items.html", "__string_template__220d85a76f90bddf5c293d8591c2f5cee73018e052f85ad77ae93056e139dbf3", 35)->display($context);
        // line 36
        echo "   </td>
 </tr>
  
 <tr>
  <td style=\"background:#fef9ef;padding:20px 30px;\">
    
   <table style=\"width:100%; table-layout: fixed;\">
\t  <tbody><tr>
\t    <th colspan=\"3\" style=\"text-align: left;\"><h5>Contact Us</h5></th>
\t    <th colspan=\"7\" style=\"text-align: left;\"><h5>For  promos, news, and updates, follow us on:</h5></th>
\t  </tr>
\t  <tr>
\t    <td colspan=\"3\" style=\"text-align: left; padding:0 3px;\" valign=\"top\">
\t     <p>";
        // line 49
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["site"] ?? null), "address", [], "any", false, false, false, 49), "html", null, true);
        echo "</p>
         <p>";
        // line 50
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["site"] ?? null), "contact", [], "any", false, false, false, 50), "html", null, true);
        echo "</p>
         <p>";
        // line 51
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["site"] ?? null), "email", [], "any", false, false, false, 51), "html", null, true);
        echo "</p>
\t    </td><td colspan=\"7\" style=\"padding:0 3px;\" valign=\"top\">
\t    
\t      ";
        // line 54
        $this->loadTemplate("social_link.html", "__string_template__220d85a76f90bddf5c293d8591c2f5cee73018e052f85ad77ae93056e139dbf3", 54)->display($context);
        // line 55
        echo "\t     
\t     <table>
\t      <tbody><tr>
\t      <td style=\"padding:0;\"><a href=\"#\" style=\"color:#000;font-size:16px;\">Terms and Conditions</a></td>
\t      <td>●</td>
\t      <td style=\"padding:0;\"><a href=\"#\" style=\"color:#000;font-size:16px;\">Privacy Policy</a></td>
\t      </tr>
\t     </tbody></table>
\t    
\t    </td>
\t  </tr>
\t</tbody></table>
  
  </td>
 </tr>
 
</tbody></table>
";
        // line 72
        $this->loadTemplate("footer.html", "__string_template__220d85a76f90bddf5c293d8591c2f5cee73018e052f85ad77ae93056e139dbf3", 72)->display($context);
    }

    public function getTemplateName()
    {
        return "__string_template__220d85a76f90bddf5c293d8591c2f5cee73018e052f85ad77ae93056e139dbf3";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  141 => 72,  122 => 55,  120 => 54,  114 => 51,  110 => 50,  106 => 49,  91 => 36,  89 => 35,  81 => 29,  79 => 28,  64 => 16,  58 => 13,  53 => 11,  44 => 5,  39 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% include 'header.html' %}
<table style=\"width:100%;\">
 <tbody><tr>
  <td style=\"background:#fef9ef;padding:20px 30px;\">
    <img style=\"max-width:20%;max-height:50px;\" src=\"{{logo}}\">
  </td>
 </tr>
 <tr>
   <td style=\"padding:30px;background:#ffffff;\" valign=\"middle\" align=\"left\">
    
    <p>Hi {{order_info.customer_name}},</p>
\t
\t<p>We are sorry the item(s) from your order {{order_info.order_id}} is taking longer than expected. 
\tWe are working closely with the restaurant team to deliver this order as soon as possible.​</p>
\t
\t<p><b>{{order_info.delayed_order}}</b></p>
\t
\t<p>
\tPlease make sure to turn on your App notification to get the latest updates on your order. 
\t</p>
    
   </td>
 </tr>
 
 <tr>
  <td style=\"background:#fef9ef;\">
  
     {% include 'summary.html' %}
   
  </td>
 </tr>
 
 <tr>
   <td style=\"background:#ffffff;\">
     {% include 'items.html' %}
   </td>
 </tr>
  
 <tr>
  <td style=\"background:#fef9ef;padding:20px 30px;\">
    
   <table style=\"width:100%; table-layout: fixed;\">
\t  <tbody><tr>
\t    <th colspan=\"3\" style=\"text-align: left;\"><h5>Contact Us</h5></th>
\t    <th colspan=\"7\" style=\"text-align: left;\"><h5>For  promos, news, and updates, follow us on:</h5></th>
\t  </tr>
\t  <tr>
\t    <td colspan=\"3\" style=\"text-align: left; padding:0 3px;\" valign=\"top\">
\t     <p>{{site.address}}</p>
         <p>{{site.contact}}</p>
         <p>{{site.email}}</p>
\t    </td><td colspan=\"7\" style=\"padding:0 3px;\" valign=\"top\">
\t    
\t      {% include 'social_link.html' %}
\t     
\t     <table>
\t      <tbody><tr>
\t      <td style=\"padding:0;\"><a href=\"#\" style=\"color:#000;font-size:16px;\">Terms and Conditions</a></td>
\t      <td>●</td>
\t      <td style=\"padding:0;\"><a href=\"#\" style=\"color:#000;font-size:16px;\">Privacy Policy</a></td>
\t      </tr>
\t     </tbody></table>
\t    
\t    </td>
\t  </tr>
\t</tbody></table>
  
  </td>
 </tr>
 
</tbody></table>
{% include 'footer.html' %}
", "__string_template__220d85a76f90bddf5c293d8591c2f5cee73018e052f85ad77ae93056e139dbf3", "");
    }
}
