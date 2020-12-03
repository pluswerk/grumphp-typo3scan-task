<?php










namespace Twig\NodeVisitor;

use Twig\Environment;
use Twig\Node\BlockReferenceNode;
use Twig\Node\Expression\BlockReferenceExpression;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Expression\FilterExpression;
use Twig\Node\Expression\FunctionExpression;
use Twig\Node\Expression\GetAttrExpression;
use Twig\Node\Expression\NameExpression;
use Twig\Node\Expression\ParentExpression;
use Twig\Node\ForNode;
use Twig\Node\IncludeNode;
use Twig\Node\Node;
use Twig\Node\PrintNode;

/**
@author







*/
final class OptimizerNodeVisitor extends AbstractNodeVisitor
{
const OPTIMIZE_ALL = -1;
const OPTIMIZE_NONE = 0;
const OPTIMIZE_FOR = 2;
const OPTIMIZE_RAW_FILTER = 4;

 const OPTIMIZE_VAR_ACCESS = 8;

private $loops = [];
private $loopsTargets = [];
private $optimizers;

/**
@param
*/
public function __construct(int $optimizers = -1)
{
if (!\is_int($optimizers) || $optimizers > (self::OPTIMIZE_FOR | self::OPTIMIZE_RAW_FILTER | self::OPTIMIZE_VAR_ACCESS)) {
throw new \InvalidArgumentException(sprintf('Optimizer mode "%s" is not valid.', $optimizers));
}

$this->optimizers = $optimizers;
}

protected function doEnterNode(Node $node, Environment $env)
{
if (self::OPTIMIZE_FOR === (self::OPTIMIZE_FOR & $this->optimizers)) {
$this->enterOptimizeFor($node, $env);
}

return $node;
}

protected function doLeaveNode(Node $node, Environment $env)
{
if (self::OPTIMIZE_FOR === (self::OPTIMIZE_FOR & $this->optimizers)) {
$this->leaveOptimizeFor($node, $env);
}

if (self::OPTIMIZE_RAW_FILTER === (self::OPTIMIZE_RAW_FILTER & $this->optimizers)) {
$node = $this->optimizeRawFilter($node, $env);
}

$node = $this->optimizePrintNode($node, $env);

return $node;
}








private function optimizePrintNode(Node $node, Environment $env): Node
{
if (!$node instanceof PrintNode) {
return $node;
}

$exprNode = $node->getNode('expr');
if (
$exprNode instanceof BlockReferenceExpression ||
$exprNode instanceof ParentExpression
) {
$exprNode->setAttribute('output', true);

return $exprNode;
}

return $node;
}




private function optimizeRawFilter(Node $node, Environment $env): Node
{
if ($node instanceof FilterExpression && 'raw' == $node->getNode('filter')->getAttribute('value')) {
return $node->getNode('node');
}

return $node;
}




private function enterOptimizeFor(Node $node, Environment $env)
{
if ($node instanceof ForNode) {

 $node->setAttribute('with_loop', false);
array_unshift($this->loops, $node);
array_unshift($this->loopsTargets, $node->getNode('value_target')->getAttribute('name'));
array_unshift($this->loopsTargets, $node->getNode('key_target')->getAttribute('name'));
} elseif (!$this->loops) {

 return;
}




 elseif ($node instanceof NameExpression && 'loop' === $node->getAttribute('name')) {
$node->setAttribute('always_defined', true);
$this->addLoopToCurrent();
}


 elseif ($node instanceof NameExpression && \in_array($node->getAttribute('name'), $this->loopsTargets)) {
$node->setAttribute('always_defined', true);
}


 elseif ($node instanceof BlockReferenceNode || $node instanceof BlockReferenceExpression) {
$this->addLoopToCurrent();
}


 elseif ($node instanceof IncludeNode && !$node->getAttribute('only')) {
$this->addLoopToAll();
}


 elseif ($node instanceof FunctionExpression
&& 'include' === $node->getAttribute('name')
&& (!$node->getNode('arguments')->hasNode('with_context')
|| false !== $node->getNode('arguments')->getNode('with_context')->getAttribute('value')
)
) {
$this->addLoopToAll();
}


 elseif ($node instanceof GetAttrExpression
&& (!$node->getNode('attribute') instanceof ConstantExpression
|| 'parent' === $node->getNode('attribute')->getAttribute('value')
)
&& (true === $this->loops[0]->getAttribute('with_loop')
|| ($node->getNode('node') instanceof NameExpression
&& 'loop' === $node->getNode('node')->getAttribute('name')
)
)
) {
$this->addLoopToAll();
}
}




private function leaveOptimizeFor(Node $node, Environment $env)
{
if ($node instanceof ForNode) {
array_shift($this->loops);
array_shift($this->loopsTargets);
array_shift($this->loopsTargets);
}
}

private function addLoopToCurrent()
{
$this->loops[0]->setAttribute('with_loop', true);
}

private function addLoopToAll()
{
foreach ($this->loops as $loop) {
$loop->setAttribute('with_loop', true);
}
}

public function getPriority()
{
return 255;
}
}

class_alias('Twig\NodeVisitor\OptimizerNodeVisitor', 'Twig_NodeVisitor_Optimizer');
