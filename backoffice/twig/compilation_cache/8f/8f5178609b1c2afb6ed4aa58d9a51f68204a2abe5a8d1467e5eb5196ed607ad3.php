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

/* __string_template__c8f60c975442b59b2950112a1532029a05ea547f4a7f51eee6b72289f470c064 */
class __TwigTemplate_00b0689152354056b468c17ac32a5a90d4b0389c63f6a05ad52d0829771ea18d extends Template
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
        $this->loadTemplate("header.html", "__string_template__c8f60c975442b59b2950112a1532029a05ea547f4a7f51eee6b72289f470c064", 1)->display($context);
        // line 2
        echo "


<table style=\"width:100%;\">
 <tbody><tr>
  <td style=\"background:#fef9ef;padding:20px 30px;\">
    <img style=\"max-width:15%;max-height:50px;\" src=\"";
        // line 8
        echo twig_escape_filter($this->env, ($context["logo"] ?? null), "html", null, true);
        echo "\">
  </td>
 </tr>
 <tr>
   <td style=\"padding:30px;background:#ffffff;\" valign=\"middle\" align=\"center\">
    <h2 style=\"margin:0;\">Thanks for your order</h2>
    <p style=\"padding:10px;\">You'll receive an email when your food are ready to deliver. If you have any questions, Call us ";
        // line 14
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["merchant"] ?? null), "contact_phone", [], "any", false, false, false, 14), "html", null, true);
        echo ".</p>
    <br>    
    <a href=\"";
        // line 16
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["order"] ?? null), "order_info", [], "any", false, false, false, 16), "tracking_link", [], "any", false, false, false, 16), "html", null, true);
        echo "\" target=\"_blank\" style=\"display:block;margin:auto;max-width:200px;padding:10px;background:#3ecf8e;color:#fff;
     text-decoration:none;font-size:18px;font-weight:bold;\">
     Track Order
     </a>
    
   </td>
 </tr>
 
 <tr>
  <td style=\"background:#fef9ef;\">
      ";
        // line 26
        $this->loadTemplate("summary.html", "__string_template__c8f60c975442b59b2950112a1532029a05ea547f4a7f51eee6b72289f470c064", 26)->display($context);
        // line 27
        echo "  </td>
 </tr>
 
 <tr>
   <td style=\"background:#ffffff;\">
     ";
        // line 32
        $this->loadTemplate("items.html", "__string_template__c8f60c975442b59b2950112a1532029a05ea547f4a7f51eee6b72289f470c064", 32)->display($context);
        // line 33
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
        // line 46
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["site"] ?? null), "address", [], "any", false, false, false, 46), "html", null, true);
        echo "</p>
         <p>";
        // line 47
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["site"] ?? null), "contact", [], "any", false, false, false, 47), "html", null, true);
        echo "</p>
         <p>";
        // line 48
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["site"] ?? null), "email", [], "any", false, false, false, 48), "html", null, true);
        echo "</p>
\t    </td><td colspan=\"7\" style=\"padding:0 3px;\" valign=\"top\">
\t    
\t    ";
        // line 51
        $this->loadTemplate("social_link.html", "__string_template__c8f60c975442b59b2950112a1532029a05ea547f4a7f51eee6b72289f470c064", 51)->display($context);
        // line 52
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
        // line 70
        $this->loadTemplate("footer.html", "__string_template__c8f60c975442b59b2950112a1532029a05ea547f4a7f51eee6b72289f470c064", 70)->display($context);
    }

    public function getTemplateName()
    {
        return "__string_template__c8f60c975442b59b2950112a1532029a05ea547f4a7f51eee6b72289f470c064";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  136 => 70,  116 => 52,  114 => 51,  108 => 48,  104 => 47,  100 => 46,  85 => 33,  83 => 32,  76 => 27,  74 => 26,  61 => 16,  56 => 14,  47 => 8,  39 => 2,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("{% include 'header.html' %}



<table style=\"width:100%;\">
 <tbody><tr>
  <td style=\"background:#fef9ef;padding:20px 30px;\">
    <img style=\"max-width:15%;max-height:50px;\" src=\"{{logo}}\">
  </td>
 </tr>
 <tr>
   <td style=\"padding:30px;background:#ffffff;\" valign=\"middle\" align=\"center\">
    <h2 style=\"margin:0;\">Thanks for your order</h2>
    <p style=\"padding:10px;\">You'll receive an email when your food are ready to deliver. If you have any questions, Call us {{merchant.contact_phone}}.</p>
    <br>    
    <a href=\"{{order.order_info.tracking_link}}\" target=\"_blank\" style=\"display:block;margin:auto;max-width:200px;padding:10px;background:#3ecf8e;color:#fff;
     text-decoration:none;font-size:18px;font-weight:bold;\">
     Track Order
     </a>
    
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
\t    {% include 'social_link.html' %}
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

{% include 'footer.html' %}", "__string_template__c8f60c975442b59b2950112a1532029a05ea547f4a7f51eee6b72289f470c064", "");
    }
}
