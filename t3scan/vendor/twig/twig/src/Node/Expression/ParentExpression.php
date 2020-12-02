<?php











namespace Twig\Node\Expression;

use Twig\Compiler;

/**
@author


*/
class ParentExpression extends AbstractExpression
{
public function __construct(string $name, int $lineno, string $tag = null)
{
parent::__construct([], ['output' => false, 'name' => $name], $lineno, $tag);
}

public function compile(Compiler $compiler)
{
if ($this->getAttribute('output')) {
$compiler
->addDebugInfo($this)
->write('$this->displayParentBlock(')
->string($this->getAttribute('name'))
->raw(", \$context, \$blocks);\n")
;
} else {
$compiler
->raw('$this->renderParentBlock(')
->string($this->getAttribute('name'))
->raw(', $context, $blocks)')
;
}
}
}

class_alias('Twig\Node\Expression\ParentExpression', 'Twig_Node_Expression_Parent');
