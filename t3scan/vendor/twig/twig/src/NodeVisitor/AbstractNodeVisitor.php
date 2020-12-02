<?php










namespace Twig\NodeVisitor;

use Twig\Environment;
use Twig\Node\Node;

/**
@author




*/
abstract class AbstractNodeVisitor implements NodeVisitorInterface
{
final public function enterNode(Node $node, Environment $env)
{
return $this->doEnterNode($node, $env);
}

final public function leaveNode(Node $node, Environment $env)
{
return $this->doLeaveNode($node, $env);
}

/**
@return


*/
abstract protected function doEnterNode(Node $node, Environment $env);

/**
@return


*/
abstract protected function doLeaveNode(Node $node, Environment $env);
}

class_alias('Twig\NodeVisitor\AbstractNodeVisitor', 'Twig_BaseNodeVisitor');
