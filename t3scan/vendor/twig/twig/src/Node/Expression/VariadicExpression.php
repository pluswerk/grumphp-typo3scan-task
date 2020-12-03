<?php










namespace Twig\Node\Expression;

use Twig\Compiler;

class VariadicExpression extends ArrayExpression
{
public function compile(Compiler $compiler)
{
$compiler->raw('...');

parent::compile($compiler);
}
}
