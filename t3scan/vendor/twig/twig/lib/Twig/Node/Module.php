<?php

use Twig\Node\ModuleNode;

class_exists('Twig\Node\ModuleNode');

@trigger_error(sprintf('Using the "Twig_Node_Module" class is deprecated since Twig version 2.7, use "Twig\Node\ModuleNode" instead.'), E_USER_DEPRECATED);

if (\false) {
/**
@deprecated */
class Twig_Node_Module extends ModuleNode
{
}
}
