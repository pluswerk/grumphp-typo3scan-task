<?php declare(strict_types=1);

namespace PhpParser\Node\Stmt;

use PhpParser\Node;

class Throw_ extends Node\Stmt
{
/**
@var */
public $expr;

/**
@param
@param


*/
public function __construct(Node\Expr $expr, array $attributes = []) {
$this->attributes = $attributes;
$this->expr = $expr;
}

public function getSubNodeNames() : array {
return ['expr'];
}

public function getType() : string {
return 'Stmt_Throw';
}
}
