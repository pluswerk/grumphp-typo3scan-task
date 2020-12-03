<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class Foreach_ extends Node\Stmt
{
/**
@var */
public $expr;
/**
@var */
public $keyVar;
/**
@var */
public $byRef;
/**
@var */
public $valueVar;
/**
@var */
public $stmts;

/**
@param
@param
@param
@param





*/
public function __construct(Node\Expr $expr, Node\Expr $valueVar, array $subNodes = [], array $attributes = []) {
$this->attributes = $attributes;
$this->expr = $expr;
$this->keyVar = $subNodes['keyVar'] ?? null;
$this->byRef = $subNodes['byRef'] ?? false;
$this->valueVar = $valueVar;
$this->stmts = $subNodes['stmts'] ?? [];
}

public function getSubNodeNames() : array {
return ['expr', 'keyVar', 'byRef', 'valueVar', 'stmts'];
}

public function getType() : string {
return 'Stmt_Foreach';
}
}
