<?php










namespace Twig\Node\Expression\Binary;

use Twig\Compiler;

class GreaterEqualBinary extends AbstractBinary
{
public function operator(Compiler $compiler)
{
return $compiler->raw('>=');
}
}

class_alias('Twig\Node\Expression\Binary\GreaterEqualBinary', 'Twig_Node_Expression_Binary_GreaterEqual');
