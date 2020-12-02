<?php










namespace Twig\Node;

use Twig\Compiler;

/**
@author








*/
class AutoEscapeNode extends Node
{
public function __construct($value, Node $body, int $lineno, string $tag = 'autoescape')
{
parent::__construct(['body' => $body], ['value' => $value], $lineno, $tag);
}

public function compile(Compiler $compiler)
{
$compiler->subcompile($this->getNode('body'));
}
}

class_alias('Twig\Node\AutoEscapeNode', 'Twig_Node_AutoEscape');
