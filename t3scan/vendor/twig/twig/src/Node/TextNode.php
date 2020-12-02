<?php











namespace Twig\Node;

use Twig\Compiler;

/**
@author


*/
class TextNode extends Node implements NodeOutputInterface
{
public function __construct(string $data, int $lineno)
{
parent::__construct([], ['data' => $data], $lineno);
}

public function compile(Compiler $compiler)
{
$compiler
->addDebugInfo($this)
->write('echo ')
->string($this->getAttribute('data'))
->raw(";\n")
;
}
}

class_alias('Twig\Node\TextNode', 'Twig_Node_Text');
