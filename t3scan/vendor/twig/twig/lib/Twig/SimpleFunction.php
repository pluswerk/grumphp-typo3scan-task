<?php










use Twig\TwigFunction;




class_exists(TwigFunction::class);

@trigger_error(sprintf('Using the "Twig_SimpleFunction" class is deprecated since Twig version 2.7, use "Twig\TwigFunction" instead.'), E_USER_DEPRECATED);

if (false) {
/**
@deprecated */
final class Twig_SimpleFunction extends TwigFunction
{
}
}
