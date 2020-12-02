<?php











namespace Twig\Node\Expression\Binary;

use Twig\Compiler;

class BitwiseOrBinary extends AbstractBinary
{
public function operator(Compiler $compiler)
{
return $compiler->raw('|');
}
}

class_alias('Twig\Node\Expression\Binary\BitwiseOrBinary', 'Twig_Node_Expression_Binary_BitwiseOr');
