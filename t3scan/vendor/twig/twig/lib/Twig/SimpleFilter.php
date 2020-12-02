<?php










use Twig\TwigFilter;




class_exists(TwigFilter::class);

@trigger_error(sprintf('Using the "Twig_SimpleFilter" class is deprecated since Twig version 2.7, use "Twig\TwigFilter" instead.'), E_USER_DEPRECATED);

if (false) {
/**
@deprecated */
final class Twig_SimpleFilter extends TwigFilter
{
}
}
