<?php

use Twig\Error\LoaderError;

class_exists('Twig\Error\LoaderError');

@trigger_error(sprintf('Using the "Twig_Error_Loader" class is deprecated since Twig version 2.7, use "Twig\Error\LoaderError" instead.'), E_USER_DEPRECATED);

if (\false) {
/**
@deprecated */
class Twig_Error_Loader extends LoaderError
{
}
}
