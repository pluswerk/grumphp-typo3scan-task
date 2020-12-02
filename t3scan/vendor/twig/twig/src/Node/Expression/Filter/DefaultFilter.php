<?php










namespace Twig\Node\Expression\Filter;

use Twig\Compiler;
use Twig\Node\Expression\ConditionalExpression;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\FilterExpression;
use Twig\Node\Expression\GetAttrExpression;
use Twig\Node\Expression\NameExpression;
use Twig\Node\Expression\Test\DefinedTest;
use Twig\Node\Node;

/**
@author




*/
class DefaultFilter extends FilterExpression
{
public function __construct(Node $node, ConstantExpression $filterName, Node $arguments, int $lineno, string $tag = null)
{
$default = new FilterExpression($node, new ConstantExpression('default', $node->getTemplateLine()), $arguments, $node->getTemplateLine());

if ('default' === $filterName->getAttribute('value') && ($node instanceof NameExpression || $node instanceof GetAttrExpression)) {
$test = new DefinedTest(clone $node, 'defined', new Node(), $node->getTemplateLine());
$false = \count($arguments) ? $arguments->getNode(0) : new ConstantExpression('', $node->getTemplateLine());

$node = new ConditionalExpression($test, $default, $false, $node->getTemplateLine());
} else {
$node = $default;
}

parent::__construct($node, $filterName, $arguments, $lineno, $tag);
}

public function compile(Compiler $compiler)
{
$compiler->subcompile($this->getNode('node'));
}
}

class_alias('Twig\Node\Expression\Filter\DefaultFilter', 'Twig_Node_Expression_Filter_Default');
