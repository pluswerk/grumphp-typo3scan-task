<?php declare(strict_types=1);

namespace PhpParser\Node\Expr;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\FunctionLike;

class Closure extends Expr implements FunctionLike
{
/**
@var */
public $static;
/**
@var */
public $byRef;
/**
@var */
public $params;
/**
@var */
public $uses;
/**
@var */
public $returnType;
/**
@var */
public $stmts;
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
$this->uses = $subNodes['uses'] ?? [];
$returnType = $subNodes['returnType'] ?? null;
$this->returnType = \is_string($returnType) ? new Node\Identifier($returnType) : $returnType;
$this->stmts = $subNodes['stmts'] ?? [];
$this->attrGroups = $subNodes['attrGroups'] ?? [];
}

public function getSubNodeNames() : array {
return ['attrGroups', 'static', 'byRef', 'params', 'uses', 'returnType', 'stmts'];
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

/**
@return */
public function getStmts() : array {
return $this->stmts;
}

public function getAttrGroups() : array {
return $this->attrGroups;
}

public function getType() : string {
return 'Expr_Closure';
}
}
