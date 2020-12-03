<?php











namespace Twig\Node\Expression;

use Twig\Compiler;

class ConstantExpression extends AbstractExpression
{
public function __construct($value, int $lineno)
{
parent::__construct([], ['value' => $value], $lineno);
}

public function compile(Compiler $compiler)
{
$compiler->repr($this->getAttribute('value'));
}
}

class_alias('Twig\Node\Expression\ConstantExpression', 'Twig_Node_Expression_Constant');
