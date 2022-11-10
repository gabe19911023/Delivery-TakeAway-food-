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

/* __string_template__46bb669f4bac5d0aa75ce5377131aad868fc124959dbbb60df3c715c97921fed */
class __TwigTemplate_4b0ef6d42edcd07cfd90e2774fd9566896f6a4cbd873a03ad5b232142550a5a3 extends Template
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
        echo "Order Summary";
    }

    public function getTemplateName()
    {
        return "__string_template__46bb669f4bac5d0aa75ce5377131aad868fc124959dbbb60df3c715c97921fed";
    }

    public function getDebugInfo()
    {
        return array (  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("Order Summary", "__string_template__46bb669f4bac5d0aa75ce5377131aad868fc124959dbbb60df3c715c97921fed", "");
    }
}
