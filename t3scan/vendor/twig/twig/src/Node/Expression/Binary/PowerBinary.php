<?php










namespace Twig\Node\Expression\Binary;

use Twig\Compiler;

class PowerBinary extends AbstractBinary
{
public function operator(Compiler $compiler)
{
return $compiler->raw('**');
}
}

class_alias('Twig\Node\Expression\Binary\PowerBinary', 'Twig_Node_Expression_Binary_Power');
