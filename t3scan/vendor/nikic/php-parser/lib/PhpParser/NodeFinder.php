<?php declare(strict_types=1);

namespace PhpParser;

use PhpParser\NodeVisitor\FindingVisitor;
use PhpParser\NodeVisitor\FirstFindingVisitor;

class NodeFinder
{
/**
@param
@param
@return



*/
public function find($nodes, callable $filter) : array {
if (!is_array($nodes)) {
$nodes = [$nodes];
}

$visitor = new FindingVisitor($filter);

$traverser = new NodeTraverser;
$traverser->addVisitor($visitor);
$traverser->traverse($nodes);

return $visitor->getFoundNodes();
}

/**
@param
@param
@return



*/
public function findInstanceOf($nodes, string $class) : array {
return $this->find($nodes, function ($node) use ($class) {
return $node instanceof $class;
});
}

/**
@param
@param
@return



*/
public function findFirst($nodes, callable $filter) {
if (!is_array($nodes)) {
$nodes = [$nodes];
}

$visitor = new FirstFindingVisitor($filter);

$traverser = new NodeTraverser;
$traverser->addVisitor($visitor);
$traverser->traverse($nodes);

return $visitor->getFoundNode();
}

/**
@param
@param
@return



*/
public function findFirstInstanceOf($nodes, string $class) {
return $this->findFirst($nodes, function ($node) use ($class) {
return $node instanceof $class;
});
}
}
