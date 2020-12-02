<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class Do_ extends Node\Stmt
{
/**
@var */
public $stmts;
/**
@var */
public $cond;

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
return ['stmts', 'cond'];
}

public function getType() : string {
return 'Stmt_Do';
}
}