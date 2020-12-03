<?php declare(strict_types=1);

namespace PhpParser\Builder;

use PhpParser;
use PhpParser\BuilderHelpers;
use PhpParser\Node;
use PhpParser\Node\Stmt;

class Namespace_ extends Declaration
{
private $name;
private $stmts = [];

/**
@param


*/
public function __construct($name) {
$this->name = null !== $name ? BuilderHelpers::normalizeName($name) : null;
}

/**
@param
@return



*/
public function addStmt($stmt) {
$this->stmts[] = BuilderHelpers::normalizeStmt($stmt);

return $this;
}

/**
@return


*/
public function getNode() : Node {
return new Stmt\Namespace_($this->name, $this->stmts, $this->attributes);
}
}
