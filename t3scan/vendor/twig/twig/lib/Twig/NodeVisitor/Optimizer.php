<?php

use Twig\NodeVisitor\OptimizerNodeVisitor;

class_exists('Twig\NodeVisitor\OptimizerNodeVisitor');

@trigger_error(sprintf('Using the "Twig_NodeVisitor_Optimizer" class is deprecated since Twig version 2.7, use "Twig\NodeVisitor\OptimizerNodeVisitor" instead.'), E_USER_DEPRECATED);

if (\false) {
/**
@deprecated */
class Twig_NodeVisitor_Optimizer extends OptimizerNodeVisitor
{
}
}
