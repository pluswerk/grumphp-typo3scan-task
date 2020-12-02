<?php










namespace Twig\Node\Expression\Binary;

use Twig\Compiler;

class InBinary extends AbstractBinary
{
public function compile(Compiler $compiler)
{
$compiler
->raw('twig_in_filter(')
->subcompile($this->getNode('left'))
->raw(', ')
->subcompile($this->getNode('right'))
->raw(')')
;
}

public function operator(Compiler $compiler)
{
return $compiler->raw('in');
}
}

class_alias('Twig\Node\Expression\Binary\InBinary', 'Twig_Node_Expression_Binary_In');
