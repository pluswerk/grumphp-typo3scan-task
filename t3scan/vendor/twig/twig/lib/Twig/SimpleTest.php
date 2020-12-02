<?php










use Twig\TwigTest;




class_exists(TwigTest::class);

@trigger_error(sprintf('Using the "Twig_SimpleTest" class is deprecated since Twig version 2.7, use "Twig\TwigTest" instead.'), E_USER_DEPRECATED);

if (false) {
/**
@deprecated */
final class Twig_SimpleTest extends TwigTest
{
}
}
