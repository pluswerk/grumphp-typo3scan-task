<?php










namespace Twig\Node\Expression\Binary;

use Twig\Compiler;

class FloorDivBinary extends AbstractBinary
{
public function compile(Compiler $compiler)
{
$compiler->raw('(int) floor(');
parent::compile($compiler);
$compiler->raw(')');
}

public function operator(Compiler $compiler)
{
return $compiler->raw('/');
}
}

class_alias('Twig\Node\Expression\Binary\FloorDivBinary', 'Twig_Node_Expression_Binary_FloorDiv');
