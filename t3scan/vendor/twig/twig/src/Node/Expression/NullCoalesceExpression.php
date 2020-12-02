<?php










namespace Twig\Node\Expression;

use Twig\Compiler;
use Twig\Node\Expression\Binary\AndBinary;
use Twig\Node\Expression\Test\DefinedTest;
use Twig\Node\Expression\Test\NullTest;
use Twig\Node\Expression\Unary\NotUnary;
use Twig\Node\Node;

class NullCoalesceExpression extends ConditionalExpression
{
public function __construct(Node $left, Node $right, int $lineno)
{
$test = new DefinedTest(clone $left, 'defined', new Node(), $left->getTemplateLine());

 if (!$left instanceof BlockReferenceExpression) {
$test = new AndBinary(
$test,
new NotUnary(new NullTest($left, 'null', new Node(), $left->getTemplateLine()), $left->getTemplateLine()),
$left->getTemplateLine()
);
}

parent::__construct($test, $left, $right, $lineno);
}

public function compile(Compiler $compiler)
{







if ($this->getNode('expr2') instanceof NameExpression) {
$this->getNode('expr2')->setAttribute('always_defined', true);
$compiler
->raw('((')
->subcompile($this->getNode('expr2'))
->raw(') ?? (')
->subcompile($this->getNode('expr3'))
->raw('))')
;
} else {
parent::compile($compiler);
}
}
}

class_alias('Twig\Node\Expression\NullCoalesceExpression', 'Twig_Node_Expression_NullCoalesce');
