<?php declare(strict_types=1);

namespace PhpParser\Builder;

use PhpParser;
use PhpParser\BuilderHelpers;
use PhpParser\Node;
use PhpParser\Node\Stmt;

class Function_ extends FunctionLike
{
protected $name;
protected $stmts = [];

/**
@param


*/
public function __construct(string $name) {
$this->name = $name;
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
return new Stmt\Function_($this->name, [
'byRef' => $this->returnByRef,
'params' => $this->params,
'returnType' => $this->returnType,
'stmts' => $this->stmts,
], $this->attributes);
}
}
