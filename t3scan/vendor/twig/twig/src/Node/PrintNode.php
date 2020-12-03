<?php











namespace Twig\Node;

use Twig\Compiler;
use Twig\Node\Expression\AbstractExpression;

/**
@author


*/
class PrintNode extends Node implements NodeOutputInterface
{
public function __construct(AbstractExpression $expr, int $lineno, string $tag = null)
{
parent::__construct(['expr' => $expr], [], $lineno, $tag);
}

public function compile(Compiler $compiler)
{
$compiler
->addDebugInfo($this)
->write('echo ')
->subcompile($this->getNode('expr'))
->raw(";\n")
;
}
}

class_alias('Twig\Node\PrintNode', 'Twig_Node_Print');
