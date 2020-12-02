<?php










namespace Twig\Node;

use Twig\Compiler;
use Twig\Node\Expression\AbstractExpression;
use Twig\Node\Expression\ConstantExpression;

/**
@author


*/
class EmbedNode extends IncludeNode
{

 public function __construct(string $name, int $index, ?AbstractExpression $variables, bool $only, bool $ignoreMissing, int $lineno, string $tag = null)
{
parent::__construct(new ConstantExpression('not_used', $lineno), $variables, $only, $ignoreMissing, $lineno, $tag);

$this->setAttribute('name', $name);
$this->setAttribute('index', $index);
}

protected function addGetTemplate(Compiler $compiler)
{
$compiler
->write('$this->loadTemplate(')
->string($this->getAttribute('name'))
->raw(', ')
->repr($this->getTemplateName())
->raw(', ')
->repr($this->getTemplateLine())
->raw(', ')
->string($this->getAttribute('index'))
->raw(')')
;
}
}

class_alias('Twig\Node\EmbedNode', 'Twig_Node_Embed');
