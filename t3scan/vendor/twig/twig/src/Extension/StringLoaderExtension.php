<?php










namespace Twig\Extension {
use Twig\TwigFunction;

final class StringLoaderExtension extends AbstractExtension
{
public function getFunctions()
{
return [
new TwigFunction('template_from_string', 'twig_template_from_string', ['needs_environment' => true]),
];
}
}

class_alias('Twig\Extension\StringLoaderExtension', 'Twig_Extension_StringLoader');
}

namespace {
use Twig\Environment;
use Twig\TemplateWrapper;

/**
@param
@param
@return





*/
function twig_template_from_string(Environment $env, $template, string $name = null)
{
return $env->createTemplate((string) $template, $name);
}
}
