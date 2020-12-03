<?php










namespace Twig\Node\Expression\Test;

use Twig\Compiler;
use Twig\Node\Expression\TestExpression;

/**
@author




*/
class DivisiblebyTest extends TestExpression
{
public function compile(Compiler $compiler)
{
$compiler
->raw('(0 == ')
->subcompile($this->getNode('node'))
->raw(' % ')
->subcompile($this->getNode('arguments')->getNode(0))
->raw(')')
;
}
}

class_alias('Twig\Node\Expression\Test\DivisiblebyTest', 'Twig_Node_Expression_Test_Divisibleby');
