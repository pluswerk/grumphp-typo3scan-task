<?php










namespace Twig\Node;

use Twig\Compiler;
use Twig\Node\Expression\AbstractExpression;

/**
@author







*/
class CheckToStringNode extends AbstractExpression
{
public function __construct(AbstractExpression $expr)
{
parent::__construct(['expr' => $expr], [], $expr->getTemplateLine(), $expr->getNodeTag());
}

public function compile(Compiler $compiler)
{
$expr = $this->getNode('expr');
$compiler
->raw('$this->sandbox->ensureToStringAllowed(')
->subcompile($expr)
->raw(', ')
->repr($expr->getTemplateLine())
->raw(', $this->source)')
;
}
}
