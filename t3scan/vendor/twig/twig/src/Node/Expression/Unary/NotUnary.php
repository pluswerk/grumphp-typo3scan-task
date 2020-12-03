<?php











namespace Twig\Node\Expression\Unary;

use Twig\Compiler;

class NotUnary extends AbstractUnary
{
public function operator(Compiler $compiler)
{
$compiler->raw('!');
}
}

class_alias('Twig\Node\Expression\Unary\NotUnary', 'Twig_Node_Expression_Unary_Not');
