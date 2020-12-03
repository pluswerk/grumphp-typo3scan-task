<?php










namespace Twig\Node\Expression\Binary;

use Twig\Compiler;

class SpaceshipBinary extends AbstractBinary
{
public function operator(Compiler $compiler)
{
return $compiler->raw('<=>');
}
}
