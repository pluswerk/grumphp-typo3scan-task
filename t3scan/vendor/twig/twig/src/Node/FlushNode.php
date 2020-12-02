<?php










namespace Twig\Node;

use Twig\Compiler;

/**
@author


*/
class FlushNode extends Node
{
public function __construct(int $lineno, string $tag)
{
parent::__construct([], [], $lineno, $tag);
}

public function compile(Compiler $compiler)
{
$compiler
->addDebugInfo($this)
->write("flush();\n")
;
}
}

class_alias('Twig\Node\FlushNode', 'Twig_Node_Flush');
