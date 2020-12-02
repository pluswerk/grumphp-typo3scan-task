<?php










namespace Twig\Node\Expression\Test;

use Twig\Compiler;
use Twig\Node\Expression\TestExpression;

/**
@author






*/
class ConstantTest extends TestExpression
{
public function compile(Compiler $compiler)
{
$compiler
->raw('(')
->subcompile($this->getNode('node'))
->raw(' === constant(')
;

if ($this->getNode('arguments')->hasNode(1)) {
$compiler
->raw('get_class(')
->subcompile($this->getNode('arguments')->getNode(1))
->raw(')."::".')
;
}

$compiler
->subcompile($this->getNode('arguments')->getNode(0))
->raw('))')
;
}
}

class_alias('Twig\Node\Expression\Test\ConstantTest', 'Twig_Node_Expression_Test_Constant');
