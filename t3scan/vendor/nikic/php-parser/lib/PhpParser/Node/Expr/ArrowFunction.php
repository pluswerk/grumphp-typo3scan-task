<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\FunctionLike;

class ArrowFunction extends Expr implements FunctionLike
{
/**
@var */
public $static;

/**
@var */
public $byRef;

/**
@var */
public $params = [];

/**
@var */
public $returnType;

/**
@var */
public $expr;
/**
@var */
public $attrGroups;

/**
@param
@param






*/
public function __construct(array $subNodes = [], array $attributes = []) {
$this->attributes = $attributes;
$this->static = $subNodes['static'] ?? false;
$this->byRef = $subNodes['byRef'] ?? false;
$this->params = $subNodes['params'] ?? [];
$returnType = $subNodes['returnType'] ?? null;
$this->returnType = \is_string($returnType) ? new Node\Identifier($returnType) : $returnType;
$this->expr = $subNodes['expr'] ?? null;
$this->attrGroups = $subNodes['attrGroups'] ?? [];
}

public function getSubNodeNames() : array {
return ['attrGroups', 'static', 'byRef', 'params', 'returnType', 'expr'];
}

public function returnsByRef() : bool {
return $this->byRef;
}

public function getParams() : array {
return $this->params;
}

public function getReturnType() {
return $this->returnType;
}

public function getAttrGroups() : array {
return $this->attrGroups;
}

/**
@return
*/
public function getStmts() : array {
return [new Node\Stmt\Return_($this->expr)];
}

public function getType() : string {
return 'Expr_ArrowFunction';
}
}
