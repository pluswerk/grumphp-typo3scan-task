<?php

use Twig\Parser;

class_exists('Twig\Parser');

@trigger_error(sprintf('Using the "Twig_Parser" class is deprecated since Twig version 2.7, use "Twig\Parser" instead.'), E_USER_DEPRECATED);

if (\false) {
/**
@deprecated */
class Twig_Parser extends Parser
{
}
}
