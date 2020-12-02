<?php











namespace Twig\Node\Expression\Unary;

use Twig\Compiler;

class NegUnary extends AbstractUnary
{
public function operator(Compiler $compiler)
{
$compiler->raw('-');
}
}

class_alias('Twig\Node\Expression\Unary\NegUnary', 'Twig_Node_Expression_Unary_Neg');
