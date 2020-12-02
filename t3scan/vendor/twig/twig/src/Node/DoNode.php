<?php










namespace Twig\Node;

use Twig\Compiler;
use Twig\Node\Expression\AbstractExpression;

/**
@author


*/
class DoNode extends Node
{
public function __construct(AbstractExpression $expr, int $lineno, string $tag = null)
{
parent::__construct(['expr' => $expr], [], $lineno, $tag);
}

public function compile(Compiler $compiler)
{
$compiler
->addDebugInfo($this)
->write('')
->subcompile($this->getNode('expr'))
->raw(";\n")
;
}
}

class_alias('Twig\Node\DoNode', 'Twig_Node_Do');
