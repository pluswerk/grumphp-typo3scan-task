<?php










namespace Twig\Profiler\Node;

use Twig\Compiler;
use Twig\Node\Node;

/**
@author


*/
class LeaveProfileNode extends Node
{
public function __construct(string $varName)
{
parent::__construct([], ['var_name' => $varName]);
}

public function compile(Compiler $compiler)
{
$compiler
->write("\n")
->write(sprintf("\$%s->leave(\$%s);\n\n", $this->getAttribute('var_name'), $this->getAttribute('var_name').'_prof'))
;
}
}

class_alias('Twig\Profiler\Node\LeaveProfileNode', 'Twig_Profiler_Node_LeaveProfile');
