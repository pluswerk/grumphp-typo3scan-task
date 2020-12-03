<?php











namespace Twig\Node\Expression\Unary;

use Twig\Compiler;

class PosUnary extends AbstractUnary
{
public function operator(Compiler $compiler)
{
$compiler->raw('+');
}
}

class_alias('Twig\Node\Expression\Unary\PosUnary', 'Twig_Node_Expression_Unary_Pos');
