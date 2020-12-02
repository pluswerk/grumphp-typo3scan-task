<?php











namespace Twig\Node\Expression\Binary;

use Twig\Compiler;

class ModBinary extends AbstractBinary
{
public function operator(Compiler $compiler)
{
return $compiler->raw('%');
}
}

class_alias('Twig\Node\Expression\Binary\ModBinary', 'Twig_Node_Expression_Binary_Mod');
