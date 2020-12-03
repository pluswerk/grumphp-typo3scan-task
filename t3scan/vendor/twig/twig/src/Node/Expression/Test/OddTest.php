<?php










namespace Twig\Node\Expression\Test;

use Twig\Compiler;
use Twig\Node\Expression\TestExpression;

/**
@author




*/
class OddTest extends TestExpression
{
public function compile(Compiler $compiler)
{
$compiler
->raw('(')
->subcompile($this->getNode('node'))
->raw(' % 2 == 1')
->raw(')')
;
}
}

class_alias('Twig\Node\Expression\Test\OddTest', 'Twig_Node_Expression_Test_Odd');
