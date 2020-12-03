<?php










namespace Twig;

use Twig\Node\Node;
use Twig\NodeVisitor\NodeVisitorInterface;

/**
@author




*/
final class NodeTraverser
{
private $env;
private $visitors = [];

/**
@param
*/
public function __construct(Environment $env, array $visitors = [])
{
$this->env = $env;
foreach ($visitors as $visitor) {
$this->addVisitor($visitor);
}
}

public function addVisitor(NodeVisitorInterface $visitor)
{
$this->visitors[$visitor->getPriority()][] = $visitor;
}




public function traverse(Node $node): Node
{
ksort($this->visitors);
foreach ($this->visitors as $visitors) {
foreach ($visitors as $visitor) {
$node = $this->traverseForVisitor($visitor, $node);
}
}

return $node;
}

/**
@return
*/
private function traverseForVisitor(NodeVisitorInterface $visitor, Node $node)
{
$node = $visitor->enterNode($node, $this->env);

foreach ($node as $k => $n) {
if (false !== ($m = $this->traverseForVisitor($visitor, $n)) && null !== $m) {
if ($m !== $n) {
$node->setNode($k, $m);
}
} else {
if (false === $m) {
@trigger_error('Returning "false" to remove a Node from NodeVisitorInterface::leaveNode() is deprecated since Twig version 2.9; return "null" instead.', E_USER_DEPRECATED);
}

$node->removeNode($k);
}
}

return $visitor->leaveNode($node, $this->env);
}
}

class_alias('Twig\NodeTraverser', 'Twig_NodeTraverser');
