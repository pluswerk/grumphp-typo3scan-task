<?php










namespace Twig\Node\Expression\Test;

use Twig\Compiler;
use Twig\Node\Expression\TestExpression;

/**
@author




*/
class NullTest extends TestExpression
{
public function compile(Compiler $compiler)
{
$compiler
->raw('(null === ')
->subcompile($this->getNode('node'))
->raw(')')
;
}
}

class_alias('Twig\Node\Expression\Test\NullTest', 'Twig_Node_Expression_Test_Null');
