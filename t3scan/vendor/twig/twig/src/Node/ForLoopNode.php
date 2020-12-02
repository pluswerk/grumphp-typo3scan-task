<?php










namespace Twig\Node;

use Twig\Compiler;

/**
@author


*/
class ForLoopNode extends Node
{
public function __construct(int $lineno, string $tag = null)
{
parent::__construct([], ['with_loop' => false, 'ifexpr' => false, 'else' => false], $lineno, $tag);
}

public function compile(Compiler $compiler)
{
if ($this->getAttribute('else')) {
$compiler->write("\$context['_iterated'] = true;\n");
}

if ($this->getAttribute('with_loop')) {
$compiler
->write("++\$context['loop']['index0'];\n")
->write("++\$context['loop']['index'];\n")
->write("\$context['loop']['first'] = false;\n")
;

if (!$this->getAttribute('ifexpr')) {
$compiler
->write("if (isset(\$context['loop']['length'])) {\n")
->indent()
->write("--\$context['loop']['revindex0'];\n")
->write("--\$context['loop']['revindex'];\n")
->write("\$context['loop']['last'] = 0 === \$context['loop']['revindex0'];\n")
->outdent()
->write("}\n")
;
}
}
}
}

class_alias('Twig\Node\ForLoopNode', 'Twig_Node_ForLoop');
