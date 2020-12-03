<?php










namespace Twig\Node\Expression;

use Twig\Compiler;
use Twig\Node\Node;

/**
@internal
*/
final class InlinePrint extends AbstractExpression
{
public function __construct(Node $node, $lineno)
{
parent::__construct(['node' => $node], [], $lineno);
}

public function compile(Compiler $compiler)
{
$compiler
->raw('print (')
->subcompile($this->getNode('node'))
->raw(')')
;
}
}
