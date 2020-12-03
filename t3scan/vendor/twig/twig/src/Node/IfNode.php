<?php











namespace Twig\Node;

use Twig\Compiler;

/**
@author


*/
class IfNode extends Node
{
public function __construct(Node $tests, ?Node $else, int $lineno, string $tag = null)
{
$nodes = ['tests' => $tests];
if (null !== $else) {
$nodes['else'] = $else;
}

parent::__construct($nodes, [], $lineno, $tag);
}

public function compile(Compiler $compiler)
{
$compiler->addDebugInfo($this);
for ($i = 0, $count = \count($this->getNode('tests')); $i < $count; $i += 2) {
if ($i > 0) {
$compiler
->outdent()
->write('} elseif (')
;
} else {
$compiler
->write('if (')
;
}

$compiler
->subcompile($this->getNode('tests')->getNode($i))
->raw(") {\n")
->indent()
->subcompile($this->getNode('tests')->getNode($i + 1))
;
}

if ($this->hasNode('else')) {
$compiler
->outdent()
->write("} else {\n")
->indent()
->subcompile($this->getNode('else'))
;
}

$compiler
->outdent()
->write("}\n");
}
}

class_alias('Twig\Node\IfNode', 'Twig_Node_If');
