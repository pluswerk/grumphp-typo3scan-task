<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;
use PhpParser\Node\FunctionLike;

/**
@property
*/
class Function_ extends Node\Stmt implements FunctionLike
{
/**
@var */
public $byRef;
/**
@var */
public $name;
/**
@var */
public $params;
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
@param







*/
public function __construct($name, array $subNodes = [], array $attributes = []) {
$this->attributes = $attributes;
$this->byRef = $subNodes['byRef'] ?? false;
$this->name = \is_string($name) ? new Node\Identifier($name) : $name;
$this->params = $subNodes['params'] ?? [];
$returnType = $subNodes['returnType'] ?? null;
$this->returnType = \is_string($returnType) ? new Node\Identifier($returnType) : $returnType;
$this->stmts = $subNodes['stmts'] ?? [];
$this->attrGroups = $subNodes['attrGroups'] ?? [];
}

public function getSubNodeNames() : array {
return ['attrGroups', 'byRef', 'name', 'params', 'returnType', 'stmts'];
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
@return */
public function getStmts() : array {
return $this->stmts;
}

public function getType() : string {
return 'Stmt_Function';
}
}
