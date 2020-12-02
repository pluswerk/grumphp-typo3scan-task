<?php










namespace Twig\Node;

use Twig\Compiler;
use Twig\Node\Expression\ConstantExpression;

/**
@author








*/
class SandboxedPrintNode extends PrintNode
{
public function compile(Compiler $compiler)
{
$compiler
->addDebugInfo($this)
->write('echo ')
;
$expr = $this->getNode('expr');
if ($expr instanceof ConstantExpression) {
$compiler
->subcompile($expr)
->raw(";\n")
;
} else {
$compiler
->write('$this->extensions[SandboxExtension::class]->ensureToStringAllowed(')
->subcompile($expr)
->raw(', ')
->repr($expr->getTemplateLine())
->raw(", \$this->source);\n")
;
}
}
}

class_alias('Twig\Node\SandboxedPrintNode', 'Twig_Node_SandboxedPrint');
