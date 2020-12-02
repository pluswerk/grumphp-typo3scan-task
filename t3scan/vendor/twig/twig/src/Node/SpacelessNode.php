<?php










namespace Twig\Node;

use Twig\Compiler;

/**
@deprecated
@author





*/
class SpacelessNode extends Node implements NodeOutputInterface
{
public function __construct(Node $body, int $lineno, string $tag = 'spaceless')
{
parent::__construct(['body' => $body], [], $lineno, $tag);
}

public function compile(Compiler $compiler)
{
$compiler
->addDebugInfo($this)
;
if ($compiler->getEnvironment()->isDebug()) {
$compiler->write("ob_start();\n");
} else {
$compiler->write("ob_start(function () { return ''; });\n");
}
$compiler
->subcompile($this->getNode('body'))
->write("echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));\n")
;
}
}

class_alias('Twig\Node\SpacelessNode', 'Twig_Node_Spaceless');
