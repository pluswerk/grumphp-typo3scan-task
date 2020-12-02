<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class ElseIf_ extends Node\Stmt
{
/**
@var */
public $cond;
/**
@var */
public $stmts;

/**
@param
@param
@param


*/
public function __construct(Node\Expr $cond, array $stmts = [], array $attributes = []) {
$this->attributes = $attributes;
$this->cond = $cond;
$this->stmts = $stmts;
}

public function getSubNodeNames() : array {
return ['cond', 'stmts'];
}

public function getType() : string {
return 'Stmt_ElseIf';
}
}
