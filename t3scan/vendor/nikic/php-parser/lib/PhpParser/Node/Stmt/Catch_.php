<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;
use PhpParser\Node\Expr;

class Catch_ extends Node\Stmt
{
/**
@var */
public $types;
/**
@var */
public $var;
/**
@var */
public $stmts;

/**
@param
@param
@param
@param


*/
public function __construct(
array $types, Expr\Variable $var = null, array $stmts = [], array $attributes = []
) {
$this->attributes = $attributes;
$this->types = $types;
$this->var = $var;
$this->stmts = $stmts;
}

public function getSubNodeNames() : array {
return ['types', 'var', 'stmts'];
}

public function getType() : string {
return 'Stmt_Catch';
}
}
