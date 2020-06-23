<?php

/* @Theme/layout.html */
class __TwigTemplate_27431978caefadbd2244d62c26371a2c42624e70b8d003fdc6bea80add3d11ef extends Core\View\Base
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!DOCTYPE html>
<html ";
        // line 2
        echo (isset($context["html"]) ? $context["html"] : null);
        echo ">
\t<head>
\t\t<title>";
        // line 4
        echo (isset($context["title"]) ? $context["title"] : null);
        echo "</title>
\t\t";
        // line 5
        echo (isset($context["header"]) ? $context["header"] : null);
        echo "
\t\t<link href=\"https://fonts.googleapis.com/css?family=Roboto:300,400,700\" rel=\"stylesheet\">
\t</head>
\t<body ";
        // line 8
        echo (isset($context["body"]) ? $context["body"] : null);
        echo ">
\t\t<div class=\"landing-background row_image\"></div>
\t\t<div id=\"pf-loading-message\">
\t\t\t<span class=\"l-1\"></span>
\t\t\t<span class=\"l-2\"></span>
\t\t\t<span class=\"l-3\"></span>
\t\t\t<span class=\"l-4\"></span>
\t\t\t<span class=\"l-5\"></span>
\t\t\t<span class=\"l-6\"></span>
\t\t</div>
\t\t<div id=\"section-header\">
\t\t\t<div class=\"sticky-bar\">
\t\t\t\t<div class=\"container sticky-bar-inner h-6 ";
        // line 20
        if ((call_user_func_array($this->env->getFunction('setting')->getCallable(), array("user.hide_main_menu")) == true)) {
            echo "setting-hide-menu";
        }
        echo "\">
\t\t\t\t\t<div class=\"mr-2 site-logo-block\">
\t\t\t\t\t\t";
        // line 22
        echo (isset($context["logo"]) ? $context["logo"] : null);
        echo "
\t\t\t\t\t</div>

\t\t\t\t\t<!-- Button collapse main nav when on device -->
\t\t\t\t\t";
        // line 26
        if (( !call_user_func_array($this->env->getFunction('setting')->getCallable(), array("user.hide_main_menu")) || (call_user_func_array($this->env->getFunction('is_user')->getCallable(), array()) == true))) {
            // line 27
            echo "\t\t\t\t\t\t<button type=\"button\" class=\"btn-nav-toggle collapsed mr-2 js-btn-collapse-main-nav\"></button>
\t\t\t\t\t";
        }
        // line 29
        echo "
\t\t\t\t\t";
        // line 30
        if (call_user_func_array($this->env->getFunction('user_group_setting')->getCallable(), array("search.can_use_global_search"))) {
            // line 31
            echo "\t\t\t\t\t\t<div id=\"search-panel\" class=\"search-panel mr-7\">
\t\t\t\t\t\t\t<div class=\"js_temp_friend_search_form\"></div>
\t\t\t\t\t\t\t<form method=\"get\" action=\"";
            // line 33
            echo call_user_func_array($this->env->getFunction('url')->getCallable(), array("search"));
            echo "\" class=\"header_search_form\" id=\"header_search_form\">
\t\t\t\t\t\t\t\t<div class=\"form-group has-feedback\">
\t\t\t\t\t\t\t\t\t<span class=\"ico ico-arrow-left btn-globalsearch-return\"></span>
\t\t\t\t\t\t\t\t\t<input type=\"text\" name=\"q\" placeholder=\"";
            // line 36
            echo call_user_func_array($this->env->getFunction('_p')->getCallable(), array("search"));
            echo "...\" autocomplete=\"off\" class=\"form-control input-sm in_focus\" id=\"header_sub_menu_search_input\" />
\t\t\t\t\t\t\t\t\t<span class=\"ico ico-search-o form-control-feedback\" data-action=\"submit_search_form\"></span>
\t\t\t\t\t\t\t\t\t<span class=\"ico ico-search-o form-control-feedback btn-mask-action\"></span>
\t\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t</form>
\t\t\t\t\t\t</div>
\t\t\t\t\t";
        }
        // line 43
        echo "
\t\t\t\t\t<!-- Main Navigation -->
\t\t\t\t\t";
        // line 45
        if (( !call_user_func_array($this->env->getFunction('setting')->getCallable(), array("user.hide_main_menu")) || (call_user_func_array($this->env->getFunction('is_user')->getCallable(), array()) == true))) {
            // line 46
            echo "\t\t\t\t\t\t<div class=\"fixed-main-navigation\">
\t\t\t\t\t\t\t<div class=\"dropdown\">
\t\t\t\t\t\t\t\t<span data-toggle=\"dropdown\"><i class=\"ico ico-navbar\"></i></span>
\t\t\t\t\t\t\t\t<ul class=\"dropdown-menu site_menu\">
\t\t\t\t\t\t\t\t\t";
            // line 50
            echo (isset($context["menu_list"]) ? $context["menu_list"] : null);
            echo "
\t\t\t\t\t\t\t\t</ul>
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t<span class=\"js-btn-collapse-main-nav link\"></span>
\t\t\t\t\t\t</div>
\t\t\t\t\t";
        }
        // line 56
        echo "
\t\t\t\t\t";
        // line 57
        if ((call_user_func_array($this->env->getFunction('is_user')->getCallable(), array()) == true)) {
            // line 58
            echo "\t\t\t\t\t\t<div id=\"user_sticky_bar\" class=\"user-sticky-bar\"></div>
\t\t\t\t\t";
        } else {
            // line 60
            echo "\t\t\t\t\t\t";
            echo (isset($context["sticky_bar"]) ? $context["sticky_bar"] : null);
            echo "
\t\t\t\t\t";
        }
        // line 62
        echo "\t\t\t\t</div>
\t\t\t</div>
\t\t\t<nav class=\"navbar main-navigation collapse navbar-collapse\" id=\"main-navigation-collapse\">
\t\t\t\t<div class=\"container\">
\t\t\t\t\t<!-- Collect the nav links, forms, and other content for toggling -->
\t\t\t\t\t";
        // line 67
        echo (isset($context["menu"]) ? $context["menu"] : null);
        echo "
\t\t\t\t</div><!-- /.container-fluid -->
\t\t\t</nav>

\t\t\t";
        // line 71
        echo (isset($context["location_6"]) ? $context["location_6"] : null);
        echo "

\t\t</div>

\t\t<div id=\"main\" class=\"";
        // line 75
        echo (isset($context["main_class"]) ? $context["main_class"] : null);
        echo "\">
\t\t\t<div class=\"container\">
\t\t\t\t<div class=\"row\">
\t\t\t\t\t<div class=\"col-md-12 col-sm-12\">
\t\t\t\t\t\t";
        // line 79
        echo (isset($context["location_11"]) ? $context["location_11"] : null);
        echo "
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t\t<div class=\"layout-main\">
\t\t\t\t\t<div class=\"layout-left \" id=\"left\">
                        ";
        // line 84
        if ((call_user_func_array($this->env->getFunction('get_controller_name')->getCallable(), array()) == "index-visitor")) {
            // line 85
            echo "\t\t\t\t\t\t\t<div id=\"index-visitor-error\">
                            ";
            // line 86
            echo (isset($context["errors"]) ? $context["errors"] : null);
            echo "
\t\t\t\t\t\t\t</div>
                        ";
        }
        // line 89
        echo "\t\t\t\t\t\t";
        echo (isset($context["left"]) ? $context["left"] : null);
        echo "
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"layout-middle\" id=\"content-holder\">
\t\t\t\t\t\t<div id=\"content-stage\" class=\"bg-tran\">
\t\t\t\t\t\t\t<div id=\"top\">
\t\t\t\t\t\t\t\t";
        // line 94
        echo (isset($context["main_top"]) ? $context["main_top"] : null);
        echo "
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t<div class=\"\">
\t\t\t\t\t\t\t\t";
        // line 97
        echo (isset($context["breadcrumb"]) ? $context["breadcrumb"] : null);
        echo "
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t\t<div id=\"content\">
\t\t\t\t\t\t\t\t";
        // line 100
        echo (isset($context["errors"]) ? $context["errors"] : null);
        echo "
\t\t\t\t\t\t\t\t";
        // line 101
        echo (isset($context["content"]) ? $context["content"] : null);
        echo "
\t\t\t\t\t\t\t</div>
\t\t\t\t\t\t</div>
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"layout-right\" id=\"right\">
\t\t\t\t\t\t";
        // line 106
        echo (isset($context["right"]) ? $context["right"] : null);
        echo "
\t\t\t\t\t</div>
\t\t\t\t\t<div class=\"btn-scrolltop\" style=\"display: none\">
\t\t\t\t\t\t<span class=\"btn btn-round btn-gradient btn-primary s-5\" onclick=\"page_scroll2top();\">
\t\t\t\t\t\t\t<i class=\"ico ico-goup\"></i>
\t\t\t\t\t\t</span>
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t\t<div class=\"row\">
\t\t\t\t\t<div class=\"col-md-12 col-sm-12\">
\t\t\t\t\t\t";
        // line 116
        echo (isset($context["location_8"]) ? $context["location_8"] : null);
        echo "
\t\t\t\t\t</div>
\t\t\t\t</div>
\t\t\t</div>
\t\t</div>

\t\t<div id=\"bottom_placeholder\">
\t\t\t";
        // line 123
        echo (isset($context["location_12"]) ? $context["location_12"] : null);
        echo "
\t\t</div>

\t\t<footer id=\"section-footer\">
\t\t\t<div class=\"container\">
\t\t\t\t";
        // line 128
        echo (isset($context["footer"]) ? $context["footer"] : null);
        echo "
\t\t\t\t";
        // line 129
        echo (isset($context["location_5"]) ? $context["location_5"] : null);
        echo "
\t\t\t</div>
\t\t</footer>
\t\t";
        // line 132
        echo (isset($context["js"]) ? $context["js"] : null);
        echo "
\t\t<div class=\"nav-mask-modal\"></div>
\t</body>
</html>";
    }

    public function getTemplateName()
    {
        return "@Theme/layout.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  254 => 132,  248 => 129,  244 => 128,  236 => 123,  226 => 116,  213 => 106,  205 => 101,  201 => 100,  195 => 97,  189 => 94,  180 => 89,  174 => 86,  171 => 85,  169 => 84,  161 => 79,  154 => 75,  147 => 71,  140 => 67,  133 => 62,  127 => 60,  123 => 58,  121 => 57,  118 => 56,  109 => 50,  103 => 46,  101 => 45,  97 => 43,  87 => 36,  81 => 33,  77 => 31,  75 => 30,  72 => 29,  68 => 27,  66 => 26,  59 => 22,  52 => 20,  37 => 8,  31 => 5,  27 => 4,  22 => 2,  19 => 1,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "@Theme/layout.html", "/home/barterbender1/public_html/dev/PF.Site/flavors/material/html/layout.html");
    }
}
