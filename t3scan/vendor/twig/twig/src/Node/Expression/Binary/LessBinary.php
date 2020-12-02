<?php










namespace Twig\Node\Expression\Binary;

use Twig\Compiler;

class LessBinary extends AbstractBinary
{
public function operator(Compiler $compiler)
{
return $compiler->raw('<');
}
}

class_alias('Twig\Node\Expression\Binary\LessBinary', 'Twig_Node_Expression_Binary_Less');
