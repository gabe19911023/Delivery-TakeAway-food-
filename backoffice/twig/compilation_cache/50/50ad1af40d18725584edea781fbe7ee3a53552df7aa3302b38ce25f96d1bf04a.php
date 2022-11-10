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

/* header.html */
class __TwigTemplate_a7ddd87ac03882a5f76cca5dfbe85b4d5152c4a55840d2ac07f5a69dae750cd6 extends Template
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
        echo "<!DOCTYPE html>
<html lang=\"en\">
<head>
<meta charset=\"utf-8\">
<meta name=\"viewport\" content=\"width=device-width, initial-scale=1, shrink-to-fit=no\">
<meta name=\"robots\" content=\"noindex, nofollow\" />
<link href='http://fonts.googleapis.com/css2?family=Petrona:ital,wght@0,100;0,200;0,400;0,500;1,100;1,200&display=swap' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,600;1,300&display=swap' rel='stylesheet' type='text/css'>
<style type=\"text/css\">
body {
font-family: 'Petrona', serif;
background:#fff;
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
<body>";
    }

    public function getTemplateName()
    {
        return "header.html";
    }

    public function getDebugInfo()
    {
        return array (  37 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("<!DOCTYPE html>
<html lang=\"en\">
<head>
<meta charset=\"utf-8\">
<meta name=\"viewport\" content=\"width=device-width, initial-scale=1, shrink-to-fit=no\">
<meta name=\"robots\" content=\"noindex, nofollow\" />
<link href='http://fonts.googleapis.com/css2?family=Petrona:ital,wght@0,100;0,200;0,400;0,500;1,100;1,200&display=swap' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,600;1,300&display=swap' rel='stylesheet' type='text/css'>
<style type=\"text/css\">
body {
font-family: 'Petrona', serif;
background:#fff;
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
<body>", "header.html", "/home/cilentw/www/geamenu.com/backoffice/twig/header.html");
    }
}
