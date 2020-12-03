<?php











namespace Twig\Node\Expression\Binary;

use Twig\Compiler;

class BitwiseXorBinary extends AbstractBinary
{
public function operator(Compiler $compiler)
{
return $compiler->raw('^');
}
}

class_alias('Twig\Node\Expression\Binary\BitwiseXorBinary', 'Twig_Node_Expression_Binary_BitwiseXor');
