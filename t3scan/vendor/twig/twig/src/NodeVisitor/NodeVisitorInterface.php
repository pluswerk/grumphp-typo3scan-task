<?php










namespace Twig\NodeVisitor;

use Twig\Environment;
use Twig\Node\Node;

/**
@author


*/
interface NodeVisitorInterface
{
/**
@return


*/
public function enterNode(Node $node, Environment $env);

/**
@return


*/
public function leaveNode(Node $node, Environment $env);

/**
@return




*/
public function getPriority();
}

class_alias('Twig\NodeVisitor\NodeVisitorInterface', 'Twig_NodeVisitorInterface');


class_exists('Twig\Environment');
