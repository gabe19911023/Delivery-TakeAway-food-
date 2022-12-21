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

/* social_link.html */
class __TwigTemplate_1c0d30efd8545f8aa91dd5d57ea6a3d0e9a9fd15289cdaa1e0e8c9cfa1c9a93b extends Template
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
        echo "<table style=\"margin-bottom:20px;\">
 <tbody>
  <tr>
   <td style=\"padding:0 20px 0 0;\"><a href=\"";
        // line 4
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["social"] ?? null), "facebook", [], "any", false, false, false, 4), "html", null, true);
        echo "\" target=\"_blank\"><img style=\"width:35px;\" src=\"";
        echo twig_escape_filter($this->env, ($context["facebook"] ?? null), "html", null, true);
        echo "\"></a></td>\t       
   <td style=\"padding:0 20px 0 0;\"><a href=\"";
        // line 5
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["social"] ?? null), "twitter", [], "any", false, false, false, 5), "html", null, true);
        echo "\" target=\"_blank\"><img style=\"width:40px;\" src=\"";
        echo twig_escape_filter($this->env, ($context["twitter"] ?? null), "html", null, true);
        echo "\"></a></td>\t       
   <td style=\"padding:0 20px 0 0;\"><a href=\"";
        // line 6
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["social"] ?? null), "instagram", [], "any", false, false, false, 6), "html", null, true);
        echo "\" target=\"_blank\"><img style=\"width:40px;\" src=\"";
        echo twig_escape_filter($this->env, ($context["instagram"] ?? null), "html", null, true);
        echo "\"></a></td>\t       
   <td style=\"padding:0 20px 0 0;\"><a href=\"";
        // line 7
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["social"] ?? null), "whatsapp", [], "any", false, false, false, 7), "html", null, true);
        echo "\" target=\"_blank\"><img style=\"width:35px;\" src=\"";
        echo twig_escape_filter($this->env, ($context["whatsapp"] ?? null), "html", null, true);
        echo "\"></a></td>\t       
   <td style=\"padding:0 20px 0 0;\"><a href=\"";
        // line 8
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, ($context["social"] ?? null), "youtube", [], "any", false, false, false, 8), "html", null, true);
        echo "\" target=\"_blank\"><img style=\"width:35px;\" src=\"";
        echo twig_escape_filter($this->env, ($context["youtube"] ?? null), "html", null, true);
        echo "\"></a></td>\t       
  </tr>
</tbody>
</table>";
    }

    public function getTemplateName()
    {
        return "social_link.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  66 => 8,  60 => 7,  54 => 6,  48 => 5,  42 => 4,  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("<table style=\"margin-bottom:20px;\">
 <tbody>
  <tr>
   <td style=\"padding:0 20px 0 0;\"><a href=\"{{social.facebook}}\" target=\"_blank\"><img style=\"width:35px;\" src=\"{{facebook}}\"></a></td>\t       
   <td style=\"padding:0 20px 0 0;\"><a href=\"{{social.twitter}}\" target=\"_blank\"><img style=\"width:40px;\" src=\"{{twitter}}\"></a></td>\t       
   <td style=\"padding:0 20px 0 0;\"><a href=\"{{social.instagram}}\" target=\"_blank\"><img style=\"width:40px;\" src=\"{{instagram}}\"></a></td>\t       
   <td style=\"padding:0 20px 0 0;\"><a href=\"{{social.whatsapp}}\" target=\"_blank\"><img style=\"width:35px;\" src=\"{{whatsapp}}\"></a></td>\t       
   <td style=\"padding:0 20px 0 0;\"><a href=\"{{social.youtube}}\" target=\"_blank\"><img style=\"width:35px;\" src=\"{{youtube}}\"></a></td>\t       
  </tr>
</tbody>
</table>", "social_link.html", "D:\\DevOps\\work\\PHP(yii&vue)\\GEA-COM\\GEA-COM\\backoffice\\twig\\social_link.html");
    }
}
