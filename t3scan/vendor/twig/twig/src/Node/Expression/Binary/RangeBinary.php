<?php










namespace Twig\Node\Expression\Binary;

use Twig\Compiler;

class RangeBinary extends AbstractBinary
{
public function compile(Compiler $compiler)
{
$compiler
->raw('range(')
->subcompile($this->getNode('left'))
->raw(', ')
->subcompile($this->getNode('right'))
->raw(')')
;
}

public function operator(Compiler $compiler)
{
return $compiler->raw('..');
}
}

class_alias('Twig\Node\Expression\Binary\RangeBinary', 'Twig_Node_Expression_Binary_Range');
