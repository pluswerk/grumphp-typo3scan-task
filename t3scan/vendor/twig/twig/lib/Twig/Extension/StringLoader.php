<?php

use Twig\Extension\StringLoaderExtension;

class_exists('Twig\Extension\StringLoaderExtension');

@trigger_error(sprintf('Using the "Twig_Extension_StringLoader" class is deprecated since Twig version 2.7, use "Twig\Extension\StringLoaderExtension" instead.'), E_USER_DEPRECATED);

if (\false) {
/**
@deprecated */
class Twig_Extension_StringLoader extends StringLoaderExtension
{
}
}
