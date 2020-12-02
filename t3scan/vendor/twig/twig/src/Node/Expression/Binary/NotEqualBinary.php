<?php










namespace Twig\Node\Expression\Binary;

use Twig\Compiler;

class NotEqualBinary extends AbstractBinary
{
public function operator(Compiler $compiler)
{
return $compiler->raw('!=');
}
}

class_alias('Twig\Node\Expression\Binary\NotEqualBinary', 'Twig_Node_Expression_Binary_NotEqual');
