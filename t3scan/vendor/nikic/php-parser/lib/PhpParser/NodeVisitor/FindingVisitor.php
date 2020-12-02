<?php declare(strict_types=1);

namespace PhpParser\NodeVisitor;

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;





class FindingVisitor extends NodeVisitorAbstract
{
/**
@var */
protected $filterCallback;
/**
@var */
protected $foundNodes;

public function __construct(callable $filterCallback) {
$this->filterCallback = $filterCallback;
}

/**
@return




*/
public function getFoundNodes() : array {
return $this->foundNodes;
}

public function beforeTraverse(array $nodes) {
$this->foundNodes = [];

return null;
}

public function enterNode(Node $node) {
$filterCallback = $this->filterCallback;
if ($filterCallback($node)) {
$this->foundNodes[] = $node;
}

return null;
}
}
