<?php

use Twig\Node\ForNode;

class_exists('Twig\Node\ForNode');

@trigger_error(sprintf('Using the "Twig_Node_For" class is deprecated since Twig version 2.7, use "Twig\Node\ForNode" instead.'), E_USER_DEPRECATED);

if (\false) {
/**
@deprecated */
class Twig_Node_For extends ForNode
{
}
}
